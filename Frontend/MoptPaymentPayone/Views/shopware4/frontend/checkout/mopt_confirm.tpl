{extends file="frontend/checkout/confirm.tpl"}

{block name="frontend_index_content" append}
{if $moptAddressCheckNeedsUserVerification}
<script type="text/javascript">
  $(document).ready(function() {
    $.post('{url controller=moptAjaxPayone action=ajaxVerifyAddress forceSecure}', function (data) {
      $.modal(data, '', {
        'position': 'fixed',
        'width': 500,
        'height': 500
      }).find('.close').remove();
    });
  });
</script>
{/if}
{/block}

{* Payment selection *}
{block name='frontend_checkout_confirm_payment' append}

<script type="text/javascript">
  $(document).ready(function() {
    $('[name="register[payment]"]').removeClass('auto_submit');
    
    var myRadio =  $('input[name="register[payment]"]');
    var orgValue = myRadio.filter('[checked="checked"]').val();
    var orgLabel = $('input[name="register[payment]"]:checked + label').text();
    
    $('#basketButton').parents('form:first').bind('submit', function() {
      // get orginal checked payment method
      var checkedValue = myRadio.filter(':checked').val();
      var checkedLabel = $('input[name="register[payment]"]:checked + label').text();
      var checkedId = $('input[name="register[payment]"]:checked').attr('id');
      
      if(checkedValue != orgValue)
      {
        //show dialog
        $.post("{url controller=moptAjaxPayone action=ajaxVerifyPayment forceSecure}?moptSelectedPayment="+checkedLabel+"&moptOriginalPayment="+orgLabel+"&moptCheckedId="+checkedId+"", function (data) {
          $.modal(data, '', {
            'position': 'fixed',
            'width': 500,
            'height': 500
          }).find('.close').remove();
        });

        return false;
      }
      else
      {
        $('#basketButton').attr('disabled','disabled');
        return true;
      }
    });
    {if $moptAgbChecked}
      $('#sAGB').prop('checked', true);
      $('#sAGB').attr('checked','checked');
      $('input[name=sAGB]').val(1);
    {/if}
  });
</script>

{/block}

{block name="frontend_checkout_confirm_footer" prepend}
  {if $moptMandateData.mopt_payone__showMandateText}
    <div class="grid_16 first">
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
    <div class="error center bold">
      {s name='mandateAgreementError' namespace='frontend/MoptPaymentPayone/payment'}Bitte bestätigen Sie die Erteilung des Mandats.{/s}
		</div>
  {/if}
{/block}