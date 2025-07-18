<div class="form-group has-feedback has-error menu-level-experte">
    <label for="{$id}" class="text-left col-md-3 control-label">{$label}</label>
    <div class="col-md-6">
        <select class="form-control " pattern='{$pattern}' id="{$id}" name="{$id}" aria-describedby="{$id}-status" >
            <option value="1">Live</option>
            <option value="0">Test</option>
        </select>
        <div class="help-block with-errors"></div>
    </div>
</div>