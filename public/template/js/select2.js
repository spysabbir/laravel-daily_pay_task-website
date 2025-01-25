$(function () {
    'use strict';

    // Set default dropdownParent globally
    $.fn.select2.defaults.set("dropdownParent", $(document.body));

    // Initialize Select2 for single select box
    if ($(".js-select2-single").length) {
        $(".js-select2-single").select2({
            dropdownParent: $('.select2Model'), // Specify the modal or parent dynamically
        });
    }

    // Initialize Select2 for multiple select box
    if ($(".js-select2-multiple").length) {
        $(".js-select2-multiple").select2({
            dropdownParent: $('.select2Model'), // Specify the modal or parent dynamically
            allowClear: true,
        });
    }
});
