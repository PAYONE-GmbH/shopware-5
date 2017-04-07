{namespace name='frontend/MoptPaymentPayone/payment'}

<div class="payment--form-group">
    {if ! $sUserData.billingaddress.company}
        <p class ="none">
            <label for="mopt_payone__ratepay_direct_debit_birthday">
                {s name='birthdate'}Geburtsdatum{/s}
            </label>
        </p>
        <select name="moptPaymentData[mopt_payone__ratepay_direct_debit_birthday]"
                id="mopt_payone__ratepay_direct_debit_birthday" onchange="ratepayDirectDebitDobInput()"
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="is--required{if $error_flags.mopt_payone__ratepay_direct_debit_birthday} has--error{/if}">
            <option value="">--</option>
            {section name="birthdate" start=1 loop=32 step=1}
                <option value="{if $smarty.section.birthdate.index < 10}0{/if}{$smarty.section.birthdate.index}"
                        {if $smarty.section.birthdate.index eq $moptCreditCardCheckEnvironment.mopt_payone__ratepay_direct_debit_birthday}
                            selected
                        {/if}>
                    {$smarty.section.birthdate.index}</option>
            {/section}
        </select>

        <select name="moptPaymentData[mopt_payone__ratepay_direct_debit_birthmonth]"
            id="mopt_payone__ratepay_direct_debit_birthmonth" onchange="ratepayDirectDebitDobInput()"
            {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
            class="is--required {if $error_flags.mopt_payone__ratepay_direct_debit_birthmonth} has--error{/if}">
            <option value="">--</option>
            {section name="birthmonth" start=1 loop=13 step=1}
                <option value="{if $smarty.section.birthmonth.index < 10}0{/if}{$smarty.section.birthmonth.index}"
                    {if $smarty.section.birthmonth.index eq $moptCreditCardCheckEnvironment.mopt_payone__ratepay_direct_debit_birthmonth}
                        selected
                    {/if}>
                    {if $smarty.section.birthmonth.index < 10}0{/if}{$smarty.section.birthmonth.index}</option>
            {/section}
        </select>

        <select name="moptPaymentData[mopt_payone__ratepay_direct_debit_birthyear]"
                id="mopt_payone__ratepay_direct_debit_birthyear" onchange="ratepayDirectDebitDobInput()"
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="select--country is--required{if $error_flags.mopt_payone__ratepay_direct_debit_birthyear} register--error-msg {/if}">
            <option value="">----</option>
            {section name="birthyear" loop=2016 max=100 step=-1}
                <option value="{$smarty.section.birthyear.index}"
                        {if $smarty.section.birthyear.index eq $moptCreditCardCheckEnvironment.mopt_payone__ratepay_direct_debit_birthyear}
                selected
                        {/if}>
                    {$smarty.section.birthyear.index}</option>
            {/section}
        </select>
    {/if}
    <input class="is--hidden validate-18-years" type="text" name="moptPaymentData[mopt_payone__ratepay_direct_debit_birthdaydate]" id="mopt_payone__ratepay_direct_debit_birthdaydate" value="{$moptCreditCardCheckEnvironment.birthday}">
    <div id="ratepay-direct_debit-hint-18-years" class="is--hidden">Sie müssen mindestens 18 Jahre alt sein, um diese Zahlart verwenden zu können.</div>

    <!--
    <input name="moptPaymentData[mopt_payone__ratepay_direct_debit_bankaccountholder]"
           type="text"
           id="mopt_payone__ratepay_direct_debit_bankaccountholder"
           required="required" aria-required="true"
           placeholder="{s name='bankAccoutHolder'}Kontoinhaber{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
           value="{$form_data.mopt_payone__debit_bankaccountholder|escape}"
           class="payment--field is--required{if $error_flags.mopt_payone__ratepay_direct_debit_bankaccountholder} has--error{/if}" />
-->
    <input name="moptPaymentData[mopt_payone__ratepay_direct_debit_iban]"
           type="text"
           id="mopt_payone__ratepay_direct_debit_iban" aria-required="true"
           placeholder="{s name='bankIBAN'}IBAN{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
           value="{$form_data.mopt_payone__ratepay_direct_debit_iban|escape}"
           data-moptIbanErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}"
           class="payment--field is--required{if $error_flags.mopt_payone__ratepay_direct_debit_iban} has--error{/if} moptPayoneIbanBic" />

    <input name="moptPaymentData[mopt_payone__ratepay_direct_debit_bic]"
           type="text"
           id="mopt_payone__ratepay_direct_debit_bic" aria-required="true"
           placeholder="{s name='bankBIC'}BIC{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
           value="{$form_data.mopt_payone__ratepay_direct_debit_bic|escape}"
           data-moptIbanErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}"
           class="payment--field is--required{if $error_flags.mopt_payone__ratepay_direct_debit_bic} has--error{/if} moptPayoneIbanBic" />

    <input class="is--hidden" type="text" name="moptPaymentData[mopt_payone__ratepay_direct_debit_shopid]" value="{$moptRatepayConfig.shopid}"/>
    <input class="is--hidden" type="text" name="moptPaymentData[mopt_payone__ratepay_direct_debit_device_fingerprint]" value="{$moptRatepayConfig.deviceFingerPrint}"/>

    <input name="moptPaymentData[mopt_payone__ratepay_direct_debit_telephone]"
           type="text"
           id="mopt_payone__ratepay_direct_debit_telephone"
           {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
           placeholder="{s name='telephoneNumber'}Telefonnummer{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
           value="{$moptCreditCardCheckEnvironment.mopt_payone__ratepay_direct_debit_telephone|escape}"
           class="payment--field is--required{if $error_flags.mopt_payone__ratepay_direct_debit_telephone} has--error{/if}"
    />
    {if $sUserData.billingaddress.company}
        <input type="text" name="moptPaymentData[mopt_payone__ratepay_direct_debit_company_trade_registry_number]"
               id="mopt_payone__ratepay_direct_debit_company_trade_registry_number"
               {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
               placeholder="{s name='companyTradeRegistryNumber'}Handelsregisternummer*{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
               class="is--required{if $error_flags.mopt_payone__ratepay_direct_debit_company_trade_registry_number} has--error{/if}">

        <input class="is--hidden" type="text" name="moptPaymentData[mopt_payone__ratepay_b2bmode]" id="mopt_payone__ratepay_b2bmode" value="1">

    {/if}
</div>
{if $moptRatepayConfig.deviceFingerPrint && $moptRatepayConfig.deviceFingerprintSnippetId}
    <!-- Only Include if moptRatepayConfig is configured in Backend to prevent 404 erorrs -->
    <script language="JavaScript">
        var di = { t: '{$moptRatepayConfig.deviceFingerPrint}', v: '{ $moptRatepayConfig.deviceFingerprintSnippetId}', l: 'Checkout'};
    </script>
    <script type="text/javascript"
            src="//d.ratepay.com/{$moptRatepayConfig.deviceFingerprintSnippetId}/di.js"></script>
    <noscript><link rel="stylesheet" type="text/css"
                    href="//d.ratepay.com/di.css?t={$moptRatepayConfig.deviceFingerPrint}&v={$moptRatepayConfig.deviceFingerprintSnippetId}&l=Check
                    out"></noscript>
    <object type="application/x-shockwave-flash"
            data="//d.ratepay.com/{$moptRatepayConfig.deviceFingerprintSnippetId}/c.swf" width="0" height="0">
        <param name="movie" value="//d.ratepay.com/{$moptRatepayConfig.deviceFingerprintSnippetId}/c.swf" />
        <param name="flashvars"
               value="t={$moptRatepayConfig.deviceFingerPrint}&v={$moptRatepayConfig.deviceFingerprintSnippetId}"/><param
                name="AllowScriptAccess" value="always"/>
    </object>
{/if}
<script type="text/javascript">

    function ratepayDirectDebitDobInput()
    {
        var daySelect = document.getElementById("mopt_payone__ratepay_direct_debit_birthday");
        var monthSelect = document.getElementById("mopt_payone__ratepay_direct_debit_birthmonth");
        var yearSelect = document.getElementById('mopt_payone__ratepay_direct_debit_birthyear');
        var hiddenDobFull = document.getElementById("mopt_payone__ratepay_direct_debit_birthdaydate");
        var hiddenDobHint = document.getElementById("ratepay-direct_debit-hint-18-years");

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