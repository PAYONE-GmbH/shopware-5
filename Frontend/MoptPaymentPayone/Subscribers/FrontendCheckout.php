<?php

namespace Shopware\Plugins\MoptPaymentPayone\Subscribers;

use Enlight\Event\SubscriberInterface;

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
            'Shopware_Controllers_Frontend_Checkout::deleteArticleAction::after'  => 'onBasketChangeConfirmPage',
            'Shopware_Controllers_Frontend_Checkout::changeQuantityAction::after' => 'onBasketChangeConfirmPage',
            'sBasket::sGetBasket::after' => 'onBasketDataUpdate',
            'Shopware_Controllers_Frontend_Checkout::saveOrder::before' => 'onSaveOrder',
        ];
    }

    /**
     * hooks before save order for catching abo-commerce orders with
     * payone
     */
    public function onSaveOrder(\Enlight_Hook_HookArgs $args) {
        $return = $args->getReturn();

        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $paymentName = $userData['additional']['payment']['name'];
        $paymentHelper = $this->container->get('MoptPayoneMain')->getPaymentHelper();
        $isRecurringAboCommerceOrder = $this->isRecurringAboCommerceOrder();
        $isPayonePayment = $paymentHelper->isPayonePaymentMethod($paymentName);

        // payone authorization calls are only needed
        // if this is a recurring payone order of an an abocommerce
        // order version > 2.0
        $triggerPayoneAuthorization = ($isRecurringAboCommerceOrder && $isPayonePayment);

        if ($triggerPayoneAuthorization) {
            $this->triggerPayoneAuthorization($args);
        }

        $args->setReturn($return);
    }

    public function triggerPayoneAuthorization(\Enlight_Hook_HookArgs $args) {
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $paymentName = $userData['additional']['payment']['name'];
        $paymentHelper = $this->container->get('MoptPayoneMain')->getPaymentHelper();
        $action = $paymentHelper->getActionFromPaymentName($paymentName);
        $this->forward(array(
            'controller'=>'MoptPaymentPayone',
            'action' => $action,
            'forceSecure' => true
        ));
    }


    /**
     * Check if this is a recurring abo commerce order of abocommerce plugin
     * version higher or equal than 2.0
     *
     * @param void
     * @return bool
     */
    public function isRecurringAboCommerceOrder() {
        // check 1: isRecurring value in session
        $session = Shopware()->Session();
        $isRecurringAboOrder = $session->offsetGet('isRecurringAboOrder');
        if (!$isRecurringAboOrder) {
            // this isn't a recurring abo order, so we can
            // pass all other checks
            return false;
        }

        // check 2: plugin installed
        $pluginManager  = $this->container->get('shopware_plugininstaller.plugin_manager');
        $plugin = $pluginManager->getPluginByName('AboCommerce');

        if (!$plugin->getInstalled()) {
            // if plugin is not installed it cannot be a recurring order indeed
            return false;
        }

        // check 3 plugin version >= 2.0
        $pluginVersion = $plugin->getVersion();
        $pluginVersionOlderThan2 = version_compare($pluginVersion, '2.0', '<');
        if ($pluginVersionOlderThan2) {
            // handling is not needed in versions former 2.0
            return false;
        }

        return true;
    }


    /**
     * Sets a flag when basket data has been updated to prevent unnecessary calls to `sBasket::sGetBasket()`
     *
     * @param \Enlight_Hook_HookArgs $args
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
            ) {
            return;
        }
        // Set redirect flag
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
        $userData = Shopware()->Modules()->Admin()->sGetUserData();

        if ($request->getActionName() === 'shippingPayment') {
            $view->extendsTemplate('frontend/checkout/mopt_shipping_payment.tpl');
            $view->extendsTemplate('frontend/checkout/mopt_shipping_payment_core.tpl');

            // used for amazon error handling
            if ($session->moptAmazonError) {
                $view->assign('moptAmazonError', $session->moptAmazonError);
                unset($session->moptAmazonError);
            }
            if ($session->moptAmazonLogout) {
                $view->assign('moptAmazonLogout', $session->moptAmazonLogout);
                unset($session->moptAmazonLogout);
            }
        }
        
        if ($request->getActionName() === 'cart') {
            if ($session->moptPayPalEcsError) {
                unset($session->moptPayPalEcsError);
                $view->assign('sBasketInfo', Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages')
                                ->get('generalErrorMessage', 'Es ist ein Fehler aufgetreten', true));
            }
        }
        
        $templateSuffix = '';
        if ($this->container->get('MoptPayoneMain')->getHelper()->isResponsive()) {
            $templateSuffix = '_responsive';
        }

        if ($templateSuffix === '' && $this->container->get('MoptPayoneMain')->getPaymentHelper()->isAmazonPayActive()
            && ($payoneAmazonPayConfig = $this->container->get('MoptPayoneMain')->getHelper()->getPayoneAmazonPayConfig())
        ) {
            if ($session->moptAmazonError) {
                $view->assign('moptAmazonError', $session->moptAmazonError);
                unset($session->moptAmazonError);
            }
            if ($session->moptAmazonLogout) {
                $view->assign('moptAmazonLogout', $session->moptAmazonLogout);
                unset($session->moptAmazonLogout);
            }
            $view->assign('payoneAmazonPayConfig', $payoneAmazonPayConfig);
            $view->extendsTemplate('frontend/checkout/ajax_cart_amazon.tpl');
            $view->extendsTemplate('frontend/checkout/mopt_cart_amazon.tpl');
        }

        if ($templateSuffix === '' && $this->isPayPalEcsActive($subject) && ($imageUrl = $this->moptPayoneShortcutImgURL())) {
            $view->assign('moptPaypalShortcutImgURL', $imageUrl);
            $view->extendsTemplate('frontend/checkout/mopt_cart' . $templateSuffix . '.tpl');
        }

        if (!empty($userData['additional']['payment']['id'])) {
            $paymentId = $userData['additional']['payment']['id'];
        } else {
            $paymentId = 0;
        }

        $config = $this->container->get('MoptPayoneMain')->getPayoneConfig($paymentId);
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
}
