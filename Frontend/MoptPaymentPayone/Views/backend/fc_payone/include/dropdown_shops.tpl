{namespace name=backend/mopt_config_payone/main}
<div class="row">
    <div class="col-md-8">
        <div class="btn-group">
            <button id="shopdropdown" type="button" class="btn-payone-fixed btn-payone btn dropdown-toggle" data-toggle="dropdown">
                <span class="selection">Shop</span><span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
                {foreach from=$shops item=shop}
                    <li><a href="#" id="{$shop->getId()}">{$shop->getName()}</a></li>
                {/foreach}
            </ul>
        </div>
    </div>
</div>
