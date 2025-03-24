var forms = $('.form-horizontal');

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
    $(function () {
        $('[data-toggle="popover"]').popover()
    });

});

