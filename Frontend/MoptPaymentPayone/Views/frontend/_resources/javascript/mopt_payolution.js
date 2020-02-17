;(function ($, window) {
    'use strict';
    console.log('DBG1');

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
        StateManager.addPlugin('#shippingPaymentForm', 'requestFraudPreventionToken', null, null);
    }

    function updatePlugin() {
        StateManager.updatePlugin('#shippingPaymentForm', 'requestFraudPreventionToken');
    }

    function destroyPlugin() {
        StateManager.destroyPlugin('#shippingPaymentForm', 'requestFraudPreventionToken');
        StateManager.removePlugin('#shippingPaymentForm', 'requestFraudPreventionToken', null);
    }

    $.plugin('requestFraudPreventionToken', {
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
            var $checkbox = $("input[id*='agreement']").not("input[id*='agreement2']");

            me._on($checkbox, 'click', function (event) {
                if ($checkbox.is(':checked')) {
                    $.loadingIndicator.open();
                    console.log('DBG2');
                    setTimeout(
                        function() {
                            $.loadingIndicator.close();
                        },
                        2000
                    );
                }
            });
        },
    });
})(jQuery, window);
