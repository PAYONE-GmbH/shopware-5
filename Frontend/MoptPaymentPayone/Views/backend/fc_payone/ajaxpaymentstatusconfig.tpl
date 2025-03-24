{extends file="parent:backend/_base/layout.tpl"}


{block name="content/main"}
    <div class="col-md-12">
        <h3>{s name="global-form/fieldset4"}Paymentstatus{/s}</h3>
        <div>
            Stellen Sie hier die Konfiguration für das Paymentstatus-Mapping  für alle Zahlarten ein.
        </div>
        {include file='backend/fc_payone/include/dropdown_payments.tpl'}
        <div class='col-md-12'>
            <form role="form" id="ajaxpaymentstatusconfigform" class="form-horizontal">
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
        
        var form = $('#ajaxpaymentstatusconfigform');
        var url = "{url controller=FcPayone action=ajaxgetPaymentStatusConfig forceSecure}";
        var paymentid = 0;
        
        $(document).ready(function ()
        {
            var params = "paymentid=0";
            var call = url + '?' + params;
           
            $.ajax({
                url: call,
                type: 'POST',
                success: function (data) {
                    response = $.parseJSON(data);
                    if (response.status === 'success') {
                        populateForm(form,response.data);
                    }
                    if (response.status === 'error') {
                    }
                }
            });
        });

        $(".dropdown-menu li a").click(function () {
            var params = "paymentid=" + this.id;
            var call = url + '?' + params;
            paymentid  = this.id;

            $.ajax({
                url: call,
                type: 'POST',
                success: function (data) {
                    response = $.parseJSON(data);
                    if (response.status === 'success') {
                        populateForm(form,response.data);
                    }
                    if (response.status === 'error') {
                    }
                }
            });
        });

        form.on("submit", function (event) {
            event.preventDefault();
            var checkboxes = form.find('input[type="checkbox"]');
            $.each(checkboxes, function (key, value) {
                if (value.checked === false) {
                    value.value = 0;
                } else {
                    value.value = 1;
                }
                $(value).attr('type', 'hidden');
            });
            values = form.serialize();
            $.each(checkboxes, function (key, value) {
                $(value).attr('type', 'checkbox');
            });
            var url = 'ajaxSavePayoneConfig';
            values = values + '&paymentId=' + paymentid;
            $.post(url, values, function (response) {
                var data_array = $.parseJSON(response);
                showalert("Die Daten wurden gespeichert", "alert-success");
            });
        });          
    </script>
{/block}
