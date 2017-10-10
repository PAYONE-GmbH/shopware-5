<div class="debit">
  <p class="none">
    <label for="mopt_payone__klarna_telephone">
      {s namespace='frontend/MoptPaymentPayone/payment' name='telephoneNumber'}Telefonnummer{/s}
    </label>
    <input name="moptPaymentData[mopt_payone__klarna_telephone]" type="text" id="mopt_payone__klarna_telephone" 
           value="{$moptCreditCardCheckEnvironment.mopt_payone__klarna_telephone|escape}" 
           class="text {if $error_flags.mopt_payone__klarna_telephone}instyle_error{/if}" />
  </p>
  <p class="none">
				<label for="mopt_payone__klarna_birthday">
          {s namespace='frontend/MoptPaymentPayone/payment' name='birthdate'}Geburtsdatum{/s}
        </label>
				<select class="{if $error_flags.mopt_payone__klarna_birthday}instyle_error{/if}" style="width:auto;" 
                id="mopt_payone__klarna_birthday" name="moptPaymentData[mopt_payone__klarna_birthday]">
				<option value="">--</option>	
				{section name="birthdate" start=1 loop=32 step=1}
					<option value="{$smarty.section.birthdate.index}" 
          {if $smarty.section.birthdate.index eq $moptCreditCardCheckEnvironment.mopt_payone__klarna_birthday}
            selected
          {/if}>
          {$smarty.section.birthdate.index}</option>
				{/section}
				</select>
				
				<select class="{if $error_flags.mopt_payone__klarna_birthmonth}instyle_error{/if}"style="width:auto;" 
                id="mopt_payone__klarna_birthmonth"  name="moptPaymentData[mopt_payone__klarna_birthmonth]">
				<option value="">-</option>	
				{section name="birthmonth" start=1 loop=13 step=1}
					<option value="{$smarty.section.birthmonth.index}" 
          {if $smarty.section.birthmonth.index eq $moptCreditCardCheckEnvironment.mopt_payone__klarna_birthmonth}
            selected
          {/if}>
          {$smarty.section.birthmonth.index}</option>
				{/section}
				</select>
				
				<select class="{if $error_flags.mopt_payone__klarna_birthyear}instyle_error{/if}" style="width:auto;" 
                id="mopt_payone__klarna_birthyear"  name="moptPaymentData[mopt_payone__klarna_birthyear]">
				<option value="">----</option>	
				{section name="birthyear" loop=2000 max=100 step=-1}
					<option value="{$smarty.section.birthyear.index}" 
          {if $smarty.section.birthyear.index eq $moptCreditCardCheckEnvironment.mopt_payone__klarna_birthyear}
            selected
          {/if}>
          {$smarty.section.birthyear.index}</option>
				{/section}
				</select>
  </p>
  <p class="description {if $error_flags.mopt_payone__klarna_agreement}instyle_error{/if}">
    <input name="moptPaymentData[mopt_payone__klarna_agreement]" type="checkbox" id="mopt_payone__klarna_agreement" 
           {if $form_data.mopt_payone__klarna_agreement eq "on"}checked{/if} 
           class="text {if $error_flags.mopt_payone__klarna_agreement}instyle_error{/if}" style="width:auto;"/>
    {$moptCreditCardCheckEnvironment.moptKlarnaInformation.consent}
  <p class="description">{$moptCreditCardCheckEnvironment.moptKlarnaInformation.legalTerm}</p>
  </p>
</div>

<script type="text/javascript">
  $('#mopt_payone__klarna_telephone').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__klarna_birthyear').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__klarna_birthmonth').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__klarna_birthday').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__klarna_agreement').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
</script>