{extends file="parent:backend/index/header.tpl"}
{block name="backend/base/header/css"}
    {$smarty.block.parent}
    <style type="text/css">
        .payoneicon {
            background:url({link file="backend/_resources/images/payone-favicon.png"})no-repeat 0 0 !important;
            background-size: 16px 16px !important;
        }
    </style>
{/block}
