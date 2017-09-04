{namespace name='frontend/MoptPaymentPayone/payment'}

<div class="payment--form-group">
    <div class="select-field">
        <select name="moptPaymentData[mopt_payone__eps_bankgrouptype]" 
                id="mopt_payone__eps_bankgrouptype" 
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="select--country is--required{if $error_flags.mopt_payone__eps_bankgrouptype} has--error{/if}">
            <option disabled="disabled" value="" selected="selected">{s name='bankGroup'}Bankgruppe{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}</option>
            <option value="ARZ_OVB" {if $form_data.mopt_payone__eps_bankgrouptype == 'ARZ_OVB'}selected="selected"{/if}>Volksbanken</option>
            <option value="ARZ_BAF" {if $form_data.mopt_payone__eps_bankgrouptype == 'ARZ_BAF'}selected="selected"{/if}>Bank für Ärzte und Freie Berufe</option>
            <option value="ARZ_NLH" {if $form_data.mopt_payone__eps_bankgrouptype == 'ARZ_NLH'}selected="selected"{/if}>Niederösterreichische Landes-Hypo</option>
            <option value="ARZ_VLH" {if $form_data.mopt_payone__eps_bankgrouptype == 'ARZ_VLH'}selected="selected"{/if}>Vorarlberger Landes-Hypo</option>
            <option value="ARZ_BCS" {if $form_data.mopt_payone__eps_bankgrouptype == 'ARZ_BCS'}selected="selected"{/if}>Bankhaus Carl Spängler & Co. AG</option>
            <option value="ARZ_HTB" {if $form_data.mopt_payone__eps_bankgrouptype == 'ARZ_HTB'}selected="selected"{/if}>Hypo Tyrol</option>
            <option value="ARZ_HAA" {if $form_data.mopt_payone__eps_bankgrouptype == 'ARZ_HAA'}selected="selected"{/if}>Hypo Alpe Adria</option>
            <option value="ARZ_IKB" {if $form_data.mopt_payone__eps_bankgrouptype == 'ARZ_IKB'}selected="selected"{/if}>Investkreditbank</option>
            <option value="ARZ_OAB" {if $form_data.mopt_payone__eps_bankgrouptype == 'ARZ_OAB'}selected="selected"{/if}>Österreichische Apothekerbank</option>
            <option value="ARZ_IMB" {if $form_data.mopt_payone__eps_bankgrouptype == 'ARZ_IMB'}selected="selected"{/if}>Immobank</option>
            <option value="ARZ_GRB" {if $form_data.mopt_payone__eps_bankgrouptype == 'ARZ_GRB'}selected="selected"{/if}>Gärtnerbank</option>
            <option value="ARZ_HIB" {if $form_data.mopt_payone__eps_bankgrouptype == 'ARZ_AUS'}selected="selected"{/if}>HYPO Investment</option>
            <option value="BA_AUS" {if $form_data.mopt_payone__eps_bankgrouptype == 'BA_BANK'}selected="selected"{/if}>Bank Austria</option>
            <option value="BAWAG_BWG" {if $form_data.mopt_payone__eps_bankgrouptype == 'BAWAG_BWG'}selected="selected"{/if}>BAWAG</option>
            <option value="BAWAG_PSK" {if $form_data.mopt_payone__eps_bankgrouptype == 'BAWAG_PSK'}selected="selected"{/if}>PSK Bank</option>
            <option value="BAWAG_ESY" {if $form_data.mopt_payone__eps_bankgrouptype == 'BAWAG_ESY'}selected="selected"{/if}>easybank</option>
            <option value="BAWAG_SPD" {if $form_data.mopt_payone__eps_bankgrouptype == 'BAWAG_SPD'}selected="selected"{/if}>Sparda Bank</option>
            <option value="SPARDAT_EBS" {if $form_data.mopt_payone__eps_bankgrouptype == 'SPARDAT_EBS'}selected="selected"{/if}>Erste Bank</option>
            <option value="SPARDAT_BBL" {if $form_data.mopt_payone__eps_bankgrouptype == 'SPARDAT_BBL'}selected="selected"{/if}>Bank Burgenland</option>
            <option value="RAC_RAC" {if $form_data.mopt_payone__eps_bankgrouptype == 'RAC_RAC'}selected="selected"{/if}>Raiffeisen</option>
            <option value="HRAC_OOS" {if $form_data.mopt_payone__eps_bankgrouptype == 'HRAC_OOS'}selected="selected"{/if}>Hypo Oberösterreich</option>
            <option value="HRAC_SLB" {if $form_data.mopt_payone__eps_bankgrouptype == 'HRAC_SLB'}selected="selected"{/if}>Hypo Salzburg</option>
            <option value="HRAC_STM" {if $form_data.mopt_payone__eps_bankgrouptype == 'HRAC_STM'}selected="selected"{/if}>Hypo Steiermark</option>
        </select>
    </div>
</div>
