jQuery( document ).ready(function ($) {

	$( '.notice.is-dismissible' ).on('click', '.notice-dismiss', function ( event ) {

        event.preventDefault();
        var $this = $(this);
        if( 'undefined' == $this.parent().data('wsmd5') ){
            return;
        }

		var ws_md5 = $this.parent().data('wsmd5');

		$.post( ajaxurl, {
            action: 'ws_ls_dismiss_notice',
            url: ajaxurl,
            md5: ws_md5
        });

    });


});
