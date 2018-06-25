<?php

/**
 * $Id: $
 */
class Mopt_PayonePaymentHelper
{
    //Klarna CDN links for consents and legal terms
    const MOPT_PAYONE_KLARNA_CONSENT_DE = "https://cdn.klarna.com/1.0/shared/content/legal/terms/##storeid##/de_de/consent";
    const MOPT_PAYONE_KLARNA_CONSENT_AT = "https://cdn.klarna.com/1.0/shared/content/legal/terms/##storeid##/de_at/consent";

    const MOPT_PAYONE_KLARNA_INVOICE_TERMS_DE = "https://cdn.klarna.com/1.0/shared/content/legal/terms/##storeid##/de_de/invoice?fee=0";
    const MOPT_PAYONE_KLARNA_INVOICE_TERMS_AT = "https://cdn.klarna.com/1.0/shared/content/legal/terms/##storeid##/de_at/invoice?fee=0";
    const MOPT_PAYONE_KLARNA_INVOICE_TERMS_DK = "https://cdn.klarna.com/1.0/shared/content/legal/terms/##storeid##/da_dk/invoice?fee=0";
    const MOPT_PAYONE_KLARNA_INVOICE_TERMS_FI = "https://cdn.klarna.com/1.0/shared/content/legal/terms/##storeid##/fi_fi/invoice?fee=0";
    const MOPT_PAYONE_KLARNA_INVOICE_TERMS_NL = "https://cdn.klarna.com/1.0/shared/content/legal/terms/##storeid##/nl_nl/invoice?fee=0";
    const MOPT_PAYONE_KLARNA_INVOICE_TERMS_NO = "https://cdn.klarna.com/1.0/shared/content/legal/terms/##storeid##/nb_no/invoice?fee=0";
    const MOPT_PAYONE_KLARNA_INVOICE_TERMS_SE = "https://cdn.klarna.com/1.0/shared/content/legal/terms/##storeid##/sv_se/invoice?fee=0";

    //Payolution links for consents and legal terms
    const MOPT_PAYONE_PAYOLUTION_CONSENT_DE = "https://payment.payolution.com/payolution-payment/infoport/dataprivacydeclaration?mId=";
    const MOPT_PAYONE_PAYOLUTION_SEPA_DE = "https://payment.payolution.com/payolution-payment/infoport/sepa/mandate.pdf";

    /**
     * @var string
     */
    protected $_sFallback = "
        <header>
            <strong>Zusätzliche Hinweise für die Datenschutzerklärung für Kauf auf Rechnung, Ratenzahlung und Zahlung mittels SEPA-Basis-Lastschrift von **company** (im Folgenden: \"wir\")</strong></br>
            <span><i>(Stand: 17.03.2016)</i></span>
        </header>
        <ol>
          <li><p>Bei Kauf auf Rechnung oder Ratenzahlung oder SEPA-Basis-Lastschrift wird von Ihnen während des Bestellprozesses eine datenschutzrechtliche Einwilligung eingeholt. Folgend finden Sie eine Wiederholung dieser Bestimmungen, die lediglich informativen Charakter haben.</p></li>
          <li><p>Bei Auswahl von Kauf auf Rechnung oder Ratenzahlung oder Bezahlung mittels SEPA-Basis-Lastschrift werden für die Abwicklung dieser Zahlarten personenbezogene Daten (Vorname, Nachname, Adresse, Email, Telefonnummer, Geburtsdatum, IP-Adresse, Geschlecht) gemeinsam mit für die Transaktionsabwicklung erforderlichen Daten (Artikel, Rechnungsbetrag, Zinsen, Raten, Fälligkeiten, Gesamtbetrag, Rechnungsnummer, Steuern, Währung, Bestelldatum und Bestellzeitpunkt) an payolution übermittelt werden. payolution hat ein berechtigtes Interesse an den Daten und benötigt bzw. verwendet diese um Risikoüberprüfungen durchzuführen.</p></li>
          <li>
            <p>Zur Überprüfung der Identität bzw. Bonität des Kunden werden Abfragen und Auskünfte bei öffentlich zugänglichen Datenbanken sowie Kreditauskunfteien durchgeführt. Bei nachstehenden Anbietern können Auskünfte und gegebenenfalls Bonitätsinformationen auf Basis mathematisch-statistischer Verfahren eingeholt werden:</p>
            <ul>
                <li>CRIF GmbH, Diefenbachgasse 35, A-1150 Wien</li>
                <li>CRIF AG, Hagenholzstrasse 81, CH-8050 Zürich</li>
                <li>Deltavista GmbH, Dessauerstraße 9, D-80992 München</li>
                <li>SCHUFA Holding AG, Kormoranweg 5, D-65201 Wiesbaden</li>
                <li>KSV1870 Information GmbH, Wagenseilgasse 7, A-1120 Wien</li>
                <li>Bürgel Wirtschaftsinformationen GmbH & Co. KG, Gasstraße 18, D-22761 Hamburg</li>
                <li>Creditreform Boniversum GmbH, Hellersbergstr. 11, D-41460 Neuss</li>
                <li>infoscore Consumer Data GmbH, Rheinstraße 99, D-76532 Baden-Baden</li>
                <li>ProfileAddress Direktmarketing GmbH, Altmannsdorfer Strasse 311, A-1230 Wien</li>
                <li>Deutsche Post Direkt GmbH, Junkersring 57, D-53844 Troisdorf</li>
                <li>payolution GmbH, Am Euro Platz 2, A-1120 Wien</li>
            </ul>
            <p>payolution wird Ihre Angaben zur Bankverbindung (insbesondere Bankleitzahl und Kontonummer) zum Zwecke der Kontonummernprüfung an die SCHUFA Holding AG übermitteln. Die SCHUFA prüft anhand dieser Daten zunächst, ob die von Ihnen gemachten Angaben zur Bankverbindung plausibel sind. Die SCHUFA überprüft, ob die zur Prüfung verwendeten Daten ggf. in Ihrem Datenbestand gespeichert sind und übermittelt sodann das Ergebnis der Überprüfung an payolution zurück. Ein weiterer Datenaustausch wie die Bekanntgabe von Bonitätsinformationen oder eine Übermittlung abweichender Bankverbindungsdaten sowie Speicherung Ihrer Daten im SCHUFA-Datenbestand finden im Rahmen der Kontonummernprüfung nicht statt. Es wird aus Nachweisgründen allein die Tatsache der Überprüfung der Bankverbindungsdaten bei der SCHUFA gespeichert.</p>
            <p>payolution ist berechtigt, auch Daten zu etwaigem nicht-vertragsgemäßen Verhalten (z.B. unbestrittene offene Forderungen) zu speichern, zu verarbeiten, zu nutzen und an oben genannte Auskunfteien zu übermitteln.</p>
          </li>
          <li><p>Wir sind bereits nach den Bestimmungen des Bürgerlichen Gesetzbuches über Finanzierungshilfen zwischen Unternehmern und Verbrauchern, zu einer Prüfung Ihrer Kreditwürdigkeit gesetzlich verpflichtet.</p></li>
          <li><p>Im Fall eines Kaufs auf Rechnung oder Ratenkauf oder einer Bezahlung mittels SEPA-Basis-Lastschrift werden der payolution GmbH Daten über die Aufnahme (zu Ihrer Person, Kaufpreis, Laufzeit des Teilzahlungsgeschäfts, Ratenbeginn) und vereinbarungsgemäße Abwicklung (z.B. vorzeitige Rückzahlung, Laufzeitverlängerung, erfolgte Rückzahlungen) dieses Teilzahlungsgeschäfts übermittelt. Nach Abtretung der Kaufpreisforderung wird die forderungsübernehmende Bank die genannten Datenübermittlungen vornehmen. Wir bzw. die Bank, der die Kaufpreisforderung abgetreten wird, werden payolution GmbH auch Daten aufgrund nichtvertragsgemäßer Abwicklung (z.B. Kündigung des Teilzahlungsgeschäfts, Zwangsvollstreckungs-maßnahmen) melden. Diese Meldungen dürfen nach den datenschutzrechtlichen Bestimmungen nur erfolgen, soweit dies zur Wahrung berechtigter Interessen von Vertragspartnern der payolution GmbH oder der Allgemeinheit erforderlich ist und dadurch Ihre schutzwürdigen Belange nicht beeinträchtigt werden. payolution GmbH speichert die Daten, um ihren Vertragspartnern, die gewerbsmäßig Teilzahlungs- und sonstige Kreditgeschäfte an Verbraucher geben, Informationen zur Beurteilung der Kreditwürdigkeit von Kunden geben zu können. An Unternehmen, die gewerbsmäßig Forderungen einziehen und payolution GmbH vertraglich angeschlossen sind, können zum Zwecke der Schuldnerermittlung Adressdaten übermittelt werden. payolution GmbH stellt die Daten ihren Vertragspartnern nur zur Verfügung, wenn diese ein berechtigtes Interesse an der Datenübermittlung glaubhaft darlegen. payolution GmbH übermittelt nur objektive Daten ohne Angabe der Bank; subjektive Werturteile sowie persönliche Einkommens- und Vermögensverhältnisse sind in Auskünften der payolution GmbH nicht enthalten.</p></li>
          <li><p>Die im Bestellprozess durch Einwilligung erfolgte Zustimmung zur Datenweitergabe kann jederzeit, auch ohne Angabe von Gründen, uns gegenüber widerrufen können. Die oben genannten gesetzlichen Verpflichtungen zur Überprüfung Ihrer Kreditwürdigkeit bleiben von einem allfälligen Widerruf jedoch unberührt. Sie sind verpflichtet ausschließlich wahrheitsgetreue Angaben gegenüber uns zu machen.</p></li>
          <li><p>Sollten Sie Auskunft über die Erhebung, Nutzung, Verarbeitung oder Übermittlung von Sie betreffenden personenbezogenen Daten erhalten wollen oder Auskünfte, Berichtigungen, Sperrungen oder Löschung dieser Daten wünschen, können Sie sich an den Sachbearbeiter für Datenschutz bei payolution wenden:</p></li>
        </ol>

        <footer>Sachbearbeiter für Datenschutz<br />
            datenschutz@payolution.com<br />
            payolution GmbH<br />
            Am Euro Platz 2<br />
            1120 Wien<br />
            DVR: 4008655
        </footer>
    ";


    /**
     * adds Payone API value for creditcard
     *
     * @param array $cardData
     * @return array
     */
    public function mapCardLetter($cardData)
    {
        foreach ($cardData as &$creditCard) {
            $start = strpos($creditCard['name'], 'mopt_payone__cc');

            if ($start === false) {
                continue;
            }

            $creditCardName = substr($creditCard['name'], $start, 19);

            switch ($creditCardName) {
                case "mopt_payone__cc_vis":
                    $creditCard['short'] = 'V';
                    break;
                case 'mopt_payone__cc_mas':
                    $creditCard['short'] = 'M';
                    break;
                case 'mopt_payone__cc_ame':
                    $creditCard['short'] = 'A';
                    break;
                case 'mopt_payone__cc_din':
                    $creditCard['short'] = 'D';
                    break;
                case 'mopt_payone__cc_jcb':
                    $creditCard['short'] = 'J';
                    break;
                case 'mopt_payone__cc_mae':
                    $creditCard['short'] = 'O';
                    break;
                case 'mopt_payone__cc_dis':
                    $creditCard['short'] = 'C';
                    break;
                case 'mopt_payone__cc_car':
                    $creditCard['short'] = 'B';
                    break;
            }
        }
        return $cardData;
    }

    /**
     * delete saved payment data
     *
     * @param $userId
     * @throws Zend_Db_Adapter_Exception
     */
    public function deletePaymentData($userId)
    {
        if ($userId != null) {
            $sql = 'SELECT userId FROM s_plugin_mopt_payone_payment_data WHERE userId = ' . $userId;
            $result = Shopware()->Db()->fetchOne($sql);
            if ($result) {
                $sql = 'DELETE FROM s_plugin_mopt_payone_payment_data WHERE userId = ' . $userId;
                Shopware()->Db()->exec($sql);
            }
        }
    }

    /**
     * @param $sCompany
     * @return mixed
     */
    protected function _getFallbackText($sCompany)
    {
        $sFallback = str_replace('**company**', $sCompany, $this->_sFallback);
        return $sFallback;
    }

    /**
     * returns payment name
     *
     * @param string $paymentID
     * @return string
     */
    public function getPaymentNameFromId($paymentID)
    {
        $sql = 'SELECT `name` FROM `s_core_paymentmeans` WHERE id = ?';
        $paymentName = Shopware()->Db()->fetchOne($sql, $paymentID);

        return $paymentName;
    }

    /**
     * returns payment name
     *
     * @param string $paymentName
     * @return string
     */
    public function getPaymentIdFromName($paymentName)
    {
        $sql = 'SELECT `id` FROM `s_core_paymentmeans` WHERE name = ?';
        $paymentId = Shopware()->Db()->fetchOne($sql, $paymentName);

        return $paymentId;
    }

    /**
     * save payment data
     *
     * @param $userId
     * @param $paymentData
     * @throws Zend_Db_Adapter_Exception
     */
    public function savePaymentData($userId, $paymentData)
    {
        $sql = 'REPLACE INTO `s_plugin_mopt_payone_payment_data`' .
            '(`userId`,`moptPaymentData`) VALUES (?,?)';
        $paymentData = serialize($paymentData['formData']);
        Shopware()->Db()->query($sql, array($userId, $paymentData));
    }

    /**
     * set configured default payment as payment method
     *
     * @param $userId
     * @throws Zend_Db_Adapter_Exception
     */
    public function setConfiguredDefaultPaymentAsPayment($userId)
    {
        $sql = "UPDATE s_user SET paymentID = ? WHERE id = ?";
        Shopware()->Db()->query($sql, array((int)Shopware()->Config()->Defaultpayment, (int)$userId));
    }

    /**
     * extract clearing data from response object
     *
     * @param object $response
     * @return boolean|array
     */
    public function extractClearingDataFromResponse($response)
    {
        $responseData = $response->toArray();

        if ($responseData['txid']) {
            $responseData['clearing_txid'] = $responseData['txid'];
        }

        foreach ($responseData as $key => $value) {
            if (strpos($key, 'clearing_') === false) {
                unset($responseData[$key]);
            }
        }

        if (empty($responseData)) {
            return false;
        }

        return $responseData;
    }

    /**
     * extract barzahlen code to embed on checkout finish page from response object
     *
     * @param $response
     * @return bool|string
     */
    public function extractBarzahlenCodeFromResponse($response)
    {
        if (!method_exists($response, 'getPaydata')) {
            return false;
        }
        $payData = $response->getPaydata();
        if (!$payData) {
            return false;
        }
        $arr = $payData->toArray();
        foreach ($arr as $k => $v) {
            $arr[substr($k, strpos($k, '[') + 1, -1)] = $v;
        }
        if ($arr['content_format'] === 'HTML') {
            return urldecode($arr['instruction_notes']);
        } else {
            return $arr['instruction_notes'];
        }
    }

    /**
     * extract Payolution Clearingdata on checkout finish page from response object
     *
     * @param object $response
     * @return boolean|array
     */
    public function extractPayolutionClearingDataFromResponse($response)
    {
        if ($response instanceof Payone_Api_Response_Authorization_Approved) {
            $responseData = $response->toArray();

            foreach ($responseData['rawResponse'] as $key => $value) {
                if (strpos($key, 'clearing_') === false) {
                    unset($responseData['rawResponse'][$key]);
                }
            }

            if (empty($responseData['rawResponse'])) {
                return false;
            }
            $responseData['rawResponse']['add_paydata[clearing_reference]'] = $responseData['rawResponse']['clearing_reference'];

            return $responseData['rawResponse'];
        }

        if (!method_exists($response, 'getPaydata')) {
            return;
        }
        $payData = $response->getPaydata();
        if (!$payData) {
            return false;
        }
        $arr = $payData->toArray();

        return ($arr);
    }

    /**
     * returns clearing data
     *
     * @param string $orderId
     * @return array
     * @throws Exception
     */
    public function getClearingDataFromOrderId($orderId)
    {
        $data = array();

        if (!$order = Shopware()->Models()->getRepository('Shopware\Models\Order\Order')->find($orderId)) {
            throw new Exception("Order not found.");
        }

        $attribute = $order->getAttribute();
        $clearingData = $attribute->getMoptPayoneClearingData();
        json_decode($clearingData, $data);

        return $data;
    }

    /**
     * check if given payment name is payone creditcard payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneCreditcard($paymentName)
    {
        return preg_match('#mopt_payone__cc#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone creditcard payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneCreditcardForExport($paymentName)
    {
        if (preg_match('#mopt_payone__cc#', $paymentName) || $paymentName == 'mopt_payone_creditcard' || preg_match('#mopt_payone__creditcard_iframe#', $paymentName)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * check if given payment name is payone creditcard payment not grouped
     * it only checks for real existing payment methods not for virtual method "mopt_payone_creditcard"
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneCreditcardNotGrouped($paymentName)
    {
        return preg_match('#mopt_payone__cc#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone sofortueberweisung payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneSofortuerberweisung($paymentName)
    {
        return preg_match('#mopt_payone__ibt_sofortueberweisung#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone sofortueberweisung payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneBancontact($paymentName)
    {
        return preg_match('#mopt_payone__ibt_bancontact#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone barzahlen payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneBarzahlen($paymentName)
    {
        return preg_match('#mopt_payone__csh_barzahlen#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone giropay payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneGiropay($paymentName)
    {
        return preg_match('#mopt_payone__ibt_giropay#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone eps payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneEPS($paymentName)
    {
        return preg_match('#mopt_payone__ibt_eps#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone post eFinance payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayonePostEFinance($paymentName)
    {
        return preg_match('#mopt_payone__ibt_post_efinance#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone post finance card payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayonePostFinanceCard($paymentName)
    {
        return preg_match('#mopt_payone__ibt_post_finance_card#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone iDeal payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneIDeal($paymentName)
    {
        return preg_match('#mopt_payone__ibt_ideal#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone paypal payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayonePaypal($paymentName)
    {
        return preg_match('#mopt_payone__ewallet_paypal#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone paydirekt payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayonePaydirekt($paymentName)
    {
        return preg_match('#mopt_payone__ewallet_paydirekt#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is a payone ewallet payment method
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isEWallet($paymentName)
    {
        return preg_match('#mopt_payone__ewallet#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone debitnote payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneDebitnote($paymentName)
    {
        return preg_match('#mopt_payone__acc_debitnote#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone invoice payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneInvoice($paymentName)
    {
        return preg_match('#mopt_payone__acc_invoice#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone safe invoice payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneSafeInvoice($paymentName)
    {
        return preg_match('#mopt_payone__acc_payone_safe_invoice#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone pay in advance payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayonePayInAdvance($paymentName)
    {
        return preg_match('#mopt_payone__acc_payinadvance#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone cash on delivery payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneCashOnDelivery($paymentName)
    {
        return preg_match('#mopt_payone__acc_cashondel#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone billsafe payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneBillsafe($paymentName)
    {
        return preg_match('#mopt_payone__fin_billsafe#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone klarna payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneKlarna($paymentName)
    {
        return preg_match('#mopt_payone__fin_klarna#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone P24 payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneP24($paymentName)
    {
        return preg_match('#mopt_payone__ibt_p24#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone payment method
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayonePaymentMethod($paymentName)
    {
        return preg_match('#mopt_payone__#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone instant bank transfer payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneInstantBankTransfer($paymentName)
    {
        return preg_match('#mopt_payone__ibt#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone finance payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneFinance($paymentName)
    {
        return preg_match('#mopt_payone__fin#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone creditcard iframe
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneCreditcardIframe($paymentName)
    {
        return strpos($paymentName, 'mopt_payone__creditcard_iframe') === 0;
    }

    /**
     * check if given payment name is payone payolution debitnote payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayonePayolutionDebitNote($paymentName)
    {
        return preg_match('#mopt_payone__fin_payolution_debitnote#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone payolution invoice payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayonePayolutionInvoice($paymentName)
    {
        return preg_match('#mopt_payone__fin_payolution_invoice#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone payolution installment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayonePayolutionInstallment($paymentName)
    {
        return preg_match('#mopt_payone__fin_payolution_installment#', $paymentName) ? true : false;
    }

    /**
     * Determines if given payment is of type ratepay
     *
     * @param $paymentName
     * @return bool
     */
    public function isPayoneRatePay($paymentName) {
        $matchPosition = strpos($paymentName, 'mopt_payone__fin_ratepay');
        $return = ($matchPosition !== false) ? true : false;

        return $return;
    }

    /**
     * check if given payment name is payone ratepay invoice
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneRatepayInvoice($paymentName)
    {
        return preg_match('#mopt_payone__fin_ratepay_invoice#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone ratepay installment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneRatepayInstallment($paymentName)
    {
        return preg_match('#mopt_payone__fin_ratepay_installment#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone ratepay direct debit
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneRatepayDirectDebit($paymentName)
    {
        return preg_match('#mopt_payone__fin_ratepay_direct_debit#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone amazonpay
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneAmazonPay($paymentName)
    {
        return preg_match('#mopt_payone__ewallet_amazon_pay#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone masterpass
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneMasterpass($paymentName)
    {
        return preg_match('#mopt_payone__ewallet_masterpass#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone alipay payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneAlipay($paymentName)
    {
        return preg_match('#mopt_payone__ewallet_alipay#', $paymentName) ? true : false;
    }

    /**
     * get online bank transfer type for api communication
     *
     * @param string $paymentName
     * @return string
     */
    public function getOnlineBankTransferTypeFromPaymentName($paymentName)
    {
        if ($this->isPayoneSofortuerberweisung($paymentName)) {
            return Payone_Api_Enum_OnlinebanktransferType::INSTANT_MONEY_TRANSFER;
        }

        if ($this->isPayoneBancontact($paymentName)) {
            return Payone_Api_Enum_OnlinebanktransferType::BANCONTACT;
        }

        if ($this->isPayoneGiropay($paymentName)) {
            return Payone_Api_Enum_OnlinebanktransferType::GIROPAY;
        }

        if ($this->isPayoneEPS($paymentName)) {
            return Payone_Api_Enum_OnlinebanktransferType::EPS_ONLINE_BANK_TRANSFER;
        }

        if ($this->isPayonePostEFinance($paymentName)) {
            return Payone_Api_Enum_OnlinebanktransferType::POSTFINANCE_EFINANCE;
        }

        if ($this->isPayonePostFinanceCard($paymentName)) {
            return Payone_Api_Enum_OnlinebanktransferType::POSTFINANCE_CARD;
        }

        if ($this->isPayoneIDeal($paymentName)) {
            return Payone_Api_Enum_OnlinebanktransferType::IDEAL;
        }

        if ($this->isPayoneP24($paymentName)) {
            return Payone_Api_Enum_OnlinebanktransferType::P24;
        }

        return '';
    }

    public function moptGetCountriesAssignedToPayment($paymentId)
    {
        $sql = 'SELECT s_core_paymentmeans_countries.countryID, s_core_countries.countryname, s_core_countries.countryiso '
            . 'FROM s_core_paymentmeans_countries, s_core_countries '
            . 'WHERE s_core_paymentmeans_countries.paymentID = ? '
            . 'AND s_core_countries.id = s_core_paymentmeans_countries.countryID;';
        $paymentCountries = Shopware()->Db()->fetchAll($sql, $paymentId);

        return $paymentCountries;
    }

    public function moptGetShippingCountriesAssignedToPayment($paymentId)
    {
        $sql = 'SELECT s_premium_dispatch_countries.countryID, s_core_countries.countryname, s_core_countries.countryiso '
            . 'FROM s_premium_dispatch_countries, s_core_countries, s_premium_dispatch_paymentmeans '
            . 'WHERE s_premium_dispatch_countries.countryID = s_core_countries.id AND s_premium_dispatch_paymentmeans.paymentID = ? '
            . 'AND s_premium_dispatch_paymentmeans.dispatchID = s_premium_dispatch_countries.dispatchID;';
        $paymentCountries = Shopware()->Db()->fetchAll($sql, $paymentId);

        return $paymentCountries;
    }

    public function moptGetKlarnaAdditionalInformation($country, $storeId)
    {
        $information = array('consent' => '', 'legalTerm' => '');

        switch ($country) {
            case 'DE':
                {
                    $information['consent'] = 'Mit der Übermittlung der für die Abwicklung der gewählten Klarna Zahlungsmethode '
                        . 'und einer Identitäts- und Bonitätsprüfung erforderlichen Daten an Klarna bin ich einverstanden. '
                        . 'Meine <a target="_blank" href="' . self::MOPT_PAYONE_KLARNA_CONSENT_DE . '" '
                        . 'style="text-decoration: underline !important;">Einwilligung</a> '
                        . 'kann ich jederzeit mit Wirkung für die Zukunft widerrufen. Es gelten die AGB des Händlers.';
                    $information['legalTerm'] = 'Weitere Informationen zum Rechnungskauf finden Sie in den '
                        . '<a target="_blank" href="' . self::MOPT_PAYONE_KLARNA_INVOICE_TERMS_DE . '" style="text-decoration: underline !important;">'
                        . 'Rechnungsbedingungen</a>.';
                }
                break;

            case 'AT':
                {
                    $information['consent'] = 'Mit der Übermittlung der für die Abwicklung der gewählten Klarna Zahlungsmethode '
                        . 'und einer Identitäts- und Bonitätsprüfung erforderlichen Daten an Klarna bin ich einverstanden. '
                        . 'Meine <a target="_blank" href="' . self::MOPT_PAYONE_KLARNA_CONSENT_AT . '" '
                        . 'style="text-decoration: underline !important;">Einwilligung</a> '
                        . 'kann ich jederzeit mit Wirkung für die Zukunft widerrufen. Es gelten die AGB des Händlers.';
                    $information['legalTerm'] = 'Weitere Informationen zum Rechnungskauf finden Sie in den '
                        . '<a target="_blank" href="' . self::MOPT_PAYONE_KLARNA_INVOICE_TERMS_AT . '" style="text-decoration: underline !important;">'
                        . 'Rechnungsbedingungen</a>.';
                }
                break;

            case 'DK':
                {
                    $information['legalTerm'] = 'Accept legal terms. '
                        . '<a target="_blank" href="' . self::MOPT_PAYONE_KLARNA_INVOICE_TERMS_DK . '" style="text-decoration: underline !important;">'
                        . 'Vilkår for faktura</a>.';
                }
                break;

            case 'NL':
                {
                    $information['legalTerm'] = 'Accept legal terms. '
                        . '<a target="_blank" href="' . self::MOPT_PAYONE_KLARNA_INVOICE_TERMS_DK . '" style="text-decoration: underline !important;">'
                        . 'Factuurvoorwaarden</a>.';
                }
                break;

            case 'NO':
                {
                    $information['legalTerm'] = 'Accept legal terms. '
                        . '<a target="_blank" href="' . self::MOPT_PAYONE_KLARNA_INVOICE_TERMS_DK . '" style="text-decoration: underline !important;">'
                        . 'Vilkår for faktura</a>.';
                }
                break;

            case 'FI':
                {
                    $information['legalTerm'] = 'Accept legal terms. '
                        . '<a target="_blank" href="' . self::MOPT_PAYONE_KLARNA_INVOICE_TERMS_DK . '" style="text-decoration: underline !important;">'
                        . 'Laskuehdot</a>.';
                }
                break;

            case 'SE':
                {
                    $information['legalTerm'] = 'Accept legal terms. '
                        . '<a target="_blank" href="' . self::MOPT_PAYONE_KLARNA_INVOICE_TERMS_SE . '" style="text-decoration: underline !important;">'
                        . 'Villkor för faktura</a>.';
                }
                break;
        }

        $information['consent'] = str_replace('##storeid##', $storeId, $information['consent']);
        $information['legalTerm'] = str_replace('##storeid##', $storeId, $information['legalTerm']);

        return $information;
    }

    public function moptGetPayolutionAdditionalInformation($country, $companyname)
    {
        $information = array('consentDebit' => '', 'consentInvoice' => '', 'sepaagreement' => '');

        switch ($country) {
            case 'DE':
                {
                    $information['consentDebit'] = 'Mit der Übermittlung der für die Abwicklung des Einkaufs '
                        . 'und einer Identitäts- und Bonitätsprüfung erforderlichen Daten an payolution bin ich einverstanden. '
                        . 'Meine <a href="#" style="float:none; margin:0;" onclick="displayOverlayDebit();return false;">Einwilligung</a> '
                        . 'kann ich jederzeit mit Wirkung für die Zukunft widerrufen.';

                    $information['consentInvoice'] = 'Mit der Übermittlung der für die Abwicklung des Einkaufs '
                        . 'und einer Identitäts- und Bonitätsprüfung erforderlichen Daten an payolution bin ich einverstanden. '
                        . 'Meine <a href="#" style="float:none; margin:0;" onclick="displayOverlayInvoice();return false;">Einwilligung</a> '
                        . 'kann ich jederzeit mit Wirkung für die Zukunft widerrufen.';


                    $information['consentInstallment'] = 'Mit der Übermittlung der für die Abwicklung des Einkaufs '
                        . 'und einer Identitäts- und Bonitätsprüfung erforderlichen Daten an payolution bin ich einverstanden. '
                        . 'Meine <a href="#" style="float:none; margin:0;" onclick="displayOverlayInstallment();return false;">Einwilligung</a> '
                        . 'kann ich jederzeit mit Wirkung für die Zukunft widerrufen.';


                    $information['overlaycontent'] = $this->moptGetPayolutionAcceptanceText($companyname);


                    $information['sepaagreement'] = 'Hiermit erteile ich das <a target="_blank" href="' . self::MOPT_PAYONE_PAYOLUTION_SEPA_DE . '" '
                        . 'style="text-decoration: underline !important;">Sepa-Lastschriftmandat</a> ';

                }
                break;
        }

        return $information;
    }

    public function moptGetRatepayDeviceFingerprint()
    {

        if (!Shopware()->Session()->moptRatepayFingerprint) {
            $userData = Shopware()->Modules()->Admin()->sGetUserData();
            $fingerprint = $userData['billingaddress']['firstname'];
            $fingerprint .= $userData['billingaddress']['lastname'];
            $fingerprint .= microtime();
            $fingerprint = md5($fingerprint);
            Shopware()->Session()->moptRatepayFingerprint = $fingerprint;
        } else {
            $fingerprint = Shopware()->Session()->moptRatepayFingerprint;
        }
        return $fingerprint;
    }

    /**
     * @param string $billingCountry
     * @param Mopt_PayoneMain $moptPayoneMain
     * @return mixed
     */
    public function moptGetRatepayConfig($billingCountry, $moptPayoneMain = null)
    {
        $sTable = 's_plugin_mopt_payone_ratepay';

        if (empty($moptPayoneMain)) {
            $moptPayoneMain = Shopware()->Container()->get('MoptPayoneMain');
        }

        $basket = $moptPayoneMain->sGetBasket();
        $basketValue = $basket['AmountNumeric'];
        $currency = Shopware()->Shop()->getCurrency();
        $currencyId = $currency->getId();

        $sQuery = " SELECT
                        shopid
                    FROM
                        {$sTable}
                    WHERE 
                        '{$basketValue}' BETWEEN tx_limit_invoice_min AND tx_limit_invoice_max AND
                        currency_id = '{$currencyId}' AND
                        country_code_billing = '{$billingCountry}'";
        $sQuery .= " LIMIT 1;";
        $sShopId = Shopware()->Db()->fetchOne($sQuery);
        if ($sShopId) {
            $config = Shopware()->Models()->getRepository('Shopware\CustomModels\MoptPayoneRatepay\MoptPayoneRatepay')->getRatepayConfigByShopId($sShopId);
        }
        return $config;
    }

    protected function _isUtf8EncodingNeeded($sString)
    {
        if (preg_match('!!u', $sString)) {
            // this is utf-8
            return false;
        } else {
            // definitely not utf-8
            return true;
        }
    }

    public function moptGetPayolutionAcceptanceText($companyname)
    {
        $sUrl = self::MOPT_PAYONE_PAYOLUTION_CONSENT_DE . base64_encode($companyname);
        $sContent = file_get_contents($sUrl);
        $sPage = false;
        if (!empty($sContent) && stripos($sContent, 'payolution') !== false && stripos($sContent, '<header>') !== false) {
            //Parse content from HTML-body-tag from the given page
            $sRegex = "#<\s*?body\b[^>]*>(.*?)</body\b[^>]*>#s";
            preg_match($sRegex, $sContent, $aMatches);
            if (is_array($aMatches) && count($aMatches) > 1) {
                $sPage = $aMatches[1];
                //remove everything bevore the <header> tag ( a window.close link which wouldn't work in the given context )
                $sPage = substr($sPage, stripos($sPage, '<header>'));
            }
        }
        if (!$sPage) {
            $sPage = $this->_getFallbackText($companyname);
        }
        if ($this->_isUtf8EncodingNeeded($sPage)) {
            $sPage = utf8_encode($sPage);
        }
        return $sPage;
    }

    public function moptUpdateUserInformation($userId, $paymentData)
    {
        $user = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);

        if (Shopware::VERSION === '___VERSION___' || version_compare(Shopware::VERSION, '5.2.0', '>=')) {
            $billing = $user->getDefaultBillingAddress();
        } else {
            $billing = $user->getBilling();
        }

        if (isset($paymentData['formData']['mopt_payone__klarna_birthyear'])) {
            if (Shopware::VERSION === '___VERSION___' || version_compare(Shopware::VERSION, '5.2.0', '>=')) {
                $user->setBirthday($paymentData['formData']['mopt_payone__klarna_birthyear']
                    . '-' . $paymentData['formData']['mopt_payone__klarna_birthmonth']
                    . '-' . $paymentData['formData']['mopt_payone__klarna_birthday']);
                Shopware()->Models()->persist($user);

            } else {
                $billing->setBirthday($paymentData['formData']['mopt_payone__klarna_birthyear']
                    . '-' . $paymentData['formData']['mopt_payone__klarna_birthmonth']
                    . '-' . $paymentData['formData']['mopt_payone__klarna_birthday']);
            }
            $billing->setPhone($paymentData['formData']['mopt_payone__klarna_telephone']);
        }
        if (isset($paymentData['formData']['mopt_payone__payolution_birthdaydate'])) {
            if (Shopware::VERSION === '___VERSION___' || version_compare(Shopware::VERSION, '5.2.0', '>=')) {
                $user->setBirthday($paymentData['formData']['mopt_payone__payolution_birthdaydate']);
                Shopware()->Models()->persist($user);
            } else {
                $billing->setBirthday($paymentData['formData']['mopt_payone__payolution_birthdaydate']);
            }
        }

        if (isset($paymentData['formData']['mopt_payone__ratepay_invoice_birthdaydate'])) {
            if (Shopware::VERSION === '___VERSION___' || version_compare(Shopware::VERSION, '5.2.0', '>=')) {
                $user->setBirthday($paymentData['formData']['mopt_payone__ratepay_invoice_birthdaydate']);
                Shopware()->Models()->persist($user);
            } else {
                $billing->setBirthday($paymentData['formData']['mopt_payone__ratepay_invoice_birthdaydate']);
            }
        }

        if (isset($paymentData['formData']['mopt_payone__ratepay_invoice_telephone'])) {
            $billing->setPhone($paymentData['formData']['mopt_payone__ratepay_invoice_telephone']);
        }

        if (isset($paymentData['formData']['mopt_payone__ratepay_installment_birthdaydate'])) {
            if (Shopware::VERSION === '___VERSION___' || version_compare(Shopware::VERSION, '5.2.0', '>=')) {
                $user->setBirthday($paymentData['formData']['mopt_payone__ratepay_installment_birthdaydate']);
                Shopware()->Models()->persist($user);
            } else {
                $billing->setBirthday($paymentData['formData']['mopt_payone__ratepay_installment_birthdaydate']);
            }
        }

        if (isset($paymentData['formData']['mopt_payone__ratepay_installment_telephone'])) {
            $billing->setPhone($paymentData['formData']['mopt_payone__ratepay_installment_telephone']);
        }

        if (isset($paymentData['formData']['mopt_payone__ratepay_direct_debit_birthdaydate'])) {
            if (Shopware::VERSION === '___VERSION___' || version_compare(Shopware::VERSION, '5.2.0', '>=')) {
                $user->setBirthday($paymentData['formData']['mopt_payone__ratepay_direct_debit_birthdaydate']);
                Shopware()->Models()->persist($user);
            } else {
                $billing->setBirthday($paymentData['formData']['mopt_payone__ratepay_direct_debit_birthdaydate']);
            }
        }

        if (isset($paymentData['formData']['mopt_payone__ratepay_direct_debit_telephone'])) {
            $billing->setPhone($paymentData['formData']['mopt_payone__ratepay_direct_debit_telephone']);
        }

        if (isset($paymentData['formData']['mopt_payone__payone_safe_invoice_birthdaydate'])) {
            if (Shopware::VERSION === '___VERSION___' || version_compare(Shopware::VERSION, '5.2.0', '>=')) {
                $user->setBirthday($paymentData['formData']['mopt_payone__payone_safe_invoice_birthdaydate']);
                Shopware()->Models()->persist($user);
            } else {
                $billing->setBirthday($paymentData['formData']['mopt_payone__payone_safe_invoice_birthdaydate']);
            }
        }

        Shopware()->Models()->persist($billing);
        Shopware()->Models()->flush();
    }

    /**
     * retrieve multilang errorcode based on context and errorcode
     *
     * @param string|bool $context
     * @param string|bool $errorCode
     * @return string
     */
    public function moptGetErrorMessageFromErrorCodeViaSnippet($context = false, $errorCode = false)
    {
        $namespace = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages');
        if ($context) {
            $generalErrorMessage = $namespace->get($context . 'ErrorMessage');
        } else {
            $generalErrorMessage = $namespace->get('generalErrorMessage', 'Es ist ein Fehler aufgetreten');
        }

        if ($errorCode) {
            return $namespace->get('errorMessage' . $errorCode, $generalErrorMessage, true);
        } else {
            return $generalErrorMessage;
        }
    }

    /**
     * collect and return predefinded possible creditcard check error messages
     *
     * @return array
     */
    public function getCreditCardCheckErrorMessages()
    {
        $errorMessages = array();
        $namespace = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/errorMessages');
        $errorMessages['general'] = $namespace->get(
            'creditCardCheckerrorMessage',
            'Bitte überprüfen Sie die Angaben auf der Karte.'
        );
        $errorMessages['1076'] = $namespace->get(
            'errorMessage1076',
            'Ungültiger Kartentyp. Bitte überprüfen Sie die Angaben auf der Karte.'
        );
        $errorMessages['1078'] = $namespace->get(
            'errorMessage1078',
            'Ungültige Kartennummer. Bitte überprüfen Sie die Angaben auf der Karte.'
        );
        $errorMessages['33'] = $namespace->get(
            'errorMessage33',
            'Verfallsdatum ungültig. Bitte überprüfen Sie die Angaben auf der Karte.'
        );

        return $errorMessages;
    }

    /**
     * get action name from paymentname
     *
     * @param string $paymentShortName
     * @return string|boolean
     */
    public function getActionFromPaymentName($paymentShortName)
    {
        if ($this->isPayoneCreditcard($paymentShortName)) {
            return 'creditcard';
        }

        if ($this->isPayoneInstantBankTransfer($paymentShortName)) {
            return 'instanttransfer';
        }

        if ($this->isPayonePaypal($paymentShortName)) {
            return 'paypal';
        }

        if ($this->isPayoneDebitnote($paymentShortName)) {
            return 'debitnote';
        }

        if ($this->isPayoneInvoice($paymentShortName) || $this->isPayonePayInAdvance($paymentShortName)) {
            return 'standard';
        }

        if ($this->isPayoneSafeInvoice($paymentShortName)) {
            return 'standard';
        }

        if ($this->isPayoneCashOnDelivery($paymentShortName)) {
            return 'cashondel';
        }

        if ($this->isPayoneKlarna($paymentShortName)) {
            return 'klarna';
        }

        if ($this->isPayonePayolutionDebitNote($paymentShortName)) {
            return 'payolutiondebit';
        }

        if ($this->isPayonePayolutionInvoice($paymentShortName)) {
            return 'payolutioninvoice';
        }

        if ($this->isPayonePayolutionInstallment($paymentShortName)) {
            return 'payolutioninstallment';
        }

        if ($this->isPayoneRatepayInvoice($paymentShortName)) {
            return 'ratepayinvoice';
        }

        if ($this->isPayoneRatepayInstallment($paymentShortName)) {
            return 'ratepayinstallment';
        }

        if ($this->isPayoneRatepayDirectDebit($paymentShortName)) {
            return 'ratepaydirectdebit';
        }

        if ($this->isPayoneFinance($paymentShortName)) {
            return 'finance';
        }

        if ($this->isPayoneCreditcardIframe($paymentShortName)) {
            return 'creditcardIframe';
        }

        if ($this->isPayoneBarzahlen($paymentShortName)) {
            return 'barzahlen';
        }

        if ($this->isPayonePaydirekt($paymentShortName)) {
            return 'paydirekt';
        }
        if ($this->isPayoneAlipay($paymentShortName)) {
            return 'alipay';
        }
        if ($this->isPayoneMasterpass($paymentShortName)) {
            return 'masterpass';
        }
        return false;
    }

    /**
     * check if current payment method is paypal and paypal ecs is activated
     *
     * @param Mopt_PayoneMain $payoneMain
     * @param array $paymentMethod
     * @return boolean
     */
    public function isPayPalEcsActive($payoneMain, $paymentMethod)
    {
        if (!$this->isPayonePaypal($paymentMethod['name'])) {
            return false;
        }

        $config = $payoneMain->getPayoneConfig($paymentMethod['id']);
        return (bool)$config['paypalEcsActive'];
    }

    /**
     * group credit cards to single payment method creditcard
     *
     * @param array $paymentMeans
     * @return bool|array
     */
    public function groupCreditcards($paymentMeans)
    {
        $firstHit = 'not_set';
        $creditCardData = array();

        foreach ($paymentMeans as $key => $paymentmean) {
            if ($this->isPayoneCreditcardNotGrouped($paymentmean['name'])) {
                if ($firstHit === 'not_set') {
                    $firstHit = $key;
                }

                $creditCard = array();
                $creditCard['id'] = $paymentmean['id'];
                $creditCard['name'] = $paymentmean['name'];
                $creditCard['description'] = $paymentmean['description'];

                $creditCardData[] = $creditCard;

                if ($firstHit != $key) {
                    unset($paymentMeans[$key]);
                }
            }
        }

        // don't assign anything if no creditcard was found
        if ($firstHit === 'not_set') {
            return false;
        }

        $snippetObject = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/payment');
        $paymentMeans[$firstHit]['id'] = 'mopt_payone_creditcard';
        $paymentMeans[$firstHit]['name'] = 'mopt_payone_creditcard';
        $paymentMeans[$firstHit]['description'] = $snippetObject->get('PaymentMethodCreditCard', 'Kreditkarte', true);
        $paymentMeans[$firstHit]['mopt_payone_credit_cards'] = $creditCardData;

        return $paymentMeans;
    }

    /**
     * Fetches and returns amazon payment instance.
     *
     * @return \Shopware\Models\Payment\Payment
     */
    public function getPaymentAmazonPay()
    {
        $paymentAmazonPay = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment')->findOneBy(
            ['name' => 'mopt_payone__ewallet_amazon_pay']
        );
        return $paymentAmazonPay;
    }

    /**
     * checks if AmazonPay is enabled
     *
     * @return bool
     */
    public function isAmazonPayActive()
    {
        $paymentAmazonPay = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment')->findOneBy(
            ['name' => 'mopt_payone__ewallet_amazon_pay']
        );
        return $paymentAmazonPay->getActive();
    }

    /**
     * checks if Masterpass is enabled
     *
     * @return bool
     */
    public function isMasterpassActive()
    {
        $paymentMasterpass = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment')->findOneBy(
            ['name' => 'mopt_payone__ewallet_masterpass']
        );
        return $paymentMasterpass->getActive();
    }

}
