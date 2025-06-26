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
        <h3>Verbindungseinstellungen</h3>
        <div>
            Stellen Sie hier Ihre Verbindungsdaten zur PAYONE Plattform ein. <BR>
            Diese finden Sie im Backend von PAYONE unter <a href=http://pmi.pay1.de>pmi.pay1.de</a><BR>
            <BR>
        </div>
        <div class='col-md-12'>
            <form role="form" id="ajaxconfigform" class="form-horizontal">
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="ID des zu verwendenden Accounts">
                    <label for="merchantId" class="text-left col-md-3 control-label">{s name="fieldlabel/merchantId"}Merchant-ID{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[0-9]*' minlength="1" maxlength="5" id="merchantId" name="merchantId" aria-describedby="merchantId-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="merchantId-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="ID des zu verwendenden Zahlungsportal">
                    <label for="portalId" class="text-left col-md-3 control-label">{s name="fieldlabel/portalId"}Portal-ID{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[0-9]*' minlength="1" maxlength="7" id="portalId" name="portalId" aria-describedby="portalId-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="portalId-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="ID des zu verwendenden SubAccounts">
                    <label for="subaccountId" class="text-left col-md-3 control-label">{s name="fieldlabel/subaccountId"}Subaccount-ID{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[0-9]*' minlength="1" maxlength="5" id="subaccountId" name="subaccountId" aria-describedby="subaccountId-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="subaccountId-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>                    
                <div class="form-group has-feedback has-error ">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="Schlüssel des zu verwendenden Zahlungsportal">
                    <label for="apiKey" class="text-left col-md-3 control-label">{s name="fieldlabel/apiKey"}Schlüssel{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="40" id="apiKey" name="apiKey" aria-describedby="apiKey-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="apiKey-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
        <div class='col-md-8'>
        </div>
        <div class='col-md-12'>
            <h3>Verbindungstest</h3>
            Hier können Sie einen Verbindungstest zur PAYONE Plattform mit den oben angegebenen Daten starten <BR>
            Bitte prüfen Sie das Ergebnis und eventuell vorhandene Hinweise im unten sichtbaren Protokollfenster <BR>
            <BR>
            <button id="startTest" type="button" class="btn-payone btn" >Verbindungstest starten</button>
            <BR>
            <div id="data" style="font-size: 12px; color: #FFFFFF; background-color: #000000; margin-top: 15px; width: 640px; height: 300px;"></div>
        </div>
    </div>

{/block}

{block name="resources/javascript" append}  
    <script type="text/javascript" src="{link file='backend/_resources/js/formhelper.js'}"></script>

    <script type="text/javascript">

        var form = $('#ajaxconfigform');
        var url = "{url controller=FcPayone action=ajaxgetGeneralConfig forceSecure}";
        var testurl = "{url controller=FcPayone action=connectiontest forceSecure}";
        var statusurl = "{url controller=FcPayone action=ajaxgetTestResults forceSecure}";
        var paymentid = 0;

        function getLog() {
            $.ajax({
                url: statusurl,
                dataType: 'text',
                success: function (text) {
                    $("#data").html(text);
                    setTimeout(getLog, 3000); // refresh every 3 seconds
                }
            })
        }

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
                        //ToDo
                    }
                }
            });
        });

        $(".dropdown-menu li a").click(function () {
            var params = "paymentid=0";
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
                           // ToDo
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

        $("#startTest").on('click', function () {
                var mid = encodeURIComponent(document.getElementById('merchantId').value);
                var aid = encodeURIComponent(document.getElementById('subaccountId').value);
                var pid = encodeURIComponent(document.getElementById('portalId').value);
                var apikey = encodeURIComponent(document.getElementById('apiKey').value);
                var myurl = testurl + '?mid=' + mid + '&aid=' + aid + '&pid=' + pid + '&apikey=' + apikey;
                alert(myurl)
                $.ajax({
                    url: myurl,
                    type: 'get', 
                    dataType: 'json', 

                });
                getLog();
        });

        $(function () {
            $('[data-toggle="popover"]').popover()
        });        
    </script>
{/block}
