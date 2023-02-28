{extends file="parent:frontend/checkout/shipping_payment_core.tpl"}

{block name="frontend_checkout_shipping_payment_core_payment_fields"}
    {$smarty.block.parent}
    <script type="text/javascript">

        // IE 11 Windows 8.1 compatibility
        // see https://developer.mozilla.org/de/docs/Web/JavaScript/Reference/Global_Objects/Object/assign#Polyfill
        if (typeof Object.assign != 'function') {
            // Must be writable: true, enumerable: false, configurable: true
            Object.defineProperty(Object, "assign", {
                value: function assign(target, varArgs) { // .length of function is 2
                    'use strict';
                    if (target == null) { // TypeError if undefined or null
                        throw new TypeError('Cannot convert undefined or null to object');
                    }

                    var to = Object(target);

                    for (var index = 1; index < arguments.length; index++) {
                        var nextSource = arguments[index];

                        if (nextSource != null) { // Skip over if undefined or null
                            for (var nextKey in nextSource) {
                                // Avoid bugs when hasOwnProperty is shadowed
                                if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
                                    to[nextKey] = nextSource[nextKey];
                                }
                            }
                        }
                    }
                    return to;
                },
                writable: true,
                configurable: true
            });
        }

        //<![CDATA[
        if (typeof $ !== 'undefined') {
            if($.isFunction($('select:not([data-no-fancy-select="true"])').selectboxReplacement)) {
                $('select:not([data-no-fancy-select="true"])').selectboxReplacement();
            }
            $('.moptPayoneIban').moptPayoneIbanValidator();
            $('.moptPayoneNumber').moptPayoneNumberValidator();
            $('.moptPayoneBankcode').moptPayoneBankcodeValidator();
            $('#shippingPaymentForm').moptPayoneSubmitPaymentForm();
        }
        //]]>
    </script>
{/block}