<script type="text/javascript">
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover();
    $('.table').on('all.bs.table', function (e, name, args) {
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover();
    });
});

function idDetailsFormatter(value) {
    return '<a data-placement="left" data-toggle="popover" data-trigger="focus" href="#" data-content="' + value + '" title="Details" data-html="true" class="">{s name="details/title"}Details{/s}</a>';
}

function DateFormatter(value) {
    return value.substr(0, 16);
}

function modeDetailsFormatter(value) {
    if (value === false) {
        return 'Test';
    } else {
        return 'Live';
    }
}
</script>