{if $moptCreditCardCheckEnvironment.moptCreditcardConfig.integration_type === '0'}
    {assign var="moptIsAjax" value=false}
{else}
    {assign var="moptIsAjax" value=true}
{/if}

{namespace name='frontend/MoptPaymentPayone/payment'}

<div class="payment--form-group" 
     id="mopt_payone_creditcard_form" 
     data-mopt_payone__cc_paymentid="{$form_data.mopt_payone__cc_paymentid}" 
     data-mopt_payone__cc_paymentshort="{$form_data.mopt_payone__cc_cardtype}" 
     data-mopt_payone_credit_cards_id="{$payment_mean.mopt_payone_credit_cards[0]['id']}" 
     data-mopt_payone_credit_cards_short="{$moptCreditCardCheckEnvironment.payment_mean.mopt_payone_credit_cards[0]['short']}" 
     data-mopt_payone__cc_Year="{$form_data.mopt_payone__cc_Year}" 
     data-messageCreditCardCvcProcessed="{s name='creditCardCvcProcessed'}Kartenprüfziffer wurde verarbeitet{/s}" 
     data-moptPayoneParamsMode="{$moptCreditCardCheckEnvironment.moptPayoneParams.mode}" 
     data-moptPayoneParamsMid="{$moptCreditCardCheckEnvironment.moptPayoneParams.mid}" 
     data-moptPayoneParamsAid="{$moptCreditCardCheckEnvironment.moptPayoneParams.aid}" 
     data-moptPayoneParamsPortalid="{$moptCreditCardCheckEnvironment.moptPayoneParams.portalid}" 
     data-moptPayoneParamsHash="{$moptCreditCardCheckEnvironment.moptPayoneParams.hash}" 
     data-moptPayoneParamsLanguage="{$moptCreditCardCheckEnvironment.moptPayoneParams.language}" 
     data-moptCreditcardMinValid="{$moptCreditCardCheckEnvironment.moptCreditcardMinValid}" 
     data-moptCreditcardIntegration="{$moptCreditCardCheckEnvironment.moptCreditcardConfig.integration_type}" 
     data-moptCreditcardConfig='{$moptCreditCardCheckEnvironment.moptCreditcardConfig.jsonConfig}' 
     >


    <a href="#" onclick="showIframe();" id="showiframelink" style="display: none" >ändern</a>
    <BR><BR>
    <input name="moptPaymentData[mopt_payone__cc_accountholder]"
           type="text"
           id="mopt_payone__cc_accountholder"
           {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
           placeholder="{s name='creditCardHolder'}Karteninhaber{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
           value="{$form_data.mopt_payone__cc_accountholder|escape}"
           class="payment--field is--required{if $error_flags.mopt_payone__cc_accountholder} has--error{/if}" />

    <div class="select-field">
        <select name="moptPaymentData[mopt_payone__cc_cardtype]"
                id="mopt_payone__cc_cardtype"
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="select--country is--required{if $error_flags.mopt_payone__cc_cardtype} has--error{/if}">
            <option disabled="disabled" value="" selected="selected">{s name='creditCardType'}Kartentyp{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}</option>
            {foreach from=$moptCreditCardCheckEnvironment.payment_mean.mopt_payone_credit_cards item=credit_card}
                <option value="{$credit_card.short}"
                        {if $form_data.mopt_payone__cc_paymentname == $credit_card.name}selected="selected"{/if}
                        mopt_payone__cc_paymentname="{$credit_card.name}" mopt_payone__cc_paymentid="{$credit_card.id}">
                    {$credit_card.description}
                </option>
            {/foreach}
        </select>
    </div>

    {if $moptIsAjax}
    <input name="moptPaymentData[mopt_payone__cc_truncatedcardpan]"
           type="text"
           id="mopt_payone__cc_truncatedcardpan"
           {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
           placeholder="{s name='creditCardNumber'}Kartennummer{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
           value="{$form_data.mopt_payone__cc_truncatedcardpan|escape}"
           data-moptNumberErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="numberFormField"}Dieses Feld darf nur Zahlen enthalten{/s}"
           class="payment--field is--required{if $error_flags.mopt_payone__cc_truncatedcardpan} has--error{/if} moptPayoneNumber" />
    {else}
        <p class="none">
            <label for="mopt_payone__cc_truncatedcardpan">
                {s name='creditCardNumber'}Kartennummer{/s}
            </label>
        </p>
       <span class="inputIframe" id="mopt_payone__cc_truncatedcardpan"></span>

       <span class="hiddenCCFields" style="display: none">
        <input name="moptPaymentData[mopt_payone__cc_truncatedcardpan_hidden]" type="text"
               id="mopt_payone__cc_truncatedcardpan_hidden"
               value="{$form_data.mopt_payone__cc_truncatedcardpan_hidden|escape}"  readonly
               style="height: {$moptCreditCardCheckEnvironment.moptCreditcardConfig.cardno_iframe_height};
                      width: {$moptCreditCardCheckEnvironment.moptCreditcardConfig.cardno_iframe_width} ! important;
                      {if $moptCreditCardCheckEnvironment.moptCreditcardConfig.cardno_input_css !== ''}
                            {$moptCreditCardCheckEnvironment.moptCreditcardConfig.cardno_input_css}
                      {else}
                            {$moptCreditCardCheckEnvironment.moptCreditcardConfig.standard_input_css}
                      {/if}
                     "
        />
       </span>
        <BR><BR>
    {/if}
    <p class="none">
        <label for="mopt_payone__cc_month">
            {s name='creditCardValidUntil'}Gültig Bis{/s}
        </label>
    </p>
    <div class="select field">
        {if $moptIsAjax}
        <select name="moptPaymentData[mopt_payone__cc_month]"
                id="mopt_payone__cc_month"
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="select--country is--required{if $error_flags.mopt_payone__cc_month} has--error{/if}">
            <option {if $form_data.mopt_payone__cc_month == '01'}selected="selected"{/if} value="01">01</option>
            <option {if $form_data.mopt_payone__cc_month == '02'}selected="selected"{/if} value="02">02</option>
            <option {if $form_data.mopt_payone__cc_month == '03'}selected="selected"{/if} value="03">03</option>
            <option {if $form_data.mopt_payone__cc_month == '04'}selected="selected"{/if} value="04">04</option>
            <option {if $form_data.mopt_payone__cc_month == '05'}selected="selected"{/if} value="05">05</option>
            <option {if $form_data.mopt_payone__cc_month == '06'}selected="selected"{/if} value="06">06</option>
            <option {if $form_data.mopt_payone__cc_month == '07'}selected="selected"{/if} value="07">07</option>
            <option {if $form_data.mopt_payone__cc_month == '08'}selected="selected"{/if} value="08">08</option>
            <option {if $form_data.mopt_payone__cc_month == '09'}selected="selected"{/if} value="09">09</option>
            <option {if $form_data.mopt_payone__cc_month == '10'}selected="selected"{/if} value="10">10</option>
            <option {if $form_data.mopt_payone__cc_month == '11'}selected="selected"{/if} value="11">11</option>
            <option {if $form_data.mopt_payone__cc_month == '12'}selected="selected"{/if} value="12">12</option>
        </select>
        {html_select_date prefix='mopt_payone__cc_' end_year='+10' display_days=false
  display_months=false year_extra='id="mopt_payone__cc_Year" class="select--country is--required"'}
        {else}
             <span id="expireInput" class="inputIframe">
             <span id="mopt_payone__cc_month"></span>
             <span id="mopt_payone__cc_Year"></span>
             </span>

             <span class="hiddenCCFields" style="display: none">
                <input name="moptPaymentData[mopt_payone__cc_cardexpiremonth_hidden]" type="text"
                       id="mopt_payone__cc_cardexpiremonth_hidden"
                       value="{$form_data.mopt_payone__cc_cardexpiremonth_hidden|escape}"  readonly
                       style="height: {$moptCreditCardCheckEnvironment.moptCreditcardConfig.cardmonth_iframe_height};
                               width: {$moptCreditCardCheckEnvironment.moptCreditcardConfig.cardmonth_iframe_width} ! important;
                       {if $moptCreditCardCheckEnvironment.moptCreditcardConfig.cardmonth_input_css !== ''}
                           {$moptCreditCardCheckEnvironment.moptCreditcardConfig.cardmonth_input_css}
                       {else}
                           {$moptCreditCardCheckEnvironment.moptCreditcardConfig.standard_input_css}
                       {/if}
                               "


                />
             </span>
             <span class="hiddenCCFields" style="display: none">
                <input name="moptPaymentData[mopt_payone__cc_cardexpireyear_hidden]" type="text"
                       id="mopt_payone__cc_cardexpireyear_hidden"
                       value="{$form_data.mopt_payone__cc_cardexpireyear_hidden|escape}"  readonly
                       style="height: {$moptCreditCardCheckEnvironment.moptCreditcardConfig.cardyear_iframe_height};
                               width: {$moptCreditCardCheckEnvironment.moptCreditcardConfig.cardyear_iframe_width} ! important;
                       {if $moptCreditCardCheckEnvironment.moptCreditcardConfig.cardyear_input_css !== ''}
                           {$moptCreditCardCheckEnvironment.moptCreditcardConfig.cardyear_input_css}
                       {else}
                           {$moptCreditCardCheckEnvironment.moptCreditcardConfig.standard_input_css}
                       {/if}
                               "
                />
             </span>
        {/if}
    </div>

    {if $moptIsAjax}
    <p class="none" {if !$moptCreditCardCheckEnvironment.moptPayoneCheckCc}style="display:none;"{/if}>
        <input name="mopt_payone__cc_cvc"
               type="text"
               id="mopt_payone__cc_cvc"
               data-moptNumberErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="numberFormField"}Dieses Feld darf nur Zahlen enthalten{/s}"
               {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
               placeholder="{s name='creditCardCvc'}Prüfziffer{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
               class="payment--field is--required{if $error_flags.mopt_payone__cc_cvc} has--error{/if} moptPayoneNumber" />
    </p>
    {elseif $moptCreditCardCheckEnvironment.moptPayoneCheckCc}
        <p class="none" id="label_mopt_payone__cc_cvc">
            <label for="mopt_payone__cc_cvc" class="inputIframe">
                {s name='creditCardCvc'}Prüfziffer{/s}
            </label>
        </p>
        <span id="mopt_payone__cc_cvc" class="inputIframe"></span>
    {/if}

    {if $moptIsAjax}
    <p class="none">
        <span id="mopt_payone__cc_show_saved_hint" style="display:none;font-weight: bold;color: #e1540f;">
            {s namespace='frontend/MoptPaymentPayone/payment' name='ccShowSavedHint'}Platzhalter ccShowSavedHint{/s}
        </span>
    </p>
    {/if}


    <div id="errorOutput"></div>

    <input name="moptPaymentData[mopt_payone__cc_pseudocardpan]" type="hidden" 
           id="mopt_payone__cc_pseudocardpan"
           value="{$form_data.mopt_payone__cc_pseudocardpan|escape}"/>
    <input name="moptPaymentData[mopt_payone__cc_paymentid]" type="hidden" 
           id="mopt_payone__cc_paymentid" 
           value="{$form_data.mopt_payone__cc_paymentid|escape}"/>
    <input name="moptPaymentData[mopt_payone__cc_paymentname]" type="hidden" 
           id="mopt_payone__cc_paymentname" 
           value="{$form_data.mopt_payone__cc_paymentname|escape}"/>
    <input name="moptPaymentData[mopt_payone__cc_paymentdescription]" type="hidden" 
           id="mopt_payone__cc_paymentdescription" 
           value="{$form_data.mopt_payone__cc_paymentdescription|escape}"/>
    <input name="moptPaymentData[mopt_payone__cc_hostediframesubmit]" type="hidden" 
           id="mopt_payone__cc_hostediframesubmit" 
           value="1"/>
    <input name="moptPaymentData[mopt_payone__cc_cardexpiredate]" style="display:none"
           id="mopt_payone__cc_cardexpiredate" 
           value="{$form_data.mopt_payone__cc_cardexpiredate|escape}"/>
    <br />
</div>

<script type="text/javascript">
    //<![CDATA[
    function processPayoneResponse(response) {

        if (response && response.get('status') === 'VALID') {
            $('#mopt_payone__cc_paymentdescription').val($('#mopt_payone__cc_cardtype option:selected').text());
            $('#mopt_payone__cc_truncatedcardpan').val(response.get('truncatedcardpan'));
            $('#mopt_payone__cc_pseudocardpan').val(response.get('pseudocardpan'));
            $('#mopt_payone__cc_paymentid').val($('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentid'));
            $('#mopt_payone__cc_paymentname').val($('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentname'));
            $('#payment_meanmopt_payone_creditcard').val($('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentid'));
            $('#mopt_payone__cc_cvc').val("{s name='creditCardCvcProcessed'}Kartenprüfziffer wurde verarbeitet{/s}");
            $('#mopt_payone__cc_show_saved_hint').show();
            var data = {
                mopt_payone__cc_truncatedcardpan: response.get('truncatedcardpan'),
                mopt_payone__cc_month: $('#mopt_payone__cc_month').val(),
                mopt_payone__cc_year: $('#mopt_payone__cc_Year').val(),
                mopt_payone__cc_cardtype: $('#mopt_payone__cc_cardtype').val(),
                mopt_payone__cc_accountholder: $('#mopt_payone__cc_accountholder').val(),
                mopt_payone__cc_pseudocardpan: response.get('pseudocardpan'),
                mopt_payone__cc_paymentname: $('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentname'),
                mopt_payone__cc_paymentid: $('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentid'),
                mopt_payone__cc_paymentdescription: $('#mopt_payone__cc_cardtype option:selected').text()
            };
            jQuery.post('{url controller="moptAjaxPayone" action="savePseudoCard" forceSecure}', data, function ()
            {
                $("#shippingPaymentForm").submit();
                $('form[name="frmRegister"]').submit();
            });
        } else {
            var errorMessages = [{$moptCreditCardCheckEnvironment.moptPayoneParams.errorMessages}];
            if (response && (response.get('errorcode') in errorMessages[0])) {
                alert(errorMessages[0][response.get('errorcode')]);
            } else {
                alert(errorMessages[0].general);
            }
        }
                }
                ;
                
    function processPayoneIframeResponse(response) {
        if (response.status === "VALID") {
            $('#mopt_payone__cc_hostediframesubmit').val('0');
            $('#mopt_payone__cc_paymentdescription').val($('#mopt_payone__cc_cardtype option:selected').text());
            $('#mopt_payone__cc_pseudocardpan').val(response.pseudocardpan);
            $('#mopt_payone__cc_paymentid').val($('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentid'));
            $('#mopt_payone__cc_paymentname').val($('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentname'));
            $('#payment_meanmopt_payone_creditcard').val($('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentid'));
            $('#mopt_payone__cc_cardexpiredate').val(response.cardexpiredate);
            $('#mopt_payone__cc_truncatedcardpan_hidden').val(response.truncatedcardpan);

            var data = {
                mopt_payone__cc_truncatedcardpan: response.truncatedcardpan,
                mopt_payone__cc_truncatedcardpan_hidden: response.truncatedcardpan,
                mopt_payone__cc_cardtype: $('#mopt_payone__cc_cardtype').val(),
                mopt_payone__cc_accountholder: $('#mopt_payone__cc_accountholder').val(),
                mopt_payone__cc_pseudocardpan: response.pseudocardpan,
                mopt_payone__cc_paymentname: $('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentname'),
                mopt_payone__cc_paymentid: $('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentid'),
                mopt_payone__cc_paymentdescription: $('#mopt_payone__cc_cardtype option:selected').text(),
            mopt_payone__cc_cardexpiredate: response.cardexpiredate
        };
        jQuery.post('{url controller="moptAjaxPayone" action="savePseudoCard" forceSecure}', data, function ()
        {
            var today = new Date();
            var configuredDays = {$moptCreditCardCheckEnvironment.moptCreditcardMinValid};
            var minValidDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() + configuredDays);
            var selectedYear = '20'+response.cardexpiredate.substring(0,2);
            var selectedMonth = response.cardexpiredate.substring(2,4);
            var selectedDate = new Date(selectedYear,selectedMonth,0,0,0);
            var diff = selectedDate.getTime() - minValidDate.getTime();
            if (diff > 0){
                $('#mopt_payone__cc_truncatedcardpan_hidden').val(response.truncatedcardpan);
                //$('.hiddenCCFields').show();
                $("#shippingPaymentForm").submit();
                $('form[name="frmRegister"]').submit();
            } else {
              alert("Das Ablaufdatum ihrer Kreditkarte ist unzureichend!");
            }
        });
        } else {

                        if (response && response.errormessage) {
                            alert(response.errormessage);
                        } else {
                            moptShowGeneralIFrameError();
                        }
        }
    };

    function processPayoneIframeResponseWithoutSubmit(response) {
        if (response.status === "VALID") {
            $('#mopt_payone__cc_hostediframesubmit').val('0');
            $('#mopt_payone__cc_paymentdescription').val($('#mopt_payone__cc_cardtype option:selected').text());
            $('#mopt_payone__cc_pseudocardpan').val(response.pseudocardpan);
            $('#mopt_payone__cc_paymentid').val($('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentid'));
            $('#mopt_payone__cc_paymentname').val($('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentname'));
            $('#payment_meanmopt_payone_creditcard').val($('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentid'));
            $('#mopt_payone__cc_cardexpiredate').val(response.cardexpiredate);
            $('#mopt_payone__cc_truncatedcardpan_hidden').val(response.truncatedcardpan);

            var data = {
                mopt_payone__cc_truncatedcardpan: response.truncatedcardpan,
                mopt_payone__cc_truncatedcardpan_hidden: response.truncatedcardpan,
                mopt_payone__cc_cardtype: $('#mopt_payone__cc_cardtype').val(),
                mopt_payone__cc_accountholder: $('#mopt_payone__cc_accountholder').val(),
                mopt_payone__cc_pseudocardpan: response.pseudocardpan,
                mopt_payone__cc_paymentname: $('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentname'),
                mopt_payone__cc_paymentid: $('#mopt_payone__cc_cardtype option:selected').attr('mopt_payone__cc_paymentid'),
                mopt_payone__cc_paymentdescription: $('#mopt_payone__cc_cardtype option:selected').text(),
                mopt_payone__cc_cardexpiredate: response.cardexpiredate
            };
            jQuery.post('{url controller="moptAjaxPayone" action="savePseudoCard" forceSecure}', data, function ()
            {
                var today = new Date();
                var configuredDays = {$moptCreditCardCheckEnvironment.moptCreditcardMinValid};
                var minValidDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() + configuredDays);
                var selectedYear = '20'+response.cardexpiredate.substring(0,2);
                var selectedMonth = response.cardexpiredate.substring(2,4);
                var selectedDate = new Date(selectedYear,selectedMonth,0,0,0);
                var diff = selectedDate.getTime() - minValidDate.getTime();
                if (diff > 0){

                    location.reload();

                } else {
                    alert("Das Ablaufdatum ihrer Kreditkarte ist unzureichend!");
                }
            });
        } else {

            if (response && response.errormessage) {
                alert(response.errormessage);
            } else {
                console.log("ErrorWithoutsubmit");
                moptShowGeneralIFrameError();
            }
        }
    };

    
    function moptShowGeneralError() {
        var errorMessages = [{$moptCreditCardCheckEnvironment.moptPayoneParams.errorMessages}];
        alert(errorMessages[0].general);
    };
    
    function moptShowGeneralIFrameError() {
        alert("Bitte füllen Sie alle Formularfelder vollständig aus");
    };

    function showhiddenCCFields() {
        var selectedYear = '20'+ $('#mopt_payone__cc_cardexpiredate').val().substring(0,2);
        var selectedMonth = $('#mopt_payone__cc_cardexpiredate').val().substring(2,4);
        $('.inputIframe').hide();
        // show fields with already validated CC Data
        $('.hiddenCCFields').show();
        $('#mopt_payone__cc_accountholder').attr('readonly', true);
        $('#mopt_payone__cc_cardtype').attr('disabled', true);
        $('#mopt_payone__cc_cardexpireyear_hidden').val(selectedYear);
        $('#mopt_payone__cc_cardexpiremonth_hidden').val(selectedMonth);
        $('#showiframelink').show();

    };

    function showIframe() {
        $('#mopt_payone__cc_truncatedcardpan_hidden').val('');
        $('.hiddenCCFields').hide();
        $('#mopt_payone__cc_accountholder').attr('readonly', false);
        $('#mopt_payone__cc_cardtype').attr('disabled', false);
        $('#mopt_payone__cc_cardtype').parents('.js--fancy-select').removeClass('is--disabled');
        $('.inputIframe').show();
    };
//]]>
</script>           
