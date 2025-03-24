{extends file="parent:backend/_base/layout.tpl"}
{namespace name=backend/mopt_config_payone/main}
{block name="content/main"}
    <div class="col-md-12">
        <h3>{s name="global-form/fieldset2"}Kontobasierte Einstellungen{/s}</h3>
        <div>
            {s name="global-form/fieldset3Desc"}Stellen Sie hier die Konfiguration zu den Zahlarten Lastschrift, Rechnung, Nachnahme und Vorkasse ein.{/s}
        </div>

        {include file='backend/fc_payone/include/dropdown_payments.tpl'}

        <div class='col-md-12'>
            <form role="form" id="ajaxdebitform" class="form-horizontal">
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='showAccountnumber' label="{s name="fieldlabel/showAccountnumber"}Zusätzlich Kontonummer/Bankleitzahl anzeigen?{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" content=""}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='showBIC' label="{s name="fieldlabel/showBIC"}Zusätzlich zur IBAN auch BIC abfragen?{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" content=""}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='mandateActive' label="{s name="fieldlabel/mandateActive"}Mandatserteilung aktivieren?{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" content=""}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='mandateDownloadEnabled' label="{s name="fieldlabel/mandateDownloadEnabled"}Download Mandat als PDF?{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" content=""}
                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}

    <script type="text/javascript">

        var form = $('#ajaxdebitform');
        var url = "{url controller=FcPayone action=ajaxgetDebitConfig forceSecure}";
        var paymentid = null;

        $(document).ready(function ()
        {
            var params = "paymentid=0";
            var call = url + '?' + params;

            form.validator('validate');
            if (paymentid != 22) {
                $('.lastschrift').hide();
            } else {
                $('.lastschrift').show();
            }
            $.ajax({
                url: call,
                type: 'POST',
                success: function (data) {
                    response = $.parseJSON(data);
                    if (response.status === 'success') {
                        populateForm(form, response.data);
                        form.validator('validate');
                    }
                    if (response.status === 'error') {
                    }
                }
            });
        });

        $(".dropdown-menu li a").click(function () {
            var params = "paymentid=" + this.id;
            var call = url + '?' + params;
            paymentid = this.id;

            $.ajax({
                url: call,
                type: 'POST',
                success: function (data) {
                    response = $.parseJSON(data);
                    if (response.status === 'success') {
                        populateForm(form, response.data);
                        form.validator('validate');
                        if (paymentid != 22) {
                            $('.lastschrift').hide();
                        } else {
                            $('.lastschrift').show();
                        }
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
