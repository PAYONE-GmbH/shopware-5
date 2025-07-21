{namespace name=backend/mopt_config_payone/main}
<div class="form-group has-feedback has-error menu-level-experte">
{if !empty($content)} <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" data-title="PAYONE Hilfe" data-content="{$content}">{/if}
    <label for="{$id}" class="text-left col-md-3 control-label">{$label}</label>
    <div class="col-md-6">
        <select class="form-control " id="{$id}" name="{$id}" aria-describedby="{$id}-status" >
            <option value='1'>{s name="fieldvalue/yes"}Ja{/s}</option>
            <option value='0'>{s name="fieldvalue/no"}Nein{/s}</option>
        </select>
        <div class="help-block with-errors"></div>
    </div>
</div>
