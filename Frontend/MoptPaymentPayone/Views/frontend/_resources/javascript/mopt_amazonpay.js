$.plugin('addressBookWidgetDiv', {
    init: function () {
        var me = this;
        var jsloadMethod = document.querySelector('#jsLoadMethod').value;
        var isAsyncJsLoading = (jsloadMethod === 'async' || jsloadMethod === 'default');

        if (isAsyncJsLoading) {
            if (typeof document.asyncReady !== "undefined") {
                document.asyncReady(function () {
                    moptAmazonReady();
                });
            }
        } else {
            $( document ).ready(function() {
                moptAmazonReady();
            });
        }
    },
    destroy: function () {
        var me = this;
        me._destroy();
    }
});

$('#addressBookWidgetDiv').addressBookWidgetDiv();
