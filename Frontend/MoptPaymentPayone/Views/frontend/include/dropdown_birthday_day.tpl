{namespace name='frontend/MoptPaymentPayone/payment'}
<div class="profile--birthdate">
    <div>
        <strong class="birthday--label">Geburtsdatum</strong>
    </div>
    {$dayIndex = "{$id}_birthdayday"}
    <div class="profile--birthdate field--select select-field">
        <select id="{$id}_birthdayday" name="moptPaymentData[{$id}_birthdayday]" onchange="{$id}DobInput()"
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="{if $sErrorFlag.$dayIndex}has--error{/if}">
            <option value="">--</option>
            {section name="birthdayday" start=1 loop=32 step=1}
                <option value="{if $smarty.section.birthdayday.index < 10}0{/if}{$smarty.section.birthdayday.index}"
                        {if $smarty.section.birthdayday.index eq $moptCreditCardCheckEnvironment.$dayIndex}selected{/if}>
                    {if $smarty.section.birthdayday.index < 10}0{/if}{$smarty.section.birthdayday.index}</option>
            {/section}
        </select>
    </div>