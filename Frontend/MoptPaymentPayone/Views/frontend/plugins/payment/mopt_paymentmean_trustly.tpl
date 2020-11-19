{namespace name='frontend/MoptPaymentPayone/payment'}

{if $payment_mean.id == $form_data.payment}
    {assign var="moptRequired" value=1}
{else}
    {assign var="moptRequired" value=0}
{/if}

<div class="payment--form-group">
    {if $moptCreditCardCheckEnvironment.moptTrustlyShowIbanBic}

    <input name="moptPaymentData[mopt_payone__trustly_iban]"
           type="text"
           id="mopt_payone__trustly_iban"
           {if $moptRequired}required="required" aria-required="true"{/if}
           placeholder="{s name='bankIBAN'}IBAN{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
           value="{$form_data.mopt_payone__trustly_iban|escape}"
           data-moptIbanErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}"
           class="payment--field {if $moptRequired}is--required{/if}{if $error_flags.mopt_payone__trustly_iban} has--error{/if} moptPayoneIbanBic" />
    <input name="moptPaymentData[mopt_payone__trustly_bic]"
           type="text"
           id="mopt_payone__trustly_bic"
           {if $moptRequired}required="required" aria-required="true"{/if}
           placeholder="{s name='bankBIC'}BIC{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
           value="{$form_data.mopt_payone__trustly_bic|escape}"
           data-moptIbanErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}"
           class="payment--field {if $moptRequired}is--required{/if}{if $error_flags.mopt_payone__trustly_bic} has--error{/if} moptPayoneIbanBic" />

    {block name='frontend_checkout_payment_required'}
        {* Required fields hint *}
        <div class="register--required-info">
            {s name='RegisterPersonalRequiredText' namespace='frontend/register/personal_fieldset'}{/s}
        </div>
    {/block}
    {/if}
    <input class="is--hidden" type="text" name="moptPaymentData[mopt_payone__trustly_show_iban_bic]" id="moptPaymentData[mopt_payone__trustly_show_iban_bic]" value="{$moptCreditCardCheckEnvironment.moptTrustlyShowIbanBic}">
</div>
