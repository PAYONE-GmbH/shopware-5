{extends file="parent:backend/_base/layout.tpl"}

{block name="content/main"}
    {namespace name=backend/mopt_config_payone/main}
    <div class="col-md-12">
        <h3>{s name="global-form/ratepay"}Konfiguration Ratepay{/s}</h3>
        <div>
            {s name="global-form/ratepayDesc"}Stellen Sie hier die Konfiguration zur Zahlart Ratepay ein.{/s}
        </div>
        <div id="ratepayconfigs" class="form-group" >
            <form role="form" id="ratepaysettingsform" enctype="multipart/form-data">
                {include file='backend/fc_payone/include/dropdown_payments.tpl'}
                <div class="row">
                    {include file='backend/fc_payone/include/input_text.tpl' id='ratepaySnippetId' label="{s name="fieldlabel/ratepaySnippetId"}Ratepay Snippet Id{/s}" pattern='^[_ .+-?,:;"!@#$%^&*ÄÖÜäöüa-zA-Z0-9]*' minlength="1" maxlength="100" content="{s name="fieldlabelhelp/ratepaySnippetId"}Ratepay Snippet Id{/s}"}
                </div>
                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
            </form>
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
        {include file='backend/fc_payone/include/javascript.tpl.js' form="#ratepaysettingsform" loadAction="generalconfigdata" saveAction="ajaxSavePayoneConfig"}
    </script>

    <script type="text/javascript">
        var ratepayform = $('#ajaxratepay');
        var ratepaydownloadurl = "{url controller=MoptPayoneRatepay action=downloadRatepayConfigs forceSecure}";
        var ratepaysaveurl = "{url controller=MoptPayoneRatepay action=saveRatepayConfigs forceSecure}";
        var ratepaydownloadbtn = $('#downloadbtn');
        var paymentid = null;

        ratepayform.on("submit", function (event) {
            event.preventDefault();
            ratepayvalues = ratepayform.serialize();
            var submitAction = $(this.id).context.activeElement.name;
            if (submitAction == 'downloadbtn') {
                var url = ratepaydownloadurl;
            } else {
                var url = ratepaysaveurl;

            }
            $.post(url, ratepayvalues, function (response) {
                {literal}
                var data_array = $.parseJSON(response);
                {/literal}
                $('#ratepaytable tr').css('background-color','');
                if (data_array.errorElem && data_array.errorElem.length) {
                    if (data_array.errorElem.length > 0){
                        alert('data_array.errorElem.length >0');
                        data_array.errorElem.forEach(markDownloadErrors);
                        showalert("Das Abrufen von " + data_array.errorElem.length + " Konfigurationen ist fehlgeschlagen", "alert-danger");
                    }
                } else {
                    showalert("Die Daten wurden gespeichert", "alert-success");
                    location.reload();
                }
            });
        });

        function removeRow(rowId) {
            $('#row' + rowId).remove();
        };

        function addRow() {
            var len = $('#ratepaytable tbody tr').length;
            var newRow = "<tr id='row" + len + "'><td><input name='row[" + len + "][id] id='id_" + len + "' type='text' style='max-width:125px;' class='form-control' value='' readonly='readonly' ></td>" +
                "<td><input name='row[" + len + "][shopid] id='shopid_" + len + "' type='text' style='max-width:125px;' class='form-control'value=></td>" +
                "<td>"+ "<select class='form-control' name='row[" + len + "][currency]' id='currency_" + len + "'>"+
                "{foreach from=$currencies item=currency}<option value='{$currency->getId()}'>{$currency->getName()}</option>{/foreach}"+
                "</select></td>"+
                "<td>"+ "<select class='form-control' name='row[" + len + "][ratepayInstallmentMode]' id='ratepayInstallmentMode_" + len + "'>"+
                "<option value='0' {if $ratepayconfig && $ratepayconfig->getRatepayInstallmentMode() == 0}selected='selected'{/if}>Vorkasse</option>"+
                "<option value='1' {if $ratepayconfig && $ratepayconfig->getRatepayInstallmentMode() == 1}selected='selected'{/if}>Lastschrift</option>"+
                "</select></td>"+
                "<td><input name='row[" + len + "][countryCodeBilling]' id='countrycode_" + len + "' type='text' style='max-width:125px;' class='form-control' readonly='readonly'></td>" +
                "<td role='button' name='delete' value='delete' onclick='removeRow(" + len + ");'><img id='delete_" + len + "' height='100%' src='{link file="backend/_resources/images/delete.png"}'></td>" +
                "</tr>";
            $('#ratepaytable > tbody:last-child').append(newRow);
        };

        function markDownloadErrors(item, index) {
            var d = document.getElementById('row' + item);
            d.style.backgroundColor = "red";
        }
{/block}

