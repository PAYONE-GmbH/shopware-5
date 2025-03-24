{extends file="parent:backend/_base/layout.tpl"}

{block name="content/main"}
    {namespace name=backend/mopt_config_payone/main}
    <div class="col-md-12">
        <h3>{s name="global-form/ratepay"}Konfiguration Ratepay{/s}</h3>
        <div>
            {s name="global-form/ratepayDesc"}Stellen Sie hier die Konfiguration zur Zahlart Ratepay ein.{/s}
        </div>
        <div id="ratepayconfigs" class="form-group" >
            <form role="form" id="ajaxratepay" enctype="multipart/form-data">
                <table class="table-condensed" id="ratepaytable">
                    <tr><th>{s name="fieldlabel/id"}ID{/s}</th><th>ShopID</th><th>{s name="fieldlabel/currency"}Währung{/s}</th><th>{s name="fieldlabel/installmentmode"}Ratenkauf Modus{/s}</th><th>{s name="fieldlabel/country"}Land{/s}</th></tr>
                    {foreach from=$ratepayconfigs key=mykey item=ratepayconfig}
                    <tr id="row{$ratepayconfig->getId()}">
                        <td><input name="row[{$ratepayconfig->getId()}][id]" id="id_{$ratepayconfig->getId()}" type="text" style="max-width:125px;" class="form-control" value="{$ratepayconfig->getId()}" readonly="readonly" ></td>
                        <td><input name="row[{$ratepayconfig->getId()}][shopid]" id="shopid_{$ratepayconfig->getId()}" type="text" style="max-width:125px;" class="form-control" value="{$ratepayconfig->getShopid()}"></td>
                        <td><select class="form-control" name="row[{$ratepayconfig->getId()}][currency]" id="currency_{$ratepayconfig->getId()}">
                                {foreach from=$currencies item=currency}
                                    <option value="{$currency->getId()}" {if $currency->getId() == $ratepayconfig->getCurrency()->getId()}selected="selected"{/if}>{$currency->getName()}</option>
                                {/foreach}
                            </select>
                        </td>
                        <td><select class="form-control" name="row[{$ratepayconfig->getId()}][ratepayInstallmentMode]" id="ratepayInstallmentMode_{$ratepayconfig->getId()}">
                                <option value="0" {if $ratepayconfig->getRatepayInstallmentMode() == 0}selected="selected"{/if}>Vorkasse</option>
                                <option value="1" {if $ratepayconfig->getRatepayInstallmentMode() == 1}selected="selected"{/if}>Lastschrift</option>
                            </select>
                        </td>
                        <td><input name="row[{$ratepayconfig->getId()}][countryCodeBilling]" id="countrycode_{$ratepayconfig->getId()}" type="text" style="max-width:125px;" class="form-control" value="{$ratepayconfig->getCountryCodeBilling()}" readonly="readonly" ></td>
                        <td role="button" name="delete" value="delete" onclick="removeRow({$ratepayconfig->getId()})"><img id="delete_{$ratepayconfig->getId()}" height="100%" src="{link file='backend/_resources/images/delete.png'}"></td>
                        {/foreach}
                    <tr><td><img id="newRow" onclick="addRow()" src="{link file='backend/_resources/images/add.png'}"></td></tr>
                </table>

                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
                <button type="submit" name ="downloadbtn" class="btn-payone btn ">Ratepay {s name="global-form/retrieveconfig"}Konfiguration abrufen{/s}</button>
            </form>
        </div>
    </div>
{/block}

{block name="resources/javascript" append}
    <script type="text/javascript">

        var form = $('#ajaxamazonpay');
        var url = "{url controller=FcPayone action=ajaxgetAmazonConfig forceSecure}";

        $(document).ready(function ()
        {
            var params = "paymentid=0";
            var call = url + '?' + params;
            var iframecall = iframeurl;
            var iframedata = "";

            form.validator('validate');

            $.ajax({
                url: call,
                type: 'POST',
                success: function (data) {
                    response = $.parseJSON(data);
                    if (response.status === 'success') {
                        populateForm(form, response.data);
                        form.validator('validate');
                    }
                    if (response.status === 'error') {
                    }
                }
            });
        });

        form.on("submit", function (event) {
            event.preventDefault();
            var checkboxes = form.find('input[type="checkbox"]');
            $.each(checkboxes, function (key, value) {
                if (value.checked === false) {
                    value.value = 0;
                } else {
                    value.value = 1;
                }
                $(value).attr('type', 'hidden');
            });
            values = form.serialize();
            $.each(checkboxes, function (key, value) {
                $(value).attr('type', 'checkbox');
            });
            var url = 'ajaxSavePaymentConfig';
            values = values + '&paymentId=' + paymentid;
            $.post(url, values, function (response) {
                var data_array = $.parseJSON(response);
                showalert("Die Daten wurden gespeichert", "alert-success");
            });
        });
    </script>
{/block}
