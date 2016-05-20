<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU General Public License (GPL 3)
 * that is bundled with this package in the file LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Payone to newer
 * versions in the future. If you wish to customize Payone for your
 * needs please refer to http://www.payone.de for more information.
 *
 * @category        Payone
 * @package         Payone_Api
 * @subpackage      Request
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @author          Matthias Walter <info@noovias.com>
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.noovias.com
 */

/**
 *
 * @category        Payone
 * @package         Payone_Api
 * @subpackage      Request
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.noovias.com
 */
abstract class Payone_Api_Request_Authorization_Abstract
    extends Payone_Api_Request_Abstract
{
    /**
     * Sub account ID
     *
     * @var int
     */
    protected $aid = NULL;
    /**
     * @var string
     */
    protected $clearingtype = NULL;
    /**
     * Merchant reference number for the payment process. (Permitted symbols: 0-9, a-z, A-Z, .,-,_,/)
     *
     * @var string
     */
    protected $reference = NULL;
    /**
     * Total amount (in smallest currency unit! e.g. cent)
     *
     * @var int
     */
    protected $amount = NULL;
    /**
     * Currency (ISO-4217)
     *
     * @var string
     */
    protected $currency = NULL;
    /**
     * Individual parameter
     *
     * @var string
     */
    protected $param = NULL;
    /**
     * dynamic text for debit and creditcard payments
     *
     * @var string
     */
    protected $narrative_text = NULL;

    /**
     * @var Payone_Api_Request_Parameter_Authorization_PersonalData
     */
    protected $personalData = null;
    /**
     * @var Payone_Api_Request_Parameter_Authorization_DeliveryData
     */
    protected $deliveryData = null;
    /**
     * @var Payone_Api_Request_Parameter_Authorization_PaymentMethod_Abstract
     */
    protected $payment = null;
    /**
     * @var Payone_Api_Request_Parameter_Authorization_3dsecure
     */
    protected $_3dsecure = null;

    /**
     * @var Payone_Api_Request_Parameter_Invoicing_Transaction
     */
    protected $invoicing = null;
    
    /**
     * Mandatory for PayPal Express Checkout
     * Alphanumeric max 16 chars
     * @var string 
     */
    protected $workorderid = null;
    
    
    protected $customer_is_present = 'yes';
    
    protected $recurrence = null;
    
    /**
     * @param int $aid
     */
    public function setAid($aid)
    {
        $this->aid = $aid;
    }

    /**
     * @return int
     */
    public function getAid()
    {
        return $this->aid;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param string $clearingtype
     */
    public function setClearingtype($clearingtype)
    {
        $this->clearingtype = $clearingtype;
    }

    /**
     * @return string
     */
    public function getClearingtype()
    {
        return $this->clearingtype;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $narrative_text
     */
    public function setNarrativeText($narrative_text)
    {
        $this->narrative_text = $narrative_text;
    }

    /**
     * @return string
     */
    public function getNarrativeText()
    {
        return $this->narrative_text;
    }

    /**
     * @param string $param
     */
    public function setParam($param)
    {
        $this->param = $param;
    }

    /**
     * @return string
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * @param string $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param Payone_Api_Request_Parameter_Authorization_PersonalData $personalData
     */
    public function setPersonalData(Payone_Api_Request_Parameter_Authorization_PersonalData $personalData)
    {
        $this->personalData = $personalData;
    }

    /**
     * @return Payone_Api_Request_Parameter_Authorization_PersonalData
     */
    public function getPersonalData()
    {
        return $this->personalData;
    }

    /**
     * @param Payone_Api_Request_Parameter_Authorization_DeliveryData $deliveryData
     */
    public function setDeliveryData(Payone_Api_Request_Parameter_Authorization_DeliveryData $deliveryData)
    {
        $this->deliveryData = $deliveryData;
    }

    /**
     * @return Payone_Api_Request_Parameter_Authorization_DeliveryData
     */
    public function getDeliveryData()
    {
        return $this->deliveryData;
    }

    /**
     * @param Payone_Api_Request_Parameter_Authorization_PaymentMethod_Abstract $payment
     */
    public function setPayment(Payone_Api_Request_Parameter_Authorization_PaymentMethod_Abstract $payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return Payone_Api_Request_Parameter_Authorization_PaymentMethod_Abstract
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param Payone_Api_Request_Parameter_Authorization_3dsecure $secure
     */
    public function set3dsecure(Payone_Api_Request_Parameter_Authorization_3dsecure $secure)
    {
        $this->_3dsecure = $secure;
    }

    /**
     * @return Payone_Api_Request_Parameter_Authorization_3dsecure
     */
    public function get3dsecure()
    {
        return $this->_3dsecure;
    }


    /**
     * @param Payone_Api_Request_Parameter_Invoicing_Transaction $invoicing
     */
    public function setInvoicing(Payone_Api_Request_Parameter_Invoicing_Transaction $invoicing)
    {
        $this->invoicing = $invoicing;
    }

    /**
     * @return Payone_Api_Request_Parameter_Invoicing_Transaction
     */
    public function getInvoicing()
    {
        return $this->invoicing;
    }
    
    /**
     * @return string
     */
    function getWorkorderId() {
        return $this->workorderid;
    }

    /**
     * @param string $workorderid
     */
    function setWorkorderId($workorderid) {
        $this->workorderid = $workorderid;
    }
    
    function getCustomerIsPresent() {
        return $this->customer_is_present;
    }

    function getRecurrence() {
        return $this->recurrence;
    }

    function setCustomerIsPresent($customer_is_present) {
        $this->customer_is_present = $customer_is_present;
    }

    function setRecurrence($recurrence) {
        $this->recurrence = $recurrence;
    }

}
