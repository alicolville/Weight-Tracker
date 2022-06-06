( function( $ ) {

  'use strict';

  $( '.form-calculate-bmi .button-calculate-bmi' ).click( function( event ) {

    event.preventDefault();

    let form      = $(this).parents('form:first');
    let form_id   = form.attr('id');
    let is_valid  = true;
    let data      = { 'unit' : form.data( 'unit' ) };

    // Validate form and build data object for ajax call
    $( '#' + form_id + ' .ykuk-input' ).each( function() {

      if ( $(this).val() === '' ) {

        $(this).addClass( 'ykuk-form-danger' );
        is_valid = false;

      } else {

        data[ $(this).attr('id') ] = $(this).val();

        $(this).removeClass( 'ykuk-form-danger' );
      }

    });

    if ( true === is_valid ) {

      data[ 'action' ]    = 'ws_ls_bmi_calculator';
      data[ 'security' ]  = ws_ls_bmi_calc_config['ajax-security-nonce'];

      $.post( ws_ls_bmi_calc_config[ 'ajax-url' ], data, function ( response ) {

          let alert =  $( '#' + form_id + ' .bmi-alert' );

          alert.attr('class', 'ykuk-alert bmi-alert ' + response[ 'css-class' ]);
          alert.html( response[ 'text' ] );
      });
    }

  });

} )( jQuery );
