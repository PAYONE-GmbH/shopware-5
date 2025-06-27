{namespace name=backend/mopt_config_payone/main}
<div class="form-group has-feedback has-error">
    <img src="{link file='backend/_resources/images/information.png'}" data-toggle="popover" data-title="PAYONE Hilfe" data-content="{$content}">
    <label for="{$id}" class="text-left col-md-3 control-label">{$label}</label>
    <div class="col-md-6">
        <select class="form-control " pattern='{$pattern}' id="{$id}" name="{$id}" aria-describedby="{$id}-status" >
            <option value="buy">{s name="fieldvalue/buy"}Buy{/s}</option>
            <option value="checkout">{s name="fieldvalue/checkout"}Checkout{/s}</option>
            <option value="order">{s name="fieldvalue/order"}Order{/s}</option>
            <option value="pay">{s name="fieldvalue/pay"}Pay{/s}</option>
            <option value="plain">{s name="fieldvalue/plain"}Plain{/s}</option>
        </select>
    </div>
</div>