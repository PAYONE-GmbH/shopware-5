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
        $params = $this->Request()->getParams();
        $this->buildAndCallGetCheckoutAction();
    }

    /**
     * prepare and do payment server api call
     *
     * @param string $clearingType
     * @param string $walletType
     * @return \Payone_Api_Response_Error|\Payone_Api_Response_Genericpayment_Approved|\Payone_Api_Response_Genericpayment_Redirect $response
     */
    protected function buildAndCallGetCheckoutAction($clearingType = 'wlt', $walletType = 'MPA')
    {
        $router = $this->Front()->Router();
        $config = $this->moptPayoneMain->getPayoneConfig($this->moptPayonePaymentHelper->getPaymentIdFromName('mopt_payone__ewallet_masterpass'));
        $params = $this->moptPayoneMain->getParamBuilder()->buildAuthorize($config['paymentId']);
        $params['api_version'] = '3.10';
        //create hash
        $basket = Shopware()->Modules()->Basket()->sGetBasket();
        $orderHash = md5(serialize($basket));
        $this->session->offsetSet('moptOrderHash',$orderHash);

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
        $paydataArray=$response->getPayDataArray();
        //$this->session->offsetSet('fatchipBSPayoneMasterPassWorkOrderId', $response->getWorkorderId());
    }

    /**
     * Gateway action method
     *
     * Creates paymentclass and redirects to Computop URL
     * Overridden to support recurring payments init
     *
     * @return void
     * @throws Exception
     */
    public function gatewayAction()
    {
        $payment = $this->getPaymentClassForGatewayAction();
        /** @var \Fatchip\CTPayment\CTPaymentMethodsIframe\PaypalStandard $payment */

        if ($this->utils->isAboCommerceArticleInBasket()) {
            $payment->setRTF('I');
            $payment->setTxType('BAID');
        }
        $params = $payment->getRedirectUrlParams();
        $this->session->offsetSet('fatchipCTRedirectParams', $params);
        $this->redirect($payment->getHTTPGetURL($params));
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

