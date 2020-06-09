// TODO: rw remove ALL console.log()'s
;(function ($, window) {
    'use strict';

    var pluginRegistered = false;
    var widgetLoaded = false;

    reset();

    // update on ajax changes
    $.subscribe('plugin/swShippingPayment/onInputChanged', function () {
        reset();
    });

    function reset() {
        if (!window.Klarna) {
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
        data: $('#mopt_payone__klarna_information').data(),
        payTypeTranslations: {
            KDD: 'pay_now',
            KIV: 'pay_later',
            KIS: 'pay_over_time'
        },

        init: function () {
            var me = this;

            console.log('data:');
            console.log(me.data);
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

            me._on(me.$el.find('#mopt_payone__klarna_personalId'), 'change', function () {
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

        generateBirthDate: function (customerDateOfBirth_fromData) {
            if (customerDateOfBirth_fromData && customerDateOfBirth_fromData !== '0000-00-00') {
                return customerDateOfBirth_fromData
            }

            var birthdate = null;

            var $birthdate_day = $('#mopt_payone__klarna_birthday');
            var $birthdate_month = $('#mopt_payone__klarna_birthmonth');
            var $birthdate_year = $('#mopt_payone__klarna_birthyear');

            var birthdate_day = (Array(2).join("0") + $birthdate_day.val()).slice(-2);
            var birthdate_month = (Array(2).join("0") + $birthdate_month.val()).slice(-2);
            var birthdate_year = $birthdate_year.val();

            if (birthdate_day && birthdate_month && birthdate_year) {
                birthdate = birthdate_year + '-' + birthdate_month + '-' + birthdate_day // yyyy-mm-dd
            }

            return birthdate;
        },

        generatePhoneNumber: function (phoneNumber_fromData) {
            var phoneNumber_fromInput = $('#mopt_payone__klarna_telephone').val();
            if (phoneNumber_fromInput) {
                return phoneNumber_fromInput;
            }

            return phoneNumber_fromData;
        },

        generatePersonalId: function (personalId_fromData) {
            var personalId_fromInput = $('#mopt_payone__klarna_personalId').val();
            if (personalId_fromInput) {
                return personalId_fromInput;
            }

            return personalId_fromData;
        },

        inputChangeHandler: function () {
            console.log('inputChangeHandler');
            var me = this;

            me.unloadKlarnaWidget();

            me.birthdate = me.generateBirthDate(me.data['customerDateOfBirth']);
            me.billingAddressPhone = me.generatePhoneNumber(me.data['billingAddress-Phone'])
            me.personalId = me.generatePersonalId(me.data['customerNationalIdentificationNumber']);

            me.financingtype = $("#mopt_payone__klarna_paymenttype").val();
            var $gdpr_agreement = $('#mopt_payone__klarna_agreement');
            var loadWidgetIsAllowed =
                me.financingtype
                && me.birthdate
                && me.personalId
                && ((String)(me.billingAddressPhone)).length >= 5
                && $gdpr_agreement.is(':checked');

            if (loadWidgetIsAllowed) {

                // startKlarnaSessionCall is a PO call and needs no minus delimiter
                var birthdate = me.birthdate.replace(/-/g, '');

                if (me.billingAddressPhone === 'notNeededByCountry') {
                    me.billingAddressPhone = '';
                }

                if (me.personalId === 'notNeededByCountry') {
                    me.personalId = '';
                }

                me.startKlarnaSessionCall(me.financingtype, birthdate, me.billingAddressPhone, me.personalId).done(function (response) {
                    response = $.parseJSON(response);
                    $('#payment_meanmopt_payone_klarna').val(response['paymentId']);

                    me.loadKlarnaWidget(me.financingtype, response['client_token']).done(function () {
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

        startKlarnaSessionCall: function (financingtype, birthdate, phoneNumber, personalId) {
            var me = this;
            var url = me.data['startKlarnaSession-Url'];
            var parameter = {
                'financingtype': financingtype,
                'birthdate': birthdate.replace('-', ''),
                'phoneNumber': phoneNumber,
                'personalId' : personalId
            };
            return $.ajax({method: "POST", url: url, data: parameter});
        },


        unloadKlarnaWidget: function () {
            $('#mopt_payone__klarna_payments_widget_container').empty();
        },

        loadKlarnaWidget: function (paymentType, accessToken) {
            var me = this;

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
                    payment_method_category: me.payTypeTranslations[paymentType]
                }, function (res) {
                    // TODO: rw error handling
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
                order_lines: data['orderLines'],
                shipping_address: {
                    street_address: data['shippingAddress-StreetAddress'],
                    city: data['shippingAddress-City'],
                    given_name: data['shippingAddress-GivenName'],
                    postal_code: data['shippingAddress-PostalCode'],
                    family_name: data['shippingAddress-FamilyName'],
                    email: data['shippingAddress-Email'],
                    country: data['shippingAddress-Country'],
                    title: data['shippingAddress-Title'],
                    phone: data['shippingAddress-Phone'] ? data['shippingAddress-Phone'] : me.billingAddressPhone
                },
                billing_address: {
                    street_address: data['billingAddress-StreetAddress'],
                    city: data['billingAddress-City'],
                    given_name: data['billingAddress-GivenName'],
                    postal_code: data['billingAddress-PostalCode'],
                    family_name: data['billingAddress-FamilyName'],
                    email: data['billingAddress-Email'],
                    country: data['billingAddress-Country'],
                    title: data['billingAddress-Title'],
                    phone: me.billingAddressPhone
                },
                customer: {
                    date_of_birth: me.birthdate,
                    gender: data['customerGender'],
                    national_identification_number: me.personalId
                }
            };

            // TODO: rw remove
            $('<input>').attr('type', 'hidden').attr('name', 'moptPaymentData[klarna-authorize]').val(
                JSON.stringify(authorizeData)
            ).appendTo(me.$el);

            console.log('authorizeData:');
            console.log(authorizeData);

            window.Klarna.Payments.authorize({
                    payment_method_category: me.payTypeTranslations[me.financingtype]
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
                        console.log('authorize declined');
                        console.log(res);

                        $(me.$el.get(0).elements).filter(':submit').each(function (_, element) {
                            element.disabled = false;
                        });
                    }
                });
        }
    });
})(jQuery, window);
