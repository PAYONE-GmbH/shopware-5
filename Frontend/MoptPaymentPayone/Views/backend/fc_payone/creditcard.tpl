{extends file="parent:backend/_base/layout.tpl"}

{block name="content/main"}
    {namespace name=backend/mopt_config_payone/main}
    <div class="col-md-12">
        <h3>{s name="global-form/fieldset2"}Kreditkarteneinstellungen{/s}</h3>
        <div>
            {s name="global-form/fieldset2Desc"}Stellen Sie hier die Konfiguration zur Zahlart Kreditkarte ein.{/s}
        </div>
        <div id="payonetable">
            <form role="form" id="ajaxiframeconfigform">
                <table class="table caption-top">
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
                            {include file='backend/fc_payone/include/dropdown_fieldconfig.tpl' id='cardnoFieldType'}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardnoInputChars' value="30" size="3"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardnoInputCharsMax' value="16" size="3"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/dropdown_cardnocustomiframe.tpl' id='cardnoCustomIframe'}
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
                            {include file='backend/fc_payone/include/dropdown_fieldconfig.tpl' id='cardcvcFieldType'}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardcvcInputChars'  value="30" size="3"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardcvcInputCharsMax' value="16" size="3"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/dropdown_cardnocustomiframe.tpl' id='cardcvcCustomIframe'}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardcvcIframeWidth' value="200px" size="4"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardcvcIframeHeight' value="300px" size="4"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/dropdown_cardnocustomiframe.tpl' id='cardcvcCustomStyle'}
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
                        <th></th>
                        <td>
                            {include file='backend/fc_payone/include/dropdown_cardnocustomiframe.tpl' id='cardmonthFieldType'}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardmonthInputChars' value="30" size="3"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardmonthInputCharsMax' value="16" size="3"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/dropdown_cardnocustomiframe.tpl' id='cardmonthCustomIframe'}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardmonthIframeWidth' value="200px" size="4"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardmonthIframeHeight' value="300px" size="4"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/dropdown_cardnocustomiframe.tpl' id='cardmonthCustomStyle'}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardmonthInputCss'}
                        </td>
                    </tr>
                    <tr class="form-group">
                        <th>{s name="global-form/fieldconfigcreditcardyear"}Gültigkeitsjahr{/s}</th>
                        <td>
                            {include file='backend/fc_payone/include/dropdown_fieldconfig.tpl' id='cardyearFieldType'}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardyearInputChars' value="30" size="3"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardyearInputCharsMax' value="16" size="3"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/dropdown_cardnocustomiframe.tpl' id='cardyearCustomIframe'}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardyearIframeWidth' value="200px" size="4"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardyearIframeHeight' value="300px" size="4"}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/dropdown_cardnocustomiframe.tpl' id='cardyearCustomStyle'}
                        </td>
                        <td>
                            {include file='backend/fc_payone/include/input_text_iframe.tpl' id='cardyearInputCss'}
                            <input name="cardyearInputCss" id="cardyearInputCss" type="text" class="form-control" /></td>
                    </tr>
                </table>

                <table class="table-condensed">
                    <tr>
                        <th>{s name="fieldlabel/stadardstyle"}Standardstil{/s}</th>
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
                    <h3>{s name="global-form/fieldconfigerrordesc"}Fehlerausgabe und eigene Fehlermeldungen{/s}<h3>
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
                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}
    {include file='backend/fc_payone/include/creditcard.js'}
{/block}
