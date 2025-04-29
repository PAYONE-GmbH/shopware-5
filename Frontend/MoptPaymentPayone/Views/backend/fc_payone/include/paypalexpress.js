<script type="text/javascript">
    var form = $('#ajaxcreditcardform');
    var iframeform = $('#ajaxiframeconfigform');
    var url = "{url controller=FcPayone action=ajaxgetCreditCardConfig forceSecure}";
    var iframeurl = "{url controller=FcPayone action=ajaxgetIframeConfig forceSecure}";
    var paymentid = null;

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

        $.ajax({
        url: iframecall,
        type: 'POST',
        success: function (iframedata) {
        response = $.parseJSON(iframedata);
        if (response.status === 'success') {
        populateForm(iframeform, response.iframedata);
    }
        if (response.status === 'error') {
    }
    }
    });
    })
;

    $(".dropdown-menu li a
").click(function () {
    var params = "paymentid=" + this.id;
    var call = url + '?' + params;
    var data = "";
    paymentid = this.id;

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
    alert("success");
}
}
});
})
;

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
})
;

    iframeform.on("submit", function (event) {
    event.preventDefault();
    var checkboxes = iframeform.find('input[type="checkbox"]');
    $.each(checkboxes, function (key, value) {
    if (value.checked === false) {
    value.value = 0;
} else {
    value.value = 1;
}
    $(value).attr('type', 'hidden');
});
    iframevalues = iframeform.serialize();
    $.each(checkboxes, function (key, value) {
    $(value).attr('type', 'checkbox');
});
    var url = 'ajaxSaveIframeConfig';
    iframevalues = iframevalues + '&paymentId=' + paymentid;
    $.post(url, iframevalues, function (response) {
    var data_array = $.parseJSON(response);
    showalert("Die Daten wurden gespeichert", "alert-success");
});
})
;
</script>