{namespace name='frontend/MoptPaymentPayone/payment'}
{extends file="parent:frontend/checkout/confirm.tpl"}


{block name='frontend_checkout_confirm_submit'}
    {* Submit order button *}
    {if $sPayment.embediframe || $sPayment.action}
        <div id="payonegooglepaycontainer" class="right is--icon-right"></div>
    {else}
        <div id="payonegooglepaycontainer" class="right is--icon-right"></div>
    {/if}
{/block}

{block name="frontend_index_header_javascript_jquery"}
    {$smarty.block.parent}
    <script async>
        const baseRequest = {
            apiVersion: 2,
            apiVersionMinor: 0
        };

        // const allowedCardNetworks = ["MASTERCARD", "VISA"];
        const allowedCardNetworks = {$mopt_googlepay_supportedNetworks}

        // const allowedCardAuthMethods = ["PAN_ONLY", "CRYPTOGRAM_3DS"];
        const allowedCardAuthMethods = {$mopt_googlepay_allowedAuthMethods};

        const tokenizationSpecification = {
            type: 'PAYMENT_GATEWAY',
            parameters: {
                'gateway': 'payonegmbh',
                'gatewayMerchantId': '{$mopt_googlepay_gatewayMerchantId}'
            }
        };

        const baseCardPaymentMethod = {
            type: 'CARD',
            parameters: {
                allowedAuthMethods: allowedCardAuthMethods,
                allowedCardNetworks: allowedCardNetworks,
                allowPrepaidCards: {if $mopt_googlepay_allowPrepaidCards == '1'}true{else}false{/if},
                allowCreditCards: {if $mopt_googlepay_allowCreditCards == '1'}true{else}false{/if}
            }
        };

        const cardPaymentMethod = Object.assign(
                {},
            baseCardPaymentMethod,
            {
                tokenizationSpecification: tokenizationSpecification
            }
        );

        let paymentsClient = null;

        function getGoogleIsReadyToPayRequest() {
            return Object.assign(
                    {},
                baseRequest,
                {
                    allowedPaymentMethods: [baseCardPaymentMethod]
                }
            );
        }

        function getGooglePaymentDataRequest() {
            const paymentDataRequest = Object.assign({}, baseRequest);
            paymentDataRequest.allowedPaymentMethods = [cardPaymentMethod];
            paymentDataRequest.transactionInfo = getGoogleTransactionInfo();
            paymentDataRequest.merchantInfo = {
                merchantId: '{$mopt_googlepay_merchantId}',
                merchantName: '{$mopt_googlepay_merchantName}'
            };
            return paymentDataRequest;
        }

        function getGooglePaymentsClient() {
            if ( paymentsClient === null ) {
                paymentsClient = new google.payments.api.PaymentsClient({ environment: '{$mopt_googlepay_mode}' });
            }
            return paymentsClient;
        }

        function onGooglePayLoaded() {
            const paymentsClient = getGooglePaymentsClient();
            paymentsClient.isReadyToPay(getGoogleIsReadyToPayRequest())
                .then(function(response) {
                    if (response.result) {
                        addGooglePayButton();
                    }
                })
                .catch(function(err) {
                    // show error in developer console for debugging
                    console.error(err);
                });
        }
        function addGooglePayButton() {
            const paymentsClient = getGooglePaymentsClient();
            const button =
                paymentsClient.createButton({
                    onClick: onGooglePaymentButtonClicked,
                    buttonColor: '{$mopt_googlepay_buttonColor}',
                    buttonType: '{$mopt_googlepay_buttonType}',
                    buttonLocale: '{$mopt_googlepay_buttonLocale}'
                }
                );
            document.getElementById('payonegooglepaycontainer').appendChild(button);
        }

        function getGoogleTransactionInfo() {
            return {
                countryCode: '{$mopt_googlepay_country}',
                currencyCode: '{$mopt_googlepay_currency}',
                totalPriceStatus: 'FINAL',
                // set to cart total
                totalPrice: '{if $sAmountWithTax && $sUserData.additional.charge_vat}{$sAmountWithTax}{else}{$sAmount}{/if}',
                totalPriceLabel: '{s namespace="frontend/account/order_item" name="OrderItemTotal"}Gesamtsumme{/s}',
                {if $mopt_googlepay_showDisplayItems == '1'}
                displayItems: {$mopt_googlepay_displayItems}
                {/if}
            };
        }

        function prefetchGooglePaymentData() {
            const paymentDataRequest = getGooglePaymentDataRequest();
            // transactionInfo must be set but does not affect cache
            paymentDataRequest.transactionInfo = {
                totalPriceStatus: 'NOT_CURRENTLY_KNOWN',
                currencyCode: '{$mopt_googlepay_currency}'
            };
            const paymentsClient = getGooglePaymentsClient();
            paymentsClient.prefetchPaymentData(paymentDataRequest);
        }

        function onGooglePaymentButtonClicked() {
            var agbCheckbox = document.getElementById('sAGB').checked;
            if (!agbCheckbox) {
                document.getElementById('sAGB').labels[0].classList.add('has--error');
                document.getElementById('sAGB').scrollIntoView({ block: 'center' });
                return;
            }
            const paymentDataRequest = getGooglePaymentDataRequest();
            paymentDataRequest.transactionInfo = getGoogleTransactionInfo();

            const paymentsClient = getGooglePaymentsClient();
            paymentsClient.loadPaymentData(paymentDataRequest)
                .then(function(paymentData) {
                    // handle the response
                    processPayment(paymentData);
                })
                .catch(function(err) {
                    // show error in developer console for debugging
                    console.error(err);
                });
        }

        function processPayment(paymentData) {
            // show returned data in developer console for debugging
            console.log(paymentData);
            // @todo pass payment token to your gateway to process payment
            paymentToken = paymentData.paymentMethodData.tokenizationData.token;
            console.log('PaymentToken:');
            console.log(paymentToken);
            window.location = '{url controller="MoptPaymentPayone" action="google_pay" forceSecure}' + '?token=' + btoa(paymentToken);
        }
    </script>
    <script async
            src="https://pay.google.com/gp/p/js/pay.js"
            onload="onGooglePayLoaded()">
    </script>
{/block}
