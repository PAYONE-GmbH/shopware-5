{namespace name=backend/mopt_config_payone/main}
<div class="form-group has-feedback has-error menu-level-experte">
    <label for="{$id}" class="text-left col-md-3 control-label">{$label}</label>
    <div class="col-md-6">
        <select class="form-control " id="{$id}" name="{$id}" aria-describedby="{$id}-status" >
            <option value='true'>{s name="fieldvalue/yes"}Ja{/s}</option>
            <option value='false'>{s name="fieldvalue/no"}Nein{/s}</option>
        </select>
        <div class="help-block with-errors"></div>
    </div>
</div>