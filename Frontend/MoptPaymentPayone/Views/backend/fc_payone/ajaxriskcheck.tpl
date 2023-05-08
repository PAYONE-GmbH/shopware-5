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
        <h3>{s name="global-form/fieldset3"}Bonitätsprüfung{/s}</h3>
        <div>
            Stellen Sie hier die Konfiguration für die Bonitätsprüfung für alle Zahlarten ein.
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
            <form role="form" id="ajaxriskcheckform" class="form-horizontal">
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="consumerscoreActive" class="text-left col-md-3 control-label">{s name="fieldlabel/active"}Aktiv{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="consumerscoreActive" name="consumerscoreActive" aria-describedby="consumerscoreActive-status" >
                            <option value="true">Ja</option>
                            <option value="false">Nein</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="consumerscoreActive-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="consumerscoreLiveMode" class="text-left col-md-3 control-label">{s name="fieldlabel/mode"}Betriebsmodus{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="consumerscoreLiveMode" name="consumerscoreLiveMode" aria-describedby="consumerscoreLiveMode-status" >
                            <option value="true">Live</option>
                            <option value="false">Test</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="consumerscoreLiveMode-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="consumerscoreCheckMoment" class="text-left col-md-3 control-label">{s name="fieldlabel/consumerscoreCheckMoment"}Zeitpunkt der Prüfung{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="consumerscoreCheckMoment" name="consumerscoreCheckMoment" aria-describedby="consumerscoreCheckMoment-status" >
                        <option value="0">Vor der Zahlartenauswahl</option>
                        <option value="1">Nach der Zahlartenauswahl</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="consumerscoreCheckMoment-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="consumerscoreCheckModeB2C" class="text-left col-md-3 control-label">{s name="consumerscoreCheckModeB2C/active"}Prüfungsart B2C{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="consumerscoreCheckModeB2C" name="consumerscoreCheckModeB2C" aria-describedby="consumerscoreCheckModeB2C-status" >
                        <option value="IH">Infoscore (harte Kriterien)</option>
                        <option value="IA">Infoscore (alle Merkmale)</option>
                        <option value="IB">Infoscore (alle Merkmale + Boniscore)</option>
                        <option value="CE">Boniversum VERITA Score</option>
                        <option value="NO">keine Prüfung</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="consumerscoreCheckModeB2C-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">

                    <label for="consumerscoreCheckModeB2B" class="text-left col-md-3 control-label">{s name="consumerscoreCheckModeB2B/active"}Prüfungsart B2B{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="consumerscoreCheckModeB2B" name="consumerscoreCheckModeB2B" aria-describedby="consumerscoreCheckModeB2B-status" >
                            <option value="NO">keine Prüfung</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="consumerscoreCheckModeB2B-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="consumerscoreDefault" class="text-left col-md-3 control-label">{s name="fieldlabel/consumerscoreDefault"}Standardwert für Neukunden{/s}</label>
                    <div class="col-md-6">
                        <select type="" class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="consumerscoreDefault" name="consumerscoreDefault" aria-describedby="consumerscoreDefault-status" >
                        <option value="0">Rot</option>
                        <option value="1">Gelb</option>
                        <option value="2">Grün</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="consumerscoreDefault-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">

                    <label for="consumerscoreBoniversumUnknown" class="text-left col-md-3 control-label">{s name="fieldlabel/consumerscoreBoniversumUnknown"}Boniversum unbekannt{/s}</label>
                    <div class="col-md-6">
                        <select type="" class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="consumerscoreDefault" name="consumerscoreBoniversumUnknown" aria-describedby="consumerscoreBoniversumUnknown-status" >
                            <option value="0">Rot</option>
                            <option value="1">Gelb</option>
                            <option value="2">Grün</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="consumerscoreBoniversumUnknown-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="consumerscoreLifetime" class="text-left col-md-3 control-label">{s name="fieldlabel/lifetime"}Gültigkeit{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[0-9]*' minlength="1" maxlength="200" id="consumerscoreLifetime" name="consumerscoreLifetime" aria-describedby="consumerscoreLifetime-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="consumerscoreLifetime-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="consumerscoreMinBasket" class="text-left col-md-3 control-label">{s name="fieldlabel/minBasket"}Minimaler Warenwert{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[,.0-9]*' minlength="1" maxlength="200" id="consumerscoreMinBasket" name="consumerscoreMinBasket" aria-describedby="consumerscoreMinBasket-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="consumerscoreMinBasket-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
                   
                    <label for="consumerscoreMaxBasket" class="text-left col-md-3 control-label">{s name="fieldlabel/maxBasket"}Maximaler Warenwert{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[,.0-9]*' minlength="1" maxlength="200" id="consumerscoreMaxBasket" name="consumerscoreMaxBasket" aria-describedby="consumerscoreMaxBasket-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="consumerscoreMaxBasket-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="consumerscoreFailureHandling" class="text-left col-md-3 control-label">{s name="fieldlabel/failureHandling"}Fehlverhalten{/s}</label>
                    <div class="col-md-6">
                        <select class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="consumerscoreFailureHandling" name="consumerscoreFailureHandling" aria-describedby="consumerscoreFailureHandling-status" >
                        <option value="0">Vorgang abbrechen</option>
                        <option value="1">fortfahren</option>
                        </select>
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="consumerscoreFailureHandling-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="consumerscoreNoteActive" class="text-left col-md-3 control-label">{s name="fieldlabel/consumerscoreNote"}Hinweistext{/s} {s name="fieldlabel/active"}Aktiv{/s}</label>
                    <div class="col-md-6">
                        <input type="checkbox" class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="consumerscoreNoteActive" name="consumerscoreNoteActive" aria-describedby="consumerscoreNoteActive-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="consumerscoreNoteActive-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                    <img src="{link file="backend/_resources/images/information.png"}" data-toggle="popover" title="PAYONE Hilfe" data-content="Fehlermeldung bitte über Einstellungen -> Textbausteine editieren (nach consumerscoreNoteMessage suchen)">                                                         
                    <label for="consumerscoreNoteMessage" class="text-left col-md-3 control-label">{s name="fieldlabel/consumerscoreNote"}Hinweistext (nur bei Prüfung nach der Zahlartenauswahl){/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="consumerscoreNoteMessage" name="consumerscoreNoteMessage" aria-describedby="consumerscoreNoteMessage-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="consumerscoreNoteMessage-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="consumerscoreAgreementActive" class="text-left col-md-3 control-label">{s name="fieldlabel/consumerscoreAgreement"}Zustimmungsfrage{/s} {s name="fieldlabel/active"}Aktiv{/s}</label>
                    <div class="col-md-6">
                        <input type="checkbox" class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="consumerscoreAgreementActive" name="consumerscoreAgreementActive" aria-describedby="consumerscoreAgreementActive-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="consumerscoreAgreementActive-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                    <img src="{link file="backend/_resources/images/information.png"}" data-toggle="popover" title="PAYONE Hilfe" data-content="Fehlermeldung bitte über Einstellungen -> Textbausteine editieren (nach consumerscoreAgreementMessage suchen)">                    
                    <label for="consumerscoreAgreementMessage" class="text-left col-md-3 control-label">{s name="fieldlabel/consumerscoreAgreement"}Zustimmungsfrage (nur bei Prüfung nach der Zahlartenauswahl){/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="consumerscoreAgreementMessage" name="consumerscoreAgreementMessage" aria-describedby="consumerscoreAgreementMessage-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="consumerscoreAgreementMessage-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="consumerscoreAbtestActive" class="text-left col-md-3 control-label">{s name="fieldlabel/abtest"}A/B Test{/s} {s name="fieldlabel/active"}Aktiv{/s}</label>
                    <div class="col-md-6">
                        <input type="checkbox" class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="consumerscoreAbtestActive" name="consumerscoreAbtestActive" aria-describedby="consumerscoreAbtestActive-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="consumerscoreAbtestActive-status" class="sr-only">(success)</span>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                <div class="form-group has-feedback has-error menu-level-experte">
                   
                    <label for="consumerscoreAbtestValue" class="text-left col-md-3 control-label">{s name="fieldlabel/abtest"}A/B Test{/s}</label>
                    <div class="col-md-6">
                        <input type="text" class="form-control " pattern='^[_ .()+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="200" id="consumerscoreAbtestValue" name="consumerscoreAbtestValue" aria-describedby="consumerscoreAbtestValue-status" >
                        <span class="glyphicon form-control-feedback glyphicon-remove" aria-hidden="true"></span>
                        <span id="consumerscoreAbtestValue-status" class="sr-only">(success)</span>
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
        
        var form = $('#ajaxriskcheckform');
        var url = "{url controller=FcPayone action=ajaxgetRiskCheckConfig forceSecure}";     
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
