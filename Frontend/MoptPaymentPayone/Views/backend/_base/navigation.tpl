{block name="content/navigation"}
    {namespace name=backend/mopt_config_payone/main}
    <ul class="nav payone-menu-container">
        <li><a id="link-drilldown-1" href="#" class="payone-menu"  data-toggle="collapse" data-target="#drilldown-1">
                <span class="glyphicon glyphicon-arrow-right"></span>{s name="window/main/titlenew"}Konfiguration{/s}<span class="glyphicon glyphicon-chevron-right"></span>
                <span class="glyphicon glyphicon-chevron-down"></span>
            </a>
            <ul id="drilldown-1" class="nav collapse">
                <li><a href="#" id="link-drilldown-1-4" class="box box-menu payone-menu"  data-toggle="collapse" data-target="#drilldown-1-4">
                        <span class="glyphicon glyphicon-arrow-right"></span>{s name="fieldlabel/basicSettings"}Grundeinstellungen{/s}<span class="glyphicon glyphicon-chevron-right"></span>
                        <span class="glyphicon glyphicon-chevron-down"></span>
                    </a>
                    <ul id="drilldown-1-4" class="nav collapse">
                        <li><a class="payone-submenu" href="{url controller="fcPayone" action="connectionconfig"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>{s name="fieldlabel/connectionTest"}Verbindungstest{/s}</a></li>
                        <li><a class="box box-submenu payone-submenu" href="{url controller="fcPayone" action="generalconfig"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>{s name="global-form/fieldset1"}Allgemein{/s}</a></li>
                        <li><a class="box box-submenu payone-submenu" href="{url controller="fcPayone" action="transactionstatusconfig"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>{s name="global-form/fieldset5"}Statusweiterleitung{/s}</a></li>
                        <li><a class="box box-submenu payone-submenu" href="{url controller="fcPayone" action="paymentstatusconfig"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>{s name="global-form/fieldset4"}Paymentstatus{/s}</a></li>
                    </ul>
                </li>
                <li><a id="link-drilldown-1-2" href="#" class="box box-menu payone-menu menu-level-expert"  data-toggle="collapse" data-target="#drilldown-1-2">
                        <span class="glyphicon glyphicon-arrow-right"></span>{s name="fieldlabel/paymentMethods"}Zahlungsarten{/s}<span class="glyphicon glyphicon-chevron-right"></span>
                        <span class="glyphicon glyphicon-chevron-down"></span>
                    </a>
                    <ul id="drilldown-1-2" class="nav collapse">
                        <li><a class="box box-submenu payone-submenu menu-level-expert" href="{url controller="fcPayone" action="debit"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>{s name="fieldlabel/debit"}Lastschrift{/s}</a></li>
                        <li><a class="box box-submenu payone-submenu menu-level-expert" href="{url controller="fcPayone" action="amazonpay"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>{s name="fieldlabel/amazonpay"}Amazon Pay{/s}</a></li>
                        <li><a class="box box-submenu payone-submenu menu-level-expert" href="{url controller="fcPayone" action="applepay"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>{s name="fieldlabel/applepay"}Apple Pay{/s}</a></li>
                        <li><a class="box box-submenu payone-submenu menu-level-expert" href="{url controller="fcPayone" action="creditcard"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>{s name="fieldlabel/creditcard"}Kreditkarte{/s}</a></li>
                        <li><a class="box box-submenu payone-submenu menu-level-expert" href="{url controller="fcPayone" action="paypalexpress"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>{s name="fieldlabel/paypalexpress"}PayPal Express{/s}</a></li>
                        <li><a class="box box-submenu payone-submenu menu-level-expert" href="{url controller="fcPayone" action="paypalexpressv2"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>{s name="fieldlabel/paypalexpressv2"}PayPal Express V2{/s}</a></li>
                        <li><a class="box box-submenu payone-submenu menu-level-expert" href="{url controller="fcPayone" action="unzer"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>{s name="fieldlabel/unzer"}Unzer{/s}</a></li>
                        <li><a class="box box-submenu payone-submenu menu-level-expert" href="{url controller="fcPayone" action="klarna"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>{s name="fieldlabel/klarna"}Klarna{/s}</a></li>
                        <li><a class="box box-submenu payone-submenu menu-level-expert" href="{url controller="fcPayone" action="ratepay"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>{s name="fieldlabel/ratepay"}Ratepay{/s}</a></li>
                        <li><a class="box box-submenu payone-submenu menu-level-expert" href="{url controller="fcPayone" action="googlepay"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>{s name="fieldlabel/googlepay"}Google Pay{/s}</a></li>
                    </ul>
                </li>  
                <li><a href="#" id="link-drilldown-1-3" class="box box-menu payone-menu"  data-toggle="collapse" data-target="#drilldown-1-3">
                        <span class="glyphicon glyphicon-arrow-right"></span>{s name="fieldlabel/riskchecks"}Risikoprüfungen{/s}<span class="glyphicon glyphicon-chevron-right"></span>
                        <span class="glyphicon glyphicon-chevron-down"></span>
                    </a>
                    <ul id="drilldown-1-3" class="nav collapse">
                        <li><a class="box box-submenu payone-submenu" href="{url controller="fcPayone" action="riskcheck"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>{s name="fieldlabel/creditcheck"}Bonitätsprüfung{/s}</a></li>
                        <li><a class="box box-submenu payone-submenu" href="{url controller="fcPayone" action="addresscheck"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>{s name="fieldlabel/addresscheck"}Adressprüfung{/s}</a></li>
                    </ul>
                </li>
            </ul>
        </li>  
        <li><a id="link-drilldown-2" href="#" class="box box-menu payone-menu"  data-toggle="collapse" data-target="#drilldown-2">
                <span class="glyphicon glyphicon-arrow-right"></span>{s name="fieldlabel/protocols"}Protokolle{/s}<span class="glyphicon glyphicon-chevron-right"></span>
                <span class="glyphicon glyphicon-chevron-down"></span>
            </a>
            <ul id="drilldown-2" class="nav collapse">
                <li><a class="box box-submenu payone-submenu" href="{url controller="fcPayone" action="apilog"}">
                        <span class="glyphicon glyphicon-arrow-right"></span>{s name="fieldlabel/apirequests"}API Anfragen{/s}</a></li>
                <li><a class="box box-submenu payone-submenu" href="{url controller="fcPayone" action="transactionlog"}">
                        <span class="glyphicon glyphicon-arrow-right"></span>{s name="fieldlabel/paymentstatus"}Zahlstatus{/s}</a></li>
            </ul>
        </li>
    </ul>
{/block}
