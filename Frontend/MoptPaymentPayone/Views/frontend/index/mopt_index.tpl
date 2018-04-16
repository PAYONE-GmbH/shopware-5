{block name="frontend_index_after_body"}
    {assign var='isAsyncJsLoading' value=(isset($theme.asyncJavascriptLoading) && $theme.asyncJavascriptLoading)}
    <input type="hidden" id="jsLoadMethod" value="{if $isAsyncJsLoading}a{/if}sync">
    {$smarty.block.parent}
{/block}
