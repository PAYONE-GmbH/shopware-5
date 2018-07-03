{extends file="parent:frontend/checkout/shipping_payment.tpl"}

{* Main content *}
{block name="frontend_index_content"}
    <div class="content content--confirm product--table" data-ajax-shipping-payment="true">
        {include file="frontend/fatchipBSPayoneMasterpassCheckout/fatchip_shipping_payment_core.tpl"}
    </div>
{/block}

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
                        <li class="steps--entry step--confirm{if $sStepActive=='finished'} is--active{/if}">
                            <span class="icon">3</span>
                            <span class="text"><span class="text--inner">Prüfen und Bestellen</span></span>
                        </li>
                    {/block}
                </ul>
            {/block}
        </div>
    </div>
{/block}