jQuery( document ).ready( function ( $ ) {

  // -----------------------------------------------------------------------
  // Custom Fields slider
  // -----------------------------------------------------------------------

  $( '.ws-ls-meta-fields-slider' ).each( function () {
    
    let id = '#' + this.id;

    $( id ).slider({
        min: 	$( this ).data( 'min' ),
        max: 	$( this ).data( 'max' ),
        step:	$( this ).data( 'step' ),
        value: $( this ).data( 'value' )
    }
    ).slider( "pips", {
      rest: $( this ).data( 'pips' )
    }
    ).on( "slidechange", function(e,ui) {
      $( id + "-value" ).val( ui.value );
    });
    
  });

  // -----------------------------------------------------------------------
  // Custom Fields accumulator
  // -----------------------------------------------------------------------

  $( '.ws-ls-acc-buttons button' ).click( function( event ) {

    event.preventDefault();

    let button          = $( this );
    let parent          = button.data( 'parent-id' );
    let status_message  = $( '#' + parent + ' .ws-ls-status-message' );
    let increment       = button.data( 'increment' );

    // Reset status message
    status_message.removeClass( 'ws-meta-error' ).addClass( 'ws-meta-hide' ).html( '' );

    // Small hack. On first click set the width of the button. This saves it jumping about with it's HTML changing
    // during HTML changes.
    if ( true !== button.data( 'width-set' ) ) {
      button.css( 'width', button.outerWidth() +'px' );
      button.data( 'width-set', true );
    }

    button.html( ws_ls_meta_fields_config[ 'text-saving' ] ) ;

    ws_ls_meta_field_post( 'ws_ls_meta_field_accumulator',
      { 'increment' : increment, 'meta-field-id' : button.data( 'meta-field-id' ) },
      function( data, response ) {

          if ( true === response[ 'error' ] ) {
            status_message.addClass( 'ws-meta-error' ).removeClass( 'ws-meta-success' ).html('<p>' + ws_ls_meta_fields_config[ 'text-failure' ] + '</p>');

            button.html( '<i class="fa fa-exclamation-circle"></i>' );
          } else {

            $( '#' + parent + ' .ws-ls-acc-value' ).html( response[ 'value' ] );

            button.html( '<i class="fa fa-check"></i>' );

          }

          status_message.removeClass( 'ws-meta-hide' );

          // Revert button back to original state
          button.delay(1000)
                .queue(function(n) {
                  button.html('<i class="fa ' + button.data( 'icon' ) + '"></i> ' + button.data( 'original-text' ) );
                  n();
                 });
    });
  });

});

/**
 * Post back to AJAX handler
 * @param action
 * @param data
 * @param callback
 */
function ws_ls_meta_field_post( action, data, callback ) {

  data[ 'action' ]    = action;
  data[ 'security' ]  = ws_ls_meta_fields_config['ajax-security-nonce'];

  jQuery.post( ws_ls_meta_fields_config[ 'ajax-url' ], data, function ( response ) {
    callback( data, response );
  });
}
