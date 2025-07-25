{namespace name=backend/mopt_config_payone/main}
<div class="form-group has-feedback has-error">
    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" data-title="{s name="fieldlabel/help"}PAYONE Hilfe{/s}" data-content="{$content}">
    <label for="{$id}" class="text-left col-md-3 control-label">{$label}</label>
    <div class="col-md-6">
        <select class="form-control " pattern='{$pattern}' id="{$id}" name="{$id}" aria-describedby="{$id}-status" >
            <option value="Vorautorisierung">{s name="fieldvalue/preauthorisation"}Vorauthorisierung{/s}</option>
            <option value="Autorisierung">{s name="fieldvalue/authorisation"}Authorisierung{/s}</option>
        </select>
        <span class="bi form-control-feedback bi-remove" aria-hidden="true"></span>
        <span id="{$id}-status" class="sr-only">(success)</span>
        <div class="help-block with-errors"></div>
    </div>
</div>
