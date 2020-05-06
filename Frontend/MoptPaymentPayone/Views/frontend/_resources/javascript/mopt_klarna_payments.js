;(function ($, window) {
    'use strict';

    var data = $('#mopt_payone__klarna_information').data();

    var pluginRegistered = false;

    // no Klarna payment activated
    if (!data) {
        return;
    }

    reset();

    // update on ajax changes
    $.subscribe('plugin/swShippingPayment/onInputChanged', function () {
        reset();
    });

    function reset() {
        // TODO: remove '&& false'
        if (!window.moptPayonePaymentType && false) {
            destroyPlugin();

            return;
        }

        if (pluginRegistered) {
            updatePlugin();
        } else {
            registerPlugin();
            pluginRegistered = true;
        }

        // fatchipCTFetchAccessToken(window.fatchipCTPaymentType);

        // delete window.fatchipCTPaymentType;
    }
    //
    // function fatchipCTLoadKlarna(paymentType, accessToken) {
    //
    //     if (!accessToken || accessToken.length === 0) {
    //         console.log('no token');
    //         return;
    //     }
    //
    //     window.Klarna.Payments.init({
    //         client_token: accessToken
    //     });
    //
    //     var payTypeTranslations = {
    //         pay_now:
    //             'pay_now',
    //         pay_later:
    //             'pay_later',
    //         slice_it:
    //             'pay_over_time',
    //         direct_bank_transfer:
    //             'direct_bank_transfer',
    //         direct_debit:
    //             'direct_debit'
    //     };
    //
    //     window.fatchipCTKlarnaPaymentType = payTypeTranslations[paymentType];
    //
    //     if (!window.Klarna) {
    //         return;
    //     }
    //
    //     Klarna.Payments.load({
    //         container: '#fatchip-computop-payment-klarna-form-' + paymentType,
    //         payment_method_category: payTypeTranslations[paymentType]
    //     }, function (res) {
    //         console.log('Klarna.Payments.load');
    //         console.log(res);
    //     });
    // }
    //
    // function fatchipCTFetchAccessToken(paymentType) {
    //     var url = data['getAccessToken-Url'];
    //     var parameter = {paymentType: paymentType};
    //
    //     $.ajax({method: "POST", url: url, data: parameter}).done(function (response) {
    //         fatchipCTLoadKlarna(paymentType, JSON.parse(response));
    //     });
    // }

    function registerPlugin() {
        StateManager.addPlugin('#shippingPaymentForm', 'payoneKlarnaPayments', null, null);
    }

    function updatePlugin() {
        StateManager.updatePlugin('#shippingPaymentForm', 'payoneKlarnaPayments');
    }

    function destroyPlugin() {
        StateManager.destroyPlugin('#shippingPaymentForm', 'payoneKlarnaPayments');
        StateManager.removePlugin('#shippingPaymentForm', 'payoneKlarnaPayments', null);
    }

    $.plugin('payoneKlarnaPayments', {
        defaults: {},

        init: function () {
            var me = this;

            me.registerEventListeners();
        },

        update: function () {
        },

        destroy: function () {
            var me = this;

            me._destroy();
        },

        registerEventListeners: function () {
            var me = this;

            me._on(me.$el, 'submit', function (event) {
                // event.preventDefault();
                //
                // me.authorize(event);
            });

            me._on(me.$el.find('#mopt_payone__klarna_agreement'), 'change', function (event) {
                var select = document.getElementById("mopt_payone__klarna_paymenttype");
                var selection = select.options[select.selectedIndex].value;

                if (selection) {
                    console.log(selection);

                    me.testCall(selection);
                }
                console.log('dbg1');
            });
        },

        testCall: function (paymentId) {
            var url = 'https://shop.testing.fatchip.local/sw564/MoptAjaxPayone/startKlarnaSession';
            var parameter = {'short': 'KDD'};
            $.ajax({method: "POST", url: url, data: parameter}).done(function (response) {
                console.log('dbg2');
            });
        },

        authorize: function (event) {
            var authorizeData = {
                purchase_country: data['billingAddress-Country'],
                purchase_currency: data['purchaseCurrency'],
                locale: data['locale'],
                billing_address: {
                    street_address: data['billingAddress-StreetAddress'],
                    city: data['billingAddress-City'],
                    given_name: data['billingAddress-GivenName'],
                    postal_code: data['billingAddress-PostalCode'],
                    family_name: data['billingAddress-FamilyName'],
                    email: data['billingAddress-Email'],
                    country: data['billingAddress-Country']
                }
            };

            event.target[0].disabled = true;
            window.Klarna.Payments.authorize({
                    payment_method_category: window.fatchipCTKlarnaPaymentType
                },
                authorizeData,
                function (res) {
                    var storeAuthorizationTokenUrl = data['storeAuthorizationToken-Url'];
                    var parameter = {'authorizationToken': res['authorization_token']};

                    if (res['approved'] && res['authorization_token']) {
                        // store authorization_token
                        $.ajax({method: "POST", url: storeAuthorizationTokenUrl, data: parameter}).done(function () {
                            event.target.submit();
                        });
                    } else {
                        event.target[0].disabled = false;
                    }
                });
        }
    });
})(jQuery, window);
