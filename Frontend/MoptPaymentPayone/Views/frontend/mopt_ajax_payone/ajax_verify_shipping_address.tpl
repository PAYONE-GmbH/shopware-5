<div class="js--modal sizing--content" style="width: 600px; height: auto; display: block; opacity: 1;">
    <div class="header">
        <div class="title">
            {s namespace='frontend/MoptPaymentPayone/payment' name='confirmShippingAddressTitle'}Lieferadresse Bestätigen{/s}
        </div>
    </div>
    <div class="content" style="padding: 25px;">
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

        <p class="none" style="margin-top: 25px;">
            <input class="btn" type="submit" onclick="saveCorrectedAddress();" 
                   value="{s namespace='frontend/MoptPaymentPayone/payment' name='saveDataButtonLabel'}Daten übernehmen{/s}"/>
            <input style="margin-bottom: 25px;" class="btn" type="submit" onclick="saveOriginalAddress();" 
                   value="{s namespace='frontend/MoptPaymentPayone/payment' name='dontSaveDataButtonLabel'}Daten nicht übernehmen{/s}"/>
        </p>
    </div>
</div>
<div class="js--overlay is--open" style="opacity: 0.8; display: block"></div>

<script type="text/javascript">
    //<!--
    function saveOriginalAddress() {
        $.post('{url controller="moptAjaxPayone" action="saveOriginalShippingAddress" forceSecure}', function () {
            window.location = "{url controller=account action=saveShipping sTarget=$moptShippingAddressCheckTarget forceSecure}";
        });
    }

    function saveCorrectedAddress() {
        $.post('{url controller="moptAjaxPayone" action="saveCorrectedShippingAddress" forceSecure}', function () {
            window.location = "{url controller=account action=saveShipping sTarget=$moptShippingAddressCheckTarget forceSecure}";
        });
    }
    // -->
</script>
