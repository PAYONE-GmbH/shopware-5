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

    function loadKlarnaWidget(paymentType, accessToken) {

        if (!accessToken || accessToken.length === 0) {
            console.log('no token');
            return;
        }

        window.Klarna.Payments.init({
            client_token: accessToken
        });

        var payTypeTranslations = {
            KDD: 'pay_now',
            KIV: 'pay_later',
            KIS: 'pay_over_time'
        };

        if (!window.Klarna) {
            return;
        }

        window.Klarna.Payments.load({
            container: '#mopt_payone__klarna_payments_widget_container',
            payment_method_category: payTypeTranslations[paymentType]
        }, function (res) {
            console.log('Klarna.Payments.load');
            console.log(res);
        });
    }

    function unloadKlarnaWidget() {
        $('#mopt_payone__klarna_payments_widget_container').empty();
    }

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

            me._on(me.$el.find('#mopt_payone__klarna_paymenttype'), 'change', function() {
                me.handleInputChange();
            });

            me._on(me.$el.find('#mopt_payone__klarna_agreement'), 'change', function() {
                me.handleInputChange();
            });
        },

        handleInputChange: function() {
            var me = this;
            var $select = $("#mopt_payone__klarna_paymenttype");
            var $checkbox = $('#mopt_payone__klarna_agreement');
            var paymentId = $select.val();

            unloadKlarnaWidget();

            if (paymentId && $checkbox.is(':checked')) {
                me.startKlarnaSessionCall(paymentId).done(function(response) {
                    loadKlarnaWidget(paymentId, $.parseJSON(response)['client_token']);
                });
            }
        },

        startKlarnaSessionCall: function (paymentId) {
            var url = 'https://shop.testing.fatchip.local/sw564/MoptAjaxPayone/startKlarnaSession';
            var parameter = {'short': 'KDD'};
            return $.ajax({method: "POST", url: url, data: parameter});
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
