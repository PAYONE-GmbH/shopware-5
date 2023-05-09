{extends file="parent:frontend/checkout/ajax_cart.tpl"}        

{block name='frontend_checkout_ajax_cart_button_container'}
    {$smarty.block.parent}
    {if $sBasket.content && $moptPaypalShortcutImgURL}
        <div class="button--container">
            <a href="{url controller='moptPaymentEcs' action='initPayment' shipping=$sShippingcosts}">
                <img src="{$moptPaypalShortcutImgURL}" />
            </a>
        </div>
    {/if}
{/block}