{extends file="parent:frontend/checkout/cart.tpl"}

{block name="frontend_checkout_actions_confirm"}
    {$smarty.block.parent}
    <div class="button--container right" style="margin-right: 10px">
        <div id="paydirekt-ex-btn"></div>
    </div>
    {include file="frontend/checkout/script-paydirekt.tpl"}
{/block}