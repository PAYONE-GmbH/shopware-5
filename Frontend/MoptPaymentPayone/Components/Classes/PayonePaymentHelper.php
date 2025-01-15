<?php

use Shopware\Components\Routing\Router;
use Shopware\Models\Customer\Address;
use Shopware\Models\Customer\Customer;
use Shopware\Models\Payment\Payment;

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
            <strong>Ergänzende Hinweise zur Datenschutzerklärung für Kauf auf Rechnung, per Ratenzahlung und direkter SEPA-Lastschrift von **company** (im Folgenden: \"wir\")</strong></br>
            <span><i>(Stand: 16.10.2020)</i></span>
        </header>
        <ol>
            <li><p>Durch die Auswahl eines Kaufs auf Rechnung, per Ratenzahlung oder direkter SEPA-Lastschrift, stimmen Sie den Datenschutzbestimmungen der payolution GmbH und der Weiterverarbeitung Ihrer persönlichen Daten zu. Diese Bestimmungen sind nachstehend ausschließlich zu Informationszwecken erneut aufgeführt.</p></li>
            <li><p>Wenn Sie die Zahlung auf Rechnung, per Ratenzahlung oder direkter SEPA-Lastschrift auswählen, werden Ihre für die Bearbeitung dieser Zahlungsmethode erforderlichen persönlichen Informationen (Vorname, Nachname, Anschrift, E-Mail-Adresse, Telefonnummer, Geburtsdatum, IP-Adresse, Geschlecht) zusammen mit den für die Ausführung der Transaktion erforderlichen Daten (Artikel, Rechnungsbetrag, Zinsen, Ratenzahlungen, Fälligkeitsdatum, Gesamtbetrag, Rechnungsnummer, Steuerbetrag, Währung, Bestelldatum und -uhrzeit) an die payolution GmbH zum Zwecke der Risikoeinschätzung im Rahmen seiner regulatorischen Verpflichten weitergeleitet.</p></li>
            <li>
                <p>Zur Identitäts- und/oder Solvenzprüfung des Kunden werden Abfragen und Auskunftsersuchen an öffentlich zugängliche Datenbanken und Kreditauskunfteien weitergeleitet. Es können Informationen, und falls erforderlich, Kreditauskünfte auf Grundlage statistischer Methoden bei den folgenden Anbietern abgefragt werden:</p>
                <ul>
                    <li>CRIF GmbH, Diefenbachgasse 35, 11 50 Wien, Österreich</li>
                    <li>CRIF AG, Hagenholzstrasse 81, 8050 Zürich, Schweiz</li>
                    <li>CRIF Bürgel GmbH, Radlkoferstraße 2, 81373 München, Deutschland</li>
                    <li>SCHUFA Holding AG, Kormoranweg 5, 65201 Wiesbaden, Deutschland</li>
                    <li>KSV1870 Information GmbH, Wagenseilgasse 7, 1100 Wien, Österreich</li>
                    <li>Creditreform Boniversum GmbH, Hellersbergstr. 11, 41460 Neuss, Deutschland</li>
                    <li><a href='https://finance.arvato.com/icdinfoblatt' rel='nofollow noopener'>infoscore Consumer Data GmbH, Rheinstrasse 99, 76532 Baden-Baden, Deutschland</a></li>
                    <li>ProfileAddress Direktmarketing GmbH, Altmannsdorfer Strasse 311, 1230 Wien, Österreich</li>
                    <li>Emailage LTD, 1 Fore Street Ave, London, EC2Y 5EJ, Vereinigtes Königreich</li>
                    <li>ThreatMetrix, The Base 3/F, Tower C, Evert van de Beekstraat 1, 1118 CL Schiphol, Niederlande</li>
                    <li>payolution GmbH, Columbuscenter, Columbusplatz 7-8, 1100 Wien, Österreich</li>
                    <li>Universum Business GmbH, Hanauer Landstr. 164, 60314 Frankfurt am Main, Deutschland</li>
                </ul>
                <p>Die payolution GmbH wird Ihre Angaben zur Bankverbindung (insbesondere Bankleitzahl und Kontonummer) zum Zwecke der Kontonummernprüfung an die SCHUFA Holding AG übermitteln. Die SCHUFA prüft anhand dieser Daten zunächst, ob die von Ihnen gemachten Angaben zur Bankverbindung plausibel sind. Die SCHUFA überprüft, ob die zur Prüfung verwendeten Daten ggf. in Ihrem Datenbestand gespeichert sind und übermittelt sodann das Ergebnis der Überprüfung an payolution zurück. Ein weiterer Datenaustausch wie die Bekanntgabe von Bonitätsinformationen oder eine Übermittlung abweichender Bankverbindungsdaten sowie Speicherung Ihrer Daten im SCHUFA-Datenbestand finden im Rahmen der Kontonummernprüfung nicht statt. Es wird aus Nachweisgründen allein die Tatsache der Überprüfung der Bankverbindungsdaten bei der SCHUFA gespeichert.</p>
                <p>Im Fall von vertragswidrigem Verhalten (z. B. Bestehen unstrittiger Forderungen) ist die payolution GmbH ebenfalls zur Speicherung, Verarbeitung, Verwendung von Daten und deren Übermittlung an die o. g. Kreditauskunfteien berechtigt.</p>
            </li>
            <li><p>Gemäß den Bestimmungen des Bürgerlichen Gesetzbuches über Finanzierungshilfen zwischen Händlern und Konsumenten sind wir gesetzlich zur Prüfung Ihrer Kreditwürdigkeit verpflichtet.</p></li>
            <li><p>Im Falle eines Kaufs auf Rechnung, per Ratenzahlung oder direkter SEPA-Lastschrift, werden wir Daten zu den Einzelheiten des entsprechenden Zahlungsvorgangs (Ihre Personendaten, Kaufpreis, Bedingungen des Zahlungsvorgangs, Beginn der Zahlung) und die Vertragsbedingungen (z. B. vorzeitige Zahlung, Verlängerung der Vertragslaufzeit, erfolgte Zahlungen) an die payolution GmbH übermitteln. Nach Abtretung der Kaufpreisforderung wird das Bankinstitut, dem die Forderung abgetreten wurde, die genannte Datenübermittlung vornehmen. Wir und/oder das Bankinstitut sind entsprechend der Abtretung der Kaufpreisforderung ebenfalls zur Meldung von Daten über vertragswidriges Verhalten (z. B. Beendigung der Zahlungsvereinbarung, Zwangsvollstreckungsmaßnahmen) an die payolution GmbH angewiesen. Gemäß den Datenschutzbestimmungen erfolgen diese Meldungen ausschließlich, wenn diese zur Sicherstellung des rechtmäßigen Interesses der Vertragspartner der payolution GmbH oder der Allgemeinheit erforderlich sind und Ihre rechtmäßigen Interessen dadurch nicht beeinträchtigt werden. Die payolution GmbH wird die Daten speichern, um seinen Vertragspartnern, die Konsumenten Ratenzahlungen oder sonstige Kreditvereinbarungen im gewerblichen Rahmen gewähren, Informationen zur Einschätzung der Kreditwürdigkeit von Kunden zur Verfügung stellen zu können. Mit der payolution GmbH in einem Vertragsverhältnis stehende gewerbliche Inkassounternehmen können Adressinformationen zur Ermittlung von Debitoren zur Verfügung gestellt werden. Die payolution GmbH ist dazu angehalten, seinen Vertragspartnern nur dann Daten zu übermitteln, wenn ein glaubwürdiges und rechtmäßiges Interesse an der Datenübermittlung besteht. Die payolution GmbH ist dazu angehalten, ausschließlich objektive Daten ohne Spezifikation an das entsprechende Bankinstitut zu übermitteln. Informationen über subjektive Werteinschätzungen und persönliches Einkommen sind in den von der payolution GmbH zur Verfügung gestellten Informationen nicht enthalten.</p></li>
            <li><p>Sie können Ihre Zustimmung zur Datenverarbeitung zum Zwecke der Auftragsabwicklung jederzeit widerrufen. Die o. g. gesetzlichen Verpflichtungen zur Prüfung Ihrer Kreditwürdigkeit bleiben von solchen Widerrufen unberührt.</p></li>
            <li><p>Sie sind uns gegenüber zur Angabe von ausschließlich wahrheitsgemäßen und korrekten Informationen verpflichtet.</p></li>
            <li><p>Weitere Informationen über die Verarbeitung Ihrer persönlichen Daten finden Sie in der vollständigen Datenschutzrichtlinie hier: <a href='https://www.unzer.com/de/privacy-payolution-consumers/' rel='nofollow noopener'>https://www.unzer.com/de/privacy-payolution-consumers/</a></p></li>
            <li><p>Sie können ebenfalls den Sachbearbeiter für Datenschutz der Unzer Group unter der folgenden Adresse kontaktieren:</p></li>
        </ol>

        <footer>Sachbearbeiter für Datenschutz<br />
            datenschutz@payolution.com<br />
            payolution GmbH<br />
            Columbusplatz 7-8<br />
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
                case 'mopt_payone__cc_chi':
                    $creditCard['short'] = 'P';
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
     * delete saved payment data
     *
     * @param $userId
     * @throws Zend_Db_Adapter_Exception
     */
    public function deleteCreditcardPaymentData($userId)
    {
        if ($userId != null) {
            $sql = 'SELECT userId FROM s_plugin_mopt_payone_creditcard_payment_data WHERE userId = ' . $userId;
            $result = Shopware()->Db()->fetchOne($sql);
            if ($result) {
                $sql = 'DELETE FROM s_plugin_mopt_payone_creditcard_payment_data WHERE userId = ' . $userId;
                Shopware()->Db()->exec($sql);
            }
            // also remove initial payment flag
            $this->updateUserCreditcardInitialPaymentSuccess($userId, false);
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
     * save payment data
     *
     * @param $userId
     * @param $creditcardPaymentData
     * @throws Zend_Db_Adapter_Exception
     */
    public function saveCreditcardPaymentData($userId, $creditcardPaymentData)
    {
        $sql = 'REPLACE INTO `s_plugin_mopt_payone_creditcard_payment_data`' .
            '(`userId`, `moptCreditcardPaymentData`) VALUES (?,?)';
        $creditcardPaymentData = serialize($creditcardPaymentData['formData']);
        Shopware()->Db()->query($sql, array($userId, $creditcardPaymentData));
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
        if (preg_match('#mopt_payone__cc#', $paymentName) || $paymentName == 'mopt_payone_creditcard') {
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
    public function isPayonePaypalV2($paymentName)
    {
        return preg_match('#mopt_payone__ewallet_paypalv2$#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone paypal payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayonePaypalExpressv2($paymentName)
    {
        return preg_match('#mopt_payone__ewallet_paypal_expressv2#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone paypal payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayonePaypal($paymentName)
    {
        if ($this->isPayonePaypalExpress($paymentName)) {
            return false;
        }
        return preg_match('#mopt_payone__ewallet_paypal$#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone paypal payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayonePaypalExpress($paymentName)
    {
        return preg_match('#mopt_payone__ewallet_paypal_express$#', $paymentName) ? true : false;
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
     * check if given payment name is old payone klarna payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneKlarna_old($paymentName)
    {
        return preg_match('#mopt_payone__fin_klarna_old#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is any of the existing payone klarna payment, except Klarna OLD
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneKlarna($paymentName)
    {
        return $this->isPayoneKlarnaInstallments($paymentName)
            || $this->isPayoneKlarnaInvoice($paymentName)
            || $this->isPayoneKlarnaDirectDebit($paymentName);
    }

    /**
     * check if given payment name is the virtual grouped klarna payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneKlarnaGrouped($paymentName)
    {
        return preg_match('#mopt_payone_klarna#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone klarna installments payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneKlarnaInstallments($paymentName)
    {
        return preg_match('#mopt_payone__fin_kis_klarna_installments#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone klarna invoice payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneKlarnaInvoice($paymentName)
    {
        return preg_match('#mopt_payone__fin_kiv_klarna_invoice#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone klarna direct debit payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneKlarnaDirectDebit($paymentName)
    {
        return preg_match('#mopt_payone__fin_kdd_klarna_direct_debit#', $paymentName) ? true : false;
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
    public function isPayoneRatePay($paymentName)
    {
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
     * check if given payment name is payone trustly payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneTrustly($paymentName)
    {
        return preg_match('#mopt_payone__ibt_trustly#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone secured invoice
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneSecuredInvoice($paymentName)
    {
        return preg_match('#mopt_payone__fin_payone_secured_invoice#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone secured invoice
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneSecuredInstallments($paymentName)
    {
        return preg_match('#mopt_payone__fin_payone_secured_installment#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone secured directdebit
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneSecuredDirectdebit($paymentName)
    {
        return preg_match('#mopt_payone__fin_payone_secured_directdebit#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone alipay payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneWechatpay($paymentName)
    {
        return preg_match('#mopt_payone__ewallet_wechatpay#', $paymentName) ? true : false;
    }

    /**
     * check if given payment name is payone applepay payment
     *
     * @param string $paymentName
     * @return boolean
     */
    public function isPayoneApplepay($paymentName)
    {
        return preg_match('#mopt_payone__ewallet_applepay#', $paymentName) ? true : false;
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

        if ($this->isPayoneTrustly($paymentName)) {
            return Payone_Api_Enum_OnlinebanktransferType::TRUSTLY;
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

        $shopCountries = Shopware()->Container()->get('modules')->Admin()->sGetCountryList();
        $keys = array_column($shopCountries, 'id');
        $values = array_values($shopCountries);
        $shopCountries = array_combine($keys, $values);
        foreach ($paymentCountries as $index => $paymentCountry) {
            if (array_key_exists($paymentCountry['countryID'], $shopCountries)) {
                $paymentCountries[$index]['countryname'] = $shopCountries[$paymentCountry['countryID']]['countryname'];
            }
        }

        return $paymentCountries;
    }

    public function isPaymentAssignedToSubshop($paymentId, $subshopID)
    {
        $sql = 'SELECT subshopID '
            . 'FROM s_core_paymentmeans_subshops '
            . 'WHERE s_core_paymentmeans_subshops.paymentID = ?;';
        $assignedShops = Shopware()->Db()->fetchAll($sql, $paymentId);

        // paymentID was not found = no restrictions
        if (count($assignedShops) == 0) {
            return true;
        }

        if (in_array((string)$subshopID, array_column($assignedShops, 'subshopID'))) {
            return true;
        }

        return false;
    }

    public function getCountryIdFromIso($countryIso)
    {
        /** @var  $entityManager \Shopware\Components\Model\ModelManager */
        $entityManager = Shopware()->Container()->get('models');
        $country = $entityManager->getRepository('Shopware\Models\Country\Country')->findOneBy(array('iso' => $countryIso));
        return $country;
    }

    /**
     * @param int $id
     * @return object|\Shopware\Models\Country\Country|null
     */
    public function getCountryFromId($id)
    {
        /** @var  $entityManager \Shopware\Components\Model\ModelManager */
        $entityManager = Shopware()->Container()->get('models');
        $country = $entityManager->getRepository('Shopware\Models\Country\Country')->findOneBy(array('id' => $id));
        return $country;
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

        $storeId = ($storeId) ? $storeId : '5223'; // use storeid 5223 as fallback

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
                    $information['consentDebit'] = 'Mit der Übermittlung der für die Abwicklung des Lastschriftkaufes '
                        . 'und einer Identitäts- und Bonitätsprüfung erforderlichen Daten an die payolution GmbH, Columbusplatz 7-8, 1120 Wien bin ich einverstanden. '
                        . 'Meine <a href="#" style="float:none; margin:0;" onclick="displayOverlayDebit();return false;">Einwilligung</a> '
                        . 'kann ich jederzeit mit Wirkung für die Zukunft widerrufen.';

                    $information['consentInvoice'] = 'Mit der Übermittlung der für die Abwicklung des Rechnungskaufes '
                        . 'und einer Identitäts- und Bonitätsprüfung erforderlichen Daten an die payolution GmbH, Columbusplatz 7-8, 1120 Wien bin ich einverstanden. '
                        . 'Meine <a href="#" style="float:none; margin:0;" onclick="displayOverlayInvoice();return false;">Einwilligung</a> '
                        . 'kann ich jederzeit mit Wirkung für die Zukunft widerrufen.';


                    $information['consentInstallment'] = 'Mit der Übermittlung der für die Abwicklung des Ratenkaufes '
                        . 'und einer Identitäts- und Bonitätsprüfung erforderlichen Daten an die payolution GmbH, Columbusplatz 7-8, 1120 Wien bin ich einverstanden. '
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
     * @return mixed
     */
    public function moptGetRatepayConfig($billingCountry)
    {
        $sTable = 's_plugin_mopt_payone_ratepay';
        $currency = Shopware()->Shop()->getCurrency();
        $currencyId = $currency->getId();

        $sQuery = " SELECT
                        shopid
                    FROM
                        {$sTable}
                    WHERE 
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
        /** @var Customer $user */
        $user = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);

        /** @var Address $billing */
        $billing = $user->getDefaultBillingAddress();

        if (isset($paymentData['formData']['mopt_payone__klarna_birthyear'])) {
            $user->setBirthday($paymentData['formData']['mopt_payone__klarna_birthyear']
                . '-' . $paymentData['formData']['mopt_payone__klarna_birthmonth']
                . '-' . $paymentData['formData']['mopt_payone__klarna_birthday']);
            Shopware()->Models()->persist($user);

            $billing->setPhone($paymentData['formData']['mopt_payone__klarna_telephone']);
        }

        if (isset($paymentData['formData']['mopt_payone__payolution_birthdaydate'])) {
            $user->setBirthday($paymentData['formData']['mopt_payone__payolution_birthdaydate']);
            Shopware()->Models()->persist($user);
        }

        if (isset($paymentData['formData']['mopt_payone__ratepay_invoice_birthdaydate'])) {
            $user->setBirthday($paymentData['formData']['mopt_payone__ratepay_invoice_birthdaydate']);
            Shopware()->Models()->persist($user);
        }

        if (isset($paymentData['formData']['mopt_payone__ratepay_invoice_telephone'])) {
            $billing->setPhone($paymentData['formData']['mopt_payone__ratepay_invoice_telephone']);
        }

        if (isset($paymentData['formData']['mopt_payone__klarna_personalId'])) {
            $user->getAttribute()->setMoptPayoneKlarnaPersonalid($paymentData['formData']['mopt_payone__klarna_personalId']);
        }

        if (isset($paymentData['formData']['mopt_payone__ratepay_installment_birthdaydate'])) {
            $user->setBirthday($paymentData['formData']['mopt_payone__ratepay_installment_birthdaydate']);
            Shopware()->Models()->persist($user);
        }

        if (isset($paymentData['formData']['mopt_payone__ratepay_installment_telephone'])) {
            $billing->setPhone($paymentData['formData']['mopt_payone__ratepay_installment_telephone']);
        }

        if (isset($paymentData['formData']['mopt_payone__ratepay_direct_debit_birthdaydate'])) {
            $user->setBirthday($paymentData['formData']['mopt_payone__ratepay_direct_debit_birthdaydate']);
            Shopware()->Models()->persist($user);
        }

        if (isset($paymentData['formData']['mopt_payone__ratepay_direct_debit_telephone'])) {
            $billing->setPhone($paymentData['formData']['mopt_payone__ratepay_direct_debit_telephone']);
        }

        if (isset($paymentData['formData']['mopt_payone__payone_safe_invoice_birthdaydate'])) {
            $user->setBirthday($paymentData['formData']['mopt_payone__payone_safe_invoice_birthdaydate']);
            Shopware()->Models()->persist($user);
        }

        if (isset($paymentData['formData']['mopt_payone__payone_secured_invoice_birthdaydate'])) {
            $user->setBirthday($paymentData['formData']['mopt_payone__payone_secured_invoice_birthdaydate']);
            Shopware()->Models()->persist($user);
        }

        if (isset($paymentData['formData']['mopt_payone__payone_secured_installment_birthdaydate'])) {
            $user->setBirthday($paymentData['formData']['mopt_payone__payone_secured_installment_birthdaydate']);
            Shopware()->Models()->persist($user);
        }

        if (isset($paymentData['formData']['mopt_payone__payone_secured_directdebit_birthdaydate'])) {
            $user->setBirthday($paymentData['formData']['mopt_payone__payone_secured_directdebit_birthdaydate']);
            Shopware()->Models()->persist($user);
        }

        if (isset($paymentData['formData']['mopt_payone__payone_secured_invoice_telephone'])) {
            $billing->setPhone($paymentData['formData']['mopt_payone__payone_secured_invoice_telephone']);
        }

        if (isset($paymentData['formData']['mopt_payone__payone_secured_installment_telephone'])) {
            $billing->setPhone($paymentData['formData']['mopt_payone__payone_secured_installment_telephone']);
        }

        if (isset($paymentData['formData']['mopt_payone__payone_secured_directdebit_telephone'])) {
            $billing->setPhone($paymentData['formData']['mopt_payone__payone_secured_directdebit_telephone']);
        }

        if (isset($paymentData['formData']['mopt_payone__secured_invoice_vatid'])) {
            $billing->setVatId($paymentData['formData']['mopt_payone__secured_invoice_vatid']);
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
        $errorMessages['CustomExpiry'] = $namespace->get(
            'errorMessageCustomExpiry',
            'Unzureichendes Gültigkeitsdatum. Ihre Kreditkarte unterschreitet die vom Händler hinterlegte Mindestgültigkeit.'
        );
        $errorMessages['33'] = $namespace->get(
            'errorMessage33',
            'Ungültiges Verfallsdatum. Bitte überprüfen Sie die Angaben auf der Karte.'
        );
        $errorMessages['877'] = $namespace->get(
            'errorMessage877',
            'Ungültige Kartennummer. Bitte überprüfen Sie die Angaben auf der Karte.'
        );
        $errorMessages['880'] = $namespace->get(
            'errorMessage880',
            'Kartentyp passt nicht zur Kartennummer. Bitte überprüfen Sie die Angaben auf der Karte.'
        );
        $errorMessages['1041'] = $namespace->get(
            'errorMessage1041',
            'Parameter successurl fehlt oder ist fehlerhaft.'
        );
        $errorMessages['1076'] = $namespace->get(
            'errorMessage1076',
            'Ungültiger Kartentyp. Bitte überprüfen Sie die Angaben auf der Karte.'
        );
        $errorMessages['1077'] = $namespace->get(
            'errorMessage1077',
            'Ungültiges Verfallsdatum. Bitte überprüfen Sie die Angaben auf der Karte.'
        );
        $errorMessages['1078'] = $namespace->get(
            'errorMessage1078',
            'Ungültige Kartennummer. Bitte überprüfen Sie die Angaben auf der Karte.'
        );
        $errorMessages['1079'] = $namespace->get(
            'errorMessage1079',
            'Ungültiges Verfallsdatum. Bitte überprüfen Sie die Angaben auf der Karte.'
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

        if ($this->isPayonePaypalExpressv2($paymentShortName)) {
            return 'paypalexpressv2';
        }

        if ($this->isPayonePaypalExpress($paymentShortName)) {
            return 'paypalexpress';
        }

        if ($this->isPayonePaypalv2($paymentShortName)) {
            return 'paypalv2';
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

        if ($this->isPayoneKlarna_old($paymentShortName)) {
            return 'klarnaold';
        }

        if ($this->isPayoneKlarnaInstallments($paymentShortName)) {
            return 'klarnainstallments';
        }

        if ($this->isPayoneKlarnaInvoice($paymentShortName)) {
            return 'klarnainvoice';
        }

        if ($this->isPayoneKlarnaDirectDebit($paymentShortName)) {
            return 'klarnadirectdebit';
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

        if ($this->isPayoneSecuredInvoice($paymentShortName)) {
            return 'payonesecuredinvoice';
        }

        if ($this->isPayoneSecuredInstallments($paymentShortName)) {
            return 'payonesecuredinstallments';
        }

        if ($this->isPayoneSecuredDirectdebit($paymentShortName)) {
            return 'payonesecureddirectdebit';
        }

        if ($this->isPayoneFinance($paymentShortName)) {
            return 'finance';
        }

        if ($this->isPayoneBarzahlen($paymentShortName)) {
            return 'barzahlen';
        }

        if ($this->isPayoneAlipay($paymentShortName)) {
            return 'alipay';
        }
        if ($this->isPayoneWechatpay($paymentShortName)) {
            return 'wechatpay';
        }
        if ($this->isPayoneTrustly($paymentShortName)) {
            return 'trustly';
        }
        if ($this->isPayoneApplepay($paymentShortName)) {
            return 'applepay';
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
        if (!$this->isPayonePaypalExpress($paymentMethod['name'])) {
            return false;
        }

        $config = $payoneMain->getPayoneConfig($paymentMethod['id']);
        return (bool)$config['paypalEcsActive'];
    }

    /**
     * check if current payment method is paypal and paypal ecs is activated
     *
     * @param Mopt_PayoneMain $payoneMain
     * @param array $paymentMethod
     * @return boolean
     */
    public function isPayPalv2EcsActive($payoneMain, $paymentMethod)
    {
        if (!$this->isPayonePaypalExpressv2($paymentMethod['name'])) {
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
        $shortCodes = array(
            'mopt_payone__cc_visa' => 'v',
            'mopt_payone__cc_mastercard' => 'm',
            'mopt_payone__cc_american_express' => 'a',
            'mopt_payone__cc_carte_blue' => 'b',
            'mopt_payone__cc_diners_club' => 'd',
            'mopt_payone__cc_jcb' => 'j',
            'mopt_payone__cc_maestro_international' => 'o',
        );

        foreach ($paymentMeans as $key => $paymentmean) {
            if ($this->isPayoneCreditcardNotGrouped($paymentmean['name'])) {
                if ($firstHit === 'not_set') {
                    $firstHit = $key;
                }

                $creditCard = array();
                $creditCard['id'] = $paymentmean['id'];
                $creditCard['name'] = $paymentmean['name'];
                $creditCard['description'] = $paymentmean['description'];
                /** @var Mopt_PayoneMain $moptPayoneMain */
                $moptPayoneMain = Shopware()->Container()->get('plugins')->Frontend()->MoptPaymentPayone()->Application()->MoptPayoneMain();
                $config = $moptPayoneMain->getPayoneConfig($paymentmean['id']);
                $defaultDescription = '<div class="payone_additionalDescriptions" id="default_additionalDescription" style="display:block">' . $config['creditcardDefaultDescription'] . '</div>';
                $creditCardData[] = $creditCard;
                $creditCardDescriptions['default'] = $defaultDescription;
                $creditCardDescriptions[$creditCard['name']] = '<div class="payone_additionalDescriptions" id="' . $shortCodes[$creditCard['name']] . '_additionalDescription" style="display:none">' . $paymentmean['additionaldescription'] . '</div>';

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
        $paymentMeans[$firstHit]['additionaldescription'] = implode('', $creditCardDescriptions);

        return $paymentMeans;
    }

    /**
     * group Klarna payments to single payment method Klarna, except Klarna OLD
     *
     * @param array $paymentMeans
     * @return array
     */
    public function groupKlarnaPayments($paymentMeans)
    {
        $snippetObject = Shopware()->Snippets()->getNamespace('frontend/MoptPaymentPayone/payment');
        $paymentGroupData = [
            'id' => 'mopt_payone_klarna',
            'name' => 'mopt_payone_klarna',
            'description' => $snippetObject->get('PaymentMethodKlarna', 'PAYONE Klarna Payments', true),
            'key' => 'mopt_payone_klarna_payments',
        ];

        return $this->groupPayments(array($this, 'isPayoneKlarna'), $paymentMeans, $paymentGroupData);
    }

    /**
     * group payment methods to single payment method
     *
     * @param callable $paymentCheckCallback callback, to check a payment, whether it belongs to a defined group of payments
     * @param array $paymentMeans
     * @param array $paymentGroupData an array with an id, a name, a description and a key
     *
     * @return array
     */
    protected function groupPayments($paymentCheckCallback, $paymentMeans, $paymentGroupData)
    {
        $firstHit = 'not_set';
        $moreThanOnePayments = false;
        $payments = array();

        $paymentMeans = $this->filterB2bPayments($paymentMeans);
        foreach ($paymentMeans as $key => $paymentmean) {
            if ($paymentCheckCallback($paymentmean['name'])) {
                if ($firstHit === 'not_set') {
                    $firstHit = $key;
                }

                $payment = array();
                $payment['id'] = $paymentmean['id'];
                $payment['name'] = $paymentmean['name'];
                $payment['description'] = $paymentmean['description'];

                $payments[] = $payment;

                if ($firstHit != $key) {
                    $moreThanOnePayments = true;
                    unset($paymentMeans[$key]);
                }
            }
        }

        // don't assign anything if no payment to be grouped was found
        if ($firstHit === 'not_set') {
            return $paymentMeans;
        }
        if (!$moreThanOnePayments) {
            return $paymentMeans;
        }

        $paymentMeans[$firstHit]['id'] = $paymentGroupData['id'];
        $paymentMeans[$firstHit]['name'] = $paymentGroupData['name'];
        $paymentMeans[$firstHit]['description'] = $paymentGroupData['description'];
        $paymentMeans[$firstHit][$paymentGroupData['key']] = $payments;

        return $paymentMeans;
    }

    /**
     * Returns the Klarna financingtype by name
     *
     * @param $paymentName
     *
     * @return string
     */
    public function getKlarnaFinancingtypeByName($paymentName)
    {
        // remove _1 ,_2 ... from duplicated payments before matching
        $cleanedPaymentName = preg_replace('/_[0-9]*$/', '', $paymentName);
        return $this->klarnaPaymentFinancingtypeNameMapping()[$cleanedPaymentName];
    }

    /**
     * Maps Payone API value for Klarna
     *
     * @return string[]
     */
    private function klarnaPaymentFinancingtypeNameMapping()
    {
        return [
            'mopt_payone__fin_kis_klarna_installments' => Payone_Api_Enum_FinancingType::KIS,
            'mopt_payone__fin_kiv_klarna_invoice' => Payone_Api_Enum_FinancingType::KIV,
            'mopt_payone__fin_kdd_klarna_direct_debit' => Payone_Api_Enum_FinancingType::KDD,
        ];
    }

    /**
     * Fetches and returns amazon payment instance.
     *
     * @return \Shopware\Models\Payment\Payment | null
     */
    public function getPaymentAmazonPay()
    {
        $shopID = Shopware()->Shop()->getId();
        $em = Shopware()->Models();
        $result = $em->getRepository(Payment::class)->createQueryBuilder('p')
            ->where('p.active = :active')
            ->andWhere('p.name LIKE :name')
            ->setParameter('active', true)
            ->setParameter('name', 'mopt_payone__ewallet_amazon_pay%')
            ->getQuery()
            ->getResult();

        foreach ($result as $payment) {
            if ($this->isPaymentAssignedToSubshop($payment->getId(), $shopID)) {
                return $payment;
            }
        }
        return null;
    }

    /**
     * Fetches and returns amazon payment instance.
     *
     * @return \Shopware\Models\Payment\Payment | null
     */
    public function getPaymentPaypalv2Express()
    {
        $shopID = Shopware()->Shop()->getId();
        $em = Shopware()->Models();
        $result = $em->getRepository(Payment::class)->createQueryBuilder('p')
            ->where('p.active = :active')
            ->andWhere('p.name LIKE :name')
            ->setParameter('active', true)
            ->setParameter('name', 'mopt_payone__ewallet_paypal_expressv2%')
            ->getQuery()
            ->getResult();

        foreach ($result as $payment) {
            if ($this->isPaymentAssignedToSubshop($payment->getId(), $shopID)) {
                return $payment;
            }
        }
        return null;
    }

    /**
     * checks if AmazonPay is active
     *
     * @return bool
     */
    public function isAmazonPayActive($paymentMethod)
    {
        if (!$this->isPayoneAmazonPay($paymentMethod['name'])) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * checks if AmazonPay is enabled
     *
     * @return bool
     */
    public function isAmazonPayEnabled()
    {
        $paymentAmazonPay = Shopware()->Models()->getRepository('Shopware\Models\Payment\Payment')->findOneBy(
            [
                'name' => 'mopt_payone__ewallet_amazon_pay'
            ]
        );
        return $paymentAmazonPay->getActive();
    }


    /**
     * Marks all OrderDetails and Shipping as Fully Captured
     *
     * @param Shopware\Models\Order\Order $order shopware order object
     *
     * @return void
     * @throws Exception
     */
    public function markOrderDetailsAsFullyCaptured($order)
    {
        foreach ($order->getDetails() as $position) {
            $positionAttribute = $position->getAttribute();
            $positionAttribute->setMoptPayoneCaptured($position->getPrice() * $position->getQuantity());
            Shopware()->Models()->persist($positionAttribute);
        }
        Shopware()->Models()->flush();
        $orderAttribute = $order->getAttribute();
        $orderAttribute->setMoptPayoneShipCaptured($order->getInvoiceShipping());
        Shopware()->Models()->persist($orderAttribute);
        Shopware()->Models()->flush();
    }

    /**
     * @param Router $router
     * @param $userParams
     * @param null $context
     * @return false|string
     */
    public function assembleTokenizedUrl($router, $userParams, $context = null)
    {
        if (version_compare(Shopware()->Config()->get('version'), '5.6.3', '>=')) {
            /** @noinspection PhpFullyQualifiedNameUsageInspection */
            $token = Shopware()->Container()->get(\Shopware\Components\Cart\PaymentTokenService::class)->generate();
            /** @noinspection PhpFullyQualifiedNameUsageInspection */
            $this->array_splice_assoc($userParams, -1, 0, array(\Shopware\Components\Cart\PaymentTokenService::TYPE_PAYMENT_TOKEN => $token));
            return $router->assemble(
                $userParams
                , $context);
        }

        return $router->assemble($userParams, $context);
    }

    public function array_splice_assoc(&$input, $offset, $length, $replacement = array())
    {
        $replacement = (array)$replacement;
        $key_indices = array_flip(array_keys($input));
        if (isset($input[$offset]) && is_string($offset)) {
            $offset = $key_indices[$offset];
        }
        if (isset($input[$length]) && is_string($length)) {
            $length = $key_indices[$length] - $offset;
        }

        $input = array_slice($input, 0, $offset, TRUE)
            + $replacement
            + array_slice($input, $offset + $length, NULL, TRUE);
    }

    public function buildAndCallKlarnaStartSession($paymentFinancingtype, $birthdate, $phoneNumber, $personalId, $paymentId)
    {
        $bootstrap = Shopware()->Container()->get('plugins')->Frontend()->MoptPaymentPayone();

        $userData = Shopware()->Modules()->Admin()->sGetUserData();

        /** @var Mopt_PayoneMain $moptPayoneMain */
        $moptPayoneMain = $bootstrap->Application()->MoptPayoneMain();
        $paramBuilder = $moptPayoneMain->getParamBuilder();
        $basket = $moptPayoneMain->sGetBasket();

        /** @var Payone_Builder $payoneBuilder */
        $payoneBuilder = $bootstrap->Application()->MoptPayoneBuilder();
        $service = $payoneBuilder->buildServicePaymentGenericpayment();

        $repositoryNamespace = 'Shopware\CustomModels\MoptPayoneApiLog\MoptPayoneApiLog';
        /** @var Payone_Api_Persistence_Interface $moptPayoneApiLogRepository */
        $moptPayoneApiLogRepository = Shopware()->Models()->getRepository($repositoryNamespace);
        $service->getServiceProtocol()->addRepository($moptPayoneApiLogRepository);

        $userData['additional']['user']['birthday'] = $birthdate;
        $userData['additional']['user']['mopt_payone_klarna_personalid'] = $personalId;
        Shopware()->Session()->offsetSet('mopt_klarna_phoneNumber', $phoneNumber);

        $shippingCosts = Shopware()->Modules()->Admin()->sGetPremiumShippingcosts();

        $params = $paramBuilder->buildKlarnaSessionStartParams('fnc', $paymentFinancingtype, $basket, $shippingCosts, $paymentId, $phoneNumber);
        $request = new Payone_Api_Request_Genericpayment($params);

        $basket['sShippingcosts'] = $shippingCosts['brutto'];
        $basket['sShippingcostsWithTax'] = $shippingCosts['brutto'];
        $basket['sShippingcostsNet'] = $shippingCosts['netto'];
        $basket['sShippingcostsTax'] = $shippingCosts['tax'];

        $personalData = $paramBuilder->getPersonalData($userData);
        $request->setPersonalData($personalData);
        $deliveryData = $paramBuilder->getDeliveryData($userData);
        $request->setDeliveryData($deliveryData);

        $selectedDispatchId = Shopware()->Session()['sDispatch'];
        $dispatch = Shopware()->Modules()->Admin()->sGetPremiumDispatch($selectedDispatchId);

        $invoicing = $paramBuilder->getInvoicing($basket, $dispatch, $userData);
        $request->setInvoicing($invoicing);
        $request->getPersonalData()->setTelephonenumber(Shopware()->Session()->offsetGet('mopt_klarna_phoneNumber'));
        $request->setTelephonenumber(Shopware()->Session()->offsetGet('mopt_klarna_phoneNumber'));

        $result = null;

        try {
            $result = $service->request($request);
        } catch (Exception $e) {
        }

        return $result;
    }

    public function getKlarnaGender($userData)
    {
        switch ($userData['additional']['user']['salutation']) {
            case 'mr' :
                $gender = 'male';
                break;
            case 'ms' :
                $gender = 'female';
                break;
            default:
                // klarna does not support 'divers'
                $gender = 'male';
        }

        return $gender;
    }

    public function getKlarnaTitle($userData)
    {
        $countryIso2 = $userData['additional']['country']['countryiso'];
        $salutation = $userData['additional']['user']['salutation'];
        switch ($countryIso2) {
            case 'AT':
            case 'DE':
            case 'CH':
                $title = ($salutation !== 'ms') ? 'Herr' : 'Frau';
                break;
            case 'GB':
            case 'US':
                $title = ($salutation !== 'ms') ? 'Mr' : 'Ms';
                break;
            case 'DK':
            case 'FI':
            case 'SE':
            case 'NL':
            case 'NO':
                $title = ($salutation !== 'ms') ? 'Dhr.' : 'Mevr.';
                break;
            default:
                $title = '';
        }

        return $title;
    }

    /**
     * Check if birthday field needs to be shown
     *
     * @return bool
     */
    public function isKlarnaBirthdayNeeded()
    {
        $isBirthdayValid = $this->isBirthdayValid();
        $isBirthdayNeededByCountry = $this->isKlarnaBirthdayNeededByCountry();

        return !$isBirthdayValid && $isBirthdayNeededByCountry;
    }

    /**
     * Check if telephone field needs to be shown
     *
     * @return bool
     */
    public function isKlarnaTelephoneNeeded()
    {
        $isTelephoneValid = $this->isTelephoneValid();
        $isTelephoneNeededByCountry = $this->isKlarnaTelephoneNeededByCountry();

        return !$isTelephoneValid && $isTelephoneNeededByCountry;
    }

    /**
     * Check if telephone field needs to be shown
     *
     * @return bool
     */
    public function isKlarnaPersonalIdNeeded()
    {
        $isPersonalIdValid = $this->isPersonalIdValid();
        $isPersonalIdNeededByCountry = $this->isKlarnaPersonalIdNeededByCountry();

        return !$isPersonalIdValid && $isPersonalIdNeededByCountry;
    }

    /**
     * Checks if current users birthday is valid
     *
     * @return bool
     */
    private function isBirthdayValid()
    {
        $userData = Shopware()->Modules()->Admin()->sGetUserData();

        return !is_null($userData['additional']['user']['birthday']) && $userData['additional']['user']['birthday'] !== '' && $userData['additional']['user']['birthday'] !== '0000-00-00';
    }

    /**
     * Checks if current users telephone number is valid
     *
     * @return bool
     */
    private function isTelephoneValid()
    {
        $userData = Shopware()->Modules()->Admin()->sGetUserData();

        return !is_null($userData['billingaddress']['phone']) && $userData['billingaddress']['phone'] !== '';
    }

    /**
     * Checks if current users personalid number is valid
     *
     * @return bool
     */
    private function isPersonalIdValid()
    {
        $userData = Shopware()->Modules()->Admin()->sGetUserData();

        return !is_null($userData['additional']['user']['mopt_payone_klarna_personalid']) && $userData['additional']['user']['mopt_payone_klarna_personalid'] !== '';
    }

    /**
     * Checks if birthday is mandatory for klarna payments depending on country
     *
     * @return bool
     */
    private function isKlarnaBirthdayNeededByCountry()
    {
        $moptPayoneHelper = Shopware()->Container()->get('MoptPayoneMain')->getInstance()->getHelper();
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $billingCountryIso = $moptPayoneHelper->getCountryIsoFromId($userData['billingaddress']['countryID']);
        $klarnaBirthdayNeededCountries = array('DE', 'NL', 'AT', 'CH');
        return in_array($billingCountryIso, $klarnaBirthdayNeededCountries);
    }

    /**
     * Checks if telephone is mandatory for klarna payments depending on country
     *
     * @return bool
     */
    public function isKlarnaTelephoneNeededByCountry()
    {
        $moptPayoneHelper = Shopware()->Container()->get('MoptPayoneMain')->getInstance()->getHelper();
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $billingCountryIso = $moptPayoneHelper->getCountryIsoFromId($userData['billingaddress']['countryID']);
        $klarnaTelephoneNeededCountries = array('NO', 'SE', 'DK');
        return in_array($billingCountryIso, $klarnaTelephoneNeededCountries);
    }

    /**
     * Checks if personalid is mandatory for klarna payments depending on country
     *
     * @return bool
     */
    public function isKlarnaPersonalIdNeededByCountry()
    {
        $moptPayoneHelper = Shopware()->Container()->get('MoptPayoneMain')->getInstance()->getHelper();
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        $billingCountryIso = $moptPayoneHelper->getCountryIsoFromId($userData['billingaddress']['countryID']);
        $klarnaPersonalIdNeededCountries = array('NO', 'SE', 'DK'); // SE verified FI unsure
        return in_array($billingCountryIso, $klarnaPersonalIdNeededCountries);
    }

    /**
     * remove express and installment payments from
     * payment lists
     *
     * @param $payments array
     * @return array
     */
    public function filterExpressAndInstallmentPayments($payments)
    {
        foreach ($payments as $index => $payment) {
            foreach (Mopt_PayoneConfig::PAYMENTS_EXCLUDED_FROM_ACCOUNTPAGE as $exludedPayment) {
                if (strpos($payment['name'], $exludedPayment) !== false) {
                    unset($payments[$index]);
                }
            }
        }

        return $payments;
    }

    /**
     * remove express payments from
     * payment list
     *
     * @param $payments array
     * @param $session
     * @return array
     */
    public function filterExpressPayments($payments, $session)
    {
        foreach ($payments as $index => $payment) {
            foreach (Mopt_PayoneConfig::PAYMENTS_EXCLUDED_FROM_SHIPPINGPAYMENTPAGE as $exludedPayment) {
                if (strpos($payment['name'], $exludedPayment) !== false) {
                    unset($payments[$index]);
                }
                if (strpos($payment['name'], 'mopt_payone__ewallet_applepay') !== false && $session->get('moptAllowApplePay', false) !== true) {
                    unset($payments[$index]);
                }
            }
        }

        return $payments;
    }

    /**
     * remove express and installment payments from
     * payment lists on account page
     *
     * @param $payments array
     * @return array
     */
    public function filterPaymentsInAccount($payments)
    {
        foreach ($payments as $index => $payment) {
            foreach (Mopt_PayoneConfig::PAYMENTS_EXCLUDED_FROM_ACCOUNTPAGE as $exludedPayment) {
                if (strpos($payment['name'], $exludedPayment) !== false) {
                    unset($payments[$index]);
                }
            }
        }

        return $payments;
    }

    /**
     * remove unzer b2b payments from
     * payment list when user is a company
     *
     * @param $payments array
     * @param $session
     * @return array
     */
    public function filterB2bPayments($payments)
    {
        $userData = Shopware()->Modules()->Admin()->sGetUserData();
        foreach ($payments as $index => $payment) {
            foreach (Mopt_PayoneConfig::B2BPAYMENTS_EXCLUDED_FROM_SHIPPINGPAYMENTPAGE as $exludedPayment) {
                if ((strpos($payment['name'], $exludedPayment) !== false) && !empty($userData['billingaddress']['company'])) {
                    unset($payments[$index]);
                }
            }
        }

        return $payments;
    }

    /**
     * updates user attributes
     * @param $userId
     * @param $success
     */
    public function updateUserCreditcardInitialPaymentSuccess($userId, $success)
    {
        $moptPayoneHelper = Shopware()->Container()->get('MoptPayoneMain')->getInstance()->getHelper();

        $user = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->find($userId);
        $attributes = $moptPayoneHelper->getOrCreateUserAttribute($user);
        $attributes->setMoptPayoneCreditcardInitialPayment($success);
        Shopware()->Models()->persist($attributes);
        Shopware()->Models()->flush($attributes);
    }
}
