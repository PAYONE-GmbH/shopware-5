{namespace name=backend/mopt_config_payone/main}
<div class="form-group has-feedback has-error">
    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" data-title="PAYONE Hilfe" data-content="{$content}">
    <label for="{$id}" class="text-left col-md-3 control-label">{$label}</label>
    <div class="col-md-6">
        <select class="form-control " pattern='{$pattern}' id="{$id}" name="{$id}" aria-describedby="{$id}-status" >
            <option value="rect">{s name="fieldvalue/standard"}Standard{/s}</option>
            <option value="pill">{s name="fieldvalue/pill"}Runde Ecken{/s}</option>
            <option value="sharp">{s name="fieldvalue/sharp"}Spitze Ecken{/s}</option>
        </select>
    </div>
</div>