{namespace name='frontend/MoptPaymentPayone/payment'}

{if $payment_mean.id == $form_data.payment}
    <br>
    <div id="mopt_payone__click2pay_response" hidden
         data-click2pay-response="initial"
    ></div>
    <input type="text" hidden name="moptPaymentData[mopt_payone_click2pay_paymentid]"
           id="mopt_payone_click2pay_paymentid"
           value="{$moptCreditCardCheckEnvironment.mopt_payone_click2pay_paymentid}"
    >
    <br>
    <div id="payoneclick2paycontainer" class="center-block" style="align-items: center; justify-content: center; display:flex;"></div>

        <script>
            var registeredClick = false;
            var successFullCallback = false;

            function tokenizationSuccessCallback(statusCode, token, cardDetails, cardInputMode) {
                if (statusCode === 201) {
                    $.ajax({
                        url: '{url controller="moptAjaxPayone" action="storeClick2payData" forceSecure}',
                        type: 'POST',
                        data: { token: token, cardholderName: cardDetails.cardholderName, cardNumber: cardDetails.cardNumber, expiryDate: cardDetails.expiryDate, cardType: cardDetails.cardType, cardInputMode: cardInputMode }
                    });
                    successFullCallback = true;
                    $("#shippingPaymentForm").submit();
                    $('form[name="frmRegister"]').submit();
                }

            }
            function tokenizationFailureCallback(statusCode, errorResponse) {
                console.log("Tokenization of card failed");
                console.log(statusCode);
                console.log(errorResponse);
            }
            function callbackfunc(statusCode, res) {
                if (statusCode== "ReadyToPay" && res == 0) {
                    var buttons = document.querySelectorAll('button[type="submit"]');
                    buttons.forEach(function (button) {
                        button.removeAttribute('disabled');
                    });

                    if (!registeredClick) {
                        buttons.forEach(function (button) {
                            button.onclick = function () {
                                window.HostedTokenizationSdk.submitForm(tokenizationSuccessCallback, tokenizationFailureCallback);
                            };
                        });
                        registeredClick = true;
                    }
                }
            }
            var config = {
                iframe: {
                    iframeWrapperId: "payoneclick2paycontainer",
                    zIndex: 10000,
                    height: 40,       // 40 is the default value
                    width: 300
                },
                uiConfig: {$mopt_click2pay_ui_config},
                locale: "{$mopt_click2pay_locale}",

                token: "{$mopt_click2pay_token}",
                email:  "{$mopt_click2pay_email}",
                mode: "{$mopt_click2pay_mode}",
                allowedCardSchemes: {$mopt_click2pay_allowedCardSchemes},
                CTPConfig: {
                    enableCTP: {$mopt_click2pay_enable_CTP},
                    enableCustomerOnboarding: {$mopt_click2pay_enable_customer_onboarding},
                    transactionAmount: {
                        amount: "{$sAmount * 100}",
                        currencyCode: "{$mopt_click2pay_currency}"
                    },
                    uiConfig: {$mopt_click2pay_ctp_ui_config},
                    shopName: "{$mopt_click2pay_shopname}",
                }
            }

            function getScript(scriptUrl, callback) {
                const script = document.createElement('script');
                script.src = scriptUrl;
                script.integrity = 'sha384-qV/kvLA3YM9ZkCKfcliuCeR0PkqumnwfDNDF2FwkbZCQTMFJ5kzJc/7dH4E3rT0L';
                script.crossOrigin = "anonymous";
                script.onload = callback;

                document.body.appendChild(script);
            }

            function onScriptLoad() {
                if (window.HostedTokenizationSdk) {
                    console.log('HTP-SDK initialized successfully');
                    async function loadSDK() {
                        try {
                            await window.HostedTokenizationSdk.init();
                            window.HostedTokenizationSdk.getPaymentPage(config, callbackfunc);
                        } catch (error) {
                            console.error('Error initializing HTP-SDK:', error);
                        }
                    }
                    loadSDK();
                } else {
                    console.log("HTP-SDK failed to load");
                }
            }

            getScript('https://sdk.tokenization.secure.payone.com/1.5.0/hosted-tokenization-sdk.js', onScriptLoad);
        </script>
{/if}