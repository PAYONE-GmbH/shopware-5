<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
  <h4 class="modal-title" id="myModalLabel">
    {s namespace='frontend/MoptPaymentPayone/payment' name='confirmAddressTitle'}Adresse Bestätigen{/s}
  </h4>
</div>
<div class="modal-body">
  <p class="none">
    <h3>
      {s namespace='frontend/MoptPaymentPayone/payment' name='originalAddressTitle'}Eingegebene Adresse:{/s}
    </h3>
      {$moptAddressCheckOriginalAddress.street}<br>
      {$moptAddressCheckOriginalAddress.zipcode}<br>
      {$moptAddressCheckOriginalAddress.city}
  </p>
  <p class="none" style="margin-top: 25px;">
    <h3>
      {s namespace='frontend/MoptPaymentPayone/payment' name='correctedAddressTitle'}Korrigierte Adresse:{/s}
    </h3>
      {$moptAddressCheckCorrectedAddress.street}<br>
      {$moptAddressCheckCorrectedAddress.zip}<br>
      {$moptAddressCheckCorrectedAddress.city}
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
  function saveOriginalAddress() {
      jQuery.post( '{url controller="moptAjaxPayone" action="saveOriginalAddress" forceSecure}' ,function() {
        window.location = "{url controller=account action=saveBilling sTarget=$moptAddressCheckTarget forceSecure}";
      });
    }

  function saveCorrectedAddress() {
      jQuery.post( '{url controller="moptAjaxPayone" action="saveCorrectedAddress" forceSecure}' ,function() {
        window.location = "{url controller=account action=saveBilling sTarget=$moptAddressCheckTarget forceSecure}";
      });
    }
  // -->
</script>