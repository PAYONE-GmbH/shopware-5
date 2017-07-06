{extends file="frontend/checkout/confirm.tpl"}

{block name="frontend_index_header_javascript_jquery" append}
    <script src="{link file='frontend/_resources/javascript/mopt_shipping.js'}"></script>
{/block}

{block name="frontend_checkout_confirm_agb_checkbox" append}
    <input name="moptShippingAddressCheckNeedsUserVerification" type="hidden" 
           data-moptShippingAddressCheckNeedsUserVerification="{$moptShippingAddressCheckNeedsUserVerification}" 
           data-moptShippingAddressCheckVerificationUrl="{url controller=moptAjaxPayone action=ajaxVerifyShippingAddress forceSecure}" 
               id="moptShippingAddressCheckNeedsUserVerification"/>
{/block}
