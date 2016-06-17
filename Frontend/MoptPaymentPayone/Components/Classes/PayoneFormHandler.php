<?php

/**
 * $Id: $
 */
class Mopt_PayoneFormHandler {

    /**
     * process payment form
     *
     * @param string $paymentId
     * @param array $formData
     * @param Mopt_PayonePaymentHelper $paymentHelper
     * @return array payment data 
     */
    public function processPaymentForm($paymentId, $formData, $paymentHelper) {
        if ($paymentHelper->isPayoneCreditcard($paymentId)) {
            return $this->proccessCreditCard($formData);
        }

        if ($paymentHelper->isPayoneSofortuerberweisung($paymentId)) {
            return $this->proccessSofortueberweisung($formData);
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

        if ($paymentHelper->isPayoneKlarnaInstallment($paymentId)) {
            return $this->proccessKlarnaInstallment($formData);
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

        return array();
    }

    /**
     * process form data 
     *
     * @param array $formData
     * @return array 
     */
    protected function proccessSofortueberweisung($formData) {
        $paymentData = array();

        if (!$formData["mopt_payone__sofort_bankaccount"]) {
            $paymentData['sErrorFlag']["mopt_payone__sofort_bankaccount"] = true;
        } else {
            $paymentData['formData']["mopt_payone__sofort_bankaccount"] = $formData["mopt_payone__sofort_bankaccount"];
        }

        if (!$formData["mopt_payone__sofort_bankcode"]) {
            $paymentData['sErrorFlag']["mopt_payone__sofort_bankcode"] = true;
        } else {
            $paymentData['formData']["mopt_payone__sofort_bankcode"] = $formData["mopt_payone__sofort_bankcode"];
        }

        if (!$formData["mopt_payone__sofort_iban"]) {
            $paymentData['sErrorFlag']["mopt_payone__sofort_iban"] = true;
        } else {
            $paymentData['formData']["mopt_payone__sofort_iban"] = $formData["mopt_payone__sofort_iban"];
        }

        if (!$formData["mopt_payone__sofort_bic"]) {
            $paymentData['sErrorFlag']["mopt_payone__sofort_bic"] = true;
        } else {
            $paymentData['formData']["mopt_payone__sofort_bic"] = $formData["mopt_payone__sofort_bic"];
        }

        if ($paymentData['sErrorFlag']["mopt_payone__sofort_iban"] && $paymentData['sErrorFlag']["mopt_payone__sofort_bic"] && !$paymentData['sErrorFlag']["mopt_payone__sofort_bankaccount"] && !$paymentData['sErrorFlag']["mopt_payone__sofort_bankcode"]
        ) {
            unset($paymentData['sErrorFlag']["mopt_payone__sofort_iban"]);
            unset($paymentData['sErrorFlag']["mopt_payone__sofort_bic"]);
        }

        if (!$paymentData['sErrorFlag']["mopt_payone__sofort_iban"] && !$paymentData['sErrorFlag']["mopt_payone__sofort_bic"] && $paymentData['sErrorFlag']["mopt_payone__sofort_bankaccount"] && $paymentData['sErrorFlag']["mopt_payone__sofort_bankcode"]
        ) {
            unset($paymentData['sErrorFlag']["mopt_payone__sofort_bankaccount"]);
            unset($paymentData['sErrorFlag']["mopt_payone__sofort_bankcode"]);
        }

        if (count($paymentData['sErrorFlag'])) {
            return $paymentData;
        }

        $paymentData['formData']['mopt_payone__onlinebanktransfertype'] = Payone_Api_Enum_OnlinebanktransferType::INSTANT_MONEY_TRANSFER;

        return $paymentData;
    }

    /**
     * process form data 
     *
     * @param array $formData
     * @return array 
     */
    protected function proccessGiropay($formData) {
        $paymentData = array();

        if (!$formData["mopt_payone__giropay_iban"]) {
            $paymentData['sErrorFlag']["mopt_payone__giropay_iban"] = true;
        } else {
            $paymentData['formData']["mopt_payone__giropay_iban"] = $formData["mopt_payone__giropay_iban"];
        }

        if (!$formData["mopt_payone__giropay_bic"]) {
            $paymentData['sErrorFlag']["mopt_payone__giropay_bic"] = true;
        } else {
            $paymentData['formData']["mopt_payone__giropay_bic"] = $formData["mopt_payone__giropay_bic"];
        }

        if (count($paymentData['sErrorFlag'])) {
            return $paymentData;
        }

        $paymentData['formData']['mopt_payone__onlinebanktransfertype'] = Payone_Api_Enum_OnlinebanktransferType::GIROPAY;
        $paymentData['formData']['mopt_payone__giropay_bankcountry'] = 'DE';

        return $paymentData;
    }

    /**
     * process form data 
     *
     * @param array $formData
     * @return array 
     */
    protected function proccessEps($formData) {
        $paymentData = array();

        if (!isset($formData["mopt_payone__eps_bankgrouptype"]) || empty($formData["mopt_payone__eps_bankgrouptype"])) {
            $paymentData['sErrorFlag']["mopt_payone__eps_bankgrouptype"] = true;
        } else {
            $paymentData['formData']["mopt_payone__eps_bankgrouptype"] = $formData["mopt_payone__eps_bankgrouptype"];
            $paymentData['formData']['mopt_payone__onlinebanktransfertype'] = Payone_Api_Enum_OnlinebanktransferType::EPS_ONLINE_BANK_TRANSFER;
            $paymentData['formData']['mopt_payone__eps_bankcountry'] = 'AT';
        }

        return $paymentData;
    }

    /**
     * process form data 
     *
     * @param array $formData
     * @return array 
     */
    protected function proccessIdeal($formData) {
        $paymentData = array();

        if ($formData["mopt_payone__ideal_bankgrouptype"] == 'not_choosen') {
            $paymentData['sErrorFlag']["mopt_payone__ideal_bankgrouptype"] = true;
        } else {
            $paymentData['formData']["mopt_payone__ideal_bankgrouptype"] = $formData["mopt_payone__ideal_bankgrouptype"];
            $paymentData['formData']['mopt_payone__onlinebanktransfertype'] = Payone_Api_Enum_OnlinebanktransferType::IDEAL;
            $paymentData['formData']['mopt_payone__ideal_bankcountry'] = 'NL';
        }

        return $paymentData;
    }

    /**
     * process form data 
     *
     * @param array $formData
     * @return array 
     */
    protected function proccessDebitNote($formData) {
        $paymentData = array();

        //bankaccount/code or bic/iban

        if (!$formData["mopt_payone__debit_iban"]) {
            $paymentData['sErrorFlag']["mopt_payone__debit_iban"] = true;
        } else {
            $paymentData['formData']["mopt_payone__debit_iban"] = $formData["mopt_payone__debit_iban"];
        }

        if (!$formData["mopt_payone__debit_bic"]) {
            $paymentData['sErrorFlag']["mopt_payone__debit_bic"] = true;
        } else {
            $paymentData['formData']["mopt_payone__debit_bic"] = $formData["mopt_payone__debit_bic"];
        }

        if (!$formData["mopt_payone__debit_bankaccount"]) {
            $paymentData['sErrorFlag']["mopt_payone__debit_bankaccount"] = true;
        } else {
            $paymentData['formData']["mopt_payone__debit_bankaccount"] = $formData["mopt_payone__debit_bankaccount"];
        }

        if (!$formData["mopt_payone__debit_bankcode"]) {
            $paymentData['sErrorFlag']["mopt_payone__debit_bankcode"] = true;
        } else {
            $paymentData['formData']["mopt_payone__debit_bankcode"] = $formData["mopt_payone__debit_bankcode"];
        }

        if (!$formData["mopt_payone__debit_bankaccountholder"]) {
            $paymentData['sErrorFlag']["mopt_payone__debit_bankaccountholder"] = true;
        } else {
            $paymentData['formData']["mopt_payone__debit_bankaccountholder"] = $formData["mopt_payone__debit_bankaccountholder"];
        }

        if (!isset($formData["mopt_payone__debit_bankcountry"]) || empty($formData["mopt_payone__debit_bankcountry"])) {
            $paymentData['sErrorFlag']["mopt_payone__debit_bankcountry"] = true;
        } else {
            $paymentData['formData']["mopt_payone__debit_bankcountry"] = $formData["mopt_payone__debit_bankcountry"];
        }

        if ($paymentData['sErrorFlag']["mopt_payone__debit_iban"] && $paymentData['sErrorFlag']["mopt_payone__debit_bic"] && !$paymentData['sErrorFlag']["mopt_payone__debit_bankaccount"] && !$paymentData['sErrorFlag']["mopt_payone__debit_bankcode"]
        ) {
            unset($paymentData['sErrorFlag']["mopt_payone__debit_iban"]);
            unset($paymentData['sErrorFlag']["mopt_payone__debit_bic"]);
        }

        if (!$paymentData['sErrorFlag']["mopt_payone__debit_iban"] && !$paymentData['sErrorFlag']["mopt_payone__debit_bic"] && $paymentData['sErrorFlag']["mopt_payone__debit_bankaccount"] && $paymentData['sErrorFlag']["mopt_payone__debit_bankcode"]
        ) {
            unset($paymentData['sErrorFlag']["mopt_payone__debit_bankaccount"]);
            unset($paymentData['sErrorFlag']["mopt_payone__debit_bankcode"]);
        }

        return $paymentData;
    }

    /**
     * process form data 
     *
     * @param array $formData
     * @return array 
     */
    protected function proccessCreditCard($formData) {
        $paymentData = array();
        $paymentData['formData'] = $formData;
        return $paymentData;
    }

    /**
     * process form data 
     *
     * @param array $formData
     * @return array 
     */
    protected function proccessKlarna($formData) {
        $paymentData = array();

        if (!$formData["mopt_payone__klarna_telephone"]) {
            $paymentData['sErrorFlag']["mopt_payone__klarna_telephone"] = true;
        } else {
            $paymentData['formData']["mopt_payone__klarna_telephone"] = $formData["mopt_payone__klarna_telephone"];
        }

        if (!$formData["mopt_payone__klarna_agreement"] || !in_array($formData["mopt_payone__klarna_agreement"], array('on', true))) {
            $paymentData['sErrorFlag']["mopt_payone__klarna_agreement"] = true;
        } else {
            $paymentData['formData']["mopt_payone__klarna_agreement"] = $formData["mopt_payone__klarna_agreement"];
        }

        if (!$formData["mopt_payone__klarna_birthyear"]) {
            $paymentData['sErrorFlag']["mopt_payone__klarna_birthyear"] = true;
        } else {
            $paymentData['formData']["mopt_payone__klarna_birthyear"] = $formData["mopt_payone__klarna_birthyear"];
        }

        if (!$formData["mopt_payone__klarna_birthmonth"]) {
            $paymentData['sErrorFlag']["mopt_payone__klarna_birthmonth"] = true;
        } else {
            $paymentData['formData']["mopt_payone__klarna_birthmonth"] = $formData["mopt_payone__klarna_birthmonth"];
        }

        if (!$formData["mopt_payone__klarna_birthday"]) {
            $paymentData['sErrorFlag']["mopt_payone__klarna_birthday"] = true;
        } else {
            $paymentData['formData']["mopt_payone__klarna_birthday"] = $formData["mopt_payone__klarna_birthday"];
        }
        $paymentData['formData']['mopt_save_birthday_and_phone'] = true;

        if ($paymentData['sErrorFlag']["mopt_payone__klarna_telephone"] || $paymentData['sErrorFlag']["mopt_payone__klarna_birthyear"] || $paymentData['sErrorFlag']["mopt_payone__klarna_birthmonth"] || $paymentData['sErrorFlag']["mopt_payone__klarna_birthday"]) {
            $paymentData['formData']['mopt_save_birthday_and_phone'] = false;
        }

        return $paymentData;
    }

    /**
     * process form data 
     *
     * @param array $formData
     * @return array 
     */
    protected function proccessKlarnaInstallment($formData) {
        $paymentData = array();

        if (!$formData["mopt_payone__klarna_inst_telephone"]) {
            $paymentData['sErrorFlag']["mopt_payone__klarna_inst_telephone"] = true;
        } else {
            $paymentData['formData']["mopt_payone__klarna_inst_telephone"] = $formData["mopt_payone__klarna_inst_telephone"];
        }

        if (!$formData["mopt_payone__klarna_inst_agreement"] || $formData["mopt_payone__klarna_inst_agreement"] !== 'on') {
            $paymentData['sErrorFlag']["mopt_payone__klarna_inst_agreement"] = true;
        } else {
            $paymentData['formData']["mopt_payone__klarna_inst_agreement"] = $formData["mopt_payone__klarna_inst_agreement"];
        }

        if (!$formData["mopt_payone__klarna_inst_birthyear"]) {
            $paymentData['sErrorFlag']["mopt_payone__klarna_inst_birthyear"] = true;
        } else {
            $paymentData['formData']["mopt_payone__klarna_inst_birthyear"] = $formData["mopt_payone__klarna_inst_birthyear"];
        }

        if (!$formData["mopt_payone__klarna_inst_birthmonth"]) {
            $paymentData['sErrorFlag']["mopt_payone__klarna_inst_birthmonth"] = true;
        } else {
            $paymentData['formData']["mopt_payone__klarna_inst_birthmonth"] = $formData["mopt_payone__klarna_inst_birthmonth"];
        }

        if (!$formData["mopt_payone__klarna_inst_birthday"]) {
            $paymentData['sErrorFlag']["mopt_payone__klarna_inst_birthday"] = true;
        } else {
            $paymentData['formData']["mopt_payone__klarna_inst_birthday"] = $formData["mopt_payone__klarna_inst_birthday"];
        }
        $paymentData['formData']['mopt_save_birthday_and_phone'] = true;

        if ($paymentData['sErrorFlag']["mopt_payone__klarna_inst_telephone"] || $paymentData['sErrorFlag']["mopt_payone__klarna_inst_birthyear"] || $paymentData['sErrorFlag']["mopt_payone__klarna_inst_birthmonth"] || $paymentData['sErrorFlag']["mopt_payone__klarna_inst_birthday"]) {
            $paymentData['formData']['mopt_save_birthday_and_phone'] = false;
        }

        return $paymentData;
    }

    /**
     * process form data 
     *
     * @param array $formData
     * @return array 
     */
    protected function proccessPayolutionDebitNote($formData) {

        $paymentData = array();

        if (!$formData["mopt_payone__debit_agreement"] || !in_array($formData["mopt_payone__debit_agreement"], array('on', true))) {
            $paymentData['sErrorFlag']["mopt_payone__debit_agreement"] = true;
        } else {
            $paymentData['formData']["mopt_payone__debit_agreement"] = $formData["mopt_payone__debit_agreement"];
        }
        if (!$formData["mopt_payone__debit_agreement2"] || !in_array($formData["mopt_payone__debit_agreement2"], array('on', true))) {
            $paymentData['sErrorFlag']["mopt_payone__debit_agreement2"] = true;
        } else {
            $paymentData['formData']["mopt_payone__debit_agreement2"] = $formData["mopt_payone__debit_agreement2"];
        }        

        if ($formData[mopt_payone__payolution_birthdaydate] == "0000-00-00" && !$formData[mopt_payone__payolution_b2bmode] == "1") {
            if (!$formData["mopt_payone__payolution_debitnote_birthyear"]) {
                $paymentData['sErrorFlag']["mopt_payone__payolution_debitnote_birthyear"] = true;
            } else {
                $paymentData['formData']["mopt_payone__payolution_debitnote_birthyear"] = $formData["mopt_payone__payolution_debitnote_birthyear"];
            }

            if (!$formData["mopt_payone__payolution_debitnote_birthmonth"]) {
                $paymentData['sErrorFlag']["mopt_payone__payolution_debitnote_birthmonth"] = true;
            } else {
                $paymentData['formData']["mopt_payone__payolution_debitnote_birthmonth"] = $formData["mopt_payone__payolution_debitnote_birthmonth"];
            }

            if (!$formData["mopt_payone__payolution_debitnote_birthday"]) {
                $paymentData['sErrorFlag']["mopt_payone__payolution_debitnote_birthday"] = true;
            } else {
                $paymentData['formData']["mopt_payone__payolution_debitnote_birthday"] = $formData["mopt_payone__payolution_debitnote_birthday"];
            }
            $paymentData['formData']['mopt_save_birthday'] = true;

            if ($paymentData['sErrorFlag']["mopt_payone__payolution_debitnote_birthyear"] || $paymentData['sErrorFlag']["mopt_payone__payolution_debitnote_birthmonth"] || $paymentData['sErrorFlag']["mopt_payone__payolution_debitnote_birthday"]) {
                $paymentData['formData']['mopt_save_birthday'] = false;
            }
        }

        if (!$formData["mopt_payone__debit_iban"]) {
            $paymentData['sErrorFlag']["mopt_payone__debit_iban"] = true;
        } else {
            $paymentData['formData']["mopt_payone__debit_iban"] = $formData["mopt_payone__debit_iban"];
        }

        if (!$formData["mopt_payone__debit_bic"]) {
            $paymentData['sErrorFlag']["mopt_payone__debit_bic"] = true;
        } else {
            $paymentData['formData']["mopt_payone__debit_bic"] = $formData["mopt_payone__debit_bic"];
        }

        if ($paymentData['sErrorFlag']["mopt_payone__debit_iban"] && $paymentData['sErrorFlag']["mopt_payone__debit_bic"]) {
            unset($paymentData['sErrorFlag']["mopt_payone__debit_iban"]);
            unset($paymentData['sErrorFlag']["mopt_payone__debit_bic"]);
        }
        
        if ($formData[mopt_payone__payolution_b2bmode] === "1")  {

            if (!$formData["mopt_payone__debitnote_company_trade_registry_number"]) {
                $paymentData['sErrorFlag']["mopt_payone__debitnote_company_trade_registry_number"] = true;
            } else {
                $paymentData['formData']["mopt_payone__debitnote_company_trade_registry_number"] = $formData["mopt_payone__debitnote_company_trade_registry_number"];
            }
            
            $paymentData['formData']["mopt_payone__payolution_b2bmode"] = $formData["mopt_payone__payolution_b2bmode"];            
        }        
        $paymentData['formData']["mopt_payone__payolution_birthdaydate"] = $formData["mopt_payone__payolution_birthdaydate"];
        
        // set sessionflag to trigger precheck
        Shopware()->Session()->moptPayolutionPrecheck = "1";

        return $paymentData;
    }

    /**
     * process form data 
     *
     * @param array $formData
     * @return array 
     */
    protected function proccessPayolutionInvoice($formData) {
        $paymentData = array();

        if (!$formData["mopt_payone__payolution_invoice_agreement"] || !in_array($formData["mopt_payone__payolution_invoice_agreement"], array('on', true))) {
            $paymentData['sErrorFlag']["mopt_payone__payolution_invoice_agreement"] = true;
        } else {
            $paymentData['formData']["mopt_payone__payolution_invoice_agreement"] = $formData["mopt_payone__payolution_invoice_agreement"];
        }

        if ($formData[mopt_payone__payolution_birthdaydate] == "0000-00-00" && $formData[mopt_payone__payolution_b2bmode] !== "1") {
            if (!$formData["mopt_payone__payolution_invoice_birthyear"]) {
                $paymentData['sErrorFlag']["mopt_payone__payolution_invoice_birthyear"] = true;
            } else {
                $paymentData['formData']["mopt_payone__payolution_invoice_birthyear"] = $formData["mopt_payone__payolution_invoice_birthyear"];
            }

            if (!$formData["mopt_payone__payolution_invoice_birthmonth"]) {
                $paymentData['sErrorFlag']["mopt_payone__payolution_invoice_birthmonth"] = true;
            } else {
                $paymentData['formData']["mopt_payone__payolution_invoice_birthmonth"] = $formData["mopt_payone__payolution_invoice_birthmonth"];
            }

            if (!$formData["mopt_payone__payolution_invoice_birthday"]) {
                $paymentData['sErrorFlag']["mopt_payone__payolution_invoice_birthday"] = true;
            } else {
                $paymentData['formData']["mopt_payone__payolution_invoice_birthday"] = $formData["mopt_payone__payolution_invoice_birthday"];
            }
            $paymentData['formData']['mopt_save_birthday'] = true;

            if ($paymentData['sErrorFlag']["mopt_payone__payolution_invoice_birthyear"] || $paymentData['sErrorFlag']["mopt_payone__payolution_invoice_birthmonth"] || $paymentData['sErrorFlag']["mopt_payone__payolution_invoice_birthday"]) {
                $paymentData['formData']['mopt_save_birthday'] = false;
            }
        } 
        
        if ($formData[mopt_payone__payolution_b2bmode] === "1")  {

            if (!$formData["mopt_payone__invoice_company_trade_registry_number"]) {
                $paymentData['sErrorFlag']["mopt_payone__invoice_company_trade_registry_number"] = true;
            } else {
                $paymentData['formData']["mopt_payone__invoice_company_trade_registry_number"] = $formData["mopt_payone__invoice_company_trade_registry_number"];
            }
            
            $paymentData['formData']["mopt_payone__payolution_b2bmode"] = $formData["mopt_payone__payolution_b2bmode"];            
        }
        
        $paymentData['formData']["mopt_payone__payolution_birthdaydate"] = $formData["mopt_payone__payolution_birthdaydate"];
        
        // set sessionflag to trigger precheck
        Shopware()->Session()->moptPayolutionPrecheck = "1";
        
        return $paymentData;
    }

}
