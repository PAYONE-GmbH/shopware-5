<div class="debit">
    <div class="form-group {if $error_flags.mopt_payone__ideal_bankgrouptype}has-error{/if}">
        <label for="mopt_payone__ideal_bankgrouptype" class="col-lg-4 control-label">
          {s namespace='frontend/MoptPaymentPayone/payment' name='bankGroup'}Bankgruppe{/s}
        </label>

        <div class="col-lg-6">
            <select name="moptPaymentData[mopt_payone__ideal_bankgrouptype]" 
                    id="mopt_payone__ideal_bankgrouptype" size="1" class="form-control">
      <option value="not_choosen">
        {s namespace='frontend/MoptPaymentPayone/payment' name='selectValueLabel'}Bitte auswählen...{/s}
      </option>
                <option value="ABN_AMRO_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'ABN_AMRO_BANK'}selected="selected"{/if}>ABN Amro</option>
                <option value="BUNQ_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'BUNQ_BANK'}selected="selected"{/if}>Bunq</option>
                <option value="RABOBANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'RABOBANK'}selected="selected"{/if}>Rabobank</option>
                <option value="ASN_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'ASN_BANK'}selected="selected"{/if}>ASN Bank</option>
                <option value="SNS_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'SNS_BANK'}selected="selected"{/if}>SNS BANK</option>
                <option value="TRIODOS_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'TRIODOS_BANK'}selected="selected"{/if}>Triodos Bank</option>
                <option value="SNS_REGIO_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'SNS_REGIO_BANK'}selected="selected"{/if}>SNS Regio Bank</option>
                <option value="ING_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'ING_BANK'}selected="selected"{/if}>ING Bank</option>
                <option value="KNAB_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'KNAB_BANK'}selected="selected"{/if}>Knab</option>
                <option value="VAN_LANSCHOT_BANKIERS" {if $form_data.mopt_payone__ideal_bankgrouptype == 'VAN_LANSCHOT_BANKIERS'}selected="selected"{/if}>van Lanschot Bank</option>
                <option value="MONEYOU" {if $form_data.mopt_payone__ideal_bankgrouptype == 'MONEYOU'}selected="selected"{/if}>Moneyou</option>
            </select>
        </div>
    </div>
    <input type="hidden" name="moptPaymentData[mopt_payone__onlinebanktransfertype]" 
           id="mopt_payone__onlinebanktransfertype" value="IDL"/>
    <input type="hidden" name="moptPaymentData[mopt_payone__ideal_bankcountry]" 
           id="mopt_payone__ideal_bankcountry" value="NL"/>
</div>

<script type="text/javascript">
    $('#mopt_payone__ideal_bankgrouptype').focus(function () 
    {
      $('#payment_mean{$payment_mean.id}').attr('checked', true);
      $('#moptSavePayment{$payment_mean.id}').slideDown();
      $('input[type="radio"]:not(:checked)').trigger('change');
    });
</script>