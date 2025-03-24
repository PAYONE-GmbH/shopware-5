{extends file="parent:backend/_base/layout.tpl"}
{namespace name=backend/mopt_config_payone/main}
{block name="content/main"}
    <div class="col-md-12">
        <h3>{s name="global-form/fieldset1"}Allgemein{/s}</h3>
        <div>
            {s name="fieldlabel/configurePaymentsText"}Stellen Sie hier die Konfiguration für Zahlarten ein.{/s}
        </div>
        {include file='backend/fc_payone/include/dropdown_payments.tpl'}
        <div class='col-md-12'>
            <form role="form" id="ajaxgeneralconfigform" class="form-horizontal">
                {include file='backend/fc_payone/include/input_text.tpl' id='merchantId' label="{s name="fieldlabel/merchantId"}Merchant-Id{/s}" pattern="^[0-9]*" minlength="1" maxlength="5" content="{s name="fieldlabelhelp/merchantId"}ID des zu verwendenden Accounts{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='portalId' label="{s name="fieldlabel/portalId"}Portal-Id{/s}" pattern="^[0-9]*" minlength="1" maxlength="5" content="{s name="fieldlabelhelp/portalId"}ID des zu verwendenden Zahlungsportal{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='subaccountId' label="{s name="fieldlabel/subaccountId"}Subaccount-Id{/s}" pattern="^[0-9]*" minlength="1" maxlength="5" content="{s name="fieldlabelhelp/subaccountId"}ID des zu verwendenden SubAccounts{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='apiKey' label="{s name="fieldlabel/apiKey"}Schlüssel{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="100" content="{s name="fieldlabelhelp/apiKey"}Schlüssel des zu verwendenden Zahlungsportal{/s}"}
                {include file='backend/fc_payone/include/dropdown_livetest.tpl' id='liveMode' label="{s name="fieldlabel/liveMode"}Betriebsmodus{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/liveMode"}Hier wird definiert wie die Zahlart verwendet wird. Live = Zahlungen werden auf der PAYONE-Plattform ausgeführt Test = Zahlungen werden nur auf der PAYONE-Testumgebung simuliert{/s}"}
                {include file='backend/fc_payone/include/dropdown_authpreauth.tpl' id='authorisationMethod' label="{s name="fieldlabel/authorisationMethod"}Autorisierung{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/authorisationMethod"}Die Vorautorisation ist die Eröffnung eines Zahlvorgangs auf der PAYONE-Plattform. Wenn die Zahlart es zulässt wird eine Reservierung des Betrages durchgeführt. Bei Zahlarten wie Sofortueberweisung.de wird der Betrag sofort eingezogen weil dort keine Reservierung durchgeführt werden kann. Bei Zahlarten wie z.B. Vorkasse oder Rechnung wird der Zahlvorgang nur auf der PAYONE – Plattform angelegt. Wenn die Autorisation durchgeführt wird, dann wird wenn möglich der Betrag sofort eingezogen{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='submitBasket' label="{s name="fieldlabel/submitBasket"}Warenkorbübergabe{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/submitBasket"}Soll der Warenkorbinhalt an PAYONE übermittelt werden?{/s}"}
                {include file='backend/fc_payone/include/dropdown_saveterms.tpl' id='saveTerms' label="{s name="fieldlabel/saveTerms"}Speichern der AGB Bestätigung{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/saveTerms"}Sobald die AGB einmal bestätigt wurden, wird dies gespeichert und die Checkbox dementsprechend vorausgewählt{/s}"}
                {include file='backend/fc_payone/include/input_checkbox.tpl' id='sendOrdernumberAsReference' label="{s name="fieldlabel/sendOrdernumberAsReference"}Benutze Shopware-Bestellnummer{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/sendOrdernumberAsReference"}Sendet die Shopware Bestellnummer anstatt einen Zufallswert an Payone{/s}"}
                {include file='backend/fc_payone/include/input_checkbox.tpl' id='changeOrderOnTXS' label="{s name="fieldlabel/changeOrderOnTXS"}Bestellung geändert bei TX Status{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/changeOrderOnTXS"}Setze das changed Datum einer Bestellung, wenn ein Transaktions-Status erfolgreich war. Greift erst ab Shopware Version 5.5.0{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='ratepaySnippetId' label="{s name="fieldlabel/ratepaySnippetId"}Ratepay Snippet Id{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="100" content="{s name="fieldlabelhelp/ratepaySnippetId"}Ratepay Snippet Id{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='applepayMerchantId' label="{s name="fieldlabel/applepayMerchantID"}Apple Pay MerchantId{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="100" content="{s name='fieldlabelhelp/applepayMerchantID'}Ihre Apple Pay MerchantId{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='applepayVisa' label="{s name="fieldlabel/applepayVisa"}Apple Pay Visa erlauben{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/applepayVisa"}Erlaubt Visa Karten über Apple Pay{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='applepayMastercard' label="{s name="fieldlabel/applepayMastercard"}Apple Pay Visa erlauben{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/applepayMastercard"}Erlaubt Mastercard Karten über Apple Pay{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='applepayGirocard' label="{s name="fieldlabel/applepayGirocard"}Apple Pay Girocard erlauben{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/applepayGirocard"}Erlaubt Giropay Karten über Apple Pay{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='applepayCertificate' label="{s name="fieldlabel/applepayCertificate"}Apple Pay Zertifikat{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/applepayCertificate"}Apple Pay Pfad zur Zertifikats Datei{/s}"}
                <input type="file" accept=".pem" id="applepayCertificateFile" name="applepayCertificateFile" />
                <button type="button" class="btn-payone btn" id="applecertupload" >{s name="fieldlabel/uploadbutton"}Hochladen{/s}</button>

                {include file='backend/fc_payone/include/input_text.tpl' id='applepayPrivateKey' label="{s name="fieldlabel/applepayPrivateKey"}Apple Pay Private Key{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/applepayPrivateKey"}Absoluter Pfad zum Private Key{/s}"}
                <input type="file" accept=".key" id="applepayKeyFile" name="applepayKeyFile" />
                <button type="button" class="btn-payone btn" id="applekeyupload" >{s name="fieldlabel/uploadbutton"}Hochladen{/s}</button>

                {include file='backend/fc_payone/include/input_text.tpl' id='applepayPrivateKeyPassword' label="{s name="fieldlabel/applepayPrivateKeyPassword"}Apple Pay Private Key Passwort{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/applepayPrivateKeyPassword"}Kann auch dann gesetzt werden, wenn der Key unverschlüsselt ist{/s}"}
                {include file='backend/fc_payone/include/input_checkbox.tpl' id='applepayDebug' label="{s name="fieldlabel/applepayDebug"}Apple Pay Debug{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/applepayDebug"}Zeigt im Frontend die Debugging Informationen an{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='allowDifferentAddresses' label="{s name="fieldlabel/allowDifferentAdresses"}Abweichende Lieferadressen zulassen{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/allowDifferentAdresses"}Hinweis: Muss im PAYONE-Konto freigeschaltet sein!{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='creditcardDefaultDescription' label="{s name='fieldlabel/creditcardDefaultDescription'}Kreditkarte Zusätzliche Beschreibung{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name='fieldlabelhelp/creditcardDefaultDescription'}Zusätzliche Beschreibung der Zahlart bei Gruppierung{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='paypalExpressUseDefaultShipping' label="{s name="fieldlabel/paypalExpressUseDefaultShipping"}Vorläufige Versandkosten bei Paypal Express übergeben{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/paypalExpressUseDefaultShipping"}Wenn aktiviert, werden die vorläufigen Versandkosten mit an Paypal Express übergeben{/s}"}
                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}
    <script type="text/javascript">

        var form = $('#ajaxgeneralconfigform');
        var url = "{url controller=FcPayone action=ajaxgetGeneralConfig forceSecure}";
        var paymentid = 0;

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
                        $('#allowDifferentAddresses').prop( "disabled", true);
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
            paymentid = this.id;
            var filterid = this.getAttribute('id');
            var paymentname = this.getAttribute('data-name');

            $.ajax({
                url: call,
                type: 'POST',
                success: function (data) {
                    response = $.parseJSON(data);
                    if (response.status === 'success') {
                        $('#sendOrdernumberAsReference').prop( "disabled", filterid !== '0');
                        $('#allowDifferentAddresses').prop( "disabled", filterid === '0');
                        if(/mopt_payone__fin_payone_secured/.test(paymentname)){
                            $('#allowDifferentAddresses').prop( "disabled", false);
                        } else {
                            $('#allowDifferentAddresses').prop( "disabled", true);
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
                showalert("Die Daten wurden gespeichert", "alert-success");
                
            });
        });

        $("#applecertupload").click(function(){
            var fd = new FormData();
            var files = $('#applepayCertificateFile')[0].files;

            // Check file selected or not
            if(files.length > 0 ){
                fd.append('file',files[0]);

                $.ajax({
                    url: 'ajaxsaveApplepayCert',
                    type: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(response){
                        if(response != 0){
                            console.log(response);
                            $('#applepayCertificate').val(response);
                            form.submit();
                            showalert("Das Zertifikat wurde gespeichert", "alert-success");
                        }else{
                            showalert("Fehler beim Speichern des Zertifikats", "alert-error");
                        }
                    },
                });
            }else{
                showalert("Bitte eine Zertifikats Datei .pem auswählen", "alert-error");
            }
        });

        $("#applekeyupload").click(function(){
            var fd = new FormData();
            var files = $('#applepayKeyFile')[0].files;

            // Check file selected or not
            if(files.length > 0 ){
                fd.append('file',files[0]);

                $.ajax({
                    url: 'ajaxsaveApplepayKey',
                    type: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(response){
                        if(response != 0){
                            console.log(response);
                            $('#applepayPrivateKey').val(response);
                            form.submit();
                            showalert("Die Schlüsseldatei wurde gespeichert", "alert-success");
                        }else{
                            showalert("Fehler beim Speichern des Schlüssels", "alert-error");
                        }
                    },
                });
            }else{
                showalert("Bitte eine Key Datei .key auswählen", "alert-error");
            }
        });
    </script>
{/block}
