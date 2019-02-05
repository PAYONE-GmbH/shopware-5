<?php

class Shopware_Controllers_Frontend_FatchipBSPayonePaypalInstallment extends Shopware_Controllers_Frontend_Payment
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
        $this->forward('pay');
    }

    /**
     * Method will be called after
     * checkout confirm
     *
     * @return void
     */
    public function payAction()
    {
        $config = $this->moptPayoneMain->getPayoneConfig($this->moptPayonePaymentHelper->getPaymentIdFromName('mopt_payone__fin_paypal_installment'));
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

        $request->setClearingtype(Payone_Enum_ClearingType::FINANCING);
        $request->setFinancingtype(Payone_Api_Enum_FinancingType::PPI);
        $request->setWorkorderId($this->session->offsetGet('moptPaypalInstallmentWorkerId'));
        $request->setCurrency( $this->getCurrencyShortName());
        $request->setAmount($this->getAmount());
        if ( $config['sendOrdernumberAsReference'] === true )
        {
            $paymentReference = $this->moptPayoneMain->reserveOrdernumber();
        } else {
            $paymentReference = $this->moptPayoneMain->getParamBuilder()->getParamPaymentReference();
        }
        $request->setReference($paymentReference);
        $personalData = $this->moptPayoneMain->getParamBuilder()->getPersonalData($orderVariables['sUserData']);
        $request->setPersonalData($personalData);
        $deliveryData = $this->moptPayoneMain->getParamBuilder()->getDeliveryData($orderVariables['sUserData']);
        $request->setDeliveryData($deliveryData);
        $admin = Shopware()->Modules()->Admin();
        $transactionStatusPushCustomParam = 'session-' . Shopware()->Shop()->getId()
            . '|' . $admin->sSYSTEM->sSESSION_ID . '|' . $orderHash;
        $request->setParam($transactionStatusPushCustomParam);

        $this->service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));

        if (!$config['submitBasket']) {
            // do nothing
        } else {
            $request->setInvoicing($this->moptPayoneMain->getParamBuilder()->getInvoicing($orderVariables['sBasket'],$orderVariables['sDispatch'], $orderVariables['sUserData']));
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
                $this->session->offsetUnset('moptPaypalInstallmentWorkerId');
                // set errors in session so we can use the error handling in MoptPayment Controller
                $this->session->payoneErrorMessage = $this->moptPayoneMain->getPaymentHelper()
                    ->moptGetErrorMessageFromErrorCodeViaSnippet(false, $response->getErrorcode());
                $this->forward('error', 'MoptPaymentPayone', null);
                break;
        }
    }

    /**
     * Method will be called after successful
     * return from  paypal
     *
     * @return void
     * @throws Exception
     */
    public function successAction()
    {
        $this->forward($this->buildAndCallGetPayment());

    }

    /**
     * get complete user-data as array to use in view
     *
     * @return array
     */
    protected function getUserData()
    {
        $system = Shopware()->System();
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $countryShipping = $userData['additional']['countryShipping'];

        if (empty($countryShipping)) {
            return $userData;
        }

        $sTaxFree = (
            !empty($countryShipping['taxfree']) ||
            !empty($countryShipping['taxfree_ustid']) &&
            !empty($userData['billingaddress']['ustid'])
        );

        $system->sUSERGROUPDATA = Shopware()->Db()->fetchRow(
            'SELECT * FROM s_core_customergroups
              WHERE groupkey = ?',
            array($system->sUSERGROUP)
        );

        $userData['additional']['show_net'] = false;
        $userData['additional']['charge_vat'] = false;
        Shopware()->Session()->offsetSet('sOutputNet', true);

        if (!empty($sTaxFree)) {
            $system->sUSERGROUPDATA['tax'] = 0;
            $this->container->get('config')->offsetSet('sARTICLESOUTPUTNETTO', 1);
            Shopware()->Session()->offsetSet('sUserGroupData', $system->sUSERGROUPDATA);
        } else {
            $userData['additional']['charge_vat'] = true;
            $userData['additional']['show_net'] = !empty($system->sUSERGROUPDATA['tax']);
            Shopware()->Session()->offsetSet('sOutputNet', empty($system->sUSERGROUPDATA['tax']));
        }

        return $userData;
    }

    /**
     * prepare and do payment server api call
     *
     * @param string $clearingType
     * @param string $financingType
     * @return void
     * @throws Exception
     */
    protected function buildAndCallGetPayment($clearingType = 'fnc', $financingType = 'PPI')
    {
        $config = $this->moptPayoneMain->getPayoneConfig($this->moptPayonePaymentHelper->getPaymentIdFromName('mopt_payone__ewallet_masterpass'));
        $params = $this->moptPayoneMain->getParamBuilder()->buildAuthorize($config['paymentId']);
        $params['api_version'] = '3.10';

        $request = new Payone_Api_Request_Genericpayment($params);

        $payData = new Payone_Api_Request_Parameter_Paydata_Paydata();

        $addPaydataAction = Payone_Api_Enum_GenericpaymentAction::PAYPAL_INSTALLMENT_GET_PAYMENT;

        $this->service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));

        $payData->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'action', 'data' => $addPaydataAction)
        ));

        $request->setPaydata($payData);
        $request->setClearingtype($clearingType);
        $request->setFinancingType($financingType);
        $request->setWorkorderId($this->session->offsetGet('moptPaypalInstallmentWorkerId'));
        $request->setCurrency($this->getCurrencyShortName());


        $response = $this->service->request($request);
        switch ($response->getStatus()) {
            case\Payone_Api_Enum_ResponseType::OK;
                $installmentData = $response->getPayData()->toAssocArray();
                $this->forward('confirm', 'FatchipBSPayonePaypalInstallmentCheckout', null, $installmentData);
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

    public function abortAction()
    {
        $session = Shopware()->Session();
        $session->moptPayPalInstallmentError = true;
        unset($this->session->moptPaypalInstallmentWorkerId);
        unset($this->session->moptFormSubmitted);

        return $this->redirect(['controller' => 'checkout', 'action' => 'cart']);
    }
}

