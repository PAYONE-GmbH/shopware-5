{namespace name='frontend/MoptPaymentPayone/payment'}
    {$yearIndex = "{$id}_birthdayyear"}
    <div class="profile--birthyear field--select select-field">
        <select id="{$id}_birthdayyear" name="moptPaymentData[{$id}_birthdayyear]" onchange="{$id}DobInput()"
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="{if $sErrorFlag.$yearIndex}has--error{/if}">
            <option value="">----</option>
            {section name="birthdayyear" loop=2016 max=100 step=-1}
                <option value="{$smarty.section.birthdayyear.index}"
                        {if $smarty.section.birthdayyear.index eq $moptCreditCardCheckEnvironment.$yearIndex}selected{/if}>
                    {$smarty.section.birthdayyear.index}</option>
            {/section}
        </select>
    </div>
</div>