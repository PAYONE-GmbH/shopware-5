<div class="debit">
    <div class="form-group {if $error_flags.mopt_payone__giropay_iban}has-error{/if}">
        <label for="mopt_payone__giropay_iban" class="col-lg-4 control-label">
          {s namespace='frontend/MoptPaymentPayone/payment' name='bankIBAN'}IBAN{/s}
        </label>

        <div class="col-lg-6">
            <input name="moptPaymentData[mopt_payone__giropay_iban]" type="text" 
                   id="mopt_payone__giropay_iban" value="{$form_data.mopt_payone__giropay_iban|escape}" class="form-control"/>
        </div>
    </div>
    <div class="form-group {if $error_flags.mopt_payone__giropay_bic}has-error{/if}">
        <label for="mopt_payone__giropay_bic" class="col-lg-4 control-label">
          {s namespace='frontend/MoptPaymentPayone/payment' name='bankBIC'}BIC{/s}
        </label>

        <div class="col-lg-6">
            <input name="moptPaymentData[mopt_payone__giropay_bic]" type="text" id="mopt_payone__giropay_bic" 
                   value="{$form_data.mopt_payone__giropay_bic|escape}" class="form-control"/>
        </div>
    </div>

    <input type="hidden" name="moptPaymentData[mopt_payone__onlinebanktransfertype]" 
           id="mopt_payone__onlinebanktransfertype" value="GPY"/>
    <input type="hidden" name="moptPaymentData[mopt_payone__giropay_bankcountry]" 
           id="mopt_payone__giropay_bankcountry" value="DE"/>
</div>

<script type="text/javascript">
  $('#mopt_payone__giropay_iban').focus(function() 
  {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
  $('#mopt_payone__giropay_bic').focus(function() 
  {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
  
   $(document).ready(function() {
    $('#moptSavePaymentButton{$payment_mean.id}').attr('onClick', "");
    $( "#moptSavePaymentButton{$payment_mean.id}" ).click(function( event ) {
        var valid = moptValidateAndSaveGiropay{$payment_mean.id}();
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
 
 function moptValidateAndSaveGiropay{$payment_mean.id}() {
    var ibanbicReg =  /^[A-Z0-9]+$/;
    var formNotValid = false;

    $(".moptFormError").remove();
    
    $('#mopt_payone__giropay_iban').removeClass('instyle_error');
    $('#mopt_payone__giropay_bic').removeClass('instyle_error');
    
     if(!ibanbicReg.test($('#mopt_payone__giropay_iban').val())){
      $('#mopt_payone__giropay_iban').addClass('instyle_error');
      $('#mopt_payone__giropay_iban').parent().after('<div class="error moptFormError">{s namespace="frontend/MoptPaymentPayone/errorMessages" name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}</div>');
      formNotValid = true;
      }
      
    if(!ibanbicReg.test($('#mopt_payone__giropay_bic').val())){
          $('#mopt_payone__giropay_bic').addClass('instyle_error');
          $('#mopt_payone__giropay_bic').parent().after('<div class="error moptFormError">{s namespace="frontend/MoptPaymentPayone/errorMessages" name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}</div>');
               formNotValid = true;
       }

       if (formNotValid)
       {
       return false;
       }

       return true;
       };
</script>