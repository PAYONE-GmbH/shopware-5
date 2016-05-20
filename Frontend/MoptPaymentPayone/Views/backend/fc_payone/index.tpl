{extends file="parent:backend/_base/layout.tpl"}

{block name="content/main"}
                
        <h3 style="padding-left: 15px;padding-right: 15px;padding-top: 27px; ">Übersicht</h3>
        <BR>
        <div class="col-md-6">

            <div class="panel panel-primary">
                <div class="panel-heading" style="background-color: #00395a; text-align:center">
                    <h3 style="font-size: 24px; text-align:center" class="panel-title">{$title.0}</h3>
                </div>
                <div style="font-size: 48px ; text-align:center" class="panel-body">
                    {$data.0} 
                </div>
            </div>
            <div class="panel panel-primary">
                <div class="panel-heading"style="background-color: #00395a;text-align:center">
                    <h3 style="font-size: 24px;text-align:center" class="panel-title">{$title.1}</h3>
                </div>
                <div style="font-size: 48px; text-align:center" class="panel-body">
                    {$data.1}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading" style="background-color: #00395a; text-align:center"">
                    <h3 style="font-size: 24px;text-align:center" class="panel-title">{$title.2}</h3>
                </div>
                <div style="font-size: 48px ;text-align:center" class="panel-body">
                    {$data.2}
                </div>
            </div>
            <div class="panel panel-primary">
                <div class="panel-heading" style="background-color: #00395a">
                    <h3 style="font-size: 24px ;text-align:center" class="panel-title">{$title.3}</h3>
                </div>
                <div  style="font-size: 48px ;text-align:center" class="panel-body">
                    {$data.3}
                </div>
            </div>
        </div>
{/block}