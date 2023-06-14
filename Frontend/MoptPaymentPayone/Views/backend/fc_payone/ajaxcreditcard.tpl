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
        <h3>{s name="global-form/fieldset2"}Kreditkarteneinstellungen{/s}</h3>
        <div>
            Stellen Sie hier die Konfiguration zur Zahlart Kreditkarte ein.
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="btn-group">
                    <button id="paymentmethodsdropdown" type="button" class="btn-payone-fixed btn-payone btn dropdown-toggle" data-toggle="dropdown">
                        <span class="selection">{s name="paymentMethod/label"}Gilt für Zahlart:{/s}</span><span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        {foreach from=$payonepaymentmethods item=paymentmethod}
                            <li><a href="#" id="{$paymentmethod.id}">{$paymentmethod.description}</a></li>
                            {/foreach}   
                    </ul>
                </div>
            </div>
        </div>
        <div class='col-md-12'>
            <form role="form" id="ajaxcreditcardform" class="form-horizontal">
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">

                    <label for="description" class="text-left col-md-3 control-label">{s name="formpanel_description_label"}Bezeichnung{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .\(\)\+\-?,:;"!@#$%!^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="0" maxlength="200" name="description" aria-describedby="description-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="description-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">

                    <label for="additionalDescription" class="text-left col-md-3 control-label">{s name="formpanel_additional-description_label"}Zusätzliche Beschreibung{/s}</label>
                    <div class="col-md-6">
                        <textarea rows="3" class="form-control " pattern='^[_ .\(\)\+\-?,:;"!@#$%!^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="0" maxlength="200" id="additionalDescription" name="additionalDescription" aria-describedby="additionalDescription-status" >
                        </textarea>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="additionalDescription-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>  
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">

                    <label for="debitPercent" class="text-left col-md-3 control-label">{s name="formpanel_surcharge_label"}Aufschlag/Abschlag (in %){/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[,.0-9]*' minlength="0" maxlength="200" minlength="0" maxlength="3" id="debitPercent" name="debitPercent" aria-describedby="debitPercent-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="debitPercent-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">

                    <label for="surcharge" class="text-left col-md-3 control-label">{s name="formpanel_generalSurcharge_label"}Pauschaler Aufschlag{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[,.0-9]*' minlength="0" maxlength="200" id="surcharge" name="surcharge" aria-describedby="surcharge-status" >
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
                        <input type="checkbox" class="form-control " pattern='^[_ .\(\)\+\-?,:;"!@#$%!^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="0" maxlength="200" id="active" name="active" aria-describedby="active-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="active-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">

                    <label for="esdActive" class="text-left col-md-3 control-label">{s name="formpanel_esdActive_label"}Aktiv für ESD-Produkte{/s}</label>
                    <div class="col-md-6">
                        <input type="checkbox" class="form-control " pattern='^[_ .\(\)\+\-?,:;"!@#$%!^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="0" maxlength="200" id="esdActive" name="esdActive" aria-describedby="esdActive-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="esdActive-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">

                    <label for="mobileInactive" class="text-left col-md-3 control-label">{s name="formpanel_mobileInactive_label"}Inaktiv für Smartphone{/s}</label>
                    <div class="col-md-6">
                        <input type="checkbox" class="form-control " pattern='^[_ .\(\)\+\-?,:;"!@#$%!^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="0" maxlength="200" id="mobileInactive" name="mobileInactive" aria-describedby="mobileInactive-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="mobileInactive-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>                
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte" >

                    <label for="checkCc" class="text-left col-md-3 control-label">{s name="fieldlabel/checkCc"}Abfrage Kreditkartenprüfziffer<br>(nur global konfigurierbar){/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .\(\)\+\-?,:;"!@#$%!^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="0" maxlength="200" id="checkCc" name="checkCc" aria-describedby="checkCc-status" >
                            <option value="true">Ja</option>
                            <option value="false">Nein</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="checkCc-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>   
                <div class="form-group has-feedback has-error menu-level-experte"">
                    <img src="{link file="backend/_resources/images/information.png"}" data-toggle="popover" title="PAYONE Hilfe" data-content="Gültigkeit der Kreditkarte in Tagen zudem eine Kreditkarte im Checkout akzeptiert wird.">                   
                    <label for="creditcardMinValid" class="text-left col-md-3 control-label">{s name="fieldlabel/creditcardMinValid"}Gültigkeit der Kreditkarte{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[0-9]*' minlength="0" maxlength="200" id="creditcardMinValid" name="creditcardMinValid" aria-describedby="creditcardMinValid-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="creditcardMinValid-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>                       
                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
        <a style="font-size: 28px" href="#" data-toggle="collapse" data-target="#payonetable">Konfiguration hosted iFrame</a>
        <div id="payonetable" class="collapse">
            <form role="form" id="ajaxiframeconfigform">
                <table class="table-condensed">
                    <tr>
                        <th>Feld</th>
                        <th>Typ</th>
                        <th>Anzahl<br/>Zeichen</th>
                        <th>Zeichen<br/>Max</th>
                        <th>Iframe</th>
                        <th>Breite</th>
                        <th>Höhe</th>
                        <th>Stil</th>
                        <th>Css</th>
                        <th>Platzhalter</th>
                    </tr>
                    <tr class="form-group">
                        <th>Kreditkartennummer</th>
                        <td><select name="cardnoFieldType" id="cardnoFieldType" style="max-width:125px;" class="form-control">
                                <option value="tel">Numerisch</option>
                                <option value="password">Passwort</option>
                                <option value="text">Text</option>
                                <option value="select">Auswahl</option>
                            </select></td>
                        <td><input name="cardnoInputChars" id="cardnoInputChars" type="text" class="form-control" value="30" size="3"/></td>
                        <td><input name="cardnoInputCharsMax" id="cardnoInputCharsMax" type="text" class="form-control" value="16" size="3"/></td>
                        <td><select name="cardnoCustomIframe" id="cardnoCustomIframe" style="max-width:125px;" class="form-control">
                                <option value="true">Standard</option>
                                <option value="false">Benutzerdefiniert</option>
                            </select></td>
                        <td><input name="cardnoIframeWidth" id="cardnoIframeWidth" type="text" class="form-control" value="200px" size="4"/></td>
                        <td><input name="cardnoIframeHeight" id="cardnoIframeWidth" type="text" class="form-control" value="300px" size="4"/></td>
                        <td><select name="cardnoCustomStyle" id="cardnoIframeWidth" style="max-width:125px;" class="form-control">
                                <option value="true">Standard</option>
                                <option value="false">Benutzerdefiniert</option>
                            </select></td>
                        <td><input name="cardnoInputCss" id="cardnoInputCss" type="text" class="form-control" /></td>
                        <td><input name="defaultTranslationIframeCardpan" id="defaultTranslationIframeCardpan" type="text" class="form-control" /></td>
                    </tr>
                    <tr class="form-group">
                        <th>Kreditkartenprüfziffer</th>
                        <td><select name="cardcvcFieldType" id="cardcvcFieldType" style="max-width:125px;" class="form-control">
                                <option value="tel">Numerisch</option>
                                <option value="password">Passwort</option>
                                <option value="text">Text</option>
                                <option value="select">Auswahl</option>
                            </select></td>
                        <td><input name="cardcvcInputChars" id="cardcvcInputChars" type="text" class="form-control" value="30" size="3"/></td>
                        <td><input name="cardcvcInputCharsMax" id="cardcvcInputCharsMax" type="text" class="form-control" value="16" size="3"/></td>
                        <td><select name="cardcvcCustomIframe" id="cardcvcCustomIframe" style="max-width:125px;" class="form-control">
                                <option value="true">Standard</option>
                                <option value="false">Benutzerdefiniert</option>
                            </select></td>
                        <td><input name="cardcvcIframeWidth" id="cardcvcIframeWidth" type="text" class="form-control" value="200px" size="4"/></td>
                        <td><input name="cardcvcIframeHeight" id="cardcvcIframeHeight" type="text" class="form-control" value="300px" size="4"/></td>
                        <td><select name="cardcvcCustomStyle" id="cardcvcCustomStyle" style="max-width:125px;" class="form-control">
                                <option value="true">Standard</option>
                                <option value="false">Benutzerdefiniert</option>
                            </select></td>
                        <td><input name="cardcvcInputCss" id="cardcvcInputCss" type="text" class="form-control" /></td>
                        <td><input name="defaultTranslationIframeCvc" id="defaultTranslationIframeCvc" type="text" class="form-control" /></td>
                    </tr>
                    <tr class="form-group">
                        <th>Gültigkeitsmonat</th>
                        <td><select name="cardmonthFieldType" id="cardmonthFieldType" style="max-width:125px;" class="form-control">
                                <option value="tel">Numerisch</option>
                                <option value="password">Passwort</option>
                                <option value="text">Text</option>
                                <option value="select">Auswahl</option>
                            </select></td>
                        <td><input name="cardmonthInputChars" id="cardmonthInputChars" type="text" class="form-control" value="30" size="3"/></td>
                        <td><input name="cardmonthInputCharsMax" id="cardmonthInputCharsMax" type="text" class="form-control" value="16" size="3"/></td>
                        <td><select name="cardmonthCustomIframe" id="cardmonthCustomIframe" style="max-width:125px;" class="form-control">
                                <option value="true">Standard</option>
                                <option value="false">Benutzerdefiniert</option>
                            </select></td>
                        <td><input name="cardmonthIframeWidth" id="cardmonthIframeWidth" type="text" class="form-control" value="200px" size="4"/></td>
                        <td><input name="cardmonthIframeHeight" id="cardmonthIframeHeight" type="text" class="form-control" value="300px" size="4"/></td>
                        <td><select name="cardmonthCustomStyle" id="cardmonthCustomStyle" style="max-width:125px;" class="form-control">
                                <option value="true">Standard</option>
                                <option value="false">Benutzerdefiniert</option>
                            </select></td>
                        <td><input name="cardmonthInputCss" id="cardmonthInputCss" type="text" class="form-control" /></td>
                    </tr>
                    <tr class="form-group">
                        <th>Gültigkeitsjahr</th>
                        <td><select name="cardyearFieldType" id="cardyearFieldType" style="max-width:125px;" class="form-control">
                                <option value="tel">Numerisch</option>
                                <option value="password">Passwort</option>
                                <option value="text">Text</option>
                                <option value="select">Auswahl</option>
                            </select></td>
                        <td><input name="cardyearInputChars" id="cardyearInputChars" type="text" class="form-control" value="30" size="3"/></td>
                        <td><input name="cardyearInputCharsMax" id="cardyearInputCharsMax" type="text" class="form-control" value="16" size="3"/></td>
                        <td><select name="cardyearCustomIframe" id="cardyearCustomIframe" style="max-width:125px;" class="form-control">
                                <option value="true">Standard</option>
                                <option value="false">Benutzerdefiniert</option>
                            </select></td>
                        <td><input name="cardyearIframeWidth" id="cardyearIframeWidth" type="text" class="form-control" value="200px" size="4"/></td>
                        <td><input name="cardyearIframeHeight" id="cardyearIframeHeight" type="text" class="form-control" value="300px" size="4"/></td>
                        <td><select name="cardyearCustomStyle" id="cardyearCustomStyle" style="max-width:125px;" class="form-control">
                                <option value="true">Standard</option>
                                <option value="false">Benutzerdefiniert</option>
                            </select></td>
                        <td><input name="cardyearInputCss" id="cardyearInputCss" type="text" class="form-control" /></td>
                    </tr>
                </table>

                <table class="table-condensed">
                    <tr>
                        <th>Standardstil</th>
                        <th>Eingabe</th>
                        <th>Auswahl</th>
                    </tr>
                    <tr class="form-group">
                        <th>Felder</th>
                        <td><input name="standardInputCss" id="standardInputCss" type="text" class="form-control"/></td>
                        <td><input name="standardInputCssSelected" id="standardInputCssSelected" type="text" class="form-control"/></td>
                    </tr>
                    <tr class="form-group">
                        <th>Iframe</th>
                        <td></span><input name="standardIframeHeight" id="standardIframeHeight" type="text" class="form-control" placeholder="Breite"/></td>
                        <td><input name="standardIframeWidth" id="standardIframeWidth" type="text" class="form-control" placeholder="Höhe"/></td>
                    </tr>

                <table class="table-condensed">
                    <th>Standardübersetzung</th>
                    <tr>
                        <th>Monat</th>
                        <th>Übersetzung</th>
                        <th>Monat</th>
                        <th>Übersetzung</th>
                    </tr>
                    <tr class="form-group">
                        <th>Januar</th>
                        <td><input name="defaultTranslationIframeMonth1" id="defaultTranslationIframeMonth1" type="text" class="form-control"/></td>
                        <th>Juli</th>
                        <td><input name="defaultTranslationIframeMonth7" id="defaultTranslationIframeMonth7" type="text" class="form-control"/></td>
                    </tr>
                    <tr class="form-group">
                        <th>Februar</th>
                        <td><input name="defaultTranslationIframeMonth2" id="defaultTranslationIframeMonth2" type="text" class="form-control"/></td>
                        <th>August</th>
                        <td><input name="defaultTranslationIframeMonth8" id="defaultTranslationIframeMonth8" type="text" class="form-control"/></td>                        
                    </tr>
                    <tr class="form-group">
                        <th>März</th>
                        <td><input name="defaultTranslationIframeMonth3" id="defaultTranslationIframeMonth3" type="text" class="form-control"/></td>
                        <th>September</th>
                        <td><input name="defaultTranslationIframeMonth9" id="defaultTranslationIframeMonth9" type="text" class="form-control"/></td>                        
                    </tr>
                    <tr class="form-group">
                        <th>April</th>
                        <td><input name="defaultTranslationIframeMonth4" id="defaultTranslationIframeMonth4" type="text" class="form-control"/></td>
                        <th>Oktober</th>
                        <td><input name="defaultTranslationIframeMonth10" id="defaultTranslationIframeMonth10" type="text" class="form-control"/></td>                        
                    </tr>
                    <tr class="form-group">
                        <th>Mai</th>
                        <td><input name="defaultTranslationIframeMonth5" id="defaultTranslationIframeMonth5" type="text" class="form-control"/></td>
                        <th>November</th>
                        <td><input name="defaultTranslationIframeMonth11" id="defaultTranslationIframeMonth11" type="text" class="form-control"/></td>
                    </tr>
                    <tr class="form-group">
                        <th>Juni</th>
                        <td><input name="defaultTranslationIframeMonth6" id="defaultTranslationIframeMonth6" type="text" class="form-control"/></td>
                        <th>Dezember</th>
                        <td><input name="defaultTranslationIframeMonth12" id="defaultTranslationIframeMonth12" type="text" class="form-control"/></td>                        
                    </tr>
                </table>
                <table class="table-condensed">
                    <h3>Fehlerausgabe und eigene Fehlermeldungen<h3>
                    <tr class="form-group">
                        <th>Fehlerausgabe aktivieren</th>
                        <td><input name="showErrors" id="showErrors" type="checkbox" class="form-control"/></td>
                    </tr>
                    <tr>
                        <th>Fehlermeldung</th>
                        <th>eigene Fehlermeldung</th>
                    </tr>
                    <tr class="form-group">
                        <th>Ungültige Kreditkartennummer</th>
                        <td><input name="defaultTranslationIframeinvalidCardpan" id="defaultTranslationIframeinvalidCardpan" type="text" class="form-control"/></td>
                    </tr>
                    <tr class="form-group">
                        <th>Ungültige Kartenprüfziffer</th>
                        <td><input name="defaultTranslationIframeinvalidCvc" id="defaultTranslationIframeinvalidCvc" type="text" class="form-control"/></td>
                    </tr>
                    <tr class="form-group">
                        <th>Ungültige Kreditkartennummer für den Kartentyp</th>
                        <td><input name="defaultTranslationIframeinvalidPanForCardtype" id="defaultTranslationIframeinvalidPanForCardtype" type="text" class="form-control"/></td>
                    </tr>
                    <tr class="form-group">
                        <th>Ungültiger Kartentyp</th>
                        <td><input name="defaultTranslationIframeinvalidCardtype" id="defaultTranslationIframeinvalidCardtype" type="text" class="form-control"/></td>
                    </tr>
                    <tr class="form-group">
                        <th>Ungültiges Verfallsdatum</th>
                        <td><input name="defaultTranslationIframeinvalidExpireDate" id="defaultTranslationIframeinvalidExpireDate" type="text" class="form-control"/></td>
                    </tr>
                    <tr class="form-group">
                        <th>Ungültige Ausstellungsnummer</th>
                        <td><input name="defaultTranslationIframeinvalidIssueNumber" id="defaultTranslationIframeinvalidIssueNumber" type="text" class="form-control"/></td>
                    </tr>
                    <tr class="form-group">
                        <th>Transaktion abgelehnt</th>
                        <td><input name="defaultTranslationIframetransactionRejected" id="defaultTranslationIframetransactionRejected" type="text" class="form-control"/></td>
                    </tr>                    
                </table>                    
                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}  
    <script type="text/javascript" src="{link file="backend/_resources/js/formhelper.js"}"></script>

    <script type="text/javascript">

        var form = $('#ajaxcreditcardform');
        var iframeform = $('#ajaxiframeconfigform');
        var url = "{url controller=FcPayone action=ajaxgetCreditCardConfig forceSecure}";
        var iframeurl = "{url controller=FcPayone action=ajaxgetIframeConfig forceSecure}";
        var paymentid = null;

        $(document).ready(function ()
        {
            var params = "paymentid=0";
            var call = url + '?' + params;
            var iframecall = iframeurl;
            var iframedata = "";

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
                url: iframecall,
                type: 'POST',
                success: function (iframedata) {
                    response = $.parseJSON(iframedata);
                    if (response.status === 'success') {
                        populateForm(iframeform, response.iframedata);
                    }
                    if (response.status === 'error') {
                    }
                }
            });
        });

        $(".dropdown-menu li a").click(function () {
            var params = "paymentid=" + this.id;
            var call = url + '?' + params;
            var data = "";
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
                        alert("success");
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
            var url = 'ajaxSavePaymentConfig';
            values = values + '&paymentId=' + paymentid;
            $.post(url, values, function (response) {
                var data_array = $.parseJSON(response);
                showalert("Die Daten wurden gespeichert", "alert-success");
            });
        });

        iframeform.on("submit", function (event) {
            event.preventDefault();
            var checkboxes = iframeform.find('input[type="checkbox"]');
            $.each(checkboxes, function (key, value) {
                if (value.checked === false) {
                    value.value = 0;
                } else {
                    value.value = 1;
                }
                $(value).attr('type', 'hidden');
            });
            iframevalues = iframeform.serialize();
            $.each(checkboxes, function (key, value) {
                $(value).attr('type', 'checkbox');
            });
            var url = 'ajaxSaveIframeConfig';
            iframevalues = iframevalues + '&paymentId=' + paymentid;
            $.post(url, iframevalues, function (response) {
                var data_array = $.parseJSON(response);
                showalert("Die Daten wurden gespeichert", "alert-success");
            });
        });

        $(function () {
            $('[data-toggle="popover"]').popover()
        });
    </script>
{/block}
