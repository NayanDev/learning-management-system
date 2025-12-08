$( document ).ajaxStop(function() {
    $('.support-live-select2').each(function () {
        $(this).select2({
            dropdownParent: $(this).parent(),// fix select2 search input focus bug
        })
    })
});