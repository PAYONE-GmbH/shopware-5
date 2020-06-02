;(function ($, window) {
    'use strict';

    var data = $('#mopt_payone__klarna_information').data();

    var payTypeTranslations = {
        KDD: 'pay_now',
        KIV: 'pay_later',
        KIS: 'pay_over_time'
    };

    var pluginRegistered = false;
    var widgetLoaded = false;

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
        // TODO: remove '&& false' and ALL console.log()'s
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
    }

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
        financingtype: null,
        submitPressed: false,
        authorizationToken: null,
        data: data,

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
                me.submitHandler(event);
            });

            me._on(me.$el.find('#mopt_payone__klarna_paymenttype'), 'change', function () {
                me.inputChangeHandler();
            });

            me._on(me.$el.find('#mopt_payone__klarna_telephone'), 'change', function () {
                me.inputChangeHandler();
            });

            me._on(me.$el.find('#mopt_payone__klarna_birthday'), 'change', function () {
                me.inputChangeHandler();
            });

            me._on(me.$el.find('#mopt_payone__klarna_birthmonth'), 'change', function () {
                me.inputChangeHandler();
            });

            me._on(me.$el.find('#mopt_payone__klarna_birthyear'), 'change', function () {
                me.inputChangeHandler();
            });

            me._on(me.$el.find('#mopt_payone__klarna_agreement'), 'change', function () {
                me.inputChangeHandler();
            });
        },

        submitHandler: function (event) {
            var me = this;

            var checkedRadioId = $('input[name=payment]:checked', '#shippingPaymentForm').attr('id');
            if (checkedRadioId !== 'payment_meanmopt_payone_klarna') {
                return;
            }

            if (me.authorizationToken) {
                return;
            }

            event.preventDefault();

            me.submitPressed = true;
            console.log('submit');

            // disable submit buttons
            $(me.$el.get(0).elements).filter(':submit').each(function (_, element) {
                element.disabled = true;
            });

            if (widgetLoaded) {
                console.log('call authorize [submit]');
                me.authorize();
            }
        },

        inputChangeHandler: function () {
            console.log('inputChangeHandler');
            var me = this;
            var $paymenttype = $("#mopt_payone__klarna_paymenttype");
            var $telefone = $('#mopt_payone__klarna_telephone');
            var $birthdate_day = $('#mopt_payone__klarna_birthday');
            var $birthdate_month = $('#mopt_payone__klarna_birthmonth');
            var $birthdate_year = $('#mopt_payone__klarna_birthyear');
            var $gdpr_agreement = $('#mopt_payone__klarna_agreement');
            var financingtype = $paymenttype.val();
            me.financingtype = financingtype;

            me.unloadKlarnaWidget();

            var birthdate_day = (Array(2).join("0") + $birthdate_day.val()).slice(-2);
            var birthdate_month = (Array(2).join("0") + $birthdate_month.val()).slice(-2);
            var birthdate_year = $birthdate_year.val();
            var birthdate = birthdate_year + birthdate_month + birthdate_day// yyyymmdd

            var telefoneNo = $telefone.val();
            var loadWidgetIsAllowed =
                financingtype
                && birthdate
                && telefoneNo.length >= 5
                && $gdpr_agreement.is(':checked');

            if (loadWidgetIsAllowed) {
                me.startKlarnaSessionCall(financingtype, birthdate, telefoneNo).done(function (response) {
                    response = $.parseJSON(response);
                    $('#payment_meanmopt_payone_klarna').val(response['paymentId']);

                    me.loadKlarnaWidget(financingtype, response['client_token']).done(function () {
                        console.log('widget loaded');
                        if (!me.submitPressed) {
                            return;
                        }

                        console.log('call authorize [change]');
                        me.authorize();
                    });
                });
            }
        },

        startKlarnaSessionCall: function (financingtype, birthdate, telefoneNo) {
            var url = data['startKlarnaSession-Url'];
            var parameter = {
                'financingtype': financingtype,
                'birthdate': birthdate,
                'telefoneNo': telefoneNo
            };
            return $.ajax({method: "POST", url: url, data: parameter});
        },


        unloadKlarnaWidget: function () {
            $('#mopt_payone__klarna_payments_widget_container').empty();
        },

        loadKlarnaWidget: function (paymentType, accessToken) {
            if (!accessToken || accessToken.length === 0) {
                console.log('no token');
                return;
            }

            if (!window.Klarna) {
                return;
            }

            window.Klarna.Payments.init({
                client_token: accessToken
            });

            console.log('load Klarna widget');
            return $.Deferred(function (defer) {
                window.Klarna.Payments.load({
                    container: '#mopt_payone__klarna_payments_widget_container',
                    payment_method_category: payTypeTranslations[paymentType]
                }, function (res) {
                    // TODO: error handling
                    widgetLoaded = true;
                    console.log('Klarna widget loaded');
                    console.log(res);
                    defer.resolve();
                });
            }).promise();
        },

        authorize: function () {
            var me = this;
            var data = me.data;
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

            window.Klarna.Payments.authorize({
                    payment_method_category: payTypeTranslations[me.financingtype]
                },
                authorizeData,
                function (res) {
                    var storeAuthorizationTokenUrl = data['storeAuthorizationToken-Url'];

                    if (res['approved'] && res['authorization_token']) {
                        console.log('authorize approved');
                        console.log(res);

                        var parameter = {'authorizationToken': res['authorization_token']};
                        me.authorizationToken = res['authorization_token'];

                        // store authorization_token
                        $.ajax({method: "POST", url: storeAuthorizationTokenUrl, data: parameter}).done(function () {
                            console.log('Authorization token stored');

                            me.$el.submit();
                        });
                    } else {
                        $(me.$el.get(0).elements).filter(':submit').each(function (_, element) {
                            element.disabled = false;
                        });
                    }
                });
        }
    });
})(jQuery, window);