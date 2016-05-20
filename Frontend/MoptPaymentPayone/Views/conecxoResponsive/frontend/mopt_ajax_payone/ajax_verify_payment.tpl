<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
  <h4 class="modal-title" id="myModalLabel">
    {s namespace='frontend/MoptPaymentPayone/payment' name='confirmPaymentTitle'}Zahlart Bestätigen{/s}
  </h4>
</div>
<div class="modal-body">
  {s namespace='frontend/MoptPaymentPayone/payment' name='paymentChangedTitle'}Sie haben die Zahlart geändert{/s}</br>
  {s namespace='frontend/MoptPaymentPayone/payment' name='paymentNewPayment'}Neue Zahlart:{/s} <span 
    style="font-weight: bold;">{$moptSelectedPayment}</span></br>
  {s namespace='frontend/MoptPaymentPayone/payment' name='paymentSelectedPayment'}Bisher gewählte Zahlart:{/s} <span 
    style="font-weight: bold;">{$moptOriginalPayment}</span></br>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" onclick="reloadPage();" data-dismiss="modal">
    {s namespace='frontend/MoptPaymentPayone/payment' name='dontSaveNewButtonLabel'}Nicht übernehmen{/s}
  </button>
  <button type="button" class="btn btn-primary" onclick="savePayment();">
    {s namespace='frontend/MoptPaymentPayone/payment' name='saveNewButtonLabel'}Neue übernehmen{/s}
  </button>
</div>

<script type="text/javascript">
  <!--
  function savePayment() {
    if('{$moptCheckedId}' == 'payment_meanmopt_payone_creditcard')
    {
      checkCreditCard();
      return false;
    }
    else
    {
      //submit payment form
      $("form").each(function() {
        var me = this;
        var action = me.getAttribute('action');
        if (action.indexOf("savePayment") >= 0)
        {
          me.submit();  
        }
      });
    }
  }

  function reloadPage() {
    window.location = "{url controller=checkout action=confirm forceSecure}";
  }
  // -->
</script>