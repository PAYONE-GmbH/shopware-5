{extends file="parent:backend/_base/layout.tpl"}

{block name="content/main"}
    {namespace name=backend/mopt_config_payone/main}
    <div class="col-md-12">
        <h3>{s name="global-form/paypalexpress"}PayPal Express{/s} V2</h3>
        <div>
            {s name="global-form/fieldset7Desc"}Stellen Sie hier die Konfiguration zur Zahlart PayPal Express V2 ein.{/s}
        </div>
        <div class='col-md-12'>
            <form role="form" id="paypalexpressv2form" class="form-horizontal">
                {include file='backend/fc_payone/include/dropdown_payments.tpl'}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='paypalV2ShowButton' label="{s name="fieldlabel/paypalV2ShowButton"}Paypal V2 BNPL Button anzeigen{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/paypalV2ShowButton"}{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='paypalV2MerchantId' label="{s name='fieldlabel/paypalV2MerchantId'}Paypal V2 Merchant ID{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name='fieldlabelhelp/paypalV2MerchantId'}Im Testmodus NICHT erforderlich. Da wird eine feste ID von Payone verwendet.{/s}"}
                {include file='backend/fc_payone/include/dropdown_paypalv2buttoncolor.tpl' id='paypalV2ButtonColor' label="{s name="fieldlabel/paypalV2ButtonColor"}Paypal V2 Express Button Farbe{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/paypalV2ButtonColor"}Paypal V2 Express Button Farbe{/s}"}
                {include file='backend/fc_payone/include/dropdown_paypalv2buttonshape.tpl' id='paypalV2ButtonShape' label="{s name="fieldlabel/paypalV2ButtonShape"}Paypal V2 Express Button Form{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/paypalV2ButtonShape"}Paypal V2 Express Button Form{/s}"}
                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}
    <script type="text/javascript">
        {include file='backend/fc_payone/include/javascript.tpl.js' form="#paypalexpressv2form" loadAction="generalconfigdata" saveAction="ajaxSavePayoneConfig"}
    </script>
{/block}
