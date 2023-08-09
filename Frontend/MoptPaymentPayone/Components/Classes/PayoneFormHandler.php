<?php
/**
 * This class handles:
 * form submits for all PAYONE payment methods
 *
 *
 * PHP version 5
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
 * @package         Payone Payment Plugin for Shopware 5
 * @subpackage      Formhandler
 * @copyright       Copyright (c) 2016 <kontakt@fatchip.de> - www.fatchip.com
 * @author          Stefan Müller <stefan.mueller@fatchip.de>
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 3)
 * @link            http://www.fatchip.com
 */


class Mopt_PayoneFormHandler
{
    const PAYOLUTION_NO_IBANBIC_COUNTRIES = [
        'GB',
        'CH',
    ];

    private $session;

    /**
     * class constructor
     */
    public function __construct()
    {
        $this->session = Shopware()->Session();
    }

    /**
     * process payment form
     *
     * @param string $paymentId
     * @param array $formData
     * @param Mopt_PayonePaymentHelper $paymentHelper
     * @return array payment data
     */
    public function processPaymentForm($paymentId, $formData, $paymentHelper)
    {
        if ($paymentHelper->isPayoneCreditcard($paymentId)) {
            return $this->proccessCreditCard($formData);
        }

        if ($paymentHelper->isPayoneSofortuerberweisung($paymentId)) {
            return $this->proccessSofortueberweisung($formData);
        }

        if ($paymentHelper->isPayoneBancontact($paymentId)) {
            return $this->proccessBancontact($formData);
        }

        if ($paymentHelper->isPayoneGiropay($paymentId)) {
            return $this->proccessGiropay($formData);
        }

        if ($paymentHelper->isPayoneEPS($paymentId)) {
            return $this->proccessEps($formData);
        }

        if ($paymentHelper->isPayoneIDeal($paymentId)) {
            return $this->proccessIdeal($formData);
        }

        if ($paymentHelper->isPayoneDebitnote($paymentId)) {
            return $this->proccessDebitNote($formData);
        }

        if ($paymentHelper->isPayoneKlarna_old($paymentId)) {
            return $this->processKlarna_old($formData);
        }

        if ($paymentHelper->isPayoneKlarnaInstallments($paymentId)) {
            return $this->processKlarnaInstallments($formData);
        }

        if ($paymentHelper->isPayoneKlarnaInvoice($paymentId)) {
            return $this->processKlarnaInvoice($formData);
        }

        if ($paymentHelper->isPayoneKlarnaDirectDebit($paymentId)) {
            return $this->processKlarnaDirectDebit($formData);
        }

        if ($paymentHelper->isPayonePayolutionDebitNote($paymentId)) {
            return $this->proccessPayolutionDebitNote($formData);
        }

        if ($paymentHelper->isPayonePayolutionInvoice($paymentId)) {
            return $this->proccessPayolutionInvoice($formData);
        }
        if ($paymentHelper->isPayonePayolutionInstallment($paymentId)) {
            return $this->proccessPayolutionInstallment($formData);
        }
        if ($paymentHelper->isPayoneRatepayInvoice($paymentId)) {
            return $this->proccessRatepayInvoice($formData);
        }
        if ($paymentHelper->isPayoneRatepayInstallment($paymentId)) {
            return $this->proccessRatepayInstallment($formData);
        }
        if ($paymentHelper->isPayoneRatepayDirectDebit($paymentId)) {
            return $this->proccessRatepayDirectDebit($formData);
        }

        if ($paymentHelper->isPayoneSafeInvoice($paymentId)) {
            return $this->proccessPayoneSafeInvoice($formData);
        }

        if ($paymentHelper->isPayoneTrustly($paymentId)) {
            return $this->proccessPayoneTrustly($formData);
        }

        if ($paymentHelper->isPayoneSecuredInvoice($paymentId)) {
            return $this->proccessPayoneSecuredInvoice($formData);
        }

        if ($paymentHelper->isPayoneSecuredInstallments($paymentId)) {
            return $this->proccessPayoneSecuredInstallments($formData);
        }

        if ($paymentHelper->isPayoneSecuredDirectdebit($paymentId)) {
            return $this->proccessPayoneSecuredDirectdebit($formData);
        }

        if ($paymentHelper->isPayonePaymentMethod($paymentId)) {
            $this->setFormSubmittedFlag();

        }

        return array();
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function proccessSofortueberweisung($formData)
    {
        $paymentData = array();

        if (!$formData['mopt_payone__sofort_bankaccount']) {
            $paymentData['sErrorFlag']['mopt_payone__sofort_bankaccount'] = true;
        } else {
            $paymentData['formData']['mopt_payone__sofort_bankaccount'] = $formData['mopt_payone__sofort_bankaccount'];
        }

        if (!$formData['mopt_payone__sofort_bankcode']) {
            $paymentData['sErrorFlag']['mopt_payone__sofort_bankcode'] = true;
        } else {
            $paymentData['formData']['mopt_payone__sofort_bankcode'] = $formData['mopt_payone__sofort_bankcode'];
        }

        if (!$formData['mopt_payone__sofort_iban'] && !$formData['mopt_payone__debit_show_sofort_iban_bic']==="") {
            $paymentData['sErrorFlag']['mopt_payone__sofort_iban'] = true;
        } else {
            if ($formData['mopt_payone__sofort_iban'] && !$this->isValidIban($formData['mopt_payone__sofort_iban'])){
                $paymentData['sErrorFlag']['mopt_payone__sofort_iban'] = true;
            } else {
                $paymentData['formData']['mopt_payone__sofort_iban'] = $formData['mopt_payone__sofort_iban'];
            }
        }

        if (!$formData['mopt_payone__sofort_bic'] && !$formData['mopt_payone__debit_show_sofort_iban_bic']==="") {
            $paymentData['sErrorFlag']['mopt_payone__sofort_bic'] = true;
        } else {
            if ($formData['mopt_payone__sofort_bic'] && !$this->isValidBic($formData['mopt_payone__sofort_bic'])){
                $paymentData['sErrorFlag']['mopt_payone__sofort_bic'] = true;
            } else {
                $paymentData['formData']['mopt_payone__sofort_bic'] = $formData['mopt_payone__sofort_bic'];
            }
        }

        if ($paymentData['sErrorFlag']['mopt_payone__sofort_iban'] && $paymentData['sErrorFlag']['mopt_payone__sofort_bic'] && !$paymentData['sErrorFlag']['mopt_payone__sofort_bankaccount'] && !$paymentData['sErrorFlag']['mopt_payone__sofort_bankcode']
        ) {
            unset($paymentData['sErrorFlag']['mopt_payone__sofort_iban']);
            unset($paymentData['sErrorFlag']['mopt_payone__sofort_bic']);
        }

        if (!$paymentData['sErrorFlag']['mopt_payone__sofort_iban'] && !$paymentData['sErrorFlag']['mopt_payone__sofort_bic'] && $paymentData['sErrorFlag']['mopt_payone__sofort_bankaccount'] && $paymentData['sErrorFlag']['mopt_payone__sofort_bankcode']
        ) {
            unset($paymentData['sErrorFlag']['mopt_payone__sofort_bankaccount']);
            unset($paymentData['sErrorFlag']['mopt_payone__sofort_bankcode']);
        }

        if (!empty($paymentData['sErrorFlag']) && count($paymentData['sErrorFlag'])) {
            return $paymentData;
        }

        $paymentData['formData']['mopt_payone__onlinebanktransfertype'] = Payone_Api_Enum_OnlinebanktransferType::INSTANT_MONEY_TRANSFER;

        $paymentData['formData']['mopt_payone__sofort_bankcountry'] = $formData['mopt_payone__sofort_bankcountry'];

        $this->setFormSubmittedFlag();

        return $paymentData;
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function proccessBancontact($formData)
    {
        $paymentData = array();

        $paymentData['formData']['mopt_payone__bancontact_bankcountry'] = $formData['mopt_payone__bancontact_bankcountry'];

        if (!empty($paymentData['sErrorFlag']) && count($paymentData['sErrorFlag'])) {
            return $paymentData;
        }

        $paymentData['formData']['mopt_payone__onlinebanktransfertype'] = Payone_Api_Enum_OnlinebanktransferType::BANCONTACT;

        $this->setFormSubmittedFlag();

        return $paymentData;
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function proccessGiropay($formData)
    {
        $paymentData = array();

        if (!$formData['mopt_payone__giropay_iban'] || !$this->isValidIban($formData['mopt_payone__giropay_iban'])) {
            $paymentData['sErrorFlag']['mopt_payone__giropay_iban'] = true;
        } else {
            $paymentData['formData']['mopt_payone__giropay_iban'] = $formData['mopt_payone__giropay_iban'];
        }

        if (!$formData['mopt_payone__giropay_bic'] || !$this->isValidBic($formData['mopt_payone__giropay_bic']) ) {
            $paymentData['sErrorFlag']['mopt_payone__giropay_bic'] = true;
        } else {
            $paymentData['formData']['mopt_payone__giropay_bic'] = $formData['mopt_payone__giropay_bic'];
        }

        if (!empty($paymentData['sErrorFlag']) && count($paymentData['sErrorFlag'])) {
            return $paymentData;
        }

        $paymentData['formData']['mopt_payone__onlinebanktransfertype'] = Payone_Api_Enum_OnlinebanktransferType::GIROPAY;
        $paymentData['formData']['mopt_payone__giropay_bankcountry'] = 'DE';

        $this->setFormSubmittedFlag();

        return $paymentData;
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function proccessEps($formData)
    {
        $paymentData = array();

        if (!isset($formData['mopt_payone__eps_bankgrouptype']) || empty($formData['mopt_payone__eps_bankgrouptype'])) {
            $paymentData['sErrorFlag']['mopt_payone__eps_bankgrouptype'] = true;
        } else {
            $paymentData['formData']['mopt_payone__eps_bankgrouptype'] = $formData['mopt_payone__eps_bankgrouptype'];
            $paymentData['formData']['mopt_payone__onlinebanktransfertype'] = Payone_Api_Enum_OnlinebanktransferType::EPS_ONLINE_BANK_TRANSFER;
            $paymentData['formData']['mopt_payone__eps_bankcountry'] = 'AT';
        }

        $this->setFormSubmittedFlag();

        return $paymentData;
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function proccessIdeal($formData)
    {
        $paymentData = array();

        if ($formData['mopt_payone__ideal_bankgrouptype'] == 'not_choosen') {
            $paymentData['sErrorFlag']['mopt_payone__ideal_bankgrouptype'] = true;
        } else {
            $paymentData['formData']['mopt_payone__ideal_bankgrouptype'] = $formData['mopt_payone__ideal_bankgrouptype'];
            $paymentData['formData']['mopt_payone__onlinebanktransfertype'] = Payone_Api_Enum_OnlinebanktransferType::IDEAL;
            $paymentData['formData']['mopt_payone__ideal_bankcountry'] = 'NL';
        }

        $this->setFormSubmittedFlag();

        return $paymentData;
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function proccessDebitNote($formData)
    {
        $paymentData = array();

        if (!$formData['mopt_payone__debit_iban'] || !$this->isValidIban($formData['mopt_payone__debit_iban'])) {
            $paymentData['sErrorFlag']['mopt_payone__debit_iban'] = true;
        } else {
            $paymentData['formData']['mopt_payone__debit_iban'] = $formData['mopt_payone__debit_iban'];
        }

        if (!$formData['mopt_payone__debit_bic'] && $formData['mopt_payone__debit_showbic']=="1") {
            $paymentData['sErrorFlag']['mopt_payone__debit_bic'] = true;
        } else {
            if ($formData['mopt_payone__debit_bic'] && !$this->isValidBic($formData['mopt_payone__debit_bic'])){
                $paymentData['sErrorFlag']['mopt_payone__debit_bic'] = true;
            }else {
                $paymentData['formData']['mopt_payone__debit_bic'] = $formData['mopt_payone__debit_bic'];
            }
        }

        if (!$formData['mopt_payone__debit_bankaccount']) {
            $paymentData['sErrorFlag']['mopt_payone__debit_bankaccount'] = true;
        } else {
            $paymentData['formData']['mopt_payone__debit_bankaccount'] = $formData['mopt_payone__debit_bankaccount'];
        }

        if (!$formData['mopt_payone__debit_bankcode']) {
            $paymentData['sErrorFlag']['mopt_payone__debit_bankcode'] = true;
        } else {
            $paymentData['formData']['mopt_payone__debit_bankcode'] = $formData['mopt_payone__debit_bankcode'];
        }

        if (!$formData['mopt_payone__debit_bankaccountholder']) {
            $paymentData['sErrorFlag']['mopt_payone__debit_bankaccountholder'] = true;
        } else {
            $paymentData['formData']['mopt_payone__debit_bankaccountholder'] = $formData['mopt_payone__debit_bankaccountholder'];
        }

        if (!isset($formData['mopt_payone__debit_bankcountry']) || empty($formData['mopt_payone__debit_bankcountry'])) {
            $paymentData['sErrorFlag']['mopt_payone__debit_bankcountry'] = true;
        } else {
            $paymentData['formData']['mopt_payone__debit_bankcountry'] = $formData['mopt_payone__debit_bankcountry'];
        }

        if ($paymentData['sErrorFlag']['mopt_payone__debit_iban'] && ( $paymentData['sErrorFlag']['mopt_payone__debit_bic'] || $formData['mopt_payone__debit_showbic']=="" ) && !$paymentData['sErrorFlag']['mopt_payone__debit_bankaccount'] && !$paymentData['sErrorFlag']['mopt_payone__debit_bankcode']
        ) {
            unset($paymentData['sErrorFlag']['mopt_payone__debit_iban']);
            unset($paymentData['sErrorFlag']['mopt_payone__debit_bic']);
        }

        if (!$paymentData['sErrorFlag']['mopt_payone__debit_iban'] && !$paymentData['sErrorFlag']['mopt_payone__debit_bic'] && $paymentData['sErrorFlag']['mopt_payone__debit_bankaccount'] && $paymentData['sErrorFlag']['mopt_payone__debit_bankcode']
        ) {
            unset($paymentData['sErrorFlag']['mopt_payone__debit_bankaccount']);
            unset($paymentData['sErrorFlag']['mopt_payone__debit_bankcode']);
        }

        $this->setFormSubmittedFlag();

        return $paymentData;
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function proccessCreditCard($formData)
    {
        $paymentData = array();
        $paymentData['formData'] = $formData;
        $this->setFormSubmittedFlag();

        return $paymentData;
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function processKlarnaInstallments($formData)
    {
        return $this->processKlarnaGeneric($formData);
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function processKlarnaInvoice($formData)
    {
        return $this->processKlarnaGeneric($formData);
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function processKlarnaDirectDebit($formData)
    {
        return $this->processKlarnaGeneric($formData);
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function processKlarnaGeneric($formData)
    {
        $paymentData = array();

        // file_put_contents("/var/www/sw564/var/log/klarna_authorize.json", $formData['klarna-authorize']);

        // validation of fields was already done in klarna widget
        $paymentData['formData']['mopt_payone__klarna_telephone'] = $formData['mopt_payone__klarna_telephone'];
        $paymentData['formData']['mopt_payone__klarna_personalId'] = $formData['mopt_payone__klarna_personalId'];
        $paymentData['formData']['mopt_payone__klarna_birthyear'] = $formData['mopt_payone__klarna_birthyear'];
        $paymentData['formData']['mopt_payone__klarna_birthmonth'] = $formData['mopt_payone__klarna_birthmonth'];
        $paymentData['formData']['mopt_payone__klarna_birthday'] = $formData['mopt_payone__klarna_birthday'];
        $paymentData['formData']['mopt_save_birthday_and_phone'] = true;

        $this->setFormSubmittedFlag();

        return $paymentData;
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function processKlarna_old($formData)
    {
        $paymentData = array();

        if (!$formData['mopt_payone__klarna_old_telephone']) {
            $paymentData['sErrorFlag']['mopt_payone__klarna_telephone'] = true;
        } else {
            $paymentData['formData']['mopt_payone__klarna_telephone'] = $formData['mopt_payone__klarna_old_telephone'];
        }

        if (!$formData['mopt_payone__klarna_old_agreement'] || !in_array($formData['mopt_payone__klarna_old_agreement'], array('on', true))) {
            $paymentData['sErrorFlag']['mopt_payone__klarna_agreement'] = true;
        } else {
            $paymentData['formData']['mopt_payone__klarna_agreement'] = $formData['mopt_payone__klarna_old_agreement'];
        }

        if (!$formData['mopt_payone__klarna_old_birthyear']) {
            $paymentData['sErrorFlag']['mopt_payone__klarna_birthyear'] = true;
        } else {
            $paymentData['formData']['mopt_payone__klarna_birthyear'] = $formData['mopt_payone__klarna_old_birthyear'];
        }

        if (!$formData['mopt_payone__klarna_old_birthmonth']) {
            $paymentData['sErrorFlag']['mopt_payone__klarna_birthmonth'] = true;
        } else {
            $paymentData['formData']['mopt_payone__klarna_birthmonth'] = $formData['mopt_payone__klarna_old_birthmonth'];
        }

        if (!$formData['mopt_payone__klarna_old_birthday']) {
            $paymentData['sErrorFlag']['mopt_payone__klarna_birthday'] = true;
        } else {
            $paymentData['formData']['mopt_payone__klarna_birthday'] = $formData['mopt_payone__klarna_old_birthday'];
        }
        $paymentData['formData']['mopt_save_birthday_and_phone'] = true;

        if ($paymentData['sErrorFlag']['mopt_payone__klarna_telephone'] || $paymentData['sErrorFlag']['mopt_payone__klarna_birthyear'] || $paymentData['sErrorFlag']['mopt_payone__klarna_birthmonth'] || $paymentData['sErrorFlag']['mopt_payone__klarna_birthday']) {
            $paymentData['formData']['mopt_save_birthday_and_phone'] = false;
        }

        $this->setFormSubmittedFlag();

        return $paymentData;
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function proccessPayolutionDebitNote($formData)
    {

        $paymentData = array();

        if (!$formData['mopt_payone__payolution_debitnote_agreement'] || !in_array($formData['mopt_payone__payolution_debitnote_agreement'], array('on', true))) {
            $paymentData['sErrorFlag']['mopt_payone__payolution_debitnote_agreement'] = true;
        } else {
            $paymentData['formData']['mopt_payone__payolution_debitnote_agreement'] = $formData['mopt_payone__payolution_debitnote_agreement'];
        }
        if (!$formData['mopt_payone__payolution_debitnote_agreement2'] || !in_array($formData['mopt_payone__payolution_debitnote_agreement2'], array('on', true))) {
            $paymentData['sErrorFlag']['mopt_payone__payolution_debitnote_agreement2'] = true;
        } else {
            $paymentData['formData']['mopt_payone__payolution_debitnote_agreement2'] = $formData['mopt_payone__payolution_debitnote_agreement2'];
        }

        if ($formData['mopt_payone__payolution_birthdaydate'] !== "0000-00-00" && $formData['mopt_payone__payolution_b2bmode'] !== "1") {
            if (time() < strtotime('+18 years', strtotime($formData['mopt_payone__payolution_birthdaydate']))) {
                $paymentData['sErrorFlag']['mopt_payone__payolution_debitnote_birthday'] = true;
                $paymentData['sErrorFlag']['mopt_payone__payolution_debitnote_birthmonth'] = true;
                $paymentData['sErrorFlag']['mopt_payone__payolution_debitnote_birthyear'] = true;
            } else {
                $paymentData['formData']['mopt_payone__payolution_birthdaydate'] = $formData['mopt_payone__payolution_debitnote_birthdaydate'];
                $paymentData['formData']['mopt_save_birthday'] = true;
            }
        }

        if (!$formData['mopt_payone__payolution_debitnote_iban'] || !$this->isValidIban($formData['mopt_payone__payolution_debitnote_iban'])) {
            $paymentData['sErrorFlag']['mopt_payone__payolution_debitnote_iban'] = true;
        } else {
            $paymentData['formData']['mopt_payone__payolution_debitnote_iban'] = $formData['mopt_payone__payolution_debitnote_iban'];
        }

        if ($paymentData['sErrorFlag']['mopt_payone__payolution_debitnote_iban'] && $paymentData['sErrorFlag']['mopt_payone__payolution_debitnote_bic']) {
            unset($paymentData['sErrorFlag']['mopt_payone__debit_iban']);
            unset($paymentData['sErrorFlag']['mopt_payone__debit_bic']);
        }

        if ($formData['mopt_payone__payolution_debitnote_b2bmode'] === "1") {
            $paymentData['formData']['mopt_payone__company_trade_registry_number'] = $formData['mopt_payone__debitnote_company_trade_registry_number'];
            $paymentData['formData']['mopt_payone__payolution_b2bmode'] = $formData['mopt_payone__payolution_debitnote_b2bmode'];
        }

        // set sessionflag to trigger precheck
        $this->session->moptPayolutionPrecheck = "1";

        $this->setFormSubmittedFlag();

        return $paymentData;
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function proccessPayolutionInvoice($formData)
    {
        $paymentData = array();

        if (!$formData['mopt_payone__payolution_invoice_agreement'] || !in_array($formData['mopt_payone__payolution_invoice_agreement'], array('on', true))) {
            $paymentData['sErrorFlag']['mopt_payone__payolution_invoice_agreement'] = true;
        } else {
            $paymentData['formData']['mopt_payone__payolution_invoice_agreement'] = $formData['mopt_payone__payolution_invoice_agreement'];
        }

        if ($formData['mopt_payone__payolution_invoice_birthdaydate'] !== "0000-00-00" && $formData['mopt_payone__payolution_b2bmode'] !== "1") {
            if (time() < strtotime('+18 years', strtotime($formData['mopt_payone__payolution_invoice_birthdaydate']))) {
                $paymentData['sErrorFlag']['mopt_payone__payolution_invoice_birthday'] = true;
                $paymentData['sErrorFlag']['mopt_payone__payolution_invoice_birthmonth'] = true;
                $paymentData['sErrorFlag']['mopt_payone__payolution_invoice_birthyear'] = true;
                $paymentData['formData']['mopt_save_birthday'] = false;
            } else {
                $paymentData['formData']['mopt_payone__payolution_birthdaydate'] = $formData['mopt_payone__payolution_invoice_birthdaydate'];
                $paymentData['formData']['mopt_save_birthday'] = true;
            }
        }

        if ($formData['mopt_payone__payolution_invoice_b2bmode'] === "1") {
            $paymentData['formData']['mopt_payone__company_trade_registry_number'] = $formData['mopt_payone__invoice_company_trade_registry_number'];
            $paymentData['formData']['mopt_payone__payolution_b2bmode'] = $formData['mopt_payone__payolution_invoice_b2bmode'];
        }

        // set sessionflag to trigger precheck
        $this->session->moptPayolutionPrecheck = "1";

        $this->setFormSubmittedFlag();

        return $paymentData;
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function proccessPayolutionInstallment($formData)
    {
        $paymentData = array();

        if (!$formData['mopt_payone__payolution_installment_agreement'] || !in_array($formData['mopt_payone__payolution_installment_agreement'], array('on', true))) {
            $paymentData['sErrorFlag']['mopt_payone__payolution_installment_agreement'] = true;
        } else {
            $paymentData['formData']['mopt_payone__payolution_installment_agreement'] = $formData['mopt_payone__payolution_installment_agreement'];
        }

        if ($formData['mopt_payone__payolution_installment_birthdaydate'] !== "0000-00-00" && $formData['mopt_payone__payolution_b2bmode'] !== "1") {
            if (time() < strtotime('+18 years', strtotime($formData['mopt_payone__payolution_installment_birthdaydate']))) {
                $paymentData['sErrorFlag']['mopt_payone__payolution_installment_birthday'] = true;
                $paymentData['sErrorFlag']['mopt_payone__payolution_installment_birthmonth'] = true;
                $paymentData['sErrorFlag']['mopt_payone__payolution_installment_birthyear'] = true;
                $paymentData['formData']['mopt_save_birthday'] = false;
            } else {
                $paymentData['formData']['mopt_payone__payolution_birthdaydate'] = $formData['mopt_payone__payolution_installment_birthdaydate'];
                $paymentData['formData']['mopt_save_birthday'] = true;
            }
        }

        if ($formData['mopt_payone__payolution_installment_b2bmode'] === "1") {
            $paymentData['formData']['mopt_payone__company_trade_registry_number'] = $formData['mopt_payone__installment_company_trade_registry_number'];
            $paymentData['formData']['mopt_payone__payolution_b2bmode'] = $formData['mopt_payone__payolution_installment_b2bmode'];
        }

        if ($formData['mopt_payone__payolution_installment_duration'] ==="") {
            $paymentData['sErrorFlag']['mopt_payone__payolution_installment_duration'] = true;
        } else {
            $paymentData['formData']['mopt_payone__payolution_installment_duration'] = $formData['mopt_payone__payolution_installment_duration'];
        }

        if ($formData['mopt_payone__payolution_installment_workorderid'] ==="") {
            $paymentData['sErrorFlag']['mopt_payone__payolution_installment_workorderid'] = true;
        } else {
            $paymentData['formData']['mopt_payone__payolution_installment_workorderid'] = $formData['mopt_payone__payolution_installment_workorderid'];
            $this->session->moptPayolutionInstallmentWorkerId = $formData['mopt_payone__payolution_installment_workorderid'];
        }

        if (! in_array($this->getUserCountyIso(), self::PAYOLUTION_NO_IBANBIC_COUNTRIES)) {
            if (!$formData['mopt_payone__payolution_installment_iban'] || !$this->isValidIban($formData['mopt_payone__payolution_installment_iban'])) {
                $paymentData['sErrorFlag']['mopt_payone__payolution_installment_iban'] = true;
            } else {
                $paymentData['formData']['mopt_payone__payolution_installment_iban'] = $formData['mopt_payone__payolution_installment_iban'];
            }
        }

        $this->setFormSubmittedFlag();

        return $paymentData;
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function proccessRatepayInvoice($formData)
    {
        $paymentData = array();

        if ($formData['mopt_payone__ratepay_invoice_birthdaydate'] !== "0000-00-00" && $formData['mopt_payone__ratepay_b2bmode'] !== "1") {
            if (time() < strtotime('+18 years', strtotime($formData['mopt_payone__ratepay_invoice_birthdaydate']))) {
                $paymentData['sErrorFlag']['mopt_payone__ratepay_invoice_birthday'] = true;
                $paymentData['sErrorFlag']['mopt_payone__ratepay_invoice_birthmonth'] = true;
                $paymentData['sErrorFlag']['mopt_payone__ratepay_invoice_birthyear'] = true;
                $paymentData['formData']['mopt_save_birthday'] = false;
            } else {
                $paymentData['formData']['mopt_payone__ratepay_invoice_birthdaydate'] = $formData['mopt_payone__ratepay_invoice_birthdaydate'];
                $paymentData['formData']['mopt_save_birthday'] = true;
            }
        }

        if ($formData['mopt_payone__ratepay_b2bmode'] === "1") {
            if (!$formData['mopt_payone__ratepay_invoice_company_trade_registry_number']) {
                $paymentData['sErrorFlag']['mopt_payone__ratepay_invoice_company_trade_registry_number'] = true;
            } else {
                $paymentData['formData']['mopt_payone__ratepay_invoice_company_trade_registry_number'] = $formData['mopt_payone__ratepay_invoice_company_trade_registry_number'];
            }

            $paymentData['formData']['mopt_payone__ratepay_b2bmode'] = $formData['mopt_payone__ratepay_b2bmode'];
        }

        if (!$formData['mopt_payone__ratepay_invoice_telephone']) {
            $paymentData['sErrorFlag']['mopt_payone__ratepay_invoice_telephone'] = true;
        } else {
            $paymentData['formData']['mopt_payone__ratepay_invoice_telephone'] = $formData['mopt_payone__ratepay_invoice_telephone'];
            $paymentData['formData']['mopt_save_phone'] = true;
        }
        $paymentData['formData']['mopt_payone__ratepay_shopid'] = $formData['mopt_payone__ratepay_invoice_shopid'];
        $paymentData['formData']['mopt_payone__ratepay_invoice_device_fingerprint'] = $formData['mopt_payone__ratepay_invoice_device_fingerprint'];

        $this->setFormSubmittedFlag();
        Shopware()->Session()->offsetSet('moptRatepayCountry', $this->getUserCountyIso());

        return $paymentData;
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function proccessRatepayInstallment($formData)
    {

        $paymentData = array();

        if ($formData['mopt_payone__ratepay_installment_birthdaydate'] !== "0000-00-00" && $formData['mopt_payone__ratepay_b2bmode'] !== "1") {
            if (time() < strtotime('+18 years', strtotime($formData['mopt_payone__ratepay_installment_birthdaydate']))) {
                $paymentData['sErrorFlag']['mopt_payone__ratepay_installment_birthday'] = true;
                $paymentData['sErrorFlag']['mopt_payone__ratepay_installment_birthmonth'] = true;
                $paymentData['sErrorFlag']['mopt_payone__ratepay_installment_birthyear'] = true;
                $paymentData['formData']['mopt_save_birthday'] = false;
            } else {
                $paymentData['formData']['mopt_payone__ratepay_installment_birthdaydate'] = $formData['mopt_payone__ratepay_installment_birthdaydate'];
                $paymentData['formData']['mopt_save_birthday'] = true;
            }
        }

        if ($formData['mopt_payone__ratepay_b2bmode'] === "1") {
            if (!$formData['mopt_payone__ratepay_installment_company_trade_registry_number']) {
                $paymentData['sErrorFlag']['mopt_payone__ratepay_installment_company_trade_registry_number'] = true;
            } else {
                $paymentData['formData']['mopt_payone__ratepay_installment_company_trade_registry_number'] = $formData['mopt_payone__ratepay_installment_company_trade_registry_number'];
            }

            $paymentData['formData']['mopt_payone__ratepay_b2bmode'] = $formData['mopt_payone__ratepay_b2bmode'];
        }

        if (!$formData['mopt_payone__ratepay_installment_telephone']) {
            $paymentData['sErrorFlag']['mopt_payone__ratepay_installment_telephone'] = true;
        } else {
            $paymentData['formData']['mopt_payone__ratepay_installment_telephone'] = $formData['mopt_payone__ratepay_installment_telephone'];
            $paymentData['formData']['mopt_save_phone'] = true;
        }

        if ($formData['mopt_payone__ratepay_installment_iban'] && $this->isValidIban($formData['mopt_payone__ratepay_installment_iban'])) {
            $paymentData['formData']['mopt_payone__ratepay_installment_iban'] = str_replace(' ', '',$formData['mopt_payone__ratepay_installment_iban']);
        } elseif (!$formData['mopt_payone__ratepay_installment_iban']){
            $paymentData['formData']['mopt_payone__ratepay_installment_iban'] = str_replace(' ', '',$formData['mopt_payone__ratepay_installment_iban']);
        } else {
            $paymentData['sErrorFlag']['mopt_payone__ratepay_installment_iban'] = true;
        }

        if ($formData['mopt_payone__ratepay_installment_bic'] && $this->isValidBic($formData['mopt_payone__ratepay_installment_bic'])) {
            $paymentData['formData']['mopt_payone__ratepay_installment_bic'] = str_replace(' ', '',$formData['mopt_payone__ratepay_installment_bic']);
        } elseif (!$formData['mopt_payone__ratepay_installment_bic']) {
            $paymentData['formData']['mopt_payone__ratepay_installment_bic'] = str_replace(' ', '',$formData['mopt_payone__ratepay_installment_bic']);
        } else {
            $paymentData['sErrorFlag']['mopt_payone__ratepay_installment_bic'] = true;
        }

        if (!$formData['mopt_payone__ratepay_installment_number']) {
            $paymentData['sErrorFlag']['mopt_payone__ratepay_installment_number'] = true;
            $paymentData['sErrorFlag']['mopt_payone__ratepay_installment_amount'] = true;
        } else {
            $paymentData['formData']['mopt_payone__ratepay_installment_number'] = $formData['mopt_payone__ratepay_installment_number'];
        }


        $paymentData['formData']['mopt_payone__ratepay_shopid'] = $formData['mopt_payone__ratepay_installment_shopid'];
        $paymentData['formData']['mopt_payone__ratepay_installment_device_fingerprint'] = $formData['mopt_payone__ratepay_installment_device_fingerprint'];
        $paymentData['formData']['mopt_payone__ratepay_installment_installment_amount'] = $formData['mopt_payone__ratepay_installment_amount'];
        $paymentData['formData']['mopt_payone__ratepay_installment_total'] = $formData['mopt_payone__ratepay_installment_total'];
        $paymentData['formData']['mopt_payone__ratepay_installment_last_installment_amount'] = $formData['mopt_payone__ratepay_installment_last_installment_amount'];
        $paymentData['formData']['mopt_payone__ratepay_installment_interest_rate'] = $formData['mopt_payone__ratepay_installment_interest_rate'];

        $this->setFormSubmittedFlag();
        Shopware()->Session()->offsetSet('moptRatepayCountry', $this->getUserCountyIso());

        return $paymentData;
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function proccessRatepayDirectDebit($formData)
    {
        $paymentData = array();

        if ($formData['mopt_payone__ratepay_direct_debit_birthdaydate'] !== "0000-00-00" && $formData['mopt_payone__ratepay_b2bmode'] !== "1") {
            if (time() < strtotime('+18 years', strtotime($formData['mopt_payone__ratepay_direct_debit_birthdaydate']))) {
                $paymentData['sErrorFlag']['mopt_payone__ratepay_direct_debit_birthday'] = true;
                $paymentData['sErrorFlag']['mopt_payone__ratepay_direct_debit_birthmonth'] = true;
                $paymentData['sErrorFlag']['mopt_payone__ratepay_direct_debit_birthyear'] = true;
                $paymentData['formData']['mopt_save_birthday'] = false;
            } else {
                $paymentData['formData']['mopt_payone__ratepay_direct_debit_birthdaydate'] = $formData['mopt_payone__ratepay_direct_debit_birthdaydate'];
            }
        }

        if ($formData['mopt_payone__ratepay_b2bmode'] === "1") {
            if (!$formData['mopt_payone__ratepay_direct_debit_company_trade_registry_number']) {
                $paymentData['sErrorFlag']['mopt_payone__ratepay_direct_debit_company_trade_registry_number'] = true;
            } else {
                $paymentData['formData']['mopt_payone__ratepay_direct_debit_company_trade_registry_number'] = $formData['mopt_payone__ratepay_direct_debit_company_trade_registry_number'];
            }

            $paymentData['formData']['mopt_payone__ratepay_b2bmode'] = $formData['mopt_payone__ratepay_b2bmode'];
        }

        if (!$formData['mopt_payone__ratepay_direct_debit_telephone']) {
            $paymentData['sErrorFlag']['mopt_payone__ratepay_direct_debit_telephone'] = true;
        } else {
            $paymentData['formData']['mopt_payone__ratepay_direct_debit_telephone'] = $formData['mopt_payone__ratepay_direct_debit_telephone'];
            $paymentData['formData']['mopt_save_phone'] = true;
        }

        if (!empty($paymentData['formData']['mopt_payone__ratepay_direct_debit_birthdaydate']) && !empty($paymentData['formData']['mopt_payone__ratepay_direct_debit_telephone'])){
            $paymentData['formData']['mopt_save_birthday_and_phone'] = true;
        }

        if ($formData['mopt_payone__ratepay_direct_debit_iban'] && $this->isValidIban($formData['mopt_payone__ratepay_direct_debit_iban'])) {
            $paymentData['formData']['mopt_payone__ratepay_direct_debit_iban'] = str_replace(' ', '',$formData['mopt_payone__ratepay_direct_debit_iban']);
        } else {
            $paymentData['sErrorFlag']['mopt_payone__ratepay_direct_debit_iban'] = true;
        }


        if ($formData['mopt_payone__ratepay_direct_debit_bic'] && $this->isValidBic($formData['mopt_payone__ratepay_direct_debit_bic'])) {
            $paymentData['formData']['mopt_payone__ratepay_direct_debit_bic'] = str_replace(' ', '',$formData['mopt_payone__ratepay_direct_debit_bic']);
        } else {
            $paymentData['sErrorFlag']['mopt_payone__ratepay_direct_debit_bic'] = true;
        }

        $paymentData['formData']['mopt_payone__ratepay_shopid'] = $formData['mopt_payone__ratepay_direct_debit_shopid'];
        $paymentData['formData']['mopt_payone__ratepay_direct_debit_device_fingerprint'] = $formData['mopt_payone__ratepay_direct_debit_device_fingerprint'];

        $this->setFormSubmittedFlag();
        Shopware()->Session()->offsetSet('moptRatepayCountry', $this->getUserCountyIso());

        return $paymentData;
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function proccessPayoneSafeInvoice($formData)
    {
        $paymentData = array();

        if ($formData['mopt_payone__payone_safe_invoice_birthdaydate'] !== "0000-00-00" ) {
            if (time() < strtotime('+18 years', strtotime($formData['mopt_payone__payone_safe_invoice_birthdaydate']))) {
                $paymentData['sErrorFlag']['mopt_payone__payone_safe_invoice_birthday'] = true;
                $paymentData['sErrorFlag']['mopt_payone__payone_safe_invoice_birthmonth'] = true;
                $paymentData['sErrorFlag']['mopt_payone__payone_safe_invoice_birthyear'] = true;
                $paymentData['formData']['mopt_save_birthday'] = false;
            } else {
                $paymentData['formData']['mopt_payone__payone_safe_invoice_birthdaydate'] = $formData['mopt_payone__payone_safe_invoice_birthdaydate'];
                $paymentData['formData']['mopt_save_birthday'] = true;
            }
        }

        $this->setFormSubmittedFlag();
        return $paymentData;
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function proccessPayoneTrustly($formData)
    {
        $paymentData = array();

        if ($formData['mopt_payone__trustly_show_iban_bic'] == "1") {
            if (!$formData['mopt_payone__trustly_iban'] || !$this->isValidIban($formData['mopt_payone__trustly_iban'])) {
                $paymentData['sErrorFlag']['mopt_payone__trustly_iban'] = true;
            } else {
                $paymentData['formData']['mopt_payone__trustly_iban'] = $formData['mopt_payone__trustly_iban'];
            }

            if (!$formData['mopt_payone__trustly_bic'] || !$this->isValidBic($formData['mopt_payone__trustly_bic']) ) {
                $paymentData['sErrorFlag']['mopt_payone__trustly_bic'] = true;
            } else {
                $paymentData['formData']['mopt_payone__trustly_bic'] = $formData['mopt_payone__trustly_bic'];
            }

            if (!empty($paymentData['sErrorFlag']) && count($paymentData['sErrorFlag'])) {
                return $paymentData;
            }

        }

        $paymentData['formData']['mopt_payone__onlinebanktransfertype'] = Payone_Api_Enum_OnlinebanktransferType::TRUSTLY;
        $paymentData['formData']['mopt_payone__giropay_bankcountry'] = 'DE';

        $this->setFormSubmittedFlag();

        return $paymentData;
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function proccessPayoneSecuredInvoice($formData)
    {
        $paymentData = [];

        if ($formData['mopt_payone__payone_secured_invoice_birthdaydate'] !== "0000-00-00") {
            if (time() < strtotime('+18 years', strtotime($formData['mopt_payone__payone_secured_invoice_birthdaydate']))) {
                $paymentData['sErrorFlag']['mopt_payone__payone_secured_invoice_birthday'] = true;
                $paymentData['sErrorFlag']['mopt_payone__payone_secured_invoice_birthmonth'] = true;
                $paymentData['sErrorFlag']['mopt_payone__payone_secured_invoice_birthyear'] = true;
            } else {
                $paymentData['formData']['mopt_payone__payone_secured_invoice_birthdaydate'] = $formData['mopt_payone__payone_secured_invoice_birthdaydate'];
                $paymentData['formData']['mopt_save_birthday'] = true;
            }
        }

        if (empty($formData['mopt_payone__payone_secured_invoice_telephone'])) {
            $paymentData['sErrorFlag']['mopt_payone__payone_secured_invoice_telephone'] = true;
        } else {
            $paymentData['formData']['mopt_payone__payone_secured_invoice_telephone'] = $formData['mopt_payone__payone_secured_invoice_telephone'];
            $paymentData['formData']['mopt_save_birthday'] = true;
        }

        if ($formData['mopt_payone__payone_secured_invoice_token'] !== "") {
            Shopware()->Session()->moptPayoneSecuredToken =  $formData['mopt_payone__payone_secured_invoice_token'];
            $paymentData['formData']['mopt_payone__payone_secured_invoice_token'] = $formData['mopt_payone__payone_secured_invoice_token'];
        }

        $paymentData['formData']['mopt_payone__secured_invoice_vatid'] = $formData['mopt_payone__secured_invoice_vatid'];

        $this->setFormSubmittedFlag();

        Shopware()->Session()->moptPayment = $paymentData;
        return $paymentData;
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function proccessPayoneSecuredInstallments($formData)
    {
        $paymentData = [];

        if (!$formData['mopt_payone__payone_secured_installment_iban'] || !$this->isValidIban($formData['mopt_payone__payone_secured_installment_iban']) ) {
            $paymentData['sErrorFlag']['mopt_payone__payone_secured_installment_iban'] = true;
        } else {
            $paymentData['formData']['mopt_payone__payone_secured_installment_iban'] = $formData['mopt_payone__payone_secured_installment_iban'];
        }

        if ($formData['mopt_payone__payone_secured_installment_birthdaydate'] !== "0000-00-00" && $formData['mopt_payone__secured_installment_b2bmode'] !== "1") {
            if (time() < strtotime('+18 years', strtotime($formData['mopt_payone__payone_secured_installment_birthdaydate']))) {
                $paymentData['sErrorFlag']['mopt_payone__payone_secured_installment_birthday'] = true;
                $paymentData['sErrorFlag']['mopt_payone__payone_secured_installment_birthmonth'] = true;
                $paymentData['sErrorFlag']['mopt_payone__payone_secured_installment_birthyear'] = true;
            } else {
                $paymentData['formData']['mopt_payone__payone_secured_installment_birthdaydate'] = $formData['mopt_payone__payone_secured_installment_birthdaydate'];
                $paymentData['formData']['mopt_save_birthday'] = true;
            }
        }

        if (empty($formData['mopt_payone__payone_secured_installment_telephone'])) {
            $paymentData['sErrorFlag']['mopt_payone__payone_secured_installment_telephone'] = true;
        } else {
            $paymentData['formData']['mopt_payone__payone_secured_installment_telephone'] = $formData['mopt_payone__payone_secured_installment_telephone'];
            $paymentData['formData']['mopt_save_birthday'] = true;
        }

        if ($formData['mopt_payone__payone_secured_installment_token'] !== "") {
            Shopware()->Session()->moptPayoneSecuredToken =  $formData['mopt_payone__payone_secured_installment_token'];
            $paymentData['formData']['mopt_payone__payone_secured_installment_token'] = $formData['mopt_payone__payone_secured_installment_token'];
        }

        if (empty($formData['mopt_payone__payone_secured_installment_plan'])) {
            $paymentData['sErrorFlag']['mopt_payone__payone_secured_installment_plan'] = true;
        } else {
            $paymentData['formData']['mopt_payone__payone_secured_installment_plan'] = $formData['mopt_payone__payone_secured_installment_plan'];
        }

        $this->setFormSubmittedFlag();

        Shopware()->Session()->moptPayment = $paymentData;
        return $paymentData;
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function proccessPayoneSecuredDirectdebit($formData)
    {
        $paymentData = [];

        if (!$formData['mopt_payone__payone_secured_directdebit_iban'] || !$this->isValidIban($formData['mopt_payone__payone_secured_directdebit_iban']) ) {
            $paymentData['sErrorFlag']['mopt_payone__payone_secured_directdebit_iban'] = true;
        } else {
            $paymentData['formData']['mopt_payone__payone_secured_directdebit_iban'] = $formData['mopt_payone__payone_secured_directdebit_iban'];
        }

        if ($formData['mopt_payone__payone_secured_directdebit_birthdaydate'] !== "0000-00-00" && $formData['mopt_payone__secured_directdebit_b2bmode'] !== "1") {
            if (time() < strtotime('+18 years', strtotime($formData['mopt_payone__payone_secured_directdebit_birthdaydate']))) {
                $paymentData['sErrorFlag']['mopt_payone__payone_secured_directdebit_birthday'] = true;
                $paymentData['sErrorFlag']['mopt_payone__payone_secured_directdebit_birthmonth'] = true;
                $paymentData['sErrorFlag']['mopt_payone__payone_secured_directdebit_birthyear'] = true;
            } else {
                $paymentData['formData']['mopt_payone__payone_secured_directdebit_birthdaydate'] = $formData['mopt_payone__payone_secured_directdebit_birthdaydate'];
                $paymentData['formData']['mopt_save_birthday'] = true;
            }
        }

        if (empty($formData['mopt_payone__payone_secured_directdebit_telephone'])) {
            $paymentData['sErrorFlag']['mopt_payone__payone_secured_directdebit_telephone'] = true;
        } else {
            $paymentData['formData']['mopt_payone__payone_secured_directdebit_telephone'] = $formData['mopt_payone__payone_secured_directdebit_telephone'];
            $paymentData['formData']['mopt_save_birthday'] = true;
        }

        if ($formData['mopt_payone__payone_secured_directdebit_token'] !== "") {
            Shopware()->Session()->moptPayoneSecuredToken =  $formData['mopt_payone__payone_secured_directdebit_token'];
            $paymentData['formData']['mopt_payone__payone_secured_directdebit_token'] = $formData['mopt_payone__payone_secured_directdebit_token'];
        }

        $this->setFormSubmittedFlag();

        Shopware()->Session()->moptPayment = $paymentData;
        return $paymentData;
    }

    /**
     * validates IBAN fields
     *
     * @param string $iban
     * @return boolean
     */
    private function isValidIban($iban) {
        $iban = strtolower(str_replace(' ','',$iban));
        $Countries = [
            'al'=>28,'ad'=>24,'at'=>20,'az'=>28,'bh'=>22,'be'=>16,'ba'=>20,'br'=>29,'bg'=>22,'cr'=>21,'hr'=>21,'cy'=>28,'cz'=>24,
            'dk'=>18,'do'=>28,'ee'=>20,'fo'=>18,'fi'=>18,'fr'=>27,'ge'=>22,'de'=>22,'gi'=>23,'gr'=>27,'gl'=>18,'gt'=>28,'hu'=>28,
            'is'=>26,'ie'=>22,'il'=>23,'it'=>27,'jo'=>30,'kz'=>20,'kw'=>30,'lv'=>21,'lb'=>28,'li'=>21,'lt'=>20,'lu'=>20,'mk'=>19,
            'mt'=>31,'mr'=>27,'mu'=>30,'mc'=>27,'md'=>24,'me'=>22,'nl'=>18,'no'=>15,'pk'=>24,'ps'=>29,'pl'=>28,'pt'=>25,'qa'=>29,
            'ro'=>24,'sm'=>27,'sa'=>24,'rs'=>22,'sk'=>24,'si'=>19,'es'=>24,'se'=>24,'ch'=>21,'tn'=>24,'tr'=>26,'ae'=>23,'gb'=>22,'vg'=>24
        ];
        $Chars = [
            'a'=>10,'b'=>11,'c'=>12,'d'=>13,'e'=>14,'f'=>15,'g'=>16,'h'=>17,'i'=>18,'j'=>19,'k'=>20,'l'=>21,'m'=>22,
            'n'=>23,'o'=>24,'p'=>25,'q'=>26,'r'=>27,'s'=>28,'t'=>29,'u'=>30,'v'=>31,'w'=>32,'x'=>33,'y'=>34,'z'=>35
        ];

        if (strlen($iban) != $Countries[ substr($iban,0,2) ]) { return false; }

            $MovedChar = substr($iban, 4) . substr($iban,0,4);
            $MovedCharArray = str_split($MovedChar);
            $NewString = "";

            foreach ($MovedCharArray as $k => $v) {

                if ( !is_numeric($MovedCharArray[$k]) ) {
                    $MovedCharArray[$k] = $Chars[$MovedCharArray[$k]];
                }
                $NewString .= $MovedCharArray[$k];
            }

            // http://au2.php.net/manual/en/function.bcmod.php#38474
            $x = $NewString; $y = "97";
            $take = 5; $mod = "";

            do {
                $a = (int)$mod . substr($x, 0, $take);
                $x = substr($x, $take);
                $mod = $a % $y;
            }
            while (strlen($x));

            return (int)$mod == 1;
    }

    /**
     * validates BIC fields
     *
     * @param string $bic
     * @return boolean
     */
    private function isValidBic($bic) {

        if (!preg_match('/^[A-Z0-9 ]+$/',$bic)) {
            return false;
        } else {
            return true;
        }
    }


    /**
     * gets the country from the current user in iso format
     *
     * @return string
     */
    private function getUserCountyIso() {
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        return $userData['additional']['country']['countryiso'];
    }

    /**
     * sets the session flag moptFormsubmitted
     * this is used to prevent the redirectiion of the customer to shippingPayment
     *
     * @return void
     */
    private function setFormSubmittedFlag() {
        $this->session->offsetSet('moptFormSubmitted', true);
    }
}
