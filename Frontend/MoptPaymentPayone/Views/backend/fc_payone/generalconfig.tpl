{extends file="parent:backend/_base/layout.tpl"}
{namespace name=backend/mopt_config_payone/main}
{block name="content/main"}
    <div class="col-md-12">
        <h3>{s name="global-form/fieldset1"}Allgemein{/s}</h3>
        <div>
            {s name="fieldlabel/configurePaymentsText"}Stellen Sie hier die Konfiguration für Zahlarten ein.{/s}
        </div>
        <div class='col-md-12'>
            <form role="form" id="generalconfigform" class="form-horizontal">
                {include file='backend/fc_payone/include/dropdown_payments.tpl'}
                {include file='backend/fc_payone/include/input_text.tpl' id='merchantId' label="{s name="fieldlabel/merchantId"}Merchant-Id{/s}" pattern="^[0-9]*" minlength="1" maxlength="5" content="{s name="fieldlabelhelp/merchantId"}ID des zu verwendenden Accounts{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='portalId' label="{s name="fieldlabel/portalId"}Portal-Id{/s}" pattern="^[0-9]*" minlength="1" maxlength="7" content="{s name="fieldlabelhelp/portalId"}ID des zu verwendenden Zahlungsportal{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='subaccountId' label="{s name="fieldlabel/subaccountId"}Subaccount-Id{/s}" pattern="^[0-9]*" minlength="1" maxlength="5" content="{s name="fieldlabelhelp/subaccountId"}ID des zu verwendenden SubAccounts{/s}"}
                {include file='backend/fc_payone/include/input_text.tpl' id='apiKey' label="{s name="fieldlabel/apiKey"}Schlüssel{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9`´€]*' minlength="1" maxlength="100" content="{s name="fieldlabelhelp/apiKey"}Schlüssel des zu verwendenden Zahlungsportal{/s}"}
                {include file='backend/fc_payone/include/dropdown_livetest.tpl' id='liveMode' label="{s name="fieldlabel/mode"}Betriebsmodus{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/liveMode"}Hier wird definiert wie die Zahlart verwendet wird. Live = Zahlungen werden auf der PAYONE-Plattform ausgeführt Test = Zahlungen werden nur auf der PAYONE-Testumgebung simuliert{/s}"}
                {include file='backend/fc_payone/include/dropdown_authpreauth.tpl' id='authorisationMethod' label="{s name="fieldlabel/authorisationMethod"}Autorisierung{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/authorisationMethod"}Die Vorautorisierung ist die Eröffnung eines Zahlvorgangs auf der PAYONE-Plattform. Wenn die Zahlart es zulässt wird eine Reservierung des Betrages durchgeführt. Bei Zahlarten wie Sofortueberweisung.de wird der Betrag sofort eingezogen weil dort keine Reservierung durchgeführt werden kann. Bei Zahlarten wie z.B. Vorkasse oder Rechnung wird der Zahlvorgang nur auf der PAYONE – Plattform angelegt. Wenn die Autorisierung durchgeführt wird, dann wird wenn möglich der Betrag sofort eingezogen.{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='submitBasket' label="{s name="fieldlabel/submitBasket"}Warenkorbübergabe{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/submitBasket"}Soll der Warenkorbinhalt an PAYONE übermittelt werden?{/s}"}
                {include file='backend/fc_payone/include/dropdown_saveterms.tpl' id='saveTerms' label="{s name="fieldlabel/saveTerms"}Speichern der AGB Bestätigung{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/saveTerms"}Sobald die AGB einmal bestätigt wurden, wird dies gespeichert und die Checkbox dementsprechend vorausgewählt.{/s}"}
                {include file='backend/fc_payone/include/input_checkbox.tpl' id='sendOrdernumberAsReference' label="{s name="fieldlabel/sendOrdernumberAsReference"}Benutze Shopware-Bestellnummer{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/sendOrdernumberAsReference"}Sendet die Shopware Bestellnummer anstatt einen Zufallswert an Payone. Diese Option gilt global für alle Zaharten{/s}"}
                {include file='backend/fc_payone/include/input_checkbox.tpl' id='changeOrderOnTXS' label="{s name="fieldlabel/changeOrderOnTXS"}Bestellung geändert bei TX Status{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/changeOrderOnTXS"}Setze das changed Datum einer Bestellung, wenn ein Transaktions-Status erfolgreich war. Greift erst ab Shopware Version 5.5.0{/s}"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='allowDifferentAddresses' label="{s name="fieldlabel/allowDifferentAdresses"}Abweichende Lieferadressen zulassen{/s}" pattern="^[0-9]*" content="{s name="fieldlabelhelp/allowDifferentAdresses"}Hinweis: Muss im PAYONE-Konto freigeschaltet sein!{/s}"}
                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}
    <script type="text/javascript">
        {include file='backend/fc_payone/include/javascript.tpl.js' form="#generalconfigform" loadAction="generalconfigdata" saveAction="ajaxSavePayoneConfig"}
    </script>
{/block}
