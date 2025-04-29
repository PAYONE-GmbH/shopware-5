{namespace name=backend/mopt_config_payone/main}
<div class="form-group has-feedback has-error menu-level-experte">
    <label for="{$id}" class="text-left col-md-3 control-label">{$label}</label>
    <div class="col-md-6">
        <select class="form-control " pattern='{$pattern}' id="{$id}" name="{$id}" aria-describedby="{$id}-status" >
            <option value="tel">{s name="fieldvalue/numeric"}Numerisch{/s}</option>
            <option value="password">{s name="fieldvalue/password"}Passwort{/s}</option>
            <option value="text">{s name="fieldvalue/text"}Text{/s}</option>
            <option value="select">{s name="fieldvalue/choice"}Auswahl{/s}</option>
        <span class="bi form-control-feedback bi-remove" aria-hidden="true"></span>
        <span id="{$id}-status" class="sr-only">(success)</span>
        <div class="help-block with-errors"></div>
    </div>
</div>
