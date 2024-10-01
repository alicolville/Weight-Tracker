jQuery( document ).ready( function ( $ ) {

    $( '.ws-ls-js-redirect' ).each( function () {

        window.location.replace( $( this ).data('url') );

        return false; 
    });
});