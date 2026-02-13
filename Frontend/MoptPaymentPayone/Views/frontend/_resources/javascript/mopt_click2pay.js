;(function ($, window) {
    'use strict';
    var pluginRegistered = false;

    reset();

// update on ajax changes
    $.subscribe('plugin/swShippingPayment/onInputChanged', function () {
        reset();
    });

    function reset() {
        if (pluginRegistered) {
            updatePlugin();
        } else {
            registerPlugin();
            pluginRegistered = true;
        }
    }

    function registerPlugin() {
        StateManager.addPlugin('#shippingPaymentForm', 'click2payData', null, null);
    }

    function updatePlugin() {
        StateManager.updatePlugin('#shippingPaymentForm', 'click2payData');
    }

    function destroyPlugin() {
        StateManager.destroyPlugin('#shippingPaymentForm', 'click2payData');
        StateManager.removePlugin('#shippingPaymentForm', 'click2payData', null);
    }

    $.plugin('click2payData', {
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
            var checkedRadioId = $('input[name=payment]:checked', '#shippingPaymentForm').attr('id');
            var paymentid = 'payment_mean' + $('#mopt_payone_click2pay_paymentid').val();
            if (! (checkedRadioId === 'payment_meanmopt_payone_click2pay' || checkedRadioId === paymentid)) {
                return;
            }
            // disable submit buttons
            $(me.$el.get(0).elements).filter(':submit').each(function (_, element) {
                element.disabled = true;
            });

            me._on(me.$el, 'submit', function (event) {
                if (!successFullCallback) {
                    event.preventDefault();
                }
            });

        },
    });
})(jQuery, window);
