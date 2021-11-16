{extends file="parent:frontend/checkout/finish.tpl"}

{block name="frontend_index_header_css_screen"}
    {$smarty.block.parent}
    {if $moptBarzahlenCode}
        <link rel="stylesheet" type="text/css" href="{link file="frontend/_resources/styles/barzahlen.css"}" />
    {/if}
{/block}

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

{block name='frontend_checkout_cart_footer_field_labels_taxes'}
        {$smarty.block.parent}
        {if $sUserData.additional.payment.name == 'mopt_payone__fin_paypal_installment'}
            <li class="list--entry block-group entry--totalnet">
                <div class="entry--label block" style="padding-top: 15px">
                    {s name='financecost' namespace="frontend/MoptPaymentPayone/payment"}Finanzierungskosten{/s}:
                </div>
                <div class="entry--value block is--no-star" style="padding-top: 15px">
                    {$Installment.total_interest_value|number_format:2:",":"."} {$Installment.total_interest_currency}
                </div>
            </li>
            <li class="list--entry block-group entry--total">
                <div class="entry--label block">
                    {s name='financecosttotal' namespace="frontend/MoptPaymentPayone/payment"}Gesamtbetrag (mit Finanzierungskosten){/s}:
                </div>
                <div class="entry--value block is--no-star">
                    {$Installment.total_cost_value|number_format:2:",":"."} {$Installment.total_cost_currency}
                </div>
            </li>
        {/if}
{/block}

{block name="frontend_index_header_javascript_jquery" append}
    {if $sUserData.additional.payment.name == 'mopt_payone__ewallet_amazon_pay'}
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
{/block}
