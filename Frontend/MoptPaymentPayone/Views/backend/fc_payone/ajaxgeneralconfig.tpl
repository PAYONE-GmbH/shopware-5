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
        <h3>{s name="global-form/fieldset1"}Allgemein{/s}</h3>
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
                            <li><a href="#" data-name="{$paymentmethod.name}" id="{$paymentmethod.id}">{$paymentmethod.description}</a></li>
                            {/foreach}   
                    </ul>
                </div>
            </div>
        </div>
        <div class='col-md-12'>
            <form role="form" id="ajaxgeneralconfigform" class="form-horizontal">
                <div class="form-group has-feedback has-error menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="ID des zu verwendenden Accounts">
                    <label for="merchantId" class="text-left col-md-3 control-label">{s name="fieldlabel/merchantId"}Merchant-ID{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[0-9]*' minlength="1" maxlength="5" id="merchantId" name="merchantId" aria-describedby="merchantId-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="merchantId-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="ID des zu verwendenden Zahlungsportal">
                    <label for="portalId" class="text-left col-md-3 control-label">{s name="fieldlabel/portalId"}Portal-ID{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[0-9]*' minlength="1" maxlength="7" id="portalId" name="portalId" aria-describedby="portalId-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="portalId-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="ID des zu verwendenden SubAccounts">
                    <label for="subaccountId" class="text-left col-md-3 control-label">{s name="fieldlabel/subaccountId"}Subaccount-ID{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[0-9]*' minlength="1" maxlength="5" id="subaccountId" name="subaccountId" aria-describedby="subaccountId-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="subaccountId-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>                    
                <div class="form-group has-feedback has-error menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="Schlüssel des zu verwendenden Zahlungsportal">
                    <label for="apiKey" class="text-left col-md-3 control-label">{s name="fieldlabel/apiKey"}Schlüssel{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="40" id="apiKey" name="apiKey" aria-describedby="apiKey-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="apiKey-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="Hier wird definiert wie die Zahlart verwendet wird. Live = Zahlungen werden auf der PAYONE-Plattform ausgeführt Test = Zahlungen werden nur auf der PAYONE-Testumgebung simuliert">
                    <label for="liveMode" class="text-left col-md-3 control-label">{s name="fieldlabel/liveMode"}Betriebsmodus{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="liveMode" name="liveMode" aria-describedby="liveMode-status" >
                            <option value="true">Live</option>
                            <option value="false">Test</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="liveMode-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="Die Vorautorisation ist die Eröffnung eines Zahlvorgangs auf der PAYONE-Plattform. Wenn die Zahlart es zulässt wird eine Reservierung des Betrages durchgeführt. Bei Zahlarten wie Sofortueberweisung.de wird der Betrag sofort eingezogen weil dort keine Reservierung durchgeführt werden kann. Bei Zahlarten wie z.B. Vorkasse oder Rechnung wird der Zahlvorgang nur auf der PAYONE – Plattform angelegt. Wenn die Autorisation durchgeführt wird, dann wird wenn möglich der Betrag sofort eingezogen">
                    <label for="authorisationMethod" class="text-left col-md-3 control-label">{s name="fieldlabel/authorisationMethod"}Autorisierung{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="authorisationMethod" name="authorisationMethod" aria-describedby="authorisationMethod-status" >
                            <option value="Vorautorisierung">Vorautorisierung</option>
                            <option value="Autorisierung">Autorisierung</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="authorisationMethod-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="Soll der Warenkorbinhalt an PAYONE übermittelt werden?">
                    <label for="submitBasket" class="text-left col-md-3 control-label">{s name="fieldlabel/submitBasket"}Warenkorbübergabe{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="submitBasket" name="submitBasket" aria-describedby="submitBasket-status" >
                            <option value="false">Nein</option>
                            <option value="true">Ja</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="submitBasket-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="Sobald die AGB einmal bestätigt wurden, wird dies gespeichert und die Checkbox dementsprechend vorausgewählt">
                    <label for="saveTerms" class="text-left col-md-3 control-label">{s name="fieldlabel/saveTerms"}Speichern der AGB Bestätigung{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="saveTerms" name="saveTerms" aria-describedby="saveTerms-status" >
                            <option value="0">Aus</option>
                            <option value="1">Auf der Confirm Seite</option>
                            <option value="2">Global</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="saveTerms-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-standard menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="Sendet die Shopware Bestellnummer anstatt einen Zufallswert an Payone">
                    <label for="sendOrdernumberAsReference" class="text-left col-md-3 control-label">{s name="fieldlabel/sendOrdernumberAsReference"}Benutze Shopware-Bestellnummer{/s}</label>
                    <div class="col-md-6">
                        <input type="checkbox" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="sendOrdernumberAsReference" name="sendOrdernumberAsReference" aria-describedby="sendOrdernumberAsReference-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="sendOrdernumberAsReference-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-standard menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="Setze das changed Datum einer Bestellung, wenn ein Transaktions-Status erfolgreich war. Greift erst ab Shopware Version 5.5.0">
                    <label for="changeOrderOnTXS" class="text-left col-md-3 control-label">{s name="fieldlabel/changeOrderOnTXS"}Bestellung geändert bei TX Status{/s}</label>
                    <div class="col-md-6">
                        <input type="checkbox" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="changeOrderOnTXS" name="changeOrderOnTXS" aria-describedby="changeOrderOnTXS-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="changeOrderOnTXS-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="Ratepay Snippet Id">
                    <label for="ratepaySnippetId" class="text-left col-md-3 control-label">{s name="fieldlabel/ratepaySnippetId"}Ratepay Snippet Id{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="40" id="ratepaySnippetId" name="ratepaySnippetId" aria-describedby="ratepaySnippetId-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="ratepaySnippetId-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte" >
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name='fieldlabelhelp/applepayMerchantID'}Ihre Apple Pay MerchantId{/s}">
                    <label for="applepayMerchantId" class="text-left col-md-3 control-label">{s name="fieldlabel/applepayMerchantID"}Apple Pay MerchantId{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="applepayMerchantId" name="applepayMerchantId" aria-describedby="applepayMerchantId-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="applepayMerchantId-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name="fieldlabelhelp/applepayVisa"}Erlaubt Visa Karten über Apple Pay{/s}">
                    <label for="applepayVisa" class="text-left col-md-3 control-label">{s name="fieldlabel/applepayVisa"}Apple Pay Visa erlauben{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="applepayVisa" name="applepayVisa" aria-describedby="applepayVisa-status" >
                            <option value="false">Nein</option>
                            <option value="true">Ja</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="applepayVisa-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name="fieldlabelhelp/applepayMastercard"}Erlaubt Mastercard Karten über Apple Pay{/s}">
                    <label for="applepayMastercard" class="text-left col-md-3 control-label">{s name="fieldlabel/applepayMastercard"}Apple Pay Mastercard erlauben{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="applepayMastercard" name="applepayMastercard" aria-describedby="applepayMastercard-status" >
                            <option value="false">Nein</option>
                            <option value="true">Ja</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="applepayMastercard-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name="fieldlabelhelp/applepayGirocard"}Erlaubt Girocard Karten über Apple Pay{/s}">
                    <label for="applepayGirocard" class="text-left col-md-3 control-label">{s name="fieldlabel/applepayGirocard"}Apple Pay Girocard erlauben{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="applepayGirocard" name="applepayGirocard" aria-describedby="applepayGirocard-status" >
                            <option value="false">Nein</option>
                            <option value="true">Ja</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="applepayGirocard-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>

                <!--
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name="fieldlabelhelp/applepayAmex"}Erlaubt American Express Karten über Apple Pay{/s}">
                    <label for="applepayAmex" class="text-left col-md-3 control-label">{s name="fieldlabel/applepayAmex"}Apple Pay American Express erlauben{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="applepayAmex" name="applepayAmex" aria-describedby="applepayAmex-status" >
                            <option value="false">Nein</option>
                            <option value="true">Ja</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="applepayAmex-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name="fieldlabelhelp/applepayDiscover"}Erlaubt Discover Karten über Apple Pay{/s}">
                    <label for="applepayDiscover" class="text-left col-md-3 control-label">{s name="fieldlabel/applepayDisover"}Apple Pay Discover erlauben{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="applepayDiscover" name="applepayDiscover" aria-describedby="applepayDiscover-status" >
                            <option value="false">Nein</option>
                            <option value="true">Ja</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="applepayDiscover-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                -->
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte" >
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name="fieldlabelhelp/applepayCertificate"}Apple Pay Pfad zur Zertifikats Datei{/s}">
                    <label for="applepayCertificate" class="text-left col-md-3 control-label">{s name="fieldlabel/applepayCertificate"}Apple Pay Zertifikat{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="applepayCertificate" name="applepayCertificate" aria-describedby="applepayCertificate-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="applepayCertificate-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>

                <input type="file" accept=".pem" id="applepayCertificateFile" name="applepayCertificateFile" />
                <button type="button" class="btn-payone btn" id="applecertupload" >{s name="fieldlabel/uploadbutton"}Hochladen{/s}</button>

                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte" >
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name="fieldlabelhelp/applepayPrivateKey"}Absoluter Pfad zum Private Key{/s}">
                    <label for="applepayPrivateKey" class="text-left col-md-3 control-label">{s name="fieldlabel/applepayPrivateKey"}Apple Pay Private Key{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="applepayPrivateKey" name="applepayPrivateKey" aria-describedby="applepayPrivateKey-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="applepayPrivateKey-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>

                <input type="file" accept=".key" id="applepayKeyFile" name="applepayKeyFile" />
                <button type="button" class="btn-payone btn" id="applekeyupload" >{s name="fieldlabel/uploadbutton"}Hochladen{/s}</button>

                <div id="applepayPrivateKeyPassword" class="form-group has-feedback has-error  menu-level-standard  menu-level-experte" >
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name="fieldlabelhelp/applepayPrivateKeyPassword"}Kann auch dann gesetzt werden, wenn der Key unverschlüsselt ist{/s}">
                    <label for="applepayPrivateKeyPassword" class="text-left col-md-3 control-label">{s name="fieldlabel/applepayPrivateKeyPassword"}Apple Pay Private Key Passwort{/s}</label>
                    <div class="col-md-6">
                        <input type="password" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="applepayPrivateKeyPassword" name="applepayPrivateKeyPassword" aria-describedby="applepayPrivateKeyPassword-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="applepayPrivateKeyPassword-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name="fieldlabelhelp/applepayDebug"}Zeigt im Frontend die Debugging Informationen an{/s}">
                    <label for="applepayDebug" class="text-left col-md-3 control-label">{s name="fieldlabel/applepayDebug"}Apple Pay Debug{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="applepayDebug" name="applepayDebug" aria-describedby="applepayDebug-status" >
                            <option value="false">Nein</option>
                            <option value="true">Ja</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="applepayDebug-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name="fieldlabelhelp/allowDifferentAddresses"}Hinweis: Muss im PAYONE-Konto freigeschaltet sein!{/s}">
                    <label for="allowDifferentAddresses" class="text-left col-md-3 control-label">{s name="fieldlabel/allowDifferentAddresses"}Abweichende Lieferadressen zulassen{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="allowDifferentAddresses" name="allowDifferentAddresses" aria-describedby="allowDifferentAddresses-status" >
                            <option value="false">Nein</option>
                            <option value="true">Ja</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="allowDifferentAddresses-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte" >
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name='fieldlabelhelp/creditcardDefaultDescription'}Zusätzliche Beschreibung der Zahlart bei Gruppierung{/s}">
                    <label for="creditcardDefaultDescription" class="text-left col-md-3 control-label">{s name='fieldlabel/creditcardDefaultDescription'}Kreditkarte Zusätzliche Beschreibung{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" id="creditcardDefaultDescription" name="creditcardDefaultDescription" aria-describedby="creditcardDefaultDescription-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="creditcardDefaultDescription-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name="fieldlabelhelp/paypalExpressUseDefaultShipping"}Wenn aktiviert, werden die vorläufigen Versandkosten mit an Paypal Express übergeben{/s}">
                    <label for="paypalExpressUseDefaultShipping" class="text-left col-md-3 control-label">{s name="fieldlabel/paypalExpressUseDefaultShipping"}Vorläufige Versandkosten bei Paypal Express übergeben{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="paypalExpressUseDefaultShipping" name="paypalExpressUseDefaultShipping" aria-describedby="paypalExpressUseDefaultShipping-status" >
                            <option value="false">Nein</option>
                            <option value="true">Ja</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="paypalExpressUseDefaultShipping-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name="fieldlabelhelp/paypalV2ShowButton"}{/s}">
                    <label for="paypalV2ShowButton" class="text-left col-md-3 control-label">{s name="fieldlabel/paypalV2ShowButton"}Paypal V2 BNPL Button anzeigen{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="paypalV2ShowButton" name="paypalV2ShowButton" aria-describedby="paypalV2ShowButton-status" >
                            <option value="false">Nein</option>
                            <option value="true">Ja</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="paypalV2ShowButton-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte" >
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name='fieldlabelhelp/paypalV2MerchantId'}Im Testmodus NICHT erforderlich. Da wird eine feste ID von Payone verwendet.{/s}">
                    <label for="paypalV2MerchantId" class="text-left col-md-3 control-label">{s name='fieldlabel/paypalV2MerchantId'}Paypal V2 Merchant ID{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" id="paypalV2MerchantId" name="paypalV2MerchantId" aria-describedby="paypalV2MerchantId-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="paypalV2MerchantId-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name="fieldlabelhelp/paypalV2ShowButton"}Paypal V2 Express Button Farbe{/s}">
                    <label for="paypalV2ButtonColor" class="text-left col-md-3 control-label">{s name="fieldlabel/paypalV2ButtonColor"}Paypal V2 Express Button Farbe{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="paypalV2ButtonColor" name="paypalV2ButtonColor" aria-describedby="paypalV2ButtonColor-status" >
                            <option value="gold">Gold</option>
                            <option value="blue">Blau</option>
                            <option value="silver">Silber</option>
                            <option value="white">Weiss</option>
                            <option value="black">Schwarz</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="paypalV2ButtonColor-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name="fieldlabelhelp/paypalV2ButtonShape"}{/s}">
                    <label for="paypalV2ButtonShape" class="text-left col-md-3 control-label">{s name="fieldlabel/paypalV2ButtonShape"}Paypal V2 BNPL Button anzeigen{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="paypalV2ButtonShape" name="paypalV2ButtonShape" aria-describedby="paypalV2ButtonShape-status" >
                            <option value="rect">Standard</option>
                            <option value="pill">Runde Ecken</option>
                            <option value="sharp">Spitze Ecken</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="paypalV2ButtonShape-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name="fieldlabelhelp/googlepayMerchantId"}GooglePay MerchantId{/s}">
                    <label for="googlepayMerchantId" class="text-left col-md-3 control-label">{s name="fieldlabel/googlepayMerchantId"}GooglePay MerchantId{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="googlepayMerchantId" name="googlepayMerchantId" aria-describedby="googlepayMerchantId-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="googlepayMerchantId-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name="fieldlabelhelp/googlepayAllowVisa"}GooglePay Visa zulassen{/s}">
                    <label for="googlepayAllowVisa" class="text-left col-md-3 control-label">{s name="fieldlabel/googlepayAllowVisa"}GooglePay Visa zulassen{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="googlepayAllowVisa" name="googlepayAllowVisa" aria-describedby="googlepayAllowVisa-status" >
                            <option value="false">Nein</option>
                            <option value="true">Ja</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="googlepayAllowVisa-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name="fieldlabelhelp/googlepayAllowMasterCard"}GooglePay Mastercard zulassen{/s}">
                    <label for="googlepayAllowMasterCard" class="text-left col-md-3 control-label">{s name="fieldlabel/googlepayAllowMasterCard"}GooglePay Mastercard zulassen{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="googlepayAllowMasterCard" name="googlepayAllowMasterCard" aria-describedby="googlepayAllowMasterCard-status" >
                            <option value="false">Nein</option>
                            <option value="true">Ja</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="googlepayAllowMasterCard-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name="fieldlabelhelp/googlepayAllowPrepaidCards"}GooglePay Prepaid Karten zulassen{/s}">
                    <label for="googlepayAllowPrepaidCards" class="text-left col-md-3 control-label">{s name="fieldlabel/googlepayAllowPrepaidCards"}GooglePay Prepaid Karten zulassen{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="googlepayAllowPrepaidCards" name="googlepayAllowPrepaidCards" aria-describedby="googlepayAllowPrepaidCards-status" >
                            <option value="false">Nein</option>
                            <option value="true">Ja</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="googlepayAllowPrepaidCards-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name="fieldlabelhelp/googlepayAllowCreditCards"}GooglePay Kreditkarten zulassen{/s}">
                    <label for="googlepayAllowCreditCards" class="text-left col-md-3 control-label">{s name="fieldlabel/googlepayAllowCreditCards"}GooglePay Kreditkarten zulassen{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="googlepayAllowCreditCards" name="googlepayAllowCreditCards" aria-describedby="googlepayAllowCreditCards-status" >
                            <option value="false">Nein</option>
                            <option value="true">Ja</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="googlepayAllowCreditCards-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte" >
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name='fieldlabelhelp/googlepayCountryCode'}GooglePay Acquirer Ländercode{/s}">
                    <label for="googlepayCountryCode" class="text-left col-md-3 control-label">{s name='fieldlabel/googlepayCountryCode'}GooglePay Acquirer Ländercode{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" id="googlepayCountryCode" name="googlepayCountryCode" aria-describedby="googlepayCountryCode-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="googlepayCountryCode-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name="fieldlabelhelp/googlepayButtonColor"}Googlepay Button Farb{/s}">
                    <label for="googlepayButtonColor" class="text-left col-md-3 control-label">{s name="fieldlabel/googlepayButtonColor"}Googlepay Button Farb{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="googlepayButtonColor" name="googlepayButtonColor" aria-describedby="googlepayButtonColor-status" >
                            <option value="default">{s name="fieldvalue/default"}Default{/s}</option>
                            <option value="white">{s name="fieldvalue/white"}Weiss{/s}</option>
                            <option value="black">{s name="fieldvalue/black"}Schwarz{/s}</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="googlepayButtonColor-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" title="PAYONE Hilfe" data-content="{s name="fieldlabelhelp/googlepayButtonType"}GooglePay Button Typ{/s}">
                    <label for="googlepayButtonType" class="text-left col-md-3 control-label">{s name="fieldlabel/googlepayButtonType"}GooglePay Button Typ{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="googlepayButtonType" name="googlepayButtonType" aria-describedby="googlepayButtonType-status" >
                            <option value="buy">{s name="fieldvalue/buy"}Buy{/s}</option>
                            <option value="checkout">{s name="fieldvalue/checkout"}Checkout{/s}</option>
                            <option value="order">{s name="fieldvalue/order"}Order{/s}</option>
                            <option value="pay">{s name="fieldvalue/pay"}Pay{/s}</option>
                            <option value="plain">{s name="fieldvalue/plain"}Plain{/s}</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="googlepayButtonType-status" class="sr-only">(success)</span>
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
                        $('#allowDifferentAddresses').prop( "disabled", true);
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

        $(function () {
            $('[data-toggle="popover"]').popover()
        });
    </script>
{/block}
