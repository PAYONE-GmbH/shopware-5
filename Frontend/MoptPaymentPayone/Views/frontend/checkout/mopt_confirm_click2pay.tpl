{namespace name='frontend/MoptPaymentPayone/payment'}
{extends file="parent:frontend/checkout/confirm.tpl"}


{block name='frontend_checkout_confirm_submit'}
    {* Submit order button *}
    {if $sPayment.embediframe || $sPayment.action}
        <div id="payoneclick2paycontainer" class="center-block" style="align-items: center; justify-content: center; display: flex;"></div>
    {else}
        <div id="payoneclick2paycontainer" class="center-block" style="align-items: center; justify-content: center; display:flex;">></div>
    {/if}
    <button class="is--primary is--large left is--align-center is--disabled"
            onClick="window.HostedTokenizationSdk.submitForm(tokenizationSuccessCallback,tokenizationFailureCallback)"
    >
        {s name='ConfirmDoPayment'  namespace="frontend/checkout/confirm"}{/s}
    </button>
{/block}

{block name="frontend_index_header_javascript_jquery"}
    {$smarty.block.parent}
    {if $mopt_click2pay_mode == 'test'}
        <script src="https://sdk.preprod.tokenization.secure.payone.com/1.2.1/hosted-tokenization-sdk.js"
                integrity="sha384-oga+IGWvy3VpUUrebY+BnLYvsNZRsB3NUCMSa+j3CfA9ePHUZ++8/SVyim9F7Jm3" crossorigin="anonymous">
        </script>
    {else}
        <script src="https://sdk.tokenization.secure.payone.com/1.3.0/hosted-tokenization-sdk.js" crossorigin="anonymous">
        </script>
    {/if}
    <script>
        function tokenizationSuccessCallback(statusCode, token, cardDetails, cardInputMode) {
            var agbCheckbox = document.getElementById('sAGB').checked;
            if (!agbCheckbox) {
                document.getElementById('sAGB').labels[0].classList.add('has--error');
                document.getElementById('sAGB').scrollIntoView({ block: 'center' });
                return;
            }
            console.log("Tokenized card sucessfully");
            console.log(statusCode);
            console.log(token);
            console.log('Name:' + cardDetails.cardholderName);
            console.log('Number:' + cardDetails.cardNumber);
            console.log('Expiry;' + cardDetails.expiryDate);
            console.log('CardType' + cardDetails.cardType);
            if (statusCode === 201) {
                paymentToken = '{$mopt_click2pay_token}';
                window.location = '{url controller="MoptPaymentPayone" action="click2pay" forceSecure}' + '?token=' + token + '&type=' + cardInputMode+ '&cardholderName=' + cardDetails.cardholderName + '&cardNumber=' + cardDetails.cardNumber + '&cardType=' + cardDetails.cardType + '&cardExpiry=' + cardDetails.expiryDate;
            }
        }
        function tokenizationFailureCallback(statusCode, errorResponse) {
            console.log("Tokenization of card failed");
            console.log(statusCode);
            console.log(errorResponse);
        }
        function callbackfunc(statusCode, res) {
            if (res == 0) { // Behaviour not documented like this, but it seems like these in combination mean "ready to pay"
                mopt_click2pay_ready = true;
            }
            console.log("statusCode:", statusCode);
            console.log("res:", res);
        }

        const config = {
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
                    amount: "{if $sAmountWithTax && $sUserData.additional.charge_vat}{$sAmountWithTax * 100}{else}{$sAmount * 100}{/if}",
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
                    console.log('customized successfully');
                    window.HostedTokenizationSdk.getPaymentPage(config, callbackfunc);
                    console.log('HTP-SDK loaded successfully');
                } catch (error) {
                    console.error('Error initializing HTP-SDK:', error);
                }
            }
            loadSDK();
        } else {
            console.log("HTP-SDK failed to load");
        }
    </script>
{/block}
