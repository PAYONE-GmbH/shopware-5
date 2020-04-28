{namespace name='frontend/MoptPaymentPayone/payment'}

<div class="payment--form-group">
    <div id="mopt_payone__klarna_paymenttype_wrap" class="select-field">
        <label for="mopt_payone__klarna_paymenttype"></label>
        <select name="moptPaymentData[mopt_payone__klarna_paymenttype]"
                id="mopt_payone__klarna_paymenttype"
                class="select--country">
            <option disabled="disabled" value=""
                    selected="selected">{s name='klarna-paymentType'}Klarna Zahlart{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}</option>
            {foreach from=$moptCreditCardCheckEnvironment.mopt_payone_klarna.mopt_payone_klarna_payments item=klarna_payment}
                <option value="{$klarna_payment.short}"
                        {if $form_data.mopt_payone__klarna_paymentname == $klarna_payment.name}selected="selected"{/if}
                        mopt_payone__klarna_paymentname="{$klarna_payment.name}"
                        mopt_payone__klarna_paymentid="{$klarna_payment.id}"
                >
                    {$klarna_payment.description}
                </option>
            {/foreach}
        </select>
    </div>
</div>
