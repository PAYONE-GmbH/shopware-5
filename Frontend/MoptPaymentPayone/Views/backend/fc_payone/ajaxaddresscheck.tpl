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
        <h3>{s name="global-form/fieldset2"}Addressüberprüfung{/s}</h3>
        <div>
            Stellen Sie hier die Konfiguration für die Addressüberprüfung für alle Zahlarten ein.
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="btn-group">
                    <button id="paymentmethodsdropdown" type="button" class="btn-payone-fixed btn-payone btn dropdown-toggle" data-toggle="dropdown">
                        <span class="selection">Alle Zahlarten - Global</span><span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#" id="0" selected>Alle Zahlarten - Global</a></li>
                            {foreach from=$payonepaymentmethods item=paymentmethod}
                            <li><a href="#" id="{$paymentmethod.id}">{$paymentmethod.description}</a></li>
                            {/foreach}   
                    </ul>
                </div>
            </div>
        </div>
        <div class='col-md-9'>
            <form role="form" id="ajaxaddresscheckform" class="form-horizontal">
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="adresscheckActive" class="text-left col-md-3 control-label">{s name="fieldlabel/active"}Aktiv{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" id="adresscheckActive" name="adresscheckActive" aria-describedby="adresscheckActive-status" >
                            <option value='true'>Ja</option>
                            <option value='false'>Nein</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="adresscheckActive-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="adresscheckLiveMode" class="text-left col-md-3 control-label">{s name="fieldlabel/mode"}Betriebsmodus{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" id="adresscheckLiveMode" name="adresscheckLiveMode" aria-describedby="adresscheckLiveMode-status" >
                            <option value="true">Live</option>
                            <option value="false">Test</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="adresscheckLiveMode-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="adresscheckBillingAdress" class="text-left col-md-3 control-label">{s name="fieldlabel/billingAddress"}Rechnungsadresse{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" id="adresscheckBillingAdress" name="adresscheckBillingAdress" aria-describedby="adresscheckBillingAdress-status" >
                            <option value="0">nicht prüfen</option>
                            <option value="1">Basic</option>
                            <option value="2">Person</option>
                            <option value="3">Boniversum Basic</option>
                            <option value="4">Boniversum Person</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="adresscheckBillingAdress-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file="backend/_resources/images/information.png"}" data-toggle="popover" title="PAYONE Hilfe" data-content="Komma-getrennte ISO-Codes der Länder für die der Check ausgeführt werden soll. z.B. DE,CH,AT">
                    <label for="adresscheckBillingCountries" class="text-left col-md-3 control-label">{s name="fieldlabel/adresscheckBillingCountries"}Länder Rechnungsadresse{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" id="adresscheckBillingCountries" name="adresscheckBillingCountries" aria-describedby="adresscheckBillingCountries-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="adresscheckBillingCountries-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="adresscheckShippingAdress" class="text-left col-md-3 control-label">{s name="fieldlabel/shippingAddress"}Lieferadresse{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" id="adresscheckShippingAdress" name="adresscheckShippingAdress" aria-describedby="adresscheckShippingAdress-status" >
                            <option value="0">nicht prüfen</option>
                            <option value="1">Basic</option>
                            <option value="2">Person</option>
                            <option value="3">Boniversum Basic</option>
                            <option value="4">Boniversum Person</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="adresscheckShippingAdress-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                    <img src="{link file="backend/_resources/images/information.png"}" data-toggle="popover" title="PAYONE Hilfe" data-content="Komma-getrennte ISO-Codes der Länder für die der Check ausgeführt werden soll. z.B. DE,CH,AT">                   
                    <label for="adresscheckShippingCountries" class="text-left col-md-3 control-label">{s name="fieldlabel/adresscheckShippingCountries"}Länder Lieferadresse{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="3" id="adresscheckShippingCountries" name="adresscheckShippingCountries" aria-describedby="adresscheckShippingCountries-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="adresscheckShippingCountries-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="adresscheckAutomaticCorrection" class="text-left col-md-3 control-label">{s name="fieldlabel/automaticCorrection"}Automatische Korrektur{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" id="adresscheckAutomaticCorrection" name="adresscheckAutomaticCorrection" aria-describedby="adresscheckAutomaticCorrection-status" >
                            <option value="0">Ja</option>
                            <option value="1">Nein</option>
                            <option value="2">Benutzerentscheidung</option>                            
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="adresscheckAutomaticCorrection-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="adresscheckFailureHandling" class="text-left col-md-3 control-label">{s name="fieldlabel/failureHandling"}Fehlverhalten{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" id="adresscheckFailureHandling" name="adresscheckFailureHandling" aria-describedby="adresscheckFailureHandling-status" >
                            <option value="0">Vorgang abbrechen</option>
                            <option value="1">Neueingabe der Adresse die zum Fehler geführt hat</option>
                            <option value="2">Anschließende Bonitätsprüfung durchführen</option> 
                            <option value="3">fortfahren</option> 
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="adresscheckFailureHandling-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="adresscheckMinBasket" class="text-left col-md-3 control-label">{s name="fieldlabel/maxBasket"}Maximaler Warenwert{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[,.0-9]*' minlength="1" maxlength="255" id="adresscheckMinBasket" name="adresscheckMinBasket" aria-describedby="adresscheckMinBasket-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="adresscheckMinBasket-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="adresscheckMaxBasket" class="text-left col-md-3 control-label">{s name="fieldlabel/maxBasket"}Maximaler Warenwert{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[,.0-9]*'  minlength="1" maxlength="255" id="adresscheckMaxBasket" name="adresscheckMaxBasket" aria-describedby="adresscheckMaxBasket-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="adresscheckMaxBasket-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="adresscheckLifetime" class="text-left col-md-3 control-label">{s name="fieldlabel/lifetime"}Gültigkeit{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[,.0-9]*' minlength="1" maxlength="255" id="adresscheckLifetime" name="adresscheckLifetime" aria-describedby="adresscheckLifetime-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="adresscheckLifetime-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>                
                <div class="form-group has-feedback has-error menu-level-experte">
                    <img src="{link file="backend/_resources/images/information.png"}" data-toggle="popover" title="PAYONE Hilfe" data-content="Fehlermeldung bitte über Einstellungen -> Textbausteine editieren (nach adresscheckErrorMessage suchen)">                                      
                    <label for="adresscheckFailureMessage" class="text-left col-md-3 control-label">{s name="fieldlabel/adresscheckFailureMessage"}Fehlermeldung{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" id="adresscheckFailureMessage" name="adresscheckFailureMessage" aria-describedby="adresscheckFailureMessage-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="adresscheckFailureMessage-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-experte">
                   
                    <label for="mapPersonCheck" class="text-left col-md-3 control-label">{s name="fieldlabel/mapPersonCheck"}Keine Personenüberprüfung durchgeführt{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" id="mapPersonCheck" name="mapPersonCheck" aria-describedby="mapPersonCheck-status" >
                            <option value="0">Rot</option>
                            <option value="1">Gelb</option>
                            <option value="2">Grün</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="mapPersonCheck-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="mapKnowPreLastname" class="text-left col-md-3 control-label">{s name="fieldlabel/mapKnowPreLastname"}Vor- und Nachname bekannt{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" id="mapKnowPreLastname" name="mapKnowPreLastname" aria-describedby="mapKnowPreLastname-status" >
                            <option value="0">Rot</option>
                            <option value="1">Gelb</option>
                            <option value="2">Grün</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="mapKnowPreLastname-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="mapKnowLastname" class="text-left col-md-3 control-label">{s name="fieldlabel/mapKnowLastname"}Nachname bekannt{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" id="mapKnowLastname" name="mapKnowLastname" aria-describedby="mapKnowLastname-status" >
                            <option value="0">Rot</option>
                            <option value="1">Gelb</option>
                            <option value="2">Grün</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="mapKnowLastname-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="mapNotKnowPreLastname" class="text-left col-md-3 control-label">{s name="fieldlabel/mapNotKnowPreLastname"}Vor- und Nachname nicht bekannt{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" id="mapNotKnowPreLastname" name="mapNotKnowPreLastname" aria-describedby="mapNotKnowPreLastname-status" >
                            <option value="0">Rot</option>
                            <option value="1">Gelb</option>
                            <option value="2">Grün</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="mapNotKnowPreLastname-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>   
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="mapMultiNameToAdress" class="text-left col-md-3 control-label">{s name="fieldlabel/mapMultiNameToAdress"}Mehrdeutigkeit bei Name zu Anschrift{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" id="mapMultiNameToAdress" name="mapMultiNameToAdress" aria-describedby="mapMultiNameToAdress-status" >
                            <option value="0">Rot</option>
                            <option value="1">Gelb</option>
                            <option value="2">Grün</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="mapMultiNameToAdress-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div> 
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="mapUndeliverable" class="text-left col-md-3 control-label">{s name="fieldlabel/mapUndeliverable"}nicht (mehr) zustellbar{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" id="mapUndeliverable" name="mapUndeliverable" aria-describedby="mapUndeliverable-status" >
                            <option value="0">Rot</option>
                            <option value="1">Gelb</option>
                            <option value="2">Grün</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="mapUndeliverable-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div> 
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="mapPersonDead" class="text-left col-md-3 control-label">{s name="fieldlabel/mapPersonDead"}Person verstorben{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" id="mapPersonDead" name="mapPersonDead" aria-describedby="mapPersonDead-status" >
                            <option value="0">Rot</option>
                            <option value="1">Gelb</option>
                            <option value="2">Grün</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="mapPersonDead-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div> 
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="mapWrongAdress" class="text-left col-md-3 control-label">{s name="fieldlabel/mapWrongAdress"}Adresse postalisch falsch{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" id="mapWrongAdress" name="mapWrongAdress" aria-describedby="mapWrongAdress-status" >
                            <option value="0">Rot</option>
                            <option value="1">Gelb</option>
                            <option value="2">Grün</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="mapWrongAdress-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">

                    <label for="mapAddressCheckNotPossible" class="text-left col-md-3 control-label">{s name="fieldlabel/mapAddressCheckNotPossible"}Überprüfung nicht möglich (z.B. Fakename){/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" id="mapAddressCheckNotPossible" name="mapAddressCheckNotPossible" aria-describedby="mapAddressCheckNotPossible-status" >
                            <option value="0">Rot</option>
                            <option value="1">Gelb</option>
                            <option value="2">Grün</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="mapAddressCheckNotPossible-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">

                    <label for="mapAddressOkayBuildingUnknown" class="text-left col-md-3 control-label">{s name="fieldlabel/mapAddressOkayBuildingUnknown"}Adresse korrekt, aber Gebäude unbekannt{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" id="mapAddressOkayBuildingUnknown" name="mapAddressOkayBuildingUnknown" aria-describedby="mapAddressOkayBuildingUnknown-status" >
                            <option value="0">Rot</option>
                            <option value="1">Gelb</option>
                            <option value="2">Grün</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="mapAddressOkayBuildingUnknown-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">

                    <label for="mapPersonMovedAddressUnknown" class="text-left col-md-3 control-label">{s name="fieldlabel/mapPersonMovedAddressUnknown"}Person umgezogen, Adresse nicht korrigiert{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" id="mapPersonMovedAddressUnknown" name="mapPersonMovedAddressUnknown" aria-describedby="mapPersonMovedAddressUnknown-status" >
                            <option value="0">Rot</option>
                            <option value="1">Gelb</option>
                            <option value="2">Grün</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="mapPersonMovedAddressUnknown-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">

                    <label for="mapUnknownReturnValue" class="text-left col-md-3 control-label">{s name="fieldlabel/mapUnknownReturnValue"}Rückgabewert der Überprüfung unbekannt{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="255" id="mapUnknownReturnValue" name="mapUnknownReturnValue" aria-describedby="mapUnknownReturnValue-status" >
                            <option value="0">Rot</option>
                            <option value="1">Gelb</option>
                            <option value="2">Grün</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="mapUnknownReturnValue-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}  
 <script type="text/javascript" src="{link file="backend/_resources/js/formhelper.js"}"></script>

    <script type="text/javascript">

        var form = $('#ajaxaddresscheckform');
        var url = "{url controller=FcPayone action=ajaxgetAddressCheckConfig forceSecure}";
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
