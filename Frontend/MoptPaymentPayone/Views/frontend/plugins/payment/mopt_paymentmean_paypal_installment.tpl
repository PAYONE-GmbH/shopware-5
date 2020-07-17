{namespace name='frontend/MoptPaymentPayone/payment'}

{if $payment_mean.id == $form_data.payment}
    {assign var="moptRequired" value=1}
{else}
    {assign var="moptRequired" value=0}
{/if}

{if $payment_mean.id == $form_data.payment}
<div class="payment--form-group">
    {if $moptBasketChanged}
        <div id="paypal_overlay_installment_redirect_notice" class="js--modal content" style="width:40%; height:40%; opacity: 0.9; margin: 75px auto;">
            <a href="#" onclick="removePaypalOverlayInstallmentRedirectNotice();
                    return false;" style="float:right;font-weight:bold;">Fenster schliessen</a><br><br>
            {$moptOverlayRedirectNotice}
        </div>
        <div id="paypal_overlay_installment_redirect_notice_bg" class="js--overlay is--open" style="opacity: 0.8;"></div>
    {/if}
</div>

<script type="text/javascript">
    function removePaypalOverlayInstallmentRedirectNotice() {
        document.getElementById('paypal_overlay_installment_redirect_notice').style.display = "none";
        document.getElementById('paypal_overlay_installment_redirect_notice_bg').style.display = "none";
    }
</script>
{/if}