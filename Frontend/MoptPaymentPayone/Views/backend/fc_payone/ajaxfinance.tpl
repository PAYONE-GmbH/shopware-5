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
        <h3>{s name="global-form/fieldset2"}Einstellungen für Finanzierungs-basierte Zahlarten{/s}</h3>
        <div>
            Stellen Sie hier die Konfiguration zu den Zahlarten Klarna, Unzer und Ratepay ein.
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="btn-group">
                    <button id="paymentmethodsdropdown" type="button" class="btn-payone-fixed btn-payone btn dropdown-toggle" data-toggle="dropdown">
                        <span class="selection">{s name="paymentMethod/label"}Gilt für Zahlart:{/s}</span><span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        {foreach from=$payonepaymentmethods item=paymentmethod}
                            <li><a href="#" data-name="{$paymentmethod.name}" id="{$paymentmethod.id}">{$paymentmethod.description}</a></li>
                            {/foreach}   
                    </ul>
                </div>
            </div>
        </div>
        <div class='col-md-12'>
            <form role="form" id="ajaxfinanceform" class="form-horizontal">
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">

                    <label for="description" class="text-left col-md-3 control-label">{s name="formpanel_description_label"}Bezeichnung{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .\(\)\+\-?,:;"!@#$%!^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="description" name="description" aria-describedby="description-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="description-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">

                    <label for="additionalDescription" class="text-left col-md-3 control-label">{s name="formpanel_additional-description_label"}Zusätzliche Beschreibung{/s}</label>
                    <div class="col-md-6">
                        <textarea type="text" class="form-control " pattern='^[_ .\(\)\+\-?,:;"!@#$%!^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="additionalDescription" name="additionalDescription" aria-describedby="additionalDescription-status" >
                        </textarea>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="additionalDescription-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>  
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">

                    <label for="debitPercent" class="text-left col-md-3 control-label">{s name="formpanel_surcharge_label"}Aufschlag/Abschlag (in %){/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[,.0-9]*' minlength="0" maxlength="200" id="debitPercent" name="debitPercent" aria-describedby="debitPercent-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="debitPercent-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">

                    <label for="surcharge" class="text-left col-md-3 control-label">{s name="formpanel_generalSurcharge_label"}Pauschaler Aufschlag{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[,.0-9]*' minlength="1" maxlength="200" id="surcharge" name="surcharge" aria-describedby="surcharge-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="surcharge-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">

                    <label for="position" class="text-left col-md-3 control-label">{s name="formpanel_position_surcharge"}Position{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[0-9]*' minlength="1" maxlength="200" id="position" name="position" aria-describedby="position-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="position-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">

                    <label for="active" class="text-left col-md-3 control-label">{s name="formpanel_active_label"}Aktiv{/s}</label>
                    <div class="col-md-6">
                        <input type="checkbox" class="form-control " pattern='^[_ .\(\)\+\-?,:;"!@#$%!^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="active" name="active" aria-describedby="active-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="active-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">

                    <label for="esdActive" class="text-left col-md-3 control-label">{s name="formpanel_esdActive_label"}Aktiv für ESD-Produkte{/s}</label>
                    <div class="col-md-6">
                        <input type="checkbox" class="form-control " pattern='^[_ .\(\)\+\-?,:;"!@#$%!^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="esdActive" name="esdActive" aria-describedby="esdActive-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="esdActive-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                    <label for="mobileInactive" class="text-left col-md-3 control-label">{s name="formpanel_mobileInactive_label"}Inaktiv für Smartphone{/s}</label>
                    <div class="col-md-6">
                        <input type="checkbox" class="form-control " pattern='^[_ .\(\)\+\-?,:;"!@#$%!^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="mobileInactive" name="mobileInactive" aria-describedby="mobileInactive-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="mobileInactive-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>     
                <div id="klarnastoreid" class="form-group has-feedback has-error  menu-level-standard  menu-level-experte" >
                    <label for="klarnaStoreId" class="text-left col-md-3 control-label">{s name="fieldlabel/klarnaStoreId"}Klarna Store-ID{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .\(\)\+\-?,:;"!@#$%!^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="klarnaStoreId" name="klarnaStoreId" aria-describedby="klarnaStoreId-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="klarnaStoreId-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div id="payolutionCompanyName" class="form-group has-feedback has-error  menu-level-standard  menu-level-experte" >
                    <label for="payolutionCompanyName" class="text-left col-md-3 control-label">{s name="fieldlabel/payolutionCompanyName"}Unzer Firmenname{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .\(\)\+\-?,:;"!@#$%!^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="payolutionCompanyName" name="payolutionCompanyName" aria-describedby="payolutionCompanyName-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="payolutionCompanyName-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div> 
                <div id="payolutionB2bmode" class="form-group has-feedback has-error  menu-level-standard  menu-level-experte" >
                    <label for="payolutionB2bmode" class="text-left col-md-3 control-label">{s name="fieldlabel/payolutionB2bmode"}Unzer B2B Mode {/s}</label>
                    <div class="col-md-6">
                        <input type="checkbox" class="form-control " pattern='^[_ .\(\)\+\-?,:;"!@#$%!^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="payolutionB2bmode" name="payolutionB2bmode" aria-describedby="payolutionB2bmode-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="payolutionB2bmode-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>   
                <div id="payolutionDraftUser" class="form-group has-feedback has-error  menu-level-standard  menu-level-experte" >
                    <label for="payolutionDraftUser" class="text-left col-md-3 control-label">{s name="fieldlabel/payolutionDraftUser"}Unzer HTTP-Benutzername{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .\(\)\+\-?,:;"!@#$%!^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="payolutionDraftUser" name="payolutionDraftUser" aria-describedby="payolutionDraftUser-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="payolutionDraftUser-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>    
                <div id="payolutionDraftPassword" class="form-group has-feedback has-error  menu-level-standard  menu-level-experte" >
                    <label for="payolutionDraftPassword" class="text-left col-md-3 control-label">{s name="fieldlabel/payolutionDraftPassword"}Unzer HTTP-Passwort{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .\(\)\+\-?,:;"!@#$%!^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="payolutionDraftPassword" name="payolutionDraftPassword" aria-describedby="payolutionDraftPassword-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="payolutionDraftPassword-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>                      
                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>

        <a style="font-size: 28px" href="#" data-target="#ratepayconfigs">Konfiguration Ratepay</a>
        <div id="ratepayconfigs" class="form-group" >
            <form role="form" id="ajaxratepay" enctype="multipart/form-data">
                <table class="table-condensed" id="ratepaytable"> 
                    <tr><th>id</th><th>Shopid</th><th>Währung</th><th>Ratenkauf Modus</th><th>Land</th></tr>
                    {foreach from=$ratepayconfigs key=mykey item=ratepayconfig}
                        <tr id="row{$ratepayconfig->getId()}">
                            <td><input name="row[{$ratepayconfig->getId()}][id]" id="id_{$ratepayconfig->getId()}" type="text" style="max-width:125px;" class="form-control" value="{$ratepayconfig->getId()}" readonly="readonly" ></td>                        
                            <td><input name="row[{$ratepayconfig->getId()}][shopid]" id="shopid_{$ratepayconfig->getId()}" type="text" style="max-width:125px;" class="form-control" value="{$ratepayconfig->getShopid()}"></td>
                            <td><select class="form-control" name="row[{$ratepayconfig->getId()}][currency]" id="currency_{$ratepayconfig->getId()}">
                                    {foreach from=$currencies item=currency}
                                        <option value="{$currency->getId()}" {if $currency->getId() == $ratepayconfig->getCurrency()->getId()}selected="selected"{/if}>{$currency->getName()}</option>
                                    {/foreach}
                                </select>
                            </td>
                            <td><select class="form-control" name="row[{$ratepayconfig->getId()}][ratepayInstallmentMode]" id="ratepayInstallmentMode_{$ratepayconfig->getId()}">
                                    <option value="0" {if $ratepayconfig->getRatepayInstallmentMode() == 0}selected="selected"{/if}>Vorkasse</option>
                                    <option value="1" {if $ratepayconfig->getRatepayInstallmentMode() == 1}selected="selected"{/if}>Lastschrift</option>
                                </select>
                            </td>
                            <td><input name="row[{$ratepayconfig->getId()}][countryCodeBilling]" id="id_{$ratepayconfig->getId()}" type="text" style="max-width:125px;" class="form-control" value="{$ratepayconfig->getCountryCodeBilling()}" readonly="readonly" ></td>                            
                            <td role="button" name="delete" value="delete" onclick="removeRow({$ratepayconfig->getId()})"><img id="delete_{$ratepayconfig->getId()}" height="100%" src="{link file='backend/_resources/images/delete.png'}"></td>
                    {/foreach}
                    <tr><td><img id="newRow" onclick="addRow()" src="{link file='backend/_resources/images/add.png'}"></td></tr>
                </table>

                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
                <button type="submit" name ="downloadbtn" class="btn-payone btn " >Ratepay Konfiguration abrufen</button>
            </form>                
        </div>                
    </div>
{/block}

{block name="resources/javascript" append}  
    <script type="text/javascript" src="{link file="backend/_resources/js/formhelper.js"}"></script>

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
                        form.validator('validate');
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
                        form.validator('validate');form.validator('validate');
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
                        form.validator('validate');form.validator('validate');
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
