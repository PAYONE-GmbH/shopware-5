{extends file="parent:frontend/checkout/shipping_payment.tpl"}

{block name='frontend_account_payment_error_messages'}
    <div>
        {if $moptAmazonError}
            {include file="frontend/_includes/messages.tpl" type="error" content="{s name='amazonDeclined ' namespace='frontend/MoptPaymentPayone/errorMessages'}{/s}" bold=false}
        {/if}
    </div>
    {$smarty.block.parent}
{/block}

{block name="frontend_index_header_javascript_jquery"}
    {if $moptAmazonLogout === true}
        <script async="async"
            {if $payoneAmazonPayMode == 1} src='https://static-eu.payments-amazon.com/OffAmazonPayments/eur/lpa/js/Widgets.js'> {/if}
            {if $payoneAmazonPayMode == 0} src='https://static-eu.payments-amazon.com/OffAmazonPayments/eur/sandbox/lpa/js/Widgets.js'>{/if}
        </script>
        <script>
            window.onAmazonLoginReady = function () {
                amazon.Login.logout();
                console.log("Amazon Logout");
            };
        </script>
    {/if}
    {$smarty.block.parent}
{/block}
