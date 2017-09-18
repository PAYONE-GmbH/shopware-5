{extends file="parent:backend/_base/layout.tpl"}

{block name="content/breadcrump"}
                        <li>
                            {$breadcrump.0}
                        </li>
                        <li>
                            {$breadcrump.1}
                        </li>
                        <li class="active">
                            <a href="{url controller="FcPayone" action="{$breadcrump.2}"}">{$breadcrump.3}</a> <span class="divider">/</span>
                        </li> 
{/block}

{block name="content/main"}
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <iframe style="margin-top: 47px; width: 1280px; height: 1024px; display: block; border: 0px;" src="https://www.payone.de/embedded-sites/shopware/tipps-tricks/">
                </iframe>
            </div>  
        </div>
    </div>    
{/block}