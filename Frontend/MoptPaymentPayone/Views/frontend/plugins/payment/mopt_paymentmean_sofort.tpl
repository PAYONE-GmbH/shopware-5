{namespace name='frontend/MoptPaymentPayone/payment'}

<div class="payment--form-group">

    {if $moptPaymentConfigParams.moptIsSwiss}
        <input name="moptPaymentData[mopt_payone__sofort_bankaccount]"
               type="text"
               id="mopt_payone__sofort_bankaccount"
               {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
               placeholder="{s name='bankAccountNumber'}Kontonummer{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
               value="{$form_data.mopt_payone__sofort_bankaccount|escape}" 
               data-moptNumberErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="numberFormField"}Dieses Feld darf nur Zahlen enthalten{/s}" 
               class="payment--field is--required{if $error_flags.mopt_payone__sofort_bankaccount} has--error{/if} moptPayoneNumber" />

        <input name="moptPaymentData[mopt_payone__sofort_bankcode]"
               type="text"
               id="mopt_payone__sofort_bankcode"
               {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
               placeholder="{s name='bankCode'}Bankleitzahl{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
               value="{$form_data.mopt_payone__sofort_bankcode|escape}" 
               data-moptBankCodeErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name='bankcodeFormField'}Die Bankleitzahl muss aus 8 Ziffern bestehen{/s}" 
               class="payment--field is--required{if $error_flags.mopt_payone__sofort_bankcode} has--error{/if} moptPayoneBankcode" />
    {else}
        
        {if $moptCreditCardCheckEnvironment.moptShowSofortIbanBic}
        <input name="moptPaymentData[mopt_payone__sofort_iban]"
               type="text"
               id="mopt_payone__sofort_iban"
               {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
               placeholder="{s name='bankIBAN'}IBAN{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
               value="{$form_data.mopt_payone__sofort_iban|escape}"
               data-moptIbanWrongCharacterMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="moptIbanWrongCharacterMessage"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}"
               data-moptIbanWrongLengthMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="moptIbanWrongLengthMessage"}Bitte prüfen Sie die Länge der IBAN{/s}"
               data-moptIbanWrongCecksumMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="moptIbanWrongCecksumMessage"}Die Prüfsumme der IBAN ist falsch{/s}"
               class="payment--field is--required{if $error_flags.mopt_payone__sofort_iban} has--error{/if} moptPayoneIbanBic" />

        <input name="moptPaymentData[mopt_payone__sofort_bic]"
               type="text"
               id="mopt_payone__sofort_bic"
               {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
               placeholder="{s name='bankBIC'}bankBIC{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
               value="{$form_data.mopt_payone__sofort_bic|escape}" 
               data-moptIbanErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}"
               class="payment--field is--required{if $error_flags.mopt_payone__sofort_bic} has--error{/if} moptPayoneIbanBic" />
        {/if}
        <input class="is--hidden" type="text" name="moptPaymentData[mopt_payone__sofort_show_sofort_iban_bic]" id="moptPaymentData[mopt_payone__sofort_show_sofort_iban_bic]" value="{$moptPaymentConfigParams.moptShowSofortIbanBic}">              
    {/if}
    <input type="hidden" name="moptPaymentData[mopt_payone__sofort_bankcountry]" 
           id="mopt_payone__sofort_bankcountry" value="{$sUserData.additional.country.countryiso}"/>
</div>
