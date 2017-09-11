function moptAccountReady() {

    $.plugin('moptAddressCheckNeedsUserVerification', {
    defaults: {
        moptAddressCheckNeedsUserVerification: false,
        moptAddressCheckVerificationUrl: false
    },
    init: function () {
        var me = this;
        me.applyDataAttributes();

        if (me.opts.moptAddressCheckNeedsUserVerification && me.opts.moptAddressCheckVerificationUrl) {
            $(document).ready(function () {
                $.post(me.opts.moptAddressCheckVerificationUrl, function (data) {
                    $('.content-main').prepend(data);
                });
            });
        }
    },
    destroy: function () {
        var me = this;
        me._destroy();
    }
});

$.plugin('moptShippingAddressCheckNeedsUserVerification', {
    defaults: {
        moptShippingAddressCheckNeedsUserVerification: false,
        moptShippingAddressCheckVerificationUrl: false
    },
    init: function () {
        var me = this;
        me.applyDataAttributes();

        if (me.opts.moptAddressCheckNeedsUserVerification && me.opts.moptShippingAddressCheckVerificationUrl) {
            $(document).ready(function () {
                $.post(me.opts.moptShippingAddressCheckVerificationUrl, function (data) {
                    $('.content-main').prepend(data);
                });
            });
        }
    },
    destroy: function () {
        var me = this;
        me._destroy();
    }
});

$.plugin('moptConsumerScoreCheckNeedsUserAgreement', {
    defaults: {
        moptConsumerScoreCheckNeedsUserAgreement: false,
        moptConsumerScoreCheckNeedsUserAgreementUrl: false
    },
    init: function () {
        var me = this;
        me.applyDataAttributes();

        if (me.opts.moptConsumerScoreCheckNeedsUserAgreement && me.opts.moptConsumerScoreCheckNeedsUserAgreementUrl) {
            $(document).ready(function () {
                $.post(me.opts.moptConsumerScoreCheckNeedsUserAgreementUrl, function (data) {
                    $('.content-main').prepend(data);
                });
            });
        }
    },
    destroy: function () {
        var me = this;
        me._destroy();
    }
});


$('#moptAddressCheckNeedsUserVerification').moptAddressCheckNeedsUserVerification();
$('#moptShippingAddressCheckNeedsUserVerification').moptShippingAddressCheckNeedsUserVerification();
$('#moptConsumerScoreCheckNeedsUserAgreement').moptConsumerScoreCheckNeedsUserAgreement();

}

$(document).ready(function(){
    if (typeof document.asyncReady == "undefined")
    {
        moptAccountReady();
    }
});

if (typeof document.asyncReady !== "undefined") {


    document.asyncReady(function () {
        moptAccountReady();
    });
}