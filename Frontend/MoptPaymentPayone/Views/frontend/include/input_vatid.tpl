{namespace name='frontend/MoptPaymentPayone/payment'}
{$index = "{$id}_company_trade_registry_number"}
<input type="text" name="moptPaymentData[{$id}_company_trade_registry_number]"
       id="{$id}_company_trade_registry_number" aria-required="false"
       placeholder="{s name='companyTradeRegistryNumber'}Handelsregisternummer{/s}"
       class="payment--field{if $error_flags.$index} has--error{/if}"
       value="{if $moptCreditCardCheckEnvironment.sFormData.$index}{$moptCreditCardCheckEnvironment.sFormData.$index}{else}{$sUserData.billingaddress.vatId}{/if}"
/>