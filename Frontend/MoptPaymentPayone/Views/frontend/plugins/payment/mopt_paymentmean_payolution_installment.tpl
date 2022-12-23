{namespace name='frontend/MoptPaymentPayone/payment'}

<div class="payment--form-group">
    {if ($fcPayolutionConfigInstallment.payolutionB2bmode == "0" && $moptCreditCardCheckEnvironment.birthday == "0000-00-00") || ( $fcPayolutionConfigInstallment.payolutionB2bmode == 1 && $moptCreditCardCheckEnvironment.birthday == "0000-00-00" && !$sUserData.billingaddress.company  ) }

        <p class ="none">
            <label for="mopt_payone__payolution_installment_birthday">
                {s name='birthdate'}Geburtsdatum{/s}
            </label>
        </p>

    <div class="select-field">
        <select name="moptPaymentData[mopt_payone__payolution_installment_birthday]" 
                id="mopt_payone__payolution_installment_birthday" onchange="payolutionInstallmentDobInput()" 
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="is--required{if $error_flags.mopt_payone__payolution_installment_birthday} has--error{/if}">
            <option value="">--</option>
            {section name="birthdate" start=1 loop=32 step=1}
                <option value="{if $smarty.section.birthdate.index < 10}0{/if}{$smarty.section.birthdate.index}" 
                        {if $smarty.section.birthdate.index eq $moptCreditCardCheckEnvironment.mopt_payone__payolution_installment_birthday}
                            selected
                        {/if}>
                    {if $smarty.section.birthdate.index < 10}0{/if}{$smarty.section.birthdate.index}</option>
                {/section}
        </select>
    </div>

    <div class="select-field">
        <select name="moptPaymentData[mopt_payone__payolution_installment_birthmonth]" 
                id="mopt_payone__payolution_installment_birthmonth" onchange="payolutionInstallmentDobInput()" 
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="is--required {if $error_flags.mopt_payone__payolution_installment_birthmonth} has--error{/if}">
            <option value="">--</option>
            {section name="birthmonth" start=1 loop=13 step=1}
                <option value="{if $smarty.section.birthmonth.index < 10}0{/if}{$smarty.section.birthmonth.index}" 
                        {if $smarty.section.birthmonth.index eq $moptCreditCardCheckEnvironment.mopt_payone__payolution_installment_birthmonth}
                            selected
                        {/if}>
                    {if $smarty.section.birthmonth.index < 10}0{/if}{$smarty.section.birthmonth.index}</option>
            {/section}
        </select>
    </div>

    <div class="select-field">
        <select name="moptPaymentData[mopt_payone__payolution_installment_birthyear]" 
                id="mopt_payone__payolution_installment_birthyear" onchange="payolutionInstallmentDobInput()" 
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="select--country is--required{if $error_flags.mopt_payone__payolution_installment_birthyear} register--error-msg {/if}">
            <option value="">----</option>
            {section name="birthyear" loop=2016 max=100 step=-1}
                <option value="{$smarty.section.birthyear.index}" 
                        {if $smarty.section.birthyear.index eq $moptCreditCardCheckEnvironment.mopt_payone__payolution_installment_birthyear}
                            selected
                        {/if}>
                    {$smarty.section.birthyear.index}</option>
                {/section}
        </select>
    </div>
    {/if}  

    <input class="is--hidden validate-18-years" type="text" name="moptPaymentData[mopt_payone__payolution_installment_birthdaydate]" id="mopt_payone__payolution_installment_birthdaydate" value="{$moptCreditCardCheckEnvironment.birthday}">   
    <input class="is--hidden" type="text" name="moptPaymentData[mopt_payone__payolution_installment_shippingcosts]" id="mopt_payone__payolution_installment_shippingcosts" value="{$sShippingcosts}">
    <div id="installment-hint-18-years" class="is--hidden">Sie müssen mindestens 18 Jahre alt sein, um diese Zahlart verwenden zu können.</div>        

    {if $fcPayolutionConfigInstallment.payolutionB2bmode && $sUserData.billingaddress.company}
        <input type="text" name="moptPaymentData[mopt_payone__installment_company_trade_registry_number]"
               id="mopt_payone__installment_company_trade_registry_number" aria-required="false"
               placeholder="{s name='companyTradeRegistryNumber'}Handelsregisternummer{/s}"
               class="payment--field{if $error_flags.mopt_payone__installment_company_trade_registry_number} has--error{/if}">
        <input class="is--hidden" type="text" name="moptPaymentData[mopt_payone__payolution_installment_b2bmode]" id="moptPaymentData[mopt_payone__payolution_installment_b2bmode]" value="1">
    {/if}       

    {if $sUserData.additional.country.countryiso !== 'CH' && $sUserData.additional.country.countryiso !== 'GB'
        && $sBasket.sCurrencyName !== 'GBP' && $sBasket.sCurrencyName !== 'CHF' }

        <input name="moptPaymentData[mopt_payone__payolution_installment_iban]"
               type="text"
               id="mopt_payone__payolution_installment_iban"
               {if $moptRequired}required="required" aria-required="true"{/if}
               placeholder="{s name='bankIBAN'}IBAN{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
               value="{$form_data.mopt_payone__payolution_installment_iban|escape}"
               data-moptIbanErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}"
               class="payment--field {if $moptRequired}is--required{/if}{if $error_flags.mopt_payone__payolution_installment_iban} has--error{/if} moptPayoneIbanBic" />

        <input name="moptPaymentData[mopt_payone__payolution_installment_bic]"
               type="text"
               id="mopt_payone__payolution_installment_bic"
               {if $moptRequired}required="required" aria-required="true"{/if}
               placeholder="{s name='bankBIC'}BIC{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
               value="{$form_data.mopt_payone__payolution_installment_bic|escape}"
               data-moptIbanErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}"
               class="payment--field {if $moptRequired}is--required{/if}{if $error_flags.mopt_payone__payolution_installment_bic} has--error{/if} moptPayoneIbanBic" />
    {/if}
    
    <p class="none clearfix">
        <input name="moptPaymentData[mopt_payone__payolution_installment_agreement]" type="checkbox" id="mopt_payone__payolution_installment_agreement" value="true"
               class="checkbox"/>
        <label class="{if $error_flags.mopt_payone__payolution_installment_agreement} has--error{/if}" for="mopt_payone__payolution_installment_agreement"  style="float:none; width:100%; display:inline">{$moptCreditCardCheckEnvironment.moptPayolutionInformation.consentInstallment}</label>
    </p>
    <div id="payolution_overlay_installment" class="js--modal content" style="width:78%; height:90%; display: none; opacity: 0.9; margin: 75px auto;">
        <a href="#" onclick="removeOverlayInstallment();
                return false;" style="float:right;font-weight:bold;">Fenster schliessen</a><br><br>
        {$moptCreditCardCheckEnvironment.moptPayolutionInformation.overlaycontent}                    
    </div>
    <div id="payolution_overlay_installment_bg" class="js--overlay is--open" style="opacity: 0.8; display: none"></div>        
    <input type="hidden" name="moptPaymentData[mopt_payone__payolution_installment_duration]" value="" id="payone_payolution_selected_installmentplan" autocomplete="off">    
    <input type="hidden" name="moptPaymentData[mopt_payone__payolution_installment_workorderid]" value="" id="payolution_installment_workorderid" autocomplete="off">    
    <button onclick="handleInstallment();" class="button" type="button">
        <span>
            <span>{s name='CheckInstallmentAvailability'}Check installment availability{/s}</span>
        </span>
    </button>  
    <br>
    <br>

    <div id="showresults">
    </div>        
</div>

<script type="text/javascript">

    var payolutionInstallmentDob = false;
    var payolutionInstallmentAgree = false;

    function displayOverlayInstallment() {
        document.getElementById('payolution_overlay_installment').style.display = "block";
        document.getElementById('payolution_overlay_installment_bg').style.display = "block";
    }

    function removeOverlayInstallment() {
        document.getElementById('payolution_overlay_installment').style.display = "none";
        document.getElementById('payolution_overlay_installment_bg').style.display = "none";
    }

    function payolutionInstallmentDobInput()
    {
        var daySelect = document.getElementById("mopt_payone__payolution_installment_birthday");
        var monthSelect = document.getElementById("mopt_payone__payolution_installment_birthmonth");
        var yearSelect = document.getElementById('mopt_payone__payolution_installment_birthyear');
        var hiddenDobFull = document.getElementById("mopt_payone__payolution_installment_birthdaydate");
        var hiddenDobHint = document.getElementById("installment-hint-18-years");
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
            payolutionInstallmentDob = true;
            return;
        }
    }

    function handleInstallment() {
        var call = '{url controller="moptAjaxPayone" action="ajaxHandlePayolutionPreCheck" forceSecure}';
        var call2 = '{url controller="moptAjaxPayone" action="renderPayolutionInstallment" forceSecure}';
        var dob = document.getElementById("mopt_payone__payolution_installment_birthdaydate");
        var hiddenDobHint = document.getElementById("installment-hint-18-years");

        var oBirthDate = new Date(dob.value);
        var oMinDate = new Date(new Date().setYear(new Date().getFullYear() - 18));
        if (oBirthDate > oMinDate) {
            hiddenDobHint.className = "register--error-msg";
            return;
        } else {
            hiddenDobHint.className = "is--hidden";
            payolutionInstallmentDob = true;
        }

        var hreg = document.getElementById("mopt_payone__installment_company_trade_registry_number");
        var myhreg; 
        var mydob;
        if ( hreg !== null){
            myhreg = hreg.value;
        }
        
        if (dob.value !== null){
            mydob = dob.value;
            mydob = mydob.replace(/-/g,"");
        }

        payolutionInstallmentAgree = $("#mopt_payone__payolution_installment_agreement").prop('checked');

        // only make the api call when dob is ok and user agreement is checked
        if (payolutionInstallmentDob && payolutionInstallmentAgree){
            $.ajax({
                url: call,
                type: 'POST',
                data: { dob: mydob, hreg: myhreg, shippingcosts: {$sShippingcosts}, basketamount: {$sAmount} },

                beforeSend: function () {
                    $.loadingIndicator.open();
                },
                success: function (data) {
                    response = $.parseJSON(data);
                    if (response.status === 'success') {
                        $.ajax({
                            url: call2,
                            type: 'POST',
                            dataType: 'html',
                            data: $.parseJSON(data),
                            success: function (data3) {
                                $.loadingIndicator.close();
                                $('#showresults').html(data3);
                                $('#payolution_installment_workorderid').val(response.workorderid);
                            }
                        });
                    }
                    if (response.status == 'error') {
                        alert(response.errorMessage);
                        $.loadingIndicator.close();
                    }
                },
            });
        }
    }


    function switchVisibility(aIds, blShow) {
        for (var i = 0; i < aIds.length; i++) {
            var oElement = jQuery('#' + aIds[i]);
            if (oElement) {
                if (blShow == true) {
                    oElement.show();
                } else {
                    oElement.hide();
                }
            }
        }
    }

    function switchInstallmentPlan(sKey,iInstallments) {
        jQuery(".payolution_installmentplans").hide();
        jQuery(".payolution_installment_overview").hide();
        var aShow = [
            'payolution_installmentplan_' + sKey,
            'payolution_installment_overview_' + sKey
        ];
        switchVisibility(aShow, true);
        jQuery('#payone_payolution_selected_installmentplan').val(iInstallments);
    }


</script>        
