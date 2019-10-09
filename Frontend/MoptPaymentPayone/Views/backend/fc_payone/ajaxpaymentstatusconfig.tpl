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
    <div class="col-md-12">
        <h3>{s name=global-form/fieldset4}Paymentstatus{/s}</h3>
        <div>
            Stellen Sie hier die Konfiguration für das Paymentstatus-Mapping  für alle Zahlarten ein.
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="btn-group">
                    <button id="paymentmethodsdropdown" type="button" class="btn-payone-fixed btn-payone btn dropdown-toggle" data-toggle="dropdown">
                        <span class="selection">Alle Zahlarten - Global</span><span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#" id="0">Alle Zahlarten - Global</a></li>
                            {foreach from=$payonepaymentmethods item=paymentmethod}
                            <li><a href="#" id="{$paymentmethod.id}">{$paymentmethod.description}</a></li>
                            {/foreach}   
                    </ul>
                </div>
            </div>
        </div>
        <div class='col-md-12'>
            <form role="form" id="ajaxpaymentstatusconfigform" class="form-horizontal">
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="stateAppointed" class="text-left col-md-3 control-label">{s name=forwarding/status/appointed}Appointed{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="stateAppointed" name="stateAppointed" aria-describedby="stateAppointed-status" >
                            {foreach from=$payonepaymentstates item=paymentstate}
                                <option value="{$paymentstate.id}">{$paymentstate.description}</option>
                            {/foreach}   
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="stateAppointed-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="stateCapture" class="text-left col-md-3 control-label">{s name=forwarding/status/capture}Capture{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="stateCapture" name="stateCapture" aria-describedby="stateCapture-status" >
                            {foreach from=$payonepaymentstates item=paymentstate}
                                <option value="{$paymentstate.id}">{$paymentstate.description}</option>
                            {/foreach}   
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="stateCapture-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="statePaid" class="text-left col-md-3 control-label">{s name=forwarding/status/paid}Paid{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="statePaid" name="statePaid" aria-describedby="statePaid-status" >
                            {foreach from=$payonepaymentstates item=paymentstate}
                                <option value="{$paymentstate.id}">{$paymentstate.description}</option>
                            {/foreach}   
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="statePaid-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="stateUnderpaid" class="text-left col-md-3 control-label">{s name=forwarding/status/underpaid}Underpaid{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="stateUnderpaid" name="stateUnderpaid" aria-describedby="stateUnderpaid-status" >
                            {foreach from=$payonepaymentstates item=paymentstate}
                                <option value="{$paymentstate.id}">{$paymentstate.description}</option>
                            {/foreach}   
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="stateUnderpaid-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="stateCancelation" class="text-left col-md-3 control-label">{s name=forwarding/status/cancelation}Cancelation{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="stateCancelation" name="stateCancelation" aria-describedby="stateCancelation-status" >
                            {foreach from=$payonepaymentstates item=paymentstate}
                                <option value="{$paymentstate.id}">{$paymentstate.description}</option>
                            {/foreach}   
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="stateCancelation-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="stateRefund" class="text-left col-md-3 control-label">{s name=forwarding/status/refund}Refund{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="stateRefund" name="stateRefund" aria-describedby="stateRefund-status" >
                            {foreach from=$payonepaymentstates item=paymentstate}
                                <option value="{$paymentstate.id}">{$paymentstate.description}</option>
                            {/foreach}   
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="stateRefund-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>  
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="stateDebit" class="text-left col-md-3 control-label">{s name=forwarding/status/debit}Debit{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="stateDebit" name="stateDebit" aria-describedby="stateDebit-status" >
                            {foreach from=$payonepaymentstates item=paymentstate}
                                <option value="{$paymentstate.id}">{$paymentstate.description}</option>
                            {/foreach}   
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="stateDebit-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="stateReminder" class="text-left col-md-3 control-label">{s name=forwarding/status/reminder}Reminder{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="stateReminder" name="stateReminder" aria-describedby="stateReminder-status" >
                            {foreach from=$payonepaymentstates item=paymentstate}
                                <option value="{$paymentstate.id}">{$paymentstate.description}</option>
                            {/foreach}   
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="stateReminder-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="stateVauthorization" class="text-left col-md-3 control-label">{s name=forwarding/status/VAutorisierung}VAutorisierung{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="stateVauthorization" name="stateVauthorization" aria-describedby="stateVauthorization-status" >
                            {foreach from=$payonepaymentstates item=paymentstate}
                                <option value="{$paymentstate.id}">{$paymentstate.description}</option>
                            {/foreach}   
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="stateVauthorization-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>  
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="stateVsettlement" class="text-left col-md-3 control-label">{s name=forwarding/status/VSettlement}VSettlement{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="stateVsettlement" name="stateVsettlement" aria-describedby="stateVsettlement-status" >
                            {foreach from=$payonepaymentstates item=paymentstate}
                                <option value="{$paymentstate.id}">{$paymentstate.description}</option>
                            {/foreach}   
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="stateVsettlement-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="stateTransfer" class="text-left col-md-3 control-label">{s name=forwarding/status/transfer}Transfer{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="stateTransfer" name="stateTransfer" aria-describedby="stateTransfer-status" >
                            {foreach from=$payonepaymentstates item=paymentstate}
                                <option value="{$paymentstate.id}">{$paymentstate.description}</option>
                            {/foreach}   
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="stateTransfer-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="stateInvoice" class="text-left col-md-3 control-label">{s name=forwarding/status/invoice}Invoice{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="stateInvoice" name="stateInvoice" aria-describedby="stateInvoice-status" >
                            {foreach from=$payonepaymentstates item=paymentstate}
                                <option value="{$paymentstate.id}">{$paymentstate.description}</option>
                            {/foreach}   
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="stateInvoice-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">

                    <label for="stateFailed" class="text-left col-md-3 control-label">{s name=forwarding/status/failed}Failed{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="stateFailed" name="stateFailed" aria-describedby="stateFailed-status" >
                            {foreach from=$payonepaymentstates item=paymentstate}
                                <option value="{$paymentstate.id}">{$paymentstate.description}</option>
                            {/foreach}
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="stateFailed-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <button type="submit" class="btn-payone btn " >{s name=global-form/button}Speichern{/s}</button>
            </form>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}  
    <script type="text/javascript" src="{link file="backend/_resources/js/formhelper.js"}"></script>

    <script type="text/javascript">
        
        var form = $('#ajaxpaymentstatusconfigform');
        var url = "{url controller=FcPayone action=ajaxgetPaymentStatusConfig forceSecure}";
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
