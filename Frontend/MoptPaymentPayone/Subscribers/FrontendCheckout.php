<?php

namespace Shopware\Plugins\MoptPaymentPayone\Subscribers;

use Enlight\Event\SubscriberInterface;
use Mopt_PayoneMain;
use Mopt_PayonePaymentHelper;

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
            // only used for payolution installments for now
            // redirects the customer back to shippingpayment for re-calculation of payment conditions
            'Shopware_Controllers_Frontend_Checkout::deleteArticleAction::after' => 'onBasketChangeConfirmPage',
            'Shopware_Controllers_Frontend_Checkout::changeQuantityAction::after' => 'onBasketChangeConfirmPage',
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
     * only used for payolution installment to re-calculate payment conditions
     *
     * @param \Enlight_Hook_HookArgs $arguments
     * @return type
     */
    public function onBasketChangeConfirmPage(\Enlight_Hook_HookArgs $arguments)
    {
        $action = Shopware()->Modules()->Admin()->sSYSTEM->_GET['action'];
        $sTargetAction = Shopware()->Modules()->Admin()->sSYSTEM->_GET['sTargetAction'];

        if ($action !== 'addArticle' && $action !== 'changeQuantity' && $action !== 'deleteArticle') {
            return;
        }
        if ($sTargetAction !== 'confirm') {
            return;
        }

        $ret = $arguments->getReturn();
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        if (!$this->container->get('MoptPayoneMain')->getPaymentHelper()->isPayonePayolutionInstallment($userData['additional']['payment']['name'])
            && !$this->container->get('MoptPayoneMain')->getPaymentHelper()->isPayoneRatepayInstallment($userData['additional']['payment']['name'])
            && !$this->container->get('MoptPayoneMain')->getPaymentHelper()->isPayonePaypalInstallment($userData['additional']['payment']['name'])
            && !$this->container->get('MoptPayoneMain')->getPaymentHelper()->isPayonePaypDirektExpress($userData['additional']['payment']['name'])
        ) {
            return;
        }
        // Set redirect flag
        Shopware()->Session()->moptBasketChanged = true;
        $arguments->setReturn($ret);
    }

    /**
     * set redirect flag for redirecting to paymentshipping in case basket is changed
     * only used for some payone payment methods
     *
     * @param \Enlight_Hook_HookArgs $arguments
     * @return type
     */
    public function onBasketChange(\Enlight_Hook_HookArgs $arguments)
    {
        $ret = $arguments->getReturn();
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        if (!$this->container->get('MoptPayoneMain')->getPaymentHelper()->isPayonePaypalInstallment($userData['additional']['payment']['name']) &&
            !$this->container->get('MoptPayoneMain')->getPaymentHelper()->isPayonePaydirektExpress($userData['additional']['payment']['name'])
        ) {
            return;
        }
        // Set redirect flag
        if (isset(Shopware()->Session()->moptPaypalInstallmentWorkerId)) {
            Shopware()->Session()->moptBasketChanged = true;
        }
        if (isset(Shopware()->Session()->moptPaydirektExpressWorkerId)) {
            Shopware()->Session()->moptBasketChanged = true;
        }
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

        //check if payment method is PayPal ecs
        /** @var Mopt_PayonePaymentHelper $helper */
        $helper = $this->container->get('MoptPayoneMain')->getPaymentHelper();
        if ($helper->getPaymentNameFromId($userPaymentId) == 'mopt_payone__ewallet_paypal') {
            if (!$this->isShippingAddressSupported($orderVars['sUserData']['shippingaddress'])) {
                $view->assign('invalidShippingAddress', true);
                $view->assign('sBasketInfo', Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages')
                    ->get('packStationError', 'Die Lieferung an eine Packstation ist mit dieser Zahlungsart leider nicht möglich', true));
            }
        }

        $paymentName = $helper->getPaymentNameFromId($request->getParam('payment'));
        $router = $this->container->get('router');

        if ($request->getActionName() === 'saveShippingPayment' && $helper->isPayoneKlarna($paymentName)) {
            $successUrl = $router->assemble(array(
                'action'        => 'success',
                'forceSecure'   => true,
                'appendSession' => false,
            ));
            $backUrl = $router->assemble(array(
                'action'        => 'cancel',
                'forceSecure'   => true,
                'appendSession' => false,
            ));
            $errorUrl = $router->assemble(array(
                'action'        => 'failure',
                'forceSecure'   => true,
                'appendSession' => false,
            ));

            // TODO: clean session vars
            $session->offsetSet('successUrl', $successUrl);
            $session->offsetSet('backUrl', $backUrl);
            $session->offsetSet('errorUrl', $errorUrl);
        }

        if ($request->getActionName() === 'shippingPayment') {
            $view->extendsTemplate('frontend/checkout/mopt_shipping_payment.tpl');
            $view->extendsTemplate('frontend/checkout/mopt_shipping_payment_core.tpl');

            if($request->get('moptAmazonErrorCode')) {
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
            /* @see https://integrator.payone.de/jira/browse/SW-236 */
            if ($session->moptPaypalEcsWorkerId) {
                unset($session->moptPaypalEcsWorkerId);
            }

            // order lines
            /** @var Mopt_PayoneMain $moptPayoneMain */
            $moptPayoneMain = $this->container->get('MoptPayoneMain');

            $selectedDispatchId = Shopware()->Session()['sDispatch'];
            $basket = $moptPayoneMain->sGetBasket();
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

            $countryIso2 = $userData['additional']['country']['countryiso'];
            $salutation = $userData['additional']['user']['salutation'];
            switch ($countryIso2) {
                case 'AT':
                case 'DE':
                    $title = ($salutation === 'mr') ? 'Herr' : 'Frau';
                    break;
                /** @noinspection PhpDuplicateSwitchCaseBodyInspection */
                case 'CH':
                    $title = ($salutation === 'mr') ? 'Herr' : 'Frau';
                    break;
                case 'GB':
                case 'US':
                    $title = ($salutation === 'mr') ? 'Mr' : 'Ms';
                    break;
                case 'DK':
                case 'FI':
                case 'SE':
                case 'NL':
                case 'NO':
                    $title = ($salutation === 'mr') ? 'Dhr.' : 'Mevr.';
                    break;
            }

            $view->assign('klarnaOrderLines', json_encode($orderLines));

            //shipping
            $view->assign('shippingAddressCity', $userData['shippingaddress']['city']);
            $view->assign('shippingAddressCountry', $userData['additional']['country']['countryiso']);
            $view->assign('shippingAddressEmail', $userData['additional']['user']['email']);
            $view->assign('shippingAddressFamilyName', $userData['shippingaddress']['lastname']);
            $view->assign('shippingAddressGivenName', $userData['shippingaddress']['firstname']);
            $view->assign('shippingAddressPostalCode', $userData['shippingaddress']['zipcode']);
            $view->assign('shippingAddressStreetAddress', $userData['shippingaddress']['street']);
            $view->assign('shippingAddressStreetTitle', $title);
            // billing
            $view->assign('billingAddressCity', $userData['billingaddress']['city']);
            $view->assign('billingAddressCountry', $userData['additional']['country']['countryiso']);
            $view->assign('billingAddressEmail', $userData['additional']['user']['email']);
            $view->assign('billingAddressFamilyName', $userData['billingaddress']['lastname']);
            $view->assign('billingAddressGivenName', $userData['billingaddress']['firstname']);
            $view->assign('billingAddressPostalCode', $userData['billingaddress']['zipcode']);
            $view->assign('billingAddressStreetAddress', $userData['billingaddress']['street']);
            $view->assign('billingAddressStreetTitle', $title);

            // customer
            $view->assign('customerDateOfBirth', $userData['additional']['user']['birthday']);
            $view->assign('customerGender', $salutation === 'mr' ? 'male' : 'female');
            $view->assign('customerNationalIdentificationNumber', '');

            $view->assign('purchaseCurrency', Shopware()->Container()->get('currency')->getShortName());
            $view->assign('locale', str_replace('_', '-', Shopware()->Shop()->getLocale()->getLocale()));
        }

        if ($request->getActionName() === 'cart') {
            if ($session->moptPayPalEcsError) {
                unset($session->moptPayPalEcsError);
                $view->assign('sBasketInfo', Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages')
                    ->get('generalErrorMessage', 'Es ist ein Fehler aufgetreten', true));
            }
            if ($session->moptPaydirektExpressError) {
                unset($session->moptPayDirektExpressError);
                $view->assign('sBasketInfo', Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages')
                    ->get('generalErrorMessage', 'Es ist ein Fehler aufgetreten', true));
            }
        }

        $templateSuffix = '';
        if ($this->container->get('MoptPayoneMain')->getHelper()->isResponsive()) {
            $templateSuffix = '_responsive';
        }

        $moptPayoneMain = $this->container->get('MoptPayoneMain');

        if (!($moptPayoneMain->getHelper()->isAboCommerceArticleInBasket())
            && $templateSuffix === '') {

            if ($this->isAmazonPayActive($subject)
                && ($payoneAmazonPayConfig = $moptPayoneMain->getHelper()->getPayoneAmazonPayConfig())
            ) {
                $paymenthelper = $moptPayoneMain->getPaymentHelper();
                $config = $moptPayoneMain->getPayoneConfig($paymenthelper->getPaymentAmazonPay()->getId());

                if($request->get('AuthenticationStatus') == 'Failure' || $request->get('AuthenticationStatus') == 'Abandoned') {
                    //logout because confirmOrderReference failed
                    unset($session->moptPayoneAmazonAccessToken);
                    unset($session->moptPayoneAmazonReferenceId);
                    unset($session->moptPayoneAmazonWorkOrderId);
                    unset($session->moptAmazonOrdernum);
                    $session->moptAmazonLogout = true;

                    $errorMessage = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages')->get('errorMessageAuthentication');
                    $view->assign('moptAmazonErrorMessage', $errorMessage);
                }

                if($request->get('moptAmazonErrorCode')) {
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
        $payments = $checkoutController->getPayments();
        $payoneMain = $this->container->get('MoptPayoneMain');
        $payonePaymentHelper = $payoneMain->getPaymentHelper();

        if ($payoneMain->getHelper()->isAboCommerceArticleInBasket()) {
            return false;
        }

        foreach ($payments as $paymentMethod) {
            if ($payonePaymentHelper->isAmazonPayActive($paymentMethod)) {
                return true;
            }
        }

        return false;
    }

    protected function isPayPalEcsActive($checkoutController)
    {
        $payments = $checkoutController->getPayments();
        $payoneMain = $this->container->get('MoptPayoneMain');
        $payonePaymentHelper = $payoneMain->getPaymentHelper();

        if ($payoneMain->getHelper()->isAboCommerceArticleInBasket()) {
            return false;
        }

        foreach ($payments as $paymentMethod) {
            if ($payonePaymentHelper->isPayPalEcsActive($payoneMain, $paymentMethod)) {
                Shopware()->Session()->moptPaypayEcsPaymentId = $paymentMethod['id'];
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

    /**
     * get url to configured and uploaded paypal ecs button
     *
     * @return boolean|string
     */
    protected function moptPayoneShortcutImgURL()
    {
        $localeId = $this->container->get('shop')->getLocale()->getId();

        $builder = Shopware()->Models()->createQueryBuilder();
        $builder->select('button.image')
            ->from('Shopware\CustomModels\MoptPayonePaypal\MoptPayonePaypal', 'button')
            ->where('button.localeId = ?1')
            ->setParameter(1, $localeId);

        $result = $builder->getQuery()->getOneOrNullResult();

        if (!$result) {
            $builder->resetDQLParts();
            $builder->select('button.image')
                ->from('Shopware\CustomModels\MoptPayonePaypal\MoptPayonePaypal', 'button')
                ->where('button.isDefault = ?1')
                ->setParameter(1, true);

            $result = $builder->getQuery()->getOneOrNullResult();
        }

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
        $localeId = $this->container->get('shop')->getLocale()->getId();

        $builder = Shopware()->Models()->createQueryBuilder();

        $builder->select('button.image')
            ->from('Shopware\CustomModels\MoptPayonePayDirekt\MoptPayonePayDirekt', 'button')
            ->where('button.localeId = ?1')
            ->setParameter(1, $localeId);

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

    /**
     * Check if address is confirm with PayPal Configuration (packStation check)
     *
     * @param $shippingData
     * @return bool
     */
    private function isShippingAddressSupported($shippingData)
    {
        $config = Shopware()->Container()->get('MoptPayoneMain')->getHelper()->getPayonePayPalConfig();
        if ($config) {
            if ($config->getPackStationMode() == 'deny') {
                //Check if address is PackStation
                foreach ($shippingData as $addressPart) {
                    if (!is_array($addressPart)) {
                        if (strpos(strtolower($addressPart), 'packstation') !== false) {
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }
}
