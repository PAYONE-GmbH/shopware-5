<script>
    /**
     * register listener for performing order reference confirmation
     *
     * listening on button click would bypass form validation
     */
    function prepareOrderReferenceConfirmation() {

        $('#confirm--form').on('submit', function(e) {
            var sellerId = '{$payoneAmazonPayConfig->getSellerId()}';
            var orderReferenceId = moptAmazonReferenceId;

            e.preventDefault();

            OffAmazonPayments.initConfirmationFlow(sellerId, orderReferenceId, function(confirmationFlow) {
                $.loadingIndicator.open();
                confirmOrderReference(confirmationFlow);
            });

        });
    }

    function confirmOrderReference(confirmationFlow) {
        var url = '{url controller="moptAjaxPayone" action="amznConfirmOrderReference" forceSecure}';

        $.ajax({
                url: url,
                success: function (data) {
                    $.loadingIndicator.close();
                    confirmationFlow.success();
                },
                error: function (data) {
                    $.loadingIndicator.close();
                    confirmationFlow.error();
                    amazon.Login.logout();
                    window.location.href = '{url controller="checkout" action="cart" forceSecure}';
                },
                timeout: 30000
            }
        );
    }


    {if $smarty.server.HTTP_REFERER|strpos:'Amazon' == false}
    function getURLParameter(name, source) {
        return decodeURIComponent((new RegExp('[?|&|#]' + name + '=' +
            '([^&]+?)(&|#|;|$)').exec(source) || [,""])[1].replace(/\+/g,
            '%20')) || null;
    }

    var accessToken = getURLParameter("access_token", location.hash)

    if (typeof accessToken === 'string' && accessToken.match(/^Atza/)) {
        document.cookie = "amazon_Login_accessToken=" + accessToken +
            ";secure";
    }

    window.onAmazonLoginReady = function () {
        amazon.Login.setClientId('{$payoneAmazonPayConfig->getClientId()}');
        amazon.Login.setUseCookie(true);
        $('#moptAmazonPayButton').attr("disabled", "disabled");
        $.loadingIndicator.open();
    };


    {else}
    var moptAmazonReferenceId = null;

    window.onAmazonLoginReady = function () {
        amazon.Login.setClientId("{$payoneAmazonPayConfig->getClientId()}");
        $('#moptAmazonPayButton').attr("disabled", "disabled");
        $.loadingIndicator.open();
    };
    {/if}
    window.onAmazonPaymentsReady = function () {
        new OffAmazonPayments.Widgets.AddressBook({
            sellerId: "{$payoneAmazonPayConfig->getSellerId()}",
            scope: 'profile payments:widget payments:shipping_address payments:billing_address',
            {if $payoneAmazonReadOnly}
            displayMode: "Read",
            amazonOrderReferenceId: "{$payoneAmazonReadOnly}",
            {/if}
            {if !$payoneAmazonReadOnly}
            onOrderReferenceCreate: function (orderReference) {
                moptAmazonReferenceId = orderReference.getAmazonOrderReferenceId();
            },
            {/if}
            onAddressSelect: function (orderReference) {

            },
            design: {
                designMode: 'responsive'
            },
            onReady: function (orderReference) {

            },
            onError: function (error) {
                // Your error handling code.
                // During development you can use the following
                // code to view error messages:
                // console.log(error.getErrorCode() + ': ' + error.getErrorMessage());
                // See "Handling Errors" for more information.
                console.log(error.getErrorCode() + ': ' + error.getErrorMessage());
            }
        }).bind("addressBookWidgetDiv");

        walletWidget = new OffAmazonPayments.Widgets.Wallet({
            sellerId: "{$payoneAmazonPayConfig->getSellerId()}",
            scope: 'profile payments:widget payments:shipping_address payments:billing_address',
            onPaymentSelect: function (orderReference) {
                $('#moptAmazonPayButton').attr("disabled", "disabled");
                var call = '{url controller="moptAjaxPayone" action="getOrderReferenceDetails" forceSecure}';
                $.ajax({
                    url: call ,
                    type: 'post',
                    data: { referenceId: moptAmazonReferenceId}
                })
                    .done(function(response){
                        var responseData = $.parseJSON(response);

                        if (responseData.status == "error"){
                            $.loadingIndicator.close();
                            $('#jsErrors').show();
                            $('#jsErrorContent').html(responseData.errormessage);
                        } else {
                            var moptAmazonCountryChanged = responseData.countryChanged;
                            $('#jsErrors').hide();
                            // Reload the site, to update dispatches in case country changed
                            if (moptAmazonCountryChanged) {
                                console.log('Country changed');
                                $.loadingIndicator.open();
                                location.reload(true);
                            } else {
                                console.log('Country did not change');
                                $.loadingIndicator.close();
                                {if ! $sMinimumSurcharge}
                                $('#moptAmazonPayButton').removeAttr("disabled");
                                {/if}
                            }
                        }
                    })
            },
            design: {
                designMode: 'responsive'
            },
            onError: function (error) {
                console.log(error.getErrorCode() + ': ' + error.getErrorMessage());
                // See "Handling Errors" for more information.
            }
        });
        walletWidget.setPresentmentCurrency("{$amazonCurrency}");// ISO-4217 currency code, merchant is expected to enter valid list of currency supported by Amazon Pay.
        walletWidget.bind("walletWidgetDiv");
    };

    function moptAmazonReady() {
        {if $payoneAmazonPayMode == 1}
        $.getScript('https://static-eu.payments-amazon.com/OffAmazonPayments/eur/lpa/js/Widgets.js');
        {else}
        $.getScript('https://static-eu.payments-amazon.com/OffAmazonPayments/eur/sandbox/lpa/js/Widgets.js');
        {/if}

        prepareOrderReferenceConfirmation();
    };
</script>