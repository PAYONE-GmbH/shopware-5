{extends file="parent:frontend/checkout/confirm.tpl"}

{* change register Steps to 1 Ihre Adresse, 2 Versandart, 3 Prüfen und Bestellen *}
{* Step box *}
{block name='frontend_index_navigation_categories_top'}
    {* Step box *}
    <div class="steps--container container">
        <div class="steps--content panel--body center">
            {block name='frontend_register_steps'}
                <ul class="steps--list">

                    {* First Step - Address *}
                    {block name='frontend_register_steps_basket'}
                        <li class="steps--entry step--basket{if $sStepActive=='address'} is--active{/if}">
                            <span class="icon">1</span>
                            <span class="text"><span class="text--inner">Adresse und Zahlart </span></span>
                        </li>
                    {/block}

                    {* Spacer *}
                    {block name='frontend_register_steps_spacer1'}
                        <li class="steps--entry steps--spacer">
                            <i class="icon--arrow-right"></i>
                        </li>
                    {/block}

                    {* Second Step - Payment *}
                    {block name='frontend_register_steps_register'}
                        <li class="steps--entry step--register{if $sStepActive=='paymentShipping'} is--active{/if}">
                            <span class="icon">2</span>
                            <span class="text"><span class="text--inner">Versandart</span></span>
                        </li>
                    {/block}

                    {* Spacer *}
                    {block name='frontend_register_steps_spacer2'}
                        <li class="steps--entry steps--spacer">
                            <i class="icon--arrow-right"></i>
                        </li>
                    {/block}

                    {* Third Step - Confirmation *}
                    {block name='frontend_register_steps_confirm'}
                        <li class="steps--entry step--confirm is--active">
                            <span class="icon">3</span>
                            <span class="text"><span class="text--inner">Prüfen und Bestellen</span></span>
                        </li>
                    {/block}
                </ul>
            {/block}
        </div>
    </div>
{/block}

{* SW 5.0, 5.1 Disable BillingAddress Action Buttons *}
{block name="frontend_checkout_confirm_left_billing_address_actions"}
{/block}

{* SW 5.2 - 5.3, 5.4? Change PaymentMean Selection Action Button to FatchipBSPayoneMasterpassCheckout  Controller *}
{* for shippingAddress != billingAddress *}
{* Billing: *}
{block name="frontend_checkout_confirm_information_addresses_billing_panel_actions"}
{/block}

{* SW 5.2 - 5.3, 5.4? Change PaymentMean Selection Action Button to FatchipBSPayoneMasterpassCheckout Controller *}
{* for shippingAddress = billingAddress *}
{* Billing and Shipping: *}
{* both template overrides do not work in 5.3? WTF *}
{block name='frontend_checkout_confirm_information_addresses_equal_panel_actions'}
<div class="panel--actions is--wide">
    <div class="address--actions-change">
    </div>
</div>
{/block}

{block name='frontend_checkout_confirm_information_addresses_equal_panel_shipping'}
<div class="shipping--panel">
</div>
{/block}

{* SW 5.0 , 5.1 Disable ShippingAddress Action Buttons *}
{block name="frontend_checkout_confirm_left_shipping_address_actions"}
{/block}

{* SW 5.2 - 5.3, 5.4? Change PaymentMean Selection Action Button to FatchipBSPayoneMasterpassCheckout Controller *}
{* for shippingAddress != billingAddress *}
{block name="frontend_checkout_confirm_information_addresses_shipping_panel_actions"}
{/block}


{* SW 5.0 - 5.4 Change PaymentMean Selection Action Button to FatchipBSPayoneMasterpassCheckout  Controller *}
{block name='frontend_checkout_confirm_left_payment_method_actions'}
    <div class="panel--actions is--wide payment--actions">
        {* Action buttons *}
        <a href="{url controller=FatchipBSPayoneMasterpassCheckout action=shippingPayment sTarget=checkout}" class="btn is--small btn--change-payment">
            Ändern
        </a>
    </div>
{/block}
