//
// To compress this script, use https://jscompress.com
//
jQuery( document ).ready( function ( $ ) {

    /**
     * Initiate Date pickers
     */
    $( '.we-ls-datepicker, .we-ls-datepicker-plain' ).each( function() {

        let is_dob_field    = $( this ).hasClass( 'ws-ls-dob-field' );
        let year_range      = ( true === is_dob_field ) ? '-100:-1' : '-100:+0';

        let options = {
            changeMonth         : true,
            changeYear          : true,
            yearRange           : year_range,
            dateFormat          : ws_ls_config[ 'date-format' ],
            showButtonPanel     : true,
            closeText           : ws_ls_config[ 'date-picker-locale' ][ 'closeText' ],
            currentText         : ws_ls_config[ 'date-picker-locale' ][ 'currentText' ],
            monthNames          : ws_ls_config[ 'date-picker-locale' ][ 'monthNames' ],
            monthNamesShort     : ws_ls_config[ 'date-picker-locale' ][ 'monthNamesShort' ],
            dayNames            : ws_ls_config[ 'date-picker-locale' ][ 'dayNames' ],
            dayNamesShort       : ws_ls_config[ 'date-picker-locale' ][ 'dayNamesShort' ],
            dayNamesMin         : ws_ls_config[ 'date-picker-locale' ][ 'dayNamesMin' ],
            firstDay            : ws_ls_config[ 'date-picker-locale' ][ 'firstDay' ]
        };

        // Default the date to something that isn't this year for DoB
        if( true === is_dob_field ) {
            options[ 'defaultDate' ] = new Date( 90, 0, 1 );
        }

        $( this ).datepicker( options );
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
                "ws-ls-weight-pounds": ws_ls_config["validation-we-ls-weight-pounds"],
                "ws-ls-weight-kg": ws_ls_config["validation-we-ls-weight-kg"],
                "ws-ls-weight-stones": ws_ls_config["validation-we-ls-weight-stones"]
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
        }

        // Set up numeric fields to validate
        if("pounds_only" == $weight_unit)
        {
            $( "#" + $form_id + " .ws-ls-weight-pounds").rules( "add", {
                required: ! $target_form,
                number: true,
                range: [0, 5000]
            });
        } else if("stones_pounds" == $weight_unit) {
            $( "#" + $form_id + " .ws-ls-weight-stones").rules( "add", {
                required: ! $target_form,
                number: true,
                range: [0, 5000] // Stupid high in case not tracking human weight!
            });
            $( "#" + $form_id + " .ws-ls-weight-pounds").rules( "add", {
                required: ! $target_form,
                number: true,
                range: [0, 14]
            });
        } else {
            $( "#" + $form_id + " .ws-ls-weight-kg").rules( "add", {
                required: ! $target_form,
                number: true,
                range: [0, 5000] // Stupid high in case not tracking human weight!
            });
        }
    });

  // $( '.ws-ls-cancel-form' ).click(function( event ) {
  //   event.preventDefault();
  //
  //   var button = $(this);
  //   var form_id = button.data('form-id');
  //
  //   if ( undefined !== form_id ) {
  //
  //     var redirect_url = $('#' + form_id + ' #redirect-url').val();
  //
  //     if ( undefined !== redirect_url ) {
  //       window.location.href = redirect_url.replace('ws-edit-saved', 'ws-edit-cancel');
  //     }
  //
  //   }
  //
  // });

  /**
   * Handle Cancel button weight entry forms
   */
  $( '.we-ls-weight-form' ).on( 'click', '.ws-ls-cancel-form', function( event ) {

    event.preventDefault();

    let button  = $( this );
    let form_id = button.data( 'form-id' );

    if ( undefined === form_id ) {
      return;
    }

    let redirect_url = $('#' + form_id + ' #redirect-url').val();

    if ( undefined === redirect_url ) {
      return;
    }

    window.location.href = redirect_url.replace( 'ws-edit-saved', 'ws-edit-cancel' );
  });

});
