{extends file="parent:backend/_base/layout.tpl"}

{block name="content/main"}
    {namespace name=backend/mopt_config_payone/main}
    <div class="col-md-12">
        <h3>{s name="global-form/paypalexpress"}Paypal Express{/s}</h3>
        <div>
            {s name="global-form/fieldset2Desc"}Stellen Sie hier die Konfiguration zur Zahlart Paypal Express ein.{/s}
        </div>
        <div class='col-md-12'>
            <form role="form" id="paypalexpress" class="form-horizontal">
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='paypalExpressUseDefaultShipping' label="{s name="fieldlabel/paypalExpressUseDefaultShipping"}Vorläufige Versandkosten bei Paypal Express übergeben{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/paypalExpressUseDefaultShipping"}Wenn aktiviert, werden die vorläufigen Versandkosten mit an Paypal Express übergeben{/s}"}
                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>

    </div>
{/block}

{block name="resources/javascript" append}
    {include file='backend/fc_payone/include/paypalexpress.js'}
{/block}
