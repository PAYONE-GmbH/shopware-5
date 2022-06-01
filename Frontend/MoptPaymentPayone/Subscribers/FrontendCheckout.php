<?php

namespace Shopware\Plugins\MoptPaymentPayone\Subscribers;

use Enlight\Event\SubscriberInterface;
use Mopt_PayoneMain;
use Mopt_PayonePaymentHelper;
use Shopware\Models\Payment\Repository;

class FrontendCheckout implements SubscriberInterface
{

    /**
     * di container
     *
     * @var \Shopware\Components\DependencyInjection\Container
     */
    private $container;

    /**
     * inject di container
     *
     * @param \Shopware\Components\DependencyInjection\Container $container
     */
    public function __construct(\Shopware\Components\DependencyInjection\Container $container)
    {
        $this->container = $container;
    }

    /**
     * return array with all subsribed events
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            // load stored payment data for payment method overview
            'Shopware_Controllers_Frontend_Checkout::getSelectedPayment::after' => 'onGetSelectedPayment',
            // save terms agreement handling
            'Enlight_Controller_Action_PostDispatch_Frontend_Checkout' => 'moptExtendController_Frontend_Checkout',
            'Shopware_Controllers_Frontend_Checkout::ajaxAddArticleAction::after' => 'onBasketChange',
            'Shopware_Controllers_Frontend_Checkout::ajaxDeleteArticle::after' => 'onBasketChange',
            'Shopware_Controllers_Frontend_Checkout::ajaxAddArticleCartAction::after' => 'onBasketChange',
            'Shopware_Controllers_Frontend_Checkout::ajaxDeleteArticleCart::after' => 'onBasketChange',
            'Shopware_Controllers_Frontend_Checkout::addArticleAction::after' => 'onBasketChange',
            'Shopware_Controllers_Frontend_Checkout::deleteArticleAction::after' => 'onBasketChange',
            'Shopware_Controllers_Frontend_Checkout::changeQuantityAction::after' => 'onBasketChange',
            'sBasket::sGetBasket::after' => 'onBasketDataUpdate',
        ];
    }

    /**
     * Sets a flag when basket data has been updated to prevent unnecessary calls to `sBasket::sGetBasket()`
     *
     * @param \Enlight_Hook_HookArgs $args
     * @throws
     */
    public function onBasketDataUpdate(\Enlight_Hook_HookArgs $args)
    {
        $return = $args->getReturn();

        /** @var \Mopt_PayoneMain $payoneMain */
        $payoneMain = $this->container->get('MoptPayoneMain');
        $payoneMain->setBasketUpdated(true);

        $args->setReturn($return);
    }

    /**
     * set redirect flag for redirecting to paymentshipping in case basket is changed
     * only used for some payone payment methods
     *
     * @param \Enlight_Hook_HookArgs $arguments
     * @return void
     */
    public function onBasketChange(\Enlight_Hook_HookArgs $arguments)
    {
        $ret = $arguments->getReturn();
        $action = Shopware()->Modules()->Admin()->sSYSTEM->_GET['action'];
        $sTargetAction = Shopware()->Modules()->Admin()->sSYSTEM->_GET['sTargetAction'];
        $userData = Shopware()->Modules()->Admin()->sGetUserData();

        if ($action !== 'addArticle' && $action !== 'changeQuantity' && $action !== 'deleteArticle' &&
            $action !== 'ajaxAddArticleCart' && $action !== 'ajaxDeleteArticleCart' && $action !== 'ajaxDeleteArticle' &&
            $action !== 'ajaxAddArticle'
        )
        {
            return;
        }
        // targetAction is null for ajax requests
        if ($sTargetAction !== 'confirm' && !is_null($sTargetAction)) {
            return;
        }

        if (!$this->container->get('MoptPayoneMain')->getPaymentHelper()->isPayonePayolutionInstallment($userData['additional']['payment']['name'])
            && !$this->container->get('MoptPayoneMain')->getPaymentHelper()->isPayoneRatepayInstallment($userData['additional']['payment']['name'])
            && !$this->container->get('MoptPayoneMain')->getPaymentHelper()->isPayonePaydirektExpress($userData['additional']['payment']['name'])
            && !$this->container->get('MoptPayoneMain')->getPaymentHelper()->isPayonePaypalExpress($userData['additional']['payment']['name'])
        ) {
            return;
        }
        Shopware()->Session()->moptBasketChanged = true;
        $arguments->setReturn($ret);
    }

    /**
     * assign saved payment data to view
     *
     * @param \Enlight_Hook_HookArgs $arguments
     * @return type
     */
    public function onGetSelectedPayment(\Enlight_Hook_HookArgs $arguments)
    {
        $action = Shopware()->Modules()->Admin()->sSYSTEM->_GET['action'];
        $sTargetAction = Shopware()->Modules()->Admin()->sSYSTEM->_GET['sTargetAction'];

        if ($action == 'addArticle' || $action == 'cart' || $action == 'changeQuantity') {
            return;
        }

        if ($action == 'calculateShippingCosts' && $sTargetAction == 'cart') {
            return;
        }

        $ret = $arguments->getReturn();

        if (!$this->container->get('MoptPayoneMain')->getPaymentHelper()->isPayonePaymentMethod($ret['name'])) {
            return;
        }

        $userId = Shopware()->Session()->sUserId;

        $sql = 'SELECT `moptPaymentData` FROM s_plugin_mopt_payone_payment_data WHERE userId = ?';
        $paymentData = unserialize(Shopware()->Db()->fetchOne($sql, $userId));

        $ret['data'] = $paymentData;

        //save payment data to session for later use during actual payment process
        Shopware()->Session()->moptPayment = $paymentData;

        //special handling for creditCards
        // mbe: Removed manuel set to 'mopt_payone_creditcard' for confirm page
        /*if ($this->Application()->PayoneMain()->getPaymentHelper()->isPayoneCreditcardNotGrouped($ret['name']))
        {
          $ret['id'] = 'mopt_payone_creditcard';
        }*/

        $arguments->setReturn($ret);
    }

    public function moptExtendController_Frontend_Checkout(\Enlight_Controller_ActionEventArgs $args)
    {
        $subject = $args->getSubject();
        $view = $subject->View();
        $request = $subject->Request();
        $response = $subject->Response();

        if (!$request->isDispatched() || $response->isException() || $request->getModuleName() != 'frontend') {
            return;
        }

        $session = Shopware()->Session();
        $orderVars = $session->get('sOrderVariables');
        $userPaymentId = $orderVars['sUserData']['additional']['payment']['id'];
        /** @var Mopt_PayonePaymentHelper $helper */
        $helper = $this->container->get('MoptPayoneMain')->getPaymentHelper();
        $router = $this->container->get('router');

        if ($request->getActionName() === 'shippingPayment') {
            $view->extendsTemplate('frontend/checkout/mopt_shipping_payment.tpl');
            $view->extendsTemplate('frontend/checkout/mopt_shipping_payment_core.tpl');

            if ($request->get('moptAmazonErrorCode')) {
                $errorMessage = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages')->get('errorMessage' . $request->get('moptAmazonErrorCode'));
                $view->assign('moptAmazonErrorMessage', $errorMessage);
                $view->assign('moptAmazonErrorCode', $request->get('moptAmazonErrorCode'));
            }

            // used for amazon error handling
            if ($session->moptAmazonError) {
                $view->assign('moptAmazonError', $session->moptAmazonError);
                unset($session->moptAmazonError);
            }
            if ($session->moptAmazonLogout) {
                $view->assign('moptAmazonLogout', $session->moptAmazonLogout);
                unset($session->moptAmazonLogout);
            }

            // Klarna
            // order lines
            /** @var Mopt_PayoneMain $moptPayoneMain */
            $moptPayoneMain = $this->container->get('MoptPayoneMain');

            $selectedDispatchId = Shopware()->Session()['sDispatch'];
            $basket = $moptPayoneMain->sGetBasket();

            $shippingCosts = Shopware()->Modules()->Admin()->sGetPremiumShippingcosts();
            $basket['sShippingcosts'] = $shippingCosts['brutto'];
            $basket['sShippingcostsWithTax'] = $shippingCosts['brutto'];
            $basket['sShippingcostsNet'] = $shippingCosts['netto'];
            $basket['sShippingcostsTax'] = $shippingCosts['tax'];

            $dispatch = Shopware()->Modules()->Admin()->sGetPremiumDispatch($selectedDispatchId);
            $userData = Shopware()->Modules()->Admin()->sGetUserData();
            $invoicing = $moptPayoneMain->getParamBuilder()->getInvoicing($basket, $dispatch, $userData);

            $orderLines = [];
            foreach ($invoicing->getItems() as $item) {
                $price = (int)($item->getPr() * 100);
                $quantity = $item->getNo();
                /** the current items index in $basket['content'] */
                $basketItemIndex = array_search($item->getId(), array_column($basket['content'], 'ordernumber'));
                $itemUrl = $router->assemble([
                    'module' => 'frontend',
                    'sViewport' => 'detail',
                    'sArticle' => $basket['content'][$basketItemIndex]['articleID'],
                ]);

                $orderLines[] = [
                    'reference'    => $item->getId(),
                    'name'         => $item->getDe(),
                    'tax_rate'     => (int)($item->getVa()),
                    'unit_price'   => $price,
                    'quantity'     => $quantity,
                    'total_amount' => $price * $quantity,
                    'image_url'    => $basket['content'][$basketItemIndex]['image']['source'],
                    'product_url'  => $itemUrl,
                ];
            }

            $title = $helper->getKlarnaTitle($userData);

            if (!$helper->isKlarnaTelephoneNeededByCountry() && is_null($userData['billingAddressPhone']['phone'])) {
                $userData['billingaddress']['phone'] = 'notNeededByCountry';
            }

            if (!$helper->isKlarnaPersonalIdNeededByCountry() && !$userData['additional']['user']['mopt_payone_klarna_personalid']) {
                $userData['additional']['user']['mopt_payone_klarna_personalid'] = 'notNeededByCountry';
            }

            $view->assign('klarnaOrderLines', json_encode($orderLines));
            $view->assign('isKlarnaBirthdayNeeded', $helper->isKlarnaBirthdayNeeded());
            $view->assign('isKlarnaTelephoneNeeded', $helper->isKlarnaTelephoneNeeded());
            $view->assign('isKlarnaPersonalIdNeeded', $helper->isKlarnaPersonalIdNeeded());
            //shipping
            $view->assign('shippingAddressCity', $userData['shippingaddress']['city']);
            $view->assign('shippingAddressCountry', $userData['additional']['country']['countryiso']);
            $view->assign('shippingAddressEmail', $userData['additional']['user']['email']);
            $view->assign('shippingAddressFamilyName', $userData['shippingaddress']['lastname']);
            $view->assign('shippingAddressGivenName', $userData['shippingaddress']['firstname']);
            $view->assign('shippingAddressPostalCode', $userData['shippingaddress']['zipcode']);
            $view->assign('shippingAddressStreetAddress', $userData['shippingaddress']['street']);
            $view->assign('shippingAddressTitle', $title);
            $view->assign('shippingAddressPhone', $userData['shippingaddress']['phone']);
            // billing
            $view->assign('billingAddressCity', $userData['billingaddress']['city']);
            $view->assign('billingAddressCountry', $userData['additional']['country']['countryiso']);
            $view->assign('billingAddressEmail', $userData['additional']['user']['email']);
            $view->assign('billingAddressFamilyName', $userData['billingaddress']['lastname']);
            $view->assign('billingAddressGivenName', $userData['billingaddress']['firstname']);
            $view->assign('billingAddressPostalCode', $userData['billingaddress']['zipcode']);
            $view->assign('billingAddressStreetAddress', $userData['billingaddress']['street']);
            $view->assign('billingAddressTitle', $title);
            $view->assign('billingAddressPhone', $userData['billingaddress']['phone']);

            // customer
            if (is_null($userData['additional']['user']['birthday'])) {
                $view->assign('customerDateOfBirth', '0000-00-00');
            } else {
                $view->assign('customerDateOfBirth', $userData['additional']['user']['birthday']);
            }
            $view->assign('customerGender', $helper->getKlarnaGender($userData));
            $view->assign('customerNationalIdentificationNumber', $userData['additional']['user']['mopt_payone_klarna_personalid']);

            $view->assign('purchaseCurrency', Shopware()->Container()->get('currency')->getShortName());
            $view->assign('locale', str_replace('_', '-', Shopware()->Shop()->getLocale()->getLocale()));
        }

        if ($request->getActionName() === 'cart' && $session->moptPayoneUserHelperError ) {
            $view->assign('sBasketInfo', $session->moptPayoneUserHelperErrorMessage);
            unset($session->moptPayoneUserHelperError);
            unset($session->moptPayoneUserHelperErrorMessage);
        }

        $templateSuffix = '';
        if ($this->container->get('MoptPayoneMain')->getHelper()->isResponsive()) {
            $templateSuffix = '_responsive';
        }

        $moptPayoneMain = $this->container->get('MoptPayoneMain');

        if (!($moptPayoneMain->getHelper()->isAboCommerceArticleInBasket())
            && $templateSuffix === '') {

            if ($this->isAmazonPayActive($subject)
                && ($payoneAmazonPayConfig = $moptPayoneMain->getHelper()->getPayoneAmazonPayConfig(Shopware()->Shop()->getId()))
            ) {
                $paymenthelper = $moptPayoneMain->getPaymentHelper();
                $paymentId = Shopware()->Session()->moptAmazonpayPaymentId;
                $config = $moptPayoneMain->getPayoneConfig($paymentId);

                if ($request->get('AuthenticationStatus') == 'Failure' || $request->get('AuthenticationStatus') == 'Abandoned') {
                    //logout because confirmOrderReference failed
                    unset($session->moptPayoneAmazonAccessToken);
                    unset($session->moptPayoneAmazonReferenceId);
                    unset($session->moptPayoneAmazonWorkOrderId);
                    unset($session->moptAmazonOrdernum);
                    $session->moptAmazonLogout = true;

                    $errorMessage = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages')->get('errorMessageAuthentication');
                    $view->assign('moptAmazonErrorMessage', $errorMessage);
                }

                if ($request->get('moptAmazonErrorCode')) {
                    $errorMessage = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages')->get('errorMessage' . $request->get('moptAmazonErrorCode'));
                    $view->assign('moptAmazonErrorMessage', $errorMessage);
                    $view->assign('moptAmazonErrorCode', $request->get('moptAmazonErrorCode'));
                }

                if ($session->moptAmazonError) {
                    $view->assign('moptAmazonError', $session->moptAmazonError);
                    unset($session->moptAmazonError);
                }
                if ($session->moptAmazonLogout) {
                    $view->assign('moptAmazonLogout', $session->moptAmazonLogout);
                    unset($session->moptAmazonLogout);
                }
                $view->assign('payoneAmazonPayConfig', $payoneAmazonPayConfig);
                $view->assign('payoneAmazonPayMode', $config['liveMode']);
                $view->extendsTemplate('frontend/checkout/ajax_cart_amazon.tpl');
                $view->extendsTemplate('frontend/checkout/mopt_cart_amazon.tpl');
            }

            if ($this->isPayPalEcsActive($subject) && ($imageUrl = $this->moptPayoneShortcutImgURL())) {
                $view->assign('moptPaypalShortcutImgURL', $imageUrl);
                $view->extendsTemplate('frontend/checkout/mopt_cart' . $templateSuffix . '.tpl');
            }

        }

        if ($templateSuffix === '' && $this->isPaydirektExpressActive($subject) && ($imageUrl = $this->moptPayonePaydirektShortcutImgURL())) {
            $view->assign('moptPayDirektShortcutImgURL', $imageUrl);
            $view->extendsTemplate('frontend/checkout/mopt_cart_paydirekt' . $templateSuffix . '.tpl');
            $view->extendsTemplate('frontend/checkout/ajax_cart_paydirekt' . $templateSuffix . '.tpl');
        }

        if ($templateSuffix === '' && $this->isApplePayActive()) {
            if (is_null($session->get('moptAllowApplePay'))) {
                $view->assign('moptCheckApplePaySupport', 'true');
                $view->extendsTemplate('frontend/checkout/ajax_cart_applepay_devicecheck' . $templateSuffix . '.tpl');
            }
        }

        if (!empty($userPaymentId)) {
            $paymentId = $userPaymentId;
        } else {
            $paymentId = 0;
        }

        $config = $moptPayoneMain->getPayoneConfig($paymentId);
        $confirmActions = array('confirm', 'index', 'payment');

        if ($config['saveTerms'] !== 0) {
            if ($request->getParam('sAGB') === 'on') {
                $session->moptAgbChecked = true;
            }
        }

        if ($config['saveTerms'] === 1 && !in_array($request->getActionName(), $confirmActions)) {
            $session->moptAgbChecked = false;
        }

        $view->assign('moptAgbChecked', $session->moptAgbChecked);
        $view->assign('BSPayoneMode', $config['liveMode']);
    }

    protected function isAmazonPayActive($checkoutController)
    {
        $shop = $this->container->get('shop');
        $payoneMain = $this->container->get('MoptPayoneMain');

        unset(Shopware()->Session()->moptAmazonpayPaymentId);
        /** @var Repository $paymentRepository */
        $paymentRepository = Shopware()->Models()->getRepository(\Shopware\Models\Payment\Payment::class);

        $builder = $paymentRepository->getListQueryBuilder();
        $builder->addFilter(['payment.name' => '%mopt_payone__ewallet_amazon_pay%', 'payment.active' => 1]);
        $query = $builder->getQuery();
        $test = $query->execute();

        if ($payoneMain->getHelper()->isAboCommerceArticleInBasket()) {
            return false;
        }
        if (empty($test)) {
            return false;
        }

        foreach ($test AS $payment) {
            if ($payoneMain->getPaymentHelper()->isPaymentAssignedToSubshop($payment->getId(), $shop->getId())) {
                Shopware()->Session()->moptAmazonpayPaymentId = $payment->getId();
                return true;
            }
        }

        return false;
    }

    protected function isPayPalEcsActive($checkoutController)
    {
        $shop = $this->container->get('shop');
        $payoneMain = $this->container->get('MoptPayoneMain');

        unset(Shopware()->Session()->moptPaypayEcsPaymentId);
        /** @var Repository $paymentRepository */
        $paymentRepository = Shopware()->Models()->getRepository(\Shopware\Models\Payment\Payment::class);

        $builder = $paymentRepository->getListQueryBuilder();
        $builder->addFilter(['payment.name' => '%mopt_payone__ewallet_paypal_express%', 'payment.active' => 1]);
        $query = $builder->getQuery();
        $test = $query->execute();

        if ($payoneMain->getHelper()->isAboCommerceArticleInBasket()) {
            return false;
        }
        if (empty($test)) {
            return false;
        }

        foreach ($test AS $payment) {
            if ($payoneMain->getPaymentHelper()->isPaymentAssignedToSubshop($payment->getId(), $shop->getId())) {
                Shopware()->Session()->moptPaypayEcsPaymentId = $payment->getId();
                return true;
            }
        }

        return false;
    }

    protected function isPaydirektExpressActive($checkoutController)
    {
        $payments = $checkoutController->getPayments();
        $payoneMain = $this->container->get('MoptPayoneMain');
        $payonePaymentHelper = $payoneMain->getPaymentHelper();

        if ($payoneMain->getHelper()->isAboCommerceArticleInBasket()) {
            return false;
        }
        if ($payonePaymentHelper->isPaydirektExpressActive()) {
            foreach ($payments as $paymentMethod) {
                if ($payonePaymentHelper->isPayonePaydirektExpress($paymentMethod['name'])) {
                    Shopware()->Session()->moptPaydirektExpressPaymentId = $paymentMethod['id'];
                    return true;
                }
            }
        }

        return false;
    }

    protected function isApplePayActive()
    {
        $paymentApplePay = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment')->findOneBy(
            ['name' => 'mopt_payone__ewallet_applepay']
        );
        return $paymentApplePay->getActive();
    }

    /**
     * get url to configured and uploaded paypal ecs button
     *
     * @return boolean|string
     */
    protected function moptPayoneShortcutImgURL()
    {
        $shopId = $this->container->get('shop')->getId();

        $builder = Shopware()->Models()->createQueryBuilder();
        $builder->select('button.image')
            ->from('Shopware\CustomModels\MoptPayonePaypal\MoptPayonePaypal', 'button')
            ->where('button.shopId = ?1')
            ->setParameter(1, $shopId);

        $result = $builder->getQuery()->getOneOrNullResult();

        if (!$result) {
            return false;
        }

        return $result['image'];
    }

    /**
     * get url to configured and uploaded paydirekt express button
     *
     * @return boolean|string
     */
    protected function moptPayonePaydirektShortcutImgURL()
    {
        $shopId = $this->container->get('shop')->getId();

        $builder = Shopware()->Models()->createQueryBuilder();

        $builder->select('button.image')
            ->from('Shopware\CustomModels\MoptPayonePayDirekt\MoptPayonePayDirekt', 'button')
            ->where('button.shopId = ?1')
            ->setParameter(1, $shopId);

        $result = $builder->getQuery()->getOneOrNullResult();

        if (!$result) {
            $builder = Shopware()->Models()->createQueryBuilder();
            $builder->select('button.image')
                ->from('Shopware\CustomModels\MoptPayonePayDirekt\MoptPayonePayDirekt', 'button')
                ->where('button.id = ?1')
                ->setParameter(1, 1);
            $result = $builder->getQuery()->getOneOrNullResult();
        }

        if (!$result) {
            return false;
        }

        return $result['image'];
    }
}
