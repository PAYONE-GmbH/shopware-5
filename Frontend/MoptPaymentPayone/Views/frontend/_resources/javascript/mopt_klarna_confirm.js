;(function ($, window) {
    'use strict';

    var pluginRegistered = false;

    reset();

    function reset() {
        if (!window.Klarna) {
            destroyPlugin();

            return;
        }

        if (pluginRegistered) {
            updatePlugin();
        } else {
            registerPlugin();
        }
    }

    function registerPlugin() {
        StateManager.addPlugin('#confirm--form', 'payoneKlarnaConfirm', null, null);
        pluginRegistered = true;
    }

    function updatePlugin() {
        StateManager.updatePlugin('#confirm--form', 'payoneKlarnaConfirm');
    }

    function destroyPlugin() {
        StateManager.destroyPlugin('#confirm--form', 'payoneKlarnaConfirm');
        StateManager.removePlugin('#confirm--form', 'payoneKlarnaConfirm', null);
    }

    $.plugin('payoneKlarnaConfirm', {
        defaults: {},
        finalizeApproved: false,
        data: $('#mopt_payone__klarna_information').data(),


        init: function () {
            var me = this;

            if (typeof me.data == 'undefined') {
                return;
            }

            me.registerEventListeners();
        },

        registerEventListeners: function () {
            var me = this;

            me._on(me.$el, 'submit', function (event) {
                me.submitHandler(event);
            });
        },

        submitHandler: function (event) {
            var me = this;

            if (me.finalizeApproved) {
                return;
            }

            event.preventDefault();

            // disable submit buttons
            $(me.$el.get(0).elements).filter(':submit').each(function (_, element) {
                element.disabled = true;
            });

            window.Klarna.Payments.init({
                client_token: me.data['clientToken']
            });

            me.finalize();
        },

        finalize: function () {
            var me = this;

            window.Klarna.Payments.finalize({
                    payment_method_category: 'pay_now'
                }, {},
                function (res) {
                    var url = me.data['storeAuthorizationToken-Url'];

                    if (res['approved']) {
                        me.finalizeApproved = true;

                        if (res['authorization_token']) {
                            var parameters = {'authorizationToken': res['authorization_token']};

                            // store authorization_token
                            $.ajax({method: "POST", url: url, data: parameters});
                        }

                        me.$el.submit();

                    } else {
                        // TODO rw :error handling, when finalize fails
                        $(me.$el.get(0).elements).filter(':submit').each(function (_, element) {
                            element.disabled = false;
                        });
                    }
                }
            );
        }
    });
})(jQuery, window);
