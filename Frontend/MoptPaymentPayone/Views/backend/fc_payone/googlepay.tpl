{extends file="parent:backend/_base/layout.tpl"}

{block name="content/main"}
    {namespace name=backend/mopt_config_payone/main}
    <div class="col-md-9">
        <h3>{s name="global-form/googlepay"}Konfiguration GooglePay{/s}</h3>
        <div>
            {s name="global-form/googlepayDesc"}Stellen Sie hier die Konfiguration zur Zahlart GooglePay ein.{/s}
        </div>
        {include file='backend/fc_payone/include/dropdown_payments.tpl'}
        <div class='col-md-9'>
            <form role="form" id="googlepayform" class="form-horizontal">
                {include file='backend/fc_payone/include/input_text.tpl' id='googlepayMerchantId' label="{s name="fieldlabel/googlepayMerchantId"}GooglePay MerchantId{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/googlepayMerchantId"}GooglePay MerchantId{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='googlepayAllowVisa' label="{s name="fieldlabel/googlepayAllowVisa"}GooglePay Visa zulassen{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/googlepayAllowVisa"}GooglePay Visa zulassen{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='googlepayAllowMasterCard' label="{s name="fieldlabel/googlepayAllowMasterCard"}GooglePay Mastercard zulassen{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/googlepayAllowMasterCard"}GooglePay Mastercard zulassen{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='googlepayAllowPrepaidCards' label="{s name="fieldlabel/googlepayAllowPrepaidCards"}GooglePay Prepaid Karten zulassen{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/googlepayAllowPrepaidCards"}GooglePay Prepaid Karten zulassen{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='googlepayAllowCreditCards' label="{s name="fieldlabel/googlepayAllowCreditCards"}GooglePay Kreditkarten zulassen{/s}" pattern="^[0-9]*" content="{s name="fieldlabel/googlepayAllowCreditCards"}GooglePay Kreditkarten zulassen{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='googlepayCountryCode' label="{s name="fieldlabel/googlepayCountryCode"}GooglePay Acquirer Ländercode{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabel/googlepayCountryCode"}GooglePay Acquirer Ländercode{/s}"}
                {include file='backend/fc_payone/include/dropdown_googlepaybuttoncolor.tpl' id='googlepayButtonColor' label="{s name="fieldlabel/googlepayButtonColor"}Googlepay Button Farbe{/s}" pattern="^[0-9]*" content="{s name="fieldlabel/googlepayButtonColor"}Googlepay Button Farbe{/s}"}
                {include file='backend/fc_payone/include/dropdown_googlepaybuttontype.tpl' id='googlepayButtonType' label="{s name="fieldlabel/googlepayButtonType"}GooglePay Button Typ{/s}" pattern="^[0-9]*" content="{s name="fieldlabel/googlepayButtonType"}GooglePay Button Typ{/s}"}
                <button type="submit" class="btn-payone btn ">{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}
    <script type="text/javascript">
        {include file='backend/fc_payone/include/javascript.tpl.js' form="#googlepayform" loadAction="generalconfigdata" saveAction="ajaxSavePayoneConfig"}
    </script>
{/block}
