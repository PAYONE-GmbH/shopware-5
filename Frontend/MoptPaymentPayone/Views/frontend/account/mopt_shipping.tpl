{extends file="parent:frontend/account/shipping.tpl"}

{block name="frontend_index_header_javascript_jquery"}
    {$smarty.block.parent}
    <script defer src="{link file='frontend/_resources/javascript/mopt_account.js'}">
    </script>
{/block}

{block name="frontend_index_content"}
    {$smarty.block.parent}
    <input name="moptShippingAddressCheckNeedsUserVerification" type="hidden" 
           data-moptShippingAddressCheckNeedsUserVerification="{$moptShippingAddressCheckNeedsUserVerification}" 
           data-moptShippingAddressCheckVerificationUrl="{url controller=moptAjaxPayone action=ajaxVerifyShippingAddress forceSecure}" 
               id="moptShippingAddressCheckNeedsUserVerification"/>
{/block}
