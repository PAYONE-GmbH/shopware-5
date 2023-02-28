{namespace name='frontend/MoptPaymentPayone/payment'}

{if $payment_mean.id == $form_data.payment && $moptRatepayConfig.ratepayInstallmentMode}
    {assign var="moptRequired" value=1}
{else}
    {assign var="moptRequired" value=0}
{/if}

{if $payment_mean.id == $form_data.payment}
<link rel="stylesheet" type="text/css" href="{link file="frontend/_resources/styles/ratepay.css"}" />
<div class="payment--form-group">
    {if $moptBillingCountryChanged}
        <div id="ratepay_overlay_installment_redirect_notice" class="js--modal content" style="width:40%; height:40%; opacity: 0.9; margin: 75px auto;">
            <a href="#" onclick="removeRatepayOverlayInstallmentRedirectNotice();
            return false;" style="float:right;font-weight:bold;">Fenster schliessen</a><br><br>
            {$moptOverlayRedirectNotice}
        </div>
        <div id="ratepay_overlay_installment_redirect_notice_bg" class="js--overlay is--open" style="opacity: 0.8;"></div>

        <script type="text/javascript">
            function removeRatepayOverlayInstallmentRedirectNotice() {
                document.getElementById('ratepay_overlay_installment_redirect_notice').style.display = "none";
                document.getElementById('ratepay_overlay_installment_redirect_notice_bg').style.display = "none";
            }
        </script>
    {/if}
    <div id="mopt_payone__ratepay_installment_abg">
        <p>{s name='ratepayLegalText'}Mit Klicken auf "Zahlungspflichtig bestellen" erklären Sie sich mit den <a target="_blank" href="https://www.ratepay.com/legal-payment-terms">Zahlungsbedingungen unseres Zahlungspartners</a> sowie mit der Durchführung einer <a target="_blank" href="https://www.ratepay.com/legal-payment-dataprivacy">Risikoprüfung durch unseren Zahlungspartner</a> einverstanden.{/s}</p>
    </div>

    {if ! $sUserData.billingaddress.company}
        <p class ="none">
            <label for="mopt_payone__ratepay_installment_birthday">
                {s name='birthdate'}Geburtsdatum{/s}
            </label>
        </p>

    <div class="select-field">
        <select name="moptPaymentData[mopt_payone__ratepay_installment_birthday]"
                id="mopt_payone__ratepay_installment_birthday" onchange="ratepayInstallmentDobInput()"
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="is--required{if $error_flags.mopt_payone__ratepay_installment_birthday} has--error{/if}">
            <option value="">--</option>
            {section name="birthdate" start=1 loop=32 step=1}
                <option value="{if $smarty.section.birthdate.index < 10}0{/if}{$smarty.section.birthdate.index}"
                        {if $smarty.section.birthdate.index eq $moptCreditCardCheckEnvironment.mopt_payone__ratepay_installment_birthday}
                            selected
                        {/if}>
                    {$smarty.section.birthdate.index}</option>
            {/section}
        </select>
    </div>

    <div class="select-field">
        <select name="moptPaymentData[mopt_payone__ratepay_installment_birthmonth]"
            id="mopt_payone__ratepay_installment_birthmonth" onchange="ratepayInstallmentDobInput()"
            {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
            class="is--required {if $error_flags.mopt_payone__ratepay_installment_birthmonth} has--error{/if}">
            <option value="">--</option>
            {section name="birthmonth" start=1 loop=13 step=1}
                <option value="{if $smarty.section.birthmonth.index < 10}0{/if}{$smarty.section.birthmonth.index}"
                    {if $smarty.section.birthmonth.index eq $moptCreditCardCheckEnvironment.mopt_payone__ratepay_installment_birthmonth}
                        selected
                    {/if}>
                    {if $smarty.section.birthmonth.index < 10}0{/if}{$smarty.section.birthmonth.index}</option>
            {/section}
        </select>
    </div>

    <div class="select-field">
        <select name="moptPaymentData[mopt_payone__ratepay_installment_birthyear]"
                id="mopt_payone__ratepay_installment_birthyear" onchange="ratepayInstallmentDobInput()"
                {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
                class="select--country is--required{if $error_flags.mopt_payone__ratepay_installment_birthyear} register--error-msg {/if}">
            <option value="">----</option>
            {section name="birthyear" loop=2016 max=100 step=-1}
                <option value="{$smarty.section.birthyear.index}"
                        {if $smarty.section.birthyear.index eq $moptCreditCardCheckEnvironment.mopt_payone__ratepay_installment_birthyear}
                selected
                        {/if}>
                    {$smarty.section.birthyear.index}</option>
            {/section}
        </select>
    </div>
    {/if}

    {if $moptRatepayConfig.ratepayInstallmentMode}

        <input name="moptPaymentData[mopt_payone__ratepay_installment_iban]"
               type="text"
               id="mopt_payone__ratepay_installment_iban"
               {if $moptRequired}required="required" aria-required="true"{/if}
               data-moptIbanWrongCharacterMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="moptIbanWrongCharacterMessage"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}"
               data-moptIbanWrongLengthMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="moptIbanWrongLengthMessage"}Bitte prüfen Sie die Länge der IBAN{/s}"
               data-moptIbanWrongCecksumMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="moptIbanWrongCecksumMessage"}Die Prüfsumme der IBAN ist falsch{/s}"
               placeholder="{s name='bankIBAN'}IBAN{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
               value="{$form_data.mopt_payone__ratepay_installment_iban|escape}"
               class="payment--field is--required{if $error_flags.mopt_payone__ratepay_installment_iban} has--error{/if} moptPayoneIban" />

        <input name="moptPaymentData[mopt_payone__ratepay_installment_bic]"
               type="text"
               id="mopt_payone__ratepay_installment_bic"
               {if $moptRequired}required="required" aria-required="true"{/if}
               placeholder="{s name='bankBIC'}BIC{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
               value="{$form_data.mopt_payone__ratepay_installment_bic|escape}"
               data-moptIbanErrorMessage="{s namespace='frontend/MoptPaymentPayone/errorMessages' name="ibanbicFormField"}Dieses Feld darf nur Großbuchstaben und Ziffern enthalten{/s}"
               class="payment--field is--required{if $error_flags.mopt_payone__ratepay_installment_bic} has--error{/if} moptPayoneBic" />
    {/if}

    <input id="mopt_payone__ratepay_installment_birthdaydate" class="is--hidden validate-18-years" type="text" name="moptPaymentData[mopt_payone__ratepay_installment_birthdaydate]" value="{$moptCreditCardCheckEnvironment.birthday}"/>
    <div id="ratepay-installment-hint-18-years" class="is--hidden">{s name='birthdayUnderageError'}Sie müssen mindestens 18 Jahre alt sein, um diese Zahlart verwenden zu können.{/s}</div>

    <input id="ratePayShopId" class="is--hidden" type="text" name="moptPaymentData[mopt_payone__ratepay_installment_shopid]" value="{$moptRatepayConfig.shopid}"/>
    <input id="ratepayDeviceToken" class="is--hidden" type="text" name="moptPaymentData[mopt_payone__ratepay_installment_device_fingerprint]" value="{$moptRatepayConfig.deviceFingerPrint}"/>

    <input id="ratePayCurrency" class="is--hidden" type="text" name="moptPaymentData[mopt_payone__ratepay_installment_currency]" value="EUR"/>

    <input name="moptPaymentData[mopt_payone__ratepay_installment_telephone]"
           type="text"
           id="mopt_payone__ratepay_installment_telephone"
           {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
           placeholder="{s name='telephoneNumber'}Telefonnummer{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
           value="{$moptCreditCardCheckEnvironment.mopt_payone__ratepay_installment_telephone|escape}"
           class="payment--field is--required{if $error_flags.mopt_payone__ratepay_installment_telephone} has--error{/if}"
    />
    {if $sUserData.billingaddress.company}
        <input type="text" name="moptPaymentData[mopt_payone__ratepay_installment_company_trade_registry_number]"
               id="mopt_payone__ratepay_installment_company_trade_registry_number"
               {if $payment_mean.id == $form_data.payment}required="required" aria-required="true"{/if}
               placeholder="{s name='companyTradeRegistryNumber'}Handelsregisternummer*{/s}{s name="RequiredField" namespace="frontend/register/index"}{/s}"
               class="is--required{if $error_flags.mopt_payone__ratepay_installment_company_trade_registry_number} has--error{/if}">

        <input class="is--hidden" type="text" name="moptPaymentData[mopt_payone__ratepay_b2bmode]" id="mopt_payone__ratepay_b2bmode" value="1">

    {/if}
</div>

<div id="cover">
    <div id="ajaxLoaderId">
    </div>
</div>

<script type="text/javascript"
        src="{link file='frontend/_resources/javascript/mopt_ratepay.js'}">
</script>

<div id="ratepay-main-cont" style="display: block">
    <div id="ratepay-Header">
        <div class="ratepay-FullWidth">
            <h2 class="ratepay-H2">{s name='CalculateHereYourOwnRate'}HIER persönliche Rate berechnen!{/s}</h2>
        </div>
        <br class="ratepay-ClearFix" />
    </div>

    <div id="ratepay-ContentSwitch">
        <div class="ratepay-ChooseRuntime">
            {s name='cashPaymentPricePartOne'}Bitte entscheiden Sie sich nun, wie der Bestellwert von{/s}
            <input type="hidden" id="mopt_payone__ratepay_installment_amount" name="moptPaymentData[mopt_payone__ratepay_installment_amount]" value="{$sAmount}"/>
            <span><b>{$sAmount|number_format:2:",":"."}</b></span>
            {s name='cashPaymentPricePartTwo'}auf die monatlichen Raten verteilt werden soll. Hierzu haben Sie zwei M&ouml;glichkeiten:{/s}<BR>
            <label for="firstInput" style="width:100%;">
                <BR>
                <div class="ratepay-ChooseInput" id="{$sFormData.payment}_ChooseInputRate">
                    <input id="firstInput" class="ratepay-FloatLeft" type="radio" name="Zahlmethode" value="wishrate"  onClick="switchRateOrRuntime('rate', '{$sFormData.payment}');" checked="checked">
                </div>
                <div id="{$sFormData.payment}_SwitchToTerm" class="ratepay-NintyPercentWidth ratepay-FloatLeft ratepay-PaddingLeft" style="color: black;">{s name='paymentTextWishrate'}<b>Monatliche Rate</b><BR> angeben und die sich daraus ergebende Laufzeit berechnen lassen.{/s}</div>
            </label>
            <br class="ratepay-ClearFix" />
            <label for="secondInput" style="width:100%;">
                <div class="ratepay-ChooseInput" id="{$sFormData.payment}_ChooseInputRuntime">
                    <input id="secondInput" class="ratepay-FloatLeft" type="radio" name="Zahlmethode" value="runtime" onClick="switchRateOrRuntime('runtime', '{$sFormData.payment}');">
                </div>
                <div id="{$sFormData.payment}_SwitchToRuntime" class="ratepay-NintyPercentWidth ratepay-FloatLeft ratepay-PaddingLeft" style="color: black;">{s name='paymentTextRuntime'}<b>Laufzeit</b><BR> angeben und die sich daraus ergebende monatliche Rate berechnen lassen.{/s}</div>
            </label>

            <div id="{$sFormData.payment}_ContentTerm" class="ratepay-Content" style="display: block;">

                <br class="ratepay-ClearFix" />
                <div class="ratepay-MarginTop">
                    <span class="ratepay-VertAlignMiddle">{s name='please'}Bitte&nbsp{/s}{s name='insertWishrate'}Wunschrate eingeben{/s}</span>
                    <input id="{$sFormData.payment}-rate" class="ratepay-Input-amount" type="text"
                     {if $error_flags.mopt_payone__ratepay_installment_amount}style="background:#e74c3c; color: #000000"{/if}
                    >
                    <span class="ratepay-Currency"> &euro;</span>
                    <input onclick="ratepayRateCalculatorAction('rate', '{$sFormData.payment}', '{url controller='moptAjaxPayone' action='rate'}');" value="{s name='calculateRuntime'}Laufzeit jetzt berechnen{/s}" id="{$sFormData.payment}_Input-button" class="ratepay-Input-button" type="button">
                </div>
            </div>

            <div id="{$sFormData.payment}_ContentRuntime" class="ratepay-Content" style="display: none;">
                <br class="ratepay-ClearFix" />
                <div class="ratepay-MarginTop">
                    <span class="ratepay-VertAlignMiddle" style="float: left;">{s name='please'}Bitte&nbsp{/s}{s name='insertRuntime'}Laufzeit w&auml;hlen{/s}</span>
                    <div class="select-field">
                        <select id="{$sFormData.payment}-runtime">
                            <option value="3"  selected="selected">{s name='3months'}3 months{/s}</option>
                            <option value="6"  selected="selected">{s name='6months'}6 months{/s}</option>
                            <option value="9"  selected="selected">{s name='9months'}9 months{/s}</option>
                            <option value="12" selected="selected">{s name='12months'}12 months{/s}</option>
                            <option value="24" selected="selected">{s name='24months'}24 months{/s}</option>
                        </select>
                    </div>
                    <input name="" onclick="ratepayRateCalculatorAction('runtime', '{$sFormData.payment}', '{url controller='moptAjaxPayone' action='runtime'}');" value="{s name='calculateRate'}Rate jetzt berechnen{/s}" type="button" id="{$sFormData.payment}_Input-buttonRuntime"  class="ratepay-Input-button2">
                </div>
            </div>
            <br class="ratepay-ClearFix" />
            <div id="{$sFormData.payment}_ResultContainer">
            </div>
        </div>
    </div>
</div>

    <script>
        switchRateOrRuntime('rate', '{$sFormData.payment}');
    </script>

{if $moptRatepayConfig.deviceFingerPrint && $moptRatepayConfig.deviceFingerprintSnippetId}

    <!-- Only Include if moptRatepayConfig is configured in Backend to prevent 404 errors -->
    <script language="JavaScript">
        var di = { t: '{$moptRatepayConfig.deviceFingerPrint}', v: '{ $moptRatepayConfig.deviceFingerprintSnippetId}', l: 'Checkout'};
    </script>
    <script type="text/javascript"
            src="//d.ratepay.com/{$moptRatepayConfig.deviceFingerprintSnippetId}/di.js"></script>
    <noscript><link rel="stylesheet" type="text/css"
                    href="//d.ratepay.com/di.css?t={$moptRatepayConfig.deviceFingerPrint}&v={$moptRatepayConfig.deviceFingerprintSnippetId}&l=Check
                    out"></noscript>
    <object type="application/x-shockwave-flash"
            data="//d.ratepay.com/{$moptRatepayConfig.deviceFingerprintSnippetId}/c.swf" width="0" height="0">
        <param name="movie" value="//d.ratepay.com/{$moptRatepayConfig.deviceFingerprintSnippetId}/c.swf" />
        <param name="flashvars"
               value="t={$moptRatepayConfig.deviceFingerPrint}&v={$moptRatepayConfig.deviceFingerprintSnippetId}"/><param
                name="AllowScriptAccess" value="always"/>
    </object>
{/if}
<script type="text/javascript">

    function ratepayInstallmentDobInput()
    {
        var daySelect = document.getElementById("mopt_payone__ratepay_installment_birthday");
        var monthSelect = document.getElementById("mopt_payone__ratepay_installment_birthmonth");
        var yearSelect = document.getElementById('mopt_payone__ratepay_installment_birthyear');
        var hiddenDobFull = document.getElementById("mopt_payone__ratepay_installment_birthdaydate");
        var hiddenDobHint = document.getElementById("ratepay-installment-hint-18-years");

        if (daySelect.value == "" || monthSelect.value == "" || yearSelect.value == ""
            || daySelect == undefined) {
            return;
        }
        hiddenDobFull.value = yearSelect.value + "-" + monthSelect.value + "-" + daySelect.value;
        console.log("HiddenDob:");
        console.log(hiddenDobFull.value);
        var oBirthDate = new Date(hiddenDobFull.value);
        var oMinDate = new Date(new Date().setYear(new Date().getFullYear() - 18));
        if (oBirthDate > oMinDate) {
            hiddenDobHint.className = "register--error-msg";
        } else {
            hiddenDobHint.className = "is--hidden";
            return;
        }
    }

</script>
{/if}