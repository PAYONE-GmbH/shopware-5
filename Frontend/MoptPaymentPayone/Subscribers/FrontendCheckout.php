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
        ) {
            return;
        }
        // targetAction is null for ajax requests
        if ($sTargetAction !== 'confirm' && !is_null($sTargetAction)) {
            return;
        }

        if (!$this->container->get('MoptPayoneMain')->getPaymentHelper()->isPayonePayolutionInstallment($userData['additional']['payment']['name'])
            && !$this->container->get('MoptPayoneMain')->getPaymentHelper()->isPayoneRatepayInstallment($userData['additional']['payment']['name'])
            && !$this->container->get('MoptPayoneMain')->getPaymentHelper()->isPayonePaypalExpress($userData['additional']['payment']['name'])
            && !$this->container->get('MoptPayoneMain')->getPaymentHelper()->isPayoneSecuredInstallments($userData['additional']['payment']['name'])
            && !$this->container->get('MoptPayoneMain')->getPaymentHelper()->isPayonePaypalExpressv2($userData['additional']['payment']['name'])
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
        if ($this->container->get('MoptPayoneMain')->getPaymentHelper()->isPayoneCreditcardForExport($ret['name'])) {
            $sql = 'SELECT `moptCreditcardPaymentData` FROM s_plugin_mopt_payone_creditcard_payment_data WHERE userId = ?';
            $creditcardPaymentData = unserialize(Shopware()->Db()->fetchOne($sql, $userId));
            $paymentData = $creditcardPaymentData;
        }
        if (isset(Shopware()->Session()->moptSaveCreditcardData) && Shopware()->Session()->moptSaveCreditcardData === false) {
            $paymentData = Shopware()->Session()->moptPayment;
        }

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

            if (!is_array($basket['content'])) {
                $basket['content'] = [];
            }

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
            $showmoptCreditCardAgreement = $userData['additional']['user']['accountmode'] == "0" && (! isset(Shopware()->Session()->moptPayment) || Shopware()->Session()->moptPayment === false) ;
            $creditCardAgreement = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/payment')->get('creditCardSavePseudocardnumAgreement');
            $view->assign('moptCreditCardAgreement', str_replace('##Shopname##', Shopware()->Shop()->getTitle(), $creditCardAgreement));
            $view->assign('showMoptCreditCardAgreement', ($showmoptCreditCardAgreement === true) ? '1' : '0');
        }

        if ($request->getActionName() === 'cart' && $session->moptPayoneUserHelperError) {
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

            if ($this->isPayPalv2EcsActive($subject)) {
                $paymentId = $session->moptPaypalv2EcsPaymentId;
                $payonePaypalv2Config = $moptPayoneMain->getPayoneConfig($paymentId);
                $view->assign('payonePaypalv2Currency', Shopware()->Container()->get('currency')->getShortName());
                $view->assign('payonePaypalv2Config', $payonePaypalv2Config);
                $view->extendsTemplate('frontend/checkout/ajax_cart_paypalv2.tpl');
            }

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
        if (($this->isPayoneSecuredInvoiceActive() || $this->isPayoneSecuredDirectdebitActive()) && $request->getActionName() === 'shippingPayment') {
            if ($userData['additional']['payment']['name'] === 'mopt_payone__fin_payone_secured_invoice' || $userData['additional']['payment']['name'] === 'mopt_payone__fin_payone_secured_directdebit') {
                $paymentId = $userData['additional']['payment']['id'];
                $config = $moptPayoneMain->getPayoneConfig($paymentId);
                $view->assign('moptAgbChecked', $session->moptAgbChecked);
                $view->assign('BSPayoneMode', $config['liveMode']);
                $view->assign('BSPayoneMerchantId', $config['merchantId']);
                $view->assign('BSPayoneSecuredMode', $config['liveMode'] === 'false' ? 't' : 'p');
                $view->assign('BSPayonePaylaPartnerId', 'e7yeryF2of8X');
                $view->assign('BSPayoneSecuredToken', 'e7yeryF2of8X' . '_' . $config['merchantId'] . '_' . Shopware()->Session()->get('sessionId'));
            }
        }

        if ($this->isPayoneSecuredInstallmentsActive() && $request->getActionName() === 'shippingPayment') {
            if ($userData['additional']['payment']['name'] === 'mopt_payone__fin_payone_secured_installment') {
                $paymentId = $userData['additional']['payment']['id'];
                $config = $moptPayoneMain->getPayoneConfig($paymentId);
                $plan = $this->getPayoneSecuredInstallmentsPlan($paymentId);
                $view->assign('BSPayoneInstallmentPlan', $plan);
                $view->assign('moptAgbChecked', $session->moptAgbChecked);
                $view->assign('BSPayoneMode', $config['liveMode']);
                $view->assign('BSPayoneMerchantId', $config['merchantId']);
                $view->assign('BSPayoneSecuredMode', $config['liveMode'] === 'false' ? 't' : 'p');
                $view->assign('BSPayonePaylaPartnerId', 'e7yeryF2of8X');
                $view->assign('BSPayoneSecuredToken', 'e7yeryF2of8X' . '_' . $config['merchantId'] . '_' . Shopware()->Session()->get('sessionId'));
            }
        }
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

        foreach ($test as $payment) {
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

        foreach ($test as $payment) {
            if ($payoneMain->getPaymentHelper()->isPaymentAssignedToSubshop($payment->getId(), $shop->getId())) {
                Shopware()->Session()->moptPaypayEcsPaymentId = $payment->getId();
                return true;
            }
        }

        return false;
    }

    protected function isPayPalv2EcsActive($checkoutController)
    {
        $shop = $this->container->get('shop');
        $payoneMain = $this->container->get('MoptPayoneMain');

        unset(Shopware()->Session()->moptPaypalv2EcsPaymentId);
        /** @var Repository $paymentRepository */
        $paymentRepository = Shopware()->Models()->getRepository(\Shopware\Models\Payment\Payment::class);

        $builder = $paymentRepository->getListQueryBuilder();
        $builder->addFilter(['payment.name' => '%mopt_payone__ewallet_paypal_expressv2%', 'payment.active' => 1]);
        $query = $builder->getQuery();
        $test = $query->execute();

        if ($payoneMain->getHelper()->isAboCommerceArticleInBasket()) {
            return false;
        }
        if (empty($test)) {
            return false;
        }

        foreach ($test as $payment) {
            if ($payoneMain->getPaymentHelper()->isPaymentAssignedToSubshop($payment->getId(), $shop->getId())) {
                Shopware()->Session()->moptPaypalv2EcsPaymentId = $payment->getId();
                return true;
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

    protected function isPayoneSecuredInvoiceActive()
    {
        $payment = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment')->findOneBy(
            ['name' => 'mopt_payone__fin_payone_secured_invoice']
        );
        return $payment->getActive();
    }

    protected function isPayoneSecuredInstallmentsActive()
    {
        $payment = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment')->findOneBy(
            ['name' => 'mopt_payone__fin_payone_secured_installment']
        );
        return $payment->getActive();
    }

    protected function isPayoneSecuredDirectdebitActive()
    {
        $payment = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment')->findOneBy(
            ['name' => 'mopt_payone__fin_payone_secured_directdebit']
        );
        return $payment->getActive();
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
     * Calculates the rates by from user defined rate
     * called from an ajax request with ratePay parameters (ratepay.js)
     * map RatePay API parameters and request the payone API
     *
     */
    public function getPayoneSecuredInstallmentsPlan($paymentId)
    {
        $payoneMain = $this->container->get('MoptPayoneMain');
        $basket = $payoneMain->sGetBasket();
        $amount = empty($basket['AmountWithTaxNumeric']) ? $basket['AmountNumeric'] : $basket['AmountWithTaxNumeric'];
        $config = $payoneMain->getPayoneConfig($paymentId);
        $financeType = \Payone_Api_Enum_PayoneSecuredType::PIN;

        try {
            $result = $this->buildAndCallCalculatePayone($config, 'fnc', $financeType, $amount);
            if ($result instanceof \Payone_Api_Response_Genericpayment_Ok) {
                $formattedResult = $this->formatInstallmentOptions($result->getRawResponse());
            }
        }
        catch (\Exception $e) {
        }
        return $formattedResult;
    }

    public function formatInstallmentOptions($result)
    {
        $aFormattedData = [];
        $aFormattedData['status'] = $result['status'];
        $aFormattedData['workorderid'] = $result['workorderid'];
        Shopware()->Session()->mopt_payone__payone_secured_installment_workorderid = $aFormattedData['workorderid'];
        $aFormattedData['amountValue'] = $this->fcpoPriceFromCentToDec($result['add_paydata[amount_value]']);
        $aFormattedData['amountCurrency'] = $result['add_paydata[amount_currency]'];
        $aFormattedData['plans'] = [];
        $iCurrPlan = 0;
        while (true) {
            if (!isset ($result['add_paydata[total_amount_currency_' . $iCurrPlan . ']'])) {
                break;
            }

            $aFormattedData['plans'][$iCurrPlan] = [
                'effectiveInterestRate' => $this->fcpoPriceFromCentToDec($result['add_paydata[effective_interest_rate_' . $iCurrPlan . ']']),
                'firstRateDate' => $result['add_paydata[first_rate_date_' . $iCurrPlan . ']'],
                'installmentOptionId' => $result['add_paydata[installment_option_id_' . $iCurrPlan . ']'],
                'lastRateAmountCurrency' => $result['add_paydata[last_rate_amount_currency_' . $iCurrPlan . ']'],
                'lastRateAmountValue' => $this->fcpoPriceFromCentToDec($result['add_paydata[last_rate_amount_value_' . $iCurrPlan . ']']),
                'linkCreditInformationHref' => $result['add_paydata[link_credit_information_href_' . $iCurrPlan . ']'],
                'linkCreditInformationType' => $result['add_paydata[link_credit_information_type_' . $iCurrPlan . ']'],
                'monthlyAmountCurrency' => $result['add_paydata[monthly_amount_currency_' . $iCurrPlan . ']'],
                'monthlyAmountValue' => $this->fcpoPriceFromCentToDec($result['add_paydata[monthly_amount_value_' . $iCurrPlan . ']']),
                'nominalInterestRate' => $this->fcpoPriceFromCentToDec($result['add_paydata[nominal_interest_rate_' . $iCurrPlan . ']']),
                'numberOfPayments' => $result['add_paydata[number_of_payments_' . $iCurrPlan . ']'],
                'totalAmountCurrency' => $result['add_paydata[total_amount_currency_' . $iCurrPlan . ']'],
                'totalAmountValue' => $this->fcpoPriceFromCentToDec($result['add_paydata[total_amount_value_' . $iCurrPlan . ']']),
            ];

            $iCurrPlan++;
        }

        return $aFormattedData;
    }

    protected function fcpoPriceFromCentToDec($iAmount)
    {
        return number_format($iAmount / 100, 2, ',', '.');
    }

    /**
     * prepare and do payment server api call
     *
     * @param array $config
     * @param string $clearingType
     * @param string $financetype
     * @param string $amount
     * @return \Payone_Api_Response_Error|\Payone_Api_Response_Genericpayment_Ok $response
     */
    protected function buildAndCallCalculatePayone($config, $clearingType, $financetype, $amount)
    {
        $payoneMain = $this->container->get('MoptPayoneMain');
        $paramBuilder = $payoneMain->getParamBuilder();
        $personalData = $paramBuilder->getPersonalData(Shopware()->Modules()->Admin()->sGetUserData());
        $params = $payoneMain->getParamBuilder()->buildAuthorize($config['paymentId']);
        $params['api_version'] = '3.10';
        $params['financingtype'] = $financetype;
        $basket = $payoneMain->sGetBasket();
        //create hash
        $orderHash = md5(serialize($basket));
        Shopware()->Session()->moptOrderHash = $orderHash;

        $request = new \Payone_Api_Request_Genericpayment($params);

        $paydata = new \Payone_Api_Request_Parameter_Paydata_Paydata();
        $paydata->addItem(new \Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'action', 'data' => \Payone_Api_Enum_GenericpaymentAction::PAYONE_SECURED_INSTALLMENT_CALCULATE)
        ));
        $amountWithShipping = $amount; // Docs state "smallest currency Unit???
        $request->setPaydata($paydata);
        $request->setAmount($amountWithShipping);
        $request->setCurrency(Shopware()->Container()->get('currency')->getShortName());

        $request->setClearingtype($clearingType);
        $builder = Shopware()->Container()->get('MoptPayoneBuilder');

        $service = $builder->buildServicePaymentGenericpayment();
        $response = $service->request($request);
        return $response;
    }
}
