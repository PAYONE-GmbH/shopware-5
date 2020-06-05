{namespace name='frontend/MoptPaymentPayone/payment'}

<div class="payment--form-group">
    <div id="mopt_payone__klarna_information" hidden
         data-shipping-address--city="{$shippingAddressCity}"
         data-shipping-address--country="{$shippingAddressCountry}"
         data-shipping-address--email="{$shippingAddressEmail}"
         data-shipping-address--family-name="{$shippingAddressFamilyName}"
         data-shipping-address--given-name="{$shippingAddressGivenName}"
         data-shipping-address--postal-code="{$shippingAddressPostalCode}"
         data-shipping-address--street-address="{$shippingAddressStreetAddress}"
         data-shipping-address--title="{$shippingAddressTitle}"
         data-shipping-address--phone="{$shippingAddressPhone}"
         data-billing-address--city="{$billingAddressCity}"
         data-billing-address--country="{$billingAddressCountry}"
         data-billing-address--email="{$billingAddressEmail}"
         data-billing-address--family-name="{$billingAddressFamilyName}"
         data-billing-address--given-name="{$billingAddressGivenName}"
         data-billing-address--postal-code="{$billingAddressPostalCode}"
         data-billing-address--street-address="{$billingAddressStreetAddress}"
         data-billing-address--title="{$billingAddressTitle}"
         data-billing-address--phone="{$billingAddressPhone}"
         data-order-lines='{$klarnaOrderLines}'
         data-customer-date-of-birth="{$customerDateOfBirth}"
         data-customer-gender="{$customerGender}"
         data-customer-national-indentification-number="{$customerNationalIdentificationNumber}"
         data-purchase-currency="{$purchaseCurrency}"
         data-locale="{$locale}"
         data-store-authorization-token--URL="{url controller="MoptAjaxPayone" action="storeAuthorizationToken" forceSecure}"
         data-start-klarna-session--URL="{url controller="MoptAjaxPayone" action="startKlarnaSession" forceSecure}"
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

    {block name="frontend_checkout_payment_payone_telephone_label"}
        <p class="none" {if ! $isKlarnaTelephoneNeeded} style="display: none;" {/if}>
            <label for="mopt_payone__klarna_telephone">{s name='klarnaTelephoneLabel'}Telefonnummer{/s}</label>
        </p>
    {/block}

    {block name="frontend_checkout_payment_payone_telephone_input"}
        <input name="moptPaymentData[mopt_payone__klarna_telephone]" type="text" {if ! $isKlarnaTelephoneNeeded} style="display: none;" {/if}
               id="mopt_payone__klarna_telephone"
               class="payment--field {if $isKlarnaTelephoneNeeded}is--required{/if} {if $error_flags.mopt_payone__klarna_telephone} has--error{/if}"
               placeholder="{s name='telephoneNumber'}Telefonnummer{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
               {if $payment_mean.id == $form_data.payment && $isKlarnaTelephoneNeeded}}required="required" aria-required="true"{/if}
               value="{$moptCreditCardCheckEnvironment.mopt_payone__klarna_telephone|escape}"
        />
    {/block}

    {block name="frontend_checkout_payment_payone_personalid_label"}
        <p class="none" {if ! $isKlarnaPersonalIdNeeded} style="display: none;" {/if}>
            <label for="mopt_payone__klarna_personalid">{s name='klarnaPersonalidLabel'}{/s}Personalid</label>
        </p>
    {/block}

    {block name="frontend_checkout_payment_payone_personalid_input"}
        <input name="moptPaymentData[mopt_payone__klarna_personalid]" type="text" {if ! $isKlarnaPersonalIdNeeded} style="display: none;" {/if}
               id="mopt_payone__klarna_personalid"
               class="payment--field {if $isKlarnaPersonalIdNeeded}is--required{/if} {if $error_flags.mopt_payone__klarna_personalid} has--error{/if}"
               placeholder="{s name='personalid'}PersonalId :$isKlarnaTelephoneNeeded:{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                {if $payment_mean.id == $form_data.payment && $isKlarnaPersonalIdNeeded}required="required" aria-required="true"{/if}
               value="{$moptCreditCardCheckEnvironment.mopt_payone__klarna_personalid|escape}"
        />
    {/block}

    {block name="frontend_checkout_payment_payone_birthday_label"}
        <p class="none" {if ! $isKlarnaBirthdayNeeded} style="display: none;" {/if}>
            <label for="mopt_payone__klarna_birthday">{s name='birthdate'}Geburtsdatum{/s}</label>
        </p>
    {/block}

    <div class="select-field" {if ! $isKlarnaBirthdayNeeded} style="display: none;"  {/if}>
        {block name="frontend_checkout_payment_payone_birthday_day_input"}
            <select name="moptPaymentData[mopt_payone__klarna_birthday]"
                    id="mopt_payone__klarna_birthday"
                    class="select--country is--required{if $error_flags.mopt_payone__klarna_birthday} has--error{/if}"
                    {if $payment_mean.id == $form_data.payment && $isKlarnaBirthdayNeeded}required="required" aria-required="true"{/if}
            >
                <option disabled="disabled" selected="selected" value="">--</option>
                {section name="birthdate" start=1 loop=32 step=1}
                    {$isSelected = $smarty.section.birthdate.index == $moptCreditCardCheckEnvironment.mopt_payone__klarna_birthday}
                    <option value="{$smarty.section.birthdate.index}" {if $isSelected}selected{/if}>
                        {$smarty.section.birthdate.index}
                    </option>
                {/section}
            </select>
        {/block}
    </div>

    <div class="select-field" {if ! $isKlarnaBirthdayNeeded} style="display: none;"  {/if}>
        {block name="frontend_checkout_payment_payone_birthday_month_input"}
            <select name="moptPaymentData[mopt_payone__klarna_birthmonth]"
                    id="mopt_payone__klarna_birthmonth"
                    class="select--country is--required{if $error_flags.mopt_payone__klarna_birthmonth} has--error{/if}"
                    {if $payment_mean.id == $form_data.payment && $isKlarnaBirthdayNeeded}required="required" aria-required="true"{/if}
            >
                <option disabled="disabled" selected="selected" value="">-</option>
                {section name="birthmonth" start=1 loop=13 step=1}
                    {$isSelected = $smarty.section.birthmonth.index == $moptCreditCardCheckEnvironment.mopt_payone__klarna_birthmonth}
                    <option value="{$smarty.section.birthmonth.index}" {if $isSelected}selected{/if}>
                        {$smarty.section.birthmonth.index}
                    </option>
                {/section}
            </select>
        {/block}
    </div>

    <div class="select-field" {if ! $isKlarnaBirthdayNeeded} style="display: none;"  {/if}>
        {block name="frontend_checkout_payment_payone_birthday_year_input"}
            <select name="moptPaymentData[mopt_payone__klarna_birthyear]"
                    id="mopt_payone__klarna_birthyear"
                    class="select--country is--required{if $error_flags.mopt_payone__klarna_birthyear} has--error{/if}"
                    {if $payment_mean.id == $form_data.payment && $isKlarnaBirthdayNeeded}required="required" aria-required="true"{/if}
            >
                <option disabled="disabled" selected="selected" value="">----</option>
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
