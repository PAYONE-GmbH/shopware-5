{namespace name='frontend/MoptPaymentPayone/payment'}

<div class="payment--form-group">

    <input name="moptPaymentData[mopt_payone__giropay_iban]"
           type="text"
           id="mopt_payone__giropay_iban"
           {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
           placeholder="{s name='bankIBAN'}IBAN{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
           value="{$form_data.mopt_payone__giropay_iban|escape}"
           data-moptIbanWrongCharacterMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="moptIbanWrongCharacterMessage"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}"
           data-moptIbanWrongLengthMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="moptIbanWrongLengthMessage"}Bitte prüfen Sie die Länge der IBAN{/s}"
           data-moptIbanWrongCecksumMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="moptIbanWrongCecksumMessage"}Die Prüfsumme der IBAN ist falsch{/s}"
           class="payment--field is--required{if $error_flags.mopt_payone__giropay_iban} has--error{/if} moptPayoneIbanBic" />

    <input name="moptPaymentData[mopt_payone__giropay_bic]"
           type="text"
           id="mopt_payone__giropay_bic"
           {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
           placeholder="{s name='bankBIC'}BIC{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
           value="{$form_data.mopt_payone__giropay_bic|escape}" 
           data-moptIbanErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}" 
           class="payment--field is--required{if $error_flags.mopt_payone__giropay_bic} has--error{/if} moptPayoneIbanBic" />
</div>
