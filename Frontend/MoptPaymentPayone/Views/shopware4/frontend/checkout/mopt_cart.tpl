{extends file="frontend/checkout/cart.tpl"}

{block name="frontend_checkout_actions_confirm" append}
    {if !$sMinimumSurcharge && !$sDispatchNoOrder}
        <a href="{url controller='moptPaymentEcs' action='initPayment'}">
            {if $moptPaypalShortcutImgURL}
                <img src="{$moptPaypalShortcutImgURL}" />
            {else}
                <img src="{link file='frontend/_resources/images/btn_xpressCheckout.gif'}" />
            {/if}
        </a>
        <div class="clear"></div>
    {/if}
    
{/block}