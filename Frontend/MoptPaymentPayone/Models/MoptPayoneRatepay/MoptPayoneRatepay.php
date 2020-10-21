<?php

/**
 * $Id: $
 */

namespace Shopware\CustomModels\MoptPayoneRatepay;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Repository")
 * @ORM\Table(name="s_plugin_mopt_payone_ratepay")
 */
class MoptPayoneRatepay extends ModelEntity
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

        /**
     * @var
     * @ORM\Column(name="shopid", type="integer", nullable=false)
     */
    protected $shopid;      

    
    /**
     * @var
     * @ORM\Column(name="merchant_name", type="string", nullable=true)
     */
    protected $merchantName;    
    
    /**
     * @var
     * @ORM\Column(name="merchant_status", type="integer", nullable=true)
     */
    protected $merchantStatus;   
    
    /**
     * @var
     * @ORM\Column(name="shop_name", type="string", nullable=true)
     */
    protected $shopName;   
    
    /**
     * @var
     * @ORM\Column(name="name", type="string", nullable=true)
     */
    protected $name; 
    
    /**
     * @var
     * @ORM\Column(name="currency_id", type="integer")
     */
    protected $currencyId;     
    
    
    /**
     * @var \Shopware\Models\Shop\Currency $currency
     * @ORM\ManyToOne(targetEntity="Shopware\Models\Shop\Currency")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     */
    protected $currency; 

    /**
     * @var
     * @ORM\Column(name="type", type="string", nullable=true)
     */
    protected $type; 

   /**
     * @var
     * @ORM\Column(name="activation_status_elv", type="integer", nullable=true)
     */
    protected $activationStatusElv; 
    
       /**
     * @var
     * @ORM\Column(name="activation_status_installment", type="integer", nullable=true)
     */
    protected $activationStatusInstallment; 

   /**
     * @var
     * @ORM\Column(name="activation_status_invoice", type="integer", nullable=true)
     */
    protected $activationStatusInvoice;     
    
   /**
     * @var
     * @ORM\Column(name="activation_status_prepayment", type="integer", nullable=true)
     */
    protected $activationStatusPrepayment;  

   /**
     * @var
     * @ORM\Column(name="amount_min_longrun", type="float", nullable=true)
     */
    protected $amountMinLongrun; 
    
   /**
     * @var
     * @ORM\Column(name="b2b_pq_full", type="boolean", nullable=true)
     */
    protected $b2bPqFull;    
    
   /**
     * @var
     * @ORM\Column(name="b2b_pq_light", type="boolean", nullable=true)
     */
    protected $b2bPqLight;     
    
   /**
     * @var
     * @ORM\Column(name="b2b_elv", type="boolean", nullable=true)
     */
    protected $b2bElv;   

   /**
     * @var
     * @ORM\Column(name="b2b_installment", type="boolean", nullable=true)
     */
    protected $b2bInstallment;       
    
   /**
     * @var
     * @ORM\Column(name="b2b_invoice", type="boolean", nullable=true)
     */
    protected $b2bInvoice;   

   /**
     * @var
     * @ORM\Column(name="b2b_prepayment", type="boolean", nullable=true)
     */
    protected $b2bPrepayment; 
    
    /**
     * @var
     * @ORM\Column(name="country_code_billing", type="string", nullable=true)
     */
    protected $countryCodeBilling;   

    /**
     * @var
     * @ORM\Column(name="country_code_delivery", type="string", nullable=true)
     */
    protected $countryCodeDelivery;   
    
   /**
     * @var
     * @ORM\Column(name="delivery_address_pq_full", type="boolean", nullable=true)
     */
    protected $deliveryAddressPqFull;    
    
   /**
     * @var
     * @ORM\Column(name="delivery_address_pq_light", type="boolean", nullable=true)
     */
    protected $deliveryAddressPqLight;     
    
   /**
     * @var
     * @ORM\Column(name="delivery_address_elv", type="boolean", nullable=true)
     */
    protected $deliveryAddressElv;   

   /**
     * @var
     * @ORM\Column(name="delivery_address_installment", type="boolean", nullable=true)
     */
    protected $deliveryAddressInstallment;       
    
   /**
     * @var
     * @ORM\Column(name="delivery_address_invoice", type="boolean", nullable=true)
     */
    protected $deliveryAddressInvoice;   

   /**
     * @var
     * @ORM\Column(name="delivery_address_prepayment", type="boolean", nullable=true)
     */
    protected $deliveryAddressPrepayment;     

    /**
     * @var
     * @ORM\Column(name="device_fingerprint_snippet_id", type="string", nullable=true)
     */
    protected $deviceFingerprintSnippetId; 
    
   /**
     * @var
     * @ORM\Column(name="eligibility_device_fingerprint", type="boolean", nullable=true)
     */
    protected $eligibilityDeviceFingerprint;    
    
   /**
     * @var
     * @ORM\Column(name="eligibility_ratepay_elv", type="boolean", nullable=true)
     */
    protected $eligibilityRatepayElv;     
    
   /**
     * @var
     * @ORM\Column(name="eligibility_ratepay_installment", type="boolean", nullable=true)
     */
    protected $eligibilityRatepayInstallment;   

   /**
     * @var
     * @ORM\Column(name="eligibility_ratepay_invoice", type="boolean", nullable=true)
     */
    protected $eligibilityRatepayInvoice;       
    
   /**
     * @var
     * @ORM\Column(name="eligibility_ratepay_pq_full", type="boolean", nullable=true)
     */
    protected $eligibilityRatepayPqFull;   

   /**
     * @var
     * @ORM\Column(name="eligibility_ratepay_pq_light", type="boolean", nullable=true)
     */
    protected $eligibilityRatepayPqLight; 
        
   /**
     * @var
     * @ORM\Column(name="eligibility_ratepay_prepayment", type="boolean", nullable=true)
     */
    protected $eligibilityRatepayPrepayment;     
    
   /**
     * @var
     * @ORM\Column(name="interest_rate_merchant_towards_bank", type="float", nullable=true)
     */
    protected $interestRateMerchantTowardsBank;     

   /**
     * @var
     * @ORM\Column(name="interestrate_default", type="float", nullable=true)
     */
    protected $interestrateDefault;       

   /**
     * @var
     * @ORM\Column(name="interestrate_max", type="float", nullable=true)
     */
    protected $interestrateMax;    

   /**
     * @var
     * @ORM\Column(name="interestrate_min", type="float", nullable=true)
     */
    protected $interestrateMin;    
    
   /**
     * @var
     * @ORM\Column(name="min_difference_dueday", type="integer", nullable=true)
     */
    protected $minDifferenceDueday;      
    
    /**
     * @var
     * @ORM\Column(name="month_allowed", type="string", nullable=true)
     */
    protected $monthAllowed;     
    
   /**
     * @var
     * @ORM\Column(name="month_longrun", type="integer", nullable=true)
     */
    protected $monthLongrun; 

   /**
     * @var
     * @ORM\Column(name="month_number_max", type="integer", nullable=true)
     */
    protected $monthNumberMax; 

   /**
     * @var
     * @ORM\Column(name="month_number_min", type="integer", nullable=true)
     */
    protected $monthNumberMin;     

   /**
     * @var
     * @ORM\Column(name="payment_amount", type="float", nullable=true)
     */
    protected $paymentAmount;      

   /**
     * @var
     * @ORM\Column(name="payment_firstday", type="integer", nullable=true)
     */
    protected $paymentFirstday;     
    
   /**
     * @var
     * @ORM\Column(name="payment_lastrate", type="float", nullable=true)
     */
    protected $paymentLastrate;      
    
   /**
     * @var
     * @ORM\Column(name="rate_min_longrun", type="float", nullable=true)
     */
    protected $rateMinLongrun; 
    
   /**
     * @var
     * @ORM\Column(name="rate_min_normal", type="float", nullable=true)
     */
    protected $rateMinNormal; 

   /**
     * @var
     * @ORM\Column(name="service_charge", type="float", nullable=true)
     */
    protected $serviceCharge; 

   /**
     * @var
     * @ORM\Column(name="tx_limit_elv_max", type="float", nullable=true)
     */
    protected $txLimitElvMax; 

   /**
     * @var
     * @ORM\Column(name="tx_limit_elv_min", type="float", nullable=true)
     */
    protected $txLimitElvMin; 

   /**
     * @var
     * @ORM\Column(name="tx_limit_installment_max", type="float", nullable=true)
     */
    protected $txLimitInstallmentMax; 

   /**
     * @var
     * @ORM\Column(name="tx_limit_installment_min", type="float", nullable=true)
     */
    protected $txLimitInstallmentMin; 

   /**
     * @var
     * @ORM\Column(name="tx_limit_invoice_max", type="float", nullable=true)
     */
    protected $txLimitInvoiceMax; 

   /**
     * @var
     * @ORM\Column(name="tx_limit_invoice_min", type="float", nullable=true)
     */
    protected $txLimitInvoiceMin; 

   /**
     * @var
     * @ORM\Column(name="tx_limit_prepayment_max", type="float", nullable=true)
     */
    protected $txLimitPrepaymentMax; 

   /**
     * @var
     * @ORM\Column(name="txLimitPrepaymentMin", type="float", nullable=true)
     */
    protected $txLimitPrepaymentMin;     
    
   /**
     * @var
     * @ORM\Column(name="valid_payment_firstdays", type="integer", nullable=true)
     */
    protected $validPaymentFirstdays;

    /**
     * @var
     * @ORM\Column(name="ratepay_installment_mode", type="boolean", nullable=false)
     */
    protected $ratepayInstallmentMode;


    public function getId() {
        return $this->id;
    }

    public function getShopid() {
        return $this->shopid;
    }
    
    public function getMerchantName() {
        return $this->merchantName;
    }

    public function getMerchantStatus() {
        return $this->merchantStatus;
    }

    public function getShopName() {
        return $this->shopName;
    }

    public function getName() {
        return $this->name;
    }

    public function getType() {
        return $this->type;
    }

    public function getActivationStatusElv() {
        return $this->activationStatusElv;
    }

    public function getActivationStatusInstallment() {
        return $this->activationStatusInstallment;
    }

    public function getActivationStatusInvoice() {
        return $this->activationStatusInvoice;
    }

    public function getActivationStatusPrepayment() {
        return $this->activationStatusPrepayment;
    }

    public function getAmountMinLongrun() {
        return $this->amountMinLongrun;
    }

    public function getB2bPqFull() {
        return $this->b2bPqFull;
    }

    public function getB2bPqLight() {
        return $this->b2bPqLight;
    }

    public function getB2bElv() {
        return $this->b2bElv;
    }

    public function getB2bInstallment() {
        return $this->b2bInstallment;
    }

    public function getB2bInvoice() {
        return $this->b2bInvoice;
    }

    public function getB2bPrepayment() {
        return $this->b2bPrepayment;
    }

    public function getCountryCodeBilling() {
        return $this->countryCodeBilling;
    }

    public function getCountryCodeDelivery() {
        return $this->countryCodeDelivery;
    }
    
    public function getCurrency() {
        return $this->currency;
    }

    public function getShippingCountry() {
        return $this->shippingCountry;
    }

    public function getMinBasket() {
        return $this->minBasket;
    }

    public function getMaxBasket() {
        return $this->maxBasket;
    }

    public function getDeliveryAddressPqFull() {
        return $this->deliveryAddressPqFull;
    }

    public function getDeliveryAddressPqLight() {
        return $this->deliveryAddressPqLight;
    }

    public function getDeliveryAddressElv() {
        return $this->deliveryAddressElv;
    }

    public function getDeliveryAddressInstallment() {
        return $this->deliveryAddressInstallment;
    }

    public function getDeliveryAddressInvoice() {
        return $this->deliveryAddressInvoice;
    }

    public function getDeliveryAddressPrepayment() {
        return $this->deliveryAddressPrepayment;
    }

    public function getDeviceFingerprintSnippetId() {
        return $this->deviceFingerprintSnippetId;
    }

    public function getEligibilityDeviceFingerprint() {
        return $this->eligibilityDeviceFingerprint;
    }

    public function getEligibilityRatepayElv() {
        return $this->eligibilityRatepayElv;
    }

    public function getEligibilityRatepayInstallment() {
        return $this->eligibilityRatepayInstallment;
    }

    public function getEligibilityRatepayInvoice() {
        return $this->eligibilityRatepayInvoice;
    }

    public function getEligibilityRatepayPqFull() {
        return $this->eligibilityRatepayPqFull;
    }

    public function getEligibilityRatepayPqLight() {
        return $this->eligibilityRatepayPqLight;
    }

    public function getEligibilityRatepayPrepayment() {
        return $this->eligibilityRatepayPrepayment;
    }

    public function getInterestRateMerchantTowardsBank() {
        return $this->interestRateMerchantTowardsBank;
    }

    public function getInterestrateDefault() {
        return $this->interestrateDefault;
    }

    public function getInterestrateMax() {
        return $this->interestrateMax;
    }

    public function getInterestrateMin() {
        return $this->interestrateMin;
    }

    public function getMinDifferenceDueday() {
        return $this->minDifferenceDueday;
    }

    public function getMonthAllowed() {
        return $this->monthAllowed;
    }

    public function getMonthLongrun() {
        return $this->monthLongrun;
    }

    public function getMonthNumberMax() {
        return $this->monthNumberMax;
    }

    public function getMonthNumberMin() {
        return $this->monthNumberMin;
    }

    public function getPaymentAmount() {
        return $this->paymentAmount;
    }

    public function getPaymentFirstday() {
        return $this->paymentFirstday;
    }

    public function getPaymentLastrate() {
        return $this->paymentLastrate;
    }

    public function getRateMinLongrun() {
        return $this->rateMinLongrun;
    }

    public function getRateMinNormal() {
        return $this->rateMinNormal;
    }

    public function getServiceCharge() {
        return $this->serviceCharge;
    }

    public function getTxLimitElvMax() {
        return $this->txLimitElvMax;
    }

    public function getTxLimitElvMin() {
        return $this->txLimitElvMin;
    }

    public function getTxLimitInstallmentMax() {
        return $this->txLimitInstallmentMax;
    }

    public function getTxLimitInstallmentMin() {
        return $this->txLimitInstallmentMin;
    }

    public function getTxLimitInvoiceMax() {
        return $this->txLimitInvoiceMax;
    }

    public function getTxLimitInvoiceMin() {
        return $this->txLimitInvoiceMin;
    }

    public function getTxLimitPrepaymentMax() {
        return $this->txLimitPrepaymentMax;
    }

    public function getTxLimitPrepaymentMin() {
        return $this->txLimitPrepaymentMin;
    }

    public function getValidPaymentFirstdays() {
        return $this->validPaymentFirstdays;
    }

    public function getRatepayInstallmentMode() {
        return $this->ratepayInstallmentMode;
    }
    
    public function setId($id) {
        $this->id = $id;
    }

    public function setShopid($shopid) {
        $this->shopid = $shopid;
    }
    
    public function setCurrency($currency) {
        $this->currency = $currency;
    }
    public function setCurrencId($currencyId) {
        $this->currencyId = $currencyId;
    }

    public function setShippingCountry($shippingCountry) {
        $this->shippingCountry = $shippingCountry;
    }

    public function setMinBasket($minBasket) {
        $this->minBasket = $minBasket;
    }

    public function setMaxBasket($maxBasket) {
        $this->maxBasket = $maxBasket;
    }

    public function setInvoiceCountry($invoiceCountry) {
        $this->invoiceCountry = $invoiceCountry;
    }    

    public function setMerchantName($merchantName) {
        $this->merchantName = $merchantName;
    }

    public function setMerchantStatus($merchantStatus) {
        $this->merchantStatus = $merchantStatus;
    }

    public function setShopName($shopName) {
        $this->shopName = $shopName;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function setActivationStatusElv($activationStatusElv) {
        $this->activationStatusElv = $activationStatusElv;
    }

    public function setActivationStatusInstallment($activationStatusInstallment) {
        $this->activationStatusInstallment = $activationStatusInstallment;
    }

    public function setActivationStatusInvoice($activationStatusInvoice) {
        $this->activationStatusInvoice = $activationStatusInvoice;
    }

    public function setActivationStatusPrepayment($activationStatusPrepayment) {
        $this->activationStatusPrepayment = $activationStatusPrepayment;
    }

    public function setAmountMinLongrun($amountMinLongrun) {
        $this->amountMinLongrun = $amountMinLongrun;
    }

    public function setB2bPqFull($b2bPqFull) {
        $this->b2bPqFull = $b2bPqFull;
    }

    public function setB2bPqLight($b2bPqLight) {
        $this->b2bPqLight = $b2bPqLight;
    }

    public function setB2bElv($b2bElv) {
        $this->b2bElv = $b2bElv;
    }

    public function setB2bInstallment($b2bInstallment) {
        $this->b2bInstallment = $b2bInstallment;
    }

    public function setB2bInvoice($b2bInvoice) {
        $this->b2bInvoice = $b2bInvoice;
    }

    public function setB2bPrepayment($b2bPrepayment) {
        $this->b2bPrepayment = $b2bPrepayment;
    }

    public function setCountryCodeBilling($countryCodeBilling) {
        $this->countryCodeBilling = $countryCodeBilling;
    }

    public function setCountryCodeDelivery($countryCodeDelivery) {
        $this->countryCodeDelivery = $countryCodeDelivery;
    }

    public function setDeliveryAddressPqFull($deliveryAddressPqFull) {
        $this->deliveryAddressPqFull = $deliveryAddressPqFull;
    }

    public function setDeliveryAddressPqLight($deliveryAddressPqLight) {
        $this->deliveryAddressPqLight = $deliveryAddressPqLight;
    }

    public function setDeliveryAddressElv($deliveryAddressElv) {
        $this->deliveryAddressElv = $deliveryAddressElv;
    }

    public function setDeliveryAddressInstallment($deliveryAddressInstallment) {
        $this->deliveryAddressInstallment = $deliveryAddressInstallment;
    }

    public function setDeliveryAddressInvoice($deliveryAddressInvoice) {
        $this->deliveryAddressInvoice = $deliveryAddressInvoice;
    }

    public function setDeliveryAddressPrepayment($deliveryAddressPrepayment) {
        $this->deliveryAddressPrepayment = $deliveryAddressPrepayment;
    }

    public function setDeviceFingerprintSnippetId($deviceFingerprintSnippetId) {
        $this->deviceFingerprintSnippetId = $deviceFingerprintSnippetId;
    }

    public function setEligibilityDeviceFingerprint($eligibilityDeviceFingerprint) {
        $this->eligibilityDeviceFingerprint = $eligibilityDeviceFingerprint;
    }

    public function setEligibilityRatepayElv($eligibilityRatepayElv) {
        $this->eligibilityRatepayElv = $eligibilityRatepayElv;
    }

    public function setEligibilityRatepayInstallment($eligibilityRatepayInstallment) {
        $this->eligibilityRatepayInstallment = $eligibilityRatepayInstallment;
    }

    public function setEligibilityRatepayInvoice($eligibilityRatepayInvoice) {
        $this->eligibilityRatepayInvoice = $eligibilityRatepayInvoice;
    }

    public function setEligibilityRatepayPqFull($eligibilityRatepayPqFull) {
        $this->eligibilityRatepayPqFull = $eligibilityRatepayPqFull;
    }

    public function setEligibilityRatepayPqLight($eligibilityRatepayPqLight) {
        $this->eligibilityRatepayPqLight = $eligibilityRatepayPqLight;
    }

    public function setEligibilityRatepayPrepayment($eligibilityRatepayPrepayment) {
        $this->eligibilityRatepayPrepayment = $eligibilityRatepayPrepayment;
    }

    public function setInterestRateMerchantTowardsBank($interestRateMerchantTowardsBank) {
        $this->interestRateMerchantTowardsBank = $interestRateMerchantTowardsBank;
    }

    public function setInterestrateDefault($interestrateDefault) {
        $this->interestrateDefault = $interestrateDefault;
    }

    public function setInterestrateMax($interestrateMax) {
        $this->interestrateMax = $interestrateMax;
    }

    public function setInterestrateMin($interestrateMin) {
        $this->interestrateMin = $interestrateMin;
    }

    public function setMinDifferenceDueday($minDifferenceDueday) {
        $this->minDifferenceDueday = $minDifferenceDueday;
    }

    public function setMonthAllowed($monthAllowed) {
        $this->monthAllowed = $monthAllowed;
    }

    public function setMonthLongrun($monthLongrun) {
        $this->monthLongrun = $monthLongrun;
    }

    public function setMonthNumberMax($monthNumberMax) {
        $this->monthNumberMax = $monthNumberMax;
    }

    public function setMonthNumberMin($monthNumberMin) {
        $this->monthNumberMin = $monthNumberMin;
    }

    public function setPaymentAmount($paymentAmount) {
        $this->paymentAmount = $paymentAmount;
    }

    public function setPaymentFirstday($paymentFirstday) {
        $this->paymentFirstday = $paymentFirstday;
    }

    public function setPaymentLastrate($paymentLastrate) {
        $this->paymentLastrate = $paymentLastrate;
    }

    public function setRateMinLongrun($rateMinLongrun) {
        $this->rateMinLongrun = $rateMinLongrun;
    }

    public function setRateMinNormal($rateMinNormal) {
        $this->rateMinNormal = $rateMinNormal;
    }

    public function setServiceCharge($serviceCharge) {
        $this->serviceCharge = $serviceCharge;
    }

    public function setTxLimitElvMax($txLimitElvMax) {
        $this->txLimitElvMax = $txLimitElvMax;
    }

    public function setTxLimitElvMin($txLimitElvMin) {
        $this->txLimitElvMin = $txLimitElvMin;
    }

    public function setTxLimitInstallmentMax($txLimitInstallmentMax) {
        $this->txLimitInstallmentMax = $txLimitInstallmentMax;
    }

    public function setTxLimitInstallmentMin($txLimitInstallmentMin) {
        $this->txLimitInstallmentMin = $txLimitInstallmentMin;
    }

    public function setTxLimitInvoiceMax($txLimitInvoiceMax) {
        $this->txLimitInvoiceMax = $txLimitInvoiceMax;
    }

    public function setTxLimitInvoiceMin($txLimitInvoiceMin) {
        $this->txLimitInvoiceMin = $txLimitInvoiceMin;
    }

    public function setTxLimitPrepaymentMax($txLimitPrepaymentMax) {
        $this->txLimitPrepaymentMax = $txLimitPrepaymentMax;
    }

    public function setTxLimitPrepaymentMin($txLimitPrepaymentMin) {
        $this->txLimitPrepaymentMin = $txLimitPrepaymentMin;
    }

    public function setValidPaymentFirstdays($validPaymentFirstdays) {
        $this->validPaymentFirstdays = $validPaymentFirstdays;
    }

    public function setRatepayInstallmentMode($ratepayInstallmentMode) {
        $this->ratepayInstallmentMode = $ratepayInstallmentMode;
    }
}
