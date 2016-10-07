{namespace name='frontend/MoptPaymentPayone/payment'}

<div class="payment--form-group">
    {if ($moptCreditCardCheckEnvironment.birthdayunderage === "1" || $fcPayolutionConfig.payolutionB2bmode == "0" && $moptCreditCardCheckEnvironment.birthday == "0000-00-00") || ( $fcPayolutionConfig.payolutionB2bmode == 1 && $moptCreditCardCheckEnvironment.birthday == "0000-00-00" && !$sUserData.billingaddress.company  ) }

        <p class ="none">
            <label for="mopt_payone__payolution_invoice_birthday">
                {s name='birthdate'}Geburtsdatum{/s}
            </label>
        </p>

        <select name="moptPaymentData[mopt_payone__payolution_invoice_birthday]" 
                id="mopt_payone__payolution_invoice_birthday" onchange="payolutionInvoiceDobInput()" 
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="is--required{if $error_flags.mopt_payone__payolution_invoice_birthday || $moptCreditCardCheckEnvironment.birthdayunderage === "1"} has--error{/if}">
            <option value="">--</option>
            {section name="birthdate" start=1 loop=32 step=1}
                <option value="{$smarty.section.birthdate.index}" 
                        {if $smarty.section.birthdate.index eq $moptCreditCardCheckEnvironment.mopt_payone__payolution_invoice_birthday}
                            selected
                        {/if}>
                    {$smarty.section.birthdate.index}</option>
                {/section}
        </select>

        <select name="moptPaymentData[mopt_payone__payolution_invoice_birthmonth]" 
                id="mopt_payone__payolution_invoice_birthmonth" onchange="payolutionInvoiceDobInput()" 
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="is--required {if $error_flags.mopt_payone__payolution_invoice_birthmonth || $moptCreditCardCheckEnvironment.birthdayunderage === "1"} has--error{/if}">
            <option value="">--</option>
            {section name="birthmonth" start=1 loop=13 step=1}
                <option value="{$smarty.section.birthmonth.index}" 
                        {if $smarty.section.birthmonth.index eq $moptCreditCardCheckEnvironment.mopt_payone__payolution_invoice_birthmonth}
                            selected
                        {/if}>
                    {$smarty.section.birthmonth.index}</option>
                {/section}
        </select>

        <select name="moptPaymentData[mopt_payone__payolution_invoice_birthyear]" 
                id="mopt_payone__payolution_invoice_birthyear" onchange="payolutionInvoiceDobInput()" 
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="select--country is--required{if $error_flags.mopt_payone__payolution_invoice_birthyear || $moptCreditCardCheckEnvironment.birthdayunderage === "1"} register--error-msg {/if}">
            <option value="">----</option>
            {section name="birthyear" loop=2016 max=100 step=-1}
                <option value="{$smarty.section.birthyear.index}" 
                        {if $smarty.section.birthyear.index eq $moptCreditCardCheckEnvironment.mopt_payone__payolution_invoice_birthyear}
                            selected
                        {/if}>
                    {$smarty.section.birthyear.index}</option>
                {/section}
        </select>
    {/if}  

    <input class="is--hidden validate-18-years" type="text" name="moptPaymentData[mopt_payone__payolution_invoice_birthdaydate]" id="mopt_payone__payolution_invoice_birthdaydate" value="{$moptCreditCardCheckEnvironment.birthday}">   
    <div id="invoice-hint-18-years" class="{if $moptCreditCardCheckEnvironment.birthdayunderage !== "1"}is--hidden{/if} register--error-msg">{s namespace='frontend/MoptPaymentPayone/errorMessages' name="birthdayUnderageError"} Sie müssen mindestens 18 Jahre alt sein, um diese Zahlart verwenden zu können. {/s}</div>        

    {if $fcPayolutionConfig.payolutionB2bmode && $sUserData.billingaddress.company}
        <input type="text" name="moptPaymentData[mopt_payone__invoice_company_trade_registry_number]" 
               id="mopt_payone__invoice_company_trade_registry_number" 
               {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
               placeholder="{s name='companyTradeRegistryNumber'}Handelsregisternummer*{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"                
               class="is--required{if $error_flags.mopt_payone__invoice_company_trade_registry_number} has--error{/if}">

        <input class="is--hidden" type="text" name="moptPaymentData[mopt_payone__payolution_b2bmode]" id="moptPaymentData[mopt_payone__payolution_b2bmode]" value="1">   
    {/if}         

    <p class="none clearfix">
        <input name="moptPaymentData[mopt_payone__payolution_invoice_agreement]" type="checkbox" id="mopt_payone__payolution_invoice_agreement" value="true"
               {if $form_data.mopt_payone__payolution_invoice_agreement eq "on"}
                   checked="checked"
               {/if}
               class="checkbox"/>
        <label class="{if $error_flags.mopt_payone__payolution_invoice_agreement} has--error{/if}" for="mopt_payone__payolution_invoice_agreement"  style="float:none; width:100%; display:inline">{$moptCreditCardCheckEnvironment.moptPayolutionInformation.consentInvoice}</label>
    </p>
    <div id="payolution_overlay_invoice" class="js--modal content" style="width:78%; height:90%; display: none; opacity: 0.9; margin: 75px auto;">
        <a href="#" onclick="removeOverlayInvoice();
                return false;" style="float:right;font-weight:bold;">Fenster schliessen</a><br><br>
        {$moptCreditCardCheckEnvironment.moptPayolutionInformation.overlaycontent}                    
    </div>    
    <div id="payolution_overlay_invoice_bg" class="js--overlay is--open" style="opacity: 0.8; display: none"></div>        
</div>

<script type="text/javascript">
    function displayOverlayInvoice() {
        document.getElementById('payolution_overlay_invoice').style.display = "block";
        document.getElementById('payolution_overlay_invoice_bg').style.display = "block";
    }
    function removeOverlayInvoice() {
        document.getElementById('payolution_overlay_invoice').style.display = "none";
        document.getElementById('payolution_overlay_invoice_bg').style.display = "none";
    }

    function payolutionInvoiceDobInput()
    {
        var daySelect = document.getElementById("mopt_payone__payolution_invoice_birthday");
        var monthSelect = document.getElementById("mopt_payone__payolution_invoice_birthmonth");
        var yearSelect = document.getElementById('mopt_payone__payolution_invoice_birthyear');
        var hiddenDobFull = document.getElementById("mopt_payone__payolution_invoice_birthdaydate");
        var hiddenDobHint = document.getElementById("invoice-hint-18-years");

        if (daySelect.value == "" || monthSelect.value == "" || yearSelect.value == ""
                || hiddenDobFull.value == "" || daySelect == undefined) {
            return;
        }
        hiddenDobFull.value = yearSelect.value + "-" + monthSelect.value + "-" + daySelect.value;
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
