;(function ($, window) {
    'use strict';
    var data = $('#fatchipMoptPaySafeInformation').data();
    var pluginRegistered = false;

    var isPaySafeScriptLoaded = false;

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
        if ($('#paysafe-token-script').length) {
            isPaySafeScriptLoaded = true;
        }
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

        loadPaySafeJS: function () {
            var me = this;

            var _generateToken = function (callback) {
                var url = data['getSessionId-Url'];

                $.ajax({method: "GET", url: url}).done(function (response) {
                    callback(response.token)
                });
            };

            var _loadPaySafeJS = function (token) {
                var script = document.createElement("script");

                script.id = "paysafe-token-script";
                script.type = "text/javascript";
                script.src = "https://h.online-metrix.net/fp/tags.js?org_id=363t8kgq&session_id=" + token;
                $('body').append(script);

                me.isPaySafeScriptloaded = true;
                $.loadingIndicator.close();
            };

            _generateToken(_loadPaySafeJS);
        },

        registerEventListeners: function () {
            var me = this;
            var $checkbox = $("input[id*='agreement']").not("input[id*='agreement2']");

            me._on($checkbox, 'click', function (event) {
                if ($checkbox.is(':checked') && !isPaySafeScriptLoaded) {
                    $.loadingIndicator.open();

                    me.loadPaySafeJS();
                }
            });
        },
    });
})(jQuery, window);
