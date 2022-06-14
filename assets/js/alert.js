( function( $ ) {

  'use strict';

  $( '.uk-scope' ).on( 'click', '.ykuk-alert-close', function( event ) {

    event.preventDefault();

    let closer            = $( this );
    let notification_id   = closer.data( 'notification-id' );

    if ( 0 === notification_id ) {
      return;
    }

    let data = {  'action'            : 'ws_ls_delete_notification',
                  'security'          : ws_ls_config[ 'ajax-security-nonce' ],
                  'notification-id'   : notification_id
    };

    $.post( ws_ls_config[ 'ajax-url' ], data, function ( response ) {

    });

  });

} )( jQuery );
