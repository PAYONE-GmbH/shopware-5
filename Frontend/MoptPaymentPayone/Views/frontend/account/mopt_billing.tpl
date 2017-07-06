{extends file="frontend/account/billing.tpl"}

{block name="frontend_index_header_javascript_jquery" append}
    <script src="{link file='frontend/_resources/javascript/mopt_account.js'}"></script>
{/block}

{block name="frontend_index_content" append}
    <input name="moptAddressCheckNeedsUserVerification" type="hidden" 
           data-moptAddressCheckNeedsUserVerification="{$moptAddressCheckNeedsUserVerification}" 
           data-moptAddressCheckVerificationUrl="{url controller=moptAjaxPayone action=ajaxVerifyAddress forceSecure}" 
               id="moptAddressCheckNeedsUserVerification"/>
{/block}
