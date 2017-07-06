{namespace name='frontend/MoptPaymentPayone/payment'}
<div class="emotion--container emotion--column-12">     
    <div div class="emotion--container col-xs-4 block">
        <strong>{s name='NumberOfInstallments'}Wählen Sie die Anzahl der Raten{/s}</strong><br><br>        
       	{foreach item=Installment key=Key from=$InstallmentPlan}
            <a href="#" onclick="switchInstallmentPlan({$Key}, {$Installment.duration});
                    return false;">
                {$Installment.totalamount|number_format:2:",":"."} {$Installment.currency} - {$Installment.duration} {s name='Installments'}Installments{/s}
            </a><br><br>               
        {/foreach}
    </div> 
    <div div class="emotion--container col-xs-4 block">
        {foreach item=Installment key=Key from=$InstallmentPlan}
            <div id="payolution_installment_overview_{$Key}" class="payolution_installment_overview" style="display:none;">    
                <strong>{s name='Overview'}Overview{/s}</strong><br>
                <table>
                    <tr>
                        <td>{s name='NoOfInstallments'}No. of installments{/s}: </td>
                        <td>{$Installment['duration']}</td>
                    </tr>
                    <tr>
                        <td>{s name='Financingamount'}Financingamount{/s}:</td>
                        <td>{$Installment.originalamount|number_format:2:",":"."} {$Installment.currency}</td>
                    </tr>
                    <tr>
                        <td>{s name='Total'}Total{/s}:</td>
                        <td>{$Installment.totalamount|number_format:2:",":"."} {$Installment.currency}</td>
                    </tr>
                    <tr>
                        <td>{s name='InterestRate'}Interest rate{/s}:</td>
                        <td>{$Installment.interestrate|number_format:2:",":"."}%</td>
                    </tr>
                    <tr>
                        <td>{s name='EffectiveInterestRate'}Effective interest rate{/s}:</td>
                        <td>{$Installment.effectiveinterestrate|number_format:2:",":"."}%</td>
                    </tr>
                    <tr class="final">
                        <td>{s name='MonthlyInstallment'}Monthly installment{/s}</td>
                        <td>{$Installment.installment[1].amount|number_format:2:",":"."} {$Installment.currency}</td>
                    </tr>
                </table>
            </div>                
        {/foreach}  
    </div> 
    <div class="emotion--container col-xs-4 block">
        {foreach item=Installment key=Key from=$InstallmentPlan}
            <div id="payolution_installmentplan_{$Key}" class="payolution_installmentplans" style="float:right;display:none;">                
                <strong>{s name='PaymentPlan'}Payment Plan{/s}</strong><br><br> 
                {foreach item=Payment key=singleKey from=$Installment['installment']}             
                    <span>{$singleKey}. {s name='Installments'}Installments{/s}: {$Payment.amount|number_format:2:",":"."} {$Installment.currency} ({s name='Due'}due{/s} {$Payment.due|date_format:"%d.%m.%Y"})</span><br><br>
                {/foreach} 
                <a href="{url controller="moptAjaxPayone" action="getPayolutionDraftUrl" forceSecure}?url={$Installment['standardcreditinformationurl']}" target="blank">{s name='DownloadInstallment-Contract-Draft'}Ratenzahlungs-Vertrag herunterladen{/s}</a>            
            </div>    
        {/foreach}      
    </div>
</div>   
  
