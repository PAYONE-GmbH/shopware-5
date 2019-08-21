{extends file="parent:frontend/checkout/ajax_cart.tpl"}

{block name='frontend_checkout_ajax_cart_button_container'}
    {$smarty.block.parent}
    {if $sBasket.content && $moptPayDirektShortcutImgURL}
        <div class="button--container">
            <a href="{url controller='MoptPaymentPayDirekt' action='initPayment'}">
                <img src="{$moptPayDirektShortcutImgURL}" />
            </a>
        </div>
    {/if}
{/block}