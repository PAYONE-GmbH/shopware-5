<?php

/**
 * $Id: $
 */
class Mopt_PayoneParamBuilder
{

    const SEQUENCENUMBER_AUTH = -1;
    const SEQUENCENUMBER_PREAUTH = 0;
    const SEQUENCENUMBER_CAPTURE = 1;

    /**
     * Payone Config
     * @var array
     */
    protected $payoneConfig = null;

    /**
     * Payone Helper
     * @var Mopt_PayoneHelper
     */
    protected $payoneHelper = null;

    /**
     * Payone Payment Helper
     * @var Mopt_PayonePaymentHelper
     */
    protected $payonePaymentHelper = null;

    /**
     * constructor, sets config and helper class
     * @param array $payoneConfig
     * @param Mopt_PayoneHelper $payoneHelper
     * @param Mopt_PayonePaymentHelper $payonePaymentHelper
     */
    public function __construct($payoneConfig, $payoneHelper, $payonePaymentHelper)
    {
        $this->payoneConfig = $payoneConfig;
        $this->payoneHelper = $payoneHelper;
        $this->payonePaymentHelper = $payonePaymentHelper;
    }

    /**
     * returns auth-parameters for API-calls
     *
     * @param integer $paymentId
     * @return array
     */
    protected function getAuthParameters($paymentId = 0)
    {
        $this->payoneConfig = Mopt_PayoneMain::getInstance()->getPayoneConfig($paymentId);

        $authParameters = array();

        $authParameters['mid'] = $this->payoneConfig['merchantId'];
        $authParameters['portalid'] = $this->payoneConfig['portalId'];
        $authParameters['key'] = $this->payoneConfig['apiKey'];
        $authParameters['aid'] = $this->payoneConfig['subaccountId'];

        $authParameters['solution_name'] = 'fatchip';
        $authParameters['solution_version'] = Shopware()->Plugins()->Frontend()->MoptPaymentPayone()->getVersion();
        $authParameters['integrator_name'] = 'shopware';
        $authParameters['integrator_version'] = Shopware()->Config()->Version;

        if ($this->payoneConfig['liveMode'] == 1) {
            $authParameters['mode'] = Payone_Enum_Mode::LIVE;
        } else {
            $authParameters['mode'] = Payone_Enum_Mode::TEST;
        }
        $authParameters['encoding'] = 'UTF-8'; // optional param default is: ISO-8859-1

        return $authParameters;
    }

    /**
     * build parameters for payment (authorize call)
     *
     * @return array
     */
    public function buildAuthorize($paymentId = 0)
    {
        return $this->getAuthParameters($paymentId);
    }

    /**
     * returns params to capture orders
     *
     * @param object $order
     * @param array $postionIds
     * @param bool $finalize
     * @param bool $includeShipment
     * @return \Payone_Api_Request_Parameter_Capture_Business
     */
    public function buildOrderCapture($order, $postionIds, $finalize, $includeShipment = false)
    {
        $paymentName = $order->getPayment()->getName();

        $params = $this->getAuthParameters($order->getPayment()->getId());
        $params['txid'] = $order->getTransactionId();
        $params['sequencenumber'] = $this->getParamSequencenumber($order);
        $params['amount'] = $this->getParamCaptureAmount($order, $postionIds, $includeShipment);
        $params['currency'] = $order->getCurrency();
        if ($paymentName === 'mopt_payone__fin_paypal_installment') {
            $params['clearingtype'] = 'fnc';
            $params['financingtype'] = Payone_Api_Enum_FinancingType::PPI;
        }

        if ($this->payonePaymentHelper->isPayoneKlarna($paymentName)) {
            $params['capturemode'] = $finalize ? 'completed' : 'notcompleted';
        }


        //create business object (used for settleaccount param)
        $business = new Payone_Api_Request_Parameter_Capture_Business();

        if (($this->payonePaymentHelper->isPayonePayInAdvance($paymentName) ||
            $this->payonePaymentHelper->isPayoneInstantBankTransfer($paymentName)) &&
            ! $this->payonePaymentHelper->isPayoneTrustly($paymentName)
        ) {
            $business->setSettleaccount($finalize ? Payone_Api_Enum_Settleaccount::YES : Payone_Api_Enum_Settleaccount::NO);
        } elseif ($this->payonePaymentHelper->isPayoneInvoice($paymentName) || $this->payonePaymentHelper->isPayoneTrustly($paymentName) || $this->payonePaymentHelper->isPayoneWechatpay($paymentName) ) {
            $business->setSettleaccount($finalize ? Payone_Api_Enum_Settleaccount::AUTO : Payone_Api_Enum_Settleaccount::NO);
        } else {
            $business->setSettleaccount($finalize ? Payone_Api_Enum_Settleaccount::AUTO : Payone_Api_Enum_Settleaccount::AUTO);
        }


        $params['business'] = $business;

        if ($this->payonePaymentHelper->isPayonePayolutionInvoice($paymentName)  || $this->payonePaymentHelper->isPayonePayolutionDebitNote($paymentName)) {
            if ($order->getBilling()->getCompany()) {
                $params['payolution_b2b'] = true;
            }
        }

        if ($this->payonePaymentHelper->isPayoneRatepayInvoice($paymentName)
            || $this->payonePaymentHelper->isPayoneRatepayInstallment($paymentName)
            || $this->payonePaymentHelper->isPayoneRatepayDirectDebit($paymentName)
        ) {
            $params['shop_id'] = $this->getParamRatepayShopId($order);
        }

        return $params;
    }

    /**
     * returns params to capture orders
     *
     * @param object $order
     * @param array $orderDetailParams
     * @param bool $finalize
     * @param bool $includeShipment
     * @return \Payone_Api_Request_Parameter_Capture_Business
     */
    public function buildCustomOrderCapture($order, $orderDetailParams, $finalize, $includeShipment = false)
    {
        $paymentName = $order->getPayment()->getName();

        $params = $this->getAuthParameters($order->getPayment()->getId());
        $params['txid'] = $order->getTransactionId();
        $params['sequencenumber'] = $this->getParamSequencenumber($order);
        $params['amount'] = $this->getParamCustomAmount($order, $orderDetailParams, $includeShipment);
        $params['currency'] = $order->getCurrency();

        //create business object (used for settleaccount param)
        $business = new Payone_Api_Request_Parameter_Capture_Business();

        if ($this->payonePaymentHelper->isPayonePayInAdvance($paymentName) || $this->payonePaymentHelper->isPayoneInstantBankTransfer($paymentName)) {
            $business->setSettleaccount($finalize ? Payone_Api_Enum_Settleaccount::YES : Payone_Api_Enum_Settleaccount::NO);
        } else {
            $business->setSettleaccount($finalize ? Payone_Api_Enum_Settleaccount::YES : Payone_Api_Enum_Settleaccount::AUTO);
        }

        $params['business'] = $business;
        $params['payolution_b2b'] = false;

        if ($this->payonePaymentHelper->isPayonePayolutionInvoice($paymentName)  || $this->payonePaymentHelper->isPayonePayolutionDebitNote($paymentName)) {
            if ($order->getBilling()->getCompany()) {
                $params['payolution_b2b'] = true;
            }
        }

        return $params;
    }

    /**
     * build parameters for debit
     *
     * @param object $order
     * @param array $postionIds
     * @param bool $includeShipment
     * @return array
     */
    public function buildOrderDebit($order, $postionIds, $includeShipment = false)
    {
        $paymentName = $order->getPayment()->getName();

        $params = $this->getAuthParameters($order->getPayment()->getId());
        $params['txid'] = $order->getTransactionId();
        $params['sequencenumber'] = $this->getParamSequencenumber($order);
        $params['amount'] = $this->getParamDebitAmount($order, $postionIds, $includeShipment);
        $params['currency'] = $order->getCurrency();

        if ($this->payonePaymentHelper->isPayonePayolutionInvoice($paymentName)  || $this->payonePaymentHelper->isPayonePayolutionDebitNote($paymentName)) {
            if ($order->getBilling()->getCompany()) {
                $params['payolution_b2b'] = true;
            }
        }

        if ($this->payonePaymentHelper->isPayoneRatepayInvoice($paymentName)
            || $this->payonePaymentHelper->isPayoneRatepayInstallment($paymentName)
            || $this->payonePaymentHelper->isPayoneRatepayDirectDebit($paymentName)
        ) {

            $params['shop_id'] = $this->getParamRatepayShopId($order);
        }

        return $params;
    }

    /**
     * build parameters for debits with custom amounts
     *
     * @param object $order
     * @param array $orderDetailParams
     * @param bool $includeShipment
     * @return array
     */
    public function buildCustomOrderDebit($order, $orderDetailParams, $includeShipment = false)
    {
        $params = $this->getAuthParameters($order->getPayment()->getId());
        $params['txid'] = $order->getTransactionId();
        $params['sequencenumber'] = $this->getParamSequencenumber($order);
        $params['amount'] = -1 * ($this->getParamCustomAmount($order, $orderDetailParams, $includeShipment));
        $params['currency'] = $order->getCurrency();

        return $params;
    }

    /**
     * increase last seq-number for non-auth'ed orders
     *
     * @param object $order
     * @return integer
     * @throws Exception
     */
    protected function getParamSequencenumber($order)
    {
        $attribute = $this->payoneHelper->getOrCreateAttribute($order);
        $seqNo = $attribute->getMoptPayoneSequencenumber();
        return $seqNo + 1;
    }

    /**
     * get Ratepay shopid
     *
     * @param object $order
     * @return string
     */
    protected function getParamRatepayShopid($order)
    {
        $attribute = $this->payoneHelper->getOrCreateAttribute($order);
        $paymentData = unserialize($attribute->getMoptPayonePaymentData());
        return $paymentData['mopt_payone__ratepay_shopid'];
    }

    /**
     * sum all positions that should be debited
     *
     * @param object $order
     * @param array $positionIds
     * @param bool $includeShipment
     * @return float
     */
    protected function getParamDebitAmount($order, $positionIds, $includeShipment = false)
    {
        $amount = 0;
        $blTaxFree = $order->getTaxFree();
        $blNet = $order->getNet();
        // check here if netto is set and it corresponds with taxfree flag
        // if order is netto and taxfree is not set add taxes to all positions
        $blDebitBrutto = (!$blTaxFree && $blNet);


        foreach ($order->getDetails() as $position) {
            if (!in_array($position->getId(), $positionIds)) {
                continue;
            }

            $flTaxRate = $position->getTaxRate();
            $positionPrice = round($position->getPrice(), 2);

            if (!$blDebitBrutto) {
                $amount += ($positionPrice * $position->getQuantity());
            } else {
                $amount += round((($positionPrice * $position->getQuantity()) * (1 + ($flTaxRate / 100))), 2);
            }

            if ($position->getArticleNumber() == 'SHIPPING') {
                $includeShipment = false;
            }
        }

        if ($includeShipment) {
            if (!$blDebitBrutto) {
                $amount += $order->getInvoiceShipping();
            } else {
                $amount += $order->getInvoiceShipping();
            }
        }

        return $amount * -1;
    }

    /**
     * return amount to capture from positions
     *
     * @param object $order
     * @param array $positionIds
     * @param bool $includeShipment
     * @return double
     */
    protected function getParamCaptureAmount($order, $positionIds, $includeShipment = false)
    {
        $amount = 0;

        $blTaxFree = $order->getTaxFree();
        $blNet = $order->getNet();
        // check here if netto is set and it corresponds with taxfree flag
        // if order is netto and taxfree is not set add taxes to all positions
        $blDebitBrutto = (!$blTaxFree && $blNet);


        foreach ($order->getDetails() as $position) {
            if (!in_array($position->getId(), $positionIds)) {
                continue;
            }

            $flTaxRate = $position->getTaxRate();
            $positionAttribute = $this->payoneHelper->getOrCreateAttribute($position);
            $alreadyCapturedAmount = $positionAttribute ? $positionAttribute->getMoptPayoneCaptured() : 0;
            //add difference between total price and already captured amount
            $positionPrice = round($position->getPrice(), 2);

            if (!$blDebitBrutto) {
                $amount += ($positionPrice * $position->getQuantity()) - $alreadyCapturedAmount;
            } else {
                $amount += (($positionPrice * $position->getQuantity()) * (1 + ($flTaxRate / 100))) - $alreadyCapturedAmount;
            }

            if ($position->getArticleNumber() == 'SHIPPING') {
                $includeShipment = false;
            }
        }

        if ($includeShipment) {
            if (!$blDebitBrutto) {
                $amount += $order->getInvoiceShipping();
            } else {
                $amount += $order->getInvoiceShipping();
            }
        }
        $amount = round($amount, 2);
        return $amount;
    }


    /**
     * return amount to capture or refund from positions
     *
     * @param object $order
     * @param array $orderDetailParams
     * @param bool $includeShipment
     * @return double
     */
    protected function getParamCustomAmount($order, $orderDetailParams, $includeShipment = false)
    {
        $amount = 0;

        $blTaxFree = $order->getTaxFree();
        $blNet = $order->getNet();
        // check here if netto is set and it corresponds with taxfree flag
        // if order is netto and taxfree is not set add taxes to all positions
        $blDebitBrutto = (!$blTaxFree && $blNet);


        foreach ($order->getDetails() as $position) {
            if (!in_array($position->getId(), array_keys($orderDetailParams))) {
                continue;
            }

            $flTaxRate = $position->getTaxRate();

            if (!$blDebitBrutto) {
                $amount += $orderDetailParams[$position->getId()];
            } else {
                $amount += ($orderDetailParams[$position->getId()] * (1 + ($flTaxRate / 100)));
            }

            if ($position->getArticleNumber() == 'SHIPPING') {
                $includeShipment = false;
            }
        }

        if ($includeShipment) {
            if (!$blDebitBrutto) {
                $amount += $order->getInvoiceShipping();
            } else {
                $amount += $order->getInvoiceShipping();
            }
        }
        $amount = round($amount, 2);
        return $amount;
    }

    /**
     * build params for bankaccount check
     *
     * @param string $paymentId
     * @param string $checkType
     * @param string $languageId
     * @param array $bankData
     * @return array
     */
    public function buildBankaccountcheck($paymentId, $checkType, $languageId, $bankData)
    {
        $params = $this->getAuthParameters($paymentId);
        $params['checktype'] = $checkType;
        $params['bankaccount'] = $this->removeWhitespaces($bankData['mopt_payone__debit_bankaccount']);
        $params['bankcode'] = $this->removeWhitespaces($bankData['mopt_payone__debit_bankcode']);
        $params['bankcountry'] = $bankData['mopt_payone__debit_bankcountry'];
        $params['language'] = $this->getLanguageFromActiveShop();

        return $params;
    }

    /**
     * build personal data parameters
     *
     * @param array $userData
     * @return \Payone_Api_Request_Parameter_Authorization_PersonalData
     */
    public function getPersonalData($userData)
    {
        $params = array();

        $billingAddress = $userData['billingaddress'];

        $params['customerid'] = empty($billingAddress['customernumber']) ? $userData['additional']['user']['customernumber'] : $billingAddress['customernumber'];
        $params['firstname'] = $billingAddress['firstname'];
        $params['lastname'] = $billingAddress['lastname'];
        $params['company'] = $billingAddress['company'];
        $params['street'] = $billingAddress['street'];
        $params['zip'] = $billingAddress['zipcode'];
        $params['city'] = $billingAddress['city'];
        if (!empty($userData['additional']['country']['countryiso'])) {
            $params['country'] = $userData['additional']['country']['countryiso'];
        } else {
            $params['country'] = $this->getCountryFromId($billingAddress['countryID']);
        }
        if (!empty($billingAddress['stateID'])) {
            $params['state'] = $this->getStateFromId($billingAddress['stateID'], $params['country']);
        }
        $params['email'] = $userData['additional']['user']['email'];
        $params['telephonenumber'] = $billingAddress['phone'];
        $params['language'] = $this->getLanguageFromActiveShop();
        $params['vatid'] = $billingAddress['ustid'];
        $params['ip'] = $_SERVER['REMOTE_ADDR'];
        $params['personalId'] = $userData['additional']['user']['mopt_payone_klarna_personalid'];

        // GitHub #29 wrong customer ip with loadbalancer setup
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
            $proxy = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (!empty($proxy)) {
                $proxyIps = explode(',', $proxy);
                $relevantIp = array_shift($proxyIps);
                $relevantIp = trim($relevantIp);
                if (!empty($relevantIp)) {
                    $params['ip'] = $relevantIp;
                }
            }
        }

        $params['gender'] = ($billingAddress['salutation'] === 'mr') ? 'm' : 'f';
        $params['salutation'] = ($billingAddress['salutation'] === 'mr') ? 'Herr' : 'Frau';
        if (!is_null($userData['additional']['user']['birthday']) && $userData['additional']['user']['birthday'] !== '0000-00-00') {
            $params['birthday'] = str_replace('-', '', $userData['additional']['user']['birthday']); //YYYYMMDD
        }

        $personalData = new Payone_Api_Request_Parameter_Authorization_PersonalData($params);

        // # SW-95: remove userid and customerid
        unset($personalData->customerid);
        unset($personalData->userid);

        return $personalData;
    }

    /**
     * build parameters for payment
     *
     * @param array $userData
     * @return \Payone_Api_Request_Parameter_Authorization_DeliveryData
     */
    public function getDeliveryData($userData)
    {
        $params = array();
        $shippingAddress = $userData['shippingaddress'];

        $params['shipping_firstname'] = $shippingAddress['firstname'];
        $params['shipping_lastname'] = $shippingAddress['lastname'];
        $params['shipping_company'] = $shippingAddress['company'];
        $params['shipping_street'] = $shippingAddress['street'];
        $params['shipping_addressaddition'] = $shippingAddress['additionalAddressLine1'];
        $params['shipping_zip'] = $shippingAddress['zipcode'];
        $params['shipping_city'] = $shippingAddress['city'];

        // Wunschpaket Packstation saves the packstation number in street
        // this has to be prefixed with 'Packstation' for the payone api to accept
        $params['shipping_street'] = ($this->payoneHelper->isWunschpaketActive() && is_numeric($shippingAddress['street'])) ?
            'Packstation' . ' ' . $shippingAddress['street'] : $shippingAddress['street'];

        $params['shipping_country'] = $this->getCountryFromId($shippingAddress['countryID']);
        if (!empty($shippingAddress['stateID'])) {
            $params['shipping_state'] = $this->getStateFromId($shippingAddress['stateID'], $params['shipping_country']);
        }

        $personalData = new Payone_Api_Request_Parameter_Authorization_DeliveryData($params);

        return $personalData;
    }

    /**
     * returns paypal payment data object
     *
     * @param type $router
     * @param bool $intialRecurringRequest
     * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_Wallet
     */
    public function getPaymentPaypal($router, $intialRecurringRequest = false)
    {
        $params = array();

        $params['wallettype'] = 'PPE';

        if ($intialRecurringRequest) {
            $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router,array('action' => 'paypalRecurringSuccess',
                'forceSecure' => true, 'appendSession' => false), null);
        } else {
            $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router,array('action' => 'success',
                'forceSecure' => true, 'appendSession' => false), null);
        }
        $params['errorurl'] = $router->assemble(array('action' => 'failure',
            'forceSecure' => true, 'appendSession' => false));
        $params['backurl'] = $router->assemble(array('action' => 'cancel',
            'forceSecure' => true, 'appendSession' => false));

        $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_Wallet($params);
        return $payment;
    }

    /**
     * create payolution payment object
     *
     * @param string $financeType
     * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_Payolution
     */
    public function getPaymentPayolutionInvoice($financeType, $paymentData, $workorderId)
    {
        $params = array();
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $params['api_version'] = '3.10';
        $params['workorderid'] = $workorderId;
        $params['birthday'] = implode(explode('-', $userData['additional']['user']['birthday']));
        if ($params['birthday'] == "00000000") {
            unset($params['birthday']);
        }
        $params['financingtype'] = $financeType;

        $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_Payolution($params);

        $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();

        if ($paymentData['mopt_payone__payolution_b2bmode']) {
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'b2b', 'data' => 'yes')
            ));
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'company_trade_registry_number', 'data' => $paymentData['mopt_payone__company_trade_registry_number'])
            ));
        }

        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'analysis_session_id', 'data' => Shopware()->Session()->get('paySafeToken'))
        ));

        $payment->setPaydata($paydata);

        return $payment;
    }


    /**
     * create payolution payment object
     *
     * @param string $financeType
     * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_Payolution
     */
    public function getPaymentPayolutionDebitNote($financeType, $paymentData, $workorderId)
    {
        $params = array();
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $params['api_version'] = '3.10';
        $params['workorderid'] = $workorderId;
        $params['birthday'] = implode(explode('-', $userData['additional']['user']['birthday']));

        if ($params['birthday'] == "00000000") {
            unset($params['birthday']);
        }
        $params['financingtype'] = $financeType;
        $params['iban'] = $this->removeWhitespaces($paymentData['mopt_payone__payolution_debitnote_iban']);
        $params['bic'] = $this->removeWhitespaces($paymentData['mopt_payone__payolution_debitnote_bic']);

        $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_Payolution($params);

        $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();

        if ($paymentData['mopt_payone__payolution_b2bmode']) {
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'b2b', 'data' => 'yes')
            ));
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'company_trade_registry_number', 'data' => $paymentData['mopt_payone__company_trade_registry_number'])
            ));
        }

        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'analysis_session_id', 'data' => Shopware()->Session()->get('paySafeToken'))
        ));

        $payment->setPaydata($paydata);

        return $payment;
    }

    /**
     * create payolution payment object
     *
     * @param string $financeType
     * @param array $paymentData
     * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_Payolution
     */
    public function getPaymentPayolutionInstallment($financeType, $paymentData)
    {
        $params = array();
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $params['api_version'] = '3.10';
        $params['birthday'] = implode(explode('-', $userData['additional']['user']['birthday']));
        if ($params['birthday'] == "00000000") {
            unset($params['birthday']);
        }
        $params['financingtype'] = $financeType;
        $params['iban'] = $this->removeWhitespaces($paymentData['mopt_payone__payolution_installment_iban']);
        $params['bic'] = $this->removeWhitespaces($paymentData['mopt_payone__payolution_installment_bic']);
        $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_Payolution($params);

        $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();

        if ($paymentData['mopt_payone__payolution_b2bmode']) {
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'b2b', 'data' => 'yes')
            ));
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'company_trade_registry_number', 'data' => $paymentData['mopt_payone__company_trade_registry_number'])
            ));
        }

        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'installment_duration', 'data' => $paymentData['mopt_payone__payolution_installment_duration'])
        ));

        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'analysis_session_id', 'data' => Shopware()->Session()->get('paySafeToken'))
        ));

        $payment->setPaydata($paydata);

        return $payment;
    }

    /**
     * create ratepay payment object
     *
     * @param string $financeType
     * @param array $paymentData
     * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_RatePay
     */
    public function getPaymentRatepayInvoice($financeType, $paymentData)
    {
        $params = array();
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $params['api_version'] = '3.10';
        $params['birthday'] = implode(explode('-', $userData['additional']['user']['birthday']));
        if ($params['birthday'] == "00000000") {
            unset($params['birthday']);
        }

        $params['financingtype'] = $financeType;
        $params['company'] = $userData['billingaddress']['company'];
        $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_RatePay($params);
        $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'customer_allow_credit_inquiry', 'data' => 'yes')
        ));
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'device_token', 'data' => $paymentData['mopt_payone__ratepay_invoice_device_fingerprint'])
        ));

        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'shop_id', 'data' => $paymentData['mopt_payone__ratepay_shopid'])
        ));


        if (isset($params['company'])) {
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'vat_id', 'data' => $userData['billingaddress']['ustid'])
            ));
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'company_id', 'data' => $paymentData['mopt_payone__ratepay_invoice_company_trade_registry_number'])
            ));
        }
        $payment->setPaydata($paydata);
        $payment->setTelephonenumber($userData['billingaddress']['phone']);
        return $payment;
    }

    /**
     * create ratepay payment object
     *
     * @param string $financeType
     * @param array $paymentData
     * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_RatePay
     */
    public function getPaymentRatepayInstallment($financeType, $paymentData)
    {
        $params = array();
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $params['api_version'] = '3.10';
        $params['birthday'] = implode(explode('-', $userData['additional']['user']['birthday']));
        if ($params['birthday'] == "00000000") {
            unset($params['birthday']);
        }

        if (!empty($paymentData['mopt_payone__ratepay_installment_iban'])) {
            $debit_paytype = 'DIRECT-DEBIT';
        } else {
            $debit_paytype = 'BANK-TRANSFER';
        }


        $params['financingtype'] = $financeType;
        $params['company'] = $userData['billingaddress']['company'];
        $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_RatePay($params);
        $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'customer_allow_credit_inquiry', 'data' => 'yes')
        ));
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'device_token', 'data' => $paymentData['mopt_payone__ratepay_installment_device_fingerprint'])
        ));

        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'shop_id', 'data' => $paymentData['mopt_payone__ratepay_shopid'])
        ));

        //toDo direct Debit
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'debit_paytype', 'data' => $debit_paytype)
        ));

        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'installment_amount', 'data' => $paymentData['mopt_payone__ratepay_installment_installment_amount'] * 100)
        ));

        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'installment_number', 'data' => $paymentData['mopt_payone__ratepay_installment_number'])
        ));

        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'last_installment_amount', 'data' => $paymentData['mopt_payone__ratepay_installment_last_installment_amount'] * 100)
        ));

        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'interest_rate', 'data' => $paymentData['mopt_payone__ratepay_installment_interest_rate'] * 100)
        ));

        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'amount', 'data' => $paymentData['mopt_payone__ratepay_installment_total'] * 100)
        ));


        if (isset($params['company'])) {
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'vat_id', 'data' => $userData['billingaddress']['ustid'])
            ));
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'company_id', 'data' => $paymentData['mopt_payone__ratepay_installment_company_trade_registry_number'])
            ));
        }
        $payment->setPaydata($paydata);
        $payment->setIban($this->removeWhitespaces($paymentData['mopt_payone__ratepay_installment_iban']));
        $payment->setBic($this->removeWhitespaces($paymentData['mopt_payone__ratepay_installment_bic']));
        $payment->setTelephonenumber($userData['billingaddress']['phone']);
        return $payment;
    }

    /**
     * create ratepay payment object
     *
     * @param string $financeType
     * @param array $paymentData
     * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_RatePay
     */
    public function getPaymentRatepayDirectDebit($financeType, $paymentData)
    {
        $params = array();
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $params['api_version'] = '3.10';
        $params['birthday'] = implode(explode('-', $userData['additional']['user']['birthday']));
        if ($params['birthday'] == "00000000") {
            unset($params['birthday']);
        }

        $params['financingtype'] = $financeType;
        $params['company'] = $userData['billingaddress']['company'];
        $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_RatePay($params);
        $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'customer_allow_credit_inquiry', 'data' => 'yes')
        ));
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'device_token', 'data' => $paymentData['mopt_payone__ratepay_direct_debit_device_fingerprint'])
        ));

        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'shop_id', 'data' => $paymentData['mopt_payone__ratepay_shopid'])
        ));


        if (isset($params['company'])) {
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'vat_id', 'data' => $userData['billingaddress']['ustid'])
            ));
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'company_id', 'data' => $paymentData['mopt_payone__ratepay_direct_debit_company_trade_registry_number'])
            ));
        }
        // $params['bankaccountholder'] = $paymentData['mopt_payone__ratepay_direct_debit_bankaccountholder'];
        $payment->setPaydata($paydata);
        $payment->setTelephonenumber($userData['billingaddress']['phone']);
        $payment->setIban($this->removeWhitespaces($paymentData['mopt_payone__ratepay_direct_debit_iban']));
        $payment->setBic($this->removeWhitespaces($paymentData['mopt_payone__ratepay_direct_debit_bic']));
        return $payment;
    }

    /**
     * create payolution payment object
     *
     * @param string $financeType
     * @param array $paymentData
     * @return \Payone_Api_Request_Genericpayment
     */
    public function getPaymentPayolutionDebitNotePreCheck($financeType, $paymentData)
    {
        $params = array();
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $params['api_version'] = '3.10';
        $params['birthday'] = implode(explode('-', $userData['additional']['user']['birthday']));
        if ($params['birthday'] == "00000000") {
            unset($params['birthday']);
        }
        $params['financingtype'] = $financeType;
        $params['iban'] = $this->removeWhitespaces($paymentData['mopt_payone__payolution_debitnote_iban']);
        $params['bic'] = $this->removeWhitespaces($paymentData['mopt_payone__payolution_debitnote_bic']);

        $payment = new Payone_Api_Request_Genericpayment($params);
        $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'action', 'data' => Payone_Api_Enum_GenericpaymentAction::PAYOLUTION_PRE_CHECK)
        ));
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'payment_type', 'data' => Payone_Api_Enum_PayolutionType::PYD_FULL)
        ));
        if ($paymentData['mopt_payone__payolution_b2bmode']) {
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'b2b', 'data' => 'yes')
            ));
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'company_trade_registry_number', 'data' => $paymentData['mopt_payone__debitnote_company_trade_registry_number'])
            ));
            $payment->setPaydata($paydata);
        }

        return $payment;
    }

    /**
     * returns paydirekt payment data object
     *
     * @param type $router
     * @param bool $intialRecurringRequest
     * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_Wallet
     */
    public function getPaymentPaydirekt($router, $intialRecurringRequest = false)
    {
        $params = array();
        $params['wallettype'] = 'PDT';

        if ($intialRecurringRequest) {
            $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router, array('action' => 'paydirektRecurringSuccess',
                'forceSecure' => true, 'appendSession' => false));
        } else {
            $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router, array('action' => 'success',
                'forceSecure' => true, 'appendSession' => false));
        }
        $params['errorurl'] = $router->assemble(array('action' => 'failure',
            'forceSecure' => true, 'appendSession' => false));
        $params['backurl'] = $router->assemble(array('action' => 'cancel',
            'forceSecure' => true, 'appendSession' => false));

        $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_Wallet($params);
        return $payment;
    }

    /**
     * returns paydirekt payment data object
     *
     * @param type $router
     * @param bool $intialRecurringRequest
     * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_Wallet
     */
    public function getPaymentPaydirektExpress($router, $intialRecurringRequest = false)
    {
        $params = array();
        $params['wallettype'] = 'PDT';

        if ($intialRecurringRequest) {
            // TODO implement and test AboCommerce
            $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router, array('action' => 'paydirektexpressRecurringSuccess',
                'forceSecure' => true, 'appendSession' => false));
        } else {
            $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router, array('action' => 'success',
                'forceSecure' => true, 'appendSession' => false));
        }
        $params['errorurl'] = $router->assemble(array('action' => 'failure',
            'forceSecure' => true, 'appendSession' => false));
        $params['backurl'] = $router->assemble(array('action' => 'cancel',
            'forceSecure' => true, 'appendSession' => false));

        return new Payone_Api_Request_Parameter_Authorization_PaymentMethod_Wallet($params);
    }

    /**
     * returns payment data for dbitnote payment
     *
     * @param array $paymentData
     * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_DebitPayment
     */
    public function getPaymentDebitNote($paymentData)
    {
        $params = array();

        $params['bankcountry'] = $paymentData['mopt_payone__debit_bankcountry'];
        $params['bankaccount'] = $this->removeWhitespaces($paymentData['mopt_payone__debit_bankaccount']);
        $params['bankcode'] = $this->removeWhitespaces($paymentData['mopt_payone__debit_bankcode']);
        $params['bankaccountholder'] = $paymentData['mopt_payone__debit_bankaccountholder'];
        $params['iban'] = $this->removeWhitespaces($paymentData['mopt_payone__debit_iban']);
        $params['bic'] = $this->removeWhitespaces($paymentData['mopt_payone__debit_bic']);
        if (Shopware()->Session()->moptMandateData) {
            $params['mandate_identification'] = Shopware()->Session()->moptMandateData['mopt_payone__mandateIdentification'];
        }

        return new Payone_Api_Request_Parameter_Authorization_PaymentMethod_DebitPayment($params);
    }

    /**
     * build payment data object for instant bank transfer payment methods
     *
     * @param object $router
     * @param array $paymentData
     * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_OnlineBankTransfer
     */
    public function getPaymentInstantBankTransfer($router, $paymentData)
    {
        $params = array();

        if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'PNT') {
            $params['onlinebanktransfertype'] = 'PNT';
            $params['bankcountry'] = $paymentData['mopt_payone__sofort_bankcountry'];
            $params['iban'] = $this->removeWhitespaces($paymentData['mopt_payone__sofort_iban']);
            $params['bic'] = $this->removeWhitespaces($paymentData['mopt_payone__sofort_bic']);
            $params['bankaccount'] = $this->removeWhitespaces($paymentData['mopt_payone__sofort_bankaccount']);
            $params['bankcode'] = $this->removeWhitespaces($paymentData['mopt_payone__sofort_bankcode']);
            $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router, array('action' => 'success',
                'forceSecure' => true, 'appendSession' => false));
            $params['errorurl'] = $router->assemble(array('action' => 'failure',
                'forceSecure' => true, 'appendSession' => false));
            $params['backurl'] = $router->assemble(array('action' => 'cancel',
                'forceSecure' => true, 'appendSession' => false));
        }

        if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'BCT') {
            $params['onlinebanktransfertype'] = 'BCT';
            $params['bankcountry'] = $paymentData['mopt_payone__bancontact_bankcountry'];
            $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router, array('action' => 'success',
                'forceSecure' => true, 'appendSession' => false));
            $params['errorurl'] = $router->assemble(array('action' => 'failure',
                'forceSecure' => true, 'appendSession' => false));
            $params['backurl'] = $router->assemble(array('action' => 'cancel',
                'forceSecure' => true, 'appendSession' => false));
        }

        if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'GPY') {
            $params['onlinebanktransfertype'] = 'GPY';
            $params['bankcountry'] = $paymentData['mopt_payone__giropay_bankcountry'];
            $params['iban'] = $this->removeWhitespaces($paymentData['mopt_payone__giropay_iban']);
            $params['bic'] = $this->removeWhitespaces($paymentData['mopt_payone__giropay_bic']);
            $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router, array('action' => 'success',
                'forceSecure' => true, 'appendSession' => false));
            $params['errorurl'] = $router->assemble(array('action' => 'failure',
                'forceSecure' => true, 'appendSession' => false));
            $params['backurl'] = $router->assemble(array('action' => 'cancel',
                'forceSecure' => true, 'appendSession' => false));
        }

        if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'EPS') {
            $params['onlinebanktransfertype'] = 'EPS';
            $params['bankcountry'] = $paymentData['mopt_payone__eps_bankcountry'];
            $params['bankgrouptype'] = $paymentData['mopt_payone__eps_bankgrouptype'];
            $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router, array('action' => 'success',
                'forceSecure' => true, 'appendSession' => false));
            $params['errorurl'] = $router->assemble(array('action' => 'failure',
                'forceSecure' => true, 'appendSession' => false));
            $params['backurl'] = $router->assemble(array('action' => 'cancel',
                'forceSecure' => true, 'appendSession' => false));
        }

        if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'IDL') {
            $session = Shopware()->Session();
            $session->offsetSet('isIdealredirect', true);
            $params['onlinebanktransfertype'] = 'IDL';
            $params['bankcountry'] = $paymentData['mopt_payone__ideal_bankcountry'];
            $params['bankgrouptype'] = $paymentData['mopt_payone__ideal_bankgrouptype'];
            $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router, array('action' => 'success',
                'forceSecure' => true, 'appendSession' => false));
            $params['errorurl'] = $router->assemble(array('action' => 'failure',
                'forceSecure' => true, 'appendSession' => false));
            $params['backurl'] = $router->assemble(array('action' => 'cancel',
                'forceSecure' => true, 'appendSession' => false));
        }

        if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'PFF') {
            $params['onlinebanktransfertype'] = 'PFF';
            $params['bankcountry'] = 'CH';
            $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router, array('action' => 'success',
                'forceSecure' => true, 'appendSession' => false));
            $params['errorurl'] = $router->assemble(array('action' => 'failure',
                'forceSecure' => true, 'appendSession' => false));
            $params['backurl'] = $router->assemble(array('action' => 'cancel',
                'forceSecure' => true, 'appendSession' => false));
        }

        if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'PFC') {
            $params['onlinebanktransfertype'] = 'PFC';
            $params['bankcountry'] = 'CH';
            $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router, array('action' => 'success',
                'forceSecure' => true, 'appendSession' => false));
            $params['errorurl'] = $router->assemble(array('action' => 'failure',
                'forceSecure' => true, 'appendSession' => false));
            $params['backurl'] = $router->assemble(array('action' => 'cancel',
                'forceSecure' => true, 'appendSession' => false));
        }

        if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'P24') {
            $params['onlinebanktransfertype'] = 'P24';
            $params['bankcountry'] = 'PL';
            $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router, array('action' => 'success',
                'forceSecure' => true, 'appendSession' => false));
            $params['errorurl'] = $router->assemble(array('action' => 'failure',
                'forceSecure' => true, 'appendSession' => false));
            $params['backurl'] = $router->assemble(array('action' => 'cancel',
                'forceSecure' => true, 'appendSession' => false));
        }

        if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'TRL') {
            $params['onlinebanktransfertype'] = 'TRL';
            $params['bankcountry'] = 'DE';
            $params['iban'] = $this->removeWhitespaces($paymentData['mopt_payone__trustly_iban']);
            $params['bic'] = $this->removeWhitespaces($paymentData['mopt_payone__trustly_bic']);
            $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router, array('action' => 'success',
                'forceSecure' => true, 'appendSession' => false));
            $params['errorurl'] = $router->assemble(array('action' => 'failure',
                'forceSecure' => true, 'appendSession' => false));
            $params['backurl'] = $router->assemble(array('action' => 'cancel',
                'forceSecure' => true, 'appendSession' => false));
        }

        $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_OnlineBankTransfer($params);
        return $payment;
    }

    /**
     * create klarna payment object
     *
     * @param string $financeType
     * @param $router
     * @return Payone_Api_Request_Parameter_Authorization_PaymentMethod_Financing
     */
    public function getPaymentKlarna($financeType, $router)
    {
        $params = array();
        $params['financingtype'] = $financeType;

        $params['successurl'] = $router->assemble(array('action' => 'success',
                                                      'forceSecure' => true, 'appendSession' => false));
        $params['errorurl'] = $router->assemble(array('action' => 'failure',
                                                      'forceSecure' => true, 'appendSession' => false));
        $params['backurl'] = $router->assemble(array('action' => 'cancel',
                                                     'forceSecure' => true, 'appendSession' => false));

        $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_Financing($params);

        // if payment is Klarna old
        if ($financeType === Payone_Api_Enum_FinancingType::KLV) {
            return $payment;
        }

        return $this->addKlarnaPaymentParameters($payment);
    }

    /**
     * @param Payone_Api_Request_Parameter_Authorization_PaymentMethod_Financing $payment
     *
     * @return Payone_Api_Request_Parameter_Authorization_PaymentMethod_Financing
     */
    public function addKlarnaPaymentParameters($payment) {
        $session = Shopware()->Session();
        $authorizationToken = $session->offsetGet('mopt_klarna_authorization_token');

        $phoneNumber = $session['mopt_klarna_phoneNumber'];

        $paydata = $this->buildKlarnaPaydata($phoneNumber);
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'authorization_token', 'data' => $authorizationToken)
        ));

        $payment->setPaydata($paydata);

        unset($session['mopt_klarna_authorization_token']);
        unset($session['mopt_klarna_phoneNumber']);

        return $payment;
    }

    /**
     * create applepay payment object
     *
     * @param $router
     * @param $token
     * @return Payone_Api_Request_Parameter_Authorization_PaymentMethod_Wallet
     */
    public function getPaymentApplepay($router, $token)
    {
        $params = array();
        $params['clearingtype'] = 'wlt';
        $params['wallettype'] = 'APL';

        $params['successurl'] = $router->assemble(array('action' => 'success',
            'forceSecure' => true, 'appendSession' => false));
        $params['errorurl'] = $router->assemble(array('action' => 'failure',
            'forceSecure' => true, 'appendSession' => false));
        $params['backurl'] = $router->assemble(array('action' => 'cancel',
            'forceSecure' => true, 'appendSession' => false));

        $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_Wallet($params);

        return $this->addApplepayPaymentParameters($payment, $token);
    }

    /**
     * @param Payone_Api_Request_Parameter_Authorization_PaymentMethod_Wallet $payment
     * @param $token
     * @return Payone_Api_Request_Parameter_Authorization_PaymentMethod_Wallet
     */
    public function addApplepayPaymentParameters($payment, $token) {

        $cardTypeMappings = [
            'visa'       => 'V',
            'mastercard' => 'M',
            'girocard'   => 'G',
            'default'    => '?',
        ];

        $paydata = $this->buildApplepayPaydata($token);

        $payment->setPaydata($paydata);

        if (!isset($token['paymentMethod']['network']) || is_null($token['paymentMethod']['network'])) {
            $cardType = 'default';
        } else {
            $cardType = $token['paymentMethod']['network'];
        }
        $payment->setCardtype($cardTypeMappings[strtolower($cardType)]);

        return $payment;
    }

    /**
     * create finance payment object
     *
     * @param string $financeType
     * @param object $router
     * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_Financing
     */
    public function getPaymentFinance($financeType, $router)
    {
        $params = array();

        $params['financingtype'] = $financeType;
        $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router, array('action' => 'success',
            'forceSecure' => true, 'appendSession' => false));
        $params['errorurl'] = $router->assemble(array('action' => 'failure',
            'forceSecure' => true, 'appendSession' => false));
        $params['backurl'] = $router->assemble(array('action' => 'cancel',
            'forceSecure' => true, 'appendSession' => false));

        $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_Financing($params);
        return $payment;
    }

    /**
     * returns payment data for cash on delivery payment
     *
     * @param array $userData
     * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_CashOnDelivery
     */
    public function getPaymentCashOnDelivery($userData)
    {
        $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_CashOnDelivery();

        switch ($userData['additional']['countryShipping']['countryiso']) {
            case 'DE':
                $payment->setShippingprovider(Payone_Api_Enum_Shippingprovider::DHL); // DE:DHL / IT:BRT
                break;
            case 'IT':
                $payment->setShippingprovider(Payone_Api_Enum_Shippingprovider::BARTOLINI); // DE:DHL / IT:Bartolini
                break;
        }

        return $payment;
    }

    /**
     * returns payment data for credit card payment
     *
     * @param object $router
     * @param array $paymentData
     * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_CreditCard
     */
    public function getPaymentCreditCard($router, $paymentData)
    {
        $params = array();
        $params['cardholder'] = $paymentData['mopt_payone__cc_cardholder'];
        $params['pseudocardpan'] = $paymentData['mopt_payone__cc_pseudocardpan'];
        $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router, array('action' => 'success',
            'forceSecure' => true, 'appendSession' => false));
        $params['errorurl'] = $router->assemble(array('action' => 'failure',
            'forceSecure' => true, 'appendSession' => false));

        $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_CreditCard($params);
        return $payment;
    }

    /**
     * returns ALIPAY payment data object
     *
     * @param type $router
     * @param bool $intialRecurringRequest
     * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_Wallet
     */
    public function getPaymentAlipay($router, $intialRecurringRequest = false)
    {
        $params = array();
        $params['wallettype'] = 'ALP';

        if ($intialRecurringRequest) {
            $params['successurl'] = $router->assemble(array('action' => 'alipayRecurringSuccess',
                'forceSecure' => true, 'appendSession' => false));
        } else {
            $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router, array('action' => 'success',
                'forceSecure' => true, 'appendSession' => false));
        }
        $params['errorurl'] = $router->assemble(array('action' => 'failure',
            'forceSecure' => true, 'appendSession' => false));
        $params['backurl'] = $router->assemble(array('action' => 'cancel',
            'forceSecure' => true, 'appendSession' => false));

        $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_Wallet($params);
        return $payment;
    }

    /**
     * returns WeChatPay payment data object
     *
     * @param type $router
     * @return \Payone_Api_Request_Parameter_Authorization_PaymentMethod_Wallet
     */
    public function getPaymentWechatpay($router)
    {
        $params = array();
        $params['wallettype'] = 'WCP';

        $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router, array('action' => 'success',
            'forceSecure' => true, 'appendSession' => false));
        $params['errorurl'] = $router->assemble(array('action' => 'failure',
            'forceSecure' => true, 'appendSession' => false));
        $params['backurl'] = $router->assemble(array('action' => 'cancel',
            'forceSecure' => true, 'appendSession' => false));

        $payment = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_Wallet($params);
        return $payment;
    }

    /**
     * returns business parameters
     *
     * @return \Payone_Api_Request_Parameter_Authorization_Business
     */
    public function getBusiness()
    {
        $params = array();

        $params['document_date'] = '';
        $params['booking_date'] = '';
        $params['due_time'] = '';

        $payment = new Payone_Api_Request_Parameter_Authorization_Business($params);
        return $payment;
    }

    /**
     * collect all items
     *
     * @param array $basket
     * @param array $shipment
     * @param array $userData
     * @return \Payone_Api_Request_Parameter_Invoicing_Transaction
     */
    public function getInvoicing($basket, $shipment, $userData)
    {
        $transaction = new Payone_Api_Request_Parameter_Invoicing_Transaction(array());

        foreach ($this->getBasketItems($basket, $shipment, $userData) as $params) {
            $item = new Payone_Api_Request_Parameter_Invoicing_Item($params);
            $transaction->addItem($item);
        }

        return $transaction;
    }

    /**
     * return all basket positions
     *
     * @param array $basket
     * @param array $shipment
     * @param array $userData
     * @return array
     */
    protected function getBasketItems($basket, $shipment, $userData)
    {
        $items = array();
        $taxFree = false;
        $blNet = false;

        if (isset($userData['additional']['charge_vat'])) {
            $taxFree = !$userData['additional']['charge_vat'];
        }

        if (isset($userData['additional']['show_net'])) {
            $blNet = !$userData['additional']['show_net'];
        }

        foreach ($basket['content'] as $article) {
            $params = array();

            $params['id'] = substr($article['ordernumber'] ?: $article['articlename'], 0, 32); //article number
            if ($taxFree) {
                $params['pr'] = round($article['netprice'], 2); //netto price
            } elseif ($blNet) {
                $params['pr'] = round($article['netprice'] * (1 + ($article['tax_rate'] / 100)), 2);
            } else {
                $params['pr'] = round($article['priceNumeric'], 2); //brutto price
            }
            $params['no'] = $article['quantity']; // ordered quantity
            $params['de'] = mb_substr($article['articlename'], 0, 100); // description
            $params['va'] = $taxFree ? 0 : number_format($article['tax_rate'], 2, '.', ''); // vat
            $params['va'] = $params['va'] * 100;
            $params['it'] = Payone_Api_Enum_InvoicingItemType::GOODS; //item type
            if ($article['modus'] == 2) {
                $params['it'] = Payone_Api_Enum_InvoicingItemType::VOUCHER;
            }
            # paypal does not accept negative values for handling use voucher instead
            # this was an issue with articles added by the SwagAdvancedPromotionSuite plugin
            if ($article['modus'] == 4 && $params['pr'] >= "0") {
                $params['it'] = Payone_Api_Enum_InvoicingItemType::HANDLING;
            }
            if ($article['modus'] == 4 && $params['pr'] < "0") {
                $params['it'] = Payone_Api_Enum_InvoicingItemType::VOUCHER;
            }
            # Repertus Set Artikel Plus compatibility
            if ($article['modus'] == 12 && $params['pr'] == "0") {
                $params['it'] = Payone_Api_Enum_InvoicingItemType::HANDLING;
            }

            $items[] = $params;
        }

        //add shipment as position
        if ($shipment) {
            $params = array();
            $params['id'] = substr('ship' . $shipment['id'], 0, 32); //shipping id
            if ($taxFree) {
                $params['pr'] = $basket['sShippingcosts'];
            } else {
                $params['pr'] = round($basket['sShippingcostsWithTax'], 2); //price
            }

            $params['no'] = 1; // ordered quantity
            $params['de'] = substr($shipment['name'], 0, 100); // description check length
            $params['va'] = $taxFree ? 0 : number_format($basket['sShippingcostsTax'], 2, '.', ''); // vat
            $params['va'] = $params['va'] * 100;
            $params['it'] = Payone_Api_Enum_InvoicingItemType::SHIPMENT;
            $params = array_map(function ($param) {
                if (is_string($param) && !preg_match('!!u', $param)) {
                    return utf8_encode($param);
                } else {
                    return $param;
                }
            }, $params);

            $items[] = $params;
        }

        return $items;
    }

    /**
     * collect items from order
     *
     * @param object $order
     * @param array $positionIds
     * @param mixed $finalize
     * @param boolean $debit
     * @param boolean $includeShipment
     * @param array $positionquantities //  used by refundOrder in bootstrap.php to override item quantity of a position
     * @return \Payone_Api_Request_Parameter_Capture_Invoicing_Transaction
     */
    public function getInvoicingFromOrder(
        $order,
        $positionIds,
        $finalize = 'skipCaptureMode',
        $debit = false,
        $includeShipment = false,
        $positionquantities = null
    )
    {
        $blTaxFree = $order->getTaxFree();
        $blNet = $order->getNet();
        // check here if netto is set and it corresponds with taxfree flag
        // if order is netto and taxfree is not set add taxes to all positions
        $blDebitBrutto = (!$blTaxFree && $blNet);

        $transaction = new Payone_Api_Request_Parameter_Capture_Invoicing_Transaction(array());

        foreach ($order->getDetails() as $position) {
            if (!in_array($position->getId(), $positionIds)) {
                continue;
            }

            if (!$debit) {
                $positionAttribute = $this->payoneHelper->getOrCreateAttribute($position);
                if ($positionAttribute->getMoptPayoneCaptured()) {
                    continue;
                }
            }
            $flTaxRate = $position->getTaxRate();
            $params = array();
            $params['id'] = $position->getArticleNumber(); //article number
            if (!$blDebitBrutto) {
                $params['pr'] = $position->getPrice(); //price
            } else {
                $params['pr'] = $position->getPrice() * (1 + ($flTaxRate / 100));
            }

            if ($debit) {
                $params['pr'] = $params['pr'] * -1;
            }
            if (isset($positionquantities) && !empty($positionquantities[$position->getId()])) {
                $params['no'] = $positionquantities[$position->getId()]; // custom refunded quantity
            } else {
                $params['no'] = $position->getQuantity(); // ordered quantity
            }

            $params['de'] = substr($position->getArticleName(), 0, 100); // description

            // Check if article is a AboCommerce Discount
            $isAboCommerceDiscount = (strpos($position->getArticlename(), 'ABO_DISCOUNT') === false) ? false : true;
            if ($order->getTaxFree()) {
                $params['va'] = 0;
            } elseif ($position->getTaxRate() == 0 &&
                $position->getTax()->getId() !== 0
                && !$isAboCommerceDiscount) {
                $params['va'] = number_format($position->getTax()->getTax(), 2, '.', '');
            } else {
                $params['va'] = number_format($position->getTaxRate(), 2, '.', ''); // vat
            }
            $params['va'] = $params['va'] * 100;
            $params['it'] = Payone_Api_Enum_InvoicingItemType::GOODS; //item type
            $mode = $position->getMode();
            if ($mode == 2) {
                $params['it'] = Payone_Api_Enum_InvoicingItemType::VOUCHER;
                $params['id'] = substr($position->getArticleNumber(), 0, 100); //article number
            }

            # paypal does not accept negative values for handling use voucher instead
            # this was an issue with articles added by the SwagAdvancedPromotionSuite plugin
            if ($mode == 4 && $params['pr'] >= "0") {
                $params['it'] = Payone_Api_Enum_InvoicingItemType::HANDLING;
                $params['id'] = substr($position->getArticleNumber(), 0, 100); //article number
            }
            if ($mode == 4 && $params['pr'] < "0") {
                $params['it'] = Payone_Api_Enum_InvoicingItemType::VOUCHER;
                $params['id'] = substr($position->getArticleNumber(), 0, 100); //article number
            }

            if ($position->getArticleNumber() == 'SHIPPING') {
                $params['it'] = Payone_Api_Enum_InvoicingItemType::SHIPMENT;
                $params['id'] = substr($position->getArticleName(), 0, 100); //article number
                //don't use $includeShipment if shipping article exists
                $includeShipment = false;
            }
            $params = array_map('htmlspecialchars_decode', $params);
            $item = new Payone_Api_Request_Parameter_Invoicing_Item($params);
            $transaction->addItem($item);
        }

        if ($finalize !== 'skipCaptureMode') {
            $transaction
                ->setCapturemode($finalize ? Payone_Api_Enum_CaptureMode::COMPLETED : Payone_Api_Enum_CaptureMode::NOTCOMPLETED);
        }

        //add shipment costs as position
        if ($includeShipment) {
            //check if already caputered in non_debit/capture mode
            if (!$debit) {
                $orderAttribute = $this->payoneHelper->getOrCreateAttribute($order);
                if ($orderAttribute->getMoptPayoneShipCaptured()) {
                    return $transaction;
                }
            }

            $params = array();
            $params['pr'] = $order->getInvoiceShipping(); //price
            if ($debit) {
                $params['pr'] = $params['pr'] * -1;
            }
            $params['it'] = Payone_Api_Enum_InvoicingItemType::SHIPMENT;
            $params['id'] = substr($order->getDispatch()->getName(), 0, 100); //article number
            $params['de'] = substr($order->getDispatch()->getName(), 0, 100); //article number
            $params['no'] = 1;
            $params['va'] = 0;
            if ($order->getInvoiceShipping() != 0) { // Tax rate calculation below would divide by zero otherwise
                $params['va'] = number_format($order->getInvoiceShipping() / $order->getInvoiceShippingNet() - 1,2,'.') * 100;
            }
            $params['va'] = $params['va'] * 100;

            $params = array_map('htmlspecialchars_decode', $params);
            $item = new Payone_Api_Request_Parameter_Invoicing_Item($params);
            $transaction->addItem($item);
        }

        return $transaction;
    }

    /**
     * returns address check params
     *
     * @param array $addressFormData
     * @param array $personalFormData
     * @param integer $paymentId
     * @return array
     */
    public function getAddressCheckParams($addressFormData, $personalFormData, $paymentId = 0)
    {
        $params = $this->getAuthParameters($paymentId);

        $params['firstname'] = $personalFormData['firstname'];
        $params['lastname'] = $personalFormData['lastname'];
        $params['company'] = $addressFormData['company'];
        $params['street'] = $addressFormData['street'];
        $params['zip'] = $addressFormData['zipcode'];
        $params['city'] = $addressFormData['city'];

        if (!empty($addressFormData['country'])) {
            $params['country'] = $this->getCountryFromId($addressFormData['country']);
            $params['language'] = $this->getLanguageFromActiveShop();
        }
        if (isset($personalFormData['phone'])) {
            $params['telephonenumber'] = $personalFormData['phone'];
        }

        return $params;
    }

    /**
     * returns consumerscore check params
     *
     * @param array $userFormData
     * @param string $paymentId
     * @return array
     */
    public function getConsumerscoreCheckParams($userFormData, $paymentId = 0)
    {
        $params = $this->getAuthParameters($paymentId);

        $params['firstname'] = $userFormData['firstname'];
        $params['lastname'] = $userFormData['lastname'];
        $params['company'] = $userFormData['company'];
        $params['street'] = $userFormData['street'];
        $params['zip'] = $userFormData['zipcode'];
        $params['city'] = $userFormData['city'];

        if (!empty($userFormData['countryID'])) {
            $params['country'] = $this->getCountryFromId($userFormData['countryID']);
            $params['language'] = $this->getLanguageFromActiveShop();
        }

        return $params;
    }

    /**
     * get country from id
     *
     * @param string $id
     * @return string
     */
    protected function getCountryFromId($id)
    {
        $sql = 'SELECT `countryiso` FROM s_core_countries WHERE id = ' . $id;
        $country = Shopware()->Db()->fetchOne($sql);
        return $country;
    }

    /**
     * get language from active shop
     *
     * @return string
     */
    protected function getLanguageFromActiveShop()
    {
        $shopLanguage = explode('_', Shopware()->Shop()->getLocale()->getLocale());

        return $shopLanguage[0];
    }

    /**
     * get state from id
     *
     * @param integer $stateId
     * @param string $countryIso
     * @return string
     */
    protected function getStateFromId($stateId, $countryIso)
    {
        $enabledTransmittingStatesCountryIsos = array('JP', 'US', 'CA', 'MX', 'AR', 'BR', 'CN', 'ID', 'IN', 'TH');

        if (!in_array($countryIso, $enabledTransmittingStatesCountryIsos)) {
            return '';
        }

        $sql = 'SELECT `shortcode` FROM s_core_countries_states WHERE id = ' . $stateId;
        $state = Shopware()->Db()->fetchOne($sql);

        return $state;
    }

    /**
     * create random payment reference
     *
     * @return string
     */
    public function getParamPaymentReference()
    {
        return 'mopt-' . uniqid() . rand(10, 99);
    }

    /**
     * build params for mandate management
     *
     * @param string $paymentId
     * @param array $userData
     * @param array $bankData
     * @return array
     */
    public function buildManageMandate($paymentId, $userData, $bankData)
    {
        $params = $this->getAuthParameters($paymentId);

        $params['clearingtype'] = 'elv';
        $params['currency'] = Shopware()->Container()->get('currency')->getShortName();
        $params['payment'] = $this->getPaymentDebitNote($bankData);
        $params['personalData'] = $this->getPersonalData($userData);

        return $params;
    }

    /**
     * build params for mandate management get file request
     *
     * @param string $paymentId
     * @param string $mandateId
     * @return array
     */
    public function buildGetFile($paymentId, $mandateId)
    {
        $params = $this->getAuthParameters($paymentId);

        $params['file_reference'] = $mandateId;
        $params['file_type'] = 'SEPA_MANDATE';
        $params['file_format'] = 'PDF';

        return $params;
    }

    public function buildPayPalExpressCheckout($paymentId, $router, $amount, $currencyName, $userData)
    {
        $this->payoneConfig = Mopt_PayoneMain::getInstance()->getPayoneConfig($paymentId);
        $params = $this->getAuthParameters($paymentId);

        $payData = new Payone_Api_Request_Parameter_Paydata_Paydata();
        $payData->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'action',
                'data' => Payone_Api_Enum_GenericpaymentAction::PAYPAL_ECS_SET_EXPRESSCHECKOUT)
        ));

        $walletParams = $this->buildPayPalEcsWalletParams($router);

        $params['clearingtype'] = Payone_Enum_ClearingType::WALLET;
        $params['amount'] = $amount;
        $params['currency'] = $currencyName;
        $params['paydata'] = $payData;
        $params['wallet'] = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_Wallet($walletParams);

        return array_merge($params, $this->buildPayPalEcsShippingAddress($userData));
    }

    public function buildPayPalExpressCheckoutDetails($paymentId, $router, $amount, $currencyName, $userData, $workerId)
    {
        $this->payoneConfig = Mopt_PayoneMain::getInstance()->getPayoneConfig($paymentId);
        $params = $this->getAuthParameters($paymentId);

        $payData = new Payone_Api_Request_Parameter_Paydata_Paydata();
        $payData->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'action',
                'data' => Payone_Api_Enum_GenericpaymentAction::PAYPAL_ECS_GET_EXPRESSCHECKOUTDETAILS)
        ));

        $walletParams = $this->buildPayPalEcsWalletParams($router);

        $params['clearingtype'] = Payone_Enum_ClearingType::WALLET;
        $params['workorderid'] = $workerId;
        $params['amount'] = $amount;
        $params['currency'] = $currencyName;
        $params['paydata'] = $payData;
        $params['wallet'] = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_Wallet($walletParams);

        return array_merge($params, $this->buildPayPalEcsShippingAddress($userData));
    }

    protected function buildPayPalEcsWalletParams($router)
    {

        $walletParams = array(
            'wallettype' => Payone_Api_Enum_WalletType::PAYPAL_EXPRESS,
            'successurl' => $this->payonePaymentHelper->assembleTokenizedUrl($router,array('action' => 'ecs',
                'forceSecure' => true, 'appendSession' => false), null),
            'errorurl' => $router->assemble(array('action' => 'ecsAbort',
                'forceSecure' => true, 'appendSession' => false)),
            'backurl' => $router->assemble(array('action' => 'ecsAbort',
                'forceSecure' => true, 'appendSession' => false)),
        );

        return $walletParams;
    }

    protected function buildPayPalEcsShippingAddress($userData)
    {
        $params = array();

        if (array_key_exists('billingaddress', $userData)) {
            $params['shipping_firstname'] = $userData[''];
            $params['shipping_lastname'] = $userData[''];
            $params['shipping_company'] = $userData[''];
            $params['shipping_street'] = $userData[''];
            $params['shipping_zip'] = $userData[''];
            $params['shipping_city'] = $userData[''];
            $params['shipping_state'] = $userData[''];
            $params['shipping_country'] = $userData[''];
        }

        return $params;
    }

    public function getPaymentPaypalEcs($router)
    {
        $params = array();

        $params['wallettype'] = Payone_Api_Enum_WalletType::PAYPAL_EXPRESS;
        $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router,array('action' => 'success',
            'forceSecure' => true, 'appendSession' => false), null);
        $params['errorurl'] = $router->assemble(array('action' => 'failure',
            'forceSecure' => true, 'appendSession' => false));
        $params['backurl'] = $router->assemble(array('action' => 'cancel',
            'forceSecure' => true, 'appendSession' => false));
        return new Payone_Api_Request_Parameter_Authorization_PaymentMethod_Wallet($params);
    }

    /**
     * Remove whitespaces from input string
     *
     * @return string without whitespaces
     */
    protected function removeWhitespaces($input)
    {
        return preg_replace('/\s+/', '', $input);
    }

    /**
     * get total basket amount
     *
     * @param array $basket
     * @param array $userData
     * @return int
     */
    protected function getParamAmount($basket, $userData)
    {
        if (!empty($userData['additional']['charge_vat'])) {
            return empty($basket['AmountWithTaxNumeric']) ? $basket['AmountNumeric'] : $basket['AmountWithTaxNumeric'];
        } else {
            return $basket['AmountNetNumeric'];
        }
    }

    /**
     * hash iframe parameters
     *
     * @param array $request
     * @return string
     */
    protected function getParamHash($request)
    {
        $payoneConfig = Mopt_PayoneMain::getInstance()->getPayoneConfig();
        ksort($request);

        $hashString = '';
        foreach ($request as $value) {
            $hashString .= $value;
        }

        return md5($hashString .= $payoneConfig['apiKey']);
    }

    /**
     * determine authorization method
     *
     * @param array $payoneConfig
     * @return string
     */
    protected function getParamAuthorizationMethod($payoneConfig)
    {
        $preAuthValues = array('preAuthorise', 'Vorautorisierung');

        if (in_array($payoneConfig['authorisationMethod'], $preAuthValues)) {
            return Payone_Api_Enum_RequestType::PREAUTHORIZATION;
        } else {
            return Payone_Api_Enum_RequestType::AUTHORIZATION;
        }
    }

    /**
     * build custom params
     *
     * @return string
     */
    protected function getCustomSessionParameters()
    {
        $session = Shopware()->Session();

        //create hash
        $orderVariables = $session['sOrderVariables'];
        $orderHash = md5(serialize($orderVariables));
        $session->moptOrderHash = $orderHash;

        return 'session-' . Shopware()->Shop()->getId() . '|' . Shopware()->Modules()->Admin()->sSYSTEM->sSESSION_ID .
            '|' . $orderHash;
    }

    /**
     * returns basic parameters for API-calls
     *
     * @return array
     */
    public function getBasicParameters()
    {
        $params = array();

        $params['solution_name'] = 'fatchip';
        $params['solution_version'] = Shopware()->Plugins()->Frontend()->MoptPaymentPayone()->getVersion();
        $params['integrator_name'] = 'shopware';
        $params['integrator_version'] = Shopware()->Config()->Version;
        $params['encoding'] = 'UTF-8'; // optional param default is: ISO-8859-1

        return $params;
    }

    public function buildPayDirektExpressCheckout($paymentId, $router, $amount, $currencyName, $userData)
    {
        $params = $this->getAuthParameters($paymentId);

        $payData = new Payone_Api_Request_Parameter_Paydata_Paydata();
        $payData->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'action',
                'data' => Payone_Api_Enum_GenericpaymentAction::PAYDIREKTEXPRESS_CHECKOUT)
        ));

        if ($this->payoneConfig['authorisationMethod'] == 'Vorautorisierung') {
            $payData->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'type',
                    'data' => 'order')
            ));
        } else {
            $payData->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key' => 'type',
                    'data' => 'directsale')
            ));
        }
        $payData->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'web_url_shipping_terms',
                'data' => 'https://www.google.de')
        ));

        $walletParams = $this->buildPaydirektWalletParams($router);

        $params['clearingtype'] = Payone_Enum_ClearingType::WALLET;
        $params['amount'] = $amount;
        $params['currency'] = $currencyName;
        $params['paydata'] = $payData;
        $params['api_Version'] = '3.10';


        return array_merge($params, $walletParams);
    }

    public function buildPaydirektExpressGetStatus($paymentId, $router, $amount, $currencyName, $userData, $workerId)
    {
        $this->payoneConfig = Mopt_PayoneMain::getInstance()->getPayoneConfig($paymentId);
        $params = $this->getAuthParameters($paymentId);

        $payData = new Payone_Api_Request_Parameter_Paydata_Paydata();
        $payData->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key' => 'action',
                'data' => Payone_Api_Enum_GenericpaymentAction::PAYDIREKTEXPRESS_GETSTATUS)
        ));

        $walletParams = $this->buildPaydirektWalletParams($router);
        $params['clearingtype'] = Payone_Enum_ClearingType::WALLET;
        $params['workorderid'] = $workerId;
        $params['amount'] = $amount;
        $params['currency'] = $currencyName;
        $params['paydata'] = $payData;
        $params['wallet'] = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_Wallet($walletParams);
        $params['api_Version'] = '3.10';

        return $params;
    }

    protected function buildPaydirektWalletParams($router)
    {
        $walletParams = array(
            'wallettype' => Payone_Api_Enum_WalletType::PAYDIREKT_EXPRESS,
            'successurl' => $this->payonePaymentHelper->assembleTokenizedUrl($router, array('action' => 'paydirektexpress',
                'forceSecure' => true, 'appendSession' => true)),
            'errorurl' => $router->assemble(array('action' => 'paydirektexpressAbort',
                'forceSecure' => true, 'appendSession' => true)),
            'backurl' => $router->assemble(array('action' => 'paydirektexpressAbort',
                'forceSecure' => true, 'appendSession' => true)),
        );

        return $walletParams;
    }

    public function buildKlarnaSessionStartParams($clearingtype, $paymentFinancingtype, $basket, $shippingCosts, $paymentId)
    {
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $phoneNumber = Shopware()->Session()->offsetGet('mopt_klarna_phoneNumber');

        $paydata = $this->buildKlarnaPaydata($phoneNumber);
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key'  => 'action', 'data' => Payone_Api_Enum_GenericpaymentAction::KLARNA_START_SESSION)
        ));

        $params = $this->getAuthParameters($paymentId);
        $params['clearingtype'] = $clearingtype;
        $params['financingtype'] = $paymentFinancingtype;
        $params['amount'] = $basket['AmountNumeric'] + $shippingCosts['brutto'];
        $params['paydata'] = $paydata;
        $params['currency'] = Shopware()->Container()->get('currency')->getShortName();
        $params['telephonenumber'] = Shopware()->Session()->offsetGet('mopt_klarna_phoneNumber');
        $params['title'] = $this->payonePaymentHelper->getKlarnaTitle($userData);

        return $params;
    }

    /**
     * @param $phoneNumber
     *
     * @return Payone_Api_Request_Parameter_Paydata_Paydata
     */
    protected function buildKlarnaPaydata($phoneNumber) {
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();

        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key'  => 'shipping_email', 'data' => $userData['additional']['user']['email'])
        ));

        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key'  => 'shipping_title', 'data' => $this->payonePaymentHelper->getKlarnaTitle($userData))
        ));

        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            array('key'  => 'shipping_telephonenumber', 'data' => $phoneNumber)
        ));

/*        if ($userData['billingaddress']['company']) {
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key'  => 'organization_entity_type', 'data' => 'OTHER')
            ));
            $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
                array('key'  => 'organization_registry_id', 'data' => $userData['billingaddress']['vatId'])
            ));
        }
*/

        return $paydata;
    }

    /**
     *
     * @return Payone_Api_Request_Parameter_Paydata_Paydata
     */
    protected function buildApplepayPaydata($tokenData) {
        $paydata = new Payone_Api_Request_Parameter_Paydata_Paydata();

        if (!isset($tokenData['paymentData']['data']) || is_null($tokenData['paymentData']['data'])) {
            $tokenData['paymentData']['data'] = '';
        }
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            [
                'key'  => 'paymentdata_token_data',
                'data' => $tokenData['paymentData']['data'],
            ]
        ));

        if (!isset($tokenData['paymentData']['header']['ephemeralPublicKey']) || is_null($tokenData['paymentData']['header']['ephemeralPublicKey'])) {
            $tokenData['paymentData']['header']['ephemeralPublicKey'] = '';
        }
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            [
                'key'  => 'paymentdata_token_ephemeral_publickey',
                'data' => $tokenData['paymentData']['header']['ephemeralPublicKey'],
            ]
        ));

        if (!isset($tokenData['paymentData']['header']['publicKeyHash']) || is_null($tokenData['paymentData']['header']['publicKeyHash'])) {
            $tokenData['paymentData']['header']['publicKeyHash'] = '';
        }
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            [
                'key'  => 'paymentdata_token_publickey_hash',
                'data' => $tokenData['paymentData']['header']['publicKeyHash'],
            ]
        ));

        if (!isset($tokenData['paymentData']['signature']) || is_null($tokenData['paymentData']['signature'])) {
            $tokenData['paymentData']['signature'] = '';
        }
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            [
                'key'  => 'paymentdata_token_signature',
                'data' => $tokenData['paymentData']['signature'],
            ]
        ));

        if (!isset($tokenData['paymentData']['header']['transactionId']) || is_null($tokenData['paymentData']['header']['transactionId'])) {
            $tokenData['paymentData']['header']['transactionId'] = '';
        }
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            [
                'key'  => 'paymentdata_token_transaction_id',
                'data' => $tokenData['paymentData']['header']['transactionId'],
            ]
        ));

        if (!isset($tokenData['paymentData']['version']) || is_null($tokenData['paymentData']['version'])) {
            $tokenData['paymentData']['version'] = 'EC_v1';
        }
        $paydata->addItem(new Payone_Api_Request_Parameter_Paydata_DataItem(
            [
                'key'  => 'paymentdata_token_version',
                'data' => $tokenData['paymentData']['version'],
            ]
        ));

        return $paydata;
    }
}
