{namespace name=backend/mopt_config_payone/main}
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>PAYONE Kontrollzentrum </title>
        <link rel="stylesheet" href="{link file="backend/_resources/css/bootstrap.min.css"}">
        <link rel="stylesheet" href="{link file="backend/_resources/css/bootstrap-editable.css"}">
        <link rel="stylesheet" href="{link file="backend/_resources/css/bootstrap-table.css"}">
        <link rel="stylesheet" href="{link file="backend/_resources/css/payone.css"}">
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li>
                            <a href="{url controller="FcPayone" action="index"}">
                                <img src="{link file="backend/_resources/images/payone-favicon.png"}">
                            </a>
                        </li>
                        <li>
                            <a href="{url controller="FcPayone" action="index"}">Start</a>
                        </li>
                        <span style="float:right;margin-left: 30px">
                        <a href="{url controller="MoptExportPayone" action="generateConfigExport"}" class="btn-payone btn" type="button">
                            {s name="config/export"}Konfigurations Export{/s}
                        </a>
                        </span>
                        <span style="float:right;">
                            {s name="config/support"}Supportanfrage an{/s}
                            <input class="submit btn-payone btn btn-submit btn-mailto" type="button" value="PAYONE" onclick="location.href = 'mailto:tech.support@payone.de?body=Sehr%20geehrtes%20Support%20Team,%0D%0A%0D%0A%0A%0A%0ATechnische%20Informationen:%0AShopversion:%20Shopware%20{$params.integrator_version}%0AModulversion:%20{$params.solution_version}&subject=Supportanfrage%20von%20Kundennummer:%20{$data.merchantId}'">
                        </span>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    {include file="parent:backend/_base/navigation.tpl"}
                </div>
                <div class="col-md-9">
                <div class='col-md-12' id = "alert_placeholder_top"></div>                    
                {block name="content/main"}{/block}
                <div class='col-md-12' id = "alert_placeholder_bottom"></div>   
                </div>
            </div>
    </div>

    {block name="resources/javascript"}            
        <script type="text/javascript" src="{link file="backend/base/frame/postmessage-api.js"}"></script>
        <script type="text/javascript" src="{link file="backend/_resources/js/jquery.min.js"}"></script>
        <script type="text/javascript" src="{link file="backend/_resources/js/bootstrap.min.js"}"></script>
        <script type="text/javascript" src="{link file="backend/_resources/js/formhelper.js"}"></script>
        <script type="text/javascript" src="{link file="backend/_resources/js/validator.min.js"}"></script>
        <script type="text/javascript" src="{link file="backend/_resources/js/bootstrap-table.min.js"}"></script>
        <script type="text/javascript" src="{link file="backend/_resources/js/bootstrap-table-de-DE.min.js"}"></script>
        <script type="text/javascript" src="{link file="backend/_resources/js/scripts.js"}"></script>
    {/block}
</body>
</html>
