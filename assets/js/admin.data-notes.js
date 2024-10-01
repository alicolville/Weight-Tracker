jQuery( document ).ready( function ( $ ) {

    $( '.ws-note-delete-action' ).click( function( event ) {

        event.preventDefault();

        let to_delete_div = $( this ).data( 'div-id' );

        let data = { 	'action' 	: 'ws_ls_delete_note',
                        'security' 	: ws_notes_config[ 'nonce'],
                        'id'		: $( this ).data( 'id' )
        };

        jQuery.post( ws_notes_config[ 'url'], data, function ( response ) {

            if ( parseInt( response ) !== 1 ) {
                return;
            }

            $( '#' + to_delete_div ).addClass( 'ws-ls-hide' );

        }).fail(function() {
            alert( ws_notes_config[ 'error-message'] );
        })
    });
});