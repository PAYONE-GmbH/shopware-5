{namespace name='frontend/MoptPaymentPayone/payment'}
{$index = "{$id}_iban"}
<input type="text" name="moptPaymentData[{$id}_iban]"
       {if $moptRequired}required="required" aria-required="true"{/if}
       id="{$id}_iban" aria-required="false"
       placeholder="{s name='bankIBAN'}IBAN{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
       value="{$form_data.$index|escape}"
       data-moptIbanWrongCharacterMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="moptIbanWrongCharacterMessage"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}"
       data-moptIbanWrongLengthMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="moptIbanWrongLengthMessage"}Bitte prüfen Sie die Länge der IBAN{/s}"
       data-moptIbanWrongCecksumMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="moptIbanWrongCecksumMessage"}Die Prüfsumme der IBAN ist falsch{/s}"
       class="payment--field moptPayoneIban{if $error_flags.$index} has--error{/if}"
/>