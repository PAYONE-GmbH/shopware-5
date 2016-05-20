{extends file="frontend/account/billing.tpl"}

{block name="frontend_index_content" append}
{if $moptAddressCheckNeedsUserVerification}
<script type="text/javascript">
  $(document).ready(function() {
    $.post('{url controller=moptAjaxPayone action=ajaxVerifyAddress forceSecure}', function (data) {
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