{extends file="parent:backend/_base/layout.tpl"}

{block name="content/main"}
    {namespace name=backend/mopt_config_payone/main}
    <div class="col-md-9">
        <h3>{s name="global-form/googlepay"}Konfiguration Google Pay{/s}</h3>
        <div>
            {s name="global-form/googlepayDesc"}Stellen Sie hier die Konfiguration zur Zahlart Google Pay ein.{/s}
        </div>
        {include file='backend/fc_payone/include/dropdown_payments.tpl'}
        <div class='col-md-9'>
            <form role="form" id="googlepayform" class="form-horizontal">
                {include file='backend/fc_payone/include/input_text.tpl' id='googlepayMerchantId' label="{s name="fieldlabel/googlepayMerchantId"}Google Pay MerchantId{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/googlepayMerchantId"}Google Pay MerchantId{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='googlepayAllowVisa' label="{s name="fieldlabel/googlepayAllowVisa"}Google Pay Visa zulassen{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/googlepayAllowVisa"}Google Pay Visa zulassen{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='googlepayAllowMasterCard' label="{s name="fieldlabel/googlepayAllowMasterCard"}Google Pay Mastercard zulassen{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/googlepayAllowMasterCard"}Google Pay Mastercard zulassen{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='googlepayAllowPrepaidCards' label="{s name="fieldlabel/googlepayAllowPrepaidCards"}Google Pay Prepaid Karten zulassen{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/googlepayAllowPrepaidCards"}Google Pay Prepaid Karten zulassen{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='googlepayAllowCreditCards' label="{s name="fieldlabel/googlepayAllowCreditCards"}Google Pay Kreditkarten zulassen{/s}" pattern="^[0-9]*" content="{s name="fieldlabel/googlepayAllowCreditCards"}Google Pay Kreditkarten zulassen{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='googlepayCountryCode' label="{s name="fieldlabel/googlepayCountryCode"}Google Pay Acquirer Ländercode{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabel/googlepayCountryCode"}Google Pay Acquirer Ländercode{/s}"}
                {include file='backend/fc_payone/include/dropdown_googlepaybuttoncolor.tpl' id='googlepayButtonColor' label="{s name="fieldlabel/googlepayButtonColor"}Google Pay Button Farbe{/s}" pattern="^[0-9]*" content="{s name="fieldlabel/googlepayButtonColor"}Google Pay Button Farbe{/s}"}
                {include file='backend/fc_payone/include/dropdown_googlepaybuttontype.tpl' id='googlepayButtonType' label="{s name="fieldlabel/googlepayButtonType"}Google Pay Button Typ{/s}" pattern="^[0-9]*" content="{s name="fieldlabel/googlepayButtonType"}Google Pay Button Typ{/s}"}
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
