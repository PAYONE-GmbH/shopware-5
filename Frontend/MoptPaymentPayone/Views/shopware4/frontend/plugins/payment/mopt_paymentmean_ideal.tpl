<div class="debit">
  <p class="none">
    <label for="mopt_payone__ideal_bankgrouptype">
      {s namespace='frontend/MoptPaymentPayone/payment' name='bankGroup'}Bankgruppe{/s}
    </label>
    <select name="moptPaymentData[mopt_payone__ideal_bankgrouptype]" id="mopt_payone__ideal_bankgrouptype" size="1" 
            style="width:auto" class="{if $error_flags.mopt_payone__ideal_bankgrouptype}instyle_error{/if}">
      <option value="not_choosen">
        {s namespace='frontend/MoptPaymentPayone/payment' name='selectValueLabel'}Bitte auswählen...{/s}
      </option>
      <option value="ABN_AMRO_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'ABN_AMRO_BANK'}selected="selected"{/if}>ABN Amro</option>
      <option value="BUNQ_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'BUNQ_BANK'}selected="selected"{/if}>Bunq</option>
      <option value="ING_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'ING_BANK'}selected="selected"{/if}>ING Bank</option>
      <option value="RABOBANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'RABOBANK'}selected="selected"{/if}>Rabobank</option>
      <option value="SNS_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'SNS_BANK'}selected="selected"{/if}>SNS BANK</option>
      <option value="ASN_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'ASN_BANK'}selected="selected"{/if}>ASN Bank</option>
      <option value="SNS_REGIO_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'SNS_REGIO_BANK'}selected="selected"{/if}>SNS Regio Bank</option>
      <option value="TRIODOS_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'TRIODOS_BANK'}selected="selected"{/if}>Triodos Bank</option>
      <option value="KNAB_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'KNAB_BANK'}selected="selected"{/if}>Knab</option>
      <option value="VAN_LANSCHOT_BANKIERS" {if $form_data.mopt_payone__ideal_bankgrouptype == 'VAN_LANSCHOT_BANKIERS'}selected="selected"{/if}>van Lanschot</option>
    </select>
  </p>
  <input type="hidden" name="moptPaymentData[mopt_payone__onlinebanktransfertype]" 
         id="mopt_payone__onlinebanktransfertype" value="IDL"/>
  <input type="hidden" name="moptPaymentData[mopt_payone__ideal_bankcountry]" 
         id="mopt_payone__ideal_bankcountry" value="NL"/>
</div>

<script type="text/javascript">
  $('#mopt_payone__ideal_bankgrouptype').focus(function() {
    $('#payment_mean{$payment_mean.id}').attr('checked',true);
    $('#moptSavePayment{$payment_mean.id}').slideDown();
    $('input[type="radio"]:not(:checked)').trigger('change');
  });
</script>