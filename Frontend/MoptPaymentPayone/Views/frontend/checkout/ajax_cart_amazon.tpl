{extends file="parent:frontend/checkout/ajax_cart.tpl"}

{block name='frontend_checkout_ajax_cart_button_container'}
    {$smarty.block.parent}
    <div class="button--container">
        <div id="LoginWithAmazonAjaxCart"></div>
    </div>

    {include file="frontend/checkout/script-amazonpay.tpl"}

    {block name='frontend_checkout_ajax_cart_payone_amazonpay_script'}

        <script>
        {if $smarty.server.REQUEST_SCHEME === 'https' || $smarty.server.HTTPS === 'on' || $smarty.server.HTTP_HTTPS === 'on' || $smarty.server.HTTP_X_FORWARDED_PROTO === 'https'}
            window.onAmazonPaymentsReady = function () {
            offPaymentsWrapper('LoginWithAmazonAjaxCart', true, false);
            };
        {else}
            window.onAmazonPaymentsReady = function () {
            offPaymentsWrapper('LoginWithAmazonAjaxCart', false, true);
            };
        {/if}
        </script>
        <script async
                {if $payoneAmazonPayMode == 1} src='https://static-eu.payments-amazon.com/OffAmazonPayments/eur/lpa/js/Widgets.js'>{/if}
            {if $payoneAmazonPayMode == 0} src='https://static-eu.payments-amazon.com/OffAmazonPayments/eur/sandbox/lpa/js/Widgets.js'>{/if}
        </script>
    {/block}
{/block}
