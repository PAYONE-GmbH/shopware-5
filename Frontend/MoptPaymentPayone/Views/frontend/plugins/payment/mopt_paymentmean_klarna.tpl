{namespace name='frontend/MoptPaymentPayone/payment'}

<div class="payment--form-group">
    <input name="moptPaymentData[mopt_payone__klarna_telephone]"
        type="text"
        id="mopt_payone__klarna_telephone"
        {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
        placeholder="{s name='telephoneNumber'}Telefonnummer{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
        value="{$moptCreditCardCheckEnvironment.mopt_payone__klarna_telephone|escape}"
        class="payment--field is--required{if $error_flags.mopt_payone__klarna_telephone} has--error{/if}"
    />

    <p class="none">
        <label for="mopt_payone__klarna_birthday">{s name='birthdate'}Geburtsdatum{/s}</label>
    </p>

    <div class="select-field">
        <select name="moptPaymentData[mopt_payone__klarna_birthday]"
            id="mopt_payone__klarna_birthday"
            {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
            class="select--country is--required{if $error_flags.mopt_payone__klarna_birthday} has--error{/if}"
        >
            <option disabled="disabled" value="">--</option>
            {section name="birthdate" start=1 loop=32 step=1}
                {$isSelected = $smarty.section.birthdate.index == $moptCreditCardCheckEnvironment.mopt_payone__klarna_birthday}
                <option value="{$smarty.section.birthdate.index}" {if $isSelected}selected{/if}>
                    {$smarty.section.birthdate.index}
                </option>
            {/section}
        </select>
    </div>

    <div class="select-field">
        <select name="moptPaymentData[mopt_payone__klarna_birthmonth]"
            id="mopt_payone__klarna_birthmonth"
            {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
            class="select--country is--required{if $error_flags.mopt_payone__klarna_birthmonth} has--error{/if}"
        >
            <option disabled="disabled" value="">-</option>
            {section name="birthmonth" start=1 loop=13 step=1}
                {$isSelected = $smarty.section.birthmonth.index == $moptCreditCardCheckEnvironment.mopt_payone__klarna_birthmonth}
                <option value="{$smarty.section.birthmonth.index}" {if $isSelected}selected{/if}>
                    {$smarty.section.birthmonth.index}
                </option>
            {/section}
        </select>
    </div>

    <div class="select-field">
        <select name="moptPaymentData[mopt_payone__klarna_birthyear]"
            id="mopt_payone__klarna_birthyear"
            {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
            class="select--country is--required{if $error_flags.mopt_payone__klarna_birthyear} has--error{/if}"
        >
            <option disabled="disabled" value="">----</option>
            {section name="birthyear" loop=2000 max=100 step=-1}
                {$isSelected = $smarty.section.birthyear.index == $moptCreditCardCheckEnvironment.mopt_payone__klarna_birthyear}
                <option value="{$smarty.section.birthyear.index}" {if $isSelected}selected{/if}>
                    {$smarty.section.birthyear.index}
                </option>
            {/section}
        </select>
    </div>

    <p class="none clearfix">
        <input name="moptPaymentData[mopt_payone__klarna_agreement]" type="checkbox" id="mopt_payone__klarna_agreement" value="true"
            {if $form_data.mopt_payone__klarna_agreement eq "on"}checked="checked"{/if}
            class="checkbox{if $error_flags.mopt_payone__klarna_agreement} has--error{/if}"
        />
        <label for="mopt_payone__klarna_agreement" style="float: none; width: 100%; display: inline">
            {$moptCreditCardCheckEnvironment.moptKlarnaInformation.consent}
        </label>
    </p>
    <div class="register--required-info">
        {$moptCreditCardCheckEnvironment.moptKlarnaInformation.legalTerm}
    </div>
</div>
