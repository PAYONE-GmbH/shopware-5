{namespace name=backend/mopt_config_payone/main}
<div class="form-group has-feedback has-error">
    <label for="{$id}" class="text-left col-md-3 control-label">{$label}</label>
    <div class="col-md-6">
        <select class="form-control " pattern='{$pattern}' id="{$id}" name="{$id}" aria-describedby="{$id}-status" >
            <option value="0">{s name="fieldvalue/off"}Aus{/s}</option>
            <option value="1">{s name="fieldvalue/onconfirm"}Auf der Confirm Seite{/s}</option>
            <option value="2">{s name="fieldvalue/global"}Global{/s}</option>
        </select>
        <span class="bi form-control-feedback bi-remove" aria-hidden="true"></span>
        <span id="{$id}-status" class="sr-only">(success)</span>
        <div class="help-block with-errors"></div>
    </div>
</div>
