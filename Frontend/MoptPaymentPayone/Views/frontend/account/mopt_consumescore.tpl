{extends file="parent:frontend/account/payment.tpl"}

{block name="frontend_index_header_javascript_jquery"}
    {$smarty.block.parent}
    <script defer src="{link file='frontend/_resources/javascript/mopt_account.js'}">
    </script>
{/block}

{block name="frontend_index_content"}
    {$smarty.block.parent}
    <input name="moptConsumerScoreCheckNeedsUserAgreement" type="hidden" 
           data-moptConsumerScoreCheckNeedsUserAgreement="{$moptConsumerScoreCheckNeedsUserAgreement}" 
           data-moptConsumerScoreCheckNeedsUserAgreementUrl="{url controller=moptAjaxPayone action=ajaxGetConsumerScoreUserAgreement forceSecure}" 
               id="moptConsumerScoreCheckNeedsUserAgreement"/>
{/block}
