{extends file="frontend/register/confirm_payment.tpl"}

{block name="frontend_checkout_payment_fieldset_template" append}
{if $payment_mean.id != 'mopt_payone_creditcard'}
<div id="moptSavePayment{$payment_mean.id}" class="grid_14 bankdata" 
     style="clear: both; margin-right: 0px; margin-left: auto; display: none;">
  <input id="moptSavePaymentButton{$payment_mean.id}" type="submit"
         onclick="MoptSubmitPaymentForm();" class="btn btn-primary" 
         value="{s namespace='frontend/MoptPaymentPayone/payment' name='savePayment'}Zahlart speichern{/s}"/>
</div>

<script type="text/javascript">
  $('#payment_mean{$payment_mean.id}').change(function() {
    if($('#payment_mean{$payment_mean.id}').attr('checked') == 'checked')
    {
      $('#moptSavePayment{$payment_mean.id}').slideDown();
      $('input[type="radio"]:not(:checked)').trigger('change');
    }
    else
    {
      $('#moptSavePayment{$payment_mean.id}').slideUp();
    }
      
  });
</script>
{/if}

<script type="text/javascript">
  function MoptSubmitPaymentForm() {
    $("form").each(function() {
      var me = this;
      var action = me.getAttribute('action');
      if (action.indexOf("savePayment") >= 0)
      {
        $('[name="sourceCheckoutConfirm"]').val(0);
        me.submit();  
      }
    });
  };
</script>
{/block}

{* overwrite shopware template changes *}
{block name="frontend_checkout_payment_fieldset_template"}
<div class="payment_logo_{$payment_mean.name}"></div>
  {if "frontend/plugins/payment/`$payment_mean.template`"|template_exists}
    {if $payment_mean.id eq $sPayment.id}
      {include file="frontend/plugins/payment/`$payment_mean.template`" form_data=$sPayment.data}
    {else}
      {include file="frontend/plugins/payment/`$payment_mean.template`"}
    {/if}
{/if}
{/block}