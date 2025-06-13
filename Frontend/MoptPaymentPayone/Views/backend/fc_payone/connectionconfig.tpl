{extends file="parent:backend/_base/layout.tpl"}
{namespace name=backend/mopt_config_payone/main}
{block name="content/main"}
    <div class="col-md-12">
        <h3>{s name="fieldlabel/connectionSettings"}Verbindungseinstellungen{/s}</h3>
        <div>
            {s name="fieldlabel/connectionText"}
                Stellen Sie hier Ihre Verbindungsdaten zur PAYONE Plattform ein.
                <BR>
                Diese finden Sie im Backend von PAYONE unter
                <a href=https://pmi.pay1.de>pmi.pay1.de</a>
            {/s}
        </div>
        <div class='col-md-12'>
            <form id="connectionconfigform" class="form-horizontal needs-validation" novalidate>
                {include file='backend/fc_payone/include/input_text.tpl' id='merchantId' label="{s name="fieldlabel/merchantId"}Merchant-Id{/s}" pattern="^[0-9]*" minlength="1" maxlength="5" content="{s name="fieldlabelhelp/merchantId"}ID des zu verwendenden Accounts{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='portalId' label="{s name="fieldlabel/portalId"}Portal-Id{/s}" pattern="^[0-9]*" minlength="1" maxlength="7" content="{s name="fieldlabelhelp/portalId"}ID des zu verwendenden Zahlungsportal{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='subaccountId' label="{s name="fieldlabel/subaccountId"}Subaccount-Id{/s}" pattern="^[0-9]*" minlength="1" maxlength="5" content="{s name="fieldlabelhelp/subaccountId"}ID des zu verwendenden SubAccounts{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='apiKey' label="{s name="fieldlabel/apiKey"}Schlüssel{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9`´€]*' minlength="1" maxlength="100" content="{s name="fieldlabelhelp/apiKey"}Schlüssel des zu verwendenden Zahlungsportal{/s}"}
                <button type="submit" class="btn-payone btn ">{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
        <div class='col-md-12'>
            <h3>{s name="fieldlabel/connectionTest"}Verbindungstest{/s}</h3>
            {s name="fieldlabel/connectionTestText"}
                Hier können Sie einen Verbindungstest zur PAYONE Plattform mit den oben angegebenen Daten starten
                <BR>
                Bitte prüfen Sie das Ergebnis und eventuell vorhandene Hinweise im unten sichtbaren Protokollfenster
                <BR>
                <BR>
            {/s}
            <button id="startTest" type="button"
                    class="btn-payone btn">{s name="fieldlabel/connectionStart"}Verbindungstest starten{/s}</button>
            <BR>
            <div id="data"
                 style="font-size: 12px; color: #FFFFFF; background-color: #000000; margin-top: 15px; width: 640px; height: 300px;"></div>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}
    <script type="text/javascript">
        {include file='backend/fc_payone/include/javascript.tpl.js' form="#connectionconfigform" loadAction="generalconfigData" saveAction="ajaxSavePayoneConfig"}

        var testurl = "{url controller=FcPayone action=connectiontest forceSecure}";
        var statusurl = "{url controller=FcPayone action=connectiontestData forceSecure}";

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

        $("#startTest").on('click', function () {
            var mid = encodeURIComponent(document.getElementById('merchantId').value);
            var aid = encodeURIComponent(document.getElementById('subaccountId').value);
            var pid = encodeURIComponent(document.getElementById('portalId').value);
            var apikey = encodeURIComponent(document.getElementById('apiKey').value);
            var myurl = testurl + '?mid=' + mid + '&aid=' + aid + '&pid=' + pid + '&apikey=' + apikey;
            $.ajax({
                url: myurl,
                type: 'get',
                dataType: 'json',

            });
            getLog();
        });
    </script>
{/block}
