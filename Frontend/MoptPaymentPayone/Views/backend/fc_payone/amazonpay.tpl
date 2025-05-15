{extends file="parent:backend/_base/layout.tpl"}

{block name="content/main"}
    {namespace name=backend/mopt_config_payone/main}
    <div class="col-md-12">
        <h3>{s name="global-form/amazonpay"}Konfiguration AmazonPay Logos{/s}</h3>
        <div>
            {s name="global-form/amazonpayDesc"}Stellen Sie hier die Konfiguration zur Zahlart Amazonpay ein.{/s}
        </div>
        <div id="amazonpayconfigs" class="form-group">
            <form role="form" id="amazonpayform" enctype="multipart/form-data">
                <table class="table-condensed" id="amazonpaytable">
                    <tr>
                        <th>id</th>
                        <th>{s name="amazon_clientid"}Client Id{/s}</th>
                        <th>{s name="amazon_sellerid"}Seller Id{/s}</th>
                        <th>{s name="amazon_buttontype"}Button Type{/s}</th>
                        <th>{s name="amazon_buttoncolor"}Button Color{/s}</th>
                        <th>{s name="amazon_mode"}Amazon Mode{/s}</th>
                        <th>{s name="amazon_packstation_mode"}Packstation{/s}</th>
                        <th>{s name="shop"}Shop{/s}</th>
                    </tr>
                    {foreach from=$amazonpayconfigs key=mykey item=amazonpayconfig}
                    <tr id="row{$amazonpayconfig->getId()}">
                        <td><input name="row[{$amazonpayconfig->getId()}][id]" id="id_{$amazonpayconfig->getId()}"
                                   type="text" style="max-width:125px;" class="form-control"
                                   value="{$amazonpayconfig->getId()}" readonly="readonly"></td>
                        <td><input name="row[{$amazonpayconfig->getId()}][clientId]"
                                   id="amazonpayClientId_{$amazonpayconfig->getId()}" type="text"
                                   style="max-width:125px;" class="form-control"
                                   value="{$amazonpayconfig->getClientId()}" readonly="readonly"></td>
                        <td><input name="row[{$amazonpayconfig->getId()}][sellerId]"
                                   id="amazonpaySellerId_{$amazonpayconfig->getId()}" type="text"
                                   style="max-width:125px;" class="form-control"
                                   value="{$amazonpayconfig->getSellerId()}" readonly="readonly"></td>
                        <td>
                            <select class="form-control" name="row[{$amazonpayconfig->getId()}][buttonType]"
                                    id="amazonpayButtonType_{$amazonpayconfig->getId()}">
                                <option value="PwA"
                                        {if $amazonpayconfig->getButtonType() == 'PwA'}selected="selected"{/if}>{s name="amazon_buttontype_amazonpay"}Amazon Pay (Default): Typical "Amazon Pay" button{/s}</option>
                                <option value="Pay"
                                        {if $amazonpayconfig->getButtonType() == 'Pay'}selected="selected"{/if}>{s name="amazon_buttontype_pay"}Pay: A slightly smaller "Pay" button{/s}</option>
                                <option value="A"
                                        {if $amazonpayconfig->getButtonType() == 'A'}selected="selected"{/if}>{s name="amazon_buttontype_a"}A: A small button with only the Amazon Pay Logo{/s}</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-control" name="row[{$amazonpayconfig->getId()}][buttonColor]"
                                    id="amazonpayButtonColor_{$amazonpayconfig->getId()}">
                                <option value="Gold"
                                        {if $amazonpayconfig->getButtonColor() == 'Gold'}selected="selected"{/if}>{s name="amazon_buttoncolor_gold"}Gold (default){/s}</option>
                                <option value="LightGray"
                                        {if $amazonpayconfig->getButtonColor() == 'LightGray'}selected="selected"{/if}>{s name="amazon_buttoncolor_lightgray"}Light gray{/s}</option>
                                <option value="DarkGray"
                                        {if $amazonpayconfig->getButtonColor() == 'DarkGray'}selected="selected"{/if}>{s name="amazon_buttoncolor_darkgray"}Dark gray{/s}</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-control" name="row[{$amazonpayconfig->getId()}][amazonMode]"
                                    id="amazonpayAmazonMode_{$amazonpayconfig->getId()}">
                                <option value="sync"
                                        {if $amazonpayconfig->getAmazonMode() == 'sync'}selected="selected"{/if}>{s name="amazon_mode_always_sync"}Always Synchronous{/s}</option>
                                <option value="firstsync"
                                        {if $amazonpayconfig->getAmazonMode() == 'firstsync'}selected="selected"{/if}>{s name="amazon_mode_always_firstsync"}First synchronous, on failure try asynchronous (recommended, default):{/s}</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-control" name="row[{$amazonpayconfig->getId()}][packStationMode]"
                                    id="amazonpaypackStationMode_{$amazonpayconfig->getId()}">
                                <option value="allow"
                                        {if $amazonpayconfig->getPackStationMode() == 'allow'}selected="selected"{/if}>{s name="packStation/allow" namespace="backend/mopt_payone_paypal/main"}allow{/s}</option>
                                <option value="deny"
                                        {if $amazonpayconfig->getPackStationMode() == 'deny'}selected="selected"{/if}>{s name="packStation/deny" namespace="backend/mopt_payone_paypal/main"}deny{/s}</option>
                            </select>
                        </td>
                        <td><select class="form-control" name="row[{$amazonpayconfig->getId()}][shopId]" id="shop_{$amazonpayconfig->getId()}">
                                {foreach from=$shops item=shop}
                                    <option value="{$shop->getId()}" {if $shop->getId() == $amazonpayconfig->getShop()->getId()} selected="selected"{/if}>{$shop->getName()}</option>
                                {/foreach}
                            </select>
                        </td>
                        <td role="button" name="delete" value="delete" onclick="removeRow({$amazonpayconfig->getId()});">
                            <img id="delete_{$amazonpayconfig->getId()}" height="100%"
                                 src="{link file='backend/_resources/images/delete.png'}">
                        </td>
                        {/foreach}
                    </tr>
                </table>
                <div>
                    <img id="newRow" onclick="addRow()" src="{link file='backend/_resources/images/add.png'}">
                </div>

                <button type="submit" class="btn-payone btn ">{s name="global-form/button"}Speichern{/s}</button>
                <button type="submit" name="amazondownloadbtn" class="btn-payone btn">
                    AmazonPay {s name="global-form/retrieveconfig"}Konfiguration abrufen{/s}</button>
            </form>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}
<script type="text/javascript">
    {include file='backend/fc_payone/include/amazonpay.tpl.js'}
</script>
{/block}
