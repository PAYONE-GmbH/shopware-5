{block name="content/navigation"}
    <div class="btn-group">
        <button type="button" id="payone-settings-level" class="btn-payone btn btn-default dropdown-toggle" data-toggle="dropdown">
            <span class="selection">Bearbeitungsebene</span><span class="caret"></span>
        </button>
        <ul class="dropdown-menu" role="menu">
            <li><a href="#">Standard</a></li>
            <li><a href="#">Experte</a></li>
        </ul>
    </div>
    <ul class="nav payone-menu-container">
        <li><a id="link-drilldown-1" href="#" class="payone-menu"  data-toggle="collapse" data-target="#drilldown-1">
                <span class="glyphicon glyphicon-arrow-right"></span>Konfiguration<span class="glyphicon glyphicon-chevron-right"></span>
                <span class="glyphicon glyphicon-chevron-down"></span>
            </a>
            <ul id="drilldown-1" class="nav collapse">
                <li><a id="link-drilldown-1-1" href="#" class="payone-menu"  data-toggle="collapse" data-target="#drilldown-1-1">
                        <span class="glyphicon glyphicon-arrow-right"></span>Allgemein<span class="glyphicon glyphicon-chevron-right"></span>
                        <span class="glyphicon glyphicon-chevron-down"></span>
                    </a>
                    <ul id="drilldown-1-1" class="nav collapse">
                        <li><a class="payone-submenu" href="{url controller="fcPayone" action="ajaxconfig"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>Verbindungstest</a></li>
                        <li><a class="payone-submenu" href="{url controller="fcPayone" action="ajaxtextblocks"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>Texte</a></li>
                        <li><a href="#" id="link-drilldown-1-4" class="box box-menu payone-menu"  data-toggle="collapse" data-target="#drilldown-1-4">
                                <span class="glyphicon glyphicon-arrow-right"></span>Grundeinstellungen<span class="glyphicon glyphicon-chevron-right"></span>
                                <span class="glyphicon glyphicon-chevron-down"></span>
                            </a>
                            <ul id="drilldown-1-4" class="nav collapse">
                                <li><a class="box box-submenu payone-submenu" href="{url controller="fcPayone" action="ajaxgeneralconfig"}">
                                        <span class="glyphicon glyphicon-arrow-right"></span>Allgemein</a></li>
                                <li><a class="box box-submenu payone-submenu" href="{url controller="fcPayone" action="ajaxtransactionstatusconfig"}">
                                        <span class="glyphicon glyphicon-arrow-right"></span>Statusweiterleitung</a></li>
                                <li><a class="box box-submenu payone-submenu" href="{url controller="fcPayone" action="ajaxpaymentstatusconfig"}">
                                        <span class="glyphicon glyphicon-arrow-right"></span>Paymentstatus</a></li>                                
                            </ul>
                        </li>
                    </ul>
                </li>  
                <li><a id="link-drilldown-1-2" href="#" class="box box-menu payone-menu menu-level-expert"  data-toggle="collapse" data-target="#drilldown-1-2">
                        <span class="glyphicon glyphicon-arrow-right"></span>Zahlungsarten<span class="glyphicon glyphicon-chevron-right"></span>
                        <span class="glyphicon glyphicon-chevron-down"></span>
                    </a>
                    <ul id="drilldown-1-2" class="nav collapse">
                        <li><a class="box box-submenu payone-submenu menu-level-expert" href="{url controller="fcPayone" action="ajaxcreditcard"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>Kreditkarte</a></li>
                        <li><a class="box box-submenu payone-submenu menu-level-expert" href="{url controller="fcPayone" action="ajaxonlinetransfer"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>Onlineüberweisung</a></li>
                        <li><a class="box box-submenu payone-submenu menu-level-expert" href="{url controller="fcPayone" action="ajaxdebit"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>Kontobasiert</a></li>
                        <li><a class="payone-submenu menu-level-expert" href="{url controller="fcPayone" action="ajaxwallet"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>Wallet</a></li>
                        <li><a class="box box-submenu payone-submenu menu-level-expert" href="{url controller="fcPayone" action="ajaxfinance"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>Finanzierung</a></li>
                    </ul>
                </li>  
                <li><a href="#" id="link-drilldown-1-3" class="box box-menu payone-menu"  data-toggle="collapse" data-target="#drilldown-1-3">
                        <span class="glyphicon glyphicon-arrow-right"></span>Risk<span class="glyphicon glyphicon-chevron-right"></span>
                        <span class="glyphicon glyphicon-chevron-down"></span>
                    </a>
                    <ul id="drilldown-1-3" class="nav collapse">
                        <li><a class="box box-submenu payone-submenu" href="{url controller="fcPayone" action="ajaxriskcheck"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>Bonitätsprüfung</a></li>
                        <li><a class="box box-submenu payone-submenu" href="{url controller="fcPayone" action="ajaxaddresscheck"}">
                                <span class="glyphicon glyphicon-arrow-right"></span>Adressprüfung</a></li>
                    </ul>
                </li>
            </ul>
        </li>  
        <li><a id="link-drilldown-2" href="#" class="box box-menu payone-menu"  data-toggle="collapse" data-target="#drilldown-2">
                <span class="glyphicon glyphicon-arrow-right"></span>Protokolle<span class="glyphicon glyphicon-chevron-right"></span>
                <span class="glyphicon glyphicon-chevron-down"></span>
            </a>
            <ul id="drilldown-2" class="nav collapse">
                <li><a class="box box-submenu payone-submenu" href="{url controller="fcPayone" action="apilog"}">
                        <span class="glyphicon glyphicon-arrow-right"></span>API-Anfragen</a></li>
                <li><a class="box box-submenu payone-submenu" href="{url controller="fcPayone" action="transactionlog"}">
                        <span class="glyphicon glyphicon-arrow-right"></span>Zahlstatus</a></li>
            </ul>

        </li>  
        <li><a id="link-drilldown-3" href="#" class="box box-menu payone-menu"  data-toggle="collapse" data-target="#drilldown-3">
                <span class="glyphicon glyphicon-arrow-right"></span>Information<span class="glyphicon glyphicon-chevron-right"></span>
                <span class="glyphicon glyphicon-chevron-down"></span>
            </a>
            <ul id="drilldown-3" class="nav collapse">
                <li><a class="box box-submenu payone-submenu" href="{url controller="fcPayone" action="general"}">
                        <span class="glyphicon glyphicon-arrow-right"></span>Allgemein</a></li>
                <li><a class="box box-submenu payone-submenu" href="{url controller="fcPayone" action="tipps"}">
                        <span class="glyphicon glyphicon-arrow-right"></span>Tipps & Tricks</a></li>                        
                <li><a class="box box-submenu payone-submenu" href="{url controller="fcPayone" action="support"}">
                        <span class="glyphicon glyphicon-arrow-right"></span>Support</a></li>
            </ul>
        </li>
    </ul>
{/block}
