{extends file="parent:frontend/checkout/shipping_payment.tpl"}

{block name='frontend_account_payment_error_messages'}
    <div>
        {if $moptAmazonError}
            {include file="frontend/_includes/messages.tpl" type="error" content="{s name='amazonDeclined ' namespace='frontend/MoptPaymentPayone/errorMessages'}{/s}" bold=false}
        {/if}
    </div>
    <div class="payment--form-group">
        {if $moptBasketChanged}
            <div id="mopt_overlay_redirect_notice" class="js--modal content" style="width:40%; height:40%; opacity: 0.9; margin: 75px auto;">
                <a href="#" onclick="removeMoptOverlayRedirectNotice();
                    return false;" style="float:right;font-weight:bold;">Fenster schliessen</a><br><br>
                {$moptOverlayRedirectNotice}
            </div>
            <div id="mopt_overlay_redirect_notice_bg" class="js--overlay is--open" style="opacity: 0.8;"></div>
        {/if}
    </div>

    <script type="text/javascript">
        function removeMoptOverlayRedirectNotice() {
            document.getElementById('mopt_overlay_redirect_notice').style.display = "none";
            document.getElementById('mopt_overlay_redirect_notice_bg').style.display = "none";
        }
    </script>
    {$smarty.block.parent}
{/block}

{block name="frontend_index_header_javascript_jquery"}
    {if $moptAmazonLogout === true}
        <script async="async"
            {if $payoneAmazonPayMode == 1} src='https://static-eu.payments-amazon.com/OffAmazonPayments/eur/lpa/js/Widgets.js'> {/if}
            {if $payoneAmazonPayMode == 0} src='https://static-eu.payments-amazon.com/OffAmazonPayments/eur/sandbox/lpa/js/Widgets.js'>{/if}
        </script>
        <script>
            window.onAmazonLoginReady = function () {
                amazon.Login.logout();
            };
        </script>
    {/if}
    {$smarty.block.parent}
{/block}

{block name="frontend_index_content"}
    <div id="fatchipMoptPaySafeInformation" hidden
         data-get-session-id--URL="{url controller="MoptAjaxPayone" action="ajaxGetPaySafeToken" forceSecure}"
    ></div>
    {$smarty.block.parent}
{/block}
