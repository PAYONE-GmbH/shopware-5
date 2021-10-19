{extends file="parent:frontend/checkout/ajax_cart.tpl"}

{block name='frontend_checkout_ajax_cart_button_container'}
    {$smarty.block.parent}
    {if $moptCheckApplePaySupport}
        <script src="https://applepay.cdn-apple.com/jsapi/v1/apple-pay-sdk.js"></script>
        <script>
            var canMakePayments = false;
            if (window.ApplePaySession) {
                canMakePayments = ApplePaySession.canMakePayments();
            }
            $.ajax({
                url: '{url controller="MoptAjaxPayone" action="setApplePayDeviceSupport" forceSecure}',
                type: 'POST',
                dataType: 'json',
                data: {
                    'applePaySupported': canMakePayments
                }
            })
        </script>
    {/if}
{/block}