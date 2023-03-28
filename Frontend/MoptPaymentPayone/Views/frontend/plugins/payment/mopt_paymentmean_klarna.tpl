{namespace name='frontend/MoptPaymentPayone/payment'}

{if $moptKlarnaAddressChanged}
    <div id="mopt_payone__klarna_redirect_notice" class="js--modal content"
         style="width:40%; height:40%; opacity: 0.9; margin: 75px auto;">
        <a href="#" onclick="removeKlarnaOverlayRedirectNotice();
            return false;" style="float:right;font-weight:bold;">Fenster schliessen</a><br><br>
        {$moptOverlayRedirectNotice}
    </div>
    <div id="mopt_payone__klarna_redirect_notice_bg" class="js--overlay is--open" style="opacity: 0.8;"></div>
    <script type="text/javascript">
        function removeKlarnaOverlayRedirectNotice() {
            document.getElementById('mopt_payone__klarna_redirect_notice').style.display = "none";
            document.getElementById('mopt_payone__klarna_redirect_notice_bg').style.display = "none";
        }
    </script>
{/if}

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
         data-customer-national-identification-number="{$customerNationalIdentificationNumber}"
         data-purchase-currency="{$purchaseCurrency}"
         data-locale="{$locale}"
         data-store-authorization-token--URL="{url controller="MoptAjaxPayone" action="storeAuthorizationToken" forceSecure}"
         data-start-klarna-session--URL="{url controller="MoptAjaxPayone" action="startKlarnaSession" forceSecure}"
         data-unset-session-vars--URL="{url controller="MoptAjaxPayone" action="unsetSessionVars" forceSecure}"
         data-klarna-grouped="{$moptPayoneKlarnaGrouped}"
    ></div>

    {if $moptPayoneKlarnaGrouped}
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
                        {if $form_data.mopt_payone__klarna_paymentname == ''}selected="selected"{/if}
                >
                    {s name='klarna-paymentType'}Klarna Zahlart{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}
                </option>
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
    {else}
        <div id="mopt_payone__klarna_paymenttype_wrap">
            <label for="mopt_payone__klarna_paymenttype"></label>
            <input type="text" hidden
                   name="moptPaymentData[mopt_payone__klarna_paymenttype]"
                   id="mopt_payone__klarna_paymenttype"
                   value="{$moptCreditCardCheckEnvironment.mopt_payone_klarna_financingtype}"
                    {if $payment_mean.id == $form_data.payment }
                        required="required"
                        aria-required="true"
                    {/if}
            >
            <input type="text" hidden name="moptPaymentData[mopt_payone_klarna_paymentid]"
                   id="mopt_payone_klarna_paymentid"
                   value="{$moptCreditCardCheckEnvironment.mopt_payone_klarna_paymentid}"
            >
        </div>
    {/if}

    <div class="payment--form-group" style="margin-bottom: 15px">
        {block name="frontend_checkout_payment_payone_telephone_label"}
            <p class="none" {if ! $isKlarnaTelephoneNeeded} style="display: none;" {/if}
               id="mopt_payone__klarna_telephone_label">
                <label for="mopt_payone__klarna_telephone">{s name='klarnaTelephoneLabel'}Telefonnummer{/s}</label>
            </p>
        {/block}

        {block name="frontend_checkout_payment_payone_telephone_input"}
            <input name="moptPaymentData[mopt_payone__klarna_telephone]"
                   type="text" {if ! $isKlarnaTelephoneNeeded} style="display: none;" {/if}
                   id="mopt_payone__klarna_telephone"
                   class="{if $isKlarnaTelephoneNeeded}is--required {/if} {if $error_flags.mopt_payone__klarna_telephone} has--error{/if} payment--field moptPayoneTelephone"
                   placeholder="{s name='telephoneNumber'}Telefonnummer{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                    {if $payment_mean.id == $form_data.payment && $isKlarnaTelephoneNeeded}} required="required" aria-required="true"{/if}
                   value="{$moptCreditCardCheckEnvironment.mopt_payone__klarna_telephone|escape}"
                   data-moptPhoneNumberErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="moptPhoneNumberErrorMessage"}Die Telefonnummer darf nur Zahlen enthalten und muss mit + oder 0 beginnen{/s}"
                   data-moptPhoneNumberLengthErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="moptPhoneNumberLengthErrorMessage"}Die Telefonnummer muss zwischen 5 und 16 Zeichen lang sein{/s}"
            />
        {/block}

        {block name="frontend_checkout_payment_payone_personalId_label"}
            <p class="none" {if ! $isKlarnaPersonalIdNeeded} style="display: none;" {/if}>
                <label for="mopt_payone__klarna_personalId">{s name='klarnaPersonalIdLabel'}{/s}Personal Id</label>
            </p>
        {/block}

        {block name="frontend_checkout_payment_payone_personalId_input"}
            <input name="moptPaymentData[mopt_payone__klarna_personalId]"
                   type="text" {if ! $isKlarnaPersonalIdNeeded} style="display: none;" {/if}
                   id="mopt_payone__klarna_personalId"
                   class="payment--field {if $isKlarnaPersonalIdNeeded}is--required{/if} {if $error_flags.mopt_payone__klarna_personalId} has--error{/if}"
                   placeholder="{s name='personalId'}Personal Id{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
                    {if $payment_mean.id == $form_data.payment && $isKlarnaPersonalIdNeeded}required="required" aria-required="true"{/if}
                   value="{$moptCreditCardCheckEnvironment.mopt_payone__klarna_personalId|escape}"
            />
        {/block}

        {block name="frontend_checkout_payment_payone_birthday_label"}
            <p class="none" {if ! $isKlarnaBirthdayNeeded} style="display: none;" {/if}>
                <label for="mopt_payone__klarna_birthday">{s name='birthdate'}Geburtsdatum{/s}</label>
            </p>
        {/block}

        <div class="select-field" {if ! $isKlarnaBirthdayNeeded} style="display: none;" {/if}>
            {block name="frontend_checkout_payment_payone_birthday_day_input"}
                <select name="moptPaymentData[mopt_payone__klarna_birthday]"
                        id="mopt_payone__klarna_birthday"
                        class="select--country is--required{if $error_flags.mopt_payone__klarna_birthday} has--error{/if}"
                        {if $payment_mean.id == $form_data.payment && $isKlarnaBirthdayNeeded}required="required"
                        aria-required="true"{/if}
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

        <div class="select-field" {if ! $isKlarnaBirthdayNeeded} style="display: none;" {/if}>
            {block name="frontend_checkout_payment_payone_birthday_month_input"}
                <select name="moptPaymentData[mopt_payone__klarna_birthmonth]"
                        id="mopt_payone__klarna_birthmonth"
                        class="select--country is--required{if $error_flags.mopt_payone__klarna_birthmonth} has--error{/if}"
                        {if $payment_mean.id == $form_data.payment && $isKlarnaBirthdayNeeded}required="required"
                        aria-required="true"{/if}
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

        <div class="select-field" {if ! $isKlarnaBirthdayNeeded} style="display: none;" {/if}>
            {block name="frontend_checkout_payment_payone_birthday_year_input"}
                <select name="moptPaymentData[mopt_payone__klarna_birthyear]"
                        id="mopt_payone__klarna_birthyear"
                        class="select--country is--required{if $error_flags.mopt_payone__klarna_birthyear} has--error{/if}"
                        {if $payment_mean.id == $form_data.payment && $isKlarnaBirthdayNeeded}required="required"
                        aria-required="true"{/if}
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
    </div>

    <div id="mopt_payone__klarna_payments_widget_container" style="margin-top: 15px"></div>

    <div id="payone-klarna-error" style="display: none;">
        <div class="alert is--error is--rounded">
            <div class="alert--icon">
                <i class="icon--element icon--warning"></i>
            </div>
            <div id="payone-klarna-error-message" class="alert--content">
            </div>
        </div>
    </div>
    <script>
        window.klarnaAsyncCallback = function () {
            window.PayoneKlarna = Klarna;
        };
    </script>
    <script src="https://x.klarnacdn.net/kp/lib/v1/api.js" async></script>
</div>
