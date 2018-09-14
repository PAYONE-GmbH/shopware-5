{extends file="parent:frontend/checkout/cart.tpl"}

{block name="frontend_checkout_actions_confirm"}
    {$smarty.block.parent}
{if !$sMinimumSurcharge && !$sDispatchNoOrder}
<div class="button--container right" style="margin-right: 10px">
    <div id="LoginWithAmazon"></div>
</div>
{/if}

{include file="frontend/checkout/script-amazonpay.tpl"}

<script>
{if $smarty.server.REQUEST_SCHEME === 'https'}
    window.onAmazonPaymentsReady = function () {
        offPaymentsWrapper('LoginWithAmazon', true, false);
        offPaymentsWrapper('LoginWithAmazonBottom', true, false);
    };
{else}
    window.onAmazonPaymentsReady = function () {
        offPaymentsWrapper('LoginWithAmazon', false, true);
        offPaymentsWrapper('LoginWithAmazonBottom', false, true);
    };
{/if}
window.onload = function(){
    forceDomReload();
};
</script>

<script async="async"
    {if $payoneAmazonPayMode == 1} src='https://static-eu.payments-amazon.com/OffAmazonPayments/eur/lpa/js/Widgets.js'>{/if}
    {if $payoneAmazonPayMode == 0} src='https://static-eu.payments-amazon.com/OffAmazonPayments/eur/sandbox/lpa/js/Widgets.js'>{/if}
</script>

{/block}

{block name="frontend_checkout_actions_confirm_bottom_checkout"}
    {$smarty.block.parent}
    {if !$sMinimumSurcharge && !$sDispatchNoOrder}
        <div class="button--container right" style="margin-right: 10px">
            <div id="LoginWithAmazonBottom"></div>
        </div>
    {/if}
{/block}

{block name="frontend_checkout_actions_confirm_bottom_checkout"}
{$smarty.block.parent}
{if $smarty.server.REQUEST_SCHEME === 'https'}
{if !$sMinimumSurcharge && !$sDispatchNoOrder}
    <div class="button--container right" style="margin-right: 10px">
        <div id="LoginWithAmazonBottom"></div>
        <div class="clear"></div>
        <script>
            window.onAmazonLoginReady = function () {
                amazon.Login.setClientId("{$payoneAmazonPayConfig->getClientId()}");
            };
            window.onAmazonPaymentsReady = function () {
                var authRequest;
                OffAmazonPayments.Button('LoginWithAmazonBottom', "{$payoneAmazonPayConfig->getSellerId()}", {
                    type: "{$payoneAmazonPayConfig->getButtonType()}",
                    color: "{$payoneAmazonPayConfig->getButtonColor()}",
                    size: 'medium',
                    //language: "{$payoneAmazonPayConfig->getButtonLanguage()}",
                    language: "{$Locale|replace:"_":"-"}",

                    authorization: function () {
                        loginOptions = {
                            scope: 'profile payments:widget payments:shipping_address payments:billing_address',
                            popup: true
                        };
                        authRequest = amazon.Login.authorize(loginOptions, '{url controller='moptPaymentAmazon' action='index'}');
                    },
                    onError: function(error) {
                        alert("The following error occurred: "
                            + error.getErrorCode()
                            + ' - ' + error.getErrorMessage());
                    }
                });
            }
        </script>
        <script async="async"
                {if $payoneAmazonPayMode == 1} src='https://static-eu.payments-amazon.com/OffAmazonPayments/eur/lpa/js/Widgets.js'> {/if}
            {if $payoneAmazonPayMode == 0} src='https://static-eu.payments-amazon.com/OffAmazonPayments/eur/sandbox/lpa/js/Widgets.js'>{/if}
        </script>
    </div>
{/if}
{else}
<div class="button--container">
    <div id="LoginWithAmazonBottom"></div>
    <div class="clear"></div>
    <script>
        window.onAmazonLoginReady = function () {
            amazon.Login.setClientId("{$payoneAmazonPayConfig->getClientId()}");
        };
        window.onAmazonPaymentsReady = function () {
            var authRequest;
            var shopUrl = '{url controller='moptPaymentAmazon' action='index'}';
            shopUrl = shopUrl.replace("http://", "https://");
            OffAmazonPayments.Button('LoginWithAmazonBottom', "{$payoneAmazonPayConfig->getSellerId()}", {
                type: "{$payoneAmazonPayConfig->getButtonType()}",
                color: "{$payoneAmazonPayConfig->getButtonColor()}",
                size: 'medium',
                language: "{$Locale|replace:"_":"-"}",

                authorization: function () {
                    loginOptions = {
                        scope: 'profile payments:widget payments:shipping_address payments:billing_address',
                        popup: false
                    };
                    authRequest = amazon.Login.authorize(loginOptions, shopUrl);
                },
                onError: function(error) {
                    alert("The following error occurred: "
                        + error.getErrorCode()
                        + ' - ' + error.getErrorMessage());
                }
            });
        }
    </script>
    <script async="async"
            {if $payoneAmazonPayMode == 1} src='https://static-eu.payments-amazon.com/OffAmazonPayments/eur/lpa/js/Widgets.js'> {/if}
        {if $payoneAmazonPayMode == 0} src='https://static-eu.payments-amazon.com/OffAmazonPayments/eur/sandbox/lpa/js/Widgets.js'>{/if}
    </script>
    {/if}
    {/block}

{if $moptAmazonError}
    {block name="frontend_checkout_actions_checkout"}
        <a href="{url controller='checkout' action='shippingPayment'}"
           title="{"{s name='CheckoutActionsLinkProceedShort' namespace="frontend/checkout/actions"}{/s}"|escape}"
           class="btn btn--checkout-proceed is--primary right is--icon-right is--large">
            {s name="CheckoutActionsLinkProceedShort" namespace="frontend/checkout/actions"}{/s}
            <i class="icon--arrow-right"></i>
        </a>
    {/block}
    {block name="frontend_checkout_actions_confirm_bottom_checkout"}
        <a href="{url controller='checkout' action='shippingPayment'}"
           title="{"{s name='CheckoutActionsLinkProceedShort' namespace="frontend/checkout/actions"}{/s}"|escape}"
           class="btn btn--checkout-proceed is--primary right is--icon-right is--large">
            {s name="CheckoutActionsLinkProceedShort" namespace="frontend/checkout/actions"}{/s}
            <i class="icon--arrow-right"></i>
        </a>
    {/block}
{/if}

{block name='frontend_checkout_cart_error_messages'}
    <div>
        {if $moptAmazonError}
            {include file="frontend/_includes/messages.tpl" type="error" content="{s name='amazonDeclined ' namespace='frontend/MoptPaymentPayone/errorMessages'}{/s}" bold=false}
        {/if}
    </div>
{$smarty.block.parent}
{/block}

{* remove position:absolute on inquiry button  *}
{block name="frontend_checkout_actions_inquiry"}
    <a href="{$sInquiryLink}"
       title="{"{s name='CheckoutActionsLinkOffer' namespace="frontend/checkout/actions"}{/s}"|escape}"
       class="btn btn--inquiry is--large is--full is--center" style="position: relative">
        {s name="CheckoutActionsLinkOffer" namespace="frontend/checkout/actions"}{/s}
    </a>
{/block}
