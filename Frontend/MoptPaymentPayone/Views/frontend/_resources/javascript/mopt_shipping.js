function moptShippingReady() {

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

    $('#moptShippingAddressCheckNeedsUserVerification').moptShippingAddressCheckNeedsUserVerification();

}

$(document).ready(function(){
    if (typeof document.asyncReady == "undefined")
    {
        moptShippingReady();
    }
});

if (typeof document.asyncReady !== "undefined") {

    document.asyncReady(function() {
        moptShippingReady();
});
}