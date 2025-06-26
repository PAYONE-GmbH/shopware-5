{extends file="parent:backend/_base/layout.tpl"}
{namespace name=backend/mopt_config_payone/main}
{block name="content/main"}
    <div class="col-md-12">
        <h3>{s name="global-form/fieldset8"}Klarna{/s}</h3>
        <div>
            {s name="fieldlabel/fieldset8Desc"}Stellen Sie hier die Konfiguration für Klarna ein.{/s}
        </div>
        <div class='col-md-12'>
            <form role="form" id="klarnaform" class="form-horizontal">
                {include file='backend/fc_payone/include/dropdown_payments.tpl'}
                {include file='backend/fc_payone/include/input_text.tpl' id='klarnaStoreId' label="{s name="fieldlabel/klarnaStoreId"}Klarna Store-ID{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" content="{s name="fieldlabelhelp/klarnaStoreId"}Klarna Store-ID{/s}'"}
                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}
    <script type="text/javascript">
        {include file='backend/fc_payone/include/javascript.tpl.js' form="#klarnaform" loadAction="generalconfigdata" saveAction="ajaxSavePayoneConfig"}
    </script>
{/block}
