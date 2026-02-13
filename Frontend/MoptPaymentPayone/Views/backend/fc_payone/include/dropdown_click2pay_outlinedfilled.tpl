{namespace name=backend/mopt_config_payone/main}
<div class="form-group has-feedback has-error">
    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" data-title="{s name="fieldlabel/help"}PAYONE Hilfe{/s}" data-content="{$content}">
    <label for="{$id}" class="text-left col-md-3 control-label">{$label}</label>
    <div class="col-md-6">
        <select class="form-control " pattern='{$pattern}' id="{$id}" name="{$id}" aria-describedby="{$id}-status" >
            <option value="outlined">{s name="fieldvalue/outlined"}Umranded{/s}</option>
            <option value="filled">{s name="fieldvalue/filled"}Gefüllt{/s}</option>
        </select>
        <div class="help-block with-errors"></div>
    </div>
</div>
