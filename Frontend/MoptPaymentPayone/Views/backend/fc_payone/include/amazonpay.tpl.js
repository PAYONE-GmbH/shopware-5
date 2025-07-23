var form = $('#amazonpayform');
var amazonpaydownloadurl = "{url controller=MoptPayoneAmazonPay action=downloadConfigs forceSecure}";
var amazonpaysaveurl = "{url controller=MoptPayoneAmazonPay action=saveAmazonPayConfigs forceSecure}";

form.on("submit", function (event) {
    event.preventDefault();
    amazonpayvalues = form.serialize();
    var submitAction = $(this.id).context.activeElement.name;
    if (submitAction == 'amazondownloadbtn') {
        var url = amazonpaydownloadurl;
    } else {
        var url = amazonpaysaveurl;

    }
    $.post(url, amazonpayvalues, function (response) {
        var data_array = $.parseJSON(response);
        $('#amazonpaytable tr').css('background-color', '');
        if (data_array.errorElem && data_array.errorElem.length) {
            if (data_array.errorElem.length > 0) {
                data_array.errorElem.forEach(markDownloadErrors);
                showalert('{s name = "global/download1"}Das Abrufen von {/s}' + data_array.errorElem.length + '{s name = "global/download2"} Konfigurationen ist fehlgeschlagen{/s}', "alert-danger");
            }

        } else {
            showalert('{s name = "global/saveSuccess"}Die Daten wurden gespeichert{/s}', "alert-success");
            location.reload();
        }
    });
});

function removeRow(rowId) {
    $('#row' + rowId).remove();
};

function markDownloadErrors(item, index) {
    var d = document.getElementById('row' + item);
    d.style.backgroundColor = "red";
}

function addRow() {
    var len = $('#amazonpaytable tbody tr').length;
    var newRow = '' +
        '<tr id="row' + len + '">' +
        '<td><input name="row[' + len + '][id]" id="id_' + len + '" type="text" style="max-width:125px;" class="form-control" value="" readonly="readonly"></td>' +
        '<td><input name="row[' + len + '][clientId]" id="amazonpayClientId_' + len + '"  type="text" style="max-width:125px;" class="form-control" value="" readonly="readonly" ></td>' +
        '<td><input name="row[' + len + '][sellerId]" id="amazonpaySellerId_' + len + '" type="text" style="max-width:125px;" class="form-control" value="" readonly="readonly"></td>' +
        '<td>' +
        '<select name="row[' + len + '][buttonType]" id="amazonpayButtonType_' + len + '" class="form-control">' +
        '<option value="PwA" selected="selected">Amazon Pay (Default): Typical Amazon Pay button</option>' +
        '<option value="Pay">Pay: A slightly smaller Pay button</option>' +
        '<option value="A">A: A small button with only the Amazon Pay Logo</option>' +
        '</select>' +
        '</td>' +
        '<td>' +
        '<select name="row[' + len + '][buttonColor]" id="amazonpayButtonColor_' + len + '" class="form-control" >' +
        '<option value="Gold" selected="selected">Gold (default)</option>' +
        '<option value="LightGray">Light gray</option>' +
        '<option value="DarkGray">Dark gray</option>' +
        '</select>' +
        '</td>' +
        '<td>' +
        '<select name="row[' + len + '][amazonMode]" id="amazonpayAmazonMode_' + len + '" class="form-control" >' +
        '<option value="sync" selected="selected">Always Synchronous</option>' +
        '<option value="firstsync">First synchronous, on failure try asynchronous (recommended, default):</option>' +
        '</select>' +
        '</td>' +
        '<td>' +
        '<select name="row[' + len + '][packStationMode]" id="amazonpaypackStationMode_' + len + '" class="form-control">' +
        '<option value="allow">Allow</option>' +
        '<option value="deny" selected="selected">Deny</option>' +
        '</select>' +
        '</td>' +
        '<td>' +
        '<select name="row[' + len + '][shopId]" id="shop_' + len +'" class="form-control" >' +
        {foreach from=$shops item=shop}
            '<option value="{$shop->getId()}">{$shop->getName()}</option>' +
        {/foreach}
        '</select>' +
        '</td>' +
        '<td role="button" name="delete" value="delete" onclick="removeRow(' + len + ');"><img id="delete_' + len + '" height="100%" src="{link file="backend/_resources/images/delete.png"}"></td>' +
        '</tr>';
    $('#amazonpaytable > tbody:last-child').append(newRow);
}