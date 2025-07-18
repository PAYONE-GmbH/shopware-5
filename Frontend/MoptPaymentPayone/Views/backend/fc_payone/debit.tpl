{extends file="parent:backend/_base/layout.tpl"}
{namespace name=backend/mopt_config_payone/main}
{block name="content/main"}
    <div class="col-md-12">
        <h3>{s name="global-form/fieldset7"}Lastschrift{/s}</h3>
        <div>
            {s name="fieldlabel/fieldset7Desc"}Stellen Sie hier die Konfiguration für Lastschrift ein.{/s}
        </div>
        <div class='col-md-12'>
            <form role="form" id="debitform" class="form-horizontal">
                {include file='backend/fc_payone/include/dropdown_payments.tpl'}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='checkAccount' label="{s name="fieldlabel/checkAccount"}Bankdaten überprüfen{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" content=""}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='showAccountnumber' label="{s name="fieldlabel/showAccountnumber"}Zusätzlich Kontonummer/Bankleitzahl anzeigen?{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" content=""}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='showBic' label="{s name="fieldlabel/showBIC"}Zusätzlich zur IBAN auch BIC abfragen?{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" content=""}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='mandateActive' label="{s name="fieldlabel/mandateActive"}Mandatserteilung aktivieren?{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" content=""}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='mandateDownloadEnabled' label="{s name="fieldlabel/mandateDownloadEnabled"}Download Mandat als PDF?{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" content=""}
                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}
    <script type="text/javascript">
        {include file='backend/fc_payone/include/javascript.tpl.js' form="#debitform" loadAction="generalconfigdata" saveAction="ajaxSavePayoneConfig"}
    </script>
{/block}
