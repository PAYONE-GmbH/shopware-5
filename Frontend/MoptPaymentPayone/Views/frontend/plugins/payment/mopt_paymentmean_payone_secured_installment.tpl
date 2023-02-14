{namespace name='frontend/MoptPaymentPayone/payment'}

{if $payment_mean.id == $form_data.payment}
    {assign var="moptRequired" value=1}
{else}
    {assign var="moptRequired" value=0}
{/if}

{if $payment_mean.id == $form_data.payment}
    <div class="payment--form-group">
            <input type="text" hidden
                   name="moptPaymentData[mopt_payone__payone_secured_installment_token]"
                   id="mopt_payone__payone_secured_installment_token"
                   value=""
            >
        {if ! $sUserData.billingaddress.company}
            <p class ="none">
                <label for="mopt_payone__payone_secured_installment_birthday">
                    {s name='birthdate'}Geburtsdatum{/s}
                </label>
            </p>

            <div class="select-field">
                <select name="moptPaymentData[mopt_payone__payone_secured_installment_birthday]"
                        id="mopt_payone__payone_secured_installment_birthday" onchange="payoneSecuredInstallmentDobInput()"
                        {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                        class="is--required{if $error_flags.mopt_payone__payone_secured_installment_birthday} has--error{/if}">
                    <option value="">--</option>
                    {section name="birthdate" start=1 loop=32 step=1}
                        <option value="{if $smarty.section.birthdate.index < 10}0{/if}{$smarty.section.birthdate.index}"
                                {if $smarty.section.birthdate.index eq $moptCreditCardCheckEnvironment.mopt_payone__payone_secured_installment_birthday}
                        selected
                                {/if}>
                            {$smarty.section.birthdate.index}</option>
                    {/section}
                </select>
            </div>

            <div class="select-field">
                <select name="moptPaymentData[mopt_payone__payone_secured_installment_birthmonth]"
                        id="mopt_payone__payone_secured_installment_birthmonth" onchange="payoneSecuredInstallmentDobInput()"
                        {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                        class="is--required {if $error_flags.mopt_payone__payone_secured_installment_birthmonth} has--error{/if}">
                    <option value="">--</option>
                    {section name="birthmonth" start=1 loop=13 step=1}
                        <option value="{if $smarty.section.birthmonth.index < 10}0{/if}{$smarty.section.birthmonth.index}"
                                {if $smarty.section.birthmonth.index eq $moptCreditCardCheckEnvironment.mopt_payone__payone_secured_installment_birthmonth}
                        selected
                                {/if}>
                            {if $smarty.section.birthmonth.index < 10}0{/if}{$smarty.section.birthmonth.index}</option>
                    {/section}
                </select>
            </div>

            <div class="select-field">
                <select name="moptPaymentData[mopt_payone__payone_secured_installment_birthyear]"
                        id="mopt_payone__payone_secured_installment_birthyear" onchange="payoneSecuredInstallmentDobInput()"
                        {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                        class="select--country is--required{if $error_flags.mopt_payone__payone_secured_installment_birthyear} register--error-msg {/if}">
                    <option value="">----</option>
                    {section name="birthyear" loop=2016 max=100 step=-1}
                        <option value="{$smarty.section.birthyear.index}"
                                {if $smarty.section.birthyear.index eq $moptCreditCardCheckEnvironment.mopt_payone__payone_secured_installment_birthyear}
                        selected
                                {/if}>
                            {$smarty.section.birthyear.index}</option>
                    {/section}
                </select>
            </div>
        {/if}
            <br>
            <input name="moptPaymentData[mopt_payone__payone_secured_installment_iban]"
                   type="text"
                   id="mopt_payone__payone_secured_installment_iban"
                   {if $moptRequired}required="required" aria-required="true"{/if}
                   placeholder="{s name='bankIBAN'}IBAN{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                   value="{$form_data.mopt_payone__payone_secured_installment_iban|escape}"
                   data-moptIbanErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}"
                   class="payment--field is--required{if $error_flags.mopt_payone__payone_secured_installment_iban} has--error{/if} moptPayoneIbanBic" />
        <input id="mopt_payone__payone_secured_installment_birthdaydate" class="is--hidden validate-18-years" type="text" name="moptPaymentData[mopt_payone__payone_secured_installment_birthdaydate]" value="{$moptCreditCardCheckEnvironment.birthday}"/>
        <div id="payone-secured--installment-hint-18-years" class="is--hidden">{s name='birthdayUnderageError'}Sie müssen mindestens 18 Jahre alt sein, um diese Zahlart verwenden zu können.{/s}</div>
        <br>
        <input name="moptPaymentData[mopt_payone__payone_secured_installment_telephone]"
               type="text"
               id="mopt_payone__payone_secured_installment_telephone"
               {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
               placeholder="{s name='telephoneNumber'}Telefonnummer{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
               value="{$moptCreditCardCheckEnvironment.mopt_payone__payone_secured_installment_telephone|escape}"
               class="payment--field is--required{if $error_flags.mopt_payone__payone_secured_installment_telephone} has--error{/if}"
        />
    {if $sUserData.billingaddress.company}
        <input class="is--hidden" type="text" name="moptPaymentData[mopt_payone__secured_installment_b2bmode]" id="mopt_payone__secured_installment_b2bmode" value="1">
    {/if}
    <br>
    <div class="form-group">
        <label class="req control-label col-lg-3"><b>{s name='NumberOfInstallments'}Wählen Sie die Anzahl der Raten{/s}</b></label>
        <p></p>
        <div class="col-lg-9">
            {foreach from=$BSPayoneInstallmentPlan.plans key=index item=plan}
            <div>
                <input id="bnplPlan_{$index}" required="required" type="radio" name="moptPaymentData[mopt_payone__payone_secured_installment_plan]" value="{$plan.installmentOptionId}" onclick="fcpoSelectBNPLInstallmentPlan({$index})"/>
                    {$plan.monthlyAmountValue} {$plan.monthlyAmountCurrency} {s name='MonthlyInstallment'}MonthlyInstallment{/s} - {s name='NoOfInstallments'}Anzahl der Raten{/s}: {$plan.numberOfPayments}
            </div>
            {/foreach}
        </div>
    </div>

    <div class="form-group">
        <div class="col-lg-3"></div>
        <div class="col-lg-9">
            {foreach from=$BSPayoneInstallmentPlan.plans key=index item=plan}
            <div id="bnpl_installment_overview_{$index}" class="bnpl_installment_overview" style="display: none">
                <strong>{s name='bnplSecinstallmentOvwTitle'}Übersicht{/s}</strong>
                <br />
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-3">{s name='bnplSecinstallmentOvwNbrates'}Ratenanzahl{/s}:</div>
                        <div class="col-lg-4 fcpopl-secinstallment-table-value">{$plan.numberOfPayments}</div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3">{s name='bnplSecinstallmentOvwTotalfinancing'}Finanzierungsbetrag{/s}:</div>
                        <div class="col-lg-4 fcpopl-secinstallment-table-value"><b>{$sAmount|number_format:2:",":"."}</b> {$purchaseCurrency}</div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3">{s name='bnplSecinstallmentOvwTotalamount'}Gesamt{/s}:</div>
                        <div class="col-lg-4 fcpopl-secinstallment-table-value">{$plan.totalAmountValue} {$plan.totalAmountCurrency}</div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3">{s name='bnplSecinstallmentOvwInterest'}Zinssatz{/s}:</div>
                        <div class="col-lg-4 fcpopl-secinstallment-table-value">{$plan.nominalInterestRate}%</div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3">{s name='bnplSecinstallmentOvwEffectiveinterest'}Effektivzinssatz{/s}:</div>
                        <div class="col-lg-4 fcpopl-secinstallment-table-value">{$plan.effectiveInterestRate}%</div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3">{s name='bnplSecinstallmentOvwMonthlyrate'}Monatliche Rate{/s}:</div>
                        <div class="col-lg-4 fcpopl-secinstallment-table-value">{$plan.monthlyAmountValue} {$plan.monthlyAmountCurrency}</div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <br />
                            <a target="_blank" href="{$plan.linkCreditInformationHref}">{s name='bnplSecinstallmentOvwDlCredinfo'}&gt;&nbsp;Ratenkauf Mustervertrag herunterladen{/s}</a>
                        </div>
                    </div>
                </div>
            </div>
            {/foreach}
        </div>
    </div>

    <div class="alert alert-info col-lg-offset-3 desc">
        <p>{s name='bnplDataprotectionnotice'}Mit Abschluss dieser Bestellung erkläre ich mich mit den ergänzenden <a target="_blank" href="https://legal.paylater.payone.com/de/terms-of-payment.html">Zahlungsbedingungen</a> und der Durchführung einer Risikoprüfung für die ausgewählte Zahlungsart einverstanden. Den ergänzenden <a target="_blank" href="https://legal.paylater.payone.com/de/data-protection-payments.html">Datenschutzhinweis</a> habe ich zur Kenntnis genommen.{/s}</p>
    </div>

    <script type="text/javascript"
            src="{link file='frontend/_resources/javascript/mopt_payone_secured_installment.js'}">
    </script>

    <script type="text/javascript">

        function payoneSecuredInstallmentDobInput()
        {
            var daySelect = document.getElementById("mopt_payone__payone_secured_installment_birthday");
            var monthSelect = document.getElementById("mopt_payone__payone_secured_installment_birthmonth");
            var yearSelect = document.getElementById('mopt_payone__payone_secured_installment_birthyear');
            var hiddenDobFull = document.getElementById("mopt_payone__payone_secured_installment_birthdaydate");
            var hiddenDobHint = document.getElementById("payone-secured--installment-hint-18-years");

            if (daySelect.value == "" || monthSelect.value == "" || yearSelect.value == ""
                || daySelect == undefined) {
                return;
            }
            hiddenDobFull.value = yearSelect.value + "-" + monthSelect.value + "-" + daySelect.value;
            // console.log("HiddenDob:");
            // console.log(hiddenDobFull.value);
            var oBirthDate = new Date(hiddenDobFull.value);
            var oMinDate = new Date(new Date().setYear(new Date().getFullYear() - 18));
            if (oBirthDate > oMinDate) {
                hiddenDobHint.className = "register--error-msg";
            } else {
                hiddenDobHint.className = "is--hidden";
                return;
            }
        }
    </script>

    <script id="paylaDcs" type="text/javascript" src="https://d.payla.io/dcs/{$BSPayonePaylaPartnerId}/{$BSPayoneMerchantId}/dcs.js"></script>
    <script>
        function check_script_loaded(glob_var) {
            if(typeof(glob_var) !== 'undefined') {
                // console.log('PaylaDCS is ready');
                if (typeof paylaDcs.init !== 'function') {
                    // console.log('PaylaDCS.init not (yet) accessible in object, retrying in 100ms');
                    setTimeout(function() {
                        check_script_loaded(glob_var)
                    }, 100)
                } else {
                    var paylaDcsT = paylaDcs.init("{$BSPayoneSecuredMode}", "{$BSPayoneSecuredToken}");
                    // console.log(paylaDcsT);
                    tokenElem = document.getElementById('mopt_payone__payone_secured_installment_token');
                    tokenElem.setAttribute('value', paylaDcsT)
                }
            } else {
                // console.log('PaylaDCS is not ready retrying in 100ms');
                setTimeout(function() {
                    check_script_loaded(glob_var)
                }, 100)
            }
        }
        check_script_loaded('paylaDcs');
    </script>
    <link id="paylaDcsCss" type="text/css" rel="stylesheet" href="https://d.payla.io/dcs/dcs.css?st={$BSPayoneSecuredToken}&pi={$BSPayonePaylaPartnerId}&psi={$BSPayoneMerchantId}&e={$BSPayoneSecuredMode}">
{/if}