<?php

class Shopware_Controllers_Frontend_FatchipBSPayoneMasterpass extends Shopware_Controllers_Frontend_Payment
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
     * @return void
     * @throws Exception
     */
    public function init()
    {
        $this->plugin = Shopware()->Container()->get('plugins')->Frontend()->MoptPaymentPayone();
        $this->config = $this->plugin->Config()->toArray();
        $this->moptPayoneMain = $this->plugin->get('MoptPayoneMain');
        $this->moptPayonePaymentHelper = $this->moptPayoneMain->getPaymentHelper();
        $this->session = Shopware()->Session();
        $this->payoneServiceBuilder = $this->plugin->get('MoptPayoneBuilder');
        $this->service = $this->payoneServiceBuilder->buildServicePaymentGenericpayment();
    }

    /**
     * Method will be called after
     * checkout confirm
     *
     * @return void
     */
    public function indexAction()
    {
        $this->forward('gateway');
    }

    /**
     * Method will be called when something goes wrong
     * after masterpass login
     *
     * @return void
     * @throws Exception
     */
    public function errorAction()
    {
        $params = $this->Request()->getParams();
        // set errors in session so we can use the error handling in MoptPayment Controller
        $this->session->payoneErrorMessage = $this->moptPayoneMain->getPaymentHelper()
            ->moptGetErrorMessageFromErrorCodeViaSnippet(false, $params['response']->getErrorcode());
        $this->forward('error', 'MoptPaymentPayone', null);
    }

    /**
     * Method will be called after successful
     * masterpass login
     *
     * @return void
     * @throws Exception
     */
    public function successAction()
    {
        $this->buildAndCallGetCheckout();
    }

    /**
     * prepare and do payment server api call
     *
     * @param string $clearingType
     * @param string $walletType
     * @return void
     * @throws Exception
     */
    protected function buildAndCallGetCheckout($clearingType = 'wlt', $walletType = 'MPA')
    {
        $config = $this->moptPayoneMain->getPayoneConfig($this->moptPayonePaymentHelper->getPaymentIdFromName('mopt_payone__ewallet_masterpass'));
        $params = $this->moptPayoneMain->getParamBuilder()->buildAuthorize($config['paymentId']);
        $params['api_version'] = '3.10';
        $basket = Shopware()->Modules()->Basket()->sGetBasket();
        $orderHash = md5(serialize($basket));
        $this->session->offsetSet('moptOrderHash', $orderHash);

        $request = new Payone_Api_Request_Genericpayment($params);

        $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'action', 'data' => Payone_Api_Enum_GenericpaymentAction::MASTERPASS_GETCHECKOUT)
        ));
        $request->setWorkorderId($this->session->offsetGet('fatchipBSPayoneMasterPassWorkOrderId'));
        $request->setPaydata($paydata);
        $request->setClearingtype($clearingType);
        $request->setWallettype($walletType);
        $request->setCurrency("EUR");
        $request->setAmount($basket['AmountNumeric']);

        $this->service = $this->payoneServiceBuilder->buildServicePaymentGenericpayment();
        $this->service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));

        $response = $this->service->request($request);
        switch ($response->getStatus()) {
            case\Payone_Api_Enum_ResponseType::OK;
                $addressData = $response->getPayDataArray();

                $this->forward('register', 'FatchipBSPayoneMasterpassRegister', null, ['BSPayoneAddressData' => $addressData]);
                break;
            default:
                // set errors in session so we can use the error handling in MoptPayment Controller
                $this->session->payoneErrorMessage = $this->moptPayoneMain->getPaymentHelper()
                    ->moptGetErrorMessageFromErrorCodeViaSnippet(false, $response->getErrorcode());
                $this->forward('error', 'MoptPaymentPayone', null);
                break;
        }
    }

    /**
     * calls the BSPayone API
     *
     * @param string $clearingType
     * @param string $walletType
     * @throws Exception
     */
    public function gatewayAction($clearingType = 'wlt', $walletType = 'MPA')
    {
        $config = $this->moptPayoneMain->getPayoneConfig($this->moptPayonePaymentHelper->getPaymentIdFromName('mopt_payone__ewallet_masterpass'));
        $params = $this->moptPayoneMain->getParamBuilder()->buildAuthorize($config['paymentId']);
        $params['api_version'] = '3.10';
        //create hash
        $orderVariables = $this->session['sOrderVariables']->getArrayCopy();
        $orderHash = md5(serialize($this->session['sOrderVariables']));
        $this->session->offsetSet('moptOrderHash', $orderHash);

        if ($config['authorisationMethod'] == 'preAuthorise' || $config['authorisationMethod'] == 'Vorautorisierung') {
            $request = new Payone_Api_Request_Preauthorization($params);
            $this->service = $this->payoneServiceBuilder->buildServicePaymentPreAuthorize();
            $this->session->moptIsAuthorized = false;
        } else {
            $request = new Payone_Api_Request_Authorization($params);
            $this->service = $this->payoneServiceBuilder->buildServicePaymentAuthorize();
            $this->session->moptIsAuthorized = true;
        }

        $request->setWorkorderId($this->session->offsetGet('fatchipBSPayoneMasterPassWorkOrderId'));
        $request->setClearingtype($clearingType);
        $request->setWallettype($walletType);
        $request->setCurrency("EUR");
        $request->setAmount($orderVariables['sAmount']);
        // TODO: use order number
        $rand = rand(100000, 999999);
        $request->setReference($rand);
        $personalData = $this->moptPayoneMain->getParamBuilder()->getPersonalData($orderVariables['sUserData']);
        $request->setPersonalData($personalData);
        $deliveryData = $this->moptPayoneMain->getParamBuilder()->getDeliveryData($orderVariables['sUserData']);
        $request->setDeliveryData($deliveryData);

        $this->service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));

        if (!$config['submitBasket']) {
            // do nothing
        } else {
            $request->setInvoicing($this->moptPayoneMain->getParamBuilder()->getInvoicing($this->getBasket(),$orderVariables['sDispatch'], $orderVariables['sUserData']));
        }

        if ($config['authorisationMethod'] == 'preAuthorise' || $config['authorisationMethod'] == 'Vorautorisierung') {
            $response = $this->service->preauthorize($request);
        } else {
            $response = $this->service->authorize($request);
        }

        switch ($response->getStatus()) {
            case\Payone_Api_Enum_ResponseType::APPROVED;
                $clearingData = $this->moptPayoneMain->getPaymentHelper()->extractClearingDataFromResponse($response);
                if ($clearingData) {
                    $this->session->moptClearingData = $clearingData;
                }
                $this->forward('finishOrder', 'MoptPaymentPayone', null, array('txid' => $response->getTxid(),
                    'hash' => $orderHash));
                break;
            default:
                // set errors in session so we can use the error handling in MoptPayment Controller
                $this->session->payoneErrorMessage = $this->moptPayoneMain->getPaymentHelper()
                    ->moptGetErrorMessageFromErrorCodeViaSnippet(false, $response->getErrorcode());
                $this->forward('error', 'MoptPaymentPayone', null);
                break;
        }
    }
}

