<div class="js--modal sizing--content" style="width: 600px; height: auto; display: block; opacity: 1;">
    <div class="header">
        <div class="title">
            {s namespace='frontend/MoptPaymentPayone/payment' name='confirmAddressTitle'}Adresse Bestätigen{/s}
        </div>
    </div>
    <div class="content" style="padding: 25px;">
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
    function saveOriginalAddress()
    {
        $.post('{url controller="moptAjaxPayone" action="saveOriginalAddress" forceSecure}', function ()
        {
            // Check this for SW 5.2 and 5.3 saveBilling is gone
            // why redirect to account controller anway? Straight to checkout !
            window.location = "{url controller=checkout forceSecure}";
        });
    }

    function saveCorrectedAddress()
    {
        $.post('{url controller="moptAjaxPayone" action="saveCorrectedAddress" forceSecure}', function ()
        {
            // Cceck this for SW 5.2 and 5.3 saveBilling is gone
            window.location = "{url controller=checkout forceSecure}";
        });
    }
    // -->
</script>
