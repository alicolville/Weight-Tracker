( function( $ ) {

  'use strict';

  $( '.uk-scope' ).on( 'click', '.ykuk-alert-close', function( event ) {

    event.preventDefault();

    let closer            = $( this );
    let notification_id   = closer.data( 'notification-id' );

    if ( 0 === notification_id ) {
      return;
    }

    // Todo: Post AJAX to say never to show this notification again

  });

} )( jQuery );
