( function( $ ) {

  'use strict';

  $( '.form-calculate-bmi .button-calculate-bmi' ).click( function( event ) {

    event.preventDefault();

    let form    = $(this).parents('form:first');
    let form_id = form.attr('id');

    if ( 'metric' === form.data( 'unit' ) ) {

      console.log( 'Calculate BMI with kg/cm' );

      if ( true === ws_ls_validate_form( form_id ) ) {

        console.log( 'Form valid');
      }

    } else {

      console.log( 'Calculate BMI with st/lb/ft/in' );

    }

  });

  /**
   * Validate form
   * @param ref
   * @returns {boolean}
   */
  function ws_ls_validate_form( ref ) {

    let is_valid = true;

    $( '#' + ref + ' .ykuk-input' ).each( function() {

      if ( $(this).val() === '' ) {
        $(this).addClass( 'ykuk-form-danger' );
        is_valid = false;
      } else {
        $(this).removeClass( 'ykuk-form-danger' );
      }
    });

    return is_valid;
  }

} )( jQuery );

