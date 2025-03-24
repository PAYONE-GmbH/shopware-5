{extends file="parent:backend/_base/layout.tpl"}
{namespace name=backend/mopt_config_payone/main}
{block name="content/main"}
    <div class="col-md-12">
        <h3>Onlineüberweisung Einstellungen</h3>
        <div>
            Stellen Sie hier die Konfiguration für Onlineüberweisungsbasierte Zahlarten ein.
        </div>
        {include file='backend/fc_payone/include/dropdown_payments.tpl'}
        <div class='col-md-12'>
            <form role="form" id="ajaxonlinetransferform" class="form-horizontal">
                {include file='backend/fc_payone/include/input_text.tpl' id='description' label="{s namespace="backend/payment/payment" name="formpanel_description_label"}Bezeichnung{/s}" pattern="^[0-9]*"}
                {include file='backend/fc_payone/include/input_text.tpl' id='additionalDescription' label="{s namespace="backend/payment/payment" name="formpanel_additional-description_label"}Zusätzliche Beschreibung{/s}" pattern="^[0-9]*"}
                {include file='backend/fc_payone/include/input_text.tpl' id='debitPercent' label="{s namespace="backend/payment/payment" name="formpanel_surcharge_label"}Aufschlag/Abschlag (in %){/s}" pattern="^[0-9]*"}
                {include file='backend/fc_payone/include/input_text.tpl' id='surcharge' label="{s namespace="backend/payment/payment" name="formpanel_generalSurcharge_label"}Pauschaler Aufschlag{/s}" pattern="^[0-9]*"}
                {include file='backend/fc_payone/include/input_text.tpl' id='position' label="{s namespace="backend/payment/payment" name="formpanel_position_surcharge"}Position{/s}" pattern="^[0-9]*"}
                {include file='backend/fc_payone/include/input_checkbox.tpl' id='active' label="{s namespace="backend/payment/payment" name="formpanel_active_label"}Aktiv{/s}" pattern="^[0-9]*"}
                {include file='backend/fc_payone/include/input_checkbox.tpl' id='esdActive' label="{s namespace="backend/payment/payment" name="formpanel_esdActive_label"}Aktiv für ESD-Produkte{/s}" pattern="^[0-9]*"}
                {include file='backend/fc_payone/include/input_checkbox.tpl' id='mobileInactive' label="{s namespace="backend/payment/payment" name="formpanel_mobileInactive_label"}Inaktiv für Smartphone{/s}" pattern="^[0-9]*"}
                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}
    <script type="text/javascript">

        var form = $('#ajaxonlinetransferform');
        var url = "{url controller=FcPayone action=ajaxgetOnlineTransferConfig forceSecure}";
        var paymentid = null;
        
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
            alert('clock');
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
            var url = 'ajaxSavePaymentConfig';
            values = values + '&paymentId=' + paymentid;
            $.post(url, values, function (response) {
                var data_array = $.parseJSON(response);
                showalert("Die Daten wurden gespeichert", "alert-success");
            });
        });     
    </script>
{/block}
