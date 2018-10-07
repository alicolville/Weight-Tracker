//
// To compress this script, use https://jscompress.com
//
jQuery( document ).ready(function ($) {

    $(".we-ls-datepicker, .we-ls-datepicker-plain").each(function() {
        var options = {
            changeMonth: true,
            changeYear: true,
            yearRange: "-100:+0",
            showButtonPanel: true,
            dateFormat: ws_ls_config["date-format"],
            showButtonPanel: true,
            closeText: ws_ls_config["date-picker-locale"]["closeText"],
            currentText: ws_ls_config["date-picker-locale"]["currentText"],
            monthNames: ws_ls_config["date-picker-locale"]["monthNames"],
            monthNamesShort: ws_ls_config["date-picker-locale"]["monthNamesShort"],
            dayNames: ws_ls_config["date-picker-locale"]["dayNames"],
            dayNamesShort: ws_ls_config["date-picker-locale"]["dayNamesShort"],
            dayNamesMin: ws_ls_config["date-picker-locale"]["dayNamesMin"],
            firstDay: ws_ls_config["date-picker-locale"]["firstDay"]
        };

        $(this).datepicker(options);
    });

    $.validator.addMethod('filesize', function(value, element, param) {
        return this.optional(element) || (element.files[0].size <= param)
    });

    $( '.ws-ls-photo-field-delete' ).change( function() {

        // If deleting the existing photo, and we have a required field, then add the "required" field
        // back onto the input select field
        if ( 'y' === $( this ).data('required') ) {

            var field_id = '#' + $( this ).data('field-id');

            if ( true === $( this ).is(':checked') ) {
                $( field_id ).attr('required', 'required');
            } else {
                $( field_id ).removeAttr('required');
                $( field_id ).removeClass('ws-ls-invalid');
            }
        }
    });

    // Form Validation
    $(".we-ls-weight-form-validate").each(function () {

        $form_id = $(this).attr("id");
        $target_form = $(this).data("is-target-form");
        $weight_unit = $(this).data("metric-unit");

        console.log("Adding form validation to: " + $form_id + ". Target form? " + $target_form + ". Weight Unit: " + $weight_unit);

        // Add form validation
        $( "#" + $form_id ).validate({
            errorContainer: "#" + $form_id + " .ws-ls-error-summary",
            errorLabelContainer: "#" + $form_id + " .ws-ls-error-summary ul",
            wrapper: "li",
            ignore: [],
            errorClass: "ws-ls-invalid",
            validClass: "ws-ls-valid",
            messages: {
                "we-ls-date": ws_ls_config["validation-we-ls-date"],
                "we-ls-weight-pounds": ws_ls_config["validation-we-ls-weight-pounds"],
                "we-ls-weight-kg": ws_ls_config["validation-we-ls-weight-kg"],
                "we-ls-weight-stones": ws_ls_config["validation-we-ls-weight-stones"],
                "we-ls-measurements": ws_ls_config["validation-we-ls-measurements"]
            },
            submitHandler: function(form) {
                $( '.ws-ls-remove-on-submit' ).remove();
                $( '.ws-ls-form-processing-throbber' ).removeClass('ws-ls-hide');
                form.submit();
            }
        });

        // Non Target form specific fields
        if (!$target_form) {
            //If a datepicker is on this form
            if ($("#" + $form_id + " .we-ls-datepicker").length) {
                // Validate date
                if ("true" == ws_ls_config["us-date"]) {
                    $( "#" + $form_id + " .we-ls-datepicker" ).rules( "add", {
                        required: true,
                        date: true
                    });
                }
                else {
                    $( "#" + $form_id + " .we-ls-datepicker" ).rules( "add", {
                        required: true,
                        dateITA: true
                    });
                }
            }

            // Measurement form
            if ("true" == ws_ls_config["measurements-enabled"] && true == $("#" + $form_id).data("measurements-enabled")) {
                $( "#" + $form_id + " .ws-ls-measurement").rules( "add", {
                    number: true,
                    range: [1, 1000],
                    messages: {
                        number: ws_ls_config["validation-we-ls-measurements"],
                        range: ws_ls_config["validation-we-ls-measurements"]
                    }
                });
            }

        }

        // Set up numeric fields to validate
        if("imperial-pounds" == $weight_unit)
        {
            $( "#" + $form_id + " #we-ls-weight-pounds").rules( "add", {
                required: true,
                number: true,
                range: [0, 5000]
            });
        }
        if("imperial-both" == $weight_unit)
        {
            $( "#" + $form_id + " #we-ls-weight-stones").rules( "add", {
                required: true,
                number: true,
                range: [0, 5000] // Stupid high in case not tracking human weight!
            });
            $( "#" + $form_id + " #we-ls-weight-pounds").rules( "add", {
                required: true,
                number: true,
                range: [0, 14]
            });
        }
        if("metric" == $weight_unit)
        {
            $( "#" + $form_id + " #we-ls-weight-kg").rules( "add", {
                required: true,
                number: true,
                range: [0, 50000] // Stupid high in case not tracking human weight!
            });
        }
    });
});
