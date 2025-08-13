{namespace name='frontend/MoptPaymentPayone/payment'}

<div class="payment--form-group">
    {if ($fcPayolutionConfigInstallment.payolutionB2bmode == "0" && $moptCreditCardCheckEnvironment.birthday == "0000-00-00") || ( $fcPayolutionConfigInstallment.payolutionB2bmode == 1 && $moptCreditCardCheckEnvironment.birthday == "0000-00-00" && !$sUserData.billingaddress.company  ) }

        <p class ="none">
            <label for="mopt_payone__fin_payolution_installment_birthday">
                {s name='birthdate'}Geburtsdatum{/s}
            </label>
        </p>

    <div class="select-field">
        <select name="moptPaymentData[mopt_payone__fin_payolution_installment_birthday]"
                id="mopt_payone__fin_payolution_installment_birthday" onchange="payolutionInstallmentDobInput()"
                aria-label="{s name='birthdate'}Geburtsdatum{/s}"
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="is--required{if $error_flags.mopt_payone__fin_payolution_installment_birthday} has--error{/if}">
            <option value="">--</option>
            {section name="birthdate" start=1 loop=32 step=1}
                <option value="{if $smarty.section.birthdate.index < 10}0{/if}{$smarty.section.birthdate.index}"
                        {if $smarty.section.birthdate.index eq $moptCreditCardCheckEnvironment.mopt_payone__fin_payolution_installment_birthday}
                            selected
                        {/if}>
                    {if $smarty.section.birthdate.index < 10}0{/if}{$smarty.section.birthdate.index}</option>
                {/section}
        </select>
    </div>

    <div class="select-field">
        <select name="moptPaymentData[mopt_payone__fin_payolution_installment_birthmonth]"
                id="mopt_payone__fin_payolution_installment_birthmonth" onchange="payolutionInstallmentDobInput()"
                aria-label="{s name='birthdate'}Geburtsdatum{/s}"
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="is--required {if $error_flags.mopt_payone__fin_payolution_installment_birthmonth} has--error{/if}">
            <option value="">--</option>
            {section name="birthmonth" start=1 loop=13 step=1}
                <option value="{if $smarty.section.birthmonth.index < 10}0{/if}{$smarty.section.birthmonth.index}"
                        {if $smarty.section.birthmonth.index eq $moptCreditCardCheckEnvironment.mopt_payone__fin_payolution_installment_birthmonth}
                            selected
                        {/if}>
                    {if $smarty.section.birthmonth.index < 10}0{/if}{$smarty.section.birthmonth.index}</option>
            {/section}
        </select>
    </div>

    <div class="select-field">
        <select name="moptPaymentData[mopt_payone__fin_payolution_installment_birthyear]"
                id="mopt_payone__fin_payolution_installment_birthyear" onchange="payolutionInstallmentDobInput()"
                aria-label="{s name='birthdate'}Geburtsdatum{/s}"
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="select--country is--required{if $error_flags.mopt_payone__fin_payolution_installment_birthyear} register--error-msg {/if}">
            <option value="">----</option>
            {section name="birthyear" loop=2016 max=100 step=-1}
                <option value="{$smarty.section.birthyear.index}" 
                        {if $smarty.section.birthyear.index eq $moptCreditCardCheckEnvironment.mopt_payone__fin_payolution_installment_birthyear}
                            selected
                        {/if}>
                    {$smarty.section.birthyear.index}</option>
                {/section}
        </select>
    </div>
        {include file='frontend/include/dropdown_birthday_day.tpl' id='mopt_payone__fin_payolution_installment' label="{s name='birthdate'}Geburtsdatum{/s}"}
        {include file='frontend/include/dropdown_birthday_month.tpl' id='mopt_payone__fin_payolution_installment'}
        {include file='frontend/include/dropdown_birthday_year.tpl' id='mopt_payone__fin_payolution_installment'}
    {/if}
    {include file='frontend/include/input_birthday_validation.tpl' id='mopt_payone__fin_payolution_installment'}

    <input class="is--hidden" type="text" name="moptPaymentData[mopt_payone__fin_payolution_installment_shippingcosts]"
           id="mopt_payone__fin_payolution_installment_shippingcosts" value="{$sShippingcosts}">


    {if $fcPayolutionConfigInstallment.payolutionB2bmode && $sUserData.billingaddress.company}
        {include file='frontend/include/input_vatid.tpl' id='mopt_payone__fin_payolution_installment_vatid'}
        <input class="is--hidden" type="text" name="moptPaymentData[mopt_payone__fin_payolution_installment_b2bmode]"
               id="moptPaymentData[mopt_payone__fin_payolution_installment_b2bmode]" value="1">
    {/if}

    {if $sUserData.additional.country.countryiso !== 'CH' && $sUserData.additional.country.countryiso !== 'GB'
    && $sBasket.sCurrencyName !== 'GBP' && $sBasket.sCurrencyName !== 'CHF' }
        {include file='frontend/include/input_iban.tpl' id='mopt_payone__fin_payolution_installment'}
    {/if}

    <p class="none clearfix">
        <input name="moptPaymentData[mopt_payone__fin_payolution_installment_agreement]" type="checkbox"
               id="mopt_payone__fin_payolution_installment_agreement" value="true"
               class="checkbox"/>
        <label class="{if $error_flags.mopt_payone__fin_payolution_installment_agreement} has--error{/if}"
               for="mopt_payone__fin_payolution_installment_agreement"
               style="float:none; width:100%; display:inline">{$moptCreditCardCheckEnvironment.moptPayolutionInformation.consentInstallment}</label>
    </p>

    {include file='frontend/include/payolution_overlay.tpl' id='mopt_payone__fin_payolution_installment'}

    <input type="hidden" name="moptPaymentData[mopt_payone__fin_payolution_installment_duration]" value=""
           id="mopt_payone__fin_payolution_installment_duration" autocomplete="off">
    <input type="hidden" name="moptPaymentData[mopt_payone__fin_payolution_installment_workorderid]" value=""
           id="mopt_payone__fin_payolution_installment_workorderid" autocomplete="off">
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
    {include file='frontend/_resources/javascript/mopt_birthday.js.tpl' id='mopt_payone__fin_payolution_installment'}
    {include file='frontend/_resources/javascript/mopt_payolution_overlay.js.tpl' id='mopt_payone__fin_payolution_installment'}
</script>
<script type="text/javascript">

    var payolutionInstallmentDob = false;
    var payolutionInstallmentAgree = false;

    function handleInstallment() {
        var call = '{url controller="moptAjaxPayone" action="ajaxHandlePayolutionPreCheck" forceSecure}';
        var call2 = '{url controller="moptAjaxPayone" action="renderPayolutionInstallment" forceSecure}';
        var dob = document.getElementById("mopt_payone__fin_payolution_installment_birthdaydate");
        var hiddenDobHint = document.getElementById("mopt_payone__fin_payolution_installment-hint-18-years");

        var oBirthDate = new Date(dob.value);
        var oMinDate = new Date(new Date().setYear(new Date().getFullYear() - 18));
        if (oBirthDate > oMinDate) {
            hiddenDobHint.className = "register--error-msg";
            return;
        } else {
            hiddenDobHint.className = "is--hidden";
            payolutionInstallmentDob = true;
        }

        var hreg = document.getElementById("mopt_payone__fin_payolution_installment_vatid");
        var myhreg;
        var mydob;
        if (hreg !== null) {
            myhreg = hreg.value;
        }

        if (dob.value !== null) {
            mydob = dob.value;
            mydob = mydob.replace(/-/g, "");
        }

        payolutionInstallmentAgree = $("#mopt_payone__fin_payolution_installment_agreement").prop('checked');

        // only make the api call when dob is ok and user agreement is checked
        if (payolutionInstallmentDob && payolutionInstallmentAgree) {
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
                                $('#mopt_payone__fin_payolution_installment_workorderid').val(response.workorderid);
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

    function switchInstallmentPlan(sKey, iInstallments) {
        jQuery(".payolution_installmentplans").hide();
        jQuery(".payolution_installment_overview").hide();
        var aShow = [
            'mopt_payone__fin_payolution_installment_plan_' + sKey,
            'payolution_installment_overview_' + sKey
        ];
        switchVisibility(aShow, true);
        jQuery('#mopt_payone__fin_payolution_installment_duration').val(iInstallments);
    }


</script>        
