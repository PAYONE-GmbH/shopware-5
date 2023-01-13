{namespace name='frontend/MoptPaymentPayone/payment'}

{if $payment_mean.id == $form_data.payment}
    {assign var="moptRequired" value=1}
{else}
    {assign var="moptRequired" value=0}
{/if}

{if $payment_mean.id == $form_data.payment}

<div class="payment--form-group">
        <input type="text" hidden
               name="moptPaymentData[mopt_payone__payone_secured_invoice_token]"
               id="mopt_payone__payone_secured_invoice_token"
               value=""
        >
    <div id="mopt_payone__payone_secured_invoice_abg">
        <p>{s name='payoneSecuredInvoiceLegalText'}Mit Abschluss dieser Bestellung erkläre ich mich mit den ergänzenden <a target="_blank" href="https://legal.paylater.payone.com/de/terms-of-use.html">Zahlungsbedingungen</a> und der Durchführung einer Risikoprüfung für die ausgewählte Zahlungsart einverstanden. Den ergänzenden <a target="_blank" href="https://legal.paylater.payone.com/en/data-protection.html">Datenschutzhinweis</a> habe ich zur Kenntnis genommen.{/s}</p>
    </div>

    {if ! $sUserData.billingaddress.company}
        <p class ="none">
            <label for="mopt_payone__payone_secured_invoice_birthday">
                {s name='birthdate'}Geburtsdatum{/s}
            </label>
        </p>

    <div class="select-field">
        <select name="moptPaymentData[mopt_payone__payone_secured_invoice_birthday]"
                id="mopt_payone__payone_secured_invoice_birthday" onchange="payoneSecuredInvoiceDobInput()"
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="is--required{if $error_flags.mopt_payone__payone_secured_invoice_birthday} has--error{/if}">
            <option value="">--</option>
            {section name="birthdate" start=1 loop=32 step=1}
                <option value="{if $smarty.section.birthdate.index < 10}0{/if}{$smarty.section.birthdate.index}"
                        {if $smarty.section.birthdate.index eq $moptCreditCardCheckEnvironment.mopt_payone__payone_secured_invoice_birthday}
                            selected
                        {/if}>
                    {$smarty.section.birthdate.index}</option>
            {/section}
        </select>
    </div>

    <div class="select-field">
        <select name="moptPaymentData[mopt_payone__payone_secured_invoice_birthmonth]"
            id="mopt_payone__payone_secured_invoice_birthmonth" onchange="payoneSecuredInvoiceDobInput()"
            {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
            class="is--required {if $error_flags.mopt_payone__payone_secured_invoice_birthmonth} has--error{/if}">
            <option value="">--</option>
            {section name="birthmonth" start=1 loop=13 step=1}
                <option value="{if $smarty.section.birthmonth.index < 10}0{/if}{$smarty.section.birthmonth.index}"
                    {if $smarty.section.birthmonth.index eq $moptCreditCardCheckEnvironment.mopt_payone__payone_secured_invoice_birthmonth}
                        selected
                    {/if}>
                    {if $smarty.section.birthmonth.index < 10}0{/if}{$smarty.section.birthmonth.index}</option>
            {/section}
        </select>

    </div>

    <div class="select-field">
        <select name="moptPaymentData[mopt_payone__payone_secured_invoice_birthyear]"
                id="mopt_payone__payone_secured_invoice_birthyear" onchange="payoneSecuredInvoiceDobInput()"
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="is--required{if $error_flags.mopt_payone__payone_secured_invoice_birthyear} has--error{/if}">
            <option value="">----</option>
            {section name="birthyear" loop=2016 max=100 step=-1}
                <option value="{$smarty.section.birthyear.index}"
                        {if $smarty.section.birthyear.index eq $moptCreditCardCheckEnvironment.mopt_payone__payone_secured_invoice_birthyear}
                selected
                        {/if}>
                    {$smarty.section.birthyear.index}</option>
            {/section}
        </select>
    </div>
    {/if}
    <input class="is--hidden validate-18-years" type="text" name="moptPaymentData[mopt_payone__payone_secured_invoice_birthdaydate]" id="mopt_payone__payone_secured_invoice_birthdaydate" value="{$moptCreditCardCheckEnvironment.birthday}">
    <div id="payone-secured-invoice-hint-18-years" class="is--hidden">{s name='eighteenYearsHint'}Sie müssen mindestens 18 Jahre alt sein, um diese Zahlart verwenden zu können.{/s}</div>

    <input name="moptPaymentData[mopt_payone__payone_secured_invoice_telephone]"
           type="text"
           id="mopt_payone__payone_secured_invoice_telephone"
           {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
           placeholder="{s name='telephoneNumber'}Telefonnummer{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
           value="{$moptCreditCardCheckEnvironment.mopt_payone__payone_secured_invoice_telephone|escape}"
           class="payment--field is--required{if $error_flags.mopt_payone__payone_secured_invoice_telephone} has--error{/if}"
    />

    {if $sUserData.billingaddress.company}
        <input class="is--hidden" type="text" name="moptPaymentData[mopt_payone__secured_invoice_b2bmode]" id="mopt_payone__secured_invoice_b2bmode" value="1">
    {/if}
</div>
<script type="text/javascript">

    function payoneSecuredInvoiceDobInput()
    {
        var daySelect = document.getElementById("mopt_payone__payone_secured_invoice_birthday");
        var monthSelect = document.getElementById("mopt_payone__payone_secured_invoice_birthmonth");
        var yearSelect = document.getElementById('mopt_payone__payone_secured_invoice_birthyear');
        var hiddenDobFull = document.getElementById("mopt_payone__payone_secured_invoice_birthdaydate");
        var hiddenDobHint = document.getElementById("payone-secured-invoice-hint-18-years");

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
    var paylaDcsT = paylaDcs.init("{$BSPayoneSecuredMode}", "{$BSPayoneSecuredToken}");
    console.log(paylaDcsT);
    tokenElem = document.getElementById('mopt_payone__payone_secured_invoice_token');
    tokenElem.setAttribute('value',paylaDcsT)
</script>
<link id="paylaDcsCss" type="text/css" rel="stylesheet" href="https://d.payla.io/dcs/dcs.css?st={$BSPayoneSecuredToken}&pi={$BSPayonePaylaPartnerId}&psi={$BSPayoneMerchantId}&e={$BSPayoneSecuredMode}">
{/if}