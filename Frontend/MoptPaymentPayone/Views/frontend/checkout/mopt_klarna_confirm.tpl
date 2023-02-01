{extends file="parent:frontend/checkout/confirm.tpl"}

{block name='frontend_checkout_confirm_form'}
    <div id="mopt_payone__klarna_information" hidden
         data-client-token="{$mopt_klarna_client_token}"
         data-store-authorization-token--URL="{url controller="MoptAjaxPayone" action="storeAuthorizationToken" forceSecure}"
    ></div>
    <script>
        window.klarnaAsyncCallback = function () {
            window.PayoneKlarna = Klarna;
        };
    </script>
    <script src="https://x.klarnacdn.net/kp/lib/v1/api.js" async></script>
    {$smarty.block.parent}
{/block}
