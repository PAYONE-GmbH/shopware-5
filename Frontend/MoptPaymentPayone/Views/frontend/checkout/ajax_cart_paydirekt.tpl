{extends file="parent:frontend/checkout/ajax_cart.tpl"}

{block name='frontend_checkout_ajax_cart_button_container'}
    {$smarty.block.parent}
    <div class="button--container right" style="margin-right: 10px">
        <div id="paydirekt-ex-btn">123</div>
    </div>

    {include file="frontend/checkout/script-paydirekt.tpl"}
<script>
    console.log('123');
</script>
{/block}
