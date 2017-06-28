<div class="debit">
  <p class="none">
    <label for="mopt_payone__debit_bankcountry">
      {s namespace='frontend/MoptPaymentPayone/payment' name='bankCountry'}Land{/s}
    </label>
    <select name="moptPaymentData[mopt_payone__debit_bankcountry]" id="mopt_payone__debit_bankcountry" size="1" 
            style="width:auto" class="{if $error_flags.mopt_payone__debit_bankcountry}instyle_error{/if}">
      <option value="not_choosen">
        {s namespace='frontend/MoptPaymentPayone/payment' name='selectValueLabel'}Bitte auswählen...{/s}
      </option>
      {foreach from=$moptPaymentConfigParams.moptDebitCountries item=moptCountry}
        <option value="{$moptCountry.countryiso}" 
          {if $form_data.mopt_payone__debit_bankcountry == $moptCountry.countryiso}selected="selected"{/if}>
          {$moptCountry.countryname}
        </option>
      {/foreach}
    </select>
  </p>
  <p class="none">
    <label for="mopt_payone__debit_bankaccountholder">
      {s namespace='frontend/MoptPaymentPayone/payment' name='bankAccoutHolder'}Kontoinhaber{/s}
    </label>
    <input name="moptPaymentData[mopt_payone__debit_bankaccountholder]" type="text" 
           id="mopt_payone__debit_bankaccountholder" value="{$form_data.mopt_payone__debit_bankaccountholder|escape}" 
           class="text {if $error_flags.mopt_payone__debit_bankaccountholder}instyle_error{/if}" />
  </p>
  <p class="none">
    <label for="mopt_payone__debit_iban">
      {s namespace='frontend/MoptPaymentPayone/payment' name='bankIBAN'}IBAN{/s}
    </label>
    <input name="moptPaymentData[mopt_payone__debit_iban]" type="text" id="mopt_payone__debit_iban" 
           value="{$form_data.mopt_payone__debit_iban|escape}" 
           class="text {if $error_flags.mopt_payone__debit_iban}instyle_error{/if}" />
  </p>
  <p class="none">
    <label for="mopt_payone__debit_bic">
      {s namespace='frontend/MoptPaymentPayone/payment' name='bankBIC'}BIC{/s}
    </label>
    <input name="moptPaymentData[mopt_payone__debit_bic]" type="text" id="mopt_payone__debit_bic" 
           value="{$form_data.mopt_payone__debit_bic|escape}" 
           class="text {if $error_flags.mopt_payone__debit_bic}instyle_error{/if}" />
  </p>
  {if $moptPaymentConfigParams.moptShowAccountnumber}
  <p class="description">
    {s namespace='frontend/MoptPaymentPayone/payment' name='debitDescription'}
    oder bezahlen Sie wie gewohnt mit Ihren bekannten Kontodaten (nur für Deutsche Kontoverbindungen).{/s}
  </p>
  <p class="none">
    <label for="mopt_payone__debit_bankaccount">
      {s namespace='frontend/MoptPaymentPayone/payment' name='bankAccountNumber'}Kontonummer{/s}
    </label>
    <input name="moptPaymentData[mopt_payone__debit_bankaccount]" type="text" id="mopt_payone__debit_bankaccount" 
           value="{$form_data.mopt_payone__debit_bankaccount|escape}" 
           class="text {if $error_flags.mopt_payone__debit_bankaccount}instyle_error{/if}" />
  </p>
  <p class="none">
    <label for="mopt_payone__debit_bankcode">
      {s namespace='frontend/MoptPaymentPayone/payment' name='bankCode'}Bankleitzahl{/s}
    </label>
    <input name="moptPaymentData[mopt_payone__debit_bankcode]" type="text" id="mopt_payone__debit_bankcode" 
           value="{$form_data.mopt_payone__debit_bankcode|escape}" 
           class="text {if $error_flags.mopt_payone__debit_bankcode}instyle_error{/if}" />
  </p>
  {/if}
</div>

<script type="text/javascript">
  $('#mopt_payone__debit_iban').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__debit_bic').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__debit_bankaccountholder').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  {if $moptPaymentConfigParams.moptShowAccountnumber}
    $('#mopt_payone__debit_bankaccount').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__debit_bankcode').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  {/if}
      
  $(document).ready(function() {
    $('#moptSavePaymentButton{$payment_mean.id}').attr('onClick', "");
    $( "#moptSavePaymentButton{$payment_mean.id}" ).click(function( event ) {
        var valid = moptValidateAndSaveDebit{$payment_mean.id}();
        if(!valid)
        {
            event.preventDefault();
        }
       else
       {
            MoptSubmitPaymentForm();
       }
    });
});
 
 function moptValidateAndSaveDebit{$payment_mean.id}() {
    {literal}
    var ibanbicReg =  /^[A-Z0-9 ]+$/;
    var numberReg =  /^[0-9 ]+$/;
    var bankCodeReg = /^(?:\s*[0-9]\s*){8}$/;
    var formNotValid = false;
    {/literal}

    $(".moptFormError").remove();
    
    $('#mopt_payone__debit_iban').removeClass('instyle_error');
    $('#mopt_payone__debit_bic').removeClass('instyle_error');
    
    
    {if $moptPaymentConfigParams.moptShowAccountnumber}
    $('#mopt_payone__debit_bankcode').removeClass('instyle_error');
    $('#mopt_payone__debit_bankaccount').removeClass('instyle_error');

    if($('#mopt_payone__debit_bankcode').val() && !bankCodeReg.test($('#mopt_payone__debit_bankcode').val())){
          $('#mopt_payone__debit_bankcode').addClass('instyle_error');
          $('#mopt_payone__debit_bankcode').parent().after('<div class="error moptFormError">{s namespace="frontend/MoptPaymentPayone/errorMessages" name="bankcodeFormField"}Die Bankleitzahl muss aus 8 Ziffern bestehen{/s}</div>');
          formNotValid = true;
      }
      
    if($('#mopt_payone__debit_bankaccount').val() && !numberReg.test($('#mopt_payone__debit_bankaccount').val())){
          $('#mopt_payone__debit_bankaccount').addClass('instyle_error');
          $('#mopt_payone__debit_bankaccount').parent().after('<div class="error moptFormError">{s namespace="frontend/MoptPaymentPayone/errorMessages" name="numberFormField"}Dieses Feld darf nur Zahlen enthalten{/s}</div>');
          formNotValid = true;
      }
     {/if}
         
         if($('#mopt_payone__debit_iban').val() && !ibanbicReg.test($('#mopt_payone__debit_iban').val())){
          $('#mopt_payone__debit_iban').addClass('instyle_error');
          $('#mopt_payone__debit_iban').parent().after('<div class="error moptFormError">{s namespace="frontend/MoptPaymentPayone/errorMessages" name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}</div>');
          formNotValid = true;
      }
      
    if($('#mopt_payone__debit_bic').val() && !ibanbicReg.test($('#mopt_payone__debit_bic').val())){
          $('#mopt_payone__debit_bic').addClass('instyle_error');
          $('#mopt_payone__debit_bic').parent().after('<div class="error moptFormError">{s namespace="frontend/MoptPaymentPayone/errorMessages" name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}</div>');
          formNotValid = true;
      }

    if(formNotValid)
    {
        return false;
    }

    return true;
  };
</script>