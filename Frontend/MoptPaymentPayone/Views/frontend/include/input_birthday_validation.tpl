{namespace name='frontend/MoptPaymentPayone/payment'}
<input class="is--hidden validate-18-years" type="text" name="moptPaymentData[{$id}_birthdaydate]" id="{$id}_birthdaydate" value="{$moptCreditCardCheckEnvironment.birthday}">
<div id="{$id}-hint-18-years" class="is--hidden">{s name="birthdayValidation" }Sie müssen mindestens 18 Jahre alt sein, um diese Zahlart verwenden zu können.{/s}</div>