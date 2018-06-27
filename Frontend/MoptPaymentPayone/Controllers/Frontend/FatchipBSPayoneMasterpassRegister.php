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

    /**
     * PayOne Builder
     *
     * @var Payone_Builder
     */
    protected $payoneServiceBuilder;

    /**
     * @var Enlight_Components_Session_Namespace
     */
    protected $session;

    /**
     * @var Payone_Api_Service_Payment_Genericpayment
     */
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
     * This methods loads the template used by the auto registration via the jquery plugin
     * If a registration is not successful gets errors from failed registration attempts
     * and shows them to the user
     *
     * @return void
     */
    public function indexAction()
    {
        $session = Shopware()->Session();
        // this has to be set so shipping methods will work
        $session->offsetSet('sPaymentID', $this->moptPayonePaymentHelper->getPaymentIdFromName('mopt_payone__ewallet_masterpass'));

        if (version_compare(\Shopware::VERSION, '5.2', '>=')) {
            $register = $this->View()->getAssign('errors');
            $errors = array_merge($register['personal'], $register['billing'], $register['shipping']);
        } else {
            $registerArrObj = $this->View()->getAssign('register')->getArrayCopy();
            $register = $this->getArrayFromArrayObjs($registerArrObj);
            $merged_errors = array_merge($register['personal'], $register['billing'], $register['shipping']);
            $errors = $merged_errors['error_flags'];
        }
        if (!empty($errors)) {
            // first error contains SW error message, unset it
            unset($errors[array_keys($errors[0])]);
            $errorMessage = 'Fehler bei der Shop Registrierung:  ' .
                'Bitte korrigieren Sie in Ihrem Masterpass Konto folgende Angaben:';
            $this->view->assign('errorMessage', $errorMessage);
            $this->view->assign('errorFields', array_keys($errors));
        }
        $this->view->loadTemplate('frontend/fatchipBSPayoneMasterpassRegister/index.tpl');
    }

    /**
     * Converts arrayObjects from view template to an accessible array.
     *
     * @param array $arrayObjs Enlight_View_Default->getAssign()->toArray()
     *
     * @see    Enlight_View_Default::getAssign()
     * @return array
     */
    private function getArrayFromArrayObjs($arrayObjs)
    {
        $array = [];
        foreach ($arrayObjs as $key => $arrayObj) {
            $array[$key] = $arrayObj->getArrayCopy();
            foreach ($array[$key] as $arrayObjKey => $value) {
                $array[$key][$arrayObjKey] = $value->getArrayCopy();
            }
        }
        return $array;
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
        $session = Shopware()->Session();

        $addressData = $params['BSPayoneAddressData'];
        // get shippingcountryID  and billingcountryId from countries
        $addressData['countryCodeBillingID'] = $this->getCountryIdFromIso($addressData['country']);
        $addressData['countryCodeShippingID'] = $this->getCountryIdFromIso($addressData['shipping_country']);
        $addressData['salutation'] = $this->getSalutationFromGender($addressData['gender']);
        // not in response, re-use billing salutation
        $addressData['shipping_salutation'] = $addressData['salutation'];
        // shipping_firstname contains shipping_firstname and shipping_lastname, so split it
        $nameParts = preg_split("/\s+(?=\S*+$)/", $addressData['shipping_firstname']);
        // also convert wrong charset of the response, this will be fixed by BSPayone
        $addressData['shipping_firstname'] = utf8_decode($nameParts[0]);
        $addressData['shipping_lastname'] = utf8_decode($nameParts[1]);
        $addressData['firstname'] = utf8_decode($addressData['firstname']);
        $addressData['lastname'] = utf8_decode($addressData['lastname']);
        $addressData['street'] = utf8_decode($addressData['street']);
        $addressData['shipping_street'] = utf8_decode($addressData['shipping_street']);
        $addressData['shipping_addressaddition'] = utf8_decode($addressData['shipping_addressaddition']);
        $addressData['addressaddition'] = utf8_decode($addressData['addressaddition']);

        $session->offsetSet('sPaymentID', $this->moptPayonePaymentHelper->getPaymentIdFromName('mopt_payone__ewallet_masterpass'));
        // set flag so we do not get redirected back to shippingpayment
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
            'register'
        );
        return $returnArray;
    }
}


