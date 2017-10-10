{extends file="parent:frontend/account/billing.tpl"}

{block name="frontend_index_content"}

{if $moptAddressCheckNeedsUserVerification}
    {$smarty.block.parent}
<div id="moptAddressCheckNeedsUserVerificationModal" 
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
    $.post('{url controller=moptAjaxPayone action=ajaxVerifyAddress forceSecure}', function (data) 
    {
      $('#moptAddressCheckNeedsUserVerificationModal .modal-content').html(data);
      {literal}
      $('#moptAddressCheckNeedsUserVerificationModal').modal({show:true});
      {/literal}
    });
  });
</script>
{/if}
{/block}