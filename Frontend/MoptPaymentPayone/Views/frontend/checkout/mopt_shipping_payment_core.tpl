{extends file="frontend/checkout/shipping_payment_core.tpl"}

{block name="frontend_checkout_shipping_payment_core_payment_fields" append}
    <script type="text/javascript">
        //<![CDATA[
        if (typeof $ !== 'undefined') {
            if($.isFunction($('select:not([data-no-fancy-select="true"])').selectboxReplacement)) {
                $('select:not([data-no-fancy-select="true"])').selectboxReplacement();
            }
            $('.moptPayoneIbanBic').moptPayoneIbanBicValidator();
            $('.moptPayoneNumber').moptPayoneNumberValidator();
            $('.moptPayoneBankcode').moptPayoneBankcodeValidator();
            $('#shippingPaymentForm').moptPayoneSubmitPaymentForm();
        }
        //]]>
    </script>
{/block}