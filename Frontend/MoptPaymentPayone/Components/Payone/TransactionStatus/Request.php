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
 * @package         Payone_TransactionStatus
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @author          Matthias Walter <info@noovias.com>
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.noovias.com
 */

/**
 *
 * @category        Payone
 * @package         Payone_TransactionStatus
 * @subpackage      Request
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.noovias.com
 */
class Payone_TransactionStatus_Request extends Payone_TransactionStatus_Request_Abstract
{
    /**
     * @var string Payment portal key as MD5 value
     */
    protected $key = null;
    /**
     * @var string
     */
    protected $txaction = null;
    /**
     * @var string
     */
    protected $mode = null;
    /**
     * @var int Payment portal ID
     */
    protected $portalid = null;
    /**
     * @var int Account ID (subaccount ID)
     */
    protected $aid = null;
    /**     *
     * @var string
     */
    protected $clearingtype = null;
    /**
     * unix timestamp
     *
     * @var int
     */
    protected $txtime = null;
    /**
     * @var string ISO-4217
     */
    protected $currency = null;
    /**
     * @var int
     */
    protected $userid = null;
    /**
     * @var int
     */
    protected $customerid = null;
    /**
     * @var string
     */
    protected $param = null;

    // Parameter bei einer Statusmeldung eines Zahlungsvorgangs

    /**
     * @var int
     */
    protected $txid = null;
    /**
     * @var string
     */
    protected $reference = null;
    /**
     * @var string
     */
    protected $sequencenumber = null;
    /**
     * @var string
     */
    protected $receivable = null;
    /**
     * @var string
     */
    protected $balance = null;
    /**
     * @var string
     */
    protected $failedcause = null;

    // Zusätzliche Parameter Contract bei Statusmeldung eines Zahlungsvorgangs

    /**
     * @var int
     */
    protected $productid = null;
    /**
     * @var int
     */
    protected $accessid = null;

    // Zusätzliche Parameter Collect (txaction=reminder) bei Statusmeldung eines Zahlungsvorgangs

    /**
     * @var string
     */
    protected $reminderlevel = null;

    // Parameter Invoicing (txaction=invoice)

    /**
     * @var string
     */
    protected $invoiceid = null;
    /**
     * @var string
     */
    protected $invoice_grossamount = null;
    /**
     * @var string
     */
    protected $invoice_date = null;
    /**
     * @var string
     */
    protected $invoice_deliverydate = null;
    /**
     * @var string
     */
    protected $invoice_deliveryenddate = null;

    
    /**
     * @var string
     */
    protected $clearing_bankaccountholder = null;
    /**
     * @var string
     */
    protected $clearing_bankcountry = null;
    /**
     * @var string
     */
    protected $clearing_bankaccount = null;
    /**
     * @var string
     */
    protected $clearing_bankcode = null;
    /**
     * @var string
     */
    protected $clearing_bankiban = null;
    /**
     * @var string
     */
    protected $clearing_bankbic = null;
    /**
     * @var string
     */
    protected $clearing_bankcity = null;
    /**
     * @var string
     */
    protected $clearing_bankname = null;
    

    /** @var string */
    protected $clearing_legalnote = null;

    /**
     * (YYYYMMDD)
     * @var string
     */
    protected $clearing_duedate = null;

    /** @var string */
    protected $clearing_reference = null;

    /** @var string */
    protected $clearing_instructionnote = null;

    /**
     * @var string
     */
    protected $iban = null;
    /**
     * @var string
     */
    protected $bic = null;
    /**
     * @var string
     */
    protected $mandate_identification = null;
    /**
     * @var string
     */
    protected $creditor_identifier = null;
    /**
     * Format YYYYMMDD
     * @var int
     */
    protected $clearing_date = null;
    /**
     * @var float
     */
    protected $clearing_amount = null;


    /**
     * @param int $accessid
     */
    public function setAccessid($accessid)
    {
        $this->accessid = $accessid;
    }

    /**
     * @return int
     */
    public function getAccessid()
    {
        return $this->accessid;
    }

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
     * @param string $balance
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

    /**
     * @return string
     */
    public function getBalance()
    {
        return $this->balance;
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
     * @param int $customerid
     */
    public function setCustomerid($customerid)
    {
        $this->customerid = $customerid;
    }

    /**
     * @return int
     */
    public function getCustomerid()
    {
        return $this->customerid;
    }

    /**
     * @param string $failedcause
     */
    public function setFailedcause($failedcause)
    {
        $this->failedcause = $failedcause;
    }

    /**
     * @return string
     */
    public function getFailedcause()
    {
        return $this->failedcause;
    }

    /**
     * @param string $invoice_date
     */
    public function setInvoiceDate($invoice_date)
    {
        $this->invoice_date = $invoice_date;
    }

    /**
     * @return string
     */
    public function getInvoiceDate()
    {
        return $this->invoice_date;
    }

    /**
     * @param string $invoice_deliverydate
     */
    public function setInvoiceDeliverydate($invoice_deliverydate)
    {
        $this->invoice_deliverydate = $invoice_deliverydate;
    }

    /**
     * @return string
     */
    public function getInvoiceDeliverydate()
    {
        return $this->invoice_deliverydate;
    }

    /**
     * @param string $invoice_deliveryenddate
     */
    public function setInvoiceDeliveryenddate($invoice_deliveryenddate)
    {
        $this->invoice_deliveryenddate = $invoice_deliveryenddate;
    }

    /**
     * @return string
     */
    public function getInvoiceDeliveryenddate()
    {
        return $this->invoice_deliveryenddate;
    }

    /**
     * @param string $invoice_grossamount
     */
    public function setInvoiceGrossamount($invoice_grossamount)
    {
        $this->invoice_grossamount = $invoice_grossamount;
    }

    /**
     * @return string
     */
    public function getInvoiceGrossamount()
    {
        return $this->invoice_grossamount;
    }

    /**
     * @param string $invoiceid
     */
    public function setInvoiceid($invoiceid)
    {
        $this->invoiceid = $invoiceid;
    }

    /**
     * @return string
     */
    public function getInvoiceid()
    {
        return $this->invoiceid;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
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
     * @param int $portalid
     */
    public function setPortalid($portalid)
    {
        $this->portalid = $portalid;
    }

    /**
     * @return int
     */
    public function getPortalid()
    {
        return $this->portalid;
    }

    /**
     * @param int $productid
     */
    public function setProductid($productid)
    {
        $this->productid = $productid;
    }

    /**
     * @return int
     */
    public function getProductid()
    {
        return $this->productid;
    }

    /**
     * @param string $receivable
     */
    public function setReceivable($receivable)
    {
        $this->receivable = $receivable;
    }

    /**
     * @return string
     */
    public function getReceivable()
    {
        return $this->receivable;
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
     * @param string $reminderlevel
     */
    public function setReminderlevel($reminderlevel)
    {
        $this->reminderlevel = $reminderlevel;
    }

    /**
     * @return string
     */
    public function getReminderlevel()
    {
        return $this->reminderlevel;
    }

    /**
     * @param string $sequencenumber
     */
    public function setSequencenumber($sequencenumber)
    {
        $this->sequencenumber = $sequencenumber;
    }

    /**
     * @return string
     */
    public function getSequencenumber()
    {
        return $this->sequencenumber;
    }

    /**
     * @param string $txaction
     */
    public function setTxaction($txaction)
    {
        $this->txaction = $txaction;
    }

    /**
     * @return string
     */
    public function getTxaction()
    {
        return $this->txaction;
    }

    /**
     * @param int $txid
     */
    public function setTxid($txid)
    {
        $this->txid = $txid;
    }

    /**
     * @return int
     */
    public function getTxid()
    {
        return $this->txid;
    }

    /**
     * @param int $txtime
     */
    public function setTxtime($txtime)
    {
        $this->txtime = $txtime;
    }

    /**
     * @return int
     */
    public function getTxtime()
    {
        return $this->txtime;
    }

    /**
     * @param int $userid
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;
    }

    /**
     * @return int
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * @param string $clearing_bankaccount
     */
    public function setClearingBankaccount($clearing_bankaccount)
    {
        $this->clearing_bankaccount = $clearing_bankaccount;
    }

    /**
     * @return string
     */
    public function getClearingBankaccount()
    {
        return $this->clearing_bankaccount;
    }

    /**
     * @param string $clearing_bankaccountholder
     */
    public function setClearingBankaccountholder($clearing_bankaccountholder)
    {
        $this->clearing_bankaccountholder = $clearing_bankaccountholder;
    }

    /**
     * @return string
     */
    public function getClearingBankaccountholder()
    {
        return $this->clearing_bankaccountholder;
    }

    /**
     * @param string $clearing_bankbic
     */
    public function setClearingBankbic($clearing_bankbic)
    {
        $this->clearing_bankbic = $clearing_bankbic;
    }

    /**
     * @return string
     */
    public function getClearingBankbic()
    {
        return $this->clearing_bankbic;
    }

    /**
     * @param string $clearing_bankcity
     */
    public function setClearingBankcity($clearing_bankcity)
    {
        $this->clearing_bankcity = $clearing_bankcity;
    }

    /**
     * @return string
     */
    public function getClearingBankcity()
    {
        return $this->clearing_bankcity;
    }

    /**
     * @param string $clearing_bankcode
     */
    public function setClearingBankcode($clearing_bankcode)
    {
        $this->clearing_bankcode = $clearing_bankcode;
    }

    /**
     * @return string
     */
    public function getClearingBankcode()
    {
        return $this->clearing_bankcode;
    }

    /**
     * @param string $clearing_bankcountry
     */
    public function setClearingBankcountry($clearing_bankcountry)
    {
        $this->clearing_bankcountry = $clearing_bankcountry;
    }

    /**
     * @return string
     */
    public function getClearingBankcountry()
    {
        return $this->clearing_bankcountry;
    }

    /**
     * @param string $clearing_bankiban
     */
    public function setClearingBankiban($clearing_bankiban)
    {
        $this->clearing_bankiban = $clearing_bankiban;
    }

    /**
     * @return string
     */
    public function getClearingBankiban()
    {
        return $this->clearing_bankiban;
    }

    /**
     * @param string $clearing_bankname
     */
    public function setClearingBankname($clearing_bankname)
    {
        $this->clearing_bankname = $clearing_bankname;
    }

    /**
     * @return string
     */
    public function getClearingBankname()
    {
        return $this->clearing_bankname;
    }

    /**
     * @param string $clearing_duedate
     */
    public function setClearingDuedate($clearing_duedate)
    {
        $this->clearing_duedate = $clearing_duedate;
    }

    /**
     * @return string
     */
    public function getClearingDuedate()
    {
        return $this->clearing_duedate;
    }

    /**
     * @param string $clearing_instructionnote
     */
    public function setClearingInstructionnote($clearing_instructionnote)
    {
        $this->clearing_instructionnote = $clearing_instructionnote;
    }

    /**
     * @return string
     */
    public function getClearingInstructionnote()
    {
        return $this->clearing_instructionnote;
    }

    /**
     * @param string $clearing_legalnote
     */
    public function setClearingLegalnote($clearing_legalnote)
    {
        $this->clearing_legalnote = $clearing_legalnote;
    }

    /**
     * @return string
     */
    public function getClearingLegalnote()
    {
        return $this->clearing_legalnote;
    }

    /**
     * @param string $clearing_reference
     */
    public function setClearingReference($clearing_reference)
    {
        $this->clearing_reference = $clearing_reference;
    }

    /**
     * @return string
     */
    public function getClearingReference()
    {
        return $this->clearing_reference;
    }

    /**
     * @param string $iban
     */
    public function setIban($iban)
    {
        $this->iban = $iban;
    }

    /**
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * @param string $bic
     */
    public function setBic($bic)
    {
        $this->bic = $bic;
    }

    /**
     * @return string
     */
    public function getBic()
    {
        return $this->bic;
    }

    /**
     * @param string $mandateIdentification
     */
    public function setMandateIdentification($mandateIdentification)
    {
        $this->mandate_identification = $mandateIdentification;
    }

    /**
     * @return string
     */
    public function getMandateIdentification()
    {
        return $this->mandate_identification;
    }

    /**
     * @param string $creditorIdentifier
     */
    public function setCreditorIdentifier($creditorIdentifier)
    {
        $this->creditor_identifier = $creditorIdentifier;
    }

    /**
     * @return string
     */
    public function getCreditorIdentifier()
    {
        return $this->creditor_identifier;
    }

    /**
     * @param int $clearingDate
     */
    public function setClearingDate($clearingDate)
    {
        $this->clearing_date = $clearingDate;
    }

    /**
     * @return int
     */
    public function getClearingDate()
    {
        return $this->clearing_date;
    }

    /**
     * @param float $clearingAmount
     */
    public function setClearingAmount($clearingAmount)
    {
        $this->clearing_amount = $clearingAmount;
    }

    /**
     * @return float
     */
    public function getClearingAmount()
    {
        return $this->clearing_amount;
    }
}
