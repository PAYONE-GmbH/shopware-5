function {$id}DobInput()
{
    var daySelect = document.getElementById("{$id}_birthdayday");
    var monthSelect = document.getElementById("{$id}_birthdaymonth");
    var yearSelect = document.getElementById("{$id}_birthdayyear");
    var hiddenDobFull = document.getElementById("{$id}_birthdaydate");
    var hiddenDobHint = document.getElementById("{$id}-hint-18-years");

    if (daySelect.value == "" || monthSelect.value == "" || yearSelect.value == ""
        || hiddenDobFull.value == "" || daySelect == undefined) {
        return;
    }
    hiddenDobFull.value = yearSelect.value + "-" + monthSelect.value + "-" + daySelect.value;
    var oBirthDate = new Date(hiddenDobFull.value);
    var oMinDate = new Date(new Date().setYear(new Date().getFullYear() - 18));
    if (oBirthDate > oMinDate) {
        hiddenDobHint.className = "register--error-msg";
    } else {
        hiddenDobHint.className = "is--hidden";
        return;
    }
}