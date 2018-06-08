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

    protected $session;

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


    public function indexAction()
    {
        $this->forward('gateway');
    }

    public function errorAction()
    {
        $params = $this->Request()->getParams();
        die(var_dump($params));
    }

    public function successAction()
    {
        $this->buildAndCallGetCheckout();
    }

    /**
     * prepare and do payment server api call
     *
     * @param string $clearingType
     * @param string $walletType
     * @return \Payone_Api_Response_Error|\Payone_Api_Response_Genericpayment_Approved|\Payone_Api_Response_Genericpayment_Redirect $response
     */
    protected function buildAndCallGetCheckout($clearingType = 'wlt', $walletType = 'MPA')
    {
        $router = $this->Front()->Router();
        $config = $this->moptPayoneMain->getPayoneConfig($this->moptPayonePaymentHelper->getPaymentIdFromName('mopt_payone__ewallet_masterpass'));
        $params = $this->moptPayoneMain->getParamBuilder()->buildAuthorize($config['paymentId']);
        $params['api_version'] = '3.10';
        //create hash
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
                $this->forward('error');
                break;
        }

        // Steftest
        //$this->session->offsetSet('fatchipBSPayoneMasterPassWorkOrderId', $response->getWorkorderId());
    }

    public function gatewayAction($clearingType = 'wlt', $walletType = 'MPA')
    {
        $config = $this->moptPayoneMain->getPayoneConfig($this->moptPayonePaymentHelper->getPaymentIdFromName('mopt_payone__ewallet_masterpass'));
        $params = $this->moptPayoneMain->getParamBuilder()->buildAuthorize($config['paymentId']);
        $params['api_version'] = '3.10';
        //create hash
        $orderVariables = $this->session['sOrderVariables']->getArrayCopy();
        $orderHash = md5(serialize($session['sOrderVariables']));
        $this->session->offsetSet('moptOrderHash', $orderHash);

        $request = new Payone_Api_Request_Preauthorization($params);

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

        $this->service = $this->payoneServiceBuilder->buildServicePaymentPreAuthorize();
        $this->session->moptIsAuthorized = false;
        $this->service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));

        $response = $this->service->preauthorize($request);
        switch ($response->getStatus()) {
            case\Payone_Api_Enum_ResponseType::APPROVED;
                $this->forward('finishOrder', 'MoptPaymentPayone', null, array('txid' => $response->getTxid(),
                    'hash' => $orderHash));
                break;
            default:
                $this->forward('error');
                break;
        }
    }


    /**
     * Recurring payment action method.
     */
    public function recurringAction()
    {
        $params = $this->Request()->getParams();
        $this->container->get('front')->Plugins()->ViewRenderer()->setNoRender();
        $payment = $this->getPaymentClassForGatewayAction();
        $payment->setRTF('R');
        $requestParams = $payment->getRedirectUrlParams();
        $requestParams['BillingAgreementID'] = $this->getParamPaypalBillingAgreementId($params['orderId']);
        $response = $this->plugin->callComputopService($requestParams, $payment, 'PaypalRecurring', $payment->getRecurringURL());

        if ($this->Request()->isXmlHttpRequest()) {
            if ($response->getStatus() !== CTEnumStatus::OK) {
                $data = [
                    'success' => false,
                    'message' => "Error",
                ];
            } else {
                $orderNumber = $this->saveOrder(
                    $response->getTransID(),
                    $response->getPayID(),
                    self::PAYMENTSTATUSRESERVED
                );
                $this->saveTransactionResult($response);
                $this->updateRefNrWithComputopFromOrderNumber($orderNumber);
                $data = [
                    'success' => true,
                    'data' => [
                        'orderNumber' => $orderNumber,
                        'transactionId' => $response->getTransID(),
                    ],
                ];
            }
            echo Zend_Json::encode($data);
        }
    }

    /**
     * returns paypal billing agreementId from
     * the last order to use it to authorize
     * recurring payments
     *
     * @param string $orderNumber shopware order-number
     *
     * @return boolean | string paypal biilingAgreementId
     */
    protected function getParamPaypalBillingAgreementId($orderNumber)
    {
        $order = Shopware()->Models()->getRepository('Shopware\Models\Order\Order')->findOneBy(['id' => $orderNumber]);
        $agreementId = false;
        if ($order) {
            $orderAttribute = $order->getAttribute();
            $agreementId = $orderAttribute->getfatchipctPaypalbillingagreementid();

        }
        return $agreementId;
    }
}

