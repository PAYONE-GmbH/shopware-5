<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
  <h4 class="modal-title" id="myModalLabel">
    {s namespace='frontend/MoptPaymentPayone/payment' name='confirmShippingAddressTitle'}Lieferadresse Bestätigen{/s}
  </h4>
</div>
<div class="modal-body">
  <p class="none">
    <h3>
      {s namespace='frontend/MoptPaymentPayone/payment' name='originalShippingAddressTitle'}Eingegebene Lieferadresse:{/s}
    </h3>
      {$moptShippingAddressCheckOriginalAddress.street}<br>
      {$moptShippingAddressCheckOriginalAddress.zipcode}<br>
      {$moptShippingAddressCheckOriginalAddress.city}
  </p>
  <p class="none" style="margin-top: 25px;">
    <h3>
      {s namespace='frontend/MoptPaymentPayone/payment' name='correctedShippingAddressTitle'}Korrigierte Lieferadresse:{/s}
    </h3>
      {$moptShippingAddressCheckCorrectedAddress.street}<br>
      {$moptShippingAddressCheckCorrectedAddress.zip}<br>
      {$moptShippingAddressCheckCorrectedAddress.city}
  </p>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" onclick="saveOriginalAddress();" data-dismiss="modal">
    {s namespace='frontend/MoptPaymentPayone/payment' name='dontSaveDataButtonLabel'}Daten nicht übernehmen{/s}
  </button>
  <button type="button" class="btn btn-primary" onclick="saveCorrectedAddress();">
    {s namespace='frontend/MoptPaymentPayone/payment' name='saveDataButtonLabel'}Daten übernehmen{/s}
  </button>
</div>

<script type="text/javascript">
  <!--
  function saveOriginalAddress() 
  {
    jQuery.post( '{url controller="moptAjaxPayone" action="saveOriginalShippingAddress" forceSecure}' ,function() {
      window.location = "{url controller=account action=saveShipping sTarget=$moptShippingAddressCheckTarget forceSecure}";
    });
    }

  function saveCorrectedAddress() 
  {
    jQuery.post( '{url controller="moptAjaxPayone" action="saveCorrectedShippingAddress" forceSecure}' ,function() {
      window.location = "{url controller=account action=saveShipping sTarget=$moptShippingAddressCheckTarget forceSecure}";
    });
    }
  // -->
</script>