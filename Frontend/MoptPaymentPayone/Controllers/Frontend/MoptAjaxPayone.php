<?php

use Shopware\Components\CSRFWhitelistAware;


/**
 * Integrate Payone protect and Ajax call handling
 */
class Shopware_Controllers_Frontend_MoptAjaxPayone extends Enlight_Controller_Action implements CSRFWhitelistAware
{

    /**
     * Reference to sAdmin object (core/class/sAdmin.php)
     *
     * @var sAdmin
     */
    protected $admin;

    /**
     * PayoneMain
     *
     * @var Mopt_PayoneMain
     */
    protected $moptPayoneMain = null;

    /**
     * PayoneMain
     *
     * @var Mopt_PayonePaymentHelper
     */
    protected $moptPayonePaymentHelper = null;

    /**
     * PayOne Builder
     *
     * @var Payone_Builder
     */
    protected $payoneServiceBuilder;
    protected $service = null;
    protected $session;

    /**
     * Init method that get called automatically
     * Set class properties
     *
     * @return void
     */
    public function init()
    {
        $this->admin = Shopware()->Modules()->Admin();
        $this->payoneServiceBuilder = $this->Plugin()->Application()->MoptPayoneBuilder();
        $this->moptPayoneMain = $this->Plugin()->Application()->MoptPayoneMain();
        $this->moptPayonePaymentHelper = $this->moptPayoneMain->getPaymentHelper();
        $this->session = Shopware()->Session();
    }

    /**
     * Ask user wether to keep original submittted or corrected values
     */
    public function ajaxGetConsumerScoreUserAgreementAction()
    {
        $paymentId = $this->session->moptPaymentId;
        $config = $this->moptPayoneMain->getPayoneConfig($paymentId);

        //add custom texts to view
        if ($config['consumerscoreNoteActive']) {
            $this->View()->consumerscoreNoteMessage = Shopware()->Snippets()
                ->getNamespace('frontend/MoptPaymentPayone/messages')
                ->get('consumerscoreNoteMessage');
        }
        if ($config['consumerscoreAgreementActive']) {
            $this->View()->consumerscoreAgreementMessage = Shopware()->Snippets()
                ->getNamespace('frontend/MoptPaymentPayone/messages')
                ->get('consumerscoreAgreementMessage');
        }

        unset($this->session->moptConsumerScoreCheckNeedsUserAgreement);
    }

    /**
     * ask user wether to keep original submittted or newly chosen payment method
     */
    public function ajaxVerifyPaymentAction()
    {
        $this->View()->moptSelectedPayment = $this->Request()->getParam('moptSelectedPayment');
        $this->View()->moptOriginalPayment = $this->Request()->getParam('moptOriginalPayment');
        $this->View()->moptCheckedId = $this->Request()->getParam('moptCheckedId');
    }

    public function checkConsumerScoreAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $userId = $this->session->sUserId;

        unset($this->session->moptConsumerScoreCheckNeedsUserAgreement);

        //get config
        $paymentId = $this->session->moptPaymentId;

        $config = $this->moptPayoneMain->getPayoneConfig($paymentId);
        $user = $this->admin->sGetUserData();
        $billingAddressData = $user['billingaddress'];
        $billingAddressData['country'] = $billingAddressData['countryID'];
        //perform consumerscorecheck
        $params = $this->moptPayoneMain->getParamBuilder()
            ->getConsumerscoreCheckParams($billingAddressData, $paymentId);
        /** @var Payone_Api_Service_Verification_Consumerscore $service */
        $service = $this->payoneServiceBuilder->buildServiceVerificationConsumerscore();
        $service->getServiceProtocol()->addRepository(
            Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog')
        );
        $request = new Payone_Api_Request_Consumerscore($params);
        $request->setAddresschecktype(
            ($config['consumerscoreCheckMode'] == \Payone_Api_Enum_ConsumerscoreType::BONIVERSUM_VERITA) ?
                \Payone_Api_Enum_AddressCheckType::BONIVERSUM_PERSON :
                \Payone_Api_Enum_AddressCheckType::NONE
        );

        $request->setConsumerscoretype($config['consumerscoreCheckMode']);

        try {
            $response = $service->score($request);
        } catch (\Exception $e) {

            unset($this->session->moptConsumerScoreCheckNeedsUserAgreement);
            unset($this->session->moptPaymentId);

            //choose next action according to config
            if ($config['consumerscoreFailureHandling'] == 0) {
                //abort
                //delete payment data and set to payone prepayment
                $this->moptPayoneMain->getPaymentHelper()->deletePaymentData($userId);
                $this->moptPayoneMain->getPaymentHelper()->setConfiguredDefaultPaymentAsPayment($userId);
                echo json_encode(false);
                return;
            } else {
                //proceed
                $this->moptPayoneMain->getHelper()->saveConsumerScoreApproved($userId);
                echo json_encode(true);
                return;
            }
        }                
        
        if ($response->getStatus() == \Payone_Api_Enum_ResponseType::VALID) {
            if ($response->getScore() === 'U'){
                $response->setScore($this->moptPayoneMain->getHelper()->getScoreColor($config));
            }
            //save result
            $this->moptPayoneMain->getHelper()->saveConsumerScoreCheckResult($userId, $response);
            unset($this->session->moptConsumerScoreCheckNeedsUserAgreement);
            unset($this->session->moptPaymentId);
            echo json_encode(true);
        } else { /* INVALID */
            $this->moptPayoneMain->getHelper()->saveConsumerScoreError($userId, $response);
            unset($this->session->moptConsumerScoreCheckNeedsUserAgreement);
            unset($this->session->moptPaymentId);            
            
            echo json_encode(false);          
        }
    }

    public function doNotCheckConsumerScoreAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $userId = $this->session->sUserId;
        $this->moptPayoneMain->getHelper()->saveConsumerScoreDenied($userId);

        unset($this->session->moptConsumerScoreCheckNeedsUserAgreement);
        unset($this->session->moptPaymentId);

        echo json_encode(false);
    }

    public function saveOriginalAddressAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $userId = $this->session->sUserId;
        $response = unserialize($this->session->moptAddressCheckCorrectedAddress);
        $config = $this->moptPayoneMain->getPayoneConfig();

        $mappedPersonStatus = $this->moptPayoneMain->getHelper()
            ->getUserScoringValue($response->getPersonstatus(), $config);
        $mappedPersonStatus = $this->moptPayoneMain->getHelper()->getUserScoringColorFromValue($mappedPersonStatus);
        $this->moptPayoneMain->getHelper()->saveAddressCheckResult('billing', $userId, $response, $mappedPersonStatus);

        unset($this->session->moptAddressCheckNeedsUserVerification);
        unset($this->session->moptAddressCheckOriginalAddress);
        unset($this->session->moptAddressCheckCorrectedAddress);
    }

    public function saveCorrectedAddressAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $userId = $this->session->sUserId;
        $response = unserialize($this->session->moptAddressCheckCorrectedAddress);
        $config = $this->moptPayoneMain->getPayoneConfig();

        $this->moptPayoneMain->getHelper()->saveCorrectedBillingAddress($userId, $response);
        $mappedPersonStatus = $this->moptPayoneMain->getHelper()
            ->getUserScoringValue($response->getPersonstatus(), $config);
        $mappedPersonStatus = $this->moptPayoneMain->getHelper()->getUserScoringColorFromValue($mappedPersonStatus);
        $this->moptPayoneMain->getHelper()->saveAddressCheckResult('billing', $userId, $response, $mappedPersonStatus);

        unset($this->session->moptAddressCheckNeedsUserVerification);
        unset($this->session->moptAddressCheckOriginalAddress);
        unset($this->session->moptAddressCheckCorrectedAddress);
    }

    public function saveOriginalShippingAddressAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $userId = $this->session->sUserId;
        $response = unserialize($this->session->moptShippingAddressCheckCorrectedAddress);
        $config = $this->moptPayoneMain->getPayoneConfig();

        $mappedPersonStatus = $this->moptPayoneMain->getHelper()
            ->getUserScoringValue($response->getPersonstatus(), $config);
        $mappedPersonStatus = $this->moptPayoneMain->getHelper()->getUserScoringColorFromValue($mappedPersonStatus);
        $this->moptPayoneMain->getHelper()->saveAddressCheckResult('shipping', $userId, $response, $mappedPersonStatus);

        unset($this->session->moptShippingAddressCheckNeedsUserVerification);
        unset($this->session->moptShippingAddressCheckOriginalAddress);
        unset($this->session->moptShippingAddressCheckCorrectedAddress);
    }

    public function saveCorrectedShippingAddressAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $userId = $this->session->sUserId;
        $response = unserialize($this->session->moptShippingAddressCheckCorrectedAddress);
        $config = $this->moptPayoneMain->getPayoneConfig();

        $this->moptPayoneMain->getHelper()->saveCorrectedShippingAddress($userId, $response);
        $mappedPersonStatus = $this->moptPayoneMain->getHelper()
            ->getUserScoringValue($response->getPersonstatus(), $config);
        $mappedPersonStatus = $this->moptPayoneMain->getHelper()->getUserScoringColorFromValue($mappedPersonStatus);
        $this->moptPayoneMain->getHelper()->saveAddressCheckResult('shipping', $userId, $response, $mappedPersonStatus);

        unset($this->session->moptShippingAddressCheckNeedsUserVerification);
        unset($this->session->moptShippingAddressCheckOriginalAddress);
        unset($this->session->moptShippingAddressCheckCorrectedAddress);
    }

    /**
     * ask user wether to keep original submittted or corrected values
     */
    public function ajaxVerifyShippingAddressAction()
    {
        $response = unserialize($this->session->moptShippingAddressCheckCorrectedAddress);
        $this->View()->moptShippingAddressCheckOriginalAddress = $this->session->moptShippingAddressCheckOriginalAddress;
        $this->View()->moptShippingAddressCheckCorrectedAddress = $response->toArray();

        if ($this->session->moptShippingAddressCheckTarget) {
            $this->View()->moptShippingAddressCheckTarget = $this->session->moptShippingAddressCheckTarget;
        } else {
            $this->View()->moptShippingAddressCheckTarget = 'checkout';
        }
    }

    /**
     * ask user wether to keep original submittted or corrected values
     */
    public function ajaxVerifyAddressAction()
    {
        $response = unserialize($this->session->moptAddressCheckCorrectedAddress);
        $this->View()->moptAddressCheckOriginalAddress = $this->session->moptAddressCheckOriginalAddress;
        $this->View()->moptAddressCheckCorrectedAddress = $response->toArray();

        if ($this->session->moptAddressCheckTarget) {
            $this->View()->moptAddressCheckTarget = $this->session->moptAddressCheckTarget;
        } else {
            $this->View()->moptAddressCheckTarget = 'checkout';
        }
    }

    /**
     * AJAX action called from creditcard layer, saves client api response
     */
    public function savePseudoCardAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $userId = $this->session->sUserId;

        $paymentData['mopt_payone__cc_cardexpiredate'] = $this->Request()->getPost('mopt_payone__cc_cardexpiredate');
        $paymentData['mopt_payone__cc_truncatedcardpan'] = $this->Request()->getPost('mopt_payone__cc_truncatedcardpan');
        $paymentData['mopt_payone__cc_truncatedcardpan_hidden'] = $this->Request()->getPost('mopt_payone__cc_truncatedcardpan_hidden');
        $paymentData['mopt_payone__cc_pseudocardpan'] = $this->Request()->getPost('mopt_payone__cc_pseudocardpan');
        $paymentData['mopt_payone__cc_cardtype'] = $this->Request()->getPost('mopt_payone__cc_cardtype');
        $paymentData['mopt_payone__cc_accountholder'] = $this->Request()->getPost('mopt_payone__cc_accountholder');
        $paymentData['mopt_payone__cc_month'] = $this->Request()->getPost('mopt_payone__cc_month');
        $paymentData['mopt_payone__cc_year'] = $this->Request()->getPost('mopt_payone__cc_year');
        $paymentData['mopt_payone__cc_paymentname'] = $this->Request()->getPost('mopt_payone__cc_paymentname');
        $paymentData['mopt_payone__cc_paymentid'] = $this->Request()->getPost('mopt_payone__cc_paymentid');
        $paymentData['mopt_payone__cc_paymentdescription'] = $this->Request()->getPost('mopt_payone__cc_paymentdescription');

        $actualPaymentId = $paymentData['mopt_payone__cc_paymentid'];

        $sql = 'replace into `s_plugin_mopt_payone_payment_data`' .
            '(`userId`,`moptPaymentData`) values (?,?)';
        $paymentData = serialize($paymentData);
        Shopware()->Db()->query($sql, array($userId, $paymentData));

        $userData = $this->admin->sGetUserData();
        $previousPayment = $this->admin->sGetPaymentMeanById($userData['additional']['user']['paymentID']);

        if ($previousPayment['paymentTable']) {
            $deleteSQL = 'DELETE FROM ' . $previousPayment['paymentTable'] . ' WHERE userID=?';
            Shopware()->Db()->query($deleteSQL, array($this->session->sUserId));
        }

        $sqlPayment = "UPDATE s_user SET paymentID = ? WHERE id = ?";
        Shopware()->Db()->query($sqlPayment, array($actualPaymentId, $userId));
    }

    /**
     * download SEPA mandate PDF file on success page
     *
     * @return mixed
     */
    public function downloadMandateAction()
    {
        if (!$this->session->moptMandateDataDownload) {
            $this->forward('downloadError');
            return;
        }

        $params = $this->moptPayoneMain->getParamBuilder()->buildGetFile($this->getPaymentId(), Shopware()->Session()->moptMandateDataDownload);
        $service = $this->payoneServiceBuilder->buildServiceManagementGetFile();
        $request = new Payone_Api_Request_GetFile($params);

        try {
            $response = $service->getFile($request);
            $this->Front()->Plugins()->ViewRenderer()->setNoRender();

            $httpResponse = $this->Response();
            $httpResponse->setHeader('Cache-Control', 'public');
            $httpResponse->setHeader('Content-Description', 'File Transfer');
            $httpResponse->setHeader('Content-disposition', 'attachment; filename=' . "Payone_Mandate.pdf");
            $httpResponse->setHeader('Content-Type', 'application/pdf');
            $httpResponse->setHeader('Content-Transfer-Encoding', 'binary');
            $httpResponse->setHeader('Content-Length', strlen($response->getRawResponse()));
            echo $response->getRawResponse();
        } catch (Exception $exc) {
            $this->forward('downloadError');
        }
    }

    /**
     * Returns the payment plugin config data.
     *
     * @return Shopware_Plugins_Frontend_MoptPaymentPayone_Bootstrap
     */
    public function Plugin()
    {
        return Shopware()->Plugins()->Frontend()->MoptPaymentPayone();
    }

    /**
     * get actual payment method id
     *
     * @return string
     */
    protected function getPaymentId()
    {
        $userData = $this->admin->sGetUserData();
        $paymentId = $userData['additional']['payment']['id'];
        return $paymentId;
    }

    /**
     * get actual payment method id
     *
     * @return string
     */
    protected function ajaxHandlePayolutionPreCheckAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $paymentData = $this->session->moptPayment;
        $paymentData['mopt_payone__company_trade_registry_number'] = $this->Request()->getPost('hreg');
        $paymentData['dob'] = $this->Request()->getPost('dob');
        $paymentData['mopt_payone__payolution_installment_basketamount'] = $this->Request()->getPost('basketamount');
        if (!empty($paymentData['mopt_payone__company_trade_registry_number'])){
            $paymentData['mopt_payone__payolution_b2bmode'] = 1;
        } else{
            $paymentData['mopt_payone__payolution_b2bmode'] = 0;
        }
        $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
        $financeType = Payone_Api_Enum_PayolutionType::PYS;
        $paymentType = Payone_Api_Enum_PayolutionType::PYS_FULL;
        $data = [];
        $precheckResponse = $this->buildAndCallPrecheck($config, 'fnc', $financeType, $paymentType, $paymentData);
        if ($precheckResponse->getStatus() == \Payone_Api_Enum_ResponseType::OK) {
            $responseData = $precheckResponse->toArray();
            $workorderId = $responseData['rawResponse']['workorderid'];
            $calculationResponse = $this
                ->buildAndCallCalculate($config, 'fnc', $financeType, $paymentType, $paymentData, $workorderId);
            if ($calculationResponse instanceof \Payone_Api_Response_Genericpayment_Approved) {
                $installmentData = $calculationResponse->getInstallmentData();
                $data['data'] = $installmentData;
                $data['status'] = 'success';
                $data['workorderid'] = $workorderId;
                $encoded = json_encode($data);
                echo $encoded;
            } else {
                $data['errorMessage'] = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages')->get('errorMessage'.$calculationResponse->getErrorcode(), $calculationResponse->getErrorMessage().' (Fehler '.$calculationResponse->getErrorcode().')', true);
                $data['status'] = 'error';
                $encoded = json_encode($data);
                echo $encoded;
            }
        } else {
            $data['errorMessage'] = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages')->get('errorMessage'.$precheckResponse->getErrorcode(), $precheckResponse->getErrorMessage().' (Fehler '.$precheckResponse->getErrorcode().')', true);
            $data['status'] = 'error';
            $encoded = json_encode($data);
            echo $encoded;
        }
    }

    /**
     * render the payolution installment deb container for frontend usage
     *
     * @return string
     */
    protected function renderPayolutionInstallmentAction()
    {
        $installmentData = $this->Request()->getPost('data');
        $this->View()->assign(array('InstallmentPlan' => $installmentData));
    }

    /**
     * download the payolution installment info pdf for frontend usage
     *
     * @return string
     */
    protected function getPayolutionDraftUrlAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $url = $this->Request()->getParam('url');
        $duration = $this->Request()->getParam('duration');
        if ($url) {
            $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
            $user = $config['payolutionDraftUser'];
            $password = $config['payolutionDraftPassword'];

            $downloadUrl = str_ireplace('https://', 'https://' . $user . ':' . $password . '@', $url . '&duration=' . $duration);
            $content = file_get_contents($downloadUrl);
            $filename = 'terms-of-payment.pdf';
            if ($content) {
                header("Content-Type: application/pdf");
                header("Content-Disposition: attachment; filename=\"{$filename}\"");
                echo $content;
            }
            echo "Es ist ein Fehler beim Download aufgetreten <br>Bitte versuchen Sie es später noch einmal.";
        }
    }

    /**
     * prepare and do payment server api call
     *
     * @param array $config
     * @param string $clearingType
     * @param string $financetype
     * @param string $paymenttype
     * @param array $paymentdata
     * @return type $response
     */
    protected function buildAndCallPrecheck($config, $clearingType, $financetype, $paymenttype, $paymentData)
    {
        $paramBuilder = $this->moptPayoneMain->getParamBuilder();
        $personalData = $paramBuilder->getPersonalData($this->admin->sGetUserData());
        $params = $this->moptPayoneMain->getParamBuilder()->buildAuthorize($config['paymentId']);
        $params['api_version'] = '3.10';
        $params['financingtype'] = $financetype;
        $basket = $this->moptPayoneMain->sGetBasket();
        //create hash
        $orderHash = md5(serialize($basket));
        $this->session->moptOrderHash = $orderHash;

        $request = new Payone_Api_Request_Genericpayment($params);

        $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'action', 'data' => Payone_Api_Enum_GenericpaymentAction::PAYOLUTION_PRE_CHECK)
        ));
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'payment_type', 'data' => $paymenttype)
        ));

        if ($paymentData && $paymentData['mopt_payone__payolution_b2bmode']) {
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'b2b', 'data' => 'yes')
            ));
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'company_trade_registry_number', 'data' => $paymentData['mopt_payone__company_trade_registry_number'])
            ));
        }
        if (empty($paymentData['mopt_payone__payolution_installment_basketamount'])){
            $amountWithShipping = $this->getAmount() + $paymentData['mopt_payone__payolution_installment_shippingcosts'];
        } else {
            $amountWithShipping = $paymentData['mopt_payone__payolution_installment_basketamount'];
        }
        $request->setPaydata($paydata);
        $request->setAmount($amountWithShipping);
        $request->setCurrency($this->getCurrencyShortName());
        $request->setCompany($personalData->getCompany());
        $request->setFirstname($personalData->getFirstname());
        $request->setLastname($personalData->getLastname());
        $request->setStreet($personalData->getStreet());
        $request->setZip($personalData->getZip());
        $request->setCity($personalData->getCity());
        $request->setCountry($personalData->getCountry());
        if ($personalData->getBirthday() !== "00000000" && $personalData->getBirthday() !== "") {
            $request->setBirthday($personalData->getBirthday());
        } else {
            $request->setBirthday($paymentData['dob']);
        }

        if ($paymentData && $paymentData['mopt_payone__payolution_b2bmode']) {
            $request->setBirthday("");
        }
        $request->setEmail($personalData->getEmail());
        $request->setIp($personalData->getIp());
        $request->setLanguage($personalData->getLanguage());
        $request->setClearingtype($clearingType);
        $this->service = $this->payoneServiceBuilder->buildServicePaymentGenericpayment();
        $response = $this->service->request($request);
        return $response;
    }

    /**
     * prepare and do payment server api call
     *
     * @param array $config
     * @param string $clearingType
     * @param string $financetype
     * @param string $paymenttype
     * @return \Payone_Api_Response_Error|\Payone_Api_Response_Genericpayment_Approved|\Payone_Api_Response_Genericpayment_Redirect $response
     */
    protected function buildAndCallCalculate($config, $clearingType, $financetype, $paymenttype, $paymentData, $workorderId)
    {
        $paramBuilder = $this->moptPayoneMain->getParamBuilder();
        $personalData = $paramBuilder->getPersonalData(Shopware()->Modules()->Admin()->sGetUserData());
        $params = $this->moptPayoneMain->getParamBuilder()->buildAuthorize($config['paymentId']);
        $params['api_version'] = '3.10';
        $params['financingtype'] = $financetype;
        $params['workorderid'] = $workorderId;
        $basket = $this->moptPayoneMain->sGetBasket();
        //create hash
        $orderHash = md5(serialize($basket));
        $this->session->moptOrderHash = $orderHash;

        $request = new Payone_Api_Request_Genericpayment($params);

        $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'action', 'data' => Payone_Api_Enum_GenericpaymentAction::PAYOLUTION_CALCULATION)
        ));
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'payment_type', 'data' => $paymenttype)
        ));

        if ($paymentData && $paymentData['mopt_payone__payolution_b2bmode']) {
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'b2b', 'data' => 'yes')
            ));
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'company_trade_registry_number', 'data' => $paymentData['mopt_payone__invoice_company_trade_registry_number'])
            ));
        }
        if (empty($paymentData['mopt_payone__payolution_installment_basketamount'])){
            $amountWithShipping = $this->getAmount() + $paymentData['mopt_payone__payolution_installment_shippingcosts'];
        } else {
            $amountWithShipping = $paymentData['mopt_payone__payolution_installment_basketamount'];
        }
        $request->setPaydata($paydata);
        $request->setAmount($amountWithShipping);
        $request->setCurrency($this->getCurrencyShortName());
        $request->setCompany($personalData->getCompany());
        $request->setFirstname($personalData->getFirstname());
        $request->setLastname($personalData->getLastname());
        $request->setStreet($personalData->getStreet());
        $request->setZip($personalData->getZip());
        $request->setCity($personalData->getCity());
        $request->setCountry($personalData->getCountry());
        $request->setBirthday($paymentData['dob']);
        $request->setEmail($personalData->getEmail());
        $request->setIp($personalData->getIp());
        $request->setLanguage($personalData->getLanguage());

        $request->setClearingtype($clearingType);
        $this->service = $this->payoneServiceBuilder->buildServicePaymentGenericpayment();
        $this->service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));
        $response = $this->service->request($request);
        return $response;
    }

    /**
     * prepare and do payment server api call
     *
     * @param array $config
     * @param string $clearingType
     * @param string $financetype
     * @param string $calculation_type
     * @param array $paymentData
     * @param mixed $rateValue
     * @param string $shopId
     * @param mixed $rateMonth
     * @param string $amount
     * @return \Payone_Api_Response_Error|\Payone_Api_Response_Genericpayment_Approved|\Payone_Api_Response_Genericpayment_Redirect $response
     */
    protected function buildAndCallCalculateRatepay($config, $clearingType, $financetype, $calculation_type, $paymentData, $rateValue = false, $shopId, $rateMonth = false, $amount)
    {
        $paramBuilder = $this->moptPayoneMain->getParamBuilder();
        $personalData = $paramBuilder->getPersonalData(Shopware()->Modules()->Admin()->sGetUserData());
        $params = $this->moptPayoneMain->getParamBuilder()->buildAuthorize($config['paymentId']);
        $params['api_version'] = '3.10';
        $params['financingtype'] = $financetype;
        $basket = $this->moptPayoneMain->sGetBasket();
        //create hash
        $orderHash = md5(serialize($basket));
        $this->session->moptOrderHash = $orderHash;

        $request = new Payone_Api_Request_Genericpayment($params);

        $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'action', 'data' => Payone_Api_Enum_GenericpaymentAction::RATEPAY_REQUEST_TYPE_CALCULATION)
        ));
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'calculation_type', 'data' => $calculation_type)
        ));
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'customer_allow_credit_inquiry', 'data' => 'yes')
        ));
        if ($rateValue){
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'rate', 'data' => floatval($rateValue *100  ))
            ));
        }
        if ($rateMonth) {
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'month', 'data' => $rateMonth)
            ));
        }
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'shop_id', 'data' => $shopId)
        ));


        if ( !empty($paymentData) && $paymentData['mopt_payone__ratepayinstallment_b2bmode']) {
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'b2b', 'data' => 'yes')
            ));
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'company_trade_registry_number', 'data' => $paymentData['mopt_payone__invoice_company_trade_registry_number'])
            ));
        }
        $amountWithShipping = $amount; // Docs state "smallest currency Unit???
        $request->setPaydata($paydata);
        $request->setAmount($amountWithShipping);
        $request->setCurrency($this->getCurrencyShortName());
        $request->setCompany($personalData->getCompany());
        $request->setFirstname($personalData->getFirstname());
        $request->setLastname($personalData->getLastname());
        $request->setStreet($personalData->getStreet());
        $request->setZip($personalData->getZip());
        $request->setCity($personalData->getCity());
        $request->setCountry($personalData->getCountry());
        $request->setBirthday($paymentData['mopt_payone__ratepay_birthdaydate']);
        $request->setEmail($personalData->getEmail());
        $request->setIp($personalData->getIp());
        $request->setLanguage($personalData->getLanguage());

        $request->setClearingtype($clearingType);
        $this->service = $this->payoneServiceBuilder->buildServicePaymentGenericpayment();
        $response = $this->service->request($request);
        return $response;
    }

    /**
     * Return the full amount to pay.
     *
     * @return float
     */
    public function getAmount()
    {
        $basket = $this->moptPayoneMain->sGetBasket();
        return empty($basket['AmountWithTaxNumeric']) ? $basket['AmountNumeric'] : $basket['AmountWithTaxNumeric'];
    }

    /**
     * Returns the current currency short name.
     *
     * @return string
     */
    public function getCurrencyShortName()
    {
        return Shopware()->Currency()->getShortName();
    }

    public function getWhitelistedCSRFActions()
    {
        $returnArray = array(
            'ajaxGetConsumerScoreUserAgreement',
            'ajaxHandlePayolutionPreCheck',
            'ajaxVerifyAddress',
            'ajaxVerifyPayment',
            'ajaxVerifyShippingAddress',
            'checkConsumerScore',
            'doNotCheckConsumerScore',
            'downloadMandate',
            'getPayolutionDraftUrl',
            'renderPayolutionInstallment',
            'saveCorrectedAddress',
            'saveCorrectedShippingAddress',
            'saveOriginalAddress',
            'saveOriginalShippingAddress',
            'savePseudoCard',
            'rate',
            'runtime'
        );
        return $returnArray;
    }

    /**
     * Calculates the rates by from user defined rate
     * called from an ajax request with ratePay parameters (ratepay.js)
     * map RatePay API parameters and request the payone API
     *
     */
    public function rateAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $html = '';
        $calcValue = $this->Request()->getParam('calcValue');
        $ratePayShopId = $this->Request()->getParam('ratePayshopId');
        $amount = $this->Request()->getParam('amount');
        $paymentData = $this->session->moptPayment;
        $paymentData['mopt_payone__installment_company_trade_registry_number'] = $this->Request()->getParam('hreg');
        $paymentData['mopt_payone__ratepay_birthdaydate'] = str_replace("-", "", $this->Request()->getParam('dob'));
        $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
        $financeType = \Payone_Api_Enum_RatepayType::RPS;

        try {
            if (preg_match('/^[0-9]+(\.[0-9][0-9][0-9])?(,[0-9]{1,2})?$/', $calcValue)) {
                $calcValue = str_replace(".", "", $calcValue);
                $calcValue = str_replace(",", ".", $calcValue);

                $result = $this->buildAndCallCalculateRatepay($config, 'fnc', $financeType, 'calculation-by-rate', $paymentData, $calcValue, $ratePayShopId, false, $amount);


                if ($result instanceof Payone_Api_Response_Genericpayment_Ok) {
                    $responseData = $result->getPayData()->toAssocArray();
                    $html = $this->renderRatepayInstallment($responseData);
                } else {
                    if($result instanceof Payone_Api_Response_Error) {
                        $html = "<div class='ratepay-result rateError'>" . $result->getCustomermessage() . "</div>";
                    }
                }
            } else {
                /** @var \Shopware\Models\Shop\Shop $shop */
                $shop = Shopware()->Shop();
                $locale = $shop->getLocale();
                $localeId = $locale->getId();
                // get translation snippet
                $builder = Shopware()->Models()->createQueryBuilder();
                $builder->select('snippets')
                    ->from('Shopware\Models\Snippet\Snippet', 'snippets');
                $builder->Where('snippets.localeId = :localeId')
                    ->setParameter('localeId', $localeId);
                $builder->andWhere('snippets.namespace = :namespace1')
                    ->setParameter('namespace1', 'frontend/MoptPaymentPayone/payment');
                $builder->andWhere('snippets.name = :name1')
                    ->setParameter('name1', 'wrongValue');
                $snippet = $builder->getQuery()->getResult();
                $snippetText =$snippet[0]->getValue();

                $html = "<div class='rateError'>" . "$snippetText" . "</div>";
            }
        } catch (Exception $e) {
        }
        echo $html;
        return;
    }

    /**
     * Calculates the rates by from user defined runtime
     * called from an ajax request with ratePay parameters (ratepay.js)
     * map RatePay API parameters and request the payone API
     */
    public function runtimeAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $html = '';
        $calcValue = $this->Request()->getParam('calcValue');
        $ratePayShopId = $this->Request()->getParam('ratePayshopId');
        $amount = $this->Request()->getParam('amount');
        $paymentData = $this->session->moptPayment;
        $paymentData['mopt_payone__installment_company_trade_registry_number'] = $this->Request()->getParam('hreg');
        $paymentData['mopt_payone__ratepay_birthdaydate'] = str_replace("-", "", $this->Request()->getParam('dob'));
        $config = $this->moptPayoneMain->getPayoneConfig($this->getPaymentId());
        $financeType = \Payone_Api_Enum_RatepayType::RPS;

        try {
            if (preg_match('/^[0-9]{1,5}$/', $calcValue)) {
                $result = $this->buildAndCallCalculateRatepay($config, 'fnc', $financeType, 'calculation-by-time', $paymentData, false, $ratePayShopId, $calcValue , $amount);;

                if ($result instanceof Payone_Api_Response_Genericpayment_Ok) {
                    $responseData = $result->getPayData()->toAssocArray();
                    $html = $this->renderRatepayInstallment($responseData);
                } else {
                    if($result instanceof Payone_Api_Response_Error) {
                        $html = "<div class='rateError'>" . $result->getCustomermessage() . "</div>";
                    }
                }
            } else {
                /** @var \Shopware\Models\Shop\Shop $shop */
                $shop = Shopware()->Shop();
                $locale = $shop->getLocale();
                $localeId = $locale->getId();
                // get translation snippet
                $builder = Shopware()->Models()->createQueryBuilder();
                $builder->select('snippets')
                    ->from('Shopware\Models\Snippet\Snippet', 'snippets');
                $builder->Where('snippets.localeId = :localeId')
                    ->setParameter('localeId', $localeId);
                $builder->andWhere('snippets.namespace = :namespace1')
                    ->setParameter('namespace1', 'frontend/MoptPaymentPayone/payment');
                $builder->andWhere('snippets.name = :name1')
                    ->setParameter('name1', 'wrongValue');
                $snippet = $builder->getQuery()->getResult();
                $snippetText =$snippet[0]->getValue();

                $html = "<div class='rateError'>" . "$snippetText" . "</div>";
            }
        } catch (Exception $e) {
        }
        echo $html;
        return;
    }

    /**
     * show calculated installmentplan
     * @param $result
     * @return string
     */
    public function renderRatepayInstallment($result)
    {
        $numberOfRates = $result['last-rate']?$result['number-of-rates']-1:$result['number-of-rates'];
        $picturePath = $this->Request()->getBaseUrl() . "/engine/Shopware/Plugins/Community/Frontend/MoptPaymentPayone/Views/frontend/_resources/images/info-icon.png";
        $this->View()->addTemplateDir(dirname(__FILE__) . "/../../Views/");
        $this->View()->loadTemplate("frontend/mopt_ajax_payone/render_ratepay_installment.tpl");
        $this->View()->assign(array('picturePath' => $picturePath));
        $this->View()->assign(array('numberOfRates' => $numberOfRates));
        $this->View()->assign(array('result' => $result));
        return $this->View()->render();
    }

    /**
     * get actual payment method id
     *
     * @return string
     */
    protected function getOrderReferenceDetailsAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        $payoneUserHelper = $this->moptPayoneMain->getUserHelper();
        $payonePaymentHelper = $this->moptPayoneMain->getPaymentHelper();
        $postData = $this->Request()->getParams();
        $paymentData = $this->session->moptPayment;
        $config = $this->moptPayoneMain->getPayoneConfig($payonePaymentHelper->getPaymentAmazonPay()->getId());
        $clearingType = \Payone_Enum_ClearingType::WALLET;
        $walletType = \Payone_Api_Enum_WalletType::AMAZONPAY;
        if (empty($this->session->moptPayoneAmazonReferenceId)) {
            $this->session->moptPayoneAmazonReferenceId = $postData['referenceId'];
        }


        $data = [];
        $response = $this->buildAndCallGetOrderReferenceDetails($config, $clearingType, $walletType, $paymentData, $this->session->moptPayoneAmazonReferenceId, $this->session->moptPayoneAmazonAccessToken );


        if ($response->getStatus() == \Payone_Api_Enum_ResponseType::OK) {
            // create User from Address Data

                $responseData = $response->toArray();
                $responseAddress = $response->getPaydata()->toAssocArray();

            // check if billing country is active for amazon

            if (!$this->isBillingAddressSupported($responseAddress['billing_country'])) {
                $data['errormessage'] = Shopware()->Snippets()
                    ->getNamespace('frontend/MoptPaymentPayone/errorMessages')
                    ->get('amazonBillingAddressNotSupported');
                $data['status'] = 'error';
                $encoded = json_encode($data);
                echo $encoded;
                return;
            }

            if (!$this->isShippingAddressSupported($responseAddress['shipping_country'])) {
                $data['errormessage'] = Shopware()->Snippets()
                    ->getNamespace('frontend/MoptPaymentPayone/errorMessages')
                    ->get('amazonShippingAddressNotSupported');
                $data['status'] = 'error';
                $encoded = json_encode($data);
                echo $encoded;
                return;

            }

                $payoneUserHelper->createrOrUpdateUser($response, $payonePaymentHelper->getPaymentAmazonPay()->getId(), $this->session);

                if (!$this->session->moptCountry) {
                    $data['countryChanged'] = true;
                    $this->session->moptCountry = $responseAddress['shipping_country'];
                } else {

                    if ($this->session->moptCountry == $responseAddress['shipping_country']) {
                        $data['countryChanged'] = false;
                        $this->session->moptCountry = $responseAddress['shipping_country'];
                    } else {
                        $data['countryChanged'] = true;
                        $this->session->moptCountry = $responseAddress['shipping_country'];
                    }
                }

                if (empty($this->session->moptPayoneAmazonWorkOrderId)) {
                    $this->session->moptPayoneAmazonWorkOrderId = $responseData['workorderid'];
                }

                $data['data'] = $responseData['rawResponse'];
                $data['status'] = 'success';
                $data['workorderid'] = $this->session->moptPayoneAmazonWorkOrderId;
                $encoded = json_encode($data);
                echo $encoded;

        } else {
            $data['data'] = $response;
            $data['errormessage'] = $response->getErrormessage();
            $data['status'] = 'error';
            $encoded = json_encode($data);
            echo $encoded;
        }
    }

    /**
     * prepare and do payment server api call
     *
     * @param array $config
     * @param string $clearingType
     * @param string $walletType
     * @param array $paymentData
     * @param string $amazonReferenceId
     * @param array $amazonAddressToken
     * @return \Payone_Api_Response_Error|\Payone_Api_Response_Genericpayment_Approved|\Payone_Api_Response_Genericpayment_Redirect $response
     */
    protected function buildAndCallGetOrderReferenceDetails($config, $clearingType, $walletType, $paymentData, $amazonReferenceId, $amazonAddressToken)
    {
        $params = $this->moptPayoneMain->getParamBuilder()->buildAuthorize($config['paymentId']);
        $params['api_version'] = '3.10';
        //create hash
        $basket = Shopware()->Modules()->Basket()->sGetBasket();
        $orderHash = md5(serialize($basket));
        $this->session->moptOrderHash = $orderHash;

        $request = new Payone_Api_Request_Genericpayment($params);

        $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'action', 'data' => Payone_Api_Enum_GenericpaymentAction::AMAZON_GETORDERREFERENCEDETAILS)
        ));
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'amazon_reference_id', 'data' => $amazonReferenceId)
        ));
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'amazon_address_token', 'data' => $amazonAddressToken)
        ));

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
        return $response;
    }

    /**
     * prepare and do payment server api call
     *
     * @param string $clearingType
     * @param string $walletType
     * @return \Payone_Api_Response_Error|\Payone_Api_Response_Genericpayment_Approved|\Payone_Api_Response_Genericpayment_Redirect $response
     */
    protected function buildAndCallSetCheckoutAction($clearingType = 'wlt', $walletType = 'MPA')
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $router = $this->Front()->Router();
        $config = $this->moptPayoneMain->getPayoneConfig($this->moptPayonePaymentHelper->getPaymentIdFromName('mopt_payone__ewallet_masterpass'));
        $params = $this->moptPayoneMain->getParamBuilder()->buildAuthorize($config['paymentId']);
        $params['api_version'] = '3.10';
        //create hash
        $basket = Shopware()->Modules()->Basket()->sGetBasket();
        $orderHash = md5(serialize($basket));
        Shopware()->Session()->offsetSet('moptOrderHash',$orderHash);

        $request = new Payone_Api_Request_Genericpayment($params);

        $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'action', 'data' => Payone_Api_Enum_GenericpaymentAction::MASTERPASS_SETCHECKOUT)
        ));
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'originURL', 'data' => $router->assemble(['controller' => 'FatchipBSPayoneMasterpass', 'action' => 'index', 'forceSecure' => true]))
        ));

        $request->setPaydata($paydata);
        $request->setClearingtype($clearingType);
        $request->setWallettype($walletType);
        $request->setCurrency("EUR");
        $request->setAmount($basket['AmountNumeric']);
        $request->setBackurl($router->assemble(['controller' => 'FatchipBSPayoneMasterpass', 'action' => 'register', 'forceSecure' => true]));
        $request->setSuccessurl($router->assemble(['controller' => 'FatchipBSPayoneMasterpass', 'action' => 'success', 'forceSecure' => true]));
        $request->setErrorurl($router->assemble(['controller' => 'FatchipBSPayoneMasterpass', 'action' => 'error', 'forceSecure' => true]));

        $this->service = $this->payoneServiceBuilder->buildServicePaymentGenericpayment();
        $this->service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));

        $response = $this->service->request($request);
        $paydataArray=$response->getPayDataArray();
        $this->session->offsetSet('fatchipBSPayoneMasterPassWorkOrderId', $response->getWorkorderId());

        $encoded = json_encode($paydataArray);
        echo $encoded;
    }

    protected function isBillingAddressSupported($country){

        $countries = $this->moptPayoneMain->getPaymentHelper()
            ->moptGetCountriesAssignedToPayment($this->moptPayoneMain->getPaymentHelper()->getPaymentAmazonPay()->getId());

        if (count($countries) == 0){
            return true;
        }

        if (in_array($country, array_column($countries, 'countryiso'))) {
            return true;
        } else {
            return false;
        }
    }


    protected function isShippingAddressSupported($country){

        $countries = $this->moptPayoneMain->getPaymentHelper()
            ->moptGetShippingCountriesAssignedToPayment($this->moptPayoneMain->getPaymentHelper()->getPaymentAmazonPay()->getId());

        if (count($countries) == 0){
            return true;
        }

        if (in_array($country, array_column($countries, 'countryiso'))) {
            return true;
        } else {
            return false;
        }
    }

}
