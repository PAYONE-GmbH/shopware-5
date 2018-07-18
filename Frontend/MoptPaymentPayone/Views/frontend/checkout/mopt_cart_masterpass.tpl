{extends file="parent:frontend/checkout/cart.tpl"}

{block name="frontend_checkout_actions_confirm"}
    {$smarty.block.parent}
    <div class="button--container " style="display:table-cell; float:right; margin-right: 10px;">
        <img src="https://masterpass.com/dyn/img/btn/global/mp_chk_btn_147x034px.svg" onclick="getMasterPassData()">
        <a href="https://www.mastercard.com/mc_us/wallet/learnmore/{$Locale|replace:"_":"/"}/" target="_blank"><u>Learn more</u></a>
        <div class="clear"></div>
    </div>
    <script>
        function getMasterPassData(){

            {if $BSPayoneMode == '1'}
            $.getScript('https://www.masterpass.com/lightbox/Switch/integration/MasterPass.client.js');
            {else}
            $.getScript('https://sandbox.masterpass.com/lightbox/Switch/integration/MasterPass.client.js');
            {/if}

            $.post('{url controller="moptAjaxPayone" action="buildAndCallSetCheckout" forceSecure}', function (response)
            {
                var data = $.parseJSON(response);

                MasterPass.client.checkout({
                    "requestToken": data.token,
                    "merchantCheckoutId": data.merchantcheckoutid,
                    "callbackUrl": data.callbackurl,
                    "allowedCardTypes": data.allowedcardtypes,
                    "version": data.version
                });
            });
        }
    </script>
{/block}

{block name="frontend_checkout_actions_confirm_bottom_checkout"}
    {$smarty.block.parent}
    <div class="button--container " style="display:table-cell; float:right; margin-right: 10px;">
        <img src="https://masterpass.com/dyn/img/btn/global/mp_chk_btn_147x034px.svg" onclick="getMasterPassData()">
        <a href="https://www.mastercard.com/mc_us/wallet/learnmore/{$Locale|replace:"_":"/"}/" target="_blank"><u>Learn more</u></a>
        <div class="clear"></div>
    </div>
    <script>
        function getMasterPassData(){

            {if $BSPayoneMode == '1'}
            $.getScript('https://www.masterpass.com/lightbox/Switch/integration/MasterPass.client.js');
            {else}
            $.getScript('https://sandbox.masterpass.com/lightbox/Switch/integration/MasterPass.client.js');
            {/if}

            $.post('{url controller="moptAjaxPayone" action="buildAndCallSetCheckout" forceSecure}', function (response)
            {
                var data = $.parseJSON(response);

                MasterPass.client.checkout({
                    "requestToken": data.token,
                    "merchantCheckoutId": data.merchantcheckoutid,
                    "callbackUrl": data.callbackurl,
                    "allowedCardTypes": data.allowedcardtypes,
                    "version": data.version
                });
            });
        }
    </script>
{/block}