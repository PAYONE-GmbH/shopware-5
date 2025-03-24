{extends file="parent:backend/_base/layout.tpl"}
{namespace name=backend/mopt_config_payone/main}
{block name="content/main"}
    <div class="col-md-12">
        <h3>{s name="global-form/fieldset2"}Wallet Einstellungen{/s}</h3>
        <div>
            {s name="fieldlabelhelp/walletInfotext"}
            Stellen Sie hier die Konfiguration zu den Zahlarten Paypal, Paypal Express und Amazonpay ein.
            {/s}
        </div>
        {include file='backend/fc_payone/include/dropdown_payments.tpl'}
        <div class='col-md-12'>
            <form role="form" id="ajaxwalletform" class="form-horizontal">
                {include file='backend/fc_payone/include/input_text.tpl' id='description' label="{s name="formpanel_description_label"}Bezeichnung-Id{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' }
                {include file='backend/fc_payone/include/input_text.tpl' id='additionalDescription' label="{s name="formpanel_additional-description_label"}Zusätzliche Beschreibung{/s}" pattern='^[_ .\(\)+-\?,:;"!@#$%\^&\*ÄÖÜäöüa-zA-Z0-9]*' }
                {include file='backend/fc_payone/include/input_text.tpl' id='debitPercent' label="{s name="formpanel_surcharge_label"}Aufschlag/Abschlag (in %){/s}" pattern='^[,.0-9]*'}
                {include file='backend/fc_payone/include/input_text.tpl' id='surcharge' label="{s name="formpanel_generalSurcharge_label"}Pauschaler Aufschlag{/s}" pattern='^[,.0-9]*'}
                {include file='backend/fc_payone/include/input_text.tpl' id='position' label="{s name="formpanel_position_surcharge"}Position{/s}" pattern='^[0-9]*'}
                {include file='backend/fc_payone/include/input_checkbox.tpl' id='active' label="{s name="formpanel_active_label"}Aktiv{/s}" pattern="^[0-9]*"}
                {include file='backend/fc_payone/include/input_checkbox.tpl' id='esdActive' label="{s name="formpanel_esdActive_label"}Aktiv für ESD-Produkte{/s}" pattern="^[0-9]*"}
                {include file='backend/fc_payone/include/input_checkbox.tpl' id='mobileInactive' label="{s name="formpanel_mobileInactive_label"}Inaktiv für Smartphone{/s}" pattern="^[0-9]*"}
                {include file='backend/fc_payone/include/dropdown_yesno.tpl' id='paypalEcsActive' label="{s name="fieldlabel/paypalEcsActive"}PayPal ECS Button auf Warenkorbseite anzeigen?{/s}" pattern="^[0-9]*"}
                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
            </form>
        </div>
       
        <a style="font-size: 28px" href="#"  data-bs-target="#payonetable">{s name="global-form/paypalecslogos"}Konfiguration PayPal Express Logos{/s}</a>
        <div id="payonetable">
            <form role="form" id="ajaxpaypalecs" enctype="multipart/form-data">
                    <table class="table-condensed" id="paypalecstable">
                        <tr>
                            <th>{s name="fieldlabel/id"}ID{/s}</th>
                            <th>{s name="fieldlabel/shop"}Shop{/s}</th>
                            <th>{s name="fieldlabel/logo"}Logo{/s}</th>
                            <th>{s name="fieldlabel/upload"}Hochladen{/s}</th>
                        </tr>
                        {foreach from=$paypalconfigs key=mykey item=paypalconfig}
                        <tr id="row{$paypalconfig->getId()}">
                            <td><input name="row[{$paypalconfig->getId()}][id]" id="id_{$paypalconfig->getId()}" type="text" style="max-width:125px;" class="form-control" value="{$paypalconfig->getId()}" readonly="readonly" ></td>
                            <td><select class="form-control" name="row[{$paypalconfig->getId()}][shop]" id="shop_{$paypalconfig->getId()}">
                                    {foreach from=$shops item=shop}
                                        <option value="{$shop->getId()}" {if $shop->getId() == $paypalconfig->getShop()->getId()} selected="selected"{/if}>{$shop->getName()}</option>
                                    {/foreach}
                                </select>
                            </td>
                            <td>
                                <input name="row[{$paypalconfig->getId()}][image]" id="image_{$paypalconfig->getId()}" value="{$paypalconfig->getImage()}" hidden>
                                <input name="row[{$paypalconfig->getId()}][filename]" id="filename_{$paypalconfig->getId()}" value="" hidden>
                                <output id="list{$paypalconfig->getId()}"></output>
                            </td>
                            <td><input type="file" id="files{$paypalconfig->getId()}" name="files"></td>
                            <td role="button" name="delete" value="delete" onclick="removeRow({$paypalconfig->getId()})"><img id="delete_{$paypalconfig->getId()}" height="100%" src="{link file='backend/_resources/images/delete.png'}"></td>
                        {/foreach}


                   {* <!--     <tr id="row0">
                            <td><input name="row[0][id]" id="id_0" type="text" style="max-width:125px;" class="form-control" value="0" readonly="readonly" ></td>
                            <td><select class="form-control" name="row[0][shop]" id="shop_0">
                                    {foreach from=$shops item=shop}
                                        <option value="{$shop->getId()}" {if $shop->getId() == $paypalconfig->getShop()->getId()} selected="selected"{/if}>{$shop->getName()}</option>
                                    {/foreach}
                                </select>
                            </td>
                            <td>
                                <input name="row[0][image]" id="image_{$paypalconfig->getId()}" value="{$paypalconfig->getImage()}" hidden>
                                <input name="row[0][filename]" id="filename_{$paypalconfig->getId()}" value="" hidden>
                                <output id="list0"></output>
                            </td>
                            <td><input type="file" id="files0" name="files"></td>
                            <td role="button" name="delete" value="delete" onclick="removeRow(0)"><img id="delete_0" height="100%" src="{link file='backend/_resources/images/delete.png'}"></td>
--> *}

                        <tr><td><img id="newRow" onclick="addRow()" src="{link file='backend/_resources/images/add.png'}"></td></tr>
                    </table>
                <button type="submit" class="btn-payone btn " >{s name="global-form/button"}Speichern{/s}</button>
            </form>                
        </div>
{/block}

{block name="resources/javascript" append}
    <script type="text/javascript">

        var form = $('#ajaxwalletform');
        var iframeform = $('#ajaxpaypalecs');
        var amazonpayform = $('#ajaxamazonpay');
        var url = "{url controller=FcPayone action=ajaxgetWalletConfig forceSecure}";
        var iframeurl = "{url controller=FcPayone action=ajaxgetPaypalConfig forceSecure}";
        var amazonpaydownloadurl = "{url controller=MoptPayoneAmazonPay action=downloadConfigs forceSecure}";
        var amazonpaysaveurl = "{url controller=MoptPayoneAmazonPay action=saveAmazonPayConfigs forceSecure}";
        var paymentid = null;
        var imagelink = null;

        $(document).ready(function ()
        {
            var params = "paymentid=0";
            var call = url + '?' + params;
            var iframecall = iframeurl;
            var iframedata = "";

            form.validator('validate');
            alert('after validate');

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

                    if(response.iframedata) {
                        var arrayLength = response.iframedata.length;
                        for (var i = 0; i < arrayLength; i++) {
                            imagelink = response.iframedata[i].image;
                            console.log("Image Link" + i);
                            console.log(imagelink);
                            changeImage(response.iframedata[i].id, imagelink);
                        }
                    }
                }
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
            var url = 'ajaxSavePaypalConfig';
            iframevalues = iframevalues + '&paymentId=' + paymentid;
            $.post(url, iframevalues, function (response) {
                var data_array = $.parseJSON(response);
                showalert("Die Daten wurden gespeichert", "alert-success");
                location.reload();
            });

        });
        function handleFileSelect(evt) {
            console.log("In handleFileselect");
            console.log(evt);
            var files = evt.target.files;
            var id = evt.currentTarget.id.toString().replace('files', '');
            id = String(id);
            console.log(id);
            // Loop through the FileList and render image files as thumbnails.
            for (var i = 0, f; f = files[i]; i++) {

                // Only process image files.
                if (!f.type.match('image.*')) {
                    continue;
                }

                var reader = new FileReader();

                // Closure to capture the file information.
                reader.onload = (function (theFile, myid) {
                    return function (e) {
                        console.log('THEFILE');
                        console.log(theFile);
                        console.log(theFile.name.toString());
                        console.log("returnfunc:" + myid)
                        var out = ['<img width=150px class="thumb" src="', e.target.result,
                            '" />'].join('');
                        $("#list"+ myid).html(out);
                        $("#image_"+ myid).val(e.target.result);
                        $("#filename_" + myid).attr('value',theFile.name.toString());
                    };
                })(f, id);

                // Read in the image file as a data URL.
                reader.readAsDataURL(f);
            }
        }

        function changeImage(index, url) {
            console.log("In ChangeImage");
            console.log(url);
            var out = ['<img width=150px class="thumb" src="', url,
                '" />'].join('');
            $("#list" + index).html(out);
        }

        //$("#payonetable").delegate("input[type=file]", "change", function() {
        //    alert($(this).attr("id"));
        //});
        var fileInputs = document.getElementsByName('files');
        console.log('fileInputs:');
        console.log(fileInputs);
        for(let i = 0;i < fileInputs.length; i++)
        {
            fileInputs[i].addEventListener('change', handleFileSelect, false);
        }

        function clear_form_elements(ele) {

            console.log("clear triggered");

            $(ele).find(':input').each(function() {
                switch(this.type) {
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
            var len = $('#payonetable tbody tr').length -1;
            console.log('Length');
            console.log(len);

            var newRow = "" +
                "<tr id='row" + len + "'>" +
                    "<td>" +
                        "<input name='row[" + len + "][id]' id='id_" + len + "' type='text' style='max-width:125px;' class='form-control' value='' readonly='readonly' >" +
                    "</td>" +
                    "<td>" +
                    "<select class='form-control' name='row[" + len + "][shop]' id='shop_" + len + "'>" +
                        "{foreach from=$shops item=shop} <option value='{$shop->getId()}'>{$shop->getName()}</option>{/foreach}" +
                    "</select>" +
                    "</td>" +
                    "<td>" +
                        "<input name='row[" + len + "][image]' id='image_" + len + "' value='" + len + "' hidden>" +
                        "<input name='row[" + len + "][filename]' id='filename_" + len + "' value='' hidden>"+
                        "<output id='list" + len + "'></output>" +
                    "</td>" +
                    "<td>" +
                        "<input type='file' id='files" + len + "' name='files'>" +
                    "</td>" +
                    "<td role='button' name='delete' value='delete' onclick='removeRow(" + len + ")'>" +
                        "<img id='delete_'" + len + "' height='100%' src='{link file='backend/_resources/images/delete.png'}'></td>";
            $('#paypalecstable > tbody:last-child').append(newRow);

            // register event handler after adding
            var fileInputs = document.getElementsByName('files');
            console.log('fileInputs:');
            console.log(fileInputs);
            for(let i = 0;i < fileInputs.length; i++)
            {
                fileInputs[i].addEventListener('change', handleFileSelect, false);
            }
        }
    </script>
{/block}
