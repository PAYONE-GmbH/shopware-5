{namespace name='frontend/MoptPaymentPayone/payment'}
{$monthIndex = "{$id}_birthdaymonth"}
<div class="profile--birthmonth field--select select-field">
    <select id="{$id}_birthdaymonth" name="moptPaymentData[{$id}_birthdaymonth]" onchange="{$id}DobInput()"
            {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
            class="{if $sErrorFlag.$monthIndex}has--error{/if}">
        <option value="">--</option>
        {section name="birthdaymonth" start=1 loop=13 step=1}
            <option value="{if $smarty.section.birthdaymonth.index < 10}0{/if}{$smarty.section.birthdaymonth.index}"
                    {if $smarty.section.birthdaymonth.index eq $moptCreditCardCheckEnvironment.$monthIndex}selected{/if}>
                {if $smarty.section.birthdaymonth.index < 10}0{/if}{$smarty.section.birthdaymonth.index}</option>
        {/section}
    </select>
</div>