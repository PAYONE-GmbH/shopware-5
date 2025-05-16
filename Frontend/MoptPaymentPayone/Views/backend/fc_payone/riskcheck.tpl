{extends file="parent:backend/_base/layout.tpl"}
{namespace name=backend/mopt_config_payone/main}
{block name="content/main"}
    <div class="col-md-12">
        <h3>{s name="global-form/fieldset3"}Bonitätsprüfung{/s}</h3>
        <div>
            {s name="fieldvalue/riskcheckTitle"}Stellen Sie hier die Konfiguration für die Bonitätsprüfung für alle Zahlarten ein.{/s}
        </div>
        {include file='backend/fc_payone/include/dropdown_payments.tpl'}
        <div class='col-md-12'>
            <form role="form" id="riskcheckform" class="form-horizontal">
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='consumerscoreActive' label="{s name="fieldlabel/active"}Aktiv{/s}" pattern="^[0-9]*"}
                {include file='backend/fc_payone/include/dropdown_livetest.tpl' id='consumerscoreLiveMode' label="{s name="fieldlabel/mode"}Betriebsmodus{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" content="{s name="fieldlabelhelp/liveMode"}Hier wird definiert wie die Zahlart verwendet wird. Live = Zahlungen werden auf der PAYONE-Plattform ausgeführt Test = Zahlungen werden nur auf der PAYONE-Testumgebung simuliert{/s}"}
                {include file='backend/fc_payone/include/dropdown_prepostpayment.tpl' id='consumerscoreCheckMoment' label="{s name="fieldlabel/consumerscoreCheckMoment"}Zeitpunkt der Prüfung{/s}" pattern="^[0-9]*"}
                {include file='backend/fc_payone/include/dropdown_infoscoreb2c.tpl' id='consumerscoreCheckModeB2C' label="{s name="fieldlabel/consumerscoreCheckModeB2C"}Prüfungsart B2C{/s}" pattern="^[0-9]*"}
                {include file='backend/fc_payone/include/dropdown_infoscoreb2b.tpl' id='consumerscoreCheckModeB2B' label="{s name="fieldlabel/consumerscoreCheckModeB2B"}Prüfungsart B2B{/s}" pattern="^[0-9]*"}
                {include file='backend/fc_payone/include/dropdown_trafficlights.tpl' id='consumerscoreDefault' label="{s name="fieldlabel/consumerscoreDefault"}Standardwert für Neukunden{/s}" pattern="^[0-9]*"}
                {include file='backend/fc_payone/include/dropdown_trafficlights.tpl' id='consumerscoreBoniversumUnknown' label="{s name="fieldlabel/consumerscoreBoniversumUnknown"}Boniversum unbekannt{/s}" pattern="^[0-9]*"}
                {include file='backend/fc_payone/include/input_text.tpl' id='consumerscoreLifetime' label="{s name="fieldlabel/lifetime"}Gültigkeit{/s}" pattern="^[0-9]*"}
                {include file='backend/fc_payone/include/input_text.tpl' id='consumerscoreMinBasket' label="{s name="fieldlabel/minBasket"}Minimaler Warenwert{/s}" pattern="^[0-9]*"}
                {include file='backend/fc_payone/include/input_text.tpl' id='consumerscoreMaxBasket' label="{s name="fieldlabel/maxBasket"}Maximaler Warenwert{/s}" pattern="^[0-9]*"}
                {include file='backend/fc_payone/include/dropdown_cancelcontinue.tpl' id='consumerscoreFailureHandling' label="{s name="fieldlabel/failureHandling"}Fehlverhalten{/s}" pattern="^[0-9]*"}
                {include file='backend/fc_payone/include/input_checkbox.tpl' id='consumerscoreNoteActive' label="{s name="fieldlabel/consumerscoreNoteActive"}Hinweistext{/s} {s name="fieldlabel/active"}Aktiv{/s}" pattern="^[0-9]*"}
                {include file='backend/fc_payone/include/input_text.tpl' id='consumerscoreNoteMessage' label="{s name="fieldlabel/consumerscoreNote"}Hinweistext (nur bei Prüfung nach der Zahlartenauswahl){/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*'}
                {include file='backend/fc_payone/include/input_checkbox.tpl' id='consumerscoreAgreementActive' label="{s name="fieldlabel/consumerscoreAgreementActive"}Zustimmungsfrage{/s} {s name="fieldlabel/active"}Aktiv{/s}" pattern="^[0-9]*"}
                {include file='backend/fc_payone/include/input_text.tpl' id='consumerscoreAgreementMessage' label="{s name="fieldlabel/consumerscoreAgreementMessage"}Zustimmungsfrage (nur bei Prüfung nach der Zahlartenauswahl){/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*'}
                {include file='backend/fc_payone/include/input_checkbox.tpl' id='consumerscoreAbtestActive' label="{s name="fieldlabel/abtest"}A/B Test{/s} {s name="fieldlabel/active"}Aktiv{/s}" pattern="^[0-9]*"}
                {include file='backend/fc_payone/include/input_text.tpl' id='consumerscoreAbtestValue' label="{s name="fieldlabel/abtest"}A/B Test{/s}" pattern="^[0-9]*"}
                <button type="submit" class="btn-payone btn ">{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}
    <script type="text/javascript">
        {include file='backend/fc_payone/include/javascript.tpl.js' form="#riskcheckform" loadAction="generalconfigdata" saveAction="ajaxSavePayoneConfig"}
    </script>
{/block}
