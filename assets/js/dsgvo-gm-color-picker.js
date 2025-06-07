jQuery(function ($) {
    // init color pickers
    $('.wp-color-picker-field').wpColorPicker();
    // toggle custom color fields
    $('#dsgvo_gm_template').on('change', function () {
        $('#dsgvo_gm_custom_colors').toggle(this.value === 'custom');
    });
});
