{namespace name='frontend/MoptPaymentPayone/payment'}

{if $applepayNotConfiguredError}
<div class="payment--form-group">
    <div id="mopt-applepay-device-support"></div>
    <div>
        {s namespace='frontend/MoptPaymentPayone/errorMessages' name='applepayNotConfiguredError'}Apple Pay ist noch nicht konfiguriert. Bitte konfigurieren Sie MerchantId, Zertifikat und Private Key{/s}"
    </div>
</div>
{/if}