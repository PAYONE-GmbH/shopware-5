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

{block name="frontend_checkout_confirm_product_table"}
    <div class='panel has--border'>
        <div class="panel--title is--underline">
            {s name='Overview' namespace="frontend/MoptPaymentPayone/payment"}Overview{/s}
        </div>
        <div id="paypal_installment_overview" class="paypal_installment_overview panel--body">
            <table>
                <tr>
                    <td>{s name='NoOfInstallments' namespace="frontend/MoptPaymentPayone/payment"}No. of installments{/s}:</td>
                    <td>{$Installment['term']}</td>
                </tr>
                <tr class="final">
                    <td><strong>{s name='MonthlyInstallment' namespace="frontend/MoptPaymentPayone/payment"}Monthly installment{/s}</strong></td>
                    <td><strong>{$Installment.monthly_payment_value|number_format:2:",":"."} {$Installment.monthly_payment_currency}</strong></td>
                </tr>
                <tr>
                    <td>{s name='interestAmount' namespace="frontend/MoptPaymentPayone/payment"}Interest Amount{/s}:</td>
                    <td>{$Installment.total_interest_value|number_format:2:",":"."} {$Installment.total_interest_currency}</td>
                </tr>
                <tr>
                    <td><strong>{s name='Total' namespace="frontend/MoptPaymentPayone/payment"}Total{/s}:</strong></td>
                    <td><strong>{$Installment.total_cost_value|number_format:2:",":"."} {$Installment.total_cost_currency}</strong></td>
                </tr>
            </table>
        </div>
    </div>
    {$smarty.block.parent}
{/block}

{* disable changing quantities and delete basket items on confirm page *}
{block name='frontend_checkout_cart_item_quantity_selection'}
    {if !$sBasketItem.additional_details.laststock || ($sBasketItem.additional_details.laststock && $sBasketItem.additional_details.instock > 0)}
        <form name="basket_change_quantity{$sBasketItem.id}" class="select-field" method="post"
              action="{url action='changeQuantity' sTargetAction=$sTargetAction}">
            <select name="sQuantity" data-auto-submit="false" disabled>
                {section name="i" start=$sBasketItem.minpurchase loop=$sBasketItem.maxpurchase+1 step=$sBasketItem.purchasesteps}
                    <option value="{$smarty.section.i.index}"
                            {if $smarty.section.i.index==$sBasketItem.quantity}selected="selected"{/if}>
                        {$smarty.section.i.index}
                    </option>
                {/section}
            </select>
            <input type="hidden" name="sArticle" value="{$sBasketItem.id}"/>
        </form>
    {else}
        {s name="CartColumnQuantityEmpty" namespace="frontend/checkout/cart_item"}{/s}
    {/if}
{/block}

{* Remove product from basket *}
{block name='frontend_checkout_cart_item_delete_article'}
    <div class="panel--td column--actions">
        <form action="{url action='deleteArticle' sDelete=$sBasketItem.id sTargetAction=$sTargetAction}"
              method="post">
            <button type="submit" disabled class="btn is--small column--actions-link"
                    title="{"{s name='CartItemLinkDelete'}{/s}"|escape}">
                <i class="icon--cross"></i>
            </button>
        </form>
    </div>
{/block}