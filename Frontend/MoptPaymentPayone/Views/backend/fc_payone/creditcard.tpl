{extends file="parent:backend/_base/layout.tpl"}

{block name="content/main"}
    {namespace name=backend/mopt_config_payone/main}
    <div class="col-md-12">
        <h3>{s name="global-form/fieldset2"}Kreditkarteneinstellungen{/s}</h3>
        <div>
            {s name="global-form/fieldset2Desc"}Stellen Sie hier die Konfiguration zur Zahlart Kreditkarte ein.{/s}
        </div>

        <div class='col-md-12'>
            <form role="form" id="creditcardconfigform">
                <table class="table-condensed">
                    <tr class="form-group">
                        <td>
                            {include file='backend/fc_payone/include/dropdown_shops.tpl'}
                        </td>
                    </tr>
                    <tr class="form-group">
                        <td>
                            {include file='backend/fc_payone/include/input_checkbox.tpl' id='isDefault' label="Default"}
                        </td>
                    </tr>

                    <tr class="form-group">
                        <td>
                            {include file='backend/fc_payone/include/input_text.tpl' id='creditcardDefaultDescription' label="{s name='fieldlabel/creditcardDefaultDescription'}Kreditkarte Zusätzliche Beschreibung{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name='fieldlabelhelp/creditcardDefaultDescription'}Zusätzliche Beschreibung der Zahlart bei Gruppierung{/s}"}
                        </td>
                    </tr>

                    <tr class="form-group">
                        <td>
                            {include file='backend/fc_payone/include/input_checkbox.tpl' id='checkCc' label="{s name="fieldlabel/checkCc"}Abfrage Kreditkartenprüfziffer<br>(nur global konfigurierbar){/s}"}
                        </td>
                    </tr>

                    <tr class="form-group">
                        <td>
                            {include file='backend/fc_payone/include/input_text.tpl' id='creditcardMinValid' label="{s name="fieldlabel/creditcardMinValid"}Gültigkeit der Kreditkarte{/s}" pattern='^[0-9]*' content="{s name="fieldlabelhelp/creditcardMinValid"}Gültigkeit der Kreditkarte in Tagen zudem eine Kreditkarte im Checkout akzeptiert wird.{/s}"}
                        </td>
                    </tr>

                    <tr class="form-group">
                        <td>
                            {include file='backend/fc_payone/include/dropdown_iframeajax.tpl' id='integrationType' label="{s name="mopt_apilog_payone/grid/column_mode"}Modus{/s}"}
                        </td>
                    </tr>
                    <tr class="form-group">
                        <td>
                            {include file='backend/fc_payone/include/input_text.tpl' id='merchantId' label="{s name="fieldlabel/merchantId"}Merchant-Id{/s}" pattern="^[0-9]*" minlength="1" maxlength="5" content="{s name="fieldlabelhelp/merchantId"}ID des zu verwendenden Accounts{/s}"}
                        </td>
                    </tr>
                    <tr class="form-group">
                        <td>
                            {include file='backend/fc_payone/include/input_text.tpl' id='portalId' label="{s name="fieldlabel/portalId"}Portal-Id{/s}" pattern="^[0-9]*" minlength="1" maxlength="5" content="{s name="fieldlabelhelp/portalId"}ID des zu verwendenden Zahlungsportal{/s}"}
                        </td>
                    </tr>
                    <tr class="form-group">
                        <td>
                            {include file='backend/fc_payone/include/input_text.tpl' id='subaccountId' label="{s name="fieldlabel/subaccountId"}Subaccount-Id{/s}" pattern="^[0-9]*" minlength="1" maxlength="5" content="{s name="fieldlabelhelp/subaccountId"}ID des zu verwendenden SubAccounts{/s}"}
                        </td>
                    </tr>
                    <tr class="form-group">
                        <td>
                            {include file='backend/fc_payone/include/input_text.tpl' id='apiKey' label="{s name="fieldlabel/apiKey"}Schlüssel{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9`´€]*' minlength="1" maxlength="100" content="{s name="fieldlabelhelp/apiKey"}Schlüssel des zu verwendenden Zahlungsportal{/s}"}
                        </td>
                    </tr>

                    <tr class="form-group">
                        <td>
                            {include file='backend/fc_payone/include/input_checkbox.tpl' id='liveMode' label="{s name="fieldlabel/liveMode"}Livemodus{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/liveMode"}Hier wird definiert wie die Zahlart verwendet wird. Live = Zahlungen werden auf der PAYONE-Plattform ausgeführt Test = Zahlungen werden nur auf der PAYONE-Testumgebung simuliert{/s}"}
                        </td>
                    </tr>

                </table>

                <table class="table-condensed">
                    <tr>
                        <th>{s name="global-form/field"}Feld{/s}</th>
                        <th>{s name="global-form/type"}Typ{/s}</th>
                        <th>{s name="global-form/count"}Anzahl{/s}<br/>{s name="global-form/characters"}Zeichen{/s}</th>
                        <th>{s name="global-form/characters"}Zeichen{/s}<br/>{s name="global-form/max"}Max{/s}</th>
                        <th>Iframe</th>
                        <th>{s name="global-form/width"}Breite{/s}</th>
                        <th>{s name="global-form/height"}Höhe{/s}</th>
                        <th>{s name="global-form/style"}Stil{/s}</th>
                        <th>Css</th>
                        <th>{s name="global-form/placeholder"}Platzhalter{/s}</th>
                    </tr>
                    <tr class="form-group">
                        <th>{s name="global-form/fieldconfigcreditcardnumber"}Kreditkartennummer{/s}</th>
                        <td>
                            {include file='backend/fc_payone/include/dropdown_fieldconfig.tpl' id='cardnoFieldType' style="max-width:125px;"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardnoInputChars' value="30" size="3"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardnoInputCharsMax' value="16" size="3"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/dropdown_cardnocustomiframe.tpl' id='cardnoCustomIframe' style="max-width:125px;"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardnoIframeWidth' value="200px" size="4"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardnoIframeHeight' value="300px" size="4"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/dropdown_cardnocustomiframe.tpl' id='cardnoIframeWidth'}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardnoInputCss'}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='defaultTranslationIframeCardpan'}
                        </td>
                    </tr>
                    <tr class="form-group">
                        <th>{s name="global-form/fieldconfigcreditcardcvc"}Kreditkartenprüfziffer{/s}</th>
                        <td>
                            {include file='backend/fc_payone/include/dropdown_fieldconfig.tpl' id='cardcvcFieldType' style="max-width:125px;"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardcvcInputChars'  value="30" size="3"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardcvcInputCharsMax' value="16" size="3"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/dropdown_cardnocustomiframe.tpl' id='cardcvcCustomIframe' style="max-width:125px;"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardcvcIframeWidth' value="200px" size="4"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardcvcIframeHeight' value="300px" size="4"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/dropdown_cardnocustomiframe.tpl' id='cardcvcCustomStyle' style="max-width:125px;"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardcvcInputCss'}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='defaultTranslationIframeCvc'}
                        </td>
                    </tr>
                    <tr class="form-group">
                        <th>{s name="global-form/fieldconfigcreditcardmonth"}Gültigkeitsmonat{/s}</th>
                        <td>
                            {include file='backend/fc_payone/include/dropdown_cardnocustomiframe.tpl' id='cardmonthFieldType' style="max-width:125px;"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardmonthInputChars' value="30" size="3"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardmonthInputCharsMax' value="16" size="3"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/dropdown_cardnocustomiframe.tpl' id='cardmonthCustomIframe'  style="max-width:125px;"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardmonthIframeWidth' value="200px" size="4"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardmonthIframeHeight' value="300px" size="4"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/dropdown_cardnocustomiframe.tpl' id='cardmonthCustomStyle' style="max-width:125px;"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardmonthInputCss'}
                        </td>
                    </tr>
                    <tr class="form-group">
                        <th>{s name="global-form/fieldconfigcreditcardyear"}Gültigkeitsjahr{/s}</th>
                        <td>
                            {include file='backend/fc_payone/include/dropdown_fieldconfig.tpl' id='cardyearFieldType' style="max-width:125px;"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardyearInputChars' value="30" size="3"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardyearInputCharsMax' value="16" size="3"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/dropdown_cardnocustomiframe.tpl' id='cardyearCustomIframe' style="max-width:125px;"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardyearIframeWidth' value="200px" size="4"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardyearIframeHeight' value="300px" size="4"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/dropdown_cardnocustomiframe.tpl' id='cardyearCustomStyle' style="max-width:125px;"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardyearInputCss'}
                    </tr>
                </table>

                <table class="table-condensed">
                    <tr>
                        <th>{s name="fieldlabel/standardstyle"}Standardstil{/s}</th>
                        <th>{s name="fieldlabel/input"}Eingabe{/s}</th>
                        <th>{s name="fieldvalue/choice"}Auswahl{/s}</th>
                    </tr>
                    <tr class="form-group">
                        <th>Felder</th>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='standardInputCss'}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='standardInputCssSelected'}
                        </td>
                    </tr>
                    <tr class="form-group">
                        <th>Iframe</th>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='standardIframeHeight' placeholder="Breite"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='standardIframeWidth' placeholder="Höhe"}
                        </td>
                    </tr>

                    <table class="table-condensed">
                        <th>{s name="global-form/fieldconfigstandardtranslation"}Standardübersetzung{/s}</th>
                        <tr>
                            <th>{s name="global-form/fieldconfigmonth"}Monat{/s}</th>
                            <th>{s name="global-form/fieldconfigtranslation"}Übersetzung{/s}</th>
                            <th>{s name="global-form/fieldconfigmonth"}Monat{/s}</th>
                            <th>{s name="global-form/fieldconfigtranslation"}Übersetzung{/s}</th>
                        </tr>
                        <tr class="form-group">
                            <th>{s name="global-form/fieldconfigjanuary"}Januar{/s}</th>
                            <td>
                                {include file='backend/fc_payone/include/input_text_iframe.tpl' id='defaultTranslationIframeMonth1'}
                            </td>
                            <th>{s name="global-form/fieldconfigjuly"}Juli{/s}</th>
                            <td>
                                {include file='backend/fc_payone/include/input_text_iframe.tpl' id='defaultTranslationIframeMonth7'}
                            </td>
                        </tr>
                        <tr class="form-group">
                            <th>{s name="global-form/fieldconfigfebruary"}Februar{/s}</th>
                            <td>
                                {include file='backend/fc_payone/include/input_text_iframe.tpl' id='defaultTranslationIframeMonth2'}
                            </td>
                            <th>{s name="global-form/fieldconfigaugust"}August{/s}</th>
                            <td>
                                {include file='backend/fc_payone/include/input_text_iframe.tpl' id='defaultTranslationIframeMonth8'}
                            </td>
                        </tr>
                        <tr class="form-group">
                            <th>{s name="global-form/fieldconfigmarch"}März{/s}</th>
                            <td>
                                {include file='backend/fc_payone/include/input_text_iframe.tpl' id='defaultTranslationIframeMonth3' }
                            </td>
                            <th>{s name="global-form/fieldconfigseptember"}September{/s}</th>
                            <td>
                                {include file='backend/fc_payone/include/input_text_iframe.tpl' id='defaultTranslationIframeMonth9'}
                            </td>
                        </tr>
                        <tr class="form-group">
                            <th>{s name="global-form/fieldconfigapril"}April{/s}</th>
                            <td>
                                {include file='backend/fc_payone/include/input_text_iframe.tpl' id='defaultTranslationIframeMonth4'}
                            </td>
                            <th>{s name="global-form/fieldconfigoctober"}Oktober{/s}</th>
                            <td>
                                {include file='backend/fc_payone/include/input_text_iframe.tpl' id='defaultTranslationIframeMonth10'}
                            </td>
                        </tr>
                        <tr class="form-group">
                            <th>{s name="global-form/fieldconfigmay"}Mai{/s}</th>
                            <td>
                                {include file='backend/fc_payone/include/input_text_iframe.tpl' id='defaultTranslationIframeMonth5'}
                            </td>
                            <th>{s name="global-form/fieldconfignovember"}November{/s}</th>
                            <td>
                                {include file='backend/fc_payone/include/input_text_iframe.tpl' id='defaultTranslationIframeMonth11'}
                            </td>
                        </tr>
                        <tr class="form-group">
                            <th>{s name="global-form/fieldconfigjune"}Juni{/s}</th>
                            <td>
                                {include file='backend/fc_payone/include/input_text_iframe.tpl' id='defaultTranslationIframeMonth6'}
                            </td>
                            <th>{s name="global-form/fieldconfigdecember"}Dezember{/s}</th>
                            <td>
                                {include file='backend/fc_payone/include/input_text_iframe.tpl' id='defaultTranslationIframeMonth12'}
                            </td>
                        </tr>
                    </table>
                    <table class="table-condensed">
                        <h3>{s name="global-form/fieldconfigerrordesc"}Fehlerausgabe und eigene Fehlermeldungen{/s}
                            <h3>
                                <tr class="form-group">
                                    <th>{s name="global-form/fieldconfigerroractivate"}Fehlerausgabe aktivieren{/s}</th>
                                    <td>
                                        {include file='backend/fc_payone/include/input_checkbox.tpl' id='showErrors'}
                                    </td>
                                </tr>
                                <tr>
                                    <th>{s name="global-form/fieldconfigerrormessage"}Fehlermeldung{/s}</th>
                                    <th>{s name="global-form/fieldconfigcustomerrormessage"}eigene Fehlermeldung{/s}</th>
                                </tr>
                                <tr class="form-group">
                                    <th>{s name="global-form/fieldconfiginvalidccnumber"}Ungültige Kreditkartennummer{/s}</th>
                                    <td>
                                        {include file='backend/fc_payone/include/input_text_iframe.tpl' id='defaultTranslationIframeinvalidCardpan'}
                                    </td>
                                </tr>
                                <tr class="form-group">
                                    <th>{s name="global-form/fieldconfiginvalidcvc"}Ungültige Kartenprüfziffer{/s}</th>
                                    <td>
                                        {include file='backend/fc_payone/include/input_text_iframe.tpl' id='defaultTranslationIframeinvalidCvc'}
                                    </td>
                                </tr>
                                <tr class="form-group">
                                    <th>{s name="global-form/fieldconfiginvalidccnumberforcardtype"}Ungültige Kreditkartennummer für den Kartentyp{/s}</th>
                                    <td>
                                        {include file='backend/fc_payone/include/input_text_iframe.tpl' id='defaultTranslationIframeinvalidPanForCardtype'}
                                    </td>
                                </tr>
                                <tr class="form-group">
                                    <th>{s name="global-form/fieldconfiginvalidcardtype"}Ungültiger Kartentyp{/s}</th>
                                    <td>
                                        {include file='backend/fc_payone/include/input_text_iframe.tpl' id='defaultTranslationIframeinvalidCardtype'}
                                    </td>
                                </tr>
                                <tr class="form-group">
                                    <th>{s name="global-form/fieldconfiginvaliddate"}Ungültiges Verfallsdatum{/s}</th>
                                    <td>
                                        {include file='backend/fc_payone/include/input_text_iframe.tpl' id='defaultTranslationIframeinvalidExpireDate'}
                                    </td>
                                </tr>
                                <tr class="form-group">
                                    <th>{s name="global-form/fieldconfiginvalidissuer"}Ungültige Ausstellungsnummer{/s}</th>
                                    <td>
                                        {include file='backend/fc_payone/include/input_text_iframe.tpl' id='defaultTranslationIframeinvalidIssueNumber'}
                                    </td>
                                </tr>
                                <tr class="form-group">
                                    <th>{s name="global-form/fieldconfigtransactiondenied"}Transaktion abgelehnt{/s}</th>
                                    <td>
                                        {include file='backend/fc_payone/include/input_text_iframe.tpl' id='defaultTranslationIframetransactionRejected'}
                                    </td>
                                </tr>
                    </table>
                    <button type="submit" class="btn-payone btn ">{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}
    <script type="text/javascript">
        {include file='backend/fc_payone/include/javascript.tpl.js' form="#creditcardconfigform" loadAction="ajaxgetIframeConfig" saveAction="ajaxSaveIframeConfig"}
    </script>
{/block}
