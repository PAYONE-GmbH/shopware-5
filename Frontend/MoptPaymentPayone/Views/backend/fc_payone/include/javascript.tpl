<script type="text/javascript">
    var form = $('{$form}');
    var url = "{url controller=FcPayone action={$action} forceSecure}";
    var paymentid = 0;

    $(document).ready(function ()
    {
        var params = "paymentid=0";
        var call = url + '?' + params;

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
        $(function () {
            $('[data-toggle="popover"]').popover()
        });
    });

    $(".dropdown-menu li a").click(function () {
        var params = "paymentid=" + this.id;
        var call = url + '?' + params;
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
                    // ToDo
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
        var url = 'ajaxSavePayoneConfig';
        values = values + '&paymentId=' + paymentid;
        $.post(url, values, function (response) {
            var data_array = $.parseJSON(response);
            showalert("Die Daten wurden gespeichert", "alert-success");
        });
    });
    $("#applecertupload").click(function(){
        var fd = new FormData();
        var files = $('#applepayCertificateFile')[0].files;

        // Check file selected or not
        if(files.length > 0 ){
            fd.append('file',files[0]);

            $.ajax({
                url: 'ajaxsaveApplepayCert',
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function(response){
                    if(response != 0){
                        console.log(response);
                        $('#applepayCertificate').val(response);
                        form.submit();
                        showalert("Das Zertifikat wurde gespeichert", "alert-success");
                    }else{
                        showalert("Fehler beim Speichern des Zertifikats", "alert-error");
                    }
                },
            });
        }else{
            showalert("Bitte eine Zertifikats Datei .pem auswählen", "alert-error");
        }
    });

    $("#applekeyupload").click(function(){
        var fd = new FormData();
        var files = $('#applepayKeyFile')[0].files;

        // Check file selected or not
        if(files.length > 0 ){
            fd.append('file',files[0]);

            $.ajax({
                url: 'ajaxsaveApplepayKey',
                type: 'post',
                data: fd,
                contentType: false,
                processData: false,
                success: function(response){
                    if(response != 0){
                        console.log(response);
                        $('#applepayPrivateKey').val(response);
                        form.submit();
                        showalert("Die Schlüsseldatei wurde gespeichert", "alert-success");
                    }else{
                        showalert("Fehler beim Speichern des Schlüssels", "alert-error");
                    }
                },
            });
        }else{
            showalert("Bitte eine Key Datei .key auswählen", "alert-error");
        }
    });
</script>