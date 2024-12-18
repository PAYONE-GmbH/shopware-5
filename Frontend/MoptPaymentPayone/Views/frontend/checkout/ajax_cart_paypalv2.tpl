{extends file="parent:frontend/checkout/ajax_cart.tpl"}

{block name='frontend_checkout_ajax_cart_button_container'}
    {$smarty.block.parent}
    <div class="button--container">
        <div id="paypal-button-container" style="width: 90%"></div>
    </div>
    {include file="frontend/checkout/script-paypalv2.tpl"}
    <script>
        triggerPayPalButtonRender('paypal-button-container')
    </script>
{/block}
