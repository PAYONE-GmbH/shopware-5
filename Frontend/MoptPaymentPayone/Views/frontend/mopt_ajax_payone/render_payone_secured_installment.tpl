{namespace name='frontend/MoptPaymentPayone/payment'}
<h2 class="ratepay-mid-heading"><b>
        {s name='individualRateCalculation'}Individuelle Ratenberechnung*{/s}</b>
</h2>


<div class="form-group">
    <label class="req control-label col-lg-3">{s name='NumberOfInstallments'}Anzahl der Raten{/s}</label>
    <div class="col-lg-9">
        <div></div>
        [{foreach from=$installmentOptions.plans key=index item=plan}]
        <div>
            <input id="bnplPlan_[{$index}]" type="radio" name="moptPaymentData[mopt_payone__payone_secured_installment]" value="[{$plan.installmentOptionId}]" onclick="fcpoSelectBNPLInstallmentPlan([{$index}])"/>
            <a href="#" onclick="fcpoSelectBNPLInstallmentPlan([{$index}])">
                [{$plan.monthlyAmountValue}] [{$plan.monthlyAmountCurrency}] [{oxmultilang ident='FCPO_PAYOLUTION_INSTALLMENT_PER_MONTH'}] - [{$plan.numberOfPayments}] [{oxmultilang ident='FCPO_PAYOLUTION_INSTALLMENT_RATES'}]
            </a>
        </div>
        [{/foreach}]
    </div>
</div>

            <div class="form-group">
                <div class="col-lg-3"></div>
                <div class="col-lg-9">
                [{foreach from=$installmentOptions.plans key=index item=plan}]
                    <div id="bnpl_installment_overview_[{$index}]" class="bnpl_installment_overview" style="display: none">
                        <strong>[{oxmultilang ident='FCPO_BNPL_SECINSTALLMENT_OVW_TITLE'}]</strong>
                        <br />
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-3">[{oxmultilang ident='FCPO_BNPL_SECINSTALLMENT_OVW_NBRATES'}]:</div>
                                <div class="col-lg-4 fcpopl-secinstallment-table-value">[{$plan.numberOfPayments}]</div>
                            </div>

                            <div class="row">
                                <div class="col-lg-3">[{oxmultilang ident='FCPO_BNPL_SECINSTALLMENT_OVW_TOTALFINANCING'}]:</div>
                                <div class="col-lg-4 fcpopl-secinstallment-table-value">[{$installmentOptions.amountValue}] [{$installmentOptions.amountCurrency}]</div>
                            </div>

                            <div class="row">
                                <div class="col-lg-3">[{oxmultilang ident='FCPO_BNPL_SECINSTALLMENT_OVW_TOTALAMOUNT'}]:</div>
                                <div class="col-lg-4 fcpopl-secinstallment-table-value">[{$plan.totalAmountValue}] [{$plan.totalAmountCurrency}]</div>
                            </div>

                            <div class="row">
                                <div class="col-lg-3">[{oxmultilang ident='FCPO_BNPL_SECINSTALLMENT_OVW_INTEREST'}]:</div>
                                <div class="col-lg-4 fcpopl-secinstallment-table-value">[{$plan.nominalInterestRate}]%</div>
                            </div>

                            <div class="row">
                                <div class="col-lg-3">[{oxmultilang ident='FCPO_BNPL_SECINSTALLMENT_OVW_EFFECTIVEINTEREST'}]:</div>
                                <div class="col-lg-4 fcpopl-secinstallment-table-value">[{$plan.effectiveInterestRate}]%</div>
                            </div>

                            <div class="row">
                                <div class="col-lg-3">[{oxmultilang ident='FCPO_BNPL_SECINSTALLMENT_OVW_MONTHLYRATE'}]:</div>
                                <div class="col-lg-4 fcpopl-secinstallment-table-value">[{$plan.monthlyAmountValue}] [{$plan.monthlyAmountCurrency}]</div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <a target="_blank" href="[{$plan.linkCreditInformationHref}]">[{oxmultilang ident='FCPO_BNPL_SECINSTALLMENT_OVW_DL_CREDINFO'}]</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    [{/foreach}]
                </div>
            </div>