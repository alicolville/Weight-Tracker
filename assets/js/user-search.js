( function( $ ) {

  'use strict';

  $(document).ready(function(){

  //   $( '.ws-ls-component-user-search select' ).selectize({
  //     preload: false,
  //     valueField: 'id',
  //     labelField: 'user_email',
  //     searchField: 'name',
  //     options: [],
  //     load: function (query, callback) {
  //       this.clearOptions();
  //
  //       $.ajax({
  //         url: wt_config['ajax-url'],
  //         type: 'POST',
  //         data: { action: 'ws_ls_user_search', security: wt_config[ 'ajax-security-nonce' ], search: query},
  //         error: function () {
  //           callback();
  //         },
  //         success: function (res) {
  //           callback(res);
  //         }
  //       });
  //     },
  //     onChange: function (value) {
  //
  //       console.log( $( this ) );
  //
  //       console.log( value );
  //
  //     }
  //   });
  //

    $(".ws-ls-component-user-search select").selectize({
      valueField: "id",
      labelField: "user_email",
      searchField: "user_email",
      create: false,
      load: function (query, callback) {
        if (!query.length) return callback();
        $.ajax({
          url: wt_config['ajax-url'],
          type: 'POST',
          data: { action: 'ws_ls_user_search', security: wt_config[ 'ajax-security-nonce' ], search: query},
          error: function () {
            callback();
          },
          success: function (res) {
            callback(res);
          }
        });
      },
    });

  });

} )( jQuery );
