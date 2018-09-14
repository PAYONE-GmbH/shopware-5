<script>
    window.onAmazonLoginReady = function () {
        amazon.Login.setClientId("{$payoneAmazonPayConfig->getClientId()}");
    };

    function offPaymentsWrapper(buttonDiv, popup, useHttpMode) {
        var authRequest;
        var shopUrl = '{url controller='moptPaymentAmazon' action='index'}';
        if (shopUrl.indexOf('http://') !== -1)
        {
            shopUrl = shopUrl.replace("http://", "https://");
            popup = false;
        }
        OffAmazonPayments.Button(buttonDiv, "{$payoneAmazonPayConfig->getSellerId()}", {
            type: "{$payoneAmazonPayConfig->getButtonType()}",
            color: "{$payoneAmazonPayConfig->getButtonColor()}",
            size: 'medium',
            language: "{$Locale|replace:"_":"-"}",

            authorization: function () {
                loginOptions = {
                    scope: 'profile payments:widget payments:shipping_address payments:billing_address',
                    popup: popup
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

    function forceDomReload()
    {
        // force redraw of parent element to fix safari issues:
        // see https://stackoverflow.com/questions/8840580/force-dom-redraw-refresh-on-chrome-macconsole.
        document.getElementById('LoginWithAmazon').style.display = 'none';
        document.getElementById('LoginWithAmazon').style.display = 'inline-block';
        document.getElementById('LoginWithAmazonBottom').style.display = 'none';
        document.getElementById('LoginWithAmazonBottom').style.display = 'inline-block';
    }
</script>

<script>
{if $moptAmazonLogout === true}
    window.onAmazonLoginReady = function () {
        amazon.Login.logout();
        amazon.Login.setClientId("{$payoneAmazonPayConfig->getClientId()}");
    };
{/if}
</script>
