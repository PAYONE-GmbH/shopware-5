{namespace name='frontend/MoptPaymentPayone/payment'}
{$index = "{$id}_bic"}
<input type="text" name="moptPaymentData[{$id}_bic]"
       {if $moptRequired}required="required" aria-required="true"{/if}
       id="{$id}_bic" aria-required="false"
       placeholder="{s name='bankBIC'}BIC{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
       value="{$form_data.$index|escape}"
       data-moptIbanErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}"
       class="payment--field moptPayoneBic{if $error_flags.$index} has--error{/if}"
/>