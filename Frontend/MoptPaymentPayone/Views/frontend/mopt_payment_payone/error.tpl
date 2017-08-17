{extends file='frontend/index/index.tpl'}

{block name="frontend_index_header_javascript" append}
<script type="text/javascript">
  //<![CDATA[
  if(top!=self){
    top.location=self.location;
  }
  //]]>
</script>
{/block}

{* Breadcrumb *}
{block name='frontend_index_start' append}
{$sBreadcrumb = [['name'=>"{s namespace='frontend/MoptPaymentPayone/payment' name=paymentTitle}Zahlung durchführen{/s}"]]}
{/block}

{* Hide sidebar left *}
{block name='frontend_index_content_left'}{/block}

{* Main content *}
{block name="frontend_index_content"}
<div id="center" class="grid_13">

  <h2>{$errormessage|escape|nl2br}</h2>
  <br />
  <h3>{s namespace='frontend/MoptPaymentPayone/payment' name=PaymentErrorInfo}Bitte kontaktieren Sie den Shopbetreiber.{/s}</h3>
  <br />
  <h3>{s namespace='frontend/MoptPaymentPayone/payment' name=PaymentFailInfo}Bitte versuchen Sie es mit einer anderen Zahlungsart nochmal.{/s}</h3>

  <br />

  <div class="actions">
    <a class="btn" href="{url controller=checkout action=cart forceSecure}" title="{s namespace='frontend/MoptPaymentPayone/payment' name=PaymentLinkChangeBasket}Warenkorb ändern{/s}">
      {s namespace='frontend/MoptPaymentPayone/payment' name=PaymentLinkChangeBasket}{/s}
    </a>
    <a class="btn" href="{url controller=account action=payment sTarget=checkout sChange=1 forceSecure}" title="{s namespace='frontend/MoptPaymentPayone/payment' name=PaymentLinkChange}Zahlungsart ändern{/s}">
      {s namespace='frontend/MoptPaymentPayone/payment' name=PaymentLinkChange}{/s}
    </a>
  </div>

</div>
{/block}

{block name='frontend_index_actions'}{/block}

{block name="frontend_index_header_javascript_jquery" append}
  <script async="async"
          src='https://static-eu.payments-amazon.com/OffAmazonPayments/de/sandbox/lpa/js/Widgets.js'>
  </script>
  <script>
      window.onAmazonLoginReady = function () {
          // console.log("Amazon Logout");
          amazon.Login.logout();
      };
  </script>
{/block}