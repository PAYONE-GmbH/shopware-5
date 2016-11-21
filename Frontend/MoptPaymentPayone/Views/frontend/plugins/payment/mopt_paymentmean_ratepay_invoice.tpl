{namespace name='frontend/MoptPaymentPayone/payment'}

<div class="payment--form-group">
    <p class ="none">
        <label for="mopt_payone__ratepay_invoice_birthday">
            {s name='birthdate'}Geburtsdatum{/s}
        </label>
    </p>

    <select name="moptPaymentData[mopt_payone__ratepay_invoice_birthday]" 
            id="mopt_payone__ratepay_invoice_birthday" onchange="ratepayInvoiceDobInput()" 
            {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
            class="is--required{if $error_flags.mopt_payone__ratepay_invoice_birthday} has--error{/if}">
        <option value="">--</option>
        {section name="birthdate" start=1 loop=32 step=1}
            <option value="{if $smarty.section.birthdate.index < 10}0{/if}{$smarty.section.birthdate.index}" 
                    {if $smarty.section.birthdate.index eq $moptCreditCardCheckEnvironment.mopt_payone__ratepay_invoice_birthday}
                        selected
                    {/if}>
                {$smarty.section.birthdate.index}</option>
            {/section}
    </select>

    <select name="moptPaymentData[mopt_payone__ratepay_invoice_birthmonth]" 
            id="mopt_payone__ratepay_invoice_birthmonth" onchange="ratepayInvoiceDobInput()" 
            {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
            class="is--required {if $error_flags.mopt_payone__ratepay_invoice_birthmonth} has--error{/if}">
        <option value="">--</option>
        {section name="birthmonth" start=1 loop=13 step=1}
            <option value="{if $smarty.section.birthmonth.index < 10}0{/if}{$smarty.section.birthmonth.index}" 
                    {if $smarty.section.birthmonth.index eq $moptCreditCardCheckEnvironment.mopt_payone__ratepay_invoice_birthmonth}
                        selected
                    {/if}>
                {if $smarty.section.birthmonth.index < 10}0{/if}{$smarty.section.birthmonth.index}</option>
            {/section}
    </select>

    <select name="moptPaymentData[mopt_payone__ratepay_invoice_birthyear]" 
            id="mopt_payone__ratepay_invoice_birthyear" onchange="ratepayInvoiceDobInput()" 
            {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
            class="select--country is--required{if $error_flags.mopt_payone__ratepay_invoice_birthyear} register--error-msg {/if}">
        <option value="">----</option>
        {section name="birthyear" loop=2016 max=100 step=-1}
            <option value="{$smarty.section.birthyear.index}" 
                    {if $smarty.section.birthyear.index eq $moptCreditCardCheckEnvironment.mopt_payone__ratepay_invoice_birthyear}
                        selected
                    {/if}>
                {$smarty.section.birthyear.index}</option>
            {/section}
    </select>

    <input class="is--hidden validate-18-years" type="text" name="moptPaymentData[mopt_payone__ratepay_invoice_birthdaydate]" id="mopt_payone__ratepay_invoice_birthdaydate" value="{$moptCreditCardCheckEnvironment.birthday}">   
    <div id="ratepay-invoice-hint-18-years" class="is--hidden">Sie müssen mindestens 18 Jahre alt sein, um diese Zahlart verwenden zu können.</div>              

    <input class="is--hidden" type="text" name="moptPaymentData[mopt_payone__ratepay_shopid]" value="{$moptRatepayConfig.shopid}"/>    
    <input class="is--hidden" type="text" name="moptPaymentData[mopt_payone__ratepay_device_fingerprint]" value="{$moptRatepayConfig.deviceFingerPrint}"/>    

    <input name="moptPaymentData[mopt_payone__ratepay_invoice_telephone]"
           type="text"
           id="mopt_payone__ratepay_invoice_telephone"
           {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
           placeholder="{s name='telephoneNumber'}Telefonnummer{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
           value="{$moptCreditCardCheckEnvironment.mopt_payone__ratepay_invoice_telephone|escape}"
           class="payment--field is--required{if $error_flags.mopt_payone__ratepay_invoice_telephone} has--error{/if}" 
           />  
    {if $sUserData.billingaddress.company}
        <input type="text" name="moptPaymentData[mopt_payone__ratepay_company_trade_registry_number]" 
               id="mopt_payone__ratepay_company_trade_registry_number" 
               {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
               placeholder="{s name='companyTradeRegistryNumber'}Handelsregisternummer*{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"                
               class="is--required{if $error_flags.mopt_payone__ratepay_company_trade_registry_number} has--error{/if}">
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

    function ratepayInvoiceDobInput()
    {
        var daySelect = document.getElementById("mopt_payone__ratepay_invoice_birthday");
        var monthSelect = document.getElementById("mopt_payone__ratepay_invoice_birthmonth");
        var yearSelect = document.getElementById('mopt_payone__ratepay_invoice_birthyear');
        var hiddenDobFull = document.getElementById("mopt_payone__ratepay_invoice_birthdaydate");
        var hiddenDobHint = document.getElementById("ratepay-invoice-hint-18-years");

        console.log("Day:");
        console.log(daySelect.value);
        console.log("Month:");
        console.log(monthSelect.value);
        console.log("Year:");
        console.log(yearSelect.value);
        console.log("HiddenDob:");
        console.log(hiddenDobFull.value);

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
