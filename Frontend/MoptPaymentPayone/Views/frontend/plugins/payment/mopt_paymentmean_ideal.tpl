{namespace name='frontend/MoptPaymentPayone/payment'}

<div class="payment--form-group">
    <div class="select-field">
        <select name="moptPaymentData[mopt_payone__ideal_bankgrouptype]" 
                id="mopt_payone__ideal_bankgrouptype" 
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="select--country is--required{if $error_flags.mopt_payone__ideal_bankgrouptype} has--error{/if}">
            <option disabled="disabled" value="" selected="selected">{s name='bankGroup'}Bankgruppe{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}</option>
            <option value="ABN_AMRO_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'ABN_AMRO_BANK'}selected="selected"{/if}>ABN Amro Bank</option>
            <option value="ASN_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'ASN_BANK'}selected="selected"{/if}>ASN Bank</option>
            <option value="BUNQ_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'BUNQ_BANK'}selected="selected"{/if}>Bunq Bank</option>
            <option value="ING_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'ING_BANK'}selected="selected"{/if}>ING Bank</option>
            <option value="KNAB_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'KNAB_BANK'}selected="selected"{/if}>Knab Bank</option>
            <option value="NATIONALE_NEDERLANDEN" {if $form_data.mopt_payone__ideal_bankgrouptype == 'NATIONALE_NEDERLANDEN'}selected="selected"{/if}>Nationale-Nederlanden</option>
            <option value="N26" {if $form_data.mopt_payone__ideal_bankgrouptype == 'N26'}selected="selected"{/if}>N26</option>
            <option value="RABOBANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'RABOBANK'}selected="selected"{/if}>Rabobank</option>
            <option value="REVOLUT" {if $form_data.mopt_payone__ideal_bankgrouptype == 'REVOLUT'}selected="selected"{/if}>Revolut</option>
            <option value="SNS_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'SNS_BANK'}selected="selected"{/if}>SNS Bank</option>
            <option value="SNS_REGIO_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'SNS_REGIO_BANK'}selected="selected"{/if}>SNS Regio Bank</option>
            <option value="TRIODOS_BANK" {if $form_data.mopt_payone__ideal_bankgrouptype == 'TRIODOS_BANK'}selected="selected"{/if}>Triodos Bank</option>
            <option value="VAN_LANSCHOT_BANKIERS" {if $form_data.mopt_payone__ideal_bankgrouptype == 'VAN_LANSCHOT_BANKIERS'}selected="selected"{/if}>van Lanschot Kempen</option>
            <option value="YOURSAFE" {if $form_data.mopt_payone__ideal_bankgrouptype == 'YOURSAFE'}selected="selected"{/if}>Yoursafe</option>
        </select>
    </div>
</div>
