{extends file="parent:backend/_base/layout.tpl"}
{namespace name=backend/mopt_config_payone/main}
{block name="content/main"}
    <div class="col-md-12">
        <h3>{s name="global-form/fieldset2"}Einstellungen für Finanzierungs-basierte Zahlarten{/s}</h3>
        <div>
            {s name="fieldlabelhelp/financeInfotext"}Stellen Sie hier die Konfiguration zu den Zahlarten Klarna, Unzer und Ratepay ein.
            {/s}
        </div>
        {include file='backend/fc_payone/include/dropdown_payments.tpl'}
        <div class='col-md-12'>
            <form role="form" id="ajaxfinanceform" class="form-horizontal">
                {include file='backend/fc_payone/include/input_text.tpl' id='klarnastoreid' label="{s name="fieldlabel/klarnaStoreId"}Klarna Store-ID{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" content="{s name="fieldlabelhelp/klarnaStoreId"}Klarna Store-ID{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='payolutionCompanyName' label="{s name="fieldlabel/payolutionCompanyName"}Unzer Firmenname{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" content="{s name="fieldlabelhelp/payolutionCompanyName"}Unzer Firmenname{/s}"}
                {include file='backend/fc_payone/include/input_checkbox.tpl' id='payolutionB2bmode' label="{s name="fieldlabel/payolutionB2bmode"}Unzer B2B Mode {/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/payolutionB2bMode"}Unzer B2B Modus{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='payolutionDraftUser' label="{s name="fieldlabel/payolutionDraftUser"}Unzer HTTP-Benutzername{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" content="{s name="fieldlabelhelp/payolutionDraftUser"}Unzer HTTP-Benutzername{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='payolutionDraftPassword' label="{s name="fieldlabel/payolutionDraftPassword"}Unzer HTTP-Passwort{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" content="{s name="fieldlabelhelp/payolutionDraftPassword"}Unzer HTTP-Passwort{/s}"}
                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>

        <a style="font-size: 28px" href="#" data-bs-target="#ratepayconfigs">{s name="global-form/config"}Konfiguration{/s} Ratepay</a>
    </div>
{/block}

{block name="resources/javascript" append}
    {include file='backend/fc_payone/include/javascript.tpl' form="#ajaxfinanceform" action="ajaxgetAddressCheckConfig"}
    <script type="text/javascript">

        var form = $('#ajaxfinanceform');
        var ratepayform = $('#ajaxratepay');
        var url = "{url controller=FcPayone action=ajaxgetFinanceConfig forceSecure}";
        var url_config = "{url controller=FcPayone action=ajaxgetGeneralConfig forceSecure}";
        var ratepaydownloadurl = "{url controller=MoptPayoneRatepay action=downloadRatepayConfigs forceSecure}";
        var ratepaysaveurl = "{url controller=MoptPayoneRatepay action=saveRatepayConfigs forceSecure}";
        var ratepaydownloadbtn = $('#downloadbtn');
        var paymentid = null;

        $(document).ready(function ()
        {
            var params = "paymentid=0";
            var call = url + '?' + params;
            var call_config = url_config + '?' + params;
            form.validator('validate');

            $.ajax({
                url: call,
                type: 'POST',
                success: function (data) {
                    response = $.parseJSON(data);
                    if (response.status === 'success') {
                        populateForm(form, response.data);
                    }
                    if (response.status === 'error') {
                    }
                }
            });

            $.ajax({
                url: call_config,
                type: 'POST',
                success: function (data) {
                    response = $.parseJSON(data);
                    if (response.status === 'success') {
                        populateForm(form, response.data);
                    }
                    if (response.status === 'error') {
                    }
                }
            });
        });

        $(".dropdown-menu li a").click(function () {
            var params = "paymentid=" + this.id;
            var call = url + '?' + params;
            var call_config = url_config + '?' + params;
            var filterid = this.getAttribute("data-name");
            paymentid = this.id;

            $.ajax({
                url: call,
                type: 'POST',
                success: function (data) {
                    response = $.parseJSON(data);
                    if (response.status === 'success') {
                        if(/mopt_payone__fin_klarna/.test(filterid)){
                            $('#klarnastoreid').show();
                            } else {
                            $('#klarnastoreid').hide();
                        } 
                        if(/mopt_payone__fin_payolution/.test(filterid)){
                            $('#payolutionCompanyName').show();
                            } else {
                            $('#payolutionCompanyName').hide();
                        }    
                        if(/mopt_payone__fin_payolution/.test(filterid)){
                            $('#payolutionB2bmode').show();
                            } else {
                            $('#payolutionB2bmode').hide();
                        }   
                        if(/mopt_payone__fin_payolution/.test(filterid)){
                            $('#payolutionDraftUser').show();
                            } else {
                            $('#payolutionDraftUser').hide();
                        } 
                        if(/mopt_payone__fin_payolution/.test(filterid)){
                            $('#payolutionDraftPassword').show();
                            } else {
                            $('#payolutionDraftPassword').hide();
                        }                          
                        
                        populateForm(form, response.data);
                    }
                    if (response.status === 'error') {
                    }
                }
            });
            $.ajax({
                url: call_config,
                type: 'POST',
                success: function (data) {
                    response = $.parseJSON(data);
                    if (response.status === 'success') {
                        if(/mopt_payone__fin_klarna/.test(filterid)){
                            $('#klarnastoreid').show();
                            } else {
                            $('#klarnastoreid').hide();
                        } 
                        if(/mopt_payone__fin_payolution/.test(filterid)){
                            $('#payolutionCompanyName').show();
                            } else {
                            $('#payolutionCompanyName').hide();
                        }    
                        if(/mopt_payone__fin_payolution/.test(filterid)){
                            $('#payolutionB2bmode').show();
                            } else {
                            $('#payolutionB2bmode').hide();
                        }
                        if(/mopt_payone__fin_payolution/.test(filterid)){
                            $('#payolutionDraftUser').show();
                            } else {
                            $('#payolutionDraftUser').hide();
                        } 
                        if(/mopt_payone__fin_payolution/.test(filterid)){
                            $('#payolutionDraftPassword').show();
                            } else {
                            $('#payolutionDraftPassword').hide();
                        }                          
                        
                        populateForm(form, response.data);
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
            });            
            var url = 'ajaxSavePaymentConfig';
            $.post(url, values, function (response) {
                var data_array = $.parseJSON(response);
                showalert("Die Daten wurden gespeichert", "alert-success");
            });
        });

        ratepayform.on("submit", function (event) {
            event.preventDefault();
            ratepayvalues = ratepayform.serialize();
            var submitAction = $(this.id).context.activeElement.name;
            if (submitAction == 'downloadbtn') {
                var url = ratepaydownloadurl;
            } else {
                var url = ratepaysaveurl;

            }
            $.post(url, ratepayvalues, function (response) {
                var data_array = $.parseJSON(response);
                $('#ratepaytable tr').css('background-color','');
                if (data_array.errorElem && data_array.errorElem.length) {
                    if (data_array.errorElem.length > 0){
                        data_array.errorElem.forEach(markDownloadErrors);
                        showalert("Das Abrufen von " + data_array.errorElem.length + " Konfigurationen ist fehlgeschlagen", "alert-danger");
                    }
                } else {
                    showalert("Die Daten wurden gespeichert", "alert-success");
                    location.reload();
                }
            });

        });

        function removeRow(rowId) {
            $('#row' + rowId).remove();
        };

        function addRow() {
            var len = $('#ratepaytable tbody tr').length;
            var newRow = "<tr id='row" + len + "'><td><input name='row[" + len + "][id] id='id_" + len + "' type='text' style='max-width:125px;' class='form-control' value='' readonly='readonly' ></td>" +
                    "<td><input name='row[" + len + "][shopid] id='shopid_" + len + "' type='text' style='max-width:125px;' class='form-control'value=></td>" +
                    "<td>"+ "<select class='form-control' name='row[" + len + "][currency]' id='currency_" + len + "'>"+
                    "{foreach from=$currencies item=currency}<option value='{$currency->getId()}'>{$currency->getName()}</option>{/foreach}"+
                    "</select></td>"+
                    "<td>"+ "<select class='form-control' name='row[" + len + "][ratepayInstallmentMode]' id='ratepayInstallmentMode_" + len + "'>"+
                    "<option value='0' {if $ratepayconfig && $ratepayconfig->getRatepayInstallmentMode() == 0}selected='selected'{/if}>Vorkasse</option>"+
                    "<option value='1' {if $ratepayconfig && $ratepayconfig->getRatepayInstallmentMode() == 1}selected='selected'{/if}>Lastschrift</option>"+
                    "</select></td>"+
                    "<td role='button' name='delete' value='delete' onclick='removeRow(" + len + ");'><img id='delete_" + len + "' height='100%' src='{link file="backend/_resources/images/delete.png"}'></td>" +
                    "</tr>";
            $('#ratepaytable > tbody:last-child').append(newRow);
        };

        function markDownloadErrors(item, index) {
            var d = document.getElementById('row' + item);
            d.style.backgroundColor = "red";
        }

    </script>
{/block}
