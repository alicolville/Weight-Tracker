( function( $ ) {

  'use strict';

  /*
    Change tabs in a uikit switcher
   */
  $( '.uk-scope' ).on( 'click', '.ws-ls-tab-change', function( event ) {

    event.preventDefault();

    let link      = $( this );
    let tab_name  = link.data( 'tab' );

    if ( undefined === tab_name ) {
      return;
    }

    // If we couldn't switch tabs, default back to href attribute
    if ( true !== ws_ls_tab_change( tab_name ) ) {

      let redirect_url = link.attr( 'href' );

      if ( undefined === redirect_url ) {
        return;
      }

      window.location.href = redirect_url.replace( 'ws-edit-saved', 'ws-edit-cancel' );
    }
  });

} )( jQuery );

/**
 * Look up a tab names index and switch to relevant tab on switcher
 * @param name
 * @returns {boolean}
 */
function ws_ls_tab_change( name ) {

  let tab_index = ws_ls_tab_get_position( name );

  if ( null === tab_index ) {
    return false;
  }

  ykukUIkit.tab( ".ws-ls-tracker .ykuk-tab-menu" ).show( tab_index );

  return true;
}

/**
 * From the WP localised object, determine what tab index the tab is at
 * @param name
 * @returns {null|*}
 */
function ws_ls_tab_get_position( name ) {

  if( 'undefined' === typeof ws_ls_tab_positions ) {
    return null;
  }

  return ws_ls_tab_positions.indexOf( name );
}




//
// var urls = ["google", "yahoo", "facebook"];
// var cls = $('div').attr('class');
// var ind = $.inArray(cls, urls);

// console.log('shit');
// ykukUIkit.tab( ".ws-ls-tracker .ykuk-tab-menu").show(4);
