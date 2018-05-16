{extends file="parent:frontend/checkout/ajax_cart.tpl"}


{block name='frontend_checkout_ajax_cart_button_container'}
    {$smarty.block.parent}
    {if $smarty.server.REQUEST_SCHEME === 'https' && $smarty.server.HTTP_REFERER|strpos:'https://'=== 0}
    <div class="button--container">
        <div id="LoginWithAmazon"></div>
        <div class="clear"></div>
            <script>
                window.onAmazonLoginReady = function () {
                    amazon.Login.setClientId("{$payoneAmazonPayConfig->getClientId()}");
                };
                window.onAmazonPaymentsReady = function () {
                    var authRequest;
                    OffAmazonPayments.Button('LoginWithAmazon', "{$payoneAmazonPayConfig->getSellerId()}", {
                        type: "{$payoneAmazonPayConfig->getButtonType()}",
                        color: "{$payoneAmazonPayConfig->getButtonColor()}",
                        size: 'medium',
                        language: "{$Locale|replace:"_":"-"}",

                        authorization: function () {
                            loginOptions = {
                                scope: 'profile payments:widget payments:shipping_address payments:billing_address',
                                popup: true
                            };
                            authRequest = amazon.Login.authorize(loginOptions, '{url controller='moptPaymentAmazon' action='index'}');
                        },
                        onError: function(error) {
                            alert("The following error occurred: "
                                    + error.getErrorCode()
                                    + ' - ' + error.getErrorMessage());
                        }
                    });
                }
            </script>


        <script async="async"
            {if $payoneAmazonPayMode == 1} src='https://static-eu.payments-amazon.com/OffAmazonPayments/de/lpa/js/Widgets.js'> {/if}
            {if $payoneAmazonPayMode == 0} src='https://static-eu.payments-amazon.com/OffAmazonPayments/de/sandbox/lpa/js/Widgets.js'>{/if}
        </script>
    </div>
    {else}
        <div class="button--container">
            <div id="LoginWithAmazon"></div>
            <div class="clear"></div>
            <script>
                window.onAmazonLoginReady = function () {
                    amazon.Login.setClientId("{$payoneAmazonPayConfig->getClientId()}");
                };
                window.onAmazonPaymentsReady = function () {
                    var authRequest;
                    var shopUrl = '{url controller='moptPaymentAmazon' action='index'}';
                    shopUrl = shopUrl.replace("http://", "https://");
                    OffAmazonPayments.Button('LoginWithAmazon', "{$payoneAmazonPayConfig->getSellerId()}", {
                        type: "{$payoneAmazonPayConfig->getButtonType()}",
                        color: "{$payoneAmazonPayConfig->getButtonColor()}",
                        size: 'medium',
                        language: "{$Locale|replace:"_":"-"}",

                        authorization: function () {
                            loginOptions = {
                                scope: 'profile payments:widget payments:shipping_address payments:billing_address',
                                popup: false
                            };
                            authRequest = amazon.Login.authorize(loginOptions, shopUrl);
                        },
                        onError: function(error) {
                            alert("The following error occurred: "
                                + error.getErrorCode()
                                + ' - ' + error.getErrorMessage());
                        }
                    });
                }
            </script>
            <script async="async"
                {if $payoneAmazonPayMode == 1} src='https://static-eu.payments-amazon.com/OffAmazonPayments/de/lpa/js/Widgets.js'> {/if}
                {if $payoneAmazonPayMode == 0} src='https://static-eu.payments-amazon.com/OffAmazonPayments/de/sandbox/lpa/js/Widgets.js'>{/if}
            </script>
        </div>
    {/if}


{/block}

