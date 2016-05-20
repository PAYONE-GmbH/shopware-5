<div class="debit">
  <div class="form-group {if $error_flags.mopt_payone__klarna_inst_telephone}has-error{/if}">
      <label for="mopt_payone__klarna_inst_telephone" class="col-lg-4 control-label">
        {s namespace='frontend/MoptPaymentPayone/payment' name='telephoneNumber'}Telefonnummer{/s}
      </label>

      <div class="col-lg-6">
          <input name="moptPaymentData[mopt_payone__klarna_inst_telephone]" type="text" id="mopt_payone__klarna_inst_telephone" 
                 value="{$form_data.mopt_payone__klarna_inst_telephone|escape}" class="form-control"/>
      </div>
  </div>
  
  <div class="form-group {if $error_flags.mopt_payone__klarna_inst_birthday 
              || $error_flags.mopt_payone__klarna_inst_birthmonth 
              || $error_flags.mopt_payone__klarna_inst_birthyear}has-error{/if}">
      <label for="mopt_payone__klarna_inst_birthday" class="col-lg-4 control-label">
        {s namespace='frontend/MoptPaymentPayone/payment' name='birthdate'}Geburtsdatum{/s}
      </label>

      <div class="col-lg-6">
        <select style="width:auto;" id="mopt_payone__klarna_inst_birthday" 
                name="moptPaymentData[mopt_payone__klarna_inst_birthday]" class="form-control">
				<option value="">--</option>	
				{section name="birthdate" start=1 loop=32 step=1}
				<option value="{$smarty.section.birthdate.index}" 
            {if $smarty.section.birthdate.index eq $moptCreditCardCheckEnvironment.mopt_payone__klarna_inst_birthday}
            selected{/if}>{$smarty.section.birthdate.index}
        </option>
				{/section}
				</select>
				
				<select "style="width:auto;" id="mopt_payone__klarna_inst_birthmonth"  
          name="moptPaymentData[mopt_payone__klarna_inst_birthmonth]" class="form-control">
				<option value="">-</option>	
				{section name="birthmonth" start=1 loop=13 step=1}
				<option value="{$smarty.section.birthmonth.index}" 
          {if $smarty.section.birthmonth.index eq $moptCreditCardCheckEnvironment.mopt_payone__klarna_inst_birthmonth}
          selected{/if}>{$smarty.section.birthmonth.index}</option>
				{/section}
				</select>
				
				<select style="width:auto;" id="mopt_payone__klarna_inst_birthyear"  
                name="moptPaymentData[mopt_payone__klarna_inst_birthyear]" class="form-control">
				<option value="">----</option>	
				{section name="birthyear" loop=2000 max=100 step=-1}
				<option value="{$smarty.section.birthyear.index}" 
          {if $smarty.section.birthyear.index eq $moptCreditCardCheckEnvironment.mopt_payone__klarna_inst_birthyear}
          selected{/if}>{$smarty.section.birthyear.index}</option>
				{/section}
				</select>
      </div>
  </div>
  
  <div class="form-group {if $error_flags.mopt_payone__klarna_inst_agreement}has-error{/if}">
      <div class="col-lg-6">
        <input name="moptPaymentData[mopt_payone__klarna_inst_agreement]" type="checkbox" 
         id="mopt_payone__klarna_inst_agreement" {if $form_data.mopt_payone__klarna_inst_agreement eq "on"}
         checked{/if} class="text {if $error_flags.mopt_payone__klarna_inst_agreement}instyle_error{/if}" style="width:auto;"/>
        {$moptCreditCardCheckEnvironment.moptKlarnaInformation.consent}
      </div>
  </div>
    
  <p class="description">
    {$moptCreditCardCheckEnvironment.moptKlarnaInformation.legalTerm}
  </p>
  
</div>
<script type="text/javascript">
  $('#mopt_payone__klarna_inst_telephone').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__klarna_inst_birthyear').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__klarna_inst_birthmonth').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__klarna_inst_birthday').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__klarna_inst_agreement').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
</script>