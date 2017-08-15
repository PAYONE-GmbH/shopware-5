{extends file="parent:frontend/checkout/cart.tpl"}

{block name="frontend_checkout_actions_confirm" append}
    {if !$sMinimumSurcharge && !$sDispatchNoOrder}
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
                        //language: "{$payoneAmazonPayConfig->getButtonLanguage()}",
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
                    src='https://static-eu.payments-amazon.com/OffAmazonPayments/de/sandbox/lpa/js/Widgets.js'>
            </script>
        </div>
    {/if}

{/block}