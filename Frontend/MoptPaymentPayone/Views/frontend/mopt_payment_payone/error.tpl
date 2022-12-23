{extends file='parent:frontend/index/index.tpl'}

{block name="frontend_index_header_javascript"}
    {$smarty.block.parent}
    <script type="text/javascript">
        //<![CDATA[
        if(top!=self){
            top.location=self.location;
        }
        //]]>
    </script>
{/block}

{* Breadcrumb *}
{block name='frontend_index_start'}
    {$smarty.block.parent}
    {$sBreadcrumb = [['name'=>"{s namespace='frontend/MoptPaymentPayone/payment' name=paymentTitle}Zahlung durchführen{/s}"]]}
{/block}

{* Hide sidebar left *}
{block name='frontend_index_content_left'}{/block}

{* Main content *}
{block name="frontend_index_content"}
<div id="center" class="grid_13">
    <h2>{$errormessage|escape|nl2br}</h2>
    <br />
    <h3>{$contactShopOwner|escape|nl2br}</h3>
    <br />
    <h3>{$otherPaymentMethod|escape|nl2br}</h3>
    <br />
  <div class="actions">
    <a class="btn" href="{url controller=checkout action=cart forceSecure}" title="{s namespace='frontend/MoptPaymentPayone/payment' name=PaymentLinkChangeBasket}Warenkorb ändern{/s}">
      {s namespace='frontend/MoptPaymentPayone/payment' name=PaymentLinkChangeBasket}{/s}
    </a>
    <a class="btn" href="{url controller=checkout action=shippingPayment sTarget=checkout forceSecure}" title="{s namespace='frontend/MoptPaymentPayone/payment' name=PaymentLinkChange}Zahlungsart ändern{/s}">
      {s namespace='frontend/MoptPaymentPayone/payment' name=PaymentLinkChange}{/s}
    </a>
  </div>
</div>
{/block}

{block name='frontend_index_actions'}{/block}

{block name="frontend_index_header_javascript_jquery" append}
    <script async="async"
        {if $payoneAmazonPayMode == 1} src='https://static-eu.payments-amazon.com/OffAmazonPayments/eur/lpa/js/Widgets.js'> {/if}
        {if $payoneAmazonPayMode == 0} src='https://static-eu.payments-amazon.com/OffAmazonPayments/eur/sandbox/lpa/js/Widgets.js'>{/if}
    </script>
  <script>
      window.onAmazonLoginReady = function () {
          // console.log("Amazon Logout");
          amazon.Login.logout();
      };
  </script>
{/block}