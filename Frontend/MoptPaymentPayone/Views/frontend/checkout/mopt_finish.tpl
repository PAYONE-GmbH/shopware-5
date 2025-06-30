{extends file="parent:frontend/checkout/finish.tpl"}

{block name="frontend_checkout_finish_teaser_actions"}
    {$smarty.block.parent}
    {if $moptPaymentConfigParams.moptMandateDownloadEnabled && $sUserData.additional.payment.name|strstr:"mopt_payone__acc_debitnote"}
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
    {if $moptAmazonAsyncAuthMessage}
        <div class="amazonAsyncAuth">
           {$moptAmazonAsyncAuthMessage}
        </div>
    {/if}

{/block}

{block name="frontend_index_header_javascript_jquery" append}
    {if $sUserData.additional.payment.name|strstr:"mopt_payone__ewallet_amazon_pay"}
        <script async="async"
                {if $payoneAmazonPayMode == 1} src='https://static-eu.payments-amazon.com/OffAmazonPayments/eur/lpa/js/Widgets.js'> {/if}
            {if $payoneAmazonPayMode == 0} src='https://static-eu.payments-amazon.com/OffAmazonPayments/eur/sandbox/lpa/js/Widgets.js'>{/if}
        </script>
        <script>
            window.onAmazonLoginReady = function () {
                amazon.Login.logout();
            };
        </script>
    {/if}
{/block}
