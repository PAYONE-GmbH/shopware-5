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

        if ($paymentHelper->isPayoneKlarna($paymentId)) {
            return $this->proccessKlarna($formData);
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

        if ($paymentHelper->isPayonePaymentMethod($paymentId)) {
            // set SessionFlag, so we can redirect customer to shippingPayment in case the same paymentmean was used before
            $session = Shopware()->Session();
            $session->offsetSet('moptFormSubmitted', true);
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
            if ($formData['mopt_payone__sofort_iban'] && !$this->isValidIbanBic($formData['mopt_payone__sofort_iban'])){
                $paymentData['sErrorFlag']['mopt_payone__sofort_iban'] = true;
            } else {
                $paymentData['formData']['mopt_payone__sofort_iban'] = $formData['mopt_payone__sofort_iban'];
            }
        }

        if (!$formData['mopt_payone__sofort_bic'] && !$formData['mopt_payone__debit_show_sofort_iban_bic']==="") {
            $paymentData['sErrorFlag']['mopt_payone__sofort_bic'] = true;
        } else {
            if ($formData['mopt_payone__sofort_bic'] && !$this->isValidIbanBic($formData['mopt_payone__sofort_bic'])){
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

        if (count($paymentData['sErrorFlag'])) {
            return $paymentData;
        }

        $paymentData['formData']['mopt_payone__onlinebanktransfertype'] = Payone_Api_Enum_OnlinebanktransferType::INSTANT_MONEY_TRANSFER;

        $paymentData['formData']['mopt_payone__sofort_bankcountry'] = $formData['mopt_payone__sofort_bankcountry'];

        // set SessionFlag, so we can redirect customer to shippingPayment in case the same paymentmean was used before
        $session = Shopware()->Session();
        $session->offsetSet('moptFormSubmitted', true);

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

        if (count($paymentData['sErrorFlag'])) {
            return $paymentData;
        }

        $paymentData['formData']['mopt_payone__onlinebanktransfertype'] = Payone_Api_Enum_OnlinebanktransferType::BANCONTACT;

        // set SessionFlag, so we can redirect customer to shippingPayment in case the same paymentmean was used before
        $session = Shopware()->Session();
        $session->offsetSet('moptFormSubmitted', true);

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

        if (!$formData['mopt_payone__giropay_iban'] || !$this->isValidIbanBic($formData['mopt_payone__giropay_iban'])) {
            $paymentData['sErrorFlag']['mopt_payone__giropay_iban'] = true;
        } else {
            $paymentData['formData']['mopt_payone__giropay_iban'] = $formData['mopt_payone__giropay_iban'];
        }

        if (!$formData['mopt_payone__giropay_bic'] || !$this->isValidIbanBic($formData['mopt_payone__giropay_bic']) ) {
            $paymentData['sErrorFlag']['mopt_payone__giropay_bic'] = true;
        } else {
            $paymentData['formData']['mopt_payone__giropay_bic'] = $formData['mopt_payone__giropay_bic'];
        }

        if (count($paymentData['sErrorFlag'])) {
            return $paymentData;
        }

        $paymentData['formData']['mopt_payone__onlinebanktransfertype'] = Payone_Api_Enum_OnlinebanktransferType::GIROPAY;
        $paymentData['formData']['mopt_payone__giropay_bankcountry'] = 'DE';

        // set SessionFlag, so we can redirect customer to shippingPayment in case the same paymentmean was used before
        $session = Shopware()->Session();
        $session->offsetSet('moptFormSubmitted', true);

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

        // set SessionFlag, so we can redirect customer to shippingPayment in case the same paymentmean was used before
        $session = Shopware()->Session();
        $session->offsetSet('moptFormSubmitted', true);

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

        // set SessionFlag, so we can redirect customer to shippingPayment in case the same paymentmean was used before
        $session = Shopware()->Session();
        $session->offsetSet('moptFormSubmitted', true);

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

        if (!$formData['mopt_payone__debit_iban'] || !$this->isValidIbanBic($formData['mopt_payone__debit_iban'])) {
            $paymentData['sErrorFlag']['mopt_payone__debit_iban'] = true;
        } else {
            $paymentData['formData']['mopt_payone__debit_iban'] = $formData['mopt_payone__debit_iban'];
        }

        if (!$formData['mopt_payone__debit_bic'] && $formData['mopt_payone__debit_showbic']=="1") {
            $paymentData['sErrorFlag']['mopt_payone__debit_bic'] = true;
        } else {
            if ($formData['mopt_payone__debit_bic'] && !$this->isValidIbanBic($formData['mopt_payone__debit_bic'])){
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

        // set SessionFlag, so we can redirect customer to shippingPayment in case the same paymentmean was used before
        $session = Shopware()->Session();
        $session->offsetSet('moptFormSubmitted', true);

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

        // set SessionFlag, so we can redirect customer to shippingPayment in case the same paymentmean was used before
        $session = Shopware()->Session();
        $session->offsetSet('moptFormSubmitted', true);

        return $paymentData;
    }

    /**
     * process form data
     *
     * @param array $formData
     * @return array
     */
    protected function proccessKlarna($formData)
    {
        $paymentData = array();

        if (!$formData['mopt_payone__klarna_telephone']) {
            $paymentData['sErrorFlag']['mopt_payone__klarna_telephone'] = true;
        } else {
            $paymentData['formData']['mopt_payone__klarna_telephone'] = $formData['mopt_payone__klarna_telephone'];
        }

        if (!$formData['mopt_payone__klarna_agreement'] || !in_array($formData['mopt_payone__klarna_agreement'], array('on', true))) {
            $paymentData['sErrorFlag']['mopt_payone__klarna_agreement'] = true;
        } else {
            $paymentData['formData']['mopt_payone__klarna_agreement'] = $formData['mopt_payone__klarna_agreement'];
        }

        if (!$formData['mopt_payone__klarna_birthyear']) {
            $paymentData['sErrorFlag']['mopt_payone__klarna_birthyear'] = true;
        } else {
            $paymentData['formData']['mopt_payone__klarna_birthyear'] = $formData['mopt_payone__klarna_birthyear'];
        }

        if (!$formData['mopt_payone__klarna_birthmonth']) {
            $paymentData['sErrorFlag']['mopt_payone__klarna_birthmonth'] = true;
        } else {
            $paymentData['formData']['mopt_payone__klarna_birthmonth'] = $formData['mopt_payone__klarna_birthmonth'];
        }

        if (!$formData['mopt_payone__klarna_birthday']) {
            $paymentData['sErrorFlag']['mopt_payone__klarna_birthday'] = true;
        } else {
            $paymentData['formData']['mopt_payone__klarna_birthday'] = $formData['mopt_payone__klarna_birthday'];
        }
        $paymentData['formData']['mopt_save_birthday_and_phone'] = true;

        if ($paymentData['sErrorFlag']['mopt_payone__klarna_telephone'] || $paymentData['sErrorFlag']['mopt_payone__klarna_birthyear'] || $paymentData['sErrorFlag']['mopt_payone__klarna_birthmonth'] || $paymentData['sErrorFlag']['mopt_payone__klarna_birthday']) {
            $paymentData['formData']['mopt_save_birthday_and_phone'] = false;
        }

        // set SessionFlag, so we can redirect customer to shippingPayment in case the same paymentmean was used before
        $session = Shopware()->Session();
        $session->offsetSet('moptFormSubmitted', true);

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

        if (!$formData['mopt_payone__payolution_debitnote_iban'] || !$this->isValidIbanBic($formData['mopt_payone__payolution_debitnote_iban'])) {
            $paymentData['sErrorFlag']['mopt_payone__payolution_debitnote_iban'] = true;
        } else {
            $paymentData['formData']['mopt_payone__payolution_debitnote_iban'] = $formData['mopt_payone__payolution_debitnote_iban'];
        }

        if (!$formData['mopt_payone__payolution_debitnote_bic'] || !$this->isValidIbanBic($formData['mopt_payone__payolution_debitnote_bic'])) {
            $paymentData['sErrorFlag']['mopt_payone__payolution_debitnote_bic'] = true;
        } else {
            $paymentData['formData']['mopt_payone__payolution_debitnote_bic'] = $formData['mopt_payone__payolution_debitnote_bic'];
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
        Shopware()->Session()->moptPayolutionPrecheck = "1";

        // set SessionFlag, so we can redirect customer to shippingPayment in case the same paymentmean was used before
        $session = Shopware()->Session();
        $session->offsetSet('moptFormSubmitted', true);

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
        Shopware()->Session()->moptPayolutionPrecheck = "1";

        // set SessionFlag, so we can redirect customer to shippingPayment in case the same paymentmean was used before
        $session = Shopware()->Session();
        $session->offsetSet('moptFormSubmitted', true);

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
        }

        if (!$formData['mopt_payone__payolution_installment_iban'] || !$this->isValidIbanBic($formData['mopt_payone__payolution_installment_iban'])) {
            $paymentData['sErrorFlag']['mopt_payone__payolution_installment_iban'] = true;
        } else {
            $paymentData['formData']['mopt_payone__payolution_installment_iban'] = $formData['mopt_payone__payolution_installment_iban'];
        }

        if (!$formData['mopt_payone__payolution_installment_bic'] || !$this->isValidIbanBic($formData['mopt_payone__payolution_installment_bic'])) {
            $paymentData['sErrorFlag']['mopt_payone__payolution_installment_bic'] = true;
        } else {
            $paymentData['formData']['mopt_payone__payolution_installment_bic'] = $formData['mopt_payone__payolution_installment_bic'];
        }

        $paymentData['formData']['mopt_payone__payolution_installment_shippingcosts'] = $formData['mopt_payone__payolution_installment_shippingcosts'];

        // set SessionFlag, so we can redirect customer to shippingPayment in case the same paymentmean was used before
        $session = Shopware()->Session();
        $session->offsetSet('moptFormSubmitted', true);

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

        // set SessionFlag, so we can redirect customer to shippingPayment in case the same paymentmean was used before
        $session = Shopware()->Session();
        $session->offsetSet('moptFormSubmitted', true);

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

        if ($formData['mopt_payone__ratepay_installment_iban'] && $this->isValidIbanBic($formData['mopt_payone__ratepay_installment_iban'])) {
            $paymentData['formData']['mopt_payone__ratepay_installment_iban'] = $formData['mopt_payone__ratepay_installment_iban'];
        } elseif (!$formData['mopt_payone__ratepay_installment_iban']){
            $paymentData['formData']['mopt_payone__ratepay_installment_iban'] = $formData['mopt_payone__ratepay_installment_iban'];
        } else {
            $paymentData['sErrorFlag']['mopt_payone__ratepay_installment_iban'] = true;
        }

        if ($formData['mopt_payone__ratepay_installment_bic'] && $this->isValidIbanBic($formData['mopt_payone__ratepay_installment_bic'])) {
            $paymentData['formData']['mopt_payone__ratepay_installment_bic'] = $formData['mopt_payone__ratepay_installment_bic'];
        } elseif (!$formData['mopt_payone__ratepay_installment_bic']) {
            $paymentData['formData']['mopt_payone__ratepay_installment_bic'] = $formData['mopt_payone__ratepay_installment_bic'];
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

        // set SessionFlag, so we can redirect customer to shippingPayment in case the same paymentmean was used before
        $session = Shopware()->Session();
        $session->offsetSet('moptFormSubmitted', true);

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

        if ($formData['mopt_payone__ratepay_direct_debit_iban'] && $this->isValidIbanBic($formData['mopt_payone__ratepay_direct_debit_iban'])) {
            $paymentData['formData']['mopt_payone__ratepay_direct_debit_iban'] = $formData['mopt_payone__ratepay_direct_debit_iban'];
        } else {
            $paymentData['sErrorFlag']['mopt_payone__ratepay_direct_debit_iban'] = true;
        }


        if ($formData['mopt_payone__ratepay_direct_debit_bic'] && $this->isValidIbanBic($formData['mopt_payone__ratepay_direct_debit_bic'])) {
            $paymentData['formData']['mopt_payone__ratepay_direct_debit_bic'] = $formData['mopt_payone__ratepay_direct_debit_bic'];
        } else {
            $paymentData['sErrorFlag']['mopt_payone__ratepay_direct_debit_bic'] = true;
        }

        $paymentData['formData']['mopt_payone__ratepay_shopid'] = $formData['mopt_payone__ratepay_direct_debit_shopid'];
        $paymentData['formData']['mopt_payone__ratepay_direct_debit_device_fingerprint'] = $formData['mopt_payone__ratepay_direct_debit_device_fingerprint'];

        // set SessionFlag, so we can redirect customer to shippingPayment in case the same paymentmean was used before
        $session = Shopware()->Session();
        $session->offsetSet('moptFormSubmitted', true);

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

        // set SessionFlag, so we can redirect customer to shippingPayment in case the same paymentmean was used before
        $session = Shopware()->Session();
        $session->offsetSet('moptFormSubmitted', true);

        return $paymentData;
    }

    /**
     * validates IBAN/BIC fields
     *
     * @param string $ibanbic
     * @return boolean
     */
    private function isValidIbanBic($ibanbic) {

        if (!preg_match('/^[A-Z0-9 ]+$/',
            $ibanbic)) {

            return false;
        }
        else {
           return true;
        }
    }

}
