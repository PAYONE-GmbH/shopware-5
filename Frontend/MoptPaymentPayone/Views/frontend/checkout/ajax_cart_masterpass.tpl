{extends file="parent:frontend/checkout/ajax_cart.tpl"}

{block name='frontend_checkout_ajax_cart_button_container'}
    {$smarty.block.parent}
    <div class="button--container">
        <!-- <img src="https://static.masterpass.com/dyn/img/btn/global/mp_chk_btn_147x034px.svg" onclick="getMasterPassData()" > -->
        <img src="https://masterpass.com/dyn/img/btn/global/mp_chk_btn_147x034px.svg" onclick="getMasterPassData()" >
        <div class="clear"></div>
    </div>
    <script>
        function getMasterPassData(){

            $.getScript('https://sandbox.masterpass.com/lightbox/Switch/integration/MasterPass.client.js');
            $.post('{url controller="moptAjaxPayone" action="buildAndCallSetCheckout" forceSecure}', function (response)
            {
                var data = $.parseJSON(response);
                console.log("token:");
                console.log(data.token);
                console.log("checkoutid:");
                console.log(data.merchantcheckoutid);
                console.log("callbackurl:");
                console.log(data.callbackurl);
                console.log("allowedcardtypes:");
                console.log(data.allowedcardtypes);
                console.log("version:");
                console.log(data.version);

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
