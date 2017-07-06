{extends file="frontend/checkout/confirm.tpl"}

{block name="frontend_index_content" append}
{if $moptShippingAddressCheckNeedsUserVerification}
<script type="text/javascript">
  $(document).ready(function() {
    $.post('{url controller=moptAjaxPayone action=ajaxVerifyShippingAddress forceSecure}', function (data) {
      $.modal(data, '', {
        'position': 'fixed',
        'width': 500,
        'height': 500
      }).find('.close').remove();
    });
  });
</script>
{/if}
{/block}