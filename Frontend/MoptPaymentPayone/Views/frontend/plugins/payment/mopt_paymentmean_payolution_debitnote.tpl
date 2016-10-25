{namespace name='frontend/MoptPaymentPayone/payment'}

{if $payment_mean.id == $form_data.payment}
    {assign var="moptRequired" value=1}
{else}
    {assign var="moptRequired" value=0}
{/if}

<div class="payment--form-group">

    {if ($fcPayolutionConfig.payolutionB2bmode == "0" && $moptCreditCardCheckEnvironment.birthday == "0000-00-00") || ( $fcPayolutionConfig.payolutionB2bmode == 1 && $moptCreditCardCheckEnvironment.birthday == "0000-00-00" && !$sUserData.billingaddress.company  ) }
        <p class ="none">
            <label for="mopt_payone__payolution_debitnote_birthday">
                {s name='birthdate'}Geburtsdatum{/s}
            </label>
        </p>

        <select name="moptPaymentData[mopt_payone__payolution_debitnote_birthday]" 
                id="mopt_payone__payolution_debitnote_birthday" onchange="payolutionDebitNoteDobInput()"
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="payment--field {if $error_flags.mopt_payone__payolution_debitnote_birthday} has--error{/if}">
            <option value="">--</option>
            {section name="birthdate" start=1 loop=32 step=1}
                <option value="{if $smarty.section.birthdate.index < 10}0{/if}{$smarty.section.birthdate.index}" 
                        {if $smarty.section.birthdate.index eq $moptCreditCardCheckEnvironment.mopt_payone__payolution_debitnote_birthday}
                            selected
                        {/if}>
                    {if $smarty.section.birthdate.index < 10}0{/if}{$smarty.section.birthdate.index}</option>
                {/section}
        </select>

        <select name="moptPaymentData[mopt_payone__payolution_debitnote_birthmonth]" 
                id="mopt_payone__payolution_debitnote_birthmonth" onchange="payolutionDebitNoteDobInput()"
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="payment--field {if $error_flags.mopt_payone__payolution_debitnote_birthmonth} has--error{/if}">
            <option value="">--</option>
            {section name="birthmonth" start=1 loop=13 step=1}
                <option value="{if $smarty.section.birthmonth.index < 10}0{/if}{$smarty.section.birthmonth.index}" 
                        {if $smarty.section.birthmonth.index eq $moptCreditCardCheckEnvironment.mopt_payone__payolution_debitnote_birthmonth}
                            selected
                        {/if}>
                    {if $smarty.section.birthmonth.index < 10}0{/if}{$smarty.section.birthmonth.index}</option>
                {/section}
        </select>

        <select name="moptPaymentData[mopt_payone__payolution_debitnote_birthyear]" 
                id="mopt_payone__payolution_debitnote_birthyear" onchange="payolutionDebitNoteDobInput()"
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="payment--field {if $error_flags.mopt_payone__payolution_debitnote_birthyear} has--error{/if}">
            <option value="">----</option>
            {section name="birthyear" loop=2016 max=100 step=-1}
                <option value="{$smarty.section.birthyear.index}" 
                        {if $smarty.section.birthyear.index eq $moptCreditCardCheckEnvironment.mopt_payone__payolution_debitnote_birthyear}
                            selected
                        {/if}>
                    {$smarty.section.birthyear.index}</option>
                {/section}
        </select>        
    {/if}


    <input class="is--hidden validate-18-years" type="text" name="moptPaymentData[mopt_payone__payolution_debitnote_birthdaydate]" id="mopt_payone__payolution_debitnote_birthdaydate" value="{$moptCreditCardCheckEnvironment.birthday}">   
    <div id="debitnote-hint-18-years" class="is--hidden">Sie müssen mindestens 18 Jahre alt sein, um diese Zahlart verwenden zu können.</div>        

    {if $fcPayolutionConfig.payolutionB2bmode && $sUserData.billingaddress.company}
        <input type="text" name="moptPaymentData[mopt_payone__debitnote_company_trade_registry_number]" 
               id="mopt_payone__debitnote_company_trade_registry_number" 
               {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
               placeholder="{s name='companyTradeRegistryNumber'}Handelsregisternummer*{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"                
               class="payment--field is--required {if $error_flags.mopt_payone__debitnote_company_trade_registry_number} has--error{/if}" />
        <input class="is--hidden" type="text" name="moptPaymentData[mopt_payone__payolution_b2bmode]" id="moptPaymentData[mopt_payone__payolution_b2bmode]" value="1">   
    {/if}    

    <input name="moptPaymentData[mopt_payone__payolution_debitnote_iban]"
           type="text"
           id="mopt_payone__payolution_debitnote_iban"
           {if $moptRequired}required="required" aria-required="true"{/if}
           placeholder="{s name='bankIBAN'}IBAN{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
           value="{$form_data.mopt_payone__payolution_debitnote_iban|escape}" 
           data-moptIbanErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}"
           class="payment--field {if $moptRequired}is--required{/if}{if $error_flags.mopt_payone__payolution_debitnote_iban} has--error{/if} moptPayoneIbanBic" />

    <input name="moptPaymentData[mopt_payone__payolution_debitnote_bic]"
           type="text"
           id="mopt_payone__payolution_debitnote_bic"
           {if $moptRequired}required="required" aria-required="true"{/if}
           placeholder="{s name='bankBIC'}BIC{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
           value="{$form_data.mopt_payone__payolution_debitnote_bic|escape}" 
           data-moptIbanErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}"
           class="payment--field {if $moptRequired}is--required{/if}{if $error_flags.mopt_payone__payolution_debitnote_bic} has--error{/if} moptPayoneIbanBic" />        
    <p class="none clearfix">
        <input name="moptPaymentData[mopt_payone__payolution_debitnote_agreement]" type="checkbox" id="mopt_payone__payolution_debitnote_agreement" value="true"
               {if $form_data.mopt_payone__payolution_debitnote_agreement eq "on"}
                   checked="checked"
               {/if}
               class="checkbox"/>
        <label class="{if $error_flags.mopt_payone__payolution_debitnote_agreement} has--error {/if}" for="mopt_payone__payolution_debitnote_agreement"  style="float:none; width:100%; display:inline">{$moptCreditCardCheckEnvironment.moptPayolutionInformation.consentDebit}</label>
    </p>
    <div class="register--required-info">{$moptCreditCardCheckEnvironment.moptPayolutionInformation.legalTerm}</div>

    <div id="payolution_overlay_debit" class="js--modal content" style="width:78%; height:90%; display: none; opacity: 0.9; margin: 75px auto;">
        <a href="#" onclick="removeOverlayDebit();
                return false;" style="float:right;font-weight:bold;">Fenster schliessen</a><br><br>
        {$moptCreditCardCheckEnvironment.moptPayolutionInformation.overlaycontent}                    
    </div>    
    <div id="payolution_overlay_debit_bg" class="js--overlay is--open" style="opacity: 0.8; display: none"></div>

    <p class="none clearfix">
        <input name="moptPaymentData[mopt_payone__payolution_debitnote_agreement2]" type="checkbox" id="mopt_payone__payolution_debitnote_agreement2" value="true"
               {if $form_data.mopt_payone__payolution_debitnote_agreement2 eq "on"}
                   checked="checked"
               {/if}
               class="checkbox"/>
        <label class="{if $error_flags.mopt_payone__payolution_debitnote_agreement2} has--error{/if}" for="mopt_payone__payolution_debitnote_agreement2"  style="float:none; width:100%; display:inline">{$moptCreditCardCheckEnvironment.moptPayolutionInformation.sepaagreement}</label>
    </p>  
    
    <div id="payolution_overlay_debit" class="js--modal content" style="width:78%; height:auto; display: none; opacity: 0.9; margin: 0 auto;">
            <a href="#" onclick="removeOverlay();return false;" style="float:right;font-weight:bold;">Fenster schliessen</a><br><br>
                {$moptCreditCardCheckEnvironment.moptPayolutionInformation.overlaycontent}                    
    </div>    
    <div id="payolution_overlay_debit_bg" class="js--overlay is--open" style="opacity: 0.8; display: none"></div>            

    {block name='frontend_checkout_payment_required'}
        {* Required fields hint *}
        <div class="register--required-info">
            {s name='RegisterPersonalRequiredText' namespace='frontend/register/personal_fieldset'}{/s}
        </div>
    {/block}    

</div>

<script type="text/javascript">
    
    function displayOverlayDebit() {
        document.getElementById('payolution_overlay_debit').style.display = "block";
        document.getElementById('payolution_overlay_debit_bg').style.display = "block";
    }
    function removeOverlayDebit() {
        document.getElementById('payolution_overlay_debit').style.display = "none";
        document.getElementById('payolution_overlay_debit_bg').style.display = "none";
    }

    function payolutionDebitNoteDobInput()
    {
        var daySelect = document.getElementById("mopt_payone__payolution_debitnote_birthday");
        var monthSelect = document.getElementById("mopt_payone__payolution_debitnote_birthmonth");
        var yearSelect = document.getElementById('mopt_payone__payolution_debitnote_birthyear');
        var hiddenDobFull = document.getElementById("mopt_payone__payolution_debitnote_birthdaydate");
        var hiddenDobHint = document.getElementById("debitnote-hint-18-years");

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
