{namespace name='frontend/MoptPaymentPayone/payment'}

<div class="payment--form-group">

        <input name="moptPaymentData[mopt_payone__bancontact_iban]"
               type="text"
               id="mopt_payone__bancontact_iban"
               {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
               placeholder="{s name='bankIBAN'}IBAN{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
               value="{$form_data.mopt_payone__bancontact_iban|escape}"
               data-moptIbanErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}"
               class="payment--field is--required{if $error_flags.mopt_payone__bancontact_iban} has--error{/if} moptPayoneIbanBic" />

        <input name="moptPaymentData[mopt_payone__bancontact_bic]"
               type="text"
               id="mopt_payone__bancontact_bic"
               {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
               placeholder="{s name='bankBIC'}bankBIC{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
               value="{$form_data.mopt_payone__bancontact_bic|escape}"
               data-moptIbanErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}"
               class="payment--field is--required{if $error_flags.mopt_payone__sofort_bic} has--error{/if} moptPayoneIbanBic" />
</div>
