{namespace name='frontend/MoptPaymentPayone/payment'}

{if $payment_mean.id == $form_data.payment}
    {assign var="moptRequired" value=1}
{else}
    {assign var="moptRequired" value=0}
{/if}

{if $payment_mean.id == $form_data.payment}

<div class="payment--form-group">
        <input type="text" hidden
               name="moptPaymentData[mopt_payone__payone_secured_directdebit_token]"
               id="mopt_payone__payone_secured_directdebit_token"
               value=""
        >
    <div id="mopt_payone__payone_secured_directdebit_abg">
        <p>{s name='payoneSecuredDirectdebitLegalText'}Mit Abschluss dieser Bestellung erkläre ich mich mit den ergänzenden <a target="_blank" href="https://legal.paylater.payone.com/de/terms-of-payment.html">Zahlungsbedingungen</a> und der Durchführung einer Risikoprüfung für die ausgewählte Zahlungsart einverstanden. Den ergänzenden <a target="_blank" href="https://legal.paylater.payone.com/de/data-protection-payments.html">Datenschutzhinweis</a> habe ich zur Kenntnis genommen.{/s}</p>
    </div>

    {if ! $sUserData.billingaddress.company}
        <p class ="none">
            <label for="mopt_payone__payone_secured_directdebit_birthday">
                {s name='birthdate'}Geburtsdatum{/s}
            </label>
        </p>

    <div class="select-field">
        <select name="moptPaymentData[mopt_payone__payone_secured_directdebit_birthday]"
                id="mopt_payone__payone_secured_directdebit_birthday" onchange="payoneSecuredDirectdebitDobInput()"
                aria-label="{s name='birthdate'}Geburtsdatum{/s}"
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="is--required{if $error_flags.mopt_payone__payone_secured_directdebit_birthday} has--error{/if}">
            <option value="">--</option>
            {section name="birthdate" start=1 loop=32 step=1}
                <option value="{if $smarty.section.birthdate.index < 10}0{/if}{$smarty.section.birthdate.index}"
                        {if $smarty.section.birthdate.index eq $moptCreditCardCheckEnvironment.mopt_payone__payone_secured_directdebit_birthday}
                            selected
                        {/if}>
                    {$smarty.section.birthdate.index}</option>
            {/section}
        </select>
    </div>

    <div class="select-field">
        <select name="moptPaymentData[mopt_payone__payone_secured_directdebit_birthmonth]"
            id="mopt_payone__payone_secured_directdebit_birthmonth" onchange="payoneSecuredDirectdebitDobInput()"
            aria-label="{s name='birthdate'}Geburtsdatum{/s}"
            {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
            class="is--required {if $error_flags.mopt_payone__payone_secured_directdebit_birthmonth} has--error{/if}">
            <option value="">--</option>
            {section name="birthmonth" start=1 loop=13 step=1}
                <option value="{if $smarty.section.birthmonth.index < 10}0{/if}{$smarty.section.birthmonth.index}"
                    {if $smarty.section.birthmonth.index eq $moptCreditCardCheckEnvironment.mopt_payone__payone_secured_directdebit_birthmonth}
                        selected
                    {/if}>
                    {if $smarty.section.birthmonth.index < 10}0{/if}{$smarty.section.birthmonth.index}</option>
            {/section}
        </select>

    </div>

    <div class="select-field">
        <select name="moptPaymentData[mopt_payone__payone_secured_directdebit_birthyear]"
                id="mopt_payone__payone_secured_directdebit_birthyear" onchange="payoneSecuredDirectdebitDobInput()"
                aria-label="{s name='birthdate'}Geburtsdatum{/s}"
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="is--required{if $error_flags.mopt_payone__payone_secured_directdebit_birthyear} has--error{/if}">
            <option value="">----</option>
            {section name="birthyear" loop=2016 max=100 step=-1}
                <option value="{$smarty.section.birthyear.index}"
                        {if $smarty.section.birthyear.index eq $moptCreditCardCheckEnvironment.mopt_payone__payone_secured_directdebit_birthyear}
                selected
                        {/if}>
                    {$smarty.section.birthyear.index}</option>
            {/section}
        </select>
    </div>
    {/if}

    <br>
    <input name="moptPaymentData[mopt_payone__payone_secured_directdebit_iban]"
           type="text"
           id="mopt_payone__payone_secured_directdebit_iban"
           {if $moptRequired}required="required" aria-required="true"{/if}
           placeholder="{s name='bankIBAN'}IBAN{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
           value="{$form_data.mopt_payone__payone_secured_directdebit_iban|escape}"
           data-moptIbanWrongCharacterMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="moptIbanWrongCharacterMessage"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}"
           data-moptIbanWrongLengthMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="moptIbanWrongLengthMessage"}Bitte prüfen Sie die Länge der IBAN{/s}"
           data-moptIbanWrongCecksumMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="moptIbanWrongCecksumMessage"}Die Prüfsumme der IBAN ist falsch{/s}"
           class="payment--field is--required{if $error_flags.mopt_payone__payone_secured_directdebit_iban} has--error{/if} moptPayoneIban" />

    <input class="is--hidden validate-18-years" type="text" name="moptPaymentData[mopt_payone__payone_secured_directdebit_birthdaydate]" id="mopt_payone__payone_secured_directdebit_birthdaydate" value="{$moptCreditCardCheckEnvironment.birthday}">
    <div id="payone-secured-directdebit-hint-18-years" class="is--hidden">{s name='eighteenYearsHint'}Sie müssen mindestens 18 Jahre alt sein, um diese Zahlart verwenden zu können.{/s}</div>

    <input name="moptPaymentData[mopt_payone__payone_secured_directdebit_telephone]"
           type="text"
           id="mopt_payone__payone_secured_directdebit_telephone"
           {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
           placeholder="{s name='telephoneNumber'}Telefonnummer{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
           value="{$moptCreditCardCheckEnvironment.mopt_payone__payone_secured_directdebit_telephone|escape}"
           class="payment--field is--required{if $error_flags.mopt_payone__payone_secured_directdebit_telephone} has--error{/if}"
    />

    {if $sUserData.billingaddress.company}
        <input class="is--hidden" type="text" name="moptPaymentData[mopt_payone__secured_invoice_b2bmode]" id="mopt_payone__secured_invoice_b2bmode" value="1">
    {/if}
</div>
<script type="text/javascript">

    function payoneSecuredDirectdebitDobInput()
    {
        var daySelect = document.getElementById("mopt_payone__payone_secured_directdebit_birthday");
        var monthSelect = document.getElementById("mopt_payone__payone_secured_directdebit_birthmonth");
        var yearSelect = document.getElementById('mopt_payone__payone_secured_directdebit_birthyear');
        var hiddenDobFull = document.getElementById("mopt_payone__payone_secured_directdebit_birthdaydate");
        var hiddenDobHint = document.getElementById("payone-secured-directdebit-hint-18-years");

        if (daySelect.value == "" || monthSelect.value == "" || yearSelect.value == ""
            || daySelect == undefined) {
            return;
        }
        hiddenDobFull.value = yearSelect.value + "-" + monthSelect.value + "-" + daySelect.value;
        console.log("HiddenDob:");
        console.log(hiddenDobFull.value);
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
                console.log('PaylaDCS is ready');
                if (typeof paylaDcs.init !== 'function') {
                    console.log('PaylaDCS.init not (yet) accessible in object, retrying in 100ms');
                    setTimeout(function() {
                        check_script_loaded(glob_var)
                    }, 100)
                } else {
                    var paylaDcsT = paylaDcs.init("{$BSPayoneSecuredMode}", "{$BSPayoneSecuredToken}");
                    console.log(paylaDcsT);
                    tokenElem = document.getElementById('mopt_payone__payone_secured_directdebit_token');
                    tokenElem.setAttribute('value', paylaDcsT)
                }
            } else {
                console.log('PaylaDCS is not ready retrying in 100ms');
                setTimeout(function() {
                    check_script_loaded(glob_var)
                }, 100)
            }
        }
        check_script_loaded('paylaDcs');
    </script>
<link id="paylaDcsCss" type="text/css" rel="stylesheet" href="https://d.payla.io/dcs/dcs.css?st={$BSPayoneSecuredToken}&pi={$BSPayonePaylaPartnerId}&psi={$BSPayoneMerchantId}&e={$BSPayoneSecuredMode}">
{/if}