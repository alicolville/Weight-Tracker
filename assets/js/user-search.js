( function( $ ) {

  'use strict';

  $(document).ready(function(){

     $(".ws-ls-component-user-search select").selectize({
      preload: ( 'true' === wt_user_search_config[ 'preload' ] ),
      valueField: "id",
      labelField: "title",
      searchField: "title",
      placeholder: wt_user_search_config[ 'placeholder' ],
      render: {
        option: function (item, escape) {
          return (
            '<div class="ws-ls-search-item"><div class="title">' + item.title + '</div>' + item.detail + '</div>'
          );
        },
      },
      onType: function(){

        if ( 'true' !== wt_user_search_config[ 'preload' ] ) {
          this.$input[0].selectize.clearOptions();
          this.$input[0].selectize.refreshOptions(true);
        }
      },
      load: function (query, callback) {

        this.clearOptions();

        $.ajax({
          url: wt_config['ajax-url'],
          type: 'POST',
          data: { action: 'ws_ls_user_search', security: wt_config[ 'ajax-security-nonce' ], search: query},
          error: function () {
            callback();
          },
          success: function (res) {

            if ( 0 === res.length ) {
              ykukUIkit.notification({message: 'No results for: ' + query, pos: 'bottom-right', status: 'danger', timeout: 5000})
            }

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
