<?php

use Shopware\Plugins\Community\Frontend\MoptPaymentPayone\Components\Payone\PayoneEnums;
use Shopware\Plugins\Community\Frontend\MoptPaymentPayone\Components\Payone\PayoneRequest;

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
            $authParameters['mode'] = 'live';
        } else {
            $authParameters['mode'] = 'test';
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
     * @return array
     */
    public function buildOrderCapture($order, $postionIds, $finalize, $includeShipment = false)
    {
        $paymentName = $order->getPayment()->getName();

        $params = $this->getAuthParameters($order->getPayment()->getId());
        $params['txid'] = $order->getTransactionId();
        $params['sequencenumber'] = $this->getParamSequencenumber($order);
        $params['amount'] = $this->getParamCaptureAmount($order, $postionIds, $includeShipment);
        $params['currency'] = $order->getCurrency();

        if ($this->payonePaymentHelper->isPayoneKlarna($paymentName)) {
            $params['capturemode'] = $finalize ? 'completed' : 'notcompleted';
        }

        if (($this->payonePaymentHelper->isPayonePayInAdvance($paymentName) ||
            $this->payonePaymentHelper->isPayoneInstantBankTransfer($paymentName))
        ) {
            $params['settleaccount'] = $finalize ? PayoneEnums::YES : PayoneEnums::NO;
        } elseif ($this->payonePaymentHelper->isPayoneInvoice($paymentName) || $this->payonePaymentHelper->isPayoneWechatpay($paymentName) ) {
            $params['settleaccount'] = $finalize ? PayoneEnums::AUTO : PayoneEnums::NO;
        } else {
            $params['settleaccount'] = PayoneEnums::AUTO;
        }

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
     * @return array
     */
    public function buildCustomOrderCapture($order, $orderDetailParams, $finalize, $includeShipment = false)
    {
        $paymentName = $order->getPayment()->getName();

        $params = $this->getAuthParameters($order->getPayment()->getId());
        $params['txid'] = $order->getTransactionId();
        $params['sequencenumber'] = $this->getParamSequencenumber($order);
        $params['amount'] = $this->getParamCustomAmount($order, $orderDetailParams, $includeShipment);
        $params['currency'] = $order->getCurrency();

        if ($this->payonePaymentHelper->isPayonePayInAdvance($paymentName) || $this->payonePaymentHelper->isPayoneInstantBankTransfer($paymentName)) {
            $params['settleaccount'] = $finalize ? PayoneEnums::YES : PayoneEnums::NO;
        } else {
            $params['settleaccount'] = $finalize ? PayoneEnums::YES : PayoneEnums::AUTO;
        }
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
        if (!empty($paymentData['mopt_payone__fin_ratepay_invoice_shopid'])) {
            return $paymentData['mopt_payone__fin_ratepay_invoice_shopid'];
        }
        if (!empty($paymentData['mopt_payone__fin_ratepay_direct_debit_shopid'])) {
            return $paymentData['mopt_payone__fin_ratepay_direct_debit_shopid'];
        }
        if (!empty($paymentData['mopt_payone__fin_ratepay_installment_shopid'])) {
            return $paymentData['mopt_payone__fin_ratepay_installment_shopid'];
        }

        return $paymentData['mopt_payone__fin_ratepay_shopid'];
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
            if (!$positionIds || !in_array($position->getId(), $positionIds)) {
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
            $amount += $order->getInvoiceShipping();
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
            $amount += $order->getInvoiceShipping();
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
            $amount += $order->getInvoiceShipping();
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
     * @return $params
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

        switch ($billingAddress['salutation']) {
            case 'mr' :
                $params['gender'] = 'm';
                $params['salutation'] = 'Herr';
                break;
            case 'ms' :
                $params['gender'] = 'f';
                $params['salutation'] = 'Frau';
                break;
            default:
                // @see https://docs.payone.com/display/public/PLATFORM/gender+-+definition
                // diverse is currently not supported
                // $params['gender'] = 'd';
                // $params['salutation'] = 'Hallo';
                $params['gender'] = 'm';
                $params['salutation'] = 'Herr';
        }

        if (!is_null($userData['additional']['user']['birthday']) && $userData['additional']['user']['birthday'] !== '0000-00-00') {
            $params['birthday'] = str_replace('-', '', $userData['additional']['user']['birthday']); //YYYYMMDD
        }


        // # SW-95: remove userid and customerid
        $params['customerid'] = NULL;
        $params['userid'] = NULL;
        return $params;
    }

    /**
     * build parameters for payment
     *
     * @param array $userData
     * @return array $params
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

        return $params;
    }

    /**
     * returns paypal payment data object
     *
     * @param type $router
     * @param bool $intialRecurringRequest
     * @return $params
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

        return $params;
    }

    /**
     * returns paypal payment data object
     *
     * @param type $router
     * @param bool $intialRecurringRequest
     * @return $params
     */
    public function getPaymentPaypalv2($router, $intialRecurringRequest = false)
    {
        $params = array();

        $params['wallettype'] = 'PAL';

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

        return $params;
    }


    /**
     * create payolution payment object
     *
     * @param string $financeType
     * @return array
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

        if ($paymentData['mopt_payone__fin_payolution_invoice_b2bmode']) {
            $params['add_paydata[b2b]'] = 'yes';
            $params['add_paydata[company_trade_registry_number]'] = $paymentData['mopt_payone__fin_payolution_invoice_vatid'];
        }

        $params['add_paydata[analysis_session_id]'] = Shopware()->Session()->get('paySafeToken');

        return $params;
    }


    /**
     * create payolution payment object
     *
     * @param string $financeType
     * @return array
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
        if (empty($params['birthday'])) {
            if ($financeType === PayoneEnums::PYV) {
                $params['birthday'] = str_replace('-','', $paymentData['mopt_payone__fin_payolution_invoice_birthdaydate']);
            }

            if ($financeType === PayoneEnums::PYD) {
                $params['birthday'] = str_replace('-','', $paymentData['mopt_payone__fin_payolution_debitnote_birthdaydate']);
            }

            if ($financeType === PayoneEnums::PYS) {
                $params['birthday'] = str_replace('-','', $paymentData['mopt_payone__fin_payolution_installment_birthdaydate']);
            }
        }
        $params['financingtype'] = $financeType;
        $params['iban'] = $this->removeWhitespaces($paymentData['mopt_payone__fin_payolution_debitnote_iban']);
        $params['bic'] = $this->removeWhitespaces($paymentData['mopt_payone__fin_payolution_debitnote_bic']);

        if ($paymentData['mopt_payone__fin_payolution_debitnote_b2bmode']) {
            $params['add_paydata[b2b]'] = 'yes';
            $params['add_paydata[company_trade_registry_number]'] = $paymentData['mopt_payone__fin_payolution_invoice_vatid'];
        }

        $params['add_paydata[analysis_session_id]'] = Shopware()->Session()->get('paySafeToken');

        return $params;
    }

    /**
     * create payolution payment object
     *
     * @param string $financeType
     * @param array $paymentData
     * @return array
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
        $params['iban'] = $this->removeWhitespaces($paymentData['mopt_payone__fin_payolution_installment_iban']);
        $params['bic'] = $this->removeWhitespaces($paymentData['mopt_payone__fin_payolution_installment_bic']);

        if ($paymentData['mopt_payone__fin_payolution_installment_b2bmode']) {
            $params['add_paydata[b2b]'] = 'yes';
            $params['add_paydata[company_trade_registry_number]'] = $paymentData['mopt_payone__fin_payolution_invoice_vatid'];
        }

        $params['add_paydata[installment_duration]'] = $paymentData['mopt_payone__fin_payolution_installment_duration'];
        $params['add_paydata[analysis_session_id]'] = Shopware()->Session()->get('paySafeToken');

        return $params;
    }

    /**
     * create ratepay payment object
     *
     * @param string $financeType
     * @param array $paymentData
     * @return array
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
        $params['add_paydata[customer_allow_credit_inquiry]'] = 'yes';
        $params['add_paydata[device_token]'] = $paymentData['mopt_payone__fin_ratepay_invoice_device_fingerprint'];
        $params['add_paydata[shop_id]'] = $paymentData['mopt_payone__fin_ratepay_invoice_shopid'];

        if (isset($params['company'])) {
            $params['add_paydata[vat_id]'] = $userData['billingaddress']['ustid'];
            $params['add_paydata[company_id]'] = $paymentData['mopt_payone__fin_ratepay_invoice_company_trade_registry_number'];
        }
        $params['telephonenumber'] = $userData['billingaddress']['phone'];
        return $params;
    }

    /**
     * create ratepay payment object
     *
     * @param string $financeType
     * @param array $paymentData
     * @return $params
     */
    public function getPaymentRatepayInstallment($financeType, $paymentData)
    {
        $params = [];
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $params['api_version'] = '3.10';
        $params['birthday'] = implode(explode('-', $userData['additional']['user']['birthday']));
        if ($params['birthday'] == "00000000") {
            unset($params['birthday']);
        }

        if (!empty($paymentData['mopt_payone__fin_ratepay_installment_iban'])) {
            $debit_paytype = 'DIRECT-DEBIT';
        } else {
            $debit_paytype = 'BANK-TRANSFER';
        }
        $params['financingtype'] = $financeType;
        $params['company'] = $userData['billingaddress']['company'];
        $params['add_paydata[customer_allow_credit_inquiry]'] = 'yes';
        $params['add_paydata[device_token]'] = $paymentData['mopt_payone__fin_ratepay_installment_device_fingerprint'];
        $params['add_paydata[shop_id]'] = $paymentData['mopt_payone__fin_ratepay_installment_shopid'];
        $params['add_paydata[debit_paytype]'] = $debit_paytype;
        $params['add_paydata[installment_amount]'] = $paymentData['mopt_payone__fin_ratepay_installment_amount'] * 100;
        $params['add_paydata[installment_number]'] = $paymentData['mopt_payone__fin_ratepay_installment_number'];
        $params['add_paydata[last_installment_amount]'] = $paymentData['mopt_payone__fin_ratepay_installment_last_installment_amount'] * 100;
        $params['add_paydata[interest_rate]'] = $paymentData['mopt_payone__fin_ratepay_installment_interest_rate'] * 100;
        $params['add_paydata[amount]'] = $paymentData['mopt_payone__fin_ratepay_installment_total'] * 100;
        if (isset($params['company'])) {
            $params['add_paydata[vat_id]'] = $userData['billingaddress']['ustid'];
            $params['add_paydata[company_id]'] = $paymentData['mopt_payone__fin_ratepay_installment_company_trade_registry_number'];
        }
        $params['iban'] = $this->removeWhitespaces($paymentData['mopt_payone__fin_ratepay_installment_iban']);
        $params['bic'] = $this->removeWhitespaces($paymentData['mopt_payone__fin_ratepay_installment_bic']);
        $params['telephonenumber'] = $userData['billingaddress']['phone'];
        return $params;
    }

    /**
     * create ratepay payment object
     *
     * @param string $financeType
     * @param array $paymentData
     * @return array
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
        $params['add_paydata[customer_allow_credit_inquiry]'] = 'yes';
        $params['add_paydata[device_token]'] = $paymentData['mopt_payone__fin_ratepay_direct_debit_device_fingerprint'];
        $params['add_paydata[shop_id]'] = $paymentData['mopt_payone__fin_ratepay_direct_debit_shopid'];
        $params['financingtype'] = $financeType;
        $params['company'] = $userData['billingaddress']['company'];
        if (isset($params['company'])) {
            $params['add_paydata[vat_id]'] = $userData['billingaddress']['ustid'];
            $params['add_paydata[company_id]'] = $paymentData['mopt_payone__fin_ratepay_direct_debit_company_trade_registry_number'];
        }
        $params['iban'] = $this->removeWhitespaces($paymentData['mopt_payone__fin_ratepay_direct_debit_iban']);
        $params['bic'] = $this->removeWhitespaces($paymentData['mopt_payone__fin_ratepay_direct_debit_bic']);
        $params['telephonenumber'] = $userData['billingaddress']['phone'];
        return $params;
    }

    /**
     * returns payment data for dbitnote payment
     *
     * @param array $paymentData
     * @return array $params
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

        return $params;
    }

    /**
     * build payment data object for instant bank transfer payment methods
     *
     * @param object $router
     * @param array $paymentData
     * @return $paras
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
        }

        if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'BCT') {
            $params['onlinebanktransfertype'] = 'BCT';
            $params['bankcountry'] = $paymentData['mopt_payone__bancontact_bankcountry'];
        }

        if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'EPS') {
            $params['onlinebanktransfertype'] = 'EPS';
            $params['bankcountry'] = $paymentData['mopt_payone__eps_bankcountry'];
            $params['bankgrouptype'] = $paymentData['mopt_payone__eps_bankgrouptype'];
        }

        if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'IDL') {
            $session = Shopware()->Session();
            $session->offsetSet('isIdealredirect', true);
            $params['onlinebanktransfertype'] = 'IDL';
            $params['bankcountry'] = $paymentData['mopt_payone__ideal_bankcountry'];
        }

        if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'PFF') {
            $params['onlinebanktransfertype'] = 'PFF';
            $params['bankcountry'] = 'CH';
        }

        if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'PFC') {
            $params['onlinebanktransfertype'] = 'PFC';
            $params['bankcountry'] = 'CH';
        }

        if ($paymentData['mopt_payone__onlinebanktransfertype'] == 'P24') {
            $params['onlinebanktransfertype'] = 'P24';
            $params['bankcountry'] = 'PL';
        }
        $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router, array('action' => 'success',
            'forceSecure' => true, 'appendSession' => false));
        $params['errorurl'] = $router->assemble(array('action' => 'failure',
            'forceSecure' => true, 'appendSession' => false));
        $params['backurl'] = $router->assemble(array('action' => 'cancel',
            'forceSecure' => true, 'appendSession' => false));

        return $params;
    }

    /**
     * create klarna payment object
     *
     * @param string $financeType
     * @param $router
     * @return array
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

        // if payment is Klarna old
        if ($financeType === PayoneEnums::FinancingType_KLV) {
            return $params;
        }

        $session = Shopware()->Session();
        $authorizationToken = $session->offsetGet('mopt_klarna_authorization_token');

        $phoneNumber = $session->offsetGet('mopt_klarna_phoneNumber');
        $params['add_paydata[authorization_token]'] = $authorizationToken;
        $klarnaParams = $this->buildKlarnaPaydata($phoneNumber);
        $params = array_merge($klarnaParams, $params);

        unset($session['mopt_klarna_authorization_token']);
        unset($session['mopt_klarna_phoneNumber']);

        return $params;
    }

    /**
     * create applepay payment object
     *
     * @param $router
     * @param $token
     * @return $params
     */
    public function getPaymentApplepay($router, $token)
    {
        $params = [];
        $params['clearingtype'] = 'wlt';
        $params['wallettype'] = 'APL';

        $params['successurl'] = $router->assemble(array('action' => 'success',
            'forceSecure' => true, 'appendSession' => false));
        $params['errorurl'] = $router->assemble(array('action' => 'failure',
            'forceSecure' => true, 'appendSession' => false));
        $params['backurl'] = $router->assemble(array('action' => 'cancel',
            'forceSecure' => true, 'appendSession' => false));

        return $this->addApplepayPaymentParameters($params, $token);
    }

    public function getPaymentGooglePay($router, $token)
    {
        $params = array();
        $params['clearingtype'] = 'wlt';
        $params['wallettype'] = PayoneEnums::GOOGLEPAY_WALLET_TYPE;

        $params['successurl'] = $router->assemble(array('action' => 'success',
            'forceSecure' => true, 'appendSession' => false));
        $params['errorurl'] = $router->assemble(array('action' => 'failure',
            'forceSecure' => true, 'appendSession' => false));
        $params['backurl'] = $router->assemble(array('action' => 'cancel',
            'forceSecure' => true, 'appendSession' => false));

        return $this->addGooglePayPaymentParameters($params, $token);
    }

    public function getPaymentClick2Pay($router, $token, $type, $cardholderName, $cardNumber, $cardType, $cardExpiry)
    {
        $params = array();
        $params['clearingtype'] = $type === 'manual' ? 'cc' : 'wlt';
        if ($type !== 'manual') {
            $params['wallettype'] = PayoneEnums::CLICK2PAY_WALLET_TYPE;
        }
        $params['cardtype'] = $this->getClick2PayCardType($cardType);
        $params['successurl'] = $router->assemble(array('action' => 'success',
            'forceSecure' => true, 'appendSession' => false));
        $params['errorurl'] = $router->assemble(array('action' => 'failure',
            'forceSecure' => true, 'appendSession' => false));
        $params['backurl'] = $router->assemble(array('action' => 'cancel',
            'forceSecure' => true, 'appendSession' => false));

        return $this->addClick2PayPaymentParameters($params, $token, $type, $cardholderName, $cardNumber, $cardType, $cardExpiry);
    }

    private function getClick2PayCardType($cardType)
    {

        return strtoupper(substr($cardType, 0, 1));
    }

    /**
     * create secured invoice object
     *
     * @param string $financeType
     * @param array $paymentData
     * @return $params
     */
    public function getPaymentPayoneSecuredInvoice($financeType, $paymentData)
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
        $params['vatid'] = empty($paymentData['mopt_payone__secured_invoice_vatid']) ? $userData['billingaddress']['vatId'] : $paymentData['mopt_payone__secured_invoice_vatid'];
        $params['add_paydata[device_token]'] = $paymentData['mopt_payone__payone_secured_invoice_token'];
        $params['telephonenumber'] = $userData['billingaddress']['phone'];
        return $params;
    }

    /**
     * create secured installments payment object
     *
     * @param string $financeType
     * @param array $paymentData
     * @return $params
     */
    public function getPaymentPayoneSecuredInstallments($financeType, $paymentData)
    {
        $params = array();
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $params['api_version'] = '3.10';
        $params['financingtype'] = $financeType;
        $params['add_paydata[device_token]'] = $paymentData['mopt_payone__payone_secured_installment_token'];
        $params['add_paydata[installment_option_id]'] = $paymentData['mopt_payone__payone_secured_installment_plan'];
        $params['telephonenumber'] = $paymentData['mopt_payone__payone_secured_installment_telephone'];
        $params['iban'] = $paymentData['mopt_payone__payone_secured_installment_iban'];
        $params['bankaccountholder'] = $userData['billingaddress']['firstname'] . ' ' . $userData['billingaddress']['lastname'];
        $params['birthday'] = implode(explode('-', $paymentData['mopt_payone__payone_secured_installment_birthdaydate']));
        return $params;
    }

    /**
     * create secured dirctdebit object
     *
     * @param string $financeType
     * @param array $paymentData
     * @return $params
 */
    public function getPaymentPayoneSecuredDirectdebit($financeType, $paymentData)
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
        $params['add_paydata[device_token]'] = $paymentData['mopt_payone__payone_secured_directdebit_token'];
        $params['telephonenumber'] = $paymentData['mopt_payone__payone_secured_directdebit_telephone'];
        $params['iban'] = $paymentData['mopt_payone__payone_secured_directdebit_iban'];
        $params['bankaccountholder'] = $userData['billingaddress']['firstname'] . ' ' . $userData['billingaddress']['lastname'];
        $params['birthday'] = implode(explode('-', $paymentData['mopt_payone__payone_secured_directdebit_birthdaydate']));
        return $params;
    }

    /**
     * @param  $payment
     * @param $token
     * @return $params
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
     * @param $params
     * @param $token
     * @return $params
     */
    public function addGooglePayPaymentParameters($params, $token) {
        $paydata = $this->buildGooglePayPaydata($token);
        $params = array_merge($paydata, $params);
        return $params;
    }

    /**
     * @param $params
     * @param $token
     * @return $params
     */
    public function addClick2PayPaymentParameters($params, $token, $type, $cardholderName, $cardNumber, $cardType, $cardExpiry) {
        $paydata = $this->buildClick2PayPaydata($token, $type, $cardholderName, $cardNumber, $cardType, $cardExpiry);
        $params = array_merge($paydata, $params);
        return $params;
    }

    /**
     * create finance payment object
     *
     * @param string $financeType
     * @param object $router
     * @return $params
     */
    public function getPaymentFinance($financeType, $router)
    {
        $params = [];

        $params['financingtype'] = $financeType;
        $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router, array('action' => 'success',
            'forceSecure' => true, 'appendSession' => false));
        $params['errorurl'] = $router->assemble(array('action' => 'failure',
            'forceSecure' => true, 'appendSession' => false));
        $params['backurl'] = $router->assemble(array('action' => 'cancel',
            'forceSecure' => true, 'appendSession' => false));
        return $params;
    }

    /**
     * returns payment data for cash on delivery payment
     *
     * @param array $userData
     * @return array $params
     */
    public function getPaymentCashOnDelivery($userData)
    {
        $params = [];

        switch ($userData['additional']['countryShipping']['countryiso']) {
            case 'DE':
                $params['shippingprovider'] = PayoneEnums::DHL; // DE:DHL / IT:BRT
                break;
            case 'IT':
                $params['shippingprovider'] = PayoneEnums::BARTOLINI; // DE:DHL / IT:Bartolini
                break;
        }

        return $params;
    }

    /**
     * returns payment data for credit card payment
     *
     * @param object $router
     * @param array $paymentData
     * @return $params
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

        return $params;
    }

    /**
     * returns ALIPAY payment data object
     *
     * @param type $router
     * @param bool $intialRecurringRequest
     * @return $params
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

        return $params;
    }

    /**
     * returns WeChatPay payment data object
     *
     * @param type $router
     * @return $params
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

        return $params;
    }

    /**
     * returns WeChatPay payment data object
     *
     * @param type $router
     * @return $params
     */
    public function getPaymentWero($router)
    {
        $params = array();
        $params['wallettype'] = PayoneEnums::WERO_WALLET_TYPE;

        $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router, array('action' => 'success',
            'forceSecure' => true, 'appendSession' => false));
        $params['errorurl'] = $router->assemble(array('action' => 'failure',
            'forceSecure' => true, 'appendSession' => false));
        $params['backurl'] = $router->assemble(array('action' => 'cancel',
            'forceSecure' => true, 'appendSession' => false));

        return $params;
    }

    /**
     * returns business parameters
     *
     * @return $params
     */
    public function getBusiness()
    {
        $params = [];

        $params['document_date'] = '';
        $params['booking_date'] = '';
        $params['due_time'] = '';

        return $params;
    }

    /**
     * collect all items
     *
     * @param array $basket
     * @param array $shipment
     * @param array $userData
     * @return array
     */
    public function getInvoicing($basket, $shipment, $userData)
    {
        return $this->getBasketItems($basket, $shipment, $userData);
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
            $params['it'] = PayoneEnums::InvoicingItemType_GOODS; //item type
            if ($article['modus'] == 2) {
                $params['it'] = PayoneEnums::InvoicingItemType_VOUCHER;
            }
            # paypal does not accept negative values for handling use voucher instead
            # this was an issue with articles added by the SwagAdvancedPromotionSuite plugin
            if ($article['modus'] == 4 && $params['pr'] >= "0") {
                $params['it'] = PayoneEnums::InvoicingItemType_HANDLING;
            }
            if ($article['modus'] == 4 && $params['pr'] < "0") {
                $params['it'] = PayoneEnums::InvoicingItemType_VOUCHER;
            }
            # Repertus Set Artikel Plus compatibility
            if ($article['modus'] == 12 && $params['pr'] == "0") {
                $params['it'] = PayoneEnums::InvoicingItemType_HANDLING;
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
            $params['it'] = PayoneEnums::InvoicingItemType_SHIPMENT;
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
     * @return array
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
        $items = array();
        $blTaxFree = $order->getTaxFree();
        $blNet = $order->getNet();
        // check here if netto is set and it corresponds with taxfree flag
        // if order is netto and taxfree is not set add taxes to all positions
        $blDebitBrutto = (!$blTaxFree && $blNet);

        foreach ($order->getDetails() as $position) {
            if (!$positionIds || !in_array($position->getId(), $positionIds)) {
                continue;
            }

            if (!$debit) {
                $positionAttribute = $this->payoneHelper->getOrCreateAttribute($position);
                if ($positionAttribute->getMoptPayoneCaptured()) {
                    continue;
                }
            }
            $flTaxRate = $position->getTaxRate();
            $params = [];
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
            $params['it'] = PayoneEnums::InvoicingItemType_GOODS;
            $mode = $position->getMode();
            if ($mode == 2) {
                $params['it'] = PayoneEnums::InvoicingItemType_VOUCHER;
                $params['id'] = substr($position->getArticleNumber(), 0, 100); //article number
            }

            # paypal does not accept negative values for handling use voucher instead
            # this was an issue with articles added by the SwagAdvancedPromotionSuite plugin
            if ($mode == 4 && $params['pr'] >= "0") {
                $params['it'] = PayoneEnums::InvoicingItemType_HANDLING;
                $params['id'] = substr($position->getArticleNumber(), 0, 100); //article number
            }
            if ($mode == 4 && $params['pr'] < "0") {
                $params['it'] = PayoneEnums::InvoicingItemType_VOUCHER;
                $params['id'] = substr($position->getArticleNumber(), 0, 100); //article number
            }

            if ($position->getArticleNumber() == 'SHIPPING') {
                $params['it'] = PayoneEnums::InvoicingItemType_SHIPMENT;
                $params['id'] = substr($position->getArticleName(), 0, 100); //article number
                //don't use $includeShipment if shipping article exists
                $includeShipment = false;
            }
            $params = array_map('htmlspecialchars_decode', $params);
            $items[] = $params;
        }

        if ($finalize !== 'skipCaptureMode') {
            $params['capturemode'] = $finalize ? PayoneEnums::CaptureMode_COMPLETED : PayoneEnums::CaptureMode_NOTCOMPLETED;
        }

        //add shipment costs as position
        if ($includeShipment) {
            //check if already caputered in non_debit/capture mode
            if (!$debit) {
                $orderAttribute = $this->payoneHelper->getOrCreateAttribute($order);
                if ($orderAttribute->getMoptPayoneShipCaptured()) {
                    return $params;
                }
            }

            $params = array();
            $params['pr'] = $order->getInvoiceShipping(); //price
            if ($debit) {
                $params['pr'] = $params['pr'] * -1;
            }
            $params['it'] = PayoneEnums::InvoicingItemType_SHIPMENT;
            $params['id'] = substr($order->getDispatch()->getName(), 0, 100); //article number
            $params['de'] = substr($order->getDispatch()->getName(), 0, 100); //article number
            $params['no'] = 1;
            $params['va'] = 0;
            if ($order->getInvoiceShipping() != 0) { // Tax rate calculation below would divide by zero otherwise
                $params['va'] = number_format($order->getInvoiceShipping() / $order->getInvoiceShippingNet() - 1,2,'.') * 100;
            }
            $params['va'] = $params['va'] * 100;

            $params = array_map('htmlspecialchars_decode', $params);
            $items[] = $params;
        }

        return $items;
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
    public function getCountryFromId($id)
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
        $params = array_merge($params, $this->getPaymentDebitNote($bankData), $this->getPersonalData($userData));

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
        $params['add_paydata[action]'] = PayoneEnums::PAYPAL_ECS_SET_EXPRESSCHECKOUT;
        $walletParams = $this->buildPayPalEcsWalletParams($router);
        $params['clearingtype'] = PayoneEnums::WALLET;
        $params['amount'] = $amount;
        $params['currency'] = $currencyName;
        return array_merge($params, $walletParams, $this->buildPayPalEcsShippingAddress($userData));
    }

    public function buildPayPalv2ExpressCheckout($paymentId, $router, $amount, $currencyName, $userData)
    {
        $this->payoneConfig = Mopt_PayoneMain::getInstance()->getPayoneConfig($paymentId);
        $walletParams = $this->buildPayPalEcsWalletParams($router);
        $params = $this->getAuthParameters($paymentId);
        $params['add_paydata[action]'] = PayoneEnums::PAYPAL_ECS_SET_EXPRESSCHECKOUT;
        $params['clearingtype'] = PayoneEnums::WALLET;
        $params['amount'] = $amount;
        $params['currency'] = $currencyName;
        return array_merge($params, $walletParams, $this->buildPayPalEcsShippingAddress($userData));
    }

    public function buildPayPalExpressCheckoutDetails($paymentId, $router, $amount, $currencyName, $userData, $workerId)
    {
        $this->payoneConfig = Mopt_PayoneMain::getInstance()->getPayoneConfig($paymentId);
        $params = $this->getAuthParameters($paymentId);
        $params['add_paydata[action]'] = PayoneEnums::PAYPAL_ECS_GET_EXPRESSCHECKOUTDETAILS;

        $walletParams = $this->buildPayPalEcsWalletParams($router);

        $params['clearingtype'] = PayoneEnums::WALLET;
        $params['workorderid'] = $workerId;
        $params['amount'] = $amount;
        $params['currency'] = $currencyName;
        return array_merge($params, $walletParams, $this->buildPayPalEcsShippingAddress($userData));
    }

    public function buildPayPalExpressv2CheckoutDetails($paymentId, $router, $amount, $currencyName, $userData, $workerId)
    {
        $this->payoneConfig = Mopt_PayoneMain::getInstance()->getPayoneConfig($paymentId);
        $params = $this->getAuthParameters($paymentId);
        $params['add_paydata[action]'] = PayoneEnums::PAYPAL_ECS_GET_EXPRESSCHECKOUTDETAILS;
        $walletParams = $this->buildPayPalv2EcsWalletParams($router);
        $params['clearingtype'] = PayoneEnums::WALLET;
        $params['workorderid'] = $workerId;
        $params['amount'] = $amount;
        $params['currency'] = $currencyName;

        return array_merge($params, $walletParams, $this->buildPayPalv2EcsShippingAddress($userData));
    }

    protected function buildPayPalEcsWalletParams($router)
    {
        $walletParams = array(
            'wallettype' => PayoneEnums::PAYPAL_EXPRESS,
            'successurl' => $this->payonePaymentHelper->assembleTokenizedUrl($router,array('action' => 'paypalexpress',
                'forceSecure' => true, 'appendSession' => false), null),
            'errorurl' => $router->assemble(array('action' => 'paypalexpressAbort',
                'forceSecure' => true, 'appendSession' => false)),
            'backurl' => $router->assemble(array('action' => 'paypalexpressAbort',
                'forceSecure' => true, 'appendSession' => false)),
        );

        return $walletParams;
    }

    protected function buildPayPalv2EcsWalletParams($router)
    {
        $walletParams = array(
            'wallettype' => PayoneEnums::PAYPAL_EXPRESSV2,
            'successurl' => $this->payonePaymentHelper->assembleTokenizedUrl($router,array('action' => 'paypalexpressv2',
                'forceSecure' => true, 'appendSession' => false), null),
            'errorurl' => $router->assemble(array('action' => 'paypalexpressv2Abort',
                'forceSecure' => true, 'appendSession' => false)),
            'backurl' => $router->assemble(array('action' => 'paypalexpressv2Abort',
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

    protected function buildPayPalv2EcsShippingAddress($userData)
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
        $params = [];

        $params['wallettype'] = PayoneEnums::PAYPAL_EXPRESS;
        $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router,array('action' => 'success',
            'forceSecure' => true, 'appendSession' => false), null);
        $params['errorurl'] = $router->assemble(array('action' => 'failure',
            'forceSecure' => true, 'appendSession' => false));
        $params['backurl'] = $router->assemble(array('action' => 'cancel',
            'forceSecure' => true, 'appendSession' => false));
        return $params;
    }

    public function getPaymentPaypalv2Ecs($router)
    {
        $params = [];

        $params['wallettype'] = PayoneEnums::PAYPAL_EXPRESSV2;
        $params['successurl'] = $this->payonePaymentHelper->assembleTokenizedUrl($router,array('action' => 'success',
            'forceSecure' => true, 'appendSession' => false), null);
        $params['errorurl'] = $router->assemble(array('action' => 'failure',
            'forceSecure' => true, 'appendSession' => false));
        $params['backurl'] = $router->assemble(array('action' => 'cancel',
            'forceSecure' => true, 'appendSession' => false));
        return $params;
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

    public function buildKlarnaSessionStartParams($clearingtype, $paymentFinancingtype, $basket, $shippingCosts, $paymentId, $phoneNumber)
    {
        $userData = Shopware()->Modules()->Admin()->sGetUserData();

        $params = $this->getAuthParameters($paymentId);
        $params['clearingtype'] = $clearingtype;
        $params['financingtype'] = $paymentFinancingtype;
        $params['amount'] = $basket['AmountNumeric'] + $shippingCosts['brutto'];
        $params['currency'] = Shopware()->Container()->get('currency')->getShortName();
        $params['telephonenumber'] = $phoneNumber;
        $params['title'] = $this->payonePaymentHelper->getKlarnaTitle($userData);
        $klarnaParams = $this->buildKlarnaPaydata($phoneNumber);
        $params = array_merge($params, $klarnaParams);
        $params['add_paydata[action]'] = PayoneEnums::GenericpaymentAction_KLARNA_START_SESSION;

        return $params;
    }

    /**
     * @param $phoneNumber
     *
     * @return array
     */
    protected function buildKlarnaPaydata($phoneNumber) {
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $params = [];

        $params['add_paydata[shipping_email]'] = $userData['additional']['user']['email'];
        $params['add_paydata[shipping_title]'] = $this->payonePaymentHelper->getKlarnaTitle($userData);
        $params['add_paydata[shipping_telephonenumber]'] = $phoneNumber;

        return $params;
    }

    /**
     *
     * @return array
     */
    protected function buildApplepayPaydata($tokenData) {
        $params = [];

        if (!isset($tokenData['paymentData']['data']) || is_null($tokenData['paymentData']['data'])) {
            $tokenData['paymentData']['data'] = '';
        }
        $params['add_paydata[paymentdata_token_data]'] = $tokenData['paymentData']['data'];

        if (!isset($tokenData['paymentData']['header']['ephemeralPublicKey']) || is_null($tokenData['paymentData']['header']['ephemeralPublicKey'])) {
            $tokenData['paymentData']['header']['ephemeralPublicKey'] = '';
        }
        $params['add_paydata[paymentdata_token_ephemeral_publickey]'] = $tokenData['paymentData']['header']['ephemeralPublicKey'];

        if (!isset($tokenData['paymentData']['header']['publicKeyHash']) || is_null($tokenData['paymentData']['header']['publicKeyHash'])) {
            $tokenData['paymentData']['header']['publicKeyHash'] = '';
        }
        $params['add_paydata[paymentdata_token_publickey_hash]'] = $tokenData['paymentData']['header']['publicKeyHash'];

        if (!isset($tokenData['paymentData']['signature']) || is_null($tokenData['paymentData']['signature'])) {
            $tokenData['paymentData']['signature'] = '';
        }
        $params['add_paydata[paymentdata_token_signature]'] = $tokenData['paymentData']['signature'];

        if (!isset($tokenData['paymentData']['header']['transactionId']) || is_null($tokenData['paymentData']['header']['transactionId'])) {
            $tokenData['paymentData']['header']['transactionId'] = '';
        }
        $params['add_paydata[paymentdata_token_transaction_id]'] = $tokenData['paymentData']['header']['transactionId'];

        if (!isset($tokenData['paymentData']['version']) || is_null($tokenData['paymentData']['version'])) {
            $tokenData['paymentData']['version'] = 'EC_v1';
        }
        $params['add_paydata[paymentdata_token_version]'] = $tokenData['paymentData']['version'];

        return $params;
    }

    /**
     *
     * @return $params
     */
    protected function buildGooglePayPaydata($token) {
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $params['add_paydata[paymentmethod_token_data]'] = $token;
        if ($userData['billingaddress']['company']) {
            $params['add_paydata[b2b]'] = 'yes';
        }
        return $params;
    }

    /**
     *
     * @return $params
     */
    protected function buildClick2PayPaydata($token, $type, $cardholderName, $cardNumber, $cardType, $cardExpiry) {
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        if ($type !== 'manual') {
            $params['add_paydata[paymentcheckout_data]'] = $token;
        }
        if ($userData['billingaddress']['company']) {
            $params['add_paydata[b2b]'] = 'yes';
        }
        if ($type === 'manual') {
            $params['pseudocardpan'] = $token;
            $params['cardholder'] = $cardholderName;
            // $params['cardexpiredate'] = $cardExpiry;
        }
        return $params;
    }
}
