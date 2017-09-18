{extends file="parent:frontend/checkout/confirm.tpl"}

{block name="frontend_checkout_confirm_confirm_table_actions"}
    {if $moptMandateData.mopt_payone__showMandateText}
        <div>
            <div style="overflow:scroll; border:1px solid #ccc; padding:10px; height:200px;">
                {$moptMandateData.mopt_payone__mandateText}
            </div>
            <div class="clear">&nbsp;</div>
            <div> 
                <label for="mandate_status"  style="float:left; padding-right:10px;">
                    {s name='mandateIAgree' namespace='frontend/MoptPaymentPayone/payment'}Ich möchte das Mandat erteilen{/s}
                    <br />
                    {s name='mandateElectronicSubmission' namespace='frontend/MoptPaymentPayone/payment'}(elektronische Übermittlung){/s}
                </label>
                <input type="checkbox" id="mandate_status" name="mandate_status"/>
            </div>
        </div>
        <div class="clear">&nbsp;</div>
    {/if}
    {$smarty.block.parent}
{/block}

{block name="frontend_checkout_confirm_agb_checkbox"}
    {$smarty.block.parent}
    {if $moptMandateData.mopt_payone__showMandateText}
        <input name="moptMandateConfirm" type="hidden" 
               id="moptMandateConfirm"/>
    {/if}
    <input name="moptAgbChecked" type="hidden" 
           data-mopt_payone__agb_checked="{$moptAgbChecked}" 
           id="moptAgbChecked"/>
    <input name="moptAddressCheckNeedsUserVerification" type="hidden" 
           data-moptAddressCheckNeedsUserVerification="{$moptAddressCheckNeedsUserVerification}" 
           data-moptAddressCheckVerificationUrl="{url controller=moptAjaxPayone action=ajaxVerifyAddress forceSecure}" 
           id="moptAddressCheckNeedsUserVerification"/>
{/block}

{block name="frontend_checkout_confirm_error_messages"}
    {if $moptMandateAgreementError}
        {include file="frontend/_includes/messages.tpl" type="error" content="{s name='mandateAgreementError' namespace='frontend/MoptPaymentPayone/payment'}Bitte bestätigen Sie die Erteilung des Mandats.{/s}"}
    {/if}
    {$smarty.block.parent}
{/block}
