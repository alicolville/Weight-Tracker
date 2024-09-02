jQuery( document ).ready( function ( $ ) {

    let button_id 			= '#' + ws_notes_add_config[ 'component_id' ] + '_button';
    let textarea_id 		= '#' + ws_notes_add_config[ 'component_id' ] + '_textarea';
    let most_recent_id 		= '#' + ws_notes_add_config[ 'component_id' ] + '_most_recent';
    let errormessage_id 	= '#' + ws_notes_add_config[ 'component_id' ] + '_errormessage';
    let successmessage_id 	= '#' + ws_notes_add_config[ 'component_id' ] + '_successmessage';

    $( button_id ).click( function( event ) {

        event.preventDefault();

        let note = $( textarea_id ).val();

        $( button_id ).addClass( 'ws-ls-loading-button');
        $( errormessage_id ).addClass( 'ws-ls-hide');
        $( successmessage_id ).addClass( 'ws-ls-hide' );

        let data = { 	'action' : 			'ws_ls_add_note',
                        'security' : 		ws_notes_add_config[ 'nonce' ],
                        'user-id' :			ws_notes_add_config[ 'user_id' ],
                        'note' :			note,
                        'send-email' :		$( '#' + ws_notes_add_config[ 'component_id' ] + '_send_email' ).is(':checked'),
                        'visible-to-user' :	$( '#' + ws_notes_add_config[ 'component_id' ] + '_visible_to_user' ).is(':checked')
        };

        jQuery.post( ws_notes_add_config[ 'url' ], data, function ( response ) {

            if ( parseInt( response ) === 0 ) {
                $( errormessage_id ).removeClass( 'ws-ls-hide' );
                return;
            }

            $( most_recent_id ).val( $( textarea_id ).val() );

            $( textarea_id ).val( '' );

            $( "#" + ws_notes_add_config[ 'component_id' ] + "_count" ).text( response );
            $( successmessage_id ).removeClass( 'ws-ls-hide' );

        }).fail(function() {
            $( errormessage_id ).removeClass( 'ws-ls-hide' );
        })
        .always(function() {
            $( button_id ).removeClass( 'ws-ls-loading-button');
        });;
    });

    let hide_most_recent_id 	= '#' + ws_notes_add_config[ 'component_id' ] + '_hide_most_read';
    let view_most_recent_id 	= '#' + ws_notes_add_config[ 'component_id' ] + '_view_most_read';
    let view_most_recent_div_id = '#' + ws_notes_add_config[ 'component_id' ] + '_most_recent_comment_div';
    let view_add_new_div_id 	= '#' + ws_notes_add_config[ 'component_id' ] + '_add_new_div';

    $( hide_most_recent_id ).click( function( event ) {

        event.preventDefault();

        $( view_most_recent_id ).removeClass( 'ws-ls-hide' );
        $( hide_most_recent_id ).addClass( 'ws-ls-hide' );
        $( view_most_recent_div_id ).addClass( 'ws-ls-hide' );
        $( view_add_new_div_id ).removeClass( 'ws-ls-hide' );
    });

    $( view_most_recent_id ).click( function( event ) {

        event.preventDefault();

        $( hide_most_recent_id ).removeClass( 'ws-ls-hide' );
        $( view_most_recent_id ).addClass( 'ws-ls-hide' );
        $( view_most_recent_div_id ).removeClass( 'ws-ls-hide' );
        $( view_add_new_div_id ).addClass( 'ws-ls-hide' );
    });

});