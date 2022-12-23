{extends file="parent:frontend/checkout/confirm.tpl"}

{* Hide sidebar left *}
{block name='frontend_index_content_left'}{/block}

{* Hide Step box *}
     {block name='frontend_index_navigation_categories_top'}
     {/block}


{block name='frontend_checkout_confirm_form'}
    {block name='frontend_checkout_confirm_information_wrapper'}
    {/block}
{/block}

{* disable standard "Agb" Box, used by SW 5.1 *}
{block name='frontend_checkout_confirm_tos_panel'}
{/block}

{block name='frontend_index_content_top'}

    <style type="text/css">

        /*
        Please include the min-width, max-width, min-height
        and max-height if you plan to use a relative CSS unit
        measurement to make sure the widget renders in the
        optimal size allowed.
        */
        #addressBookWidgetDiv {
            min-width: 300px;
            width: 100%;
            max-width: 550px;
            min-height: 228px;
            height: 240px;
            max-height: 400px;
        {if $payoneAmazonReadOnly}
            displayMode: "Read";
        {/if}

        }
        #walletWidgetDiv {
            min-width: 300px;
            width: 100%;
            max-width: 550px;
            min-height: 228px;
            height: 240px;
            max-height: 400px;
        }


        /* override standard Shopware style sheet for table alignments
        */

        @media screen and (min-width: 30em) {
            .confirm--content .product--table .column--total-price {
                width: 31%;
            }
        }
        @media screen and (min-width: 48em) {
            .confirm--content .product--table .column--total-price {
                width: 33%;
            }
        }


    </style>

    <div id="amazonContentWrapper" class="content confirm--content content-main--inner" style="margin-top:2%;margin-bottom: 0px; padding-bottom: 1%;">
        <!-- Place this code in your HTML where you would like the address widget to appear. -->
        <div id="addressBookWidgetDiv"  style="float:left;margin-right:5%;"></div>
        <!-- Place this code in your HTML where you would like the wallet widget to appear. -->
        <div id="walletWidgetDiv" style="float:left;"></div>
    </div>
{/block}


{* SW5.2 *}
{block name='frontend_checkout_confirm_information_wrapper'}
{* Error Messages *}
<div>
    {if $moptPayoneAmazonError}
        {include file="frontend/_includes/messages.tpl" type="error" content="{s name='amazonDeclined ' namespace='frontend/MoptPaymentPayone/errorMessages'}{/s}" bold=false}
    {/if}
</div>
{* Error Messages Javascript*}
<div id="jsErrors" style="display:none">
    <div class="alert is--error is--rounded">
        <div class="alert--icon">
            <i class="icon--element icon--cross"></i>
        </div>
        <div id="jsErrorContent" class="alert--content">
        </div>
    </div>
</div>

<div class="confirm--outer-container">
    <form method="POST" action="{url controller="moptPaymentAmazon" action="index"}" class="payment">

        {* Action top *}
        {block name='frontend_checkout_shipping_payment_core_buttons'}
        {/block}

        {* Payment and shipping information *}
            {if $sDispatches}
                <div class="confirm--inner-container">
                    {block name='frontend_checkout_shipping_payment_core_shipping_fields'}
                        {include file="frontend/mopt_payment_amazon/change_shipping.tpl"}
                    {/block}
                </div>
            {/if}
    </form>
</div>
{/block}

{block name='frontend_checkout_confirm_product_table'}
    <div class="feature--voucher">
        <form method="post" action="{url action='addVoucher' sTargetAction=$sTargetAction}" class="table--add-voucher add-voucher--form">

            {block name='frontend_checkout_confirm_add_voucher_field'}
                <input type="text" class="add-voucher--field block" name="sVoucher" placeholder="{"{s name='CheckoutFooterAddVoucherLabelInline' namespace='frontend/checkout/cart_footer'}{/s}"|escape}" />
            {/block}

            {block name='frontend_checkout_confirm_add_voucher_button'}
                <button type="submit" class="add-voucher--button btn is--primary is--small block">
                    <i class="icon--arrow-right"></i>
                </button>
            {/block}
        </form>
    </div>

    <form id="confirm--form" method="post" action="{url action='finish'}">
        <div class="tos--panel panel has--border">
            <div class="panel--title primary is--underline">
                {s name="ConfirmHeadlineAGBandRevocation"  namespace="frontend/checkout/confirm"}{/s}
            </div>
            <div class="panel--body is--wide">
                {if {config name=revocationnotice}}
                    <div class="body--revocation" data-modalbox="true" data-targetSelector="a" data-mode="ajax" data-height="500" data-width="750">
                        {s name="ConfirmTextRightOfRevocationNew"}<p>Bitte beachten Sie bei Ihrer Bestellung auch unsere <a href="{url controller=custom sCustom=8 forceSecure}" data-modal-height="500" data-modal-width="800">Widerrufsbelehrung</a>.</p>{/s}
                    </div>
                {/if}

                <ul class="list--checkbox list--unstyled">

                    {* Terms of service *}
                    <li class="block-group row--tos">
                        {* Terms of service checkbox *}
                        <span class="block column--checkbox">
                                            {if !{config name='IgnoreAGB'}}
                                                <input type="checkbox" required="required" aria-required="true" id="sAGB" name="sAGB"{if $sAGBChecked} checked="checked"{/if} />
                                            {/if}
                                        </span>
                        {* AGB label *}
                        <span class="block column--label">
                                            <label for="sAGB"{if $sAGBError} class="has--error"{/if} data-modalbox="true" data-targetSelector="a" data-mode="ajax" data-height="500" data-width="750">{s name="ConfirmTerms" namespace="frontend/checkout/confirm"}{/s}</label>
                                        </span>
                    </li>
                </ul>
            </div>
        </div>
        <textarea class="is--hidden user-comment--hidden" rows="1" cols="1" name="sComment">{$sComment|escape}</textarea>
    </form>

    <div class="finish--table product--table">
        <div class="panel has--border">
            <div class="panel--body is--rounded">

                {* Table header *}
                {block name='frontend_checkout_finish_table_header'}
                    {include file="frontend/checkout/finish_header.tpl"}
                {/block}

                {* Article items *}
                {foreach $sBasket.content as $key => $sBasketItem}
                    {block name='frontend_checkout_finish_item'}
                        {include file='frontend/checkout/finish_item.tpl' isLast=$sBasketItem@last}
                    {/block}
                {/foreach}

                {* Table footer *}
                {block name='frontend_checkout_confirm_table_footer'}
                    {include file="frontend/checkout/confirm_footer.tpl"}
                {/block}
            </div>
        </div>
    </div>
    {* Jetzt Bestellen Button *}
    <div class="register--action">
        <button
                id="moptAmazonPayButton" class="btn is--primary is--large right is--icon-right" name="Submit"
                form="confirm--form"
        disabled="disabled">{s name='ConfirmActionSubmit' namespace="frontend/checkout/confirm"}{/s} <i class="icon--arrow-right"></i>
        </button>
    </div>
{/block}

{* remove SW 5.1 billing panel and use it for dispatches *}
{block name='frontend_checkout_confirm_billing_address_panel'}
    <div class="confirm--outer-container">
        <form method="POST" action="{url controller="moptPaymentAmazon" action="index"}" class="payment">

            {* Action top *}
            {block name='frontend_checkout_shipping_payment_core_buttons'}
            {/block}

            {* Payment and shipping information *}
            {if $sDispatches}
                <div class="confirm--inner-container">
                    {block name='frontend_checkout_shipping_payment_core_shipping_fields'}
                        {include file="frontend/mopt_payment_amazon/change_shipping.tpl"}
                    {/block}
                </div>
            {/if}
        </form>
    </div>
{/block}

{* remove SW 5.1 shipping panel *}
{block name='frontend_checkout_confirm_shipping_address_panel'}
{/block}

{* remove SW 5.1 payment method delivery panel *}
{block name='frontend_checkout_confirm_payment_method_panel'}
{/block}


{block name="frontend_index_header_javascript_jquery"}
    {$smarty.block.parent}
    {include file="frontend/mopt_payment_amazon/script-amazonpay.tpl"}
    {if $moptAmazonLogout === true}
    <script>
        window.onAmazonLoginReady = function () {
            amazon.Login.logout();
            console.log("Amazon Logout");
        };
    </script>
    {/if}
{/block}

{* Additional feature which can be enabled / disabled in the base configuration *}
{if {config name=commentvoucherarticle}}
    {block name="frontend_checkout_confirm_additional_features"}
        <div class="panel has--border additional--features">
            {block name="frontend_checkout_confirm_additional_features_headline"}
                <div class="panel--title is--underline">
                    {s name="ConfirmHeadlineAdditionalOptions" namespace="frontend/checkout/confirm"}{/s}
                </div>
            {/block}

            {block name="frontend_checkout_confirm_additional_features_content"}
                <div class="panel--body is--wide block-group" >

                    {* Additional customer comment for the order *}
                    {block name='frontend_checkout_confirm_comment'}
                        {* Hidden field for the user comment *}
                        <div class="feature--user-comment block" style="margin: 0px">
                            <textarea class="user-comment--field" data-storage-field="true" data-storageKeyName="sComment" rows="5" cols="20" placeholder="{s name="ConfirmPlaceholderComment" namespace="frontend/checkout/confirm"}{/s}" data-pseudo-text="true" data-selector=".user-comment--hidden">{$sComment|escape}</textarea>
                        </div>
                    {/block}
                </div>
            {/block}
        </div>
    {/block}
{/if}
