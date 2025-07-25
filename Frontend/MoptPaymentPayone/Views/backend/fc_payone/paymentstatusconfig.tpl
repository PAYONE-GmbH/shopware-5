{extends file="parent:backend/_base/layout.tpl"}
{namespace name=backend/mopt_config_payone/main}
{block name="content/main"}
    <div class="col-md-12">
        <h3>{s name="global-form/fieldset4"}Paymentstatus{/s}</h3>
        <div>
            {s name="global-form/fieldset10"}Stellen Sie hier die Konfiguration für das Paymentstatus-Mapping  für alle Zahlarten ein.{/s}
        </div>
        {include file='backend/fc_payone/include/dropdown_payments.tpl'}
        <div class='col-md-12'>
            <form role="form" id="paymentstatusconfigform" class="form-horizontal">
                {include file='backend/fc_payone/include/dropdown_paymentstates.tpl' id='stateAppointed' label="{s name="forwarding/status/appointed"}Appointed{/s}"}
                {include file='backend/fc_payone/include/dropdown_paymentstates.tpl' id='stateCapture' label="{s name="forwarding/status/capture"}Capture{/s}"}
                {include file='backend/fc_payone/include/dropdown_paymentstates.tpl' id='statePaid' label="{s name="forwarding/status/paid"}Paid{/s}"}
                {include file='backend/fc_payone/include/dropdown_paymentstates.tpl' id='stateUnderpaid' label="{s name="forwarding/status/underpaid"}Underpaid{/s}"}
                {include file='backend/fc_payone/include/dropdown_paymentstates.tpl' id='stateCancelation' label="{s name="forwarding/status/cancelation"}Cancelation{/s}"}
                {include file='backend/fc_payone/include/dropdown_paymentstates.tpl' id='stateRefund' label="{s name="forwarding/status/refund"}Refund{/s}"}
                {include file='backend/fc_payone/include/dropdown_paymentstates.tpl' id='stateDebit' label="{s name="forwarding/status/debit"}Debit{/s}"}
                {include file='backend/fc_payone/include/dropdown_paymentstates.tpl' id='stateReminder' label="{s name="forwarding/status/reminder1"}Reminder (1){/s}"}
                {include file='backend/fc_payone/include/dropdown_paymentstates.tpl' id='stateReminder2' label="{s name="forwarding/status/reminder2"}Reminder (2){/s}"}
                {include file='backend/fc_payone/include/dropdown_paymentstates.tpl' id='stateReminder3' label="{s name="forwarding/status/reminder2"}Reminder (3){/s}"}
                {include file='backend/fc_payone/include/dropdown_paymentstates.tpl' id='stateReminder4' label="{s name="forwarding/status/reminder2"}Reminder (4){/s}"}
                {include file='backend/fc_payone/include/dropdown_paymentstates.tpl' id='stateReminder5' label="{s name="forwarding/status/reminder2"}Reminder (5){/s}"}
                {include file='backend/fc_payone/include/dropdown_paymentstates.tpl' id='stateReminderS' label="{s name="forwarding/status/reminder2"}Reminder (S){/s}"}
                {include file='backend/fc_payone/include/dropdown_paymentstates.tpl' id='stateReminderM' label="{s name="forwarding/status/reminder2"}Reminder (M){/s}"}
                {include file='backend/fc_payone/include/dropdown_paymentstates.tpl' id='stateReminderI' label="{s name="forwarding/status/reminder2"}Reminder (I){/s}"}
                {include file='backend/fc_payone/include/dropdown_paymentstates.tpl' id='stateVauthorization' label="{s name="forwarding/status/VAutorisierung"}VAutorisierung{/s}"}
                {include file='backend/fc_payone/include/dropdown_paymentstates.tpl' id='stateVsettlement' label="{s name="forwarding/status/VSettlement"}VSettlement{/s}"}
                {include file='backend/fc_payone/include/dropdown_paymentstates.tpl' id='stateTransfer' label="{s name="forwarding/status/transfer"}Transfer{/s}"}
                {include file='backend/fc_payone/include/dropdown_paymentstates.tpl' id='stateInvoice' label="{s name="forwarding/status/invoice"}Invoice{/s}"}
                {include file='backend/fc_payone/include/dropdown_paymentstates.tpl' id='stateFailed' label="{s name="forwarding/status/failed"}Failed{/s}"}
                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}
    <script type="text/javascript">
        {include file='backend/fc_payone/include/javascript.tpl.js' form="#paymentstatusconfigform" loadAction="generalconfigdata" saveAction="ajaxSavePayoneConfig"}
    </script>
{/block}
