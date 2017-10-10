var standard = $('.menu-level-standard');
var expert = $('.menu-level-experte');
var forms = $('.form-horizontal');

$(".dropdown-menu li a").click(function () {

    $(this).parents(".btn-group").find('.selection').text($(this).text());
    $(this).parents(".btn-group").find('.selection').val($(this).text());
    if ($(this).text() == "Standard") {
        expert.hide();
        standard.show();
        localStorage.setItem('menu-level', 'Standard');
        forms.validator('validate');
    }
    if ($(this).text() == "Experte") {
        expert.show();
        standard.show();
        localStorage.setItem('menu-level', 'Experte');
        forms.validator('validate');
    }
});

var localeId = 1;

$(".dropdown-filter-locale li a").click(function () {
    if ($(this).text() == "Deutsch")
        localeId = 1;
    if ($(this).text() == "Englisch")
        localeId = 2;
    if ($(this).text() == "Niederländisch")
        localeId = 176;
    $('#table').bootstrapTable('refresh');
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

    var standard = $('.menu-level-standard');
    var expert = $('.menu-level-experte');

    if (localStorage.getItem('menu-level') == 'Standard') {
        forms.validator('validate');
        expert.hide();
        standard.show();
        $('#payone-settings-level').find('.selection').html('Standard');
    }
    if (localStorage.getItem('menu-level') == 'Experte') {
        forms.validator('validate');
        expert.show();
        standard.show();
        $('#payone-settings-level').find('.selection').html('Experte');
    }

});

