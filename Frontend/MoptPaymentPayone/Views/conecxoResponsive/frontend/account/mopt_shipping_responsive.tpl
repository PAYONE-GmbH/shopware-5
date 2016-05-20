{extends file="frontend/account/shipping.tpl"}

{block name="frontend_index_content" append}
{if $moptShippingAddressCheckNeedsUserVerification}

<div id="moptShippingAddressCheckNeedsUserVerificationModal" 
     class="modal fade" 
     tabindex="-1" 
     role="dialog"
     aria-labelledby="myModalLabel" 
     aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content"></div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function() 
  {
    $.post('{url controller=moptAjaxPayone action=ajaxVerifyShippingAddress forceSecure}', function (data) 
    {
      $('#moptShippingAddressCheckNeedsUserVerificationModal .modal-content').html(data);
      {literal}
      $('#moptShippingAddressCheckNeedsUserVerificationModal').modal({show:true});
      {/literal}
    });
  });
</script>
{/if}
{/block}