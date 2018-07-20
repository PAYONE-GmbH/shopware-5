{namespace name='frontend/MoptPaymentPayone/payment'}

<div class="payment--form-group">
    <div class="select-field">
        <select name="moptPaymentData[mopt_payone__ideal_bankgrouptype]" 
                id="mopt_payone__ideal_bankgrouptype" 
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="select--country is--required{if $error_flags.mopt_payone__ideal_bankgrouptype} has--error{/if}">
            <option disabled="disabled" value="" selected="selected">{s name='bankGroup'}Bankgruppe{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}</option>
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
