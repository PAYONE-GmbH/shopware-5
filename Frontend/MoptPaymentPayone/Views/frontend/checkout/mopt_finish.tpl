{extends file="parent:frontend/checkout/finish.tpl"}

{block name="frontend_index_header_css_screen"}
    {$smarty.block.parent}
    {if $moptBarzahlenCode}
        <link rel="stylesheet" type="text/css" href="{link file="frontend/_resources/styles/barzahlen.css"}" />
    {/if}
{/block}

{block name="frontend_checkout_finish_teaser_actions"}
    {$smarty.block.parent}
    {if $moptPaymentConfigParams.moptMandateDownloadEnabled}
        <p class="teaser--actions">
            {strip}
                <a href="{url controller=moptAjaxPayone action=downloadMandate forceSecure}" 
                   class="btn is--primary teaser--btn-print" 
                   target="_blank" 
                   title="{"{s name='mandateDownload' namespace='frontend/MoptPaymentPayone/payment'}Download Mandat{/s}"|escape}">
                    {s name='mandateDownload' namespace='frontend/MoptPaymentPayone/payment'}Download Mandat{/s}
                </a>
            {/strip}
        </p>
    {/if}
    {if $moptBarzahlenCode}
        <div class="barzahlencode">
        {$moptBarzahlenCode}
        </div>
    {/if}
    {if $moptAmazonAsyncAuthMessage}
        <div class="amazonAsyncAuth">
           {$moptAmazonAsyncAuthMessage}
        </div>
    {/if}

{/block}

{block name="frontend_index_header_javascript_jquery" append}
    <script async="async"
            src='https://static-eu.payments-amazon.com/OffAmazonPayments/de/sandbox/lpa/js/Widgets.js'>
    </script>
    <script>
        window.onAmazonLoginReady = function () {
            amazon.Login.logout();
            console.log("Amazon Logout");
        };
    </script>
{/block}
