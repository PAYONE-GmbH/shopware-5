{namespace name='frontend/MoptPaymentPayone/payment'}

{if $payment_mean.id == $form_data.payment}
    {assign var="moptRequired" value=1}
{else}
    {assign var="moptRequired" value=0}
{/if}
{if $moptRequired && $moptPaymentConfigParams.moptShowAccountnumber}
    {assign var="moptRequired" value=0}
{/if}
{if $moptPaymentConfigParams.moptShowAccountnumber}
    {assign var="moptRequired" value=0}
{/if}

<div class="payment--form-group">
    <p class="none">
        <select name="moptPaymentData[mopt_payone__debit_bankcountry]" 
                id="mopt_payone__debit_bankcountry" 
                {if $moptRequired}required="required" aria-required="true"{/if}
                class="select--country is--required{if $error_flags.mopt_payone__debit_bankcountry} has--error{/if}">
            <option disabled="disabled" value="" selected="selected">{s name='bankCountry'}Land{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}</option>
            {foreach from=$moptPaymentConfigParams.moptDebitCountries item=moptCountry}
                <option value="{$moptCountry.countryiso}" 
                        {if $form_data.mopt_payone__debit_bankcountry == $moptCountry.countryiso}selected="selected"{/if}>
                    {$moptCountry.countryname}
                </option>
            {/foreach}
        </select>
    </p>

    <input name="moptPaymentData[mopt_payone__debit_bankaccountholder]"
           type="text"
           id="mopt_payone__debit_bankaccountholder"
           {if $moptRequired}required="required" aria-required="true"{/if}
           placeholder="{s name='bankAccoutHolder'}Kontoinhaber{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
           value="{$form_data.mopt_payone__debit_bankaccountholder|escape}"
           class="payment--field {if $moptRequired}is--required{/if}{if $error_flags.mopt_payone__debit_bankaccountholder} has--error{/if}" />

    <input name="moptPaymentData[mopt_payone__debit_iban]"
           type="text"
           id="mopt_payone__debit_iban"
           {if $moptRequired}required="required" aria-required="true"{/if}
           placeholder="{s name='bankIBAN'}IBAN{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
           value="{$form_data.mopt_payone__debit_iban|escape}" 
           data-moptIbanErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}"
           class="payment--field {if $moptRequired}is--required{/if}{if $error_flags.mopt_payone__debit_iban} has--error{/if} moptPayoneIbanBic" />
    {if $moptPaymentConfigParams.moptShowBic}
    <input name="moptPaymentData[mopt_payone__debit_bic]"
           type="text"
           id="mopt_payone__debit_bic"
           {if $moptRequired}required="required" aria-required="true"{/if}
           placeholder="{s name='bankBIC'}BIC{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
           value="{$form_data.mopt_payone__debit_bic|escape}" 
           data-moptIbanErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}"
           class="payment--field {if $moptRequired}is--required{/if}{if $error_flags.mopt_payone__debit_bic} has--error{/if} moptPayoneIbanBic" />    
    {/if}
    
    <input class="is--hidden" type="text" name="moptPaymentData[mopt_payone__debit_showbic]" id="moptPaymentData[mopt_payone__debit_showbic]" value="{$moptPaymentConfigParams.moptShowBic}">              
    
    {if $moptPaymentConfigParams.moptShowAccountnumber}
        <p class="none">
            {s namespace='frontend/MoptPaymentPayone/payment' name='debitDescription'}
            oder bezahlen Sie wie gewohnt mit Ihren bekannten Kontodaten(nur für Deutsche Kontoverbindungen).{/s}
        </p>
        <input name="moptPaymentData[mopt_payone__debit_bankaccount]"
               type="text"
               id="mopt_payone__debit_bankaccount"
               placeholder="{s name='bankAccountNumber'}Kontonummer{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
               value="{$form_data.mopt_payone__debit_bankaccount|escape}" 
               data-moptNumberErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="numberFormField"}Dieses Feld darf nur Zahlen enthalten{/s}" 
               class="payment--field{if $error_flags.mopt_payone__debit_bankaccount} has--error{/if} moptPayoneNumber" />

        <input name="moptPaymentData[mopt_payone__debit_bankcode]"
               type="text"
               id="mopt_payone__debit_bankcode"
               placeholder="{s name='bankCode'}Bankleitzahl{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
               value="{$form_data.mopt_payone__debit_bankcode|escape}" 
               data-moptBankCodeErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name='bankcodeFormField'}Die Bankleitzahl muss aus 8 Ziffern bestehen{/s}" 
               class="payment--field{if $error_flags.mopt_payone__debit_bankcode} has--error{/if} moptPayoneBankcode" />
    {/if}

    {block name='frontend_checkout_payment_required'}
        {* Required fields hint *}
        <div class="register--required-info">
            {s name='RegisterPersonalRequiredText' namespace='frontend/register/personal_fieldset'}{/s}
        </div>
    {/block}
</div>
