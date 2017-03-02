<h2 class="ratepay-mid-heading"><b>' . "{s name='individualRateCalculation'}Individuelle Ratenberechnung*{/s}" . '</b></h2>
<table id="ratepay-InstallmentTerms" cellspacing="0">
    <tr>
        <th>
            <div class="ratepay-InfoDiv">
                <div class="ratepay-InfoImgDiv"><img class="ratepay-InfoImg" src="' . '/sw5218-payone/engine/Shopware/Plugins/Local/Frontend/MoptPaymentPayone/Views/frontend/_resources/images/info-icon.png' .'"/></div>
                <div class="ratepay-FloatLeft">' . "{s name='cashPaymentPrice'}Bestellwert{/s}" . ':</div>
                <div class="ratepay-RelativePosition">
                    <div class="ratepay-MouseoverInfo" id="ratepayMouseoverInfoPaymentPrice">' . "{s name='mouseoverCashPaymentPrice'}Summe aller Artikel ihres Warenkorbs, incl. Versandkosten etc.{/s}" . '</div>
                </div>
            </div>
        </th>
        <td>&nbsp;' . $result['amount'] . '</td>
        <td class="ratepay-TextAlignLeft">&euro;</td>
    </tr>
    <tr class="piTableHr">
        <th>
            <div class="ratepay-InfoDiv">
                <div class="ratepay-InfoImgDiv"><img class="ratepay-InfoImg" src="' . '/sw5218-payone/engine/Shopware/Plugins/Local/Frontend/MoptPaymentPayone/Views/frontend/_resources/images/info-icon.png' .'"/></div>
                <div class="ratepay-FloatLeft">' . "{s name='serviceCharge'}Vertragsabschlussgeb&uuml;hr{/s}" . ':</div>
                <div class="ratepay-RelativePosition">
                    <div class="ratepay-MouseoverInfo" id="ratepayMouseoverInfoServiceCharge">' . "{s name='mouseoverServiceCharge'}Bei Ratenzahlung pro Bestellung anfallende, einmalige Bearbeitungsgeb&uuml;hr{/s}" . '</div>
                </div>
            </div>
        </th>
        <td>&nbsp;' . $result['service-charge'] . '</td>
        <td class="ratepay-TextAlignLeft">&euro;</td>
    </tr>
    <tr class="piPriceSectionHead">
        <th class="ratepay-PercentWidth">
            <div class="ratepay-InfoDiv">
                <div class="ratepay-InfoImgDiv"><img class="ratepay-InfoImg" src="' . '/sw5218-payone/engine/Shopware/Plugins/Local/Frontend/MoptPaymentPayone/Views/frontend/_resources/images/info-icon.png' . '"/></div>
                <div class="ratepay-FloatLeft">' . "{s name='effectiveRate'}Effektiver Jahreszins{/s}" . ':</div>
                <div class="ratepay-RelativePosition">
                    <div class="ratepay-MouseoverInfo" id="ratepayMouseoverInfoEffectiveRate">' . "{s name='mouseoverEffectiveRate'}Gesamtkosten des Kredits als j&auml;hrlicher Prozentsatz{/s}" . ':</div>
                </div>
            </div>
        </th>
        <td colspan="2"><div class="ratepay-FloatLeft">&nbsp;<div class="ratepay-PercentWith">' . $result['annual-percentage-rate'] . '%</div></div></td>
    </tr>
    <tr class="piTableHr">
        <th>
            <div class="ratepay-InfoDiv">
                <div class="ratepay-InfoImgDiv"><img class="ratepay-InfoImg" src="' . '/sw5218-payone/engine/Shopware/Plugins/Local/Frontend/MoptPaymentPayone/Views/frontend/_resources/images/info-icon.png' . '"/></div>
                <div class="ratepay-FloatLeft">' . "{s name='interestrateDefault'}Sollzinssatz p.a. (gebunden){/s}" . ':</div>
                <div class="ratepay-RelativePosition">
                    <div class="ratepay-MouseoverInfo" id="ratepayMouseoverInfoDebitRate">' . "{s name='mouseoverDebitRate'}periodischer Prozentsatz, der auf das in Anspruch genommene Darlehen angewendet wird{/s}" . ':</div>
                </div>
            </div>
        </th>
        <td colspan="2"><div class="ratepay-FloatLeft">&nbsp;<div class="ratepay-PercentWith">' . $result['interest-rate'] . '%</div></div></td>
    </tr>
    <tr>
        <th>
            <div class="ratepay-InfoDiv">
                <div class="ratepay-InfoImgDiv"><img class="ratepay-InfoImg" src="' . '/sw5218-payone/engine/Shopware/Plugins/Local/Frontend/MoptPaymentPayone/Views/frontend/_resources/images/info-icon.png' . '"/></div>
                <div class="ratepay-FloatLeft">' . "{s name='interestAmount'}Zinsbetrag{/s}" . ':</div>
                <div class="ratepay-RelativePosition">
                    <div class="ratepay-MouseoverInfo" id="ratepayMouseoverInfoInterestAmount">' . "{s name='mouseoverInterestAmount'}konkreter Geldbetrag, der sich aus den Zinsen ergibt{/s}" . ':</div>
                </div>
            </div>
        </th>
        <td>&nbsp;' . $result['interest-amount'] . '</td>
        <td class="ratepay-TextAlignLeft">&euro;</td>
    </tr>
    <tr>
        <th>
            <div class="ratepay-InfoDiv">
                <div class="ratepay-InfoImgDiv"><img class="ratepay-InfoImg" src="' . '/sw5218-payone/engine/Shopware/Plugins/Local/Frontend/MoptPaymentPayone/Views/frontend/_resources/images/info-icon.png' . '"/></div>
                <div class="ratepay-FloatLeft"><b>' . "{s name='totalAmount'}Gesamtbetrag{/s}" . ':</b></div>
                <div class="ratepay-RelativePosition">
                    <div class="ratepay-MouseoverInfo" id="ratepayMouseoverInfoTotalAmount">' . "{s name='mouseoverTotalAmount'}Summe der vom K&auml;ufer zu zahlenden Betr&auml;ge aus Bestellwert, Vertragsabschlussgeb&uuml;hr und Zinsen{/s}" . '</div>
                </div>
            </div>
        </th>
        <td><b>&nbsp;' . $result['total-amount'] . '</b></td>
        <td class="ratepay-TextAlignLeft"><b>&euro;</b></td>
    </tr>
    <tr>
        <td colspan="2"><div class="ratepay-FloatLeft">&nbsp;<div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="ratepay-FloatLeft">' . "{s name='calulationResultText'}Aus Ihren Eingaben ergibt sich folgende Wunschrate{/s}" . '<div></td>
    </tr>
    <tr class="ratepay-result piPriceSectionHead">
        <th class="ratepay-PaddingTop">
            <div class="ratepay-InfoDiv">
                <div class="ratepay-InfoImgDiv"><img class="ratepay-InfoImg" src="' . '/sw5218-payone/engine/Shopware/Plugins/Local/Frontend/MoptPaymentPayone/Views/frontend/_resources/images/info-icon.png' . '"/></div>
                <div class="ratepay-FloatLeft"><b>' . "{s name='durationTime'}Laufzeit{/s}" . ':</b></div>
                <div class="ratepay-RelativePosition">
                    <div class="ratepay-MouseoverInfo" id="ratepayMouseoverInfoDurationTime">' . "{s name='mouseoverDurationTime'}Dauer des Ratenplans (kann durch Sondertilgungen verk&uuml;rzt werden{/s}" . '</div>
                </div>
            </div>
        </th>
        <td><b>&nbsp;' . $result['number-of-rates'] ."{s name='months'} Monate{/s}" . '</b></td>
        <td>&nbsp;</td>
    </tr>
    <tr class="ratepay-result">
        <th>
            <div class="ratepay-InfoDiv">
                <div class="ratepay-InfoImgDiv"><img class="ratepay-InfoImg" src="' . '/sw5218-payone/engine/Shopware/Plugins/Local/Frontend/MoptPaymentPayone/Views/frontend/_resources/images/info-icon.png' . '"/></div>
                <div class="ratepay-FloatLeft piRpPaddingLeft"><b>' . ${numberOfRates}  . '' . "{s name='durationMonth'} monatliche Raten à{/s}" . ':</b></div>
                <div class="ratepay-RelativePosition">
                    <div class="ratepay-MouseoverInfo" id="ratepayMouseoverInfoDurationMonth">' . "{s name='mouseoverDurationMonth'}monatlich f&auml;lliger Teilbetrag{/s}" . '</div>
                </div>
            </div>
        </th>
        <td><b>&nbsp;' . $result['rate'] . '</b></td>
        <td class="ratepay-PaddingRight"><b>&euro;</b></td>
    </tr>
    <tr class="ratepay-result piRpPaddingBottom">
        <th class="ratepay-PaddingBottom">
            <div class="ratepay-InfoDiv">
                <div class="ratepay-InfoImgDiv"><img class="ratepay-InfoImg" src="' . '/sw5218-payone/engine/Shopware/Plugins/Local/Frontend/MoptPaymentPayone/Views/frontend/_resources/images/info-icon.png' . '"/></div>
                <div class="ratepay-FloatLeft piRpPaddingLeft"><b>' . "{s name='lastRate'}zzgl. einer Abschlussrate à{/s}" . ':</b></div>
                <div class="ratepay-RelativePosition">
                    <div class="ratepay-MouseoverInfo" id="ratepayMouseoverInfoLastRate">' . "{s name='mouseoverLastRate'}im letzten Monat f&auml;lliger Teilbetrag{/s}" . '</div>
                </div>
            </div>
        </th>
        <td class="ratepay-PaddingBottom"><b>&nbsp;' . $result['last-rate'] . '</b></td>
        <td class="ratepay-PaddingRight piRpPaddingBottom"><b>&euro;</b></td>
    </tr>
    <tr>
        <td colspan="2"><div class="ratepay-CalculationText ">' . "{s name='calulationExample'}*die Ratenberechnung kann zum Ratenplan abweichen{/s}" . '</div></td>
    </tr>
</table>