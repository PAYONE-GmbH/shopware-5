{namespace name='frontend/MoptPaymentPayone/payment'}
{$index = "{$id}_telephone"}
<input type="text" name="moptPaymentData[{$id}_telephone]"
       {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
       id="{$id}_telephone" aria-required="false"
       placeholder="{s name='telephoneNumber'}Telefonnummer{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
       value="{if $moptCreditCardCheckEnvironment.sFormData.$index|escape}{$moptCreditCardCheckEnvironment.sFormData.$index}{else}{$sUserData.billingaddress.phone|escape}{/if}"
       class="payment--field is--required{if $error_flags.$index} has--error{/if}"
/>