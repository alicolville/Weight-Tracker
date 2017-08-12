//
// To compress this script, use https://jscompress.com
//
jQuery( document ).ready(function ($) {

	// If we're on the settings tab setup zozoTabs
    if ($.fn.zozoTabs) {
        $("#ws-ls-tabs").zozoTabs({
            rounded: false,
            multiline: true,
            theme: "silver",
            size: "medium",
            responsive: true,
            animation: {
                effects: "slideH",
                easing: "easeInOutCirc",
                type: "jquery"
            }
        });
    }

    // Disable all inputs for Pro rows
    $(".ws-ls-disabled input").prop('disabled', true);
    $(".ws-ls-disabled select").prop('disabled', true);

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
