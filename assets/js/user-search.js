( function( $ ) {

  'use strict';

  $(document).ready(function(){

    $(".ws-ls-component-user-search select").selectize({
      preload: ( 'true' === wt_user_search_config[ 'preload' ] ),
      valueField: "id",
      labelField: "title",
      searchField: "title",
      render: {
        option: function (item, escape) {
          return (
            '<div class="ws-ls-search-item"><div class="title">' + item.title + '</div>' + item.detail + '</div>'
          );
        },
      },
      load: function (query, callback) {

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
      onChange: function (value) {

        console.log(value);
      }
    });

  });

} )( jQuery );
