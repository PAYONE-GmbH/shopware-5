<div class="form-group has-feedback has-error">
    {if !empty($content)} <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" data-title="PAYONE Hilfe" data-content="{$content}">{/if}
    {if !empty($label)}<label for="{$id}" class="text-left col-md-3 control-label">{$label}</label>{/if}
    <div class="col-md-6">
        <input type="text" class="form-control needs-validation" pattern='{$pattern}' minlength="{$minlength}" maxlength="{$maxlength}" id="{$id}" name="{$id}" aria-describedby="{$id}-status" spellcheck="false"
        {if !empty($value)} value="{$value}"{/if} {if !empty($size)} size="{$size}"{/if} {if !empty($placeolder)} placeholder="{$placeholder}"{/if}>
    </div>
</div>
