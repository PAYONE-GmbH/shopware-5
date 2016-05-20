{extends file="parent:frontend/checkout/ajax_cart.tpl"}        

{block name='frontend_checkout_ajax_cart_button_container' append}
    {if $sBasket.content && $moptPaypalShortcutImgURL}
        <div class="button--container">
            <a href="{url controller='moptPaymentEcs' action='initPayment'}">
                <img src="{$moptPaypalShortcutImgURL}" />
            </a>
        </div>
    {/if}
{/block}