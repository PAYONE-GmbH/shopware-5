{namespace name='frontend/MoptPaymentPayone/payment'}

{if $payment_mean.id == $form_data.payment}
    {assign var="moptRequired" value=1}
{else}
    {assign var="moptRequired" value=0}
{/if}

<div class="payment--form-group">

    {if ($moptCreditCardCheckEnvironment.birthday == "0000-00-00" || $moptCreditCardCheckEnvironment.birthday =="") }
        <p class ="none">
            <label for="mopt_payone__payone_safe_invoice_birthday">
                {s name='birthdate'}Geburtsdatum{/s}
            </label>
        </p>

        <select name="moptPaymentData[mopt_payone__payone_safe_invoice_birthday]"
                id="mopt_payone__payone_safe_invoice_birthday" onchange="payoneSafeInvoiceDobInput()"
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="payment--field {if $error_flags.mopt_payone__payone_safe_invoice_birthday} has--error{/if}">
            <option value="">--</option>
            {section name="birthdate" start=1 loop=32 step=1}
                <option value="{if $smarty.section.birthdate.index < 10}0{/if}{$smarty.section.birthdate.index}" 
                        {if $smarty.section.birthdate.index eq $moptCreditCardCheckEnvironment.mopt_payone__payone_safe_invoice_birthday}
                            selected
                        {/if}>
                    {if $smarty.section.birthdate.index < 10}0{/if}{$smarty.section.birthdate.index}</option>
                {/section}
        </select>

        <select name="moptPaymentData[mopt_payone__payone_safe_invoice_birthmonth]"
                id="mopt_payone__payone_safe_invoice_birthmonth" onchange="payoneSafeInvoiceDobInput()"
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="payment--field {if $error_flags.mopt_payone__payone_safe_invoice_birthmonth} has--error{/if}">
            <option value="">--</option>
            {section name="birthmonth" start=1 loop=13 step=1}
                <option value="{if $smarty.section.birthmonth.index < 10}0{/if}{$smarty.section.birthmonth.index}" 
                        {if $smarty.section.birthmonth.index eq $moptCreditCardCheckEnvironment.mopt_payone__payone_safe_invoice_birthmonth}
                            selected
                        {/if}>
                    {if $smarty.section.birthmonth.index < 10}0{/if}{$smarty.section.birthmonth.index}</option>
                {/section}
        </select>

        <select name="moptPaymentData[mopt_payone__payone_safe_invoice_birthyear]"
                id="mopt_payone__payone_safe_invoice_birthyear" onchange="payoneSafeInvoiceDobInput()"
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="payment--field {if $error_flags.mopt_payone__payone_safe_invoice_birthyear} has--error{/if}">
            <option value="">----</option>
            {section name="birthyear" loop=2016 max=100 step=-1}
                <option value="{$smarty.section.birthyear.index}" 
                        {if $smarty.section.birthyear.index eq $moptCreditCardCheckEnvironment.mopt_payone__payone_safe_invoice_birthyear}
                            selected
                        {/if}>
                    {$smarty.section.birthyear.index}</option>
                {/section}
        </select>        
      {/if}


    <input class="is--hidden validate-18-years" type="text" name="moptPaymentData[mopt_payone__payone_safe_invoice_birthdaydate]" id="mopt_payone__payone_safe_invoice_birthdaydate" value="{$moptCreditCardCheckEnvironment.birthday}">
    <div id="safeinvoice-hint-18-years" class="is--hidden">Sie müssen mindestens 18 Jahre alt sein, um diese Zahlart verwenden zu können.</div>
</div>

<script type="text/javascript">

    function payoneSafeInvoiceDobInput()
    {
        var daySelect = document.getElementById("mopt_payone__payone_safe_invoice_birthday");
        var monthSelect = document.getElementById("mopt_payone__payone_safe_invoice_birthmonth");
        var yearSelect = document.getElementById('mopt_payone__payone_safe_invoice_birthyear');
        var hiddenDobFull = document.getElementById("mopt_payone__payone_safe_invoice_birthdaydate");
        var hiddenDobHint = document.getElementById("safeinvoice-hint-18-years");

        if (daySelect.value == "" || monthSelect.value == "" || yearSelect.value == ""
                 || daySelect == undefined) {
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
