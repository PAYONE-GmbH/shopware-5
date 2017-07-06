{extends file="frontend/checkout/cart.tpl"}

{block name="frontend_checkout_actions_confirm" append}
    {if !$sMinimumSurcharge && !$sDispatchNoOrder}
        <a href="{url controller='moptPaymentEcs' action='initPayment'}">
            <img src="{$moptPaypalShortcutImgURL}" />
        </a>
        <div class="clear"></div>
    {/if}

{/block}