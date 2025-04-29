var forms = $('.form-horizontal');

$(".dropdown-menu li a").click(function () {
    $(this).parents(".btn-group").find('.selection').text($(this).text());
    $(this).parents(".btn-group").find('.selection').val($(this).text());
    forms.validator('validate');
});

$(".payone-menu").click(function () {
    if ($(this).hasClass('open-menu')) {
        $(this).removeClass('open-menu');
    } else {
        $(this).addClass('open-menu');
    }
    var collapseid = this.id.replace(/link-/g, '');
    if (collapseid && !localStorage.getItem(collapseid)) {
        localStorage.setItem(collapseid, 'ausgeklappt');
    } else if (collapseid && localStorage.getItem(collapseid) === 'ausgeklappt') {
        localStorage.setItem(collapseid, 'eingeklappt');
    } else if (collapseid && localStorage.getItem(collapseid) === 'eingeklappt') {
        localStorage.setItem(collapseid, 'ausgeklappt');
    }
});


$(".payone-submenu").click(function () {
    if ($(this).hasClass('open-menu')) {
        $(this).removeClass('open-menu');
    } else {
        $(this).addClass('open-menu');
    }
    var collapseid = this.id.replace(/link-/g, '');
    if (collapseid && !localStorage.getItem(collapseid)) {
        localStorage.setItem(collapseid, 'ausgeklappt');
    } else if (collapseid && localStorage.getItem(collapseid) === 'ausgeklappt') {
        localStorage.setItem(collapseid, 'eingeklappt');
    } else if (collapseid && localStorage.getItem(collapseid) === 'eingeklappt') {
        localStorage.setItem(collapseid, 'ausgeklappt');
    }
});

$(document).ready(function () {
    $('.collapse').each(function () {
        if (this.id && localStorage.getItem(this.id) === 'ausgeklappt') {
            $(this).collapse('show');
        }

    });
});