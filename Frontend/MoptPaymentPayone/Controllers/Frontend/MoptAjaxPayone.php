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
    /** @var Enlight_Components_Session_Namespace $session */
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
        $isCompany = $this->moptPayoneMain->getHelper()->isCompany($userId);
        if ($isCompany) {
            $request->setAddresschecktype(\Payone_Api_Enum_AddressCheckType::NONE);
            $request->setBusinessRelation(null);
            $request->setConsumerscoretype($config['consumerscoreCheckModeB2B']);
        } else {
            $request->setAddresschecktype(
                ($config['consumerscoreCheckModeB2C'] === \Payone_Api_Enum_ConsumerscoreType::BONIVERSUM_VERITA) ?
                    \Payone_Api_Enum_AddressCheckType::BONIVERSUM_PERSON :
                    \Payone_Api_Enum_AddressCheckType::NONE
            );
            $request->setConsumerscoretype($config['consumerscoreCheckModeB2C']);
        }

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
                $this->moptPayoneMain->getPaymentHelper()->deleteCreditcardPaymentData($userId);
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
            if ($response->getScore() === 'U') {
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
        $paymentData['mopt_payone__cc_cardholder'] = $this->Request()->getPost('mopt_payone__cc_cardholder');


        $actualPaymentId = $paymentData['mopt_payone__cc_paymentid'];
        Shopware()->Container()->get('MoptPayoneMain')->getPaymentHelper()->saveCreditcardPaymentData($userId, $paymentData);

        $userData = $this->admin->sGetUserData();
        $previousPayment = $this->admin->sGetPaymentMeanById($userData['additional']['user']['paymentID']);

        if ($previousPayment['paymentTable']) {
            $deleteSQL = 'DELETE FROM ' . $previousPayment['paymentTable'] . ' WHERE userID=?';
            Shopware()->Db()->query($deleteSQL, array($this->session->sUserId));
        }

        $sqlPayment = "UPDATE s_user SET paymentID = ? WHERE id = ?";
        Shopware()->Db()->query($sqlPayment, array($actualPaymentId, $userId));

        $sql = "UPDATE s_user_attributes SET `mopt_payone_creditcard_initial_payment` = ? WHERE id = ?";
        Shopware()->Db()->query($sql, array(0, (int)$userId));

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

    protected function ajaxHandlePayolutionPreCheckAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $paymentData = $this->session->moptPayment;
        $paymentData['mopt_payone__company_trade_registry_number'] = $this->Request()->getPost('hreg');
        $paymentData['dob'] = $this->Request()->getPost('dob');
        $paymentData['mopt_payone__payolution_installment_basketamount'] = $this->Request()->getPost('basketamount');
        if (!empty($paymentData['mopt_payone__company_trade_registry_number'])) {
            $paymentData['mopt_payone__payolution_b2bmode'] = 1;
        } else {
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
                $data['errorMessage'] = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages')->get('errorMessage' . $calculationResponse->getErrorcode(), $calculationResponse->getErrorMessage() . ' (Fehler ' . $calculationResponse->getErrorcode() . ')', true);
                $data['status'] = 'error';
                $encoded = json_encode($data);
                echo $encoded;
            }
        } else {
            $data['errorMessage'] = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages')->get('errorMessage' . $precheckResponse->getErrorcode(), $precheckResponse->getErrorMessage() . ' (Fehler ' . $precheckResponse->getErrorcode() . ')', true);
            $data['status'] = 'error';
            $encoded = json_encode($data);
            echo $encoded;
        }
    }

    /**
     * render the payolution installment deb container for frontend usage
     *
     * @return void
     */
    protected function renderPayolutionInstallmentAction()
    {
        $installmentData = $this->Request()->getPost('data');
        $this->View()->assign(array('InstallmentPlan' => $installmentData));
    }

    /**
     * download the payolution installment info pdf for frontend usage
     *
     * @return void
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
     * @return Payone_Api_Response_Error|Payone_Api_Response_Genericpayment_Approved|Payone_Api_Response_Genericpayment_Redirect $response
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
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'analysis_session_id', 'data' => Shopware()->Session()->get('paySafeToken'))
        ));

        if ($paymentData && $paymentData['mopt_payone__payolution_b2bmode']) {
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'b2b', 'data' => 'yes')
            ));
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'company_trade_registry_number', 'data' => $paymentData['mopt_payone__company_trade_registry_number'])
            ));
        }
        if (empty($paymentData['mopt_payone__payolution_installment_basketamount'])) {
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
        if ($personalData->getBirthday() !== "00000000" && $personalData->getBirthday() !== "" && !is_null($personalData->getBirthday())) {
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
        if (empty($paymentData['mopt_payone__payolution_installment_basketamount'])) {
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
    protected function buildAndCallCalculateRatepay($config, $clearingType, $financetype, $calculation_type, $paymentData, $shopId, $amount, $rateValue = false, $rateMonth = false )
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
        if ($rateValue) {
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'rate', 'data' => floatval($rateValue * 100))
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


        if (!empty($paymentData) && $paymentData['mopt_payone__ratepayinstallment_b2bmode']) {
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
        return Shopware()->Container()->get('currency')->getShortName();
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
            'runtime',
            'checkCreditCardExpiry',
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

                $result = $this->buildAndCallCalculateRatepay($config, 'fnc', $financeType, 'calculation-by-rate', $paymentData, $ratePayShopId, $amount, $calcValue, false);


                if ($result instanceof Payone_Api_Response_Genericpayment_Ok) {
                    $responseData = $result->getPayData()->toAssocArray();
                    $html = $this->renderRatepayInstallment($responseData);
                } else {
                    if ($result instanceof Payone_Api_Response_Error) {
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
                $snippetText = $snippet[0]->getValue();

                $html = "<div class='rateError'>" . "$snippetText" . "</div>";
            }
        } catch (Exception $e) {
        }
        echo $html;
        return;
    }

    /**
     * Extract number from given string
     *
     * @param  string $sString
     * @return string|false
     */
    protected function getNumberFromString($sString)
    {
        preg_match('/^[^0-9]*_([0-9])$/m', $sString, $matches);

        if (count($matches) == 2) {
            return $matches[1];
        }
        return false;
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
                $result = $this->buildAndCallCalculateRatepay($config, 'fnc', $financeType, 'calculation-by-time', $paymentData, $ratePayShopId, $amount, false, $calcValue);;

                if ($result instanceof Payone_Api_Response_Genericpayment_Ok) {
                    $responseData = $result->getPayData()->toAssocArray();
                    $html = $this->renderRatepayInstallment($responseData);
                } else {
                    if ($result instanceof Payone_Api_Response_Error) {
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
                $snippetText = $snippet[0]->getValue();

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
        $numberOfRates = $result['last-rate'] ? $result['number-of-rates'] - 1 : $result['number-of-rates'];
        $picturePath = $this->Request()->getBaseUrl() . "/engine/Shopware/Plugins/Community/Frontend/MoptPaymentPayone/Views/frontend/_resources/images/info-icon.png";
        $this->View()->addTemplateDir(dirname(__FILE__) . "/../../Views/");
        $this->View()->loadTemplate("frontend/mopt_ajax_payone/render_ratepay_installment.tpl");
        $this->View()->assign(array('picturePath' => $picturePath));
        $this->View()->assign(array('numberOfRates' => $numberOfRates));
        $this->View()->assign(array('result' => $result));
        return $this->View()->render();
    }

    protected function formatInstallmentOptions($aResponse)
    {
        unset($aResponse['status']);
        unset($aResponse['workorderid']);

        $aInstallmentOptions = ['runtimes' => []];

        foreach ($aResponse as $sKey => $sValue) {
            $sKey = str_replace("add_paydata", "", $sKey);
            $sKey = str_replace(["[", "]"], "", $sKey);
            $sKey = str_replace("-", "_", $sKey);

            $iIndex = $this->getNumberFromString($sKey);
            if ($iIndex !== false) {
                $sKey = str_replace("_".$iIndex, "", $sKey);
                if (!isset($aInstallmentOptions['runtimes'][$iIndex])) {
                    $aInstallmentOptions['runtimes'][$iIndex] = [];
                }
                $aInstallmentOptions['runtimes'][$iIndex][$sKey] = $sValue;
            } else {
                $aInstallmentOptions[$sKey] = $sValue;
            }
        }
        return $aInstallmentOptions;
    }

    protected function amznConfirmOrderReferenceAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        //get/setorderreference calls have to be applied before confirmorderreference
        $this->buildAndCallSetOrderReferenceDetails();
        $isConfirmed = $this->buildAndCallConfirmOrderReference();
        if ($isConfirmed === false) {
            unset($this->session->moptPayoneAmazonAccessToken);
            unset($this->session->moptPayoneAmazonReferenceId);
            unset($this->session->moptPayoneAmazonWorkOrderId);
            unset($this->session->moptAmazonOrdernum);
            $this->session->moptAmazonError = 'ConfirmOrderReference';
            $this->session->moptAmazonLogout = true;
            $this->Response()->setHttpResponseCode(418);
            return;
        }
        echo json_encode('OK');
    }

    protected function buildAndCallConfirmOrderReference()
    {
        $paymentId = Shopware()->Container()->get('MoptPayoneMain')->getPaymentHelper()->getPaymentAmazonPay()->getId();
        $moptPayoneMain = $this->moptPayoneMain;

        $payoneServiceBuilder = Shopware()->Container()->get('plugins')->Frontend()->MoptPaymentPayone()->get('MoptPayoneBuilder');
        $params = $moptPayoneMain->getParamBuilder()->buildAuthorize($paymentId);

        $request = new \Payone_Api_Request_Genericpayment($params);

        $router = $this->Front()->Router();

        $successurl = $this->moptPayonePaymentHelper->assembleTokenizedUrl($router, array('controller' => 'MoptPaymentAmazon', 'action' => 'finish',
            'forceSecure' => true, 'appendSession' => false));

        $errorurl = $router->assemble(array('controller' => 'checkout', 'action' => 'cart',
            'forceSecure' => true, 'appendSession' => false));

        $request->setClearingType(\Payone_Enum_ClearingType::WALLET);
        $request->setWallettype(\Payone_Api_Enum_WalletType::AMAZONPAY);
        $request->setWorkorderid($this->session->moptPayoneAmazonWorkOrderId);
        $request->setApiVersion("3.10");

        $request->setCurrency(Shopware()->Shop()->getCurrency()->getCurrency());
        $request->setAmount(Shopware()->Session()->sOrderVariables['sAmount']);
        $request->setSuccessurl($successurl);
        $request->setErrorurl($errorurl);

        $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'amazon_reference_id', 'data' => $this->session->moptPayoneAmazonReferenceId)
        ));
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'action', 'data' => 'confirmorderreference')
        ));
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'reference', 'data' => $moptPayoneMain->reserveOrdernumber())
        ));

        $request->setPaydata($paydata);

        $service = $payoneServiceBuilder->buildServicePaymentGenericpayment();
        $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));

        $response = $service->request($request);

        if ($response->getStatus() === 'OK') {
            return true;
        }

        return false;
    }

    /**
     * prepare and do payment server api call
     *
     * @return \Payone_Api_Response_Error|\Payone_Api_Response_Genericpayment_Approved|\Payone_Api_Response_Genericpayment_Redirect $response
     */
    protected function buildAndCallSetOrderReferenceDetails()
    {
        $paymentId = Shopware()->Container()->get('MoptPayoneMain')->getPaymentHelper()->getPaymentAmazonPay()->getId();
        $config = $this->moptPayoneMain->getPayoneConfig($paymentId);

        $session = Shopware()->Session();
        $clearingType = \Payone_Enum_ClearingType::WALLET;
        $walletType = \Payone_Api_Enum_WalletType::AMAZONPAY;
        $params = Shopware()->Container()->get('plugins')->Frontend()->MoptPaymentPayone()->get('MoptPayoneMain')->getParamBuilder()->buildAuthorize($config['paymentId']);
        $payoneServiceBuilder = Shopware()->Container()->get('plugins')->Frontend()->MoptPaymentPayone()->get('MoptPayoneBuilder');
        $params['api_version'] = '3.10';
        //create hash
        $basket = Shopware()->Modules()->Basket()->sGetBasket();
        $orderHash = md5(serialize($basket));
        $session->moptOrderHash = $orderHash;

        $request = new Payone_Api_Request_Genericpayment($params);

        $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'action', 'data' => Payone_Api_Enum_GenericpaymentAction::AMAZON_SETORDERREFERENCEDETAILS)
        ));
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'amazon_reference_id', 'data' => $session->moptPayoneAmazonReferenceId)
        ));
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'amazon_address_token', 'data' => $session->moptPayoneAmazonAccessToken)
        ));
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'storename', 'data' => Shopware()->Shop()->getName())
        ));

        $request->setPaydata($paydata);
        $request->setClearingtype($clearingType);
        $request->setWallettype($walletType);
        $request->setCurrency(Shopware()->Shop()->getCurrency()->getCurrency());
        $request->setAmount($basket['AmountNumeric']);
        $request->setWorkorderId($session->moptPayoneAmazonWorkOrderId);

        $service = $payoneServiceBuilder->buildServicePaymentGenericpayment();

        $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));
        $response = $service->request($request);

        return $response;
    }

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
        $response = $this->buildAndCallGetOrderReferenceDetails($config, $clearingType, $walletType, $paymentData, $this->session->moptPayoneAmazonReferenceId, $this->session->moptPayoneAmazonAccessToken);


        if ($response->getStatus() == \Payone_Api_Enum_ResponseType::OK) {
            // create User from Address Data
            $responseData = $response->toArray();
            $responseAddress = $response->getPaydata()->toAssocArray();
            //check if telephonenumber is required in shop config
            if (Shopware()->Config()->get('requirePhoneField')) {
                $shipping_telephonenumber = $responseAddress['shipping_telephonenumber'];
                if (strlen($shipping_telephonenumber) < 1) {
                    $data['errormessage'] = Shopware()->Snippets()
                        ->getNamespace('frontend/MoptPaymentPayone/errorMessages')
                        ->get('phoneNumberRequired');
                    $data['status'] = 'error';
                    $encoded = json_encode($data);
                    echo $encoded;
                    return;
                }
            }

            $this->session->moptPayoneUserHelperError = false;
            $success = $payoneUserHelper->createOrUpdateUser($response, $payonePaymentHelper->getPaymentAmazonPay()->getId(), $this->session);

            if ($success === false) {
                $data['errormessage'] = $this->session->moptPayoneUserHelperErrorMessage;
                $data['status'] = 'error';
                $encoded = json_encode($data);
                echo $encoded;
                return;
            }


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
        $request->setCurrency(Shopware()->Shop()->getCurrency()->getCurrency());
        $request->setAmount($basket['AmountNumeric']);
        $this->service = $this->payoneServiceBuilder->buildServicePaymentGenericpayment();
        $this->service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));

        $response = $this->service->request($request);
        return $response;
    }

    /**
     * AJAX action called for creditcard expiry checks
     *
     */
    public function checkCreditCardExpiryAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $shopId = $this->container->get('shop')->getId();

        $sql = 'SELECT * FROM s_plugin_mopt_payone_creditcard_config WHERE shop_id = ?';
        $configData = Shopware()->Db()->fetchRow($sql, $shopId);

        if (!$configData) {
            $sql = 'SELECT * FROM s_plugin_mopt_payone_creditcard_config WHERE is_default = ?';
            $configData = Shopware()->Db()->fetchRow($sql, true);
        }
        if ($this->Request()->getPost('deleteUserData') === "true") {
            $this->moptPayoneMain->getPaymentHelper()->deleteCreditcardPaymentData(Shopware()->Session()->offsetGet('sUserId'));
        }
        $minExpiryDays = (int)$configData['creditcard_min_valid'];
        $expireDate = $this->Request()->getPost('mopt_payone__cc_cardexpiredate');
        $expireSplit = str_split($expireDate, 2);
        $expireMonth = $expireSplit[1];
        $expireYear = $expireSplit[0];
        $expireTime = strtotime("+" . ($minExpiryDays + 1) . " day");
        $expireNextMonth = ($expireMonth == '12') ? '01' : $expireMonth + 1;
        $expireYear = ($expireMonth == '12') ? $expireYear + 1 : $expireYear;
        $cardExpireNextMonth = strtotime("01-" . ($expireNextMonth) . "-20" . "$expireYear" . " " . "23:59:59");
        $timediff = $cardExpireNextMonth - $expireTime;
        if ($timediff >= 0) {
            echo json_encode(true);
        } else {
            echo json_encode($minExpiryDays);
        }
    }

    protected function storeAuthorizationTokenAction()
    {
        $this->container->get('front')->Plugins()->ViewRenderer()->setNoRender();

        $token = $this->request->getParam('authorizationToken');
        $finalizeRequired = $this->request->getParam('finalize_required');

        $this->session->offsetSet('mopt_klarna_authorization_token', $token);
        $this->session->offsetSet('mopt_klarna_finalize_required', $finalizeRequired);
    }

    public function startKlarnaSessionAction()
    {
        try {
            $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        } catch (Exception $e) {
        }

        $financingtype = $this->Request()->getParam('financingtype');
        $birthdate = $this->Request()->getParam('birthdate');
        $phoneNumber = $this->Request()->getParam('phoneNumber');
        $paymentId = $this->Request()->getParam('paymentId');
        $personalId = $this->Request()->getParam('personalId');

        $result = $this->moptPayonePaymentHelper->buildAndCallKlarnaStartSession(
            $financingtype,
            $birthdate,
            $phoneNumber,
            $personalId,
            $paymentId
        );

        if ($result->getStatus() === 'ERROR') {
             echo json_encode([
                'status' => $result->getStatus(),
                'customerMessage' => Shopware()->Snippets()
                    ->getNamespace('frontend/MoptPaymentPayone/errorMessages')
                    ->get('klarnaStartSessionError'),
            ]);
        } else {
            $clientToken = $result->getPaydata()->toAssocArray()['client_token'];

            $this->session->offsetSet('mopt_klarna_workorderid', $result->getWorkorderId());
            $this->session->offsetSet('mopt_klarna_client_token', $clientToken);

            echo json_encode([
                'status' => $result->getStatus(),
                'client_token' => $clientToken,
                'paymentId' => $paymentId,
                'authErrorMessage' => Shopware()->Snippets()
                ->getNamespace('frontend/MoptPaymentPayone/errorMessages')
                ->get('klarnaAuthError'),
            ]);
        }
    }

    public function unsetSessionVarsAction()
    {
        try {
            $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        } catch (Exception $e) {
        }

        $varsToUnset = $this->Request()->getParam('vars');

        foreach ($varsToUnset as $var) {
            if ($this->session->offsetGet($var)) {
                unset($this->session[$var]);
            }
        }
    }

    public function ajaxGetPaySafeTokenAction()
    {
        $this->container->get('front')->Plugins()->ViewRenderer()->setNoRender();
        $this->Response()->setHeader('content-type', 'application/json', true);

        $tokenArray = $this->session->paySafeToken;

        if (!$tokenArray) {
            $tokenArray = $this->generatePaySafeToken();
            $this->session->paySafeToken = $tokenArray['token'];
        }

        echo json_encode($tokenArray);
    }

    protected function generatePaySafeToken()
    {
        $config = $this->moptPayoneMain->getPayoneConfig();
        $sessionID = $this->session->sessionId;
        $merchantID = $config['merchantId'];
        $timestamp = microtime(false);
        $tokenInput = $sessionID . $merchantID . $timestamp;
        $apiKey = $config['apiKey'];
        $token = hash_hmac('sha384', $tokenInput, $apiKey);
        return [
            'token' => $token,
        ];
    }

    /**
     *
     */
    public function createApplePaySessionAction()
    {
        $validationUrl = $this->Request()->getParam('validationUrl');
        $this->container->get('front')->Plugins()->ViewRenderer()->setNoRender();
        $paymentId = $this->session->moptPaymentId;
        $config = $this->moptPayoneMain->getPayoneConfig($paymentId);

        $params = [
            'merchantIdentifier' => $config['applepayMerchantId'],
            'displayName' => Shopware()->Config()->shopname,
            'initiative' => 'web',
            'initiativeContext' => Shopware()->Config()->host,
        ];
        $encodedParams = json_encode($params);
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $validationUrl);
            curl_setopt($ch, CURLOPT_SSLCERT, $config['applepayCertificate']);
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $config['applepayPrivateKeyPassword']);
            curl_setopt($ch, CURLOPT_SSLKEY, $config['applepayPrivateKey']);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedParams);
            $response = curl_exec($ch);
            if (curl_errno($ch) > 0) {
                $data['success'] = false;
                $data['error'] = curl_error($ch);
                echo json_encode($data);
            } else {
                $data['success'] = true;
                $data['merchantSession'] = $response;
                echo json_encode($data);
            }
            curl_close($ch);
        } catch (\Exception $e) {
        }
    }

    /**
     *
     */
    protected function setApplePayDeviceSupportAction()
    {
        $this->container->get('front')->Plugins()->ViewRenderer()->setNoRender();
        $applePaySupported = $this->Request()->getParam('applePaySupported') === "true" ? true : false;
        $this->session->offsetSet('moptAllowApplePay', $applePaySupported);
    }

    public function startPayPalExpressAction()
    {
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();
        $paymentId = Shopware()->Container()->get('MoptPayoneMain')->getPaymentHelper()->getPaymentPaypalv2Express()->getId();
        $config = $this->moptPayoneMain->getPayoneConfig($paymentId);
        $session = Shopware()->Session();
        $clearingType = \Payone_Enum_ClearingType::WALLET;
        $walletType = \Payone_Api_Enum_WalletType::PAYPAL_EXPRESSV2;
        $params = Shopware()->Container()->get('plugins')->Frontend()->MoptPaymentPayone()->get('MoptPayoneMain')->getParamBuilder()->buildAuthorize($config['paymentId']);
        $payoneServiceBuilder = Shopware()->Container()->get('plugins')->Frontend()->MoptPaymentPayone()->get('MoptPayoneBuilder');
        $params['api_version'] = '3.10';
        //create hash
        $basket = Shopware()->Modules()->Basket()->sGetBasket();
        $orderHash = md5(serialize($basket));
        $session->moptOrderHash = $orderHash;
        $workorderId = $session->offsetget('moptPaypalv2ExpressWorkorderId');

        $request = new Payone_Api_Request_Genericpayment($params);

        $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();
        if (!empty($workorderId)) {
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'action', 'data' => Payone_Api_Enum_GenericpaymentAction::PAYPAL_ECS_GET_EXPRESSCHECKOUTDETAILS)
            ));
            $request->setWorkorderId($workorderId);
        } else {
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'action', 'data' => Payone_Api_Enum_GenericpaymentAction::PAYPAL_ECS_SET_EXPRESSCHECKOUT)
            ));
            $router = $this->Front()->Router();
            $successurl = $this->moptPayonePaymentHelper->assembleTokenizedUrl($router, array('controller' => 'MoptPaymentEcsv2', 'action' => 'paypalv2express',
                'forceSecure' => true, 'appendSession' => false));
            $errorurl = $this->moptPayonePaymentHelper->assembleTokenizedUrl($router, array('controller' => 'MoptPaymentEcsv2', 'action' => 'paypalv2expresserror',
                'forceSecure' => true, 'appendSession' => false));
            $backurl = $this->moptPayonePaymentHelper->assembleTokenizedUrl($router, array('controller' => 'MoptPaymentEcsv2', 'action' => 'paypalv2expressabort',
                'forceSecure' => true, 'appendSession' => false));

            $request->setSuccessurl($successurl);
            $request->setBackurl($errorurl);
            $request->setErrorurl($backurl);
        }
        if ($config['authorisationMethod'] === 'Vorautorisierung') {
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'payment_action', 'data' => 'authorization')
            ));
        } else {
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'payment_action', 'data' => 'Capture')
            ));
        }

        $request->setPaydata($paydata);
        $request->setClearingtype($clearingType);
        $request->setWallettype($walletType);
        $request->setCurrency(Shopware()->Shop()->getCurrency()->getCurrency());

        $basket = $this->moptPayoneMain->sGetBasket();

        $shipping = [];
        $shipping['id'] = '';
        $shipping['name'] = 'Paypal Express Shipping';

        $shippingAmount = $this->Request()->getParam('shipping') ?? 0;

        $request->setAmount($basket['AmountNumeric'] + $shippingAmount );

        $basket['sShippingcosts'] = $shippingAmount;
        $basket['sShippingcostsWithTax'] = $shippingAmount;
        $basket['sShippingcostsNet'] = $shippingAmount;
        $basket['sShippingcostsTax'] = 0;


        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $invoicing = $this->moptPayoneMain->getParamBuilder()->getInvoicing($basket, $shipping, $userData);
        $request->setInvoicing($invoicing);

        $service = $payoneServiceBuilder->buildServicePaymentGenericpayment();

        $service->getServiceProtocol()->addRepository(Shopware()->Models()->getRepository(
            'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog'
        ));
        $response = $service->request($request);

        $response = $response->toArray();
        $jsonResponse = [];
        if (isset($response['rawResponse']['status'], $response['rawResponse']['workorderid'], $response['rawResponse']['add_paydata[orderId]']) && $response['rawResponse']['status'] == 'REDIRECT') {
            $jsonResponse['success'] = true;
            $jsonResponse['order_id'] = $response['rawResponse']['add_paydata[orderId]'];

            if (!empty($response['rawResponse']['workorderid'])) {
                $session->offsetSet('moptPaypalv2ExpressWorkorderId', $response['rawResponse']['workorderid']);
            }
        }
        if (isset($response['rawResponse']['status'], $response['rawResponse']['customermessage']) && $response['rawResponse']['status'] == 'ERROR') {
            $jsonResponse['errormessage'] = $response['customermessage'];
            $session->offsetUnset('moptPaypalv2ExpressWorkorderId');
        }

        echo json_encode($jsonResponse);
    }
}
