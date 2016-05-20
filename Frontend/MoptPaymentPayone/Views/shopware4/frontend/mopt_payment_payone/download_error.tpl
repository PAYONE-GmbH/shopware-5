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
{$sBreadcrumb = [['name'=>"{s namespace='frontend/MoptPaymentPayone/payment' name=mandateDownloadTitle}Mandat Download{/s}"]]}
{/block}

{* Main content *}
{block name='frontend_index_content'}
<div id="center" class="grid_13">
  <h2>{$errormessage|escape|nl2br}</h2>
  <br />
  <h3>
    {s name='mandateDownloadError' namespace='frontend/MoptPaymentPayone/payment'}Das Mandat kann nicht heruntergeladen werden.{/s}
  </h3>
</div>
{/block}

{block name='frontend_index_actions'}{/block}