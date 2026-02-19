var form = $('{$form}');
var loadUrl = "{url controller=FcPayone action={$loadAction} forceSecure}";
var saveUrl = "{url controller=FcPayone action={$saveAction} forceSecure}";
var paymentid = 0;

$(document).ready(function () {
    var params = "paymentid=0";
    var call = loadUrl + '?' + params;
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
        }
    });
    $(function () {
        $('[data-toggle="popover"]').popover()
    });
});

$(".dropdown-menu li a").click(function () {
    var params = "paymentid=" + this.id;
    paymentid = this.id;
    var call = loadUrl + '?' + params;

    $.ajax({
        url: call,
        type: 'POST',
        success: function (data) {
            response = $.parseJSON(data);
            if (response.status === 'success') {
                if (typeof(response.paymentName) !== undefined && response.paymentName === 'mopt_payone__ewallet_click2pay' && '{$form}' === '#generalconfigform') {
                    $('#liveMode').attr('disabled', 'disabled');
                }
                populateForm(form, response.data);
                form.validator('validate');
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
    values = values + '&paymentId=' + paymentid;
    $.post(saveUrl, values, function (response) {
        var data_array = $.parseJSON(response);
        showalert('{s name = "global/saveSuccess"}Die Daten wurden gespeichert{/s}', "alert-success");
    });
});

$("#applecertupload").click(function () {
    var fd = new FormData();
    var files = $('#applepayCertificateFile')[0].files;

// Check file selected or not
    if (files.length > 0) {
        fd.append('file', files[0]);

        $.ajax({
            url: 'ajaxsaveApplepayCert',
            type: 'post',
            data: fd,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response != 0) {
                    $('#applepayCertificate').val(response);
                    form.submit();
                    showalert('{s name = "global/saveSuccess"}Die Daten wurden gespeichert{/s}', "alert-success");
                } else {
                    showalert('{s name = "global/certsaveerror"}Fehler beim Speichern des Zertifikats{/s}', "alert-error");
                }
            },
        });
    } else {
        showalert('{s name = "global/certsaveerror"}Bitte eine Zertifikats Datei .pem auswählen{/s}', "alert-error");
    }
});

$("#applekeyupload").click(function () {
    var fd = new FormData();
    var files = $('#applepayKeyFile')[0].files;

// Check file selected or not
    if (files.length > 0) {
        fd.append('file', files[0]);

        $.ajax({
            url: 'ajaxsaveApplepayKey',
            type: 'post',
            data: fd,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response != 0) {
                    $('#applepayPrivateKey').val(response);
                    form.submit();
                    showalert('{s name = "global/saveSuccess"}Die Daten wurden gespeichert{/s}', "alert-success");
                } else {
                    showalert('{s name = "global/keysaveerror"}Fehler beim Speichern des Schlüssels{/s}', "alert-error");
                }
            },
        });
    } else {
        showalert('{s name = "global/keychoose"}Bitte eine Key Datei .key auswählen{/s}', "alert-error");
    }
});