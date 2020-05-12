{namespace name='frontend/MoptPaymentPayone/payment'}

<div class="payment--form-group">
    <div id="mopt_payone__klarna_information" hidden
         data-billing-address--city="{$billingAddressCity}"
         data-billing-address--country="{$billingAddressCountry}"
         data-billing-address--email="{$billingAddressEmail}"
         data-billing-address--family-name="{$billingAddressFamilyName}"
         data-billing-address--given-name="{$billingAddressGivenName}"
         data-billing-address--postal-code="{$billingAddressPostalCode}"
         data-billing-address--street-address="{$billingAddressStreetAddress}"
         data-purchase-currency="{$purchaseCurrency}"
         data-locale="{$locale}"
         data-store-authorization-token--URL="{url controller="MoptAjaxPayone" action="storeAuthorizationToken" forceSecure}"
    ></div>
    <div id="mopt_payone__klarna_paymenttype_wrap" class="select-field">
        <label for="mopt_payone__klarna_paymenttype"></label>
        <select name="moptPaymentData[mopt_payone__klarna_paymenttype]"
                id="mopt_payone__klarna_paymenttype"
                {if $payment_mean.id == $form_data.payment}
                    required="required"
                    aria-required="true"
                {/if}
                class="select--country"
        >
            <option disabled="disabled" value=""
                    selected="selected">{s name='klarna-paymentType'}Klarna Zahlart{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}</option>
            {foreach from=$moptCreditCardCheckEnvironment.mopt_payone_klarna.mopt_payone_klarna_payments item=klarna_payment}
                <option value="{$klarna_payment.financingtype}"
                        {if $form_data.mopt_payone__klarna_paymentname == $klarna_payment.name}selected="selected"{/if}
                        mopt_payone__klarna_paymentname="{$klarna_payment.name}"
                        mopt_payone__klarna_paymentid="{$klarna_payment.id}"
                >
                    {$klarna_payment.description}
                </option>
            {/foreach}
        </select>
    </div>

    <p class="none clearfix">
        <input name="moptPaymentData[mopt_payone__klarna_agreement]"
               type="checkbox"
               id="mopt_payone__klarna_agreement" value="true"
               class="checkbox {if $error_flags.mopt_payone__klarna_agreement}has--error{/if}"
                {if $payment_mean.id == $form_data.payment}
                    required="required"
                    aria-required="true"
                {/if}
               {if $form_data.mopt_payone__klarna_agreement eq "on"}checked="checked"{/if}
        />
        <label for="mopt_payone__klarna_agreement" style="float: none; width: 100%; display: inline">
            {block name="frontend_checkout_payment_payone_consent"}
                {$moptCreditCardCheckEnvironment.moptKlarnaInformation.consent}
            {/block}
        </label>
    </p>
    <div class="register--}ired-info">
        {block name="frontend_checkout_payment_payone_legaltermn"}
            {$moptCreditCardCheckEnvironment.moptKlarnaInformation.legalTerm}
        {/block}
    </div>
    <div id="mopt_payone__klarna_payments_widget_container"></div>
    <script>
        window.klarnaAsyncCallback = function () {
            window.Klarna = Klarna;
        };
    </script>
    <script src="https://x.klarnacdn.net/kp/lib/v1/api.js" async></script>
</div>
