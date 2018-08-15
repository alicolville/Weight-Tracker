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

    // 100% Validation for Macronutrients
    $('.ws-ls-macro').focus(function() {
        prev_val = $(this).val();
    }).change(function() {

        event.preventDefault();
        var updated_id = this.id;

        var sum = 0;
        $('.ws-ls-macro').each(function(){
            sum += parseInt(this.value);
        });

        if(sum > 100 || sum < 0) {
            $(this).val(prev_val);
            alert('Please ensure the total of these three fields is greater than 0% and less than 100%');
        }
    });

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

	// ------------------------------------------------------------------------
	// User for file selector labels
	// ------------------------------------------------------------------------
	var inputs = document.querySelectorAll( '.ws-ls-input-file' );
	Array.prototype.forEach.call( inputs, function( input )
	{
		var label	 = input.nextElementSibling,
			labelVal = label.innerHTML;

		input.addEventListener( 'change', function( e )
		{
			var fileName = e.target.value.split( '\\' ).pop();

			if( fileName )
				label.querySelector( 'span' ).innerHTML = fileName;
			else
				label.innerHTML = labelVal;
		});

		// Firefox bug fix
		input.addEventListener( 'focus', function(){ input.classList.add( 'has-focus' ); });
		input.addEventListener( 'blur', function(){ input.classList.remove( 'has-focus' ); });
	});


	function ws_ls_meta_fields_show_additional() {

	    var value = $('#field_type').val();
        alert(value);

    }

    ws_ls_meta_fields_show_additional();
});
