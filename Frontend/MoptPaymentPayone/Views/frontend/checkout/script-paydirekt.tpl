<script>
    function registerPaydirektButtonEvent() {
        $('#paydirekt-ex-btn').on('click', function(e) {

            $.loadingIndicator.open();

            paydirektGenericPaymentCall();
        });
    }

    $(document).ready(function() {
        registerPaydirektButtonEvent();
    });

    function paydirektGenericPaymentCall() {
        var url = '{url controller="moptAjaxPayone" action="payDirektOrderCallAction" forceSecure}';
        $.ajax({
                url: url,
                success: function (data) {
                    $.loadingIndicator.close();
                    //@TODO: window.location.href = response.redirect;
                },
                error: function (data) {
                    $.loadingIndicator.close();
                    window.location.href = '{url controller="checkout" action="cart" error="errorCode" forceSecure}';
                },
                timeout: 30000
            }
        );
    }
</script>

