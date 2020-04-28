{namespace name='frontend/MoptPaymentPayone/payment'}

<div class="payment--form-group">
    {block name="frontend_checkout_payment_payone_telephone_label"}
        <p class="none">
            <label for="mopt_payone__klarna_telephone">{s name='klarnaTelephoneLabel'}Telefonnummer{/s}</label>
        </p>
    {/block}

    {block name="frontend_checkout_payment_payone_telephone_input"}
        <input name="moptPaymentData[mopt_payone__klarna_telephone]" type="text"
               id="mopt_payone__klarna_telephone"
               class="payment--field is--required{if $error_flags.mopt_payone__klarna_telephone} has--error{/if}"
               placeholder="{s name='telephoneNumber'}Telefonnummer{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
               {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
               value="{$moptCreditCardCheckEnvironment.mopt_payone__klarna_telephone|escape}"
        />
    {/block}

    {block name="frontend_checkout_payment_payone_birthday_label"}
        <p class="none">
            <label for="mopt_payone__klarna_birthday">{s name='birthdate'}Geburtsdatum{/s}</label>
        </p>
    {/block}

    <div class="select-field">
        {block name="frontend_checkout_payment_payone_birthday_day_input"}
            <select name="moptPaymentData[mopt_payone__klarna_birthday]"
                    id="mopt_payone__klarna_birthday"
                    class="select--country is--required{if $error_flags.mopt_payone__klarna_birthday} has--error{/if}"
                    {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
            >
                <option disabled="disabled" value="">--</option>
                {section name="birthdate" start=1 loop=32 step=1}
                    {$isSelected = $smarty.section.birthdate.index == $moptCreditCardCheckEnvironment.mopt_payone__klarna_birthday}
                    <option value="{$smarty.section.birthdate.index}" {if $isSelected}selected{/if}>
                        {$smarty.section.birthdate.index}
                    </option>
                {/section}
            </select>
        {/block}
    </div>

    <div class="select-field">
        {block name="frontend_checkout_payment_payone_birthday_month_input"}
            <select name="moptPaymentData[mopt_payone__klarna_birthmonth]"
                    id="mopt_payone__klarna_birthmonth"
                    class="select--country is--required{if $error_flags.mopt_payone__klarna_birthmonth} has--error{/if}"
                    {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
            >
                <option disabled="disabled" value="">-</option>
                {section name="birthmonth" start=1 loop=13 step=1}
                    {$isSelected = $smarty.section.birthmonth.index == $moptCreditCardCheckEnvironment.mopt_payone__klarna_birthmonth}
                    <option value="{$smarty.section.birthmonth.index}" {if $isSelected}selected{/if}>
                        {$smarty.section.birthmonth.index}
                    </option>
                {/section}
            </select>
        {/block}
    </div>

    <div class="select-field">
        {block name="frontend_checkout_payment_payone_birthday_year_input"}
            <select name="moptPaymentData[mopt_payone__klarna_birthyear]"
                    id="mopt_payone__klarna_birthyear"
                    class="select--country is--required{if $error_flags.mopt_payone__klarna_birthyear} has--error{/if}"
                    {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
            >
                <option disabled="disabled" value="">----</option>
                {section name="birthyear" loop=2021 max=120 step=-1}
                    {$isSelected = $smarty.section.birthyear.index == $moptCreditCardCheckEnvironment.mopt_payone__klarna_birthyear}
                    <option value="{$smarty.section.birthyear.index}" {if $isSelected}selected{/if}>
                        {$smarty.section.birthyear.index}
                    </option>
                {/section}
            </select>
        {/block}
    </div>

    <p class="none clearfix">
        <input name="moptPaymentData[mopt_payone__klarna_agreement]" type="checkbox"
               id="mopt_payone__klarna_agreement" value="true"
               class="checkbox{if $error_flags.mopt_payone__klarna_agreement} has--error{/if}"
               {if $form_data.mopt_payone__klarna_agreement eq "on"}checked="checked"{/if}
        />
        <label for="mopt_payone__klarna_agreement" style="float: none; width: 100%; display: inline">
            {block name="frontend_checkout_payment_payone_consent"}
                {$moptCreditCardCheckEnvironment.moptKlarnaInformation.consent}
            {/block}
        </label>
    </p>
    <div class="register--required-info">
        {block name="frontend_checkout_payment_payone_legaltermn"}
            {$moptCreditCardCheckEnvironment.moptKlarnaInformation.legalTerm}
        {/block}
    </div>
</div>
