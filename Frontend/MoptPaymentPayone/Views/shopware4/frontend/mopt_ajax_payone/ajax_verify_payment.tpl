<div class="register">
  <div class="heading">
    <h2>
      {s namespace='frontend/MoptPaymentPayone/payment' name='confirmPaymentTitle'}Zahlart Bestätigen{/s}
    </h2>
  </div>
  <div style="padding: 0px 0px 0px 23px;">
    {s namespace='frontend/MoptPaymentPayone/payment' name='paymentChangedTitle'}Sie haben die Zahlart geändert{/s}
    </br>
    {s namespace='frontend/MoptPaymentPayone/payment' name='paymentNewPayment'}Neue Zahlart:{/s} <span
      style="font-weight: bold;">{$moptSelectedPayment}</span></br>
    {s namespace='frontend/MoptPaymentPayone/payment' name='paymentSelectedPayment'}Bisher gewählte Zahlart:{/s} <span 
      style="font-weight: bold;">{$moptOriginalPayment}</span></br>

    <p class="none" style="margin-top: 25px;">
      <input  class="button-middle large left" type="submit" onclick="savePayment();" 
        value="{s namespace='frontend/MoptPaymentPayone/payment' name='saveNewButtonLabel'}Neue übernehmen{/s}"/>
      <input style="margin-bottom: 25px;" class="button-middle large left" type="submit" onclick="reloadPage();" 
        value="{s namespace='frontend/MoptPaymentPayone/payment' name='dontSaveNewButtonLabel'}Nicht übernehmen{/s}"/>
    </p>
  </div>
</div>

<script type="text/javascript">
  <!--
  function savePayment() 
  {
    if('{$moptCheckedId}' === 'payment_meanmopt_payone_creditcard')
    {
      checkCreditCard();
      return false;
    }
    else
    {
      //submit payment form
      var forms = $('.payment').get();
      
      $.each(forms, function() 
      {
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