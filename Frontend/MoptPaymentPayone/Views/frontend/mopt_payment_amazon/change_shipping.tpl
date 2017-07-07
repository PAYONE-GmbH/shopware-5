<div class="dispatch--method-list panel has--border is--rounded">

    {block name='frontend_checkout_shipping_headline'}
        <h3 class="dispatch--method-headline panel--title is--underline">{s namespace='frontend/checkout/shipping_payment' name='ChangeShippingTitle'}{/s}</h3>
    {/block}

    {block name='frontend_checkout_shipping_content'}
        <div class="panel--body is--wide block-group">
            {foreach $sDispatches as $dispatch}
                {block name="frontend_checkout_dispatch_container"}
                    <div class="content--confirm dispatch--method{if $dispatch@last} method_last{else} method{/if} block">
                        {* Radio Button *}
                        {block name='frontend_checkout_dispatch_shipping_input_radio'}
                            <div class="method--input" style="padding:.625rem .625rem .625rem .625rem;">
                                <input type="radio" id="confirm_dispatch{$dispatch.id}" data-auto-submit="true" class="radio auto_submit" value="{$dispatch.id}" name="sDispatch"{if $dispatch.id eq $sDispatch.id} checked="checked"{/if} />
                            </div>
                        {/block}

                        {* Method Name *}
                        {block name='frontend_checkout_dispatch_shipping_input_label'}
                            <div class="method--label is--first" style="padding:.625rem .625rem .625rem .625rem;">
                                <label class="method--name is--strong" for="confirm_dispatch{$dispatch.id}">{$dispatch.name}</label>
                            </div>
                        {/block}

                        {* Method Description *}
                        {block name='frontend_checkout_shipping_fieldset_description'}
                            {if $dispatch.description}
                                <div class="method--description" style="padding:.625rem .625rem .625rem .625rem;">
                                    {$dispatch.description}
                                </div>
                            {/if}
                        {/block}
                    </div>
                {/block}
            {/foreach}
        </div>
    {/block}
</div>

{block name="frontend_checkout_shipping_payment_core_payment_fields" }
{/block}