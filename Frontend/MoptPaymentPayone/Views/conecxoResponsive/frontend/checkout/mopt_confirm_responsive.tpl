{extends file="frontend/checkout/confirm.tpl"}

{block name="frontend_index_content" append}
{if $moptAddressCheckNeedsUserVerification}
<div id="moptAddressCheckNeedsUserVerificationModal" 
     class="modal fade" 
     tabindex="-1" 
     role="dialog"
     aria-labelledby="myModalLabel" 
     aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content"></div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() 
  {
    $.post('{url controller=moptAjaxPayone action=ajaxVerifyAddress forceSecure}', function (data) 
    {
      $('#moptAddressCheckNeedsUserVerificationModal .modal-content').html(data);
      {literal}
      $('#moptAddressCheckNeedsUserVerificationModal').modal({show:true});
      {/literal}
    });
  });
</script>
{/if}
{/block}

{* Payment selection *}
{block name='frontend_checkout_confirm_payment' append}

<div id="moptCheckPaymentMethodModal" 
     class="modal fade" 
     tabindex="-1" 
     role="dialog" 
     aria-labelledby="myModalLabel" 
     aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content"></div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() {
    $('[name="register[payment]"]').removeClass('auto_submit');
    
    var myRadio =  $('input[name="register[payment]"]');
    var orgValue = myRadio.filter('[checked="checked"]').val();
    var orgId = myRadio.filter('[checked="checked"]').attr('id');
    var orgLabel = $("label[for='"+ orgId + "']").text();
    
    $('#basketButton').parents('form:first').bind('submit', function() {
      // get orginal checked payment method
      var checkedValue = myRadio.filter(':checked').val();
      var checkedId = $('input[name="register[payment]"]:checked').attr('id');
      var checkedLabel = $("label[for='"+ checkedId + "']").text();
      
      if(checkedValue != orgValue)
      {
        //show dialog
       $.ajax({
            type: "POST",
            url:"{url controller=moptAjaxPayone action=ajaxVerifyPayment forceSecure}",
            {literal}
            data:{moptSelectedPayment: checkedLabel, moptOriginalPayment: orgLabel, moptCheckedId: checkedId}
            {/literal}
        }).done(function (data) {
            $('#moptCheckPaymentMethodModal .modal-content').html(data);
            {literal}
            $('#moptCheckPaymentMethodModal').modal({show:true});
            {/literal}	  
        });

        return false;
      }
      else
      {
        $('#basketButton').attr('disabled','disabled');
        return true;
      }
    });
    
  });
</script>
{/block}

{block name="frontend_checkout_confirm_footer" prepend}
  {if $moptMandateData.mopt_payone__showMandateText}
    <div class="col-lg-6">
      <div class="row-fluid" style="overflow:scroll; border:1px solid #ccc; padding:10px; height:200px;"> 
        {$moptMandateData.mopt_payone__mandateText}
      </div>
        <div class="clear">&nbsp;</div>
      <div class="row-fluid"> 
        <label class="control-label" for="mandate_status"  style="float:left; padding-right:10px;">
          {s name='mandateIAgree' namespace='frontend/MoptPaymentPayone/payment'}Ich möchte das Mandat erteilen{/s}
          <br />
          {s name='mandateElectronicSubmission' namespace='frontend/MoptPaymentPayone/payment'}(elektronische Übermittlung){/s}
        </label>
        <input type="checkbox" id="mandate_status" name="mandate_status"/>
      </div>
    </div>
    <div class="clear">&nbsp;</div>
  {/if}
{/block}

{block name="frontend_checkout_confirm_error_messages" prepend}
  {if $moptMandateAgreementError}
    <div class="alert alert-danger">
      {s name='mandateAgreementError' namespace='frontend/MoptPaymentPayone/payment'}Bitte bestätigen Sie die Erteilung des Mandats.{/s}
    </div>
  {/if}
{/block}