var tabs_global = false;

jQuery( document ).ready( function ( $ ) {

  // -----------------------------------------------------------------------
  // Clear Target
  // -----------------------------------------------------------------------

  $( '.ws-ls-clear-target' ).click( function( event ) {

    event.preventDefault();

      if( false === confirm( ws_ls_config[ 'clear-target' ] ) ){
          return;
      }

      ws_ls_post( 'ws_ls_clear_target',
                  { 'user-id' : ws_ls_config[ 'user-id' ] },
                function() { window.location.replace( ws_ls_config[ 'current-url' ] + '?target-cleared=true' ) } );
  });

  // -----------------------------------------------------------------------
  // Tabs (ZoZo)
  // -----------------------------------------------------------------------

  // Just saved data or cancelled? If so, set default Tab to be "In Detail"
  let default_tab = ( ws_ls_querystring_value( 'ws-edit-saved' ) || ws_ls_querystring_value( 'ws-edit-cancel' ) ) ? 'tab2' : 'tab1';

  let tabs_are_ready = function( event, item ) {
      $( '#ws-ls-tabs-loading' ).addClass( 'ws-ls-hide' );
      $( '#' + item.id ).addClass( 'ws-ls-force-show' );
      $( '#' + item.id ).removeClass( 'ws-ls-hide' );
  };

  tabs_global = $( '#ws-ls-tabs' ).zozoTabs({   rounded:        false,
                                                multiline:      true,
                                                theme:          'silver',
                                                size:           'small',
                                                minWindowWidth: 3000,
                                                responsive:     true,
                                                animation: {
                                                                effects: 'slideH',
                                                                easing: 'easeInOutCirc',
                                                                type: 'jquery'
                                                },
                                                defaultTab:     default_tab,
                                                ready:          tabs_are_ready
  });

  // -----------------------------------------------------------------------
  // Progress Bar
  // -----------------------------------------------------------------------

  $( '.ws-ls-progress' ).each( function() {

    let id        = '#' + $( this ).attr('id' );
    let progress  = $( this ).data( 'progress' );
    let text      = $( this ).data( 'percentage-text' );
    let options   = {
                          strokeWidth:  $( this ).data( 'stroke-width' ),
                          easing:       'easeInOut',
                          duration:     $( this ).data( 'animation-duration' ),
                          color:        $( this ).data('stroke-colour' ),
                          trailColor:   $( this ).data( 'trail-colour' ),
                          trailWidth:   $( this ).data( 'trail-width' ),
                          svgStyle:     { width: $( this ).data( 'width' ), height: $( this ).data( 'height' ) },
                          text: {
                            style: {
                                color: $(this).data( 'text-colour' )
                            },
                            value: Math.round( progress * 100 ) + '% ' + text
                          },
                          step: function( state, bar ) {
                              bar.setText( Math.round(bar.value() * 100 ) + '% ' + text );
                          }
    };

    let progress_bar = false;

    if( 'circle' === $( this ).data('type' ) ) {
       progress_bar = new ProgressBar.Circle( id, options );
    } else {
       progress_bar = new ProgressBar.Line( id, options );
    }

    progress_bar.animate( progress );
  });

  // ------------------------------------------------------------------------
  // File selector for meta fields
  // ------------------------------------------------------------------------

  if ( 'true' === ws_ls_config[ 'photos-enabled' ] ) {

    let inputs = document.querySelectorAll( '.ws-ls-input-file' );

    Array.prototype.forEach.call( inputs, function( input ) {

      let label	  = input.nextElementSibling;
      let value   = label.innerHTML;

      input.addEventListener( 'change', function( e )  {

        let file_name = e.target.value.split( '\\' ).pop();

        if( file_name ) {
          label.querySelector( 'span' ).innerHTML = file_name;
        } else {
          label.innerHTML = value;
        }
      });

      // Firefox bug fix
      input.addEventListener( 'focus', function(){ input.classList.add( 'has-focus' ); });
      input.addEventListener( 'blur', function(){ input.classList.remove( 'has-focus' ); });
    });
  }

  // -----------------------------------------------------------------------
  // User Preference form (Front end)
  // -----------------------------------------------------------------------

  if ( 'true' === ws_ls_config[ 'is-pro' ] ) {

    /**
     * Delete existing user data
     */
    $( '.ws-ls-user-delete-all' ).validate({        errorClass:           'ws-ls-invalid',
                                                            validClass:           'ws-ls-valid',
                                                            errorContainer:       '.ws-ls-user-delete-all .ws-ls-error-summary',
                                                            errorLabelContainer:  '.ws-ls-user-delete-all .ws-ls-error-summary ul',
                                                            wrapper:              'li',
                                                            messages: {
                                                                                  'ws-ls-delete-all': ws_ls_config[ 'validation-we-ls-history'  ],
                                                            },
                                                            submitHandler: function( form ) {
                                                                form.submit();
                                                            }
      });

    /**
     * Save Preferences
     */
    $( '.ws-ls-user-pref-form' ).submit( function( event ) {

        event.preventDefault();

        if ( 'true' !== ws_ls_config[ 'validation-about-you-mandatory' ] ) {
            ws_ls_submit_preference_form();
        }
    });


      // Do we want to force all About You fields in user preferences to be mandatory?
    if ( 'true' === ws_ls_config[ 'validation-about-you-mandatory' ] ) {

       $( ".ws-ls-user-pref-form" ).validate({        errorClass:           'ws-ls-invalid',
                                                              validClass:           'ws-ls-valid',
                                                              errorContainer:       '.ws-ls-user-delete-all .ws-ls-error-summary',
                                                              errorLabelContainer:  '.ws-ls-user-delete-all .ws-ls-error-summary ul',
                                                              wrapper:              'li',
                                                              rules:                 ws_ls_config[ 'validation-user-pref-rules' ],
                                                              messages:              ws_ls_config[ 'validation-user-pref-messages' ],
            submitHandler: function( form ) {
                ws_ls_submit_preference_form();
            }
        });

        $.extend( jQuery.validator.messages, {
            required: ws_ls_config[ 'validation-required' ]
        });

        // If a datepicker is on this form
        if ( $( '.ws-ls-user-pref-form .we-ls-datepicker' ).length ) {
            // Validate date
            if ( 'true' === ws_ls_config[ 'us-date' ] ) {
                $( '.ws-ls-user-pref-form .we-ls-datepicker' ).rules( 'add', {
                    required: true,
                    date:     true
                });
            } else {
                $( '.ws-ls-user-pref-form .we-ls-datepicker' ).rules( 'add', {
                    required: true,
                    dateITA:  true
                });
            }
        }
    }

    /**
     * Post user preferences to AJAX handler
     **/
    function ws_ls_submit_preference_form() {

        let post_data = { 'user-id' : ws_ls_config[ 'user-id' ] };

        // ------------------------------------------------------------------------
        // The following code is common between public and admin user preferences
        // ------------------------------------------------------------------------
        $( '.ws-ls-user-pref-form select, .ws-ls-user-pref-form .custom-field' ).each(function () {
          post_data[ $( this ).attr('id' ) ] = $( this ).val();
        });

        post_data[ 'ws-ls-dob' ]  = $( '#ws-ls-dob' ).val();

        ws_ls_post( 'ws_ls_save_preferences', post_data, ws_ls_user_preference_callback );
    }
  }
});

/**
 * Post back to AJAX handler
 * @param action
 * @param data
 * @param callback
 */
function ws_ls_post( action, data, callback ) {

  data[ 'action' ]    = action;
  data[ 'security' ]  = ws_ls_config['ajax-security-nonce'];

  jQuery.post( ws_ls_config[ 'ajax-url' ], data, function ( response ) {

    callback( data, response );
  });
}

function ws_ls_user_preference_callback(data, response)
{
    if (response == 1) {

        // Is there a redirect url  specified on the form itself? If so, redirect to that URL.
        var redirect_url = jQuery(".ws-ls-user-pref-form").data('redirect-url');

        if(redirect_url) {
            window.location.replace(redirect_url);
        } else {
            window.location.replace(ws_ls_config["current-url"] + "?user-preference-saved=true");
        }
    }
    else
    {
        console.log("Error saving the user preferences");
    }
}

/**
 * Fetch a querystring value for the given key
 * @param key
 * @returns {string}
 */
function ws_ls_querystring_value(key ) {

    let page_url    = window.location.search.substring( 1 );
    let qs_values   = page_url.split('&' );

    for ( let i = 0; i < qs_values.length; i++ ) {

        let qs_name = qs_values[ i ].split( '=' );

        if ( key === qs_name[0]) {
            return qs_values[ i ];
        }
    }
}
