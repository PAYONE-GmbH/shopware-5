{namespace name=backend/mopt_config_payone/main}
<div class="form-group has-feedback has-error">
    {if !empty($content)} <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" data-title="{s name="fieldlabel/help"}PAYONE Hilfe{/s}" data-content="{$content}">{/if}
    <label for="{$id}" class="text-left col-md-3 control-label">{$label}</label>
    <div class="col-md-6">
        <select class="form-control " pattern='{$pattern}' id="{$id}" name="{$id}" aria-describedby="{$id}-status" >
            <option value="0">{s name="fieldvalue/off"}Aus{/s}</option>
            <option value="1">{s name="fieldvalue/onconfirm"}Auf der Confirm Seite{/s}</option>
            <option value="2">{s name="fieldvalue/global"}Global{/s}</option>
        </select>
        <div class="help-block with-errors"></div>
    </div>
</div>
