{namespace name=backend/mopt_config_payone/main}
<div class="form-group has-feedback has-error">
    {if !empty($content)} <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" data-title="{s name="fieldlabel/help"}PAYONE Hilfe{/s}" data-content="{$content}">{/if}
    <label for="{$id}" class="text-left col-md-3 control-label">{$label}</label>
    <div class="col-md-6">
        <input type="checkbox" class="form-control" pattern='{$pattern}' minlength="{$minlength}" maxlength="{$maxlength}" id="{$id}" name="{$id}" spellcheck="false" >
    </div>
</div>
