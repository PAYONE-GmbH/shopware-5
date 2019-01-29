<?php

use Shopware\Components\CSRFWhitelistAware;

class Shopware_Controllers_Frontend_FatchipBSPayonePaypalInstallmentCheckout extends Shopware_Controllers_Frontend_Checkout implements CSRFWhitelistAware
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
        if (method_exists(parent::init())) {
            parent::init();
        }
        $this->plugin = Shopware()->Container()->get('plugins')->Frontend()->MoptPaymentPayone();
        $this->config = $this->plugin->Config()->toArray();
        $this->moptPayoneMain = $this->plugin->get('MoptPayoneMain');
        $this->moptPayonePaymentHelper = $this->moptPayoneMain->getPaymentHelper();
        $this->session = Shopware()->Session();
        $this->payoneServiceBuilder = $this->plugin->get('MoptPayoneBuilder');
        $this->service = $this->payoneServiceBuilder->buildServicePaymentGenericpayment();
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
    public function successAction()
    {
        $this->forward('confirm');
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function confirmAction()
    {
        // return to shippingpayment if checkout result form the previous step is not set
        $workorderId = Shopware()->Session()->offsetGet('moptPaypalInstallmentWorkerId');
        if (empty($workorderId)) {
            $this->redirect(['controller' => 'checkout', 'action' => 'shippingPayment']);
        } else {
            parent::confirmAction();
            $installmentData = $this->request->getParams();
            $this->view->loadTemplate('frontend/fatchipBSPayonePaypalInstallmentCheckout/confirm.tpl');
            $this->view->assign('Installment', $installmentData);
        }
    }

    /**
     * calls the BSPayone API
     *
     * @param string $clearingType
     * @param string $walletType
     * @throws Exception
     */
    public function gatewayAction()
    {
        $this->buildAndCallCreatePayment();
    }

    /**
     * prepare and do payment server api call
     *
     * @param string $clearingType
     * @param string $financingType
     * @return void
     * @throws Exception
     */
    protected function buildAndCallCreatePayment($clearingType = 'fnc', $financingType = 'PPI')
    {
        $config = $this->moptPayoneMain->getPayoneConfig($this->moptPayonePaymentHelper->getPaymentIdFromName('mopt_payone__fin_paypal_installment'));
        $params = $this->moptPayoneMain->getParamBuilder()->buildAuthorize($config['paymentId']);
        $params['api_version'] = '3.10';
        $userData = $this->getUserData();
        $orderVariables = $this->session['sOrderVariables']->getArrayCopy();
        $router = $this->Front()->Router();
        $request = new Payone_Api_Request_Genericpayment($params);
        $payData = new Payone_Api_Request_Parameter_Paydata_Paydata();

        if ($config['authorisationMethod'] == 'preAuthorise' || $config['authorisationMethod'] == 'Vorautorisierung') {
            $addPaydataAction = Payone_Api_Enum_GenericpaymentAction::PAYPAL_INSTALLMENT_RESERVERVATION;
            $this->session->moptIsAuthorized = false;
        } else {
            $addPaydataAction = Payone_Api_Enum_GenericpaymentAction::PAYPAL_INSTALLMENT_SALE;
            $this->session->moptIsAuthorized = true;
        }

        $payData->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'action', 'data' => $addPaydataAction)
        ));

        $request->setInvoicing($this->moptPayoneMain->getParamBuilder()->getInvoicing($orderVariables['sBasket'],$orderVariables['sDispatch'], $orderVariables['sUserData']));

        // sending these set by setInvoicing  leads to errors
        unset($request->invoice_deliverydate);
        unset($request->invoice_deliveryenddate);
        unset($request->invoice_deliverymode);
        unset($request->invoiceappendix);
        unset($request->invoiceid);

        $request->setPaydata($payData);
        $request->setClearingtype($clearingType);
        $request->setFinancingType($financingType);
        $request->setAmount($this->getAmount());
        $request->setCurrency($orderVariables['sBasket']['sCurrencyName']);

        $personalData = $this->moptPayoneMain->getParamBuilder()->getPersonalData($userData);
        $request->setPersonalData($personalData);
        $deliveryData = $this->moptPayoneMain->getParamBuilder()->getDeliveryData($userData);
        $request->setDeliveryData($deliveryData);


        // $request->setCountry();
        $request->setSuccessurl(
            $router->assemble(['controller' => 'FatchipBSPayonePaypalInstallment', 'action' => 'success', 'forceSecure' => true, 'appendSession' => false])
        );
        $request->setErrorurl(
            $router->assemble(['controller' => 'FatchipBSPayonePaypalInstallment', 'action' => 'error', 'forceSecure' => true, 'appendSession' => false])
        );
        $request->setBackurl(
            $router->assemble(['controller' => 'FatchipBSPayonePaypalInstallment', 'action' => 'abort', 'forceSecure' => true, 'appendSession' => false])
        );

        $this->service = $this->payoneServiceBuilder->buildServicePaymentGenericpayment();
        $this->service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));

        $response = $this->service->request($request);
        switch ($response->getStatus()) {
            case\Payone_Api_Enum_ResponseType::REDIRECT;
                $this->session->offsetSet('moptPaypalInstallmentWorkerId', $response->getWorkorderId());
                $this->redirect($response->getRedirecturl());
                break;
            default:
                $this->session->offsetUnset('moptPaypalInstallmentWorkerId');
                // set errors in session so we can use the error handling in MoptPayment Controller
                $this->session->payoneErrorMessage = $this->moptPayoneMain->getPaymentHelper()
                    ->moptGetErrorMessageFromErrorCodeViaSnippet(false, $response->getErrorcode());
                $this->forward('error', 'MoptPaymentPayone', null);
                break;
        }
    }

    /**
     * Return the full amount to pay.
     *
     * @return float
     */
    public function getAmount()
    {
        $orderVariables = $this->session['sOrderVariables']->getArrayCopy();
        $user = $orderVariables['sUserData'];
        $basket = $orderVariables['sBasket'];
        if (!empty($user['additional']['charge_vat'])) {
            return empty($basket['AmountWithTaxNumeric']) ? $basket['AmountNumeric'] : $basket['AmountWithTaxNumeric'];
        }

        return $basket['AmountNetNumeric'];
    }
}



