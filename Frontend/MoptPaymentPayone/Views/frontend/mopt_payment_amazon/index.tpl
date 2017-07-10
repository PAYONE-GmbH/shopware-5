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

{* disable additional features, used by SW 5.1 *}
{block name='frontend_checkout_confirm_additional_features'}
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
    <script>
        var moptAmazonReferenceId = null;
        var moptAmazonCountryChanged = null;

        window.onAmazonLoginReady = function () {
            amazon.Login.setClientId("{$payoneAmazonPayConfig->getClientId()}");
        };
        window.onAmazonPaymentsReady = function () {
            new OffAmazonPayments.Widgets.AddressBook({
                sellerId: "{$payoneAmazonPayConfig->getSellerId()}",
                scope: 'profile payments:widget payments:shipping_address payments:billing_address',
                onOrderReferenceCreate: function (orderReference) {
                    moptAmazonReferenceId = orderReference.getAmazonOrderReferenceId();
                },
                onAddressSelect: function (orderReference) {

                    $('#moptAmazonPayButton').attr("disabled", "disabled");

                    var call = '{url controller="moptAjaxPayone" action="getOrderReferenceDetails" forceSecure}';
                    $.ajax({
                        url: call ,
                        type: 'post',
                        data: { referenceId: moptAmazonReferenceId}
                    })
                        .success(function(response){

                            responseData = $.parseJSON(response);
                            moptAmazonCountryChanged = responseData.countryChanged;
                            // Reload the site, to update dispatches in case country changed
                            if (moptAmazonCountryChanged) {
                                location.reload(true);
                            } else {
                                $('#moptAmazonPayButton').removeAttr("disabled");
                            }

                        });

                },
                design: {
                    designMode: 'responsive'
                },
                onReady: function (orderReference) {

                },
                onError: function (error) {
                    // Your error handling code.
                    // During development you can use the following
                    // code to view error messages:
                    // console.log(error.getErrorCode() + ': ' + error.getErrorMessage());
                    // See "Handling Errors" for more information.
                    console.log(error.getErrorCode() + ': ' + error.getErrorMessage());
                }
            }).bind("addressBookWidgetDiv");

            new OffAmazonPayments.Widgets.Wallet({
                sellerId: "{$payoneAmazonPayConfig->getSellerId()}",
                scope: 'profile payments:widget payments:shipping_address payments:billing_address',
                onPaymentSelect: function (orderReference) {

                },
                design: {
                    designMode: 'responsive'
                },
                onError: function (error) {
                    console.log(error.getErrorCode() + ': ' + error.getErrorMessage());
                    // See "Handling Errors" for more information.
                }
            }).bind("walletWidgetDiv");
        };
    </script>
    <script async="async"
            src='https://static-eu.payments-amazon.com/OffAmazonPayments/de/sandbox/lpa/js/Widgets.js'>
    </script>
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


