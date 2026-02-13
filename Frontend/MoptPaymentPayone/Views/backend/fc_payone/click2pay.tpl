{extends file="parent:backend/_base/layout.tpl"}

{block name="content/main"}
    {namespace name=backend/mopt_config_payone/main}
    <div class="col-md-9">
        <h3>{s name="global-form/click2pay"}Konfiguration Click To Pay{/s}</h3>
        <div>
            {s name="global-form/click2payDesc"}Stellen Sie hier die Konfiguration zur Zahlart Click To Pay ein.{/s}
        </div>
        {include file='backend/fc_payone/include/dropdown_payments.tpl'}
        <form role="form" id="click2payform" class="form-horizontal">
        <div class='col-md-9'>
                {* include file='backend/fc_payone/include/input_text.tpl' id='click2payVisaSrcInitiatorId' label="{s name="fieldlabel/click2payVisaSrcInitiatorId"}click2payVisaSrcInitiatorId{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payVisaSrcInitiatorId"}click2payVisaSrcInitiatorId{/s}" placeholder="UUID" *}
                {include file='backend/fc_payone/include/input_text.tpl' id='click2paySrcDpaId' label="{s name="fieldlabel/click2paySrcDpaId"}SrcDpaId{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2paySrcDpaId"}SrcDpaId{/s}" placeholder="UUID"}
                {* include file='backend/fc_payone/include/input_text.tpl' id='click2payVisaEncryptionKey' label="{s name="fieldlabel/click2payVisaEncryptionKey"}click2payVisaEncryptionKey{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payVisaEncryptionKey"}click2payVisaEncryptionKey{/s}" placeholder="STRING" *}
                {* include file='backend/fc_payone/include/input_text.tpl' id='click2payVisaNModulus' label="{s name="fieldlabel/click2payVisaNModulus"}click2payVisaNModulus{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payVisaNModulus"}click2payVisaNModulus{/s}" placeholder="STRING" *}
                {* include file='backend/fc_payone/include/input_text.tpl' id='click2payMasterSrcInitiatorId' label="{s name="fieldlabel/click2payMasterSrcInitiatorId"}click2payMasterSrcInitiatorId{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payMasterSrcInitiatorId"}click2payMasterSrcInitiatorId{/s}" placeholder="UUID" *}
                {* include file='backend/fc_payone/include/input_text.tpl' id='click2payMasterSrcDpaId' label="{s name="fieldlabel/click2payMasterSrcDpaId"}Master SrcDpaId{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payMasterSrcDpaId"}Master SrcDpaId{/s}" placeholder="UUID" *}
        </div>
        <div class='col-md-9'>
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='click2payAllowVisa' label="{s name="fieldlabel/click2payAllowVisa"}Visa Karten erlauben{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payAllowVisa"}Visa Karten erlauben{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='click2payAllowMasterCard' label="{s name="fieldlabel/click2payAllowMasterCard"}MasterCard Karten erlauben{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payAllowMasterCard"}MasterCard Karten erlauben{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='click2payAllowAmex' label="{s name="fieldlabel/click2payAllowAmex"}Amex Karten erlauben{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payAllowAmex"}Amex Karten erlauben{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='click2payAllowMaestro' label="{s name="fieldlabel/click2payAllowMaestro"}Maestro Karten erlauben{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payAllowMaestro"}Maestro Karten erlauben{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='click2payAllowDiners' label="{s name="fieldlabel/click2payAllowDiners"}Diners Karten erlauben{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payAllowDiners"}Diners Karten erlauben{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='click2payAllowDiscover' label="{s name="fieldlabel/click2payAllowDiscover"}Discover Karten erlauben{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payAllowDiscover"}Discover Karten erlauben{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='click2payAllowJcb' label="{s name="fieldlabel/click2payAllowJcb"}Jcb Karten erlauben{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payAllowJcb"}Jcb Karten erlauben{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='click2payAllowUnionpay' label="{s name="fieldlabel/click2payAllowUnionpay"}Unionpay Karten erlauben{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payAllowUnionpay"}Unionpay Karten erlauben{/s}"}
        </div>
        <div class='col-md-9'>
            {include file='backend/fc_payone/include/input_text.tpl' id='click2payShopname' label="{s name="fieldlabel/click2payShopname"}Shop Name{/s}" pattern='^[_ .+-?,:;!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payShopname"}Shop Name{/s}" placeholder="Demoshop"}
            {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='click2payEnableCTP' label="{s name="fieldlabel/click2payEnableCTP"}Click2Pay aktivieren{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payEnableCTP"}Ermöglicht die Registrierung mit der Click-to-Pay-Zahlungsmethode. Wenn diese deaktiviert ist, kann Click to Pay nur von bereits registrierten Kunden genutzt werden. Nicht registrierte Kunden können ihre Kreditkartendaten manuell eingeben.{/s}"}
            {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='click2payEnableCustomerOnboarding' label="{s name="fieldlabel/click2payEnableCustomerOnboarding"}Click2Pay Kundenregistrierung erlauben{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payEnableCustomerOnboarding"}Click2Pay Kundenregistrierung erlauben{/s}"}
        </div>
        <div class='col-md-9'>
                {include file='backend/fc_payone/include/input_text.tpl' id='click2payFormBgColor' label="{s name="fieldlabel/click2payFormBgColor"}Formular Hintergrundfarbe{/s}" pattern='^#[a-fA-F0-9]{6}$' content="{s name="fieldlabelhelp/click2payFormBgColor"}Formular Hintergrundfarbe{/s}" placeholder="#ffffff"}
                {include file='backend/fc_payone/include/input_text.tpl' id='click2payFieldBgColor' label="{s name="fieldlabel/click2payFieldBgColor"}Feld Hintergrundfarbe{/s}" pattern='^#[a-fA-F0-9]{6}$' content="{s name="fieldlabelhelp/click2payFieldBgColor"}Feld Hintergrundfarbe{/s}" placeholder="#ffffff"}
                {include file='backend/fc_payone/include/input_text.tpl' id='click2payFieldBorder' label="{s name="fieldlabel/click2payFieldBorder"}Feld Umrandung{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payFieldBorder"}Feld Umrandung{/s}" placeholder="1px solid #D1D5DB"}
                {include file='backend/fc_payone/include/input_text.tpl' id='click2payFieldOutline' label="{s name="fieldlabel/click2payFieldOutline"}Feld Umriss{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payFieldOutline"}Feld Umriss{/s}" placeholder="#101010 auto .8px"}
                {include file='backend/fc_payone/include/input_text.tpl' id='click2payFieldLabelColor' label="{s name="fieldlabel/click2payFieldLabelColor"}Feld Label Farbe{/s}" pattern='^#[a-fA-F0-9]{6}$' content="{s name="fieldlabelhelp/click2payFieldLabelColor"}Feld Label Farbe{/s}" placeholder="#000000"}
                {include file='backend/fc_payone/include/input_text.tpl' id='click2payFieldPlaceholderColor' label="{s name="fieldlabel/click2payFieldPlaceholderColor"}Feld Platzhalter Farbe{/s}" pattern='^#[a-fA-F0-9]{6}$' content="{s name="fieldlabelhelp/click2payFieldPlaceholderColor"}Feld Platzhalter Farbe{/s}" placeholder="#757575"}
                {include file='backend/fc_payone/include/input_text.tpl' id='click2payFieldTextColor' label="{s name="fieldlabel/click2payFieldTextColor"}Feld Text Farbe{/s}" pattern='^#[a-fA-F0-9]{6}$' content="{s name="fieldlabelhelp/click2payFieldTextColor"}Feld Text Farbe{/s}" placeholder="#000000"}
                {include file='backend/fc_payone/include/input_text.tpl' id='click2payFieldErrorCodeColor' label="{s name="fieldlabel/click2payFieldErrorCodeColor"}Feld FehlerCode Farbe{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payFieldErrorCodeColor"}Feld FehlerCode Farbe{/s}" placeholder="red"}
        </div>
        <div class='col-md-9'>
                {include file='backend/fc_payone/include/dropdown_click2pay_outlinedfilled.tpl' id='click2payButtonStyle' label="{s name="fieldlabel/click2payButtonStyle"}Button Stil{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payButtonStyle"}Button Stil{/s}"}
                {include file='backend/fc_payone/include/dropdown_click2pay_capitalizeuppercase.tpl' id='click2payButtonTextCase' label="{s name="fieldlabel/click2payButtonTextCase"}Button Text Stil{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payButtonTextCase"}Button Text Stil{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='click2payButtonAndBadgeColor' label="{s name="fieldlabel/click2payButtonAndBadgeColor"}Button und Abzeichen Farbe{/s}" pattern='^#[a-fA-F0-9]{6}$' content="{s name="fieldlabelhelp/click2payButtonAndBadgeColor"}Button und Abzeichen Farbe{/s}" placeholder="#000000"}
                {include file='backend/fc_payone/include/input_text.tpl' id='click2payButtonAndBadgeTextColor' label="{s name="fieldlabel/click2payButtonAndBadgeTextColor"}Button und Abzeichen Text Farbe{/s}" pattern='^#[a-fA-F0-9]{6}$' content="{s name="fieldlabelhelp/click2payButtonAndBadgeTextColor"}Button und Abzeichen Text Farbe{/s}" placeholder="#ffffff"}
                {include file='backend/fc_payone/include/input_text.tpl' id='click2payButtonFilledHoverColor' label="{s name="fieldlabel/click2payButtonFilledHoverColor"}Button Hover Farbe{/s}" pattern='^#[a-fA-F0-9]{6}$' content="{s name="fieldlabelhelp/click2payButtonFilledHoverColor"}Button Hover Farbe{/s}" placeholder="#464646"}
                {include file='backend/fc_payone/include/input_text.tpl' id='click2payButtonOutlinedHoverColor' label="{s name="fieldlabel/click2payButtonOutlinedHoverColor"}Button Hover Umriss Farbe{/s}" pattern='^#[a-fA-F0-9]{6}$' content="{s name="fieldlabelhelp/click2payButtonOutlinedHoverColor"}Button Hover Umriss Farbe{/s}" placeholder="#60A5FA"}
                {include file='backend/fc_payone/include/input_text.tpl' id='click2payButtonDisabledColor' label="{s name="fieldlabel/click2payButtonDisabledColor"}Button Disabled Farbe{/s}" pattern='^#[a-fA-F0-9]{6}$' content="{s name="fieldlabelhelp/click2payButtonDisabledColor"}Button Disabled Farbe{/s}" placeholder="#909090"}
                {include file='backend/fc_payone/include/input_text.tpl' id='click2payCardItemActiveColor' label="{s name="fieldlabel/click2payCardItemActiveColor"}Aktive Karten Farbe{/s}" pattern='^#[a-fA-F0-9]{6}$' content="{s name="fieldlabelhelp/click2payCardItemActiveColor"}Aktive Karten Farbe{/s}" placeholder="#6390f2"}
                {include file='backend/fc_payone/include/input_text.tpl' id='click2payLinkTextColor' label="{s name="fieldlabel/click2payLinkTextColor"}Link Farbe{/s}" pattern='^#[a-fA-F0-9]{6}$' content="{s name="fieldlabelhelp/click2payLinkTextColor"}Link Farbe{/s}" placeholder="#0054b6"}
                {include file='backend/fc_payone/include/input_text.tpl' id='click2payAccentColor' label="{s name="fieldlabel/click2payAccentColor"}Akzent Farbe{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payAccentColor"}Akzent Farbe{/s}" placeholder="#6390f2"}
                {include file='backend/fc_payone/include/input_text.tpl' id='click2payFontFamily' label="{s name="fieldlabel/click2payFontFamily"}Font Familie{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payFontFamily"}Font Familie{/s}" placeholder="Nunito, sans-serif"}
                {include file='backend/fc_payone/include/input_text.tpl' id='click2payButtonAndInputRadius' label="{s name="fieldlabel/click2payButtonAndInputRadius"}Button und Input Radius{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payButtonAndInputRadius"}Button und Input Radius{/s}" placeholder="1rem"}
                {include file='backend/fc_payone/include/input_text.tpl' id='click2payCardItemRadius' label="{s name="fieldlabel/click2payCardItemRadius"}Karten Radius{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' content="{s name="fieldlabelhelp/click2payCardItemRadius"}Karten Radius{/s}" placeholder="2rem"}
        </div>
        <div class='col-md-9'>
            <button type="submit" class="btn-payone btn ">{s name="global-form/button"}Speichern{/s}</button>
        </div>
        </form>
    </div>
{/block}

{block name="resources/javascript" append}
    <script type="text/javascript">
        {include file='backend/fc_payone/include/javascript.tpl.js' form="#click2payform" loadAction="generalconfigdata" saveAction="ajaxSavePayoneConfig"}
    </script>
{/block}
