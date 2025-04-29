<script type="text/javascript">
var form = $('#amazonpayform');
console.log('Form:');
console.log(form);
var url = "{url controller=FcPayone action=amazonpay forceSecure}";
var amazonpaydownloadurl = "{url controller=MoptPayoneAmazonPay action=downloadConfigs forceSecure}";
var amazonpaysaveurl = "{url controller=MoptPayoneAmazonPay action=saveAmazonPayConfigs forceSecure}";

$(document).ready(function () {
    var call = url + '?';
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
})
;

form.on("submit", function (event) {
    event.preventDefault();
    amazonpayvalues = form.serialize();
    var submitAction = $(this.id).context.activeElement.name;
    console.log('ButtonAction:');
    console.log(submitAction);
    if (submitAction == 'amazondownloadbtn') {
        var url = amazonpaydownloadurl;
    } else {
        var url = amazonpaysaveurl;

    }
    $.post(url, amazonpayvalues, function (response) {
        var data_array = $.parseJSON(response);
        console.log('amazonpay Data:');
        console.log(data_array);
        $('#amazonpaytable tr').css('background-color', '');
        if (data_array.errorElem && data_array.errorElem.length) {
            console.log('amazonpayError');
            if (data_array.errorElem.length > 0) {
                data_array.errorElem.forEach(markDownloadErrors);
                showalert("Das Abrufen von " + data_array.errorElem.length + " Konfigurationen ist fehlgeschlagen", "alert-danger");
            }
        } else {
            console.log('amazonpayOK');
            showalert("Die Daten wurden gespeichert", "alert-success");
            location.reload();
        }
    });
})
;

function clear_form_elements(ele) {

    console.log("clear triggered");

    $(ele).find(':input').each(function () {
        switch (this.type) {
            case 'select-multiple':
            case 'select-one':
                $(this).val('');
                break;
        }
    });
}

function removeRow(rowId) {
    $('#row' + rowId).remove();
}

function addRow() {
    var len = $('#amazonpaytable tbody tr').length - 1;
    console.log('Length');
    console.log(len);

    var newRow = '' +
        '<tr id="row1">' +
        '<td><input name="row[1][id]" id="id_1" type="text" style="max-width:125px;" class="form-control" value="1" readonly="readonly"></td>' +
        '<td><input name="row[1][clientId]" id="amazonpayClientId_1" type="text" style="max-width:125px;" class="form-control" value="amzn1.application-oa2-client.718254e0e9a4492f8c8efe39d2b3d187" readonly="readonly"></td>' +
        '<td><input name="row[1][sellerId]" id="amazonpaySellerId_1" type="text" style="max-width:125px;" class="form-control" value="A1GNVABPMV8MYX" readonly="readonly"></td>' +
        '<td>' +
        '<select class="form-control" name="row[1][buttonType]" id="amazonpayButtonType_1">' +
        '<option value="PwA" selected="selected">Amazon Pay (Default): Typical "Amazon Pay" button</option>' +
        '<option value="Pay">Pay: A slightly smaller "Pay" button</option>' +
        '<option value="A">A: A small button with only the Amazon Pay Logo</option>' +
        '</select>' +
        '</td>' +
        '<td>' +
        '<select class="form-control" name="row[1][buttonColor]" id="amazonpayButtonColor_1">' +
        '<option value="Gold" selected="selected">Gold (default)</option>' +
        '<option value="LightGray">Light gray</option>' +
        '<option value="DarkGray">Dark gray</option>' +
        '</select>' +
        '</td>' +
        '<td>' +
        '<select class="form-control" name="row[1][amazonMode]" id="amazonpayAmazonMode_1">' +
        '<option value="sync" selected="selected">Always Synchronous</option>' +
        '<option value="firstsync">First synchronous, on failure try asynchronous (recommended, default):</option>' +
        '</select>' +
        '</td>' +
        '<td>' +
        '<select class="form-control" name="row[1][packStationMode]" id="amazonpaypackStationMode_1">' +
        '<option value="allow">Allow</option>' +
        '<option value="deny" selected="selected">Deny</option>' +
        '</select>' +
        '</td>' +
        '<td></td></tr>';
    $('#amazonpaytable > tbody:last-child').append(newRow);
}
</script>