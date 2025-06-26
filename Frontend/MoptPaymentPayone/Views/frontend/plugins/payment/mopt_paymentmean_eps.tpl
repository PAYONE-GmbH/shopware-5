{namespace name='frontend/MoptPaymentPayone/payment'}

<div class="payment--form-group">
    <div class="select-field">
        <select name="moptPaymentData[mopt_payone__eps_bankgrouptype]" 
                id="mopt_payone__eps_bankgrouptype"
                aria-label="{s name='bankGroup'}Bankgruppe{/s}"
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="select--country is--required{if $error_flags.mopt_payone__eps_bankgrouptype} has--error{/if}">
            <option disabled="disabled" value="" selected="selected">{s name='bankGroup'}Bankgruppe{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}</option>
            <option value="ARZ_OAB" {if $form_data.mopt_payone__eps_bankgrouptype == 'ARZ_OAB'}selected="selected"{/if}>Apothekerbank</option>
            <option value="ARZ_BAF" {if $form_data.mopt_payone__eps_bankgrouptype == 'ARZ_BAF'}selected="selected"{/if}>Ärztebank</option>
            <option value="BA_AUS" {if $form_data.mopt_payone__eps_bankgrouptype == 'BA_AUS'}selected="selected"{/if}>Bank Austria</option>
            <option value="ARZ_BCS" {if $form_data.mopt_payone__eps_bankgrouptype == 'ARZ_BCS'}selected="selected"{/if}>Bankhaus Carl Spängler & Co. AG</option>
            <option value="EPS_SCHEL" {if $form_data.mopt_payone__eps_bankgrouptype == 'EPS_SCHEL'}selected="selected"{/if}>Bankhaus Schelhammer & Schattera AG</option>
            <option value="BAWAG_PSK" {if $form_data.mopt_payone__eps_bankgrouptype == 'BAWAG_PSK'}selected="selected"{/if}>BAWAG P.S.K. AG</option>
            <option value="BAWAG_ESY" {if $form_data.mopt_payone__eps_bankgrouptype == 'BAWAG_ESY'}selected="selected"{/if}>Easybank AG</option>
            <option value="SPARDAT_EBS" {if $form_data.mopt_payone__eps_bankgrouptype == 'SPARDAT_EBS'}selected="selected"{/if}>Erste Bank und Sparkassen</option>
            <option value="ARZ_HAA" {if $form_data.mopt_payone__eps_bankgrouptype == 'ARZ_HAA'}selected="selected"{/if}>Hypo Alpe-Adria-Bank International AG</option>
            <option value="ARZ_VLH" {if $form_data.mopt_payone__eps_bankgrouptype == 'ARZ_VLH'}selected="selected"{/if}>Hypo Landesbank Vorarlberg</option>
            <option value="HRAC_OOS" {if $form_data.mopt_payone__eps_bankgrouptype == 'HRAC_OOS'}selected="selected"{/if}>HYPO Oberösterreich,Salzburg,Steiermark</option>
            <option value="ARZ_HTB" {if $form_data.mopt_payone__eps_bankgrouptype == 'ARZ_HTB'}selected="selected"{/if}>Hypo Tirol Bank AG</option>
            <option value="ARZ_IMB" {if $form_data.mopt_payone__eps_bankgrouptype == 'ARZ_IMB'}selected="selected"{/if}>Immo-bank</option>
            <option value="EPS_OBAG" {if $form_data.mopt_payone__eps_bankgrouptype == 'EPS_OBAG'}selected="selected"{/if}>Oberbank AG</option>
            <option value="RAC_RAC" {if $form_data.mopt_payone__eps_bankgrouptype == 'RAC_RAC'}selected="selected"{/if}>Raiffeisen Bankengruppe Österreich</option>
            <option value="EPS_SCHOELLER" {if $form_data.mopt_payone__eps_bankgrouptype == 'EPS_SCHOELLER'}selected="selected"{/if}>Schoellerbank AG</option>
            <option value="ARZ_OVB" {if $form_data.mopt_payone__eps_bankgrouptype == 'ARZ_OVB'}selected="selected"{/if}>Volksbank Gruppe</option>
            <option value="EPS_VRBB" {if $form_data.mopt_payone__eps_bankgrouptype == 'EPS_VRBB'}selected="selected"{/if}>VR-Bank Braunau</option>
            <option value="EPS_AAB" {if $form_data.mopt_payone__eps_bankgrouptype == 'EPS_AAB'}selected="selected"{/if}>Austrian Anadi Bank AG</option>
            <option value="EPS_BKS" {if $form_data.mopt_payone__eps_bankgrouptype == 'EPS_BKS'}selected="selected"{/if}>BKS Bank AG</option>
            <option value="EPS_BKB" {if $form_data.mopt_payone__eps_bankgrouptype == 'EPS_BKB'}selected="selected"{/if}>Brüll Kallmus Bank AG</option>
            <option value="EPS_VLB" {if $form_data.mopt_payone__eps_bankgrouptype == 'EPS_VLB'}selected="selected"{/if}>BTV VIER LÄNDER BANK</option>
            <option value="EPS_CBGG" {if $form_data.mopt_payone__eps_bankgrouptype == 'EPS_CBGG'}selected="selected"{/if}>Capital Bank Grawe Gruppe AG</option>
            <option value="EPS_DB" {if $form_data.mopt_payone__eps_bankgrouptype == 'EPS_DB'}selected="selected"{/if}>Dolomitenbank</option>
            <option value="EPS_NOEGB" {if $form_data.mopt_payone__eps_bankgrouptype == 'EPS_NOEGB'}selected="selected"{/if}>HYPO NOE Gruppe Bank AG</option>
            <option value="EPS_NOELB" {if $form_data.mopt_payone__eps_bankgrouptype == 'EPS_NOELB'}selected="selected"{/if}>HYPO NOE Landesbank AG</option>
            <option value="EPS_HBL" {if $form_data.mopt_payone__eps_bankgrouptype == 'EPS_HBL'}selected="selected"{/if}>HYPO-BANK BURGENLAND Aktiengesellschaft</option>
            <option value="EPS_MFB" {if $form_data.mopt_payone__eps_bankgrouptype == 'EPS_MFB'}selected="selected"{/if}>Marchfelder Bank</option>
            <option value="EPS_SPDBW" {if $form_data.mopt_payone__eps_bankgrouptype == 'EPS_SPDBW'}selected="selected"{/if}>Sparda Bank Wien</option>
            <option value="EPS_SPDBA" {if $form_data.mopt_payone__eps_bankgrouptype == 'EPS_SPDBA'}selected="selected"{/if}>SPARDA-BANK AUSTRIA</option>
            <option value="EPS_VKB" {if $form_data.mopt_payone__eps_bankgrouptype == 'EPS_VKB'}selected="selected"{/if}>Volkskreditbank AG</option>
        </select>
    </div>
</div>
