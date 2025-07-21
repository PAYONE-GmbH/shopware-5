{extends file="parent:backend/_base/layout.tpl"}
{namespace name=backend/mopt_config_payone/main}
{block name="content/main"}
    <div class="col-md-9">
        <h3>{s name="global-form/fieldset2"}Addressüberprüfung{/s}</h3>
        <div>
            {s name="fieldlabelhelp/adresscheckInfotext"}Stellen Sie hier die Konfiguration für die Addressüberprüfung für alle Zahlarten ein.
            {/s}
        </div>
        {include file='backend/fc_payone/include/dropdown_payments.tpl'}
        <div class='col-md-9'>
            <form role="form" id="addresscheckform" class="form-horizontal">
                {include file='backend/fc_payone/include/dropdown_adresscheckactive.tpl' id='adresscheckActive' label="{s name="fieldlabel/active"}Aktiv{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255"}
                {include file='backend/fc_payone/include/dropdown_livetest.tpl' id='adresscheckLiveMode' label="{s name="fieldlabel/mode"}Betriebsmodus{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" content="{s name="fieldlabelhelp/liveMode"}Hier wird definiert wie die Zahlart verwendet wird. Live = Zahlungen werden auf der PAYONE-Plattform ausgeführt Test = Zahlungen werden nur auf der PAYONE-Testumgebung simuliert{/s}"}
                {include file='backend/fc_payone/include/dropdown_riskcheck.tpl' id='adresscheckBillingAdress' label="{s name="fieldlabel/billingAddress"}Rechnungsadresse{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*'}
                {include file='backend/fc_payone/include/input_text.tpl' id='adresscheckBillingCountries' label="{s name="fieldlabel/adresscheckBillingCountries"}Länder Rechnungsadresse{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" content="{s name="fieldlabelhelp/adresscheckCountries"}Komme-getrennte ISO-Codes der Länder für die der Check ausgeführt werden soll. Z.b. DE,CH,AT{/s}"}
                {include file='backend/fc_payone/include/dropdown_riskcheck.tpl' id='adresscheckShippingAdress' label="{s name="fieldlabel/shippingAddress"}Lieferadresse{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*'}
                {include file='backend/fc_payone/include/input_text.tpl' id='adresscheckShippingCountries' label="{s name="fieldlabel/adresscheckShippingCountries"}Länder Lieferadresse{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" content="{s name="fieldlabelhelp/adresscheckCountries"}Komma-getrennte ISO-Codes der Länder für die der Check ausgeführt werden soll. Z.B. DE,CH,AT{/s}"}
                {include file='backend/fc_payone/include/dropdown_adresscheckautocorrection.tpl' id='adresscheckAutomaticCorrection' label="{s name="fieldlabel/automaticCorrection"}Automatische Korrektur{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255"}
                {include file='backend/fc_payone/include/input_text.tpl' id='adresscheckFailureHandling' label="{s name="fieldlabel/failureHandling"}Fehlverhalten{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255"}
                {include file='backend/fc_payone/include/input_text.tpl' id='adresscheckMinBasket' label="{s name="fieldlabel/minBasket"}Minimaler Warenwert{/s}" pattern='^[,.0-9]*' minlength="1" maxlength="255"}
                {include file='backend/fc_payone/include/input_text.tpl' id='adresscheckMaxBasket' label="{s name="fieldlabel/maxBasket"}Maximaler Warenwert{/s}" pattern='^[,.0-9]*' minlength="1" maxlength="255"}
                {include file='backend/fc_payone/include/input_text.tpl' id='adresscheckLifetime' label="{s name="fieldlabel/lifetime"}Gültigkeit{/s}" pattern='^[,.0-9]*' minlength="1" maxlength="255"}
                {include file='backend/fc_payone/include/input_text.tpl' id='adresscheckFailureMessage' label="{s name="fieldlabel/adresscheckFailureMessage"}Fehlermeldung{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" content="{s name="fieldlabelhelp/adresscheckFailureMessage"}Fehlermeldung bitte über Einstellungen -> Textbausteine editieren (nach addresscheckErrorMessage suchen){/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='mapPersonCheck' label="{s name="fieldlabel/mapPersonCheck"}Keine Personenüberprüfung durchgeführt{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255"}
                {include file='backend/fc_payone/include/dropdown_trafficlights.tpl' id='mapPersonCheck' label="{s name="fieldlabel/mapKnowPreLastname"}Vor- und Nachname bekannt{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255"}
                {include file='backend/fc_payone/include/dropdown_trafficlights.tpl' id='mapKnowLastname' label="{s name="fieldlabel/mapKnowLastname"}Nachname bekannt{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255"}
                {include file='backend/fc_payone/include/dropdown_trafficlights.tpl' id='mapNotKnowPreLastname' label="{s name="fieldlabel/mapNotKnowPreLastname"}Vor- und Nachname nicht bekannt{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255"}
                {include file='backend/fc_payone/include/dropdown_trafficlights.tpl' id='mapMultiNameToAdress' label="{s name="fieldlabel/mapMultiNameToAdress"}Mehrdeutigkeit bei Name zu Anschrift{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255"}
                {include file='backend/fc_payone/include/dropdown_trafficlights.tpl' id='mapUndeliverable' label="{s name="fieldlabel/mapUndeliverable"}nicht (mehr) zustellbar{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255"}
                {include file='backend/fc_payone/include/dropdown_trafficlights.tpl' id='mapPersonDead' label="{s name="fieldlabel/mapPersonDead"}Person verstorben{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255"}
                {include file='backend/fc_payone/include/dropdown_trafficlights.tpl' id='mapWrongAdress' label="{s name="fieldlabel/mapWrongAdress"}Adresse postalisch falsch{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255"}
                {include file='backend/fc_payone/include/dropdown_trafficlights.tpl' id='mapAddressCheckNotPossible' label="{s name="fieldlabel/mapAddressCheckNotPossible"}Überprüfung nicht möglich (z.B. Fakename){/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255"}
                {include file='backend/fc_payone/include/dropdown_trafficlights.tpl' id='mapAddressOkayBuildingUnknown' label="{s name="fieldlabel/mapAddressOkayBuildingUnknown"}Adresse korrekt, aber Gebäude unbekannt{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255"}
                {include file='backend/fc_payone/include/dropdown_trafficlights.tpl' id='mapPersonMovedAddressUnknown' label="{s name="fieldlabel/mapPersonMovedAddressUnknown"}Person umgezogen, Adresse nicht korrigiert{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255"}
                {include file='backend/fc_payone/include/dropdown_trafficlights.tpl' id='mapUnknownReturnValue' label="{s name="fieldlabel/mapUnknownReturnValue"}Rückgabewert der Überprüfung unbekannt{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255"}
                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}
    <script type="text/javascript">
        {include file='backend/fc_payone/include/javascript.tpl.js' form="#addresscheckform" loadAction="generalconfigdata" saveAction="ajaxSavePayoneConfig"}
    </script>
{/block}
