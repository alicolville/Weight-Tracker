//
// To compress this script, use https://jscompress.com
//
jQuery( document ).ready( function ( $ ) {

    /**
     * Initiate Date pickers
     */
    $( '.we-ls-datepicker, .we-ls-datepicker-plain' ).each( function() {

        let datepicker      = $( this );
        let is_dob_field    = datepicker.hasClass( 'ws-ls-dob-field' );
        let year_range      = ( true === is_dob_field ) ? '-100:-1' : '-100:+0';

        // Challenge field?
        if ( false === is_dob_field && datepicker.hasClass( 'we-ls-challenge-datepicker' ) ) {
          year_range = ( true === is_dob_field ) ? '-100:-1' : '-100:+100';
        }

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
            firstDay            : ws_ls_config[ 'date-picker-locale' ][ 'firstDay' ],
            onSelect            : function( date ) {

                if ( '1' !== ws_ls_config[ 'form-load-previous' ] ) {
                  return;
                }

              ws_ls_entry_post_data( 'ws_ls_get_entry_for_date',
                            { 'user-id' : ws_ls_config[ 'user-id' ], 'date' : date },
                            function( data, response ) {

                                // Was an entry found for this user/date?
                                if ( null !== response ) {

                                  if ( true !== confirm( ws_ls_config[ 'date-picker-locale' ][ 'entry-found' ] ) ) {
                                    return;
                                  }

                                  window.location.href = ws_ls_config[ 'current-url' ] + '?load-entry=' + response[ 'id' ];
                                }
                            });
                }
        };

        // Default the date to something that isn't this year for DoB
        if( true === is_dob_field ) {
            options[ 'defaultDate' ] = new Date( 90, 0, 1 );
        }

        $( this ).datepicker( options );
    });

    $.validator.addMethod( 'filesize', function( value, element, param ) {
        return this.optional( element ) || ( element.files[0].size <= param )
    });

    $( '.ws-ls-photo-field-delete' ).change( function() {

        // If deleting the existing photo, and we have a required field, then add the "required" field
        // back onto the input select field
        if ( 'y' === $( this ).data( 'required' ) ) {

            var field_id = '#' + $( this ).data( 'field-id' );

            if ( true === $( this ).is( ':checked' ) ) {
                $( field_id ).attr( 'required', 'required' );
            } else {
                $( field_id ).removeAttr( 'required' );
                $( field_id ).removeClass( 'ws-ls-invalid' );
            }
        }
    });

    // Form Validation
    $( '.we-ls-weight-form-validate' ).each( function () {

        let form_id           = $(this).attr("id");
        let form_type         = $(this).data("form-type");
        let target_form       = ( 'target' === form_type );
        let weight_unit       = $(this).data("metric-unit");
        let weight_mandatory  = $(this).hasClass("weight-required");

        window.console && console.log( ' Adding form validation to: ' + form_id + '. Form Type: ' + form_type+ '. Target form? ' + target_form + '. Weight Unit: ' + weight_unit );

        // Add form validation
        $( "#" + form_id ).validate({     errorContainer:       '#' + form_id + ' .ws-ls-error-summary',
                                                  errorLabelContainer:  '#' + form_id + ' .ws-ls-error-summary ul',
                                                  wrapper:              'li',
                                                  ignore:               [],
                                                  errorClass:           'ws-ls-invalid',
                                                  validClass:           'ws-ls-valid',
                                                  messages: {
                                                      'we-ls-date' :          ws_ls_config[ 'validation-we-ls-date' ],
                                                      'ws-ls-weight-pounds' : ws_ls_config[ 'validation-we-ls-weight-pounds' ],
                                                      'ws-ls-weight-kg' :     ws_ls_config[ 'validation-we-ls-weight-kg' ],
                                                      'ws-ls-weight-stones' : ws_ls_config[ 'validation-we-ls-weight-stones' ]
                                                  },
            submitHandler: function( form ) {
                $( '.ws-ls-remove-on-submit' ).remove();
                $( '.ws-ls-form-processing-throbber' ).removeClass( 'ws-ls-hide' );
                form.submit();
            }
        });

        /**
         * Non target form with date picker?
         */
        if ( false === target_form ) {
            // If a datepicker is on this form
            if ( $( '#' + form_id + ' .we-ls-datepicker' ).length) {

                let date_validation_options = { required: true };

                if ( "true" === ws_ls_config["us-date"] ) {
                    date_validation_options[ 'date' ] = true;
                } else {
                    date_validation_options[ 'dateITA' ] = true;
                }

                $( "#" + form_id + " .we-ls-datepicker" ).rules( "add",  date_validation_options );
            }
        }

        if ( 'weight' === form_type &&
                true === weight_mandatory ) {
          let default_field_options = { required: ! target_form, number: true, range: [ 0, 5000 ] };

          // Set up numeric fields to validate
          if( 'pounds_only' === weight_unit ) {

            $( '#' + form_id + ' .ws-ls-weight-pounds' ).rules( 'add', default_field_options );

          } else if( 'stones_pounds' === weight_unit ) {

            $( '#' + form_id + ' .ws-ls-weight-stones' ).rules( 'add', default_field_options );

            default_field_options[ 'range' ] = [ 0, 14 ];

            $( '#' + form_id + ' .ws-ls-weight-pounds' ).rules('add',  default_field_options );

          } else {
            $( '#' + form_id + ' .ws-ls-weight-kg' ).rules('add',  default_field_options );
          }
        }
    });

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

function ws_ls_entry_post_data( action, data, callback ) {

  data[ 'action' ]    = action;
  data[ 'security' ]  = ws_ls_config['ajax-security-nonce'];

  jQuery.post( ws_ls_config[ 'ajax-url' ], data, function ( response ) {

    callback( data, response );
  });
}
