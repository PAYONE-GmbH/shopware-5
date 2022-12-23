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
{if $smarty.server.REQUEST_SCHEME === 'https' || $smarty.server.HTTPS === 'on' || $smarty.server.HTTP_HTTPS === 'on' || $smarty.server.HTTP_X_FORWARDED_PROTO === 'https'}
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
        {if $moptAmazonError == 'ConfirmOrderReference'}
            {include file="frontend/_includes/messages.tpl" type="error" content="{s name='amazonConfirmOrderReferenceFailed' namespace='frontend/MoptPaymentPayone/errorMessages'}{/s}" bold=false}
        {elseif $moptAmazonErrorMessage }
            {include file="frontend/_includes/messages.tpl" type="error" content="$moptAmazonErrorMessage" bold=false}
        {elseif $moptAmazonError || $moptAmazonErrorCode }
            {include file="frontend/_includes/messages.tpl" type="error" content="{s name='generalErrorMessage' namespace='frontend/MoptPaymentPayone/errorMessages'}{/s}" bold=false}
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
