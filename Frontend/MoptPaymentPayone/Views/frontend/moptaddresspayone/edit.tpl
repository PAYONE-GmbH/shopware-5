{extends file="parent:frontend/address/index.tpl"}
{namespace name="frontend/address/index"}

{* Breadcrumb *}
{block name="frontend_index_start"}
    {$smarty.block.parent}
    {$sBreadcrumb[] = ["name"=>"{s name="AddressesTitleEdit"}Change address{/s}", "link"=>{url id=$formData.id}]}
{/block}

{* Main content *}
{block name="frontend_index_content"}
    <div class="account--address account--address-form account--content" data-register="true">

        {* Address headline *}
        {block name="frontend_address_form_headline"}
            <div class="account--welcome">
                <h1 class="panel--title">
                    {s name="AddressesTitleEdit"}Change address{/s}
                </h1>
            </div>
        {/block}

        {block name="frontend_address_form_content"}
            {if $sTarget}
                {$url={url controller=moptaddresspayone action=edit id=$formData.id sTarget=$sTarget sTargetAction=$sTargetAction}}
            {else}
                {$url={url controller=moptaddresspayone action=edit id=$formData.id}}
            {/if}
            <div class="panel has--border is--rounded">
                <form name="frmAddresses" method="post" action="{$url}">
                    {include file="frontend/address/form.tpl" formAction="{$url}"}
                </form>
            </div>
        {/block}

    </div>
{/block}

{* remove sidebars *}
{block name="frontend_account_sidebar"}
{/block}

{block name="frontend_index_sidebar"}
{/block}

{* hide breadcrumb bar *}
{block name='frontend_index_breadcrumb'}{/block}