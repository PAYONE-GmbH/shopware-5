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
        <h3>{s name=global-form/fieldset1}Allgemein{/s}</h3>
        <div>
            Stellen Sie hier die Konfiguration für Zahlarten ein.
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
            <form role="form" id="ajaxgeneralconfigform" class="form-horizontal">
                <div class="form-group has-feedback has-error menu-level-experte">
                    <img src="{link file="backend/_resources/images/information.png"}" data-toggle="popover" title="PAYONE Hilfe" data-content="ID des zu verwendenden Accounts">
                    <label for="merchantId" class="text-left col-md-3 control-label">{s name=fieldlabel/merchantId}Merchant-ID{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[0-9]*' minlength="1" maxlength="5" id="merchantId" name="merchantId" aria-describedby="merchantId-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="merchantId-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                    <img src="{link file="backend/_resources/images/information.png"}" data-toggle="popover" title="PAYONE Hilfe" data-content="ID des zu verwendenden Zahlungsportal">
                    <label for="portalId" class="text-left col-md-3 control-label">{s name=fieldlabel/portalId}Portal-ID{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[0-9]*' minlength="1" maxlength="7" id="portalId" name="portalId" aria-describedby="portalId-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="portalId-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                    <img src="{link file="backend/_resources/images/information.png"}" data-toggle="popover" title="PAYONE Hilfe" data-content="ID des zu verwendenden SubAccounts">
                    <label for="subaccountId" class="text-left col-md-3 control-label">{s name=fieldlabel/subaccountId}Subaccount-ID{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[0-9]*' minlength="1" maxlength="5" id="subaccountId" name="subaccountId" aria-describedby="subaccountId-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="subaccountId-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>                    
                <div class="form-group has-feedback has-error menu-level-experte">
                    <img src="{link file="backend/_resources/images/information.png"}" data-toggle="popover" title="PAYONE Hilfe" data-content="Schlüssel des zu verwendenden Zahlungsportal">
                    <label for="apiKey" class="text-left col-md-3 control-label">{s name=fieldlabel/apiKey}Schlüssel{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="40" id="apiKey" name="apiKey" aria-describedby="apiKey-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="apiKey-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard menu-level-experte">
                    <img src="{link file="backend/_resources/images/information.png"}" data-toggle="popover" title="PAYONE Hilfe" data-content="Hier wird definiert wie die Zahlart verwendet wird. Live = Zahlungen werden auf der PAYONE-Plattform ausgeführt Test = Zahlungen werden nur auf der PAYONE-Testumgebung simuliert">
                    <label for="liveMode" class="text-left col-md-3 control-label">{s name=fieldlabel/liveMode}Betriebsmodus{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="liveMode" name="liveMode" aria-describedby="liveMode-status" >
                            <option value="true">Live</option>
                            <option value="false">Test</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="liveMode-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file="backend/_resources/images/information.png"}" data-toggle="popover" title="PAYONE Hilfe" data-content="Die Vorautorisation ist die Eröffnung eines Zahlvorgangs auf der PAYONE-Plattform. Wenn die Zahlart es zulässt wird eine Reservierung des Betrages durchgeführt. Bei Zahlarten wie Sofortüberweisung.de wird der Betrag sofort eingezogen weil dort keine Reservierung durchgeführt werden kann. Bei Zahlarten wie z.B. Vorkasse oder Rechnung wird der Zahlvorgang nur auf der PAYONE – Plattform angelegt. Wenn die Autorisation durchgeführt wird, dann wird wenn möglich der Betrag sofort eingezogen">
                    <label for="authorisationMethod" class="text-left col-md-3 control-label">{s name=fieldlabel/authorisationMethod}Autorisierung{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="authorisationMethod" name="authorisationMethod" aria-describedby="authorisationMethod-status" >
                            <option value="Vorautorisierung">Vorautorisierung</option>
                            <option value="Autorisierung">Autorisierung</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="authorisationMethod-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file="backend/_resources/images/information.png"}" data-toggle="popover" title="PAYONE Hilfe" data-content="Soll der Warenkorbinhalt an PAYONE übermittelt werden?">
                    <label for="submitBasket" class="text-left col-md-3 control-label">{s name=fieldlabel/submitBasket}Warenkorbübergabe{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="submitBasket" name="submitBasket" aria-describedby="submitBasket-status" >
                            <option value="false">Nein</option>
                            <option value="true">Ja</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="submitBasket-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file="backend/_resources/images/information.png"}" data-toggle="popover" title="PAYONE Hilfe" data-content="Sobald die AGB einmal bestätigt wurden, wird dies gespeichert und die Checkbox dementsprechend vorausgewählt">
                    <label for="saveTerms" class="text-left col-md-3 control-label">{s name=fieldlabel/saveTerms}Speichern der AGB Bestätigung{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="saveTerms" name="saveTerms" aria-describedby="saveTerms-status" >
                            <option value="0">Aus</option>
                            <option value="1">Auf der Confirm Seite</option>
                            <option value="2">Global</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="saveTerms-status" class="sr-only">(success)</span>
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

        var form = $('#ajaxgeneralconfigform');
        var url = "{url controller=FcPayone action=ajaxgetGeneralConfig forceSecure}";
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

        $(function () {
            $('[data-toggle="popover"]').popover()
        });
    </script>
{/block}