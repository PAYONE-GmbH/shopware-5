<div class="debit">
  {if $moptPaymentConfigParams.moptIsSwiss}
    <div class="form-group {if $error_flags.mopt_payone__sofort_bankaccount}has-error{/if}">
        <label for="mopt_payone__sofort_bankaccount" class="col-lg-4 control-label">
          {s namespace='frontend/MoptPaymentPayone/payment' name='bankAccountNumber'}Kontonummer{/s}
        </label>

        <div class="col-lg-6">
            <input name="moptPaymentData[mopt_payone__sofort_bankaccount]" type="text" 
                   id="mopt_payone__sofort_bankaccount" value="{$form_data.mopt_payone__sofort_bankaccount|escape}" 
                   class="form-control"/>
        </div>
    </div>

    <div class="form-group {if $error_flags.mopt_payone__sofort_bankcode}has-error{/if}">
        <label for="mopt_payone__sofort_bankcode" class="col-lg-4 control-label">
          {s namespace='frontend/MoptPaymentPayone/payment' name='bankCode'}Bankleitzahl{/s}
        </label>

        <div class="col-lg-6">
            <input name="moptPaymentData[mopt_payone__sofort_bankcode]" type="text" id="mopt_payone__sofort_bankcode" 
                   value="{$form_data.mopt_payone__sofort_bankcode|escape}" class="form-control"/>
        </div>
    </div>
  {else}
        {if $moptPaymentConfigParams.moptShowSofortIbanBic}
    <div class="form-group {if $error_flags.mopt_payone__sofort_iban}has-error{/if}">
        <label for="mopt_payone__sofort_iban" class="col-lg-4 control-label">
          {s namespace='frontend/MoptPaymentPayone/payment' name='bankIBAN'}IBAN{/s}
        </label>

        <div class="col-lg-6">
            <input name="moptPaymentData[mopt_payone__sofort_iban]" type="text" id="mopt_payone__sofort_iban" 
                   value="{$form_data.mopt_payone__sofort_iban|escape}" class="form-control"/>
        </div>
    </div>

    <div class="form-group {if $error_flags.mopt_payone__sofort_bic}has-error{/if}">
        <label for="mopt_payone__sofort_bic" class="col-lg-4 control-label">
          {s namespace='frontend/MoptPaymentPayone/payment' name='bankBIC'}BIC{/s}
        </label>

        <div class="col-lg-6">
            <input name="moptPaymentData[mopt_payone__sofort_bic]" type="text" id="mopt_payone__sofort_bic" 
                   value="{$form_data.mopt_payone__sofort_bic|escape}" class="form-control"/>
        </div>
    </div>
        {/if}
    <input class="is--hidden" type="text" name="moptPaymentData[mopt_payone__sofort_show_sofort_iban_bic]" id="moptPaymentData[mopt_payone__sofort_show_sofort_iban_bic]" value="{$moptPaymentConfigParams.moptShowSofortIbanBic}">              
  {/if}

  <input type="hidden" name="moptPaymentData[mopt_payone__onlinebanktransfertype]" 
         id="mopt_payone__onlinebanktransfertype" value="PNT"/>
  <input type="hidden" name="moptPaymentData[mopt_payone__sofort_bankcountry]" 
         id="mopt_payone__sofort_bankcountry" value="{$sUserData.additional.country.countryiso}"/>
</div>

<script type="text/javascript">
  {if $moptPaymentConfigParams.moptIsSwiss}
    $('#mopt_payone__sofort_bankaccount').focus(function () {
        $('#payment_mean{$payment_mean.id}').attr('checked', true);
        $('#moptSavePayment{$payment_mean.id}').slideDown();
        $('input[type="radio"]:not(:checked)').trigger('change');
    });

    $('#mopt_payone__sofort_bankcode').focus(function () {
        $('#payment_mean{$payment_mean.id}').attr('checked', true);
        $('#moptSavePayment{$payment_mean.id}').slideDown();
        $('input[type="radio"]:not(:checked)').trigger('change');
    });
  {else}
    {if $moptPaymentConfigParams.moptShowSofortIbanBic}
    $('#mopt_payone__sofort_iban').focus(function() {
      $('#payment_mean{$payment_mean.id}').attr('checked',true);
      $('#moptSavePayment{$payment_mean.id}').slideDown();
      $('input[type="radio"]:not(:checked)').trigger('change');
    });

    $('#mopt_payone__sofort_bic').focus(function() {
      $('#payment_mean{$payment_mean.id}').attr('checked',true);
      $('#moptSavePayment{$payment_mean.id}').slideDown();
      $('input[type="radio"]:not(:checked)').trigger('change');
    });
      {/if}
  {/if}
      
  $(document).ready(function() {
    $('#moptSavePaymentButton{$payment_mean.id}').attr('onClick', "");
    $( "#moptSavePaymentButton{$payment_mean.id}" ).click(function( event ) {
        var valid = moptValidateAndSaveSofort{$payment_mean.id}();
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
 
 function moptValidateAndSaveSofort{$payment_mean.id}() {
    var ibanbicReg =  /^[A-Z0-9]+$/;
    var numberReg =  /^[0-9]+$/;
    var bankCodeReg = /^[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]$/;
    var formNotValid = false;

    $(".moptFormError").remove();
    
    {if $moptPaymentConfigParams.moptIsSwiss}
    $('#mopt_payone__sofort_bankcode').removeClass('instyle_error');
    $('#mopt_payone__sofort_bankaccount').removeClass('instyle_error');

    if(!bankCodeReg.test($('#mopt_payone__sofort_bankcode').val())){
          $('#mopt_payone__sofort_bankcode').addClass('instyle_error');
          $('#mopt_payone__sofort_bankcode').parent().after('<div class="error moptFormError">{s namespace="frontend/MoptPaymentPayone/errorMessages" name="bankcodeFormField"}Die Bankleitzahl muss aus 8 Ziffern bestehen{/s}</div>');
          formNotValid = true;
      }
      
    if(!numberReg.test($('#mopt_payone__sofort_bankaccount').val())){
          $('#mopt_payone__sofort_bankaccount').addClass('instyle_error');
          $('#mopt_payone__sofort_bankaccount').parent().after('<div class="error moptFormError">{s namespace="frontend/MoptPaymentPayone/errorMessages" name="numberFormField"}Dieses Feld darf nur Zahlen enthalten{/s}</div>');
          formNotValid = true;
      }
     {else}
    $('#mopt_payone__sofort_iban').removeClass('instyle_error');
    $('#mopt_payone__sofort_bic').removeClass('instyle_error');
         
         
     if(!ibanbicReg.test($('#mopt_payone__sofort_iban').val())){
          $('#mopt_payone__sofort_iban').addClass('instyle_error');
          $('#mopt_payone__sofort_iban').parent().after('<div class="error moptFormError">{s namespace="frontend/MoptPaymentPayone/errorMessages" name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}</div>');
          formNotValid = true;
      }
      
    if(!ibanbicReg.test($('#mopt_payone__sofort_bic').val())){
          $('#mopt_payone__sofort_bic').addClass('instyle_error');
          $('#mopt_payone__sofort_bic').parent().after('<div class="error moptFormError">{s namespace="frontend/MoptPaymentPayone/errorMessages" name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}</div>');
          formNotValid = true;
      }
     {/if}

              if (formNotValid)
      {
      return false;
      }

      return true;
      };
</script>