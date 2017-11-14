function moptCheckoutReady() {

    $.plugin('moptUpdateMandateAgreement', {
        init: function () {
            var me = this;

            me.$el.bind('change', function (e) {
                if (me.$el.is(':checked')) {
                    me.$el.prop('checked', true);
                    me.$el.attr('checked', 'checked');
                    $('input[name=moptMandateConfirm]').val(1);
                }
                else {
                    me.$el.prop('checked', false);
                    me.$el.attr('checked', false);
                    $('input[name=moptMandateConfirm]').val(0);
                }
            });
        },
        destroy: function () {
            var me = this;
            me._destroy();
        }
    });

    $.plugin('moptAgbChecked', {
        defaults: {
            mopt_payone__agb_checked: false
        },
        init: function () {
            var me = this;
            me.applyDataAttributes();

            if (me.opts.mopt_payone__agb_checked) {
                $('#sAGB').prop('checked', true);
                $('#sAGB').attr('checked', 'checked');
                $('input[name=sAGB]').val(1);
            }
        },
        destroy: function () {
            var me = this;
            me._destroy();
        }
    });

    $('#mandate_status').moptUpdateMandateAgreement();
    $('#moptAgbChecked').moptAgbChecked();
}


moptCheckoutReady();