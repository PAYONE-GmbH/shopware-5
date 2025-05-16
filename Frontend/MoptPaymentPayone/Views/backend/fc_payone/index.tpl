{extends file="parent:backend/_base/layout.tpl"}

{block name="content/main"}
    <div class="container">
        <h3 class="p-4">Übersicht</h3>
        <div class="col-md-6"">
            {foreach from=$data item=datum key=i}
                {if $i%2==1}
                <div class="panel panel-primary">
                    <div class="panel-heading" style="background-color: #00395a; text-align:center">
                        <h5 class="panel-title" style="font-size: 24px; text-align:center">{$datum.title}</h5>
                        <div class="panel-body" style="font-size: 48px ; text-align:center">
                            {$datum.data}
                        </div>
                    </div>
                </div>
                {/if}
            {/foreach}
        </div>

        <div class="col-md-6">
            {foreach from=$data item=datum key=j}
                {if $j%2!=1}
                    <div class="panel panel-primary">
                        <div class="panel-heading" style="background-color: #00395a; text-align:center">
                            <h5 class="panel-title" style="font-size: 24px; text-align:center">{$datum.title}</h5>
                            <div class="panel-body" style="font-size: 48px ; text-align:center">
                                {$datum.data}
                            </div>
                        </div>
                    </div>
                {/if}
            {/foreach}
        </div>
    </div>
{/block}