{namespace name=backend/mopt_config_payone/main}
<div class="form-group has-feedback has-error  menu-level-standard  menu-level-experte">
    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" data-title="{s name="fieldlabel/help"}PAYONE Hilfe{/s}" data-bs-content="ID des zu verwendenden Accounts">
    <label for="{$id}" class="text-left col-md-3 control-label">{$label}</label>
    <div class="col-md-6">
        <input type="text" class="form-control needs-validation" pattern='{$pattern}' minlength="{$minlength}" maxlength="{$maxlength}" id="{$id}" name="{$id}" aria-describedby="{$id}-status" >
    </div>
</div>