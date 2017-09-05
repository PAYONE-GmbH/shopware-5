{extends file="parent:frontend/account/billing.tpl"}

{block name="frontend_index_header_javascript_jquery"}
    {$smarty.block.parent}
    <script defer src="{link file='frontend/_resources/javascript/mopt_account.js'}">
    </script>
{/block}

{block name="frontend_index_content"}
    {$smarty.block.parent}
    <input name="moptAddressCheckNeedsUserVerification" type="hidden" 
           data-moptAddressCheckNeedsUserVerification="{$moptAddressCheckNeedsUserVerification}" 
           data-moptAddressCheckVerificationUrl="{url controller=moptAjaxPayone action=ajaxVerifyAddress forceSecure}" 
               id="moptAddressCheckNeedsUserVerification"/>
{/block}
