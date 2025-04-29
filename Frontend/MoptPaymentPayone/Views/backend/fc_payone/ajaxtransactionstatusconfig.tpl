{extends file="parent:backend/_base/layout.tpl"}

{block name="content/breadcrump"}
                        <li>
                            {$breadcrump.0}
                        </li>
                        <li>
                            {$breadcrump.1}
                        </li>
                        <li class="active">
                            <a href="{url controller="FcPayone" action="{$breadcrump.2}"}">{$breadcrump.3}</a> <span class="divider">/</span>
                        </li> 
{/block}

{block name="content/main"}
    <div class="col-md-9">
        <h3>{s name="global-form/fieldset5"}Transaktionsstatusweiterleitung{/s}</h3>
        <div>
            {s name="forwarding/label"}Mehrere URLs können durch ; getrennt angegeben werden.{/s}
        </div>
        <div class="row">
            <div class="col-md-9">
                <div class="btn-group">
                    <button id="paymentmethodsdropdown" type="button" class="btn-payone-fixed btn-payone btn dropdown-toggle" data-toggle="dropdown">
                        <span class="selection">Alle Zahlarten - Global</span><span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#" id="0" >Alle Zahlarten - Global</a></li>
                            {foreach from=$payonepaymentmethods item=paymentmethod}
                            <li><a href="#" id="{$paymentmethod.id}">{$paymentmethod.description} - {$paymentmethod.name}</a></li>
                            {/foreach}   
                    </ul>
                </div>
            </div>
        </div>
        <div class='col-md-9'>
            <form role="form" id="ajaxtransactionstatusform" class="form-horizontal">
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="transAppointed" class="text-left col-md-3 control-label">{s name="forwarding/status/appointed"}Appointed{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="transAppointed" name="transAppointed" aria-describedby="transAppointed-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="transAppointed-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="transCapture" class="text-left col-md-3 control-label">{s name="forwarding/status/capture"}Capture{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="transCapture" name="transCapture" aria-describedby="transCapture-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="transCapture-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>  
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="transPaid" class="text-left col-md-3 control-label">{s name="forwarding/status/paid"}Paid{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="transPaid" name="transPaid" aria-describedby="transPaid-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="transPaid-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="transUnderpaid" class="text-left col-md-3 control-label">{s name="forwarding/status/underpaid"}Underpaid{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="transUnderpaid" name="transUnderpaid" aria-describedby="transUnderpaid-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="transUnderpaid-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="transCancelation" class="text-left col-md-3 control-label">{s name="forwarding/status/cancelation"}Cancelation{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="transCancelation" name="transCancelation" aria-describedby="transCancelation-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="transCancelation-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="transRefund" class="text-left col-md-3 control-label">{s name="forwarding/status/refund"}Refund{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="transRefund" name="transRefund" aria-describedby="transRefund-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="transRefund-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="transDebit" class="text-left col-md-3 control-label">{s name="forwarding/status/debit"}Debit{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="transDebit" name="transDebit" aria-describedby="transDebit-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="transDebit-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="transReminder" class="text-left col-md-3 control-label">{s name="forwarding/status/reminder"}Reminder{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="transReminder" name="transReminder" aria-describedby="transReminder-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="transReminder-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="transVauthorization" class="text-left col-md-3 control-label">{s name="forwarding/status/VAutorisierung"}VAutorisierung{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="transVauthorization" name="transVauthorization" aria-describedby="transVauthorization-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="transVauthorization-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="transVsettlement" class="text-left col-md-3 control-label">{s name="forwarding/status/VSettlement"}VSettlement{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="transVsettlement" name="transVsettlement" aria-describedby="transVsettlement-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="transVsettlement-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="transTransfer" class="text-left col-md-3 control-label">{s name="forwarding/status/transfer"}Transfer{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="transTransfer" name="transTransfer" aria-describedby="transTransfer-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="transTransfer-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="transInvoice" class="text-left col-md-3 control-label">{s name="forwarding/status/invoice"}Invoice{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="transInvoice" name="transInvoice" aria-describedby="transInvoice-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="transAppointed-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">

                    <label for="transFailed" class="text-left col-md-3 control-label">{s name="forwarding/status/failed"}Failed{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="transFailed" name="transFailed" aria-describedby="transFailed-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="transFailed-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}  
    <script type="text/javascript" src="{link file="backend/_resources/js/formhelper.js"}"></script>

    <script type="text/javascript">

        var form = $('#ajaxtransactionstatusform');
        var url = "{url controller=FcPayone action=ajaxgetTransactionStatusConfig forceSecure}";
        var paymentid = 0;
        
        $(document).ready(function ()
        {
            var params = "paymentid=0";
            var call = url + '?' + params;
            
            form.validator('validate');
           
            $.ajax({
                url: call,
                type: 'POST',
                success: function (data) {
                    response = $.parseJSON(data);
                    if (response.status === 'success') {
                        populateForm(form,response.data);
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
            paymentid  = this.id;

            $.ajax({
                url: call,
                type: 'POST',
                success: function (data) {
                    response = $.parseJSON(data);
                    if (response.status === 'success') {
                        populateForm(form,response.data);
                        form.validator('validate');
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
