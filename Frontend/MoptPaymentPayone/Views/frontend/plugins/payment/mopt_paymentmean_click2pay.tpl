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
    <div id="payoneclick2paycontainer" class="center-block" style="align-items: center; justify-content: center; display:flex;">></div>
        {if $mopt_click2pay_mode == 'test'}
            <script src="https://sdk.preprod.tokenization.secure.payone.com/1.2.1/hosted-tokenization-sdk.js"
                    integrity="sha384-oga+IGWvy3VpUUrebY+BnLYvsNZRsB3NUCMSa+j3CfA9ePHUZ++8/SVyim9F7Jm3" crossorigin="anonymous">
            </script>
        {else}
            <script src="https://sdk.tokenization.secure.payone.com/1.3.0/hosted-tokenization-sdk.js" crossorigin="anonymous">
            </script>
        {/if}
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
                mode: "{$mopt_click2pay_mode}",
                allowedCardSchemes: {$mopt_click2pay_allowedCardSchemes},
                CTPConfig: {
                    enableCTP: {$mopt_click2pay_enable_CTP},
                    enableCustomerOnboarding: {$mopt_click2pay_enable_customer_onboarding},
                    schemeConfig: {
                        merchantPresentationName: "PayoneC2P-00004",
                        visaConfig: {
                            srcInitiatorId: '2662KBGOLX92KS4XIFYU213JLdGTvLhYkOB-_1gLo1D1jOqgM',
                            srcDpaId: '{$mopt_click2pay_src_dpa_id}',
                            encryptionKey: 'GQJIKLOAMZWIT8IRIGHR14vQUlllxiMWf-XSHQHvjI5wuTZ2w',
                            nModulus: 'kPujwVJjevI_oeZwZoA2Wjt94DFcMvRCab8iRiEGrGfKWtNCwQYkylyuRoB615cYm2BVbvoKH8Yyv0aC3dwah6UmOdJszmL0pV_cbx_tXzWgYg3sYNsp0sBxUFcQ1A6DVbyOxxJbmnwlHGE5fkuzJr-qqul3RswsCG-vPrh_--2_RSipa9lVr9gvfI4AbFABLTqKeto0rWPbIBKdhcGQ7JMPxzq8239KPUZfSyNueAcdL-yHADi3L2VSzdF7tS7si3ue_IFoXDpbggsFxvEt79UlBDOBsagc_ms9_ZsYlJaKCT8ZjwhakMo_-Zdc97mudVj1jz2_L5l4l_zibF5riw',
                        },
                        mastercardConfig: {
                            srcInitiatorId: '559003b0-5d17-4d89-aa2b-b02a4023d64d',
                            srcDpaId: '{$mopt_click2pay_src_dpa_id}'
                        }
                    },
                    transactionAmount: {
                        amount: "{$sAmount * 100}",
                        currencyCode: "{$mopt_click2pay_currency}"
                    },
                    uiConfig: {$mopt_click2pay_ctp_ui_config},
                    shopName: "{$mopt_click2pay_shopname}",
                }
            }
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
        </script>
{/if}