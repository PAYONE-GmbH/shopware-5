{namespace name='frontend/MoptPaymentPayone/payment'}
{extends file="parent:frontend/checkout/confirm.tpl"}

{block name="frontend_checkout_confirm_information_wrapper"}
    {$smarty.block.parent}
    <div id="payoneApplePayConfirm" class="payment--form-group panel has--border">
        <div>
            <pre id="mopt-applepay-debug" hidden></pre>
        </div>
    </div>
    <script src="https://applepay.cdn-apple.com/jsapi/v1/apple-pay-sdk.js"></script>
    <script async>
        var mopt_applepay_debug = {$mopt_applepay_debug};
        // var mopt_applepay_outputElem = document.getElementById('mopt-applepay-device-support');
        var mopt_applepay_debugElem = document.getElementById('mopt-applepay-debug');
        if (mopt_applepay_debug == 1 ) {
            mopt_applepay_debugElem.removeAttribute('hidden');
        }
        writeDebug('Testing Suppport for Applepay Session');

        if (window.ApplePaySession) {
            writeDebug('ApplePaySession initialized');
            var canmakePayments = ApplePaySession.canMakePayments();
            if (canmakePayments) {
                writeDebug('Supported Apple Device detected. canMakePayments returns: ' + canMakePayments);
            } else {
                // mopt_applepay_outputElem.innerHTML = "{s namespace='frontend/MoptPaymentPayone/errorMessages' name="applepayUnsupportedAppleDevice"}Apple Pay unterstützt dieses Gerät nicht{/s}";
                writeDebug('Unsupported Supported Apple Device detected. canMakePayments returns: ' + canMakePayments);
            }
        } else {
            // mopt_applepay_outputElem.innerHTML = "{s namespace='frontend/MoptPaymentPayone/errorMessages' name="applepayNoAppleDevice"}Apple Pay kann nur mit Apple Geräten verwendet werden{/s}";
            writeDebug('Payment is only available with apple devices');
        }

        function writeDebug(message, data) {
            if (mopt_applepay_debug != 1) {
                return;
            }
            if (!message) message = '';
            if (!data) data = '';

            mopt_applepay_debugElem.innerHTML += message + '\n'
            if (data != '') {
                mopt_applepay_debugElem += JSON.stringify(data, undefined, '  ')
                mopt_applepay_debugElem.innerHTML += '\n';
            }
        }

        function payWithApplePay() {
            var agbCheckbox = document.getElementById('sAGB').checked;
            if (!agbCheckbox) {
                document.getElementById('sAGB').labels[0].classList.add('has--error')
                return;
            }
            const session = new ApplePaySession(3, {
                countryCode: '{$mopt_applepay_country}',
                currencyCode: '{$mopt_applepay_currency}',
                supportedNetworks: {$mopt_applepay_supportedNetworks},
                merchantCapabilities: {$mopt_applepay_merchantCapabilities},
                total: {
                    label: '{$mopt_applepay_label}',
                    amount: '{if $sAmountWithTax && $sUserData.additional.charge_vat}{$sAmountWithTax}{else}{$sAmount}{/if}'
                }
            })

            session.onvalidatemerchant = function (event) {
                var validationUrl = event.validationURL;
                // validationUrl = 'https://apple-pay-gateway-cert.apple.com/paymentservices/startSession';
                writeDebug('Vaidation URl:' + validationUrl);
                writeDebug('Basket amount:' + '{if $sAmountWithTax && $sUserData.additional.charge_vat}{$sAmountWithTax}{else}{$sAmount}{/if}');
                $.ajax({
                    url: '{url controller="MoptAjaxPayone" action="createApplePaySession" forceSecure}',
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        'validationUrl': validationUrl
                    },
                    success: function (response) {
                        parsed = JSON.parse(response.merchantSession);
                        if (response.success === true && response.merchantSession !== undefined && parsed.merchantSessionIdentifier !== undefined  ) {
                            writeDebug('validation completed successfully', response);
                            session.completeMerchantValidation(JSON.parse(response.merchantSession));
                        } else {
                            writeDebug('Apple Pay Merchant Validation failed');
                            writeDebug(JSON.stringify(response.merchantSession));
                            session.abort();
                        }
                    },
                    error: function (xhr, status, errorThrown) {
                        writeDebug('Apple Pay communication error occured.', status);
                        session.abort();
                    }
                });
            };

            session.onpaymentauthorized = function (event) {
                writeDebug('Apple Pay Authorized:');
                $.ajax({
                    url: '{url controller="MoptPaymentPayone" action="apple_pay" forceSecure}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        'token': event.payment.token,
                    },
                    success: function (response) {
                        if (response.success === true) {
                            session.completePayment({
                                status: ApplePaySession.STATUS_SUCCESS,
                                errors: []
                            });
                            window.location = response.url;
                        } else {
                            session.completePayment({
                                status: ApplePaySession.STATUS_FAILURE,
                                errors: [response.error]
                            });
                        }
                    },
                    error: function (xhr, status, errorThrown) {
                        writeDebug('Error saving Apple pay token in Session.', status);
                        session.completePayment({
                            status: ApplePaySession.STATUS_FAILURE,
                            errors: []
                        });
                    }
                });
            }
            session.begin()
        }
    </script>
{/block}

{block name='frontend_checkout_confirm_submit'}
    {* Submit order button *}
    {if $sPayment.embediframe || $sPayment.action}
        <button class="btn is--primary is--large right is--icon-right" onclick="payWithApplePay(); return false;">
            {s name='ConfirmDoPayment'  namespace="frontend/checkout/confirm"}{/s}<i class="icon--arrow-right"></i>
        </button>
    {else}
        <button class="btn is--primary is--large right is--icon-right" onclick="payWithApplePay()">
            {s name='ConfirmActionSubmit'  namespace="frontend/checkout/confirm"}{/s}<i class="icon--arrow-right" onclick="payWithApplePay(); return false;"></i>
        </button>
    {/if}
{/block}
