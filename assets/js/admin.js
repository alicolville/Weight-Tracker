//
// To compress this script, use https://jscompress.com
//
jQuery( document ).ready(function ($) {

    if ( $.fn.zozoTabs ) {
        $( '#ws-ls-tabs' ).zozoTabs({
                                      rounded:    false,
                                      multiline:  true,
                                      theme:      'silver',
                                      size:       'medium',
                                      responsive: true,
                                      animation: {
                                          effects:  'slideH',
                                          easing:   'easeInOutCirc',
                                          type:     'jquery'
                                      }
        });
    }

    /*
        Ensure the ratio for MacroN equals 100%
     */
    $('.ws-ls-macro').focus(function() {
        prev_val = $( this ).val();
    }).change(function() {

        event.preventDefault();

        var sum = 0;
        $( '.ws-ls-macro' ).each( function(){
            sum += parseInt( this.value );
        });

        if(sum > 100 || sum < 0) {
            $( this ).val( prev_val );
            alert( 'Please ensure the total of the Macronutrient fields is greater than 0% and less than 100% ');
        }
    });

    /*
        Ensure the ratio of meals for MacroN equals 100%
     */
    $( '.ws-ls-macro-meals' ).focus(function() {
        prev_val = $( this ).val();
    }).change(function() {

        event.preventDefault();

        var sum = 0;

        $( '.ws-ls-macro-meals' ).each( function(){
            sum += parseInt( this.value );
        });

        if( sum > 100 || sum < 0 ) {
            $( this ).val( prev_val );
            alert( 'Please ensure the total of the Macronutrient Meals fields is greater than 0% and less than 100% ');
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

    // ------------------------------------------------------------
    // Meta Fields
    // ------------------------------------------------------------

    // Show / hide additional fields on meta fields add / update
    function ws_ls_meta_fields_show_additional() {

        let meta_field_ids = [ 0, 1, 2, 3, 4 ];

        let value = $('#field_type').val();

        for ( $i = 0; $i < meta_field_ids.length; $i++ ) {

            if ( value == meta_field_ids[ $i ] ) {
                $('.ws-ls-meta-fields-additional-' + meta_field_ids[ $i ] ).removeClass( 'ws-ls-hide' );
            } else {
                $('.ws-ls-meta-fields-additional-' + meta_field_ids[ $i ] ).addClass( 'ws-ls-hide' );
            }

        }

    }

    $( "#field_type" ).change(function() {
        ws_ls_meta_fields_show_additional();
    });

    ws_ls_meta_fields_show_additional();

    // ------------------------------------------------------------
    // Awards
    // ------------------------------------------------------------

    // Show / hide additional fields on meta fields add / update
    function ws_ls_awards_show_additional() {

        var award_ids = [ 'bmi', 'bmi-equals', 'weight', 'weight-percentage' ];

        var value = $('#category').val();

        for ( $i = 0; $i < award_ids.length; $i++ ) {

            if ( value == award_ids[ $i ] ) {
                $('#ws-ls-awards-additional-' + award_ids[ $i ] ).removeClass( 'ws-ls-hide' );
                $('.hide-' + award_ids[ $i ] ).addClass( 'ws-ls-hide' );
            } else {
                $('#ws-ls-awards-additional-' + award_ids[ $i ] ).addClass( 'ws-ls-hide' );
                $('.hide-' + award_ids[ $i ] ).removeClass( 'ws-ls-hide' );
            }

        }
    }

    $( "#ws-ls-awards-form #category" ).change(function() {
        ws_ls_awards_show_additional();
    });

    ws_ls_awards_show_additional();

    // ------------------------------------------------------------
    // Setup Wizard
    // ------------------------------------------------------------

    $( '.setup-wizard-dismiss' ).on('click', '.notice-dismiss, .dismiss-wizard', function ( event ) {

        event.preventDefault();

        $.post( ajaxurl, {
            action: 'ws_ls_setup_wizard_dismiss',
            url: ajaxurl
        });
    });

  // ------------------------------------------------------------
  // Settings: Show Calories
  // ------------------------------------------------------------

  $( '.ws-ls-calorie-subtract-ranges-show-more' ).on('click', function ( event ) {

    event.preventDefault();

    $( '.ws-ls-calorie-subtract-ranges-rows' ).show();
    $( '.ws-ls-calorie-subtract-ranges-show-more' ).hide();

  });

  $( '.ws-ls-calorie-add-ranges-show-more' ).on('click', function ( event ) {

    event.preventDefault();

    $( '.ws-ls-calorie-add-ranges-rows' ).show();
    $( '.ws-ls-calorie-add-ranges-show-more' ).hide();

  });

  // ------------------------------------------------------------
  // Export
  // ------------------------------------------------------------

  // Show / hide additional fields on meta fields add / update
  function ws_ls_export_show_date_ranges() {

    let value = $('#ws-ls-export-new-form #date-range').val();

    if ( 'custom' === value ) {
      $( '#ws-ls-date-range-options' ).removeClass( 'ws-ls-hide' );
    } else {
      $( '#ws-ls-date-range-options' ).addClass( 'ws-ls-hide' );
    }

  }

  $( "#ws-ls-export-new-form #date-range" ).change(function() {
      ws_ls_export_show_date_ranges();
  });

  ws_ls_export_show_date_ranges();

  $( '.ws-ls-export-check-all' ).on( 'click', function ( event ) {

    event.preventDefault();

    $( '.report-column' ).prop( 'checked', true );

  });

  $( '.ws-ls-export-uncheck-all' ).on( 'click', function ( event ) {

    event.preventDefault();

    $( '.report-column' ).prop( 'checked', false );

  });

  ws_ls_export_process();

  function ws_ls_export_process(  ) {

    if ( 0 === $( '.ws-ls-export-progress-bar' ).length ) {
      return;
    }

    let data = {  'action'    : 'process_export',
                  'security'  : ws_ls_security[ 'ajax-security-nonce' ],
                  'id'        : $( '.ws-ls-export-progress-bar' ).data( 'export-id' )
    };

    jQuery.post( ajaxurl, data, function( response ) {
     // response = JSON.parse( response );
      ws_ls_export_process_callback( data, response );
    });
  }

  function ws_ls_export_process_callback( data, response) {

    // Do we have an error?
    if ( true === response[ 'error' ] ) {
      $( '#ws-ls-export-message' ).text( response[ 'message'] );
      return;
    }

    // Update progress bar
    $( '.ws-ls-export-progress-bar-inner' ).css( 'width', response[ 'percentage'] + '%');

    // Update message if we have one
    if ( '' != response[ 'message' ] ) {

      let message = response[ 'message'];

      $( '#ws-ls-export-message' ).html( message );
    }
;
    // Continue?
    if ( true === response[ 'continue' ] ) {
      ws_ls_export_process();
    }

  }

  /*
    Postbox sorting / hiding
   */
  $( '.ws-ls-postbox .handlediv' ).on('click', function ( event ) {

    event.preventDefault();

    let postbox_id = $( this ).data( 'postbox-id' );
    let postbox    = $( '#' + postbox_id );

    postbox.toggleClass( 'closed' );

    let value = ( postbox.hasClass( 'closed' ) ) ? 0 : 1;

    ws_ls_postboxes_event( postbox_id, 'display', value )

  });

  /**
   * Fire an Ajax event back to back end to update postbox display / order preferences
   * @param id
   * @param key
   * @param value
   */
  function ws_ls_postboxes_event( id, key, value ) {

    let data = {  'action'    : 'postboxes_event',
                  'security'  : ws_ls_security[ 'ajax-security-nonce' ],
                  'id'        : id,
                  'key'       : key,
                  'value'     : value
    };

    jQuery.post( ajaxurl, data, function( response ) {
      // Fire and forget.
    });
  }

  /**
   * Handle Up and down click on postbox headers
   */
  $( '.ws-ls-postbox-higher, .ws-ls-postbox-lower' ).click( function( e ) {

    e.preventDefault();

    let column_name     = $( this ).data( 'postbox-col' );
    let ids             = ws_ls_postboxes_ids( column_name );
    let selected_id     = $( this ).data( 'postbox-id' );
    let selected_index  = ids.indexOf( selected_id );
    let move_up         = $( this ).hasClass( 'ws-ls-postbox-higher' );

    if ( true === move_up && selected_index > 0 || false === move_up && selected_index < ids.length ) {

      let postboxes   = $( '#' + column_name + ' .ws-ls-postbox' );
      let swap_index  = ( true === move_up ) ? selected_index - 1 : selected_index + 1;

      ws_ls_swap_elements( $( postboxes[ selected_index ] ).attr( 'id' ), $( postboxes[ swap_index ] ).attr( 'id' ) );

      ws_ls_postboxes_event( 'order', column_name, ws_ls_postboxes_ids( column_name ) );
    }
  });

  /**
   * Get all IDs for postboxes within column
   * @param name
   * @returns {[]}
   */
  function ws_ls_postboxes_ids( column_name ) {
    let  ids = [];
    $( '#' + column_name + ' .ws-ls-postbox' ).each( function () {
      ids.push( this.id );
    });

    return ids;
  }

  /**
   * Swap around two HTML elements
   * Source: https://stackoverflow.com/questions/10716986/swap-two-html-elements-and-preserve-event-listeners-on-them
   * @param first_element_id
   * @param second_element_id
   */
  function ws_ls_swap_elements( first_element_id, second_element_id ) {

    let obj1 = document.getElementById( first_element_id );
    let obj2 = document.getElementById( second_element_id );

    // save the location of obj2
    let parent2 = obj2.parentNode;
    let next2 = obj2.nextSibling;
    // special case for obj1 is the next sibling of obj2
    if (next2 === obj1) {
      // just put obj1 before obj2
      parent2.insertBefore(obj1, obj2);
    } else {
      // insert obj2 right before obj1
      obj1.parentNode.insertBefore(obj2, obj1);

      // now insert obj1 where obj2 was
      if (next2) {
        // if there was an element after obj2, then insert obj1 right before that
        parent2.insertBefore(obj1, next2);
      } else {
        // otherwise, just append as last child
        parent2.appendChild(obj1);
      }
    }
  }
});
