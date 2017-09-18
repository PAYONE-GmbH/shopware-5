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
class Payone_Api_Request_Refund extends Payone_Api_Request_Abstract
{
    protected $request = Payone_Api_Enum_RequestType::REFUND;

    /**
     * @var string
     */
    protected $txid = null;
    /**
     * @var int
     */
    protected $sequencenumber = null;
    /**
     * @var int
     */
    protected $amount = null;
    /**
     * @var string
     */
    protected $currency = null;
    /**
     * @var string
     */
    protected $use_customerdata = null;
    /**
     * @var Payone_Api_Request_Parameter_Refund_PaymentMethod_BankAccount
     */
    protected $payment = null;
    /**
     * @var Payone_Api_Request_Parameter_Invoicing_Transaction
     */
    protected $invoicing = null;

    /**
     * @var string
     */
    protected $narrative_text = null;

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
     * @param \Payone_Api_Request_Parameter_Invoicing_Transaction $invoicing
     */
    public function setInvoicing(Payone_Api_Request_Parameter_Invoicing_Transaction $invoicing)
    {
        $this->invoicing = $invoicing;
    }

    /**
     * @return \Payone_Api_Request_Parameter_Invoicing_Transaction
     */
    public function getInvoicing()
    {
        return $this->invoicing;
    }

    /**
     * @param \Payone_Api_Request_Parameter_Refund_PaymentMethod_BankAccount $payment
     */
    public function setPayment(Payone_Api_Request_Parameter_Refund_PaymentMethod_BankAccount $payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return \Payone_Api_Request_Parameter_Refund_PaymentMethod_BankAccount
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param int $sequencenumber
     */
    public function setSequencenumber($sequencenumber)
    {
        $this->sequencenumber = $sequencenumber;
    }

    /**
     * @return int
     */
    public function getSequencenumber()
    {
        return $this->sequencenumber;
    }

    /**
     * @param string $txid
     */
    public function setTxid($txid)
    {
        $this->txid = $txid;
    }

    /**
     * @return string
     */
    public function getTxid()
    {
        return $this->txid;
    }

    /**
     * @param string $use_customerdata
     */
    public function setUseCustomerdata($use_customerdata)
    {
        $this->use_customerdata = $use_customerdata;
    }

    /**
     * @return string
     */
    public function getUseCustomerdata()
    {
        return $this->use_customerdata;
    }

    /**
     * @return null
     */
    public function getNarrativeText()
    {
        return $this->narrative_text;
    }

    /**
     * @param null $narrative_text
     */
    public function setNarrativeText($narrative_text)
    {
        $this->narrative_text = $narrative_text;
    }
}
