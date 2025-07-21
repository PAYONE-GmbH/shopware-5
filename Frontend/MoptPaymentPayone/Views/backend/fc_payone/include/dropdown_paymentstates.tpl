{namespace name=backend/mopt_config_payone/main}
<div class="form-group has-feedback has-error">
    <label for="{$id}" class="text-left col-md-3 control-label">{$label}</label>
    <div class="col-md-6">
        <select class="form-control" id="{$id}" name="{$id}" aria-describedby="{$id}-status" >
            {foreach from=$payonepaymentstates item=paymentstate}
                <option value="{$paymentstate.id}">{$paymentstate.description}</option>
            {/foreach}
        </select>
        <span class="bi form-control-feedback bi-remove" aria-hidden="true"></span>
        <div class="help-block with-errors"></div>
    </div>
</div>