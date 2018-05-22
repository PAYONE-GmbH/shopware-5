<?php

use Shopware\Components\CSRFWhitelistAware;

class Shopware_Controllers_Frontend_FatchipBSPayoneMasterpassCheckout extends Shopware_Controllers_Frontend_Checkout implements CSRFWhitelistAware
{

    /**
     * MoptPaymentPayone Plugin Bootstrap Class
     *
     * @var Shopware_Plugins_Frontend_MoptPaymentPayone_Bootstrap
     */
    protected $plugin;

    /**
     * BSPayone plugin settings
     *
     * @var array
     */
    protected $config;

    /**
     * PayoneMain
     * @var Mopt_PayoneMain
     */
    protected $moptPayoneMain;

    /**
     * PayoneMain
     * @var Mopt_PayonePaymentHelper
     */
    protected $moptPayonePaymentHelper;

    /**
     * PayOne Builder
     *
     * @var Payone_Builder
     */
    protected $payoneServiceBuilder;

    /**
     * Init payment controller
     *
     * @return void
     * @throws Exception
     */
    public function init()
    {
        if (method_exists(parent::init())) {
            parent::init();
        }
        $this->plugin = Shopware()->Container()->get('plugins')->Frontend()->MoptPaymentPayone();
        $this->config = $this->plugin->Config()->toArray();
        $this->moptPayoneMain = $this->plugin->get('MoptPayoneMain');
        $this->moptPayonePaymentHelper = $this->moptPayoneMain->getPaymentHelper();
    }

    /**
     * Action to handle selection of shipping and payment methods
     *
     * @return Enlight_View_Default
     */
    public function shippingPaymentAction()
    {
        parent::shippingPaymentAction();
        $request = $this->Request();
        $params = $request->getParams();
        $session = Shopware()->Session();
        $fatchipBSPayoneMasterpassID = $this->moptPayonePaymentHelper->getPaymentIdFromName('mopt_payone__ewallet_masterpass');
        $session->offsetSet('sPaymentID', $fatchipBSPayoneMasterpassID);

        $this->view->assign('fatchipBSPayoneMasterpassID', $fatchipBSPayoneMasterpassID);
        $this->view->assign('fatchipBSPayoneConfig', $this->config);
        $this->view->assign('sStepActive', 'paymentShipping');

        // override template with ours for xhr requests
        if ($this->Request()->getParam('isXHR')) {
            return $this->view->loadTemplate('frontend/fatchipBSPayoneMasterpassCheckout/fatchip_shipping_payment_core.tpl');
        }
        $this->view->loadTemplate('frontend/fatchipBSPayoneMasterpassCheckout/shipping_payment.tpl');
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getWhitelistedCSRFActions()
    {
        $returnArray = array(
            'shippingPayment',
        );
        return $returnArray;
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function confirmAction()
    {
        parent::confirmAction();
        $this->view->loadTemplate('frontend/fatchipBSPayoneMasterpassCheckout/confirm.tpl');
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     * @throws Exception
     */
    public function finishAction()
    {
        parent::finishAction();

        $this->view->loadTemplate('frontend/fatchipBSPayoneMasterpassCheckout/finish.tpl');
        Shopware()->Session()->unsetAll();
        Shopware()->Modules()->Basket()->sRefreshBasket();
    }
}



