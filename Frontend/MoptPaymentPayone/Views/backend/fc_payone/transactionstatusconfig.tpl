{extends file="parent:backend/_base/layout.tpl"}
{namespace name=backend/mopt_config_payone/main}
{block name="content/main"}
    <div class="col-md-12">
        <h3>{s name="global-form/fieldset5"}Transaktionsstatusweiterleitung{/s}</h3>
        <div>
            {s name="global-form/fieldconfigtransactionstatusforwarddesc"}Mehrere URLs können durch ; getrennt angegeben werden.{/s}
        </div>
        <div class="row">
            {include file='backend/fc_payone/include/dropdown_payments.tpl'}
        </div>
        <div class='col-md-12'>
            <form role="form" id="transactionstatusform" class="form-horizontal">
                {include file='backend/fc_payone/include/input_text.tpl' id='transAppointed' label="{s name="forwarding/status/appointed"}Appointed{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*a-zA-Z0-9]*'}
                {include file='backend/fc_payone/include/input_text.tpl' id='transCapture' label="{s name="forwarding/status/capture"}Capture{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*'}
                {include file='backend/fc_payone/include/input_text.tpl' id='transPaid' label="{s name="forwarding/status/paid"}Paid{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*'}
                {include file='backend/fc_payone/include/input_text.tpl' id='transUnderpaid' label="{s name="forwarding/status/underpaid"}Underpaid{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*'}
                {include file='backend/fc_payone/include/input_text.tpl' id='transCancelation' label="{s name="forwarding/status/cancelation"}Cancelation{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*'}
                {include file='backend/fc_payone/include/input_text.tpl' id='transRefund' label="{s name="forwarding/status/refund"}Refund{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*'}
                {include file='backend/fc_payone/include/input_text.tpl' id='transDebit' label="{s name="forwarding/status/debit"}Debit{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*'}
                {include file='backend/fc_payone/include/input_text.tpl' id='transReminder' label="{s name="forwarding/status/reminder"}Reminder{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*'}
                {include file='backend/fc_payone/include/input_text.tpl' id='transVauthorization' label="{s name="forwarding/status/VAutorisierung"}VAutorisierung{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*'}
                {include file='backend/fc_payone/include/input_text.tpl' id='transVsettlement' label="{s name="forwarding/status/VSettlement"}VSettlement{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*'}
                {include file='backend/fc_payone/include/input_text.tpl' id='transTransfer' label="{s name="forwarding/status/transfer"}Transfer{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*'}
                {include file='backend/fc_payone/include/input_text.tpl' id='transInvoice' label="{s name="forwarding/status/invoice"}Invoice{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*'}
                {include file='backend/fc_payone/include/input_text.tpl' id='transFailed' label="{s name="forwarding/status/failed"}Failed{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*'}
                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}
    <script type="text/javascript">
        {include file='backend/fc_payone/include/javascript.tpl.js' form="#transactionstatusform" loadAction="generalconfigdata" saveAction="ajaxSavePayoneConfig"}
    </script>
{/block}
