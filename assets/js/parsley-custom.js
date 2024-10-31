jQuery(document).ready(function($) {
    $("#partnify_settings").parsley();


    function partnify_save_settings(save_settings) {
        var partnify_email = $('.partnify-email').val();
        var partnify_api_key = $('.partnify-api-key').val();
        var save = (save_settings) ? save_settings : false;
        var data = { action: "partnify_save_settings", partnify_email: partnify_email, partnify_api_key: partnify_api_key, save: save };
        $.ajax({
            url: ajaxurl,
            data: data,
            type: "post",
            dataType: "json",
            success: function(data) {

                var status_element = $('.connection-status');

                if (data.status == 'True') {
                    status_element.addClass('connected').removeClass('not-connected');
                } else {
                    status_element.removeClass('connected').addClass('not-connected');

                }
                status_element.html(data.message);
                $('.spinner').css({ 'visibility': 'hidden' });
            }
        });
    }
    // on form submit
    $("#partnify_settings").on('submit', function(event) {
        // validate form with parsley.
        $(this).parsley().validate();

        // if this form is valid
        if ($(this).parsley().isValid()) {
            $('.spinner').css({ 'visibility': 'visible' });
            // Settings Save.

            var save_settings = true;
            partnify_save_settings(save_settings);

        }

        // prevent default so the form doesn't submit. We can return true and
        // the form will be submited or proceed with a ajax request.
        event.preventDefault();
    });

    $(document).on('change', '.partnify-api-key, .partnify-email', function(e) {
        var th = $(this).closest('form');
        th.parsley().validate();

        // if this form is valid
        if (th.parsley().isValid()) {
            $('.spinner').css({ 'visibility': 'visible' });
            partnify_save_settings();

        }
        e.preventDefault();
    });
});