{extends file="parent:backend/_base/layout.tpl"}

{block name="content/main"}
    {namespace name=backend/mopt_config_payone/main}
    <div class="col-md-9">
        <h3>{s name="global-form/unzer"}Konfiguration Unzer{/s}</h3>
        <div>
            {s name="global-form/unzerDesc"}Stellen Sie hier die Konfiguration zur Zahlart Unzer ein.{/s}
        </div>
        {include file='backend/fc_payone/include/dropdown_payments.tpl'}
        <div class='col-md-9'>
            <form role="form" id="unzerform" class="form-horizontal">
                {include file='backend/fc_payone/include/input_text.tpl' id='payolutionCompanyName' label="{s name="fieldlabel/payolutionCompanyName"}Payolution Firmenname{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/payolutionCompanyName"}Payolution Firmenname{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='payolutionB2bmode' label="{s name="fieldlabel/payolutionB2bMode"}Payolution B2B Modus{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/payolutionB2bMode"}Payolution B2B Modus{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='payolutionDraftUser' label="{s name="fieldlabel/payolutionDraftUser"}Payolution-Benutzername{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/payolutionDraftUser"}Payolution HTTP-Benutzername{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='payolutionDraftPassword' label="{s name="fieldlabel/payolutionDraftPassword"}Payolution-Passwort{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/payolutionDraftPassword"}Payolution HTTP-Passwort{/s}"}
                <button type="submit" class="btn-payone btn ">{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}
    <script type="text/javascript">
        {include file='backend/fc_payone/include/javascript.tpl.js' form="#unzerform" loadAction="generalconfigdata" saveAction="ajaxSavePayoneConfig"}
    </script>
{/block}
