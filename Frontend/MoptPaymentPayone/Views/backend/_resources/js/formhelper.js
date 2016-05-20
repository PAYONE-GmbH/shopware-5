  function showalert(message,alerttype) {

    $('#alert_placeholder_top').append('<div id="alertdiv" class="alert ' +  alerttype + '"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')

    setTimeout(function() { // this will automatically close the alert and remove this if the users doesnt close it in 5 secs
      $("#alertdiv").remove();

    }, 5000);
    $('#alert_placeholder_bottom').append('<div id="alertdiv" class="alert ' +  alerttype + '"><a class="close" data-dismiss="alert">×</a><span>'+message+'</span></div>')

    setTimeout(function() { // this will automatically close the alert and remove this if the users doesnt close it in 5 secs
      $("#alertdiv").remove();
    }, 5000);    
  }

function resetForm($form)
{
    $form.find('input:text, input:password, input:file, select, textarea').val('');
    $form.find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
}

function populateForm($form, data)
{
    console.log("PopulateForm, All form data: " + JSON.stringify(data));

    $.each(data, function (key, value)   // all json fields ordered by name
    {

        var $ctrls = $form.find('[name=' + key + ']');  //all form elements for a name. Multiple checkboxes can have the same name, but different values

        if ($ctrls.is('select'))
        {
            $('option', $ctrls).each(function () {
                 if (this.value.toString() === value.toString()) {
                    this.selected = true;
                }
            });
        }
        else if ($ctrls.is('textarea'))
        {
            $ctrls.val(value);
        }
        else
        {
            switch ($ctrls.attr("type"))   //input type
            {
                case "text":
                case "hidden":
                    $ctrls.val(value);
                    break;
                case "radio":
                    if ($ctrls.length >= 1)
                    {
                        $.each($ctrls, function (index)
                        {  // every individual element
                            var elemValue = $(this).attr("value");
                            var elemValueInData = singleVal = value;
                            if (elemValue === value) {
                                $(this).prop('checked', true);
                            }
                            else {
                                $(this).prop('checked', false);
                            }
                        });
                    }
                    break;
                case "checkbox":
                    $.each($ctrls, function (index)
                    {  // every individual element
                        $ctrl = $ctrls;
                        if (value.toString() === "true") {
                            $ctrl.prop('checked', true);
                        }

                        else {
                            $ctrl.prop('checked', false);
                        }
                    });
                    break;
            }  //switch input type
        }
    }) // all json fields
}  // populate form