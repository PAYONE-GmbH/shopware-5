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
        <h3>{s name="global-form/fieldset4"}Paymentstatus{/s}</h3>
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
                   
                    <label for="stateAppointed" class="text-left col-md-3 control-label">{s namespace="backend/mopt_config_payone/main" name="forwarding/status/appointed"}Appointed{/s}</label>
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
                   
                    <label for="stateCapture" class="text-left col-md-3 control-label">{s namespace="backend/mopt_config_payone/main" name="forwarding/status/capture"}Capture{/s}</label>
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
                   
                    <label for="statePaid" class="text-left col-md-3 control-label">{s namespace="backend/mopt_config_payone/main" name="forwarding/status/paid"}Paid{/s}</label>
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
                   
                    <label for="stateUnderpaid" class="text-left col-md-3 control-label">{s namespace="backend/mopt_config_payone/main" name="forwarding/status/underpaid"}Underpaid{/s}</label>
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
                   
                    <label for="stateCancelation" class="text-left col-md-3 control-label">{s namespace="backend/mopt_config_payone/main" name="forwarding/status/cancelation"}Cancelation{/s}</label>
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
                   
                    <label for="stateRefund" class="text-left col-md-3 control-label">{s namespace="backend/mopt_config_payone/main" name="forwarding/status/refund"}Refund{/s}</label>
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
                   
                    <label for="stateDebit" class="text-left col-md-3 control-label">{s namespace="backend/mopt_config_payone/main" name="forwarding/status/debit"}Debit{/s}</label>
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

                    <label for="stateReminder" class="text-left col-md-3 control-label">{s namespace="backend/mopt_config_payone/main" name="forwarding/status/reminder1"}Reminder (1){/s}</label>
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
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">

                    <label for="stateReminder2" class="text-left col-md-3 control-label">{s namespace="backend/mopt_config_payone/main" name="forwarding/status/reminder2"}Reminder (2){/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="stateReminder2" name="stateReminder2" aria-describedby="stateReminder2-status" >
                            {foreach from=$payonepaymentstates item=paymentstate}
                                <option value="{$paymentstate.id}">{$paymentstate.description}</option>
                            {/foreach}
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="stateReminder2-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">

                    <label for="stateReminder3" class="text-left col-md-3 control-label">{s namespace="backend/mopt_config_payone/main" name="forwarding/status/reminder3"}Reminder (3){/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="stateReminder3" name="stateReminder3" aria-describedby="stateReminder3-status" >
                            {foreach from=$payonepaymentstates item=paymentstate}
                                <option value="{$paymentstate.id}">{$paymentstate.description}</option>
                            {/foreach}
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="stateReminder3-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">

                    <label for="stateReminder4" class="text-left col-md-3 control-label">{s namespace="backend/mopt_config_payone/main" name="forwarding/status/reminder4"}Reminder (4){/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="stateReminder4" name="stateReminder4" aria-describedby="stateReminder4-status" >
                            {foreach from=$payonepaymentstates item=paymentstate}
                                <option value="{$paymentstate.id}">{$paymentstate.description}</option>
                            {/foreach}
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="stateReminder4-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">

                    <label for="stateReminder5" class="text-left col-md-3 control-label">{s namespace="backend/mopt_config_payone/main" name="forwarding/status/reminder5"}Reminder (5){/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="stateReminder5" name="stateReminder5" aria-describedby="stateReminder5-status" >
                            {foreach from=$payonepaymentstates item=paymentstate}
                                <option value="{$paymentstate.id}">{$paymentstate.description}</option>
                            {/foreach}
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="stateReminder5-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">

                    <label for="stateReminderA" class="text-left col-md-3 control-label">{s namespace="backend/mopt_config_payone/main" name="forwarding/status/reminderA"}Reminder (A){/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="stateReminderA" name="stateReminderA" aria-describedby="stateReminderA-status" >
                            {foreach from=$payonepaymentstates item=paymentstate}
                                <option value="{$paymentstate.id}">{$paymentstate.description}</option>
                            {/foreach}
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="stateReminderA-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">

                    <label for="stateReminderS" class="text-left col-md-3 control-label">{s namespace="backend/mopt_config_payone/main" name="forwarding/status/reminderS"}Reminder (S){/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="stateReminderS" name="stateReminderS" aria-describedby="stateReminderS-status" >
                            {foreach from=$payonepaymentstates item=paymentstate}
                                <option value="{$paymentstate.id}">{$paymentstate.description}</option>
                            {/foreach}
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="stateReminderS-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">

                    <label for="stateReminderM" class="text-left col-md-3 control-label">{s namespace="backend/mopt_config_payone/main" name="forwarding/status/reminderM"}Reminder (M){/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="stateReminderM" name="stateReminderM" aria-describedby="stateReminderM-status" >
                            {foreach from=$payonepaymentstates item=paymentstate}
                                <option value="{$paymentstate.id}">{$paymentstate.description}</option>
                            {/foreach}
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="stateReminderM-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">

                    <label for="stateReminderI" class="text-left col-md-3 control-label">{s namespace="backend/mopt_config_payone/main" name="forwarding/status/reminderI"}Reminder (I){/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="stateReminderI" name="stateReminderI" aria-describedby="stateReminderI-status" >
                            {foreach from=$payonepaymentstates item=paymentstate}
                                <option value="{$paymentstate.id}">{$paymentstate.description}</option>
                            {/foreach}
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="stateReminderI-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="stateVauthorization" class="text-left col-md-3 control-label">{s namespace="backend/mopt_config_payone/main" name="forwarding/status/VAutorisierung"}VAutorisierung{/s}</label>
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
                   
                    <label for="stateVsettlement" class="text-left col-md-3 control-label">{s namespace="backend/mopt_config_payone/main" name="forwarding/status/VSettlement"}VSettlement{/s}</label>
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
                   
                    <label for="stateTransfer" class="text-left col-md-3 control-label">{s namespace="backend/mopt_config_payone/main" name="forwarding/status/transfer"}Transfer{/s}</label>
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
                   
                    <label for="stateInvoice" class="text-left col-md-3 control-label">{s namespace="backend/mopt_config_payone/main" name="forwarding/status/invoice"}Invoice{/s}</label>
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

                    <label for="stateFailed" class="text-left col-md-3 control-label">{s namespace="backend/mopt_config_payone/main" name="forwarding/status/failed"}Failed{/s}</label>
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
                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}  
    <script type="text/javascript" src="{link file='backend/_resources/js/formhelper.js'}"></script>

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
