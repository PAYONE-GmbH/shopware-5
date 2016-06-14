{namespace name='frontend/MoptPaymentPayone/payment'}

<div class="payment--form-group">
    
        {if ($fcPayolutionConfig.payolutionB2bmode == "0" && $sUserData.billingaddress.birthday == "0000-00-00") || ( $fcPayolutionConfig.payolutionB2bmode == 1 && $sUserData.billingaddress.birthday == "0000-00-00" && !$sUserData.billingaddress.company  ) }
        
        <p class ="none">
            <label for="mopt_payone__payolution_invoice_birthday">
                {s name='birthdate'}Geburtsdatum{/s}
            </label>
        </p>

        <select name="moptPaymentData[mopt_payone__payolution_invoice_birthday]" 
                id="mopt_payone__payolution_invoice_birthday" 
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="is--required{if $error_flags.mopt_payone__payolution_invoice_birthday} has--error{/if}">
            <option disabled="disabled" value="">--</option>
            {section name="birthdate" start=1 loop=32 step=1}
                <option value="{$smarty.section.birthdate.index}" 
                        {if $smarty.section.birthdate.index eq $moptCreditCardCheckEnvironment.mopt_payone__payolution_invoice_birthday}
                            selected
                        {/if}>
                    {$smarty.section.birthdate.index}</option>
                {/section}
        </select>

        <select name="moptPaymentData[mopt_payone__payolution_invoice_birthmonth]" 
                id="mopt_payone__payolution_invoice_birthmonth" 
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="is--required{if $error_flags.mopt_payone__payolution_invoice_birthmonth} has--error{/if}">
            <option disabled="disabled" value="">-</option>
            {section name="birthmonth" start=1 loop=13 step=1}
                <option value="{$smarty.section.birthmonth.index}" 
                        {if $smarty.section.birthmonth.index eq $moptCreditCardCheckEnvironment.mopt_payone__payolution_invoice_birthmonth}
                            selected
                        {/if}>
                    {$smarty.section.birthmonth.index}</option>
                {/section}
        </select>

        <select name="moptPaymentData[mopt_payone__payolution_invoice_birthyear]" 
                id="mopt_payone__payolution_invoice_birthyear" 
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="select--country is--required{if $error_flags.mopt_payone__payolution_invoice_birthyear} has--error{/if}">
            <option disabled="disabled" value="">----</option>
            {section name="birthyear" loop=2000 max=100 step=-1}
                <option value="{$smarty.section.birthyear.index}" 
                        {if $smarty.section.birthyear.index eq $moptCreditCardCheckEnvironment.mopt_payone__payolution_invoice_birthyear}
                            selected
                        {/if}>
                    {$smarty.section.birthyear.index}</option>
                {/section}
        </select>
    {/if}  
    
        <input class="is--hidden" type="text" name="moptPaymentData[mopt_payone__payolution_birthdaydate]" id="moptPaymentData[mopt_payone__payolution_birthdaydate]" value="{$sUserData.billingaddress.birthday}">   
        
    {if $fcPayolutionConfig.payolutionB2bmode && $sUserData.billingaddress.company}
        <input type="text" name="moptPaymentData[mopt_payone__invoice_company_trade_registry_number]" 
                id="mopt_payone__invoice_company_trade_registry_number" 
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                placeholder="{s name='companyTradeRegistryNumber'}Handelsregisternummer*{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"                
                class="is--required{if $error_flags.mopt_payone__invoice_company_trade_registry_number} has--error{/if}">
        
        <input class="is--hidden" type="text" name="moptPaymentData[mopt_payone__payolution_b2bmode]" id="moptPaymentData[mopt_payone__payolution_b2bmode]" value="1">   
    {/if}         

        <p class="none clearfix">
            <input name="moptPaymentData[mopt_payone__payolution_invoice_agreement]" type="checkbox" id="mopt_payone__payolution_invoice_agreement" value="true"
                   {if $form_data.mopt_payone__payolution_invoice_agreement eq "on"}
                       checked="checked"
                   {/if}
                   class="checkbox{if $error_flags.mopt_payone__payolution_invoice_agreement} has--error{/if}"/>
            <label for="mopt_payone__payolution_invoice_agreement"  style="float:none; width:100%; display:inline">{$moptCreditCardCheckEnvironment.moptPayolutionInformation.consent}</label>
        </p>
        <div class="register--required-info">{$moptCreditCardCheckEnvironment.moptPayolutionInformation.legalTerm}</div>

</div>
