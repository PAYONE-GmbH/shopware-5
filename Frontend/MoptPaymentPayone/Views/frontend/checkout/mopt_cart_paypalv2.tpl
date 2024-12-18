{extends file="parent:frontend/checkout/cart.tpl"}

{block name="frontend_checkout_actions_confirm"}
    {$smarty.block.parent}
    {if !$sMinimumSurcharge && !$sDispatchNoOrder}
        <div class="button--container right" style="width: 30%;margin-right:20px">
            <div id="paypal-button-container"></div>
            {include file="frontend/checkout/script-paypalv2.tpl"}
        </div>
        <div class="clear"></div>
    {/if}
{/block}
