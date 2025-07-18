{namespace name=backend/mopt_config_payone/main}
<div class="row">
    <div class="col-md-8">
        <div class="btn-group">
            <button id="paymentmethodsdropdown" type="button" class="btn-payone-fixed btn-payone btn dropdown-toggle" data-toggle="dropdown">
                <span class="selection">{s name="fieldlabel/allpayments"}Alle Zahlarten - Global{/s}</span><span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li><a href="#" id="0" >{s name="fieldlabel/allpayments"}Alle Zahlarten - Global{/s}</a></li>
                {foreach from=$payonepaymentmethods item=paymentmethod}
                    <li><a href="#" id="{$paymentmethod.id}">{$paymentmethod.description}</a></li>
                {/foreach}
            </ul>
        </div>
    </div>
</div>
