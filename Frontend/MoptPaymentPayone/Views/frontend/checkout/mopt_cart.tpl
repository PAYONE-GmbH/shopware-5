{extends file="parent:frontend/checkout/cart.tpl"}

{block name="frontend_checkout_actions_confirm"}
    {$smarty.block.parent}
    {if !$sMinimumSurcharge && !$sDispatchNoOrder}
        <a href="{url controller='moptPaymentEcs' action='initPayment'}">
            <img src="{$moptPaypalShortcutImgURL}" />
        </a>
        <div class="clear"></div>
    {/if}

{/block}