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

{* Main content *}
{block name='frontend_index_content'}
  <div class="alert alert-info">{$errormessage|escape|nl2br}</div>
  <br />
  <h3>{s namespace='frontend/MoptPaymentPayone/payment' name=PaymentErrorInfo}Bitte kontaktieren Sie den Shopbetreiber.{/s}</h3>
  <br />
  <h3>{s namespace='frontend/MoptPaymentPayone/payment' name=PaymentFailInfo}Bitte versuchen Sie es mit einer anderen Zahlungsart nochmal.{/s}</h3>
  <br />
  <a class="btn btn-primary" href="{url controller=checkout action=cart forceSecure}" title="{s namespace='frontend/MoptPaymentPayone/payment' name=PaymentLinkChangeBasket}Warenkorb ändern{/s}">
      {s name=PaymentLinkChangeBasket}{/s}
  </a>
  <a class="btn btn-primary" href="{url controller=account action=payment sTarget=checkout sChange=1 forceSecure}" title="{s namespace='frontend/MoptPaymentPayone/payment' name=PaymentLinkChange}Zahlungsart ändern{/s}">
      {s name=PaymentLinkChange}{/s}
  </a>
{/block}

{block name='frontend_index_actions'}{/block}