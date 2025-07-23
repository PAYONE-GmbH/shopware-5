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
                {include file='backend/fc_payone/include/input_text.tpl' id='unzerCompanyName' label="{s name="fieldlabel/unzerCompanyName"}Unzer Firmenname{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/unzerCompanyName"}Unzer Firmenname{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='unzerB2bmode' label="{s name="fieldlabel/unzerB2bMode"}Unzer B2B Modus{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/unzerB2bMode"}Unzer B2B Modus{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='unzerDraftUser' label="{s name="fieldlabel/unzerDraftUser"}Unzer-Benutzername{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/unzerDraftUser"}Unzer HTTP-Benutzername{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='unzerDraftPassword' label="{s name="fieldlabel/unzerDraftPassword"}Unzer-Passwort{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/unzerDraftPassword"}Unzer HTTP-Passwort{/s}"}
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
