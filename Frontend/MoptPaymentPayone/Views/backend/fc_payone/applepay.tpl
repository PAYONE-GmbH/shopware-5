{extends file="parent:backend/_base/layout.tpl"}

{block name="content/main"}
    {namespace name=backend/mopt_config_payone/main}
    <div class="col-md-12">
        <h3>{s name="global-form/applepay"}Konfiguration Apple Pay Logos{/s}</h3>
        <div>
            {s name="global-form/applepayDesc"}Stellen Sie hier die Konfiguration zur Zahlart Apple Pay ein.{/s}
        </div>
        <div class='col-md-12'>
            <form role="form" id="applepayform" class="form-horizontal">
                {include file='backend/fc_payone/include/dropdown_payments.tpl'}
                {include file='backend/fc_payone/include/input_text.tpl' id='applepayMerchantId' label="{s name="fieldlabel/applepayMerchantID"}Apple Pay MerchantId{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="100" content="{s name='fieldlabelhelp/applepayMerchantID'}Ihre Apple Pay MerchantId{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='applepayVisa' label="{s name="fieldlabel/applepayVisa"}Apple Pay Visa erlauben{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/applepayVisa"}Erlaubt Visa Karten über Apple Pay{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='applepayMastercard' label="{s name="fieldlabel/applepayMastercard"}Apple Pay Visa erlauben{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/applepayMastercard"}Erlaubt Mastercard Karten über Apple Pay{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='applepayGirocard' label="{s name="fieldlabel/applepayGirocard"}Apple Pay Girocard erlauben{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/applepayGirocard"}Erlaubt Giropay Karten über Apple Pay{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='applepayCertificate' label="{s name="fieldlabel/applepayCertificate"}Apple Pay Zertifikat{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/applepayCertificate"}Apple Pay Pfad zur Zertifikats Datei{/s}"}
                <input type="file" accept=".pem" id="applepayCertificateFile" name="applepayCertificateFile"/>
                <button type="button" class="btn-payone btn"
                        id="applecertupload">{s name="fieldlabel/uploadbutton"}Hochladen{/s}</button>
                {include file='backend/fc_payone/include/input_text.tpl' id='applepayPrivateKey' label="{s name="fieldlabel/applepayPrivateKey"}Apple Pay Private Key{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/applepayPrivateKey"}Absoluter Pfad zum Private Key{/s}"}
                <input type="file" accept=".key" id="applepayKeyFile" name="applepayKeyFile"/>
                <button type="button" class="btn-payone btn"
                        id="applekeyupload">{s name="fieldlabel/uploadbutton"}Hochladen{/s}</button>

                {include file='backend/fc_payone/include/input_text.tpl' id='applepayPrivateKeyPassword' label="{s name="fieldlabel/applepayPrivateKeyPassword"}Apple Pay Private Key Passwort{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/applepayPrivateKeyPassword"}Kann auch dann gesetzt werden, wenn der Key unverschlüsselt ist{/s}"}
                {include file='backend/fc_payone/include/input_checkbox.tpl' id='applepayDebug' label="{s name="fieldlabel/applepayDebug"}Apple Pay Debug{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/applepayDebug"}Zeigt im Frontend die Debugging Informationen an{/s}"}
                <button type="submit" class="btn-payone btn ">{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}
<script type="text/javascript">
    {include file='backend/fc_payone/include/javascript.tpl.js' form="#applepayform" loadAction="generalconfigdata" saveAction="ajaxSavePayoneConfig"}
</script>
{/block}
