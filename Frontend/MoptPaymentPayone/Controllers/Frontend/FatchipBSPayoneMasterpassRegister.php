<?php

use Shopware\Components\CSRFWhitelistAware;

class Shopware_Controllers_Frontend_FatchipBSPayoneMasterpassRegister extends Shopware_Controllers_Frontend_Register implements CSRFWhitelistAware
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

    protected $payoneServiceBuilder;
    protected $service = null;

    /**
     * Init payment controller
     *
     * Init method does not exist in SW >5.2!
     * Also session property was removed in SW5.2?
     *
     * @return void
     * @throws Exception
     */
    public function init()
    {
        if (method_exists('Shopware_Controllers_Frontend_Register', 'init')) {
            parent::init();
        }
        $this->plugin = Shopware()->Container()->get('plugins')->Frontend()->MoptPaymentPayone();
        $this->config = $this->plugin->Config()->toArray();
        $this->moptPayoneMain = $this->plugin->get('MoptPayoneMain');
        $this->moptPayonePaymentHelper = $this->moptPayoneMain->getPaymentHelper();
        $this->payoneServiceBuilder = $this->plugin->Application()->MoptPayoneBuilder();
    }

    /**
     * Registers users in shopware.
     *
     * Assigns all neccessary values to view
     * Registration is handled by a jquery plugin
     *
     * @return void
     */
    public function registerAction()
    {
        $request = $this->Request();
        $params = $request->getParams();
        $session= Shopware()->Session();
        $addressData = $params['BSPayoneAddressData'];
        // get shippingcountryID  and billingcountryId from countries
        $addressData['countryCodeBillingID'] = $this->getCountryIdFromIso($addressData['country']);
        $addressData['countryCodeShippingID'] = $this->getCountryIdFromIso($addressData['shipping_country']);
        $addressData['salutation'] = $this->getSalutationFromGender($addressData['gender']);
            // StefTEst Remove
        $testBefore = $session->offsetGet('sPaymentID');

        $session->offsetSet('sPaymentID', $this->moptPayonePaymentHelper->getPaymentIdFromName('mopt_payone__ewallet_masterpass'));
        // StefTEst RovePayone Connector
        $testAfter = $session->offsetGet('sPaymentID');
        // set flag so we do not get edirected back to shippingpayment
        $session->offsetSet('moptFormSubmitted', true);

        $this->view->assign('fatchipBSPayone', $addressData);
        $this->view->loadTemplate('frontend/fatchipBSPayoneMasterpassRegister/index.tpl');
    }

    /**
     * @ignore <description>
     * @param $countryIso
     * @return string
     */
    public function getCountryIdFromIso($countryIso)
    {
        $countrySql = 'SELECT id FROM s_core_countries WHERE countryiso=?';
        return Shopware()->Db()->fetchOne($countrySql, [$countryIso]);
    }

    /**
     * @ignore <description>
     * @param $gender
     * @return string
     */
    public function getSalutationFromGender($gender)
    {
        $salutation = $gender === 'M' ? 'mr' : 'ms';
        return $salutation;
    }

    /**
     * {inheritdoc}
     *
     * @return array
     */
    public function getWhitelistedCSRFActions()
    {
        $returnArray = array(
            'saveRegister',
        );
        return $returnArray;
    }
}


