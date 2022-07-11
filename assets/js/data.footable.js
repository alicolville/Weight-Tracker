//
// To compress this script, use https://jscompress.com
//
jQuery( document ).ready(function ( $, undefined ) {

    ws_ls_log('Populating user data tables..' );

    // Add Footables to Calories and MacroN tables
    $( '.ws-ls-footable' ).footable( {} );

    // ------------------------------------------------------------------------------------
    // Weight Entry tables
    // ------------------------------------------------------------------------------------
    $( '.ws-ls-user-data-ajax' ).each( function () {

        let table_id = $( this ).attr( 'id' );

        ws_ls_log('Weight Entries: Fetching Data: ' + table_id );

        let data = {
                        'enable-bmi':                     $( this ).data('enable-bmi' ),
                        'enable-notes':                   $( this ).data('enable-notes' ),
                        'enable-weight':                  $( this ).data('enable-weight' ),
                        'enable-meta-fields':             $( this ).data('enable-meta-fields' ),
                        'custom-field-restrict-rows':     $( this ).data('custom-field-restrict-rows' ),
                        'custom-field-groups':            $( this ).data('custom-field-groups' ),
                        'custom-field-slugs':             $( this ).data('custom-field-slugs' ),
                        'custom-field-col-size':          $( this ).data('custom-field-col-size' ),
                        'front-end' :                     ws_ls_in_front_end(),
                        'is-admin' :                      ws_user_table_config[ 'is-admin' ],
                        'max_entries':                    $( this ).data( 'max-entries' ),
                        'user_id':                        $( this ).data('user-id' ),
                        'small_width':                    $( this ).data('small-width' ),
                        'table_id':                       table_id,
                        'week':                           $( this ).data( 'week' ),
                        'bmi-format':                     $( this ).data( 'bmi-format' ),
                        'uikit':                          $( this ).data( 'uikit' )
        };

        ws_ls_post_data_to_WP( 'table_data', data, ws_ls_callback_setup_table )

    });

  /**
   * Setup weight entry table
   * @param response
   * @param data
   */
  function ws_ls_callback_setup_table( response, data ) {

    let table_id                              = '#' + response.table_id;
    let formatters                            = {};
    let date_column                           = ( true === ws_ls_in_front_end( )) ? 2 : 3;
    formatters[ 'date' ]                      = function( value ){ return "<b>DATE: " + value + "</b>"; };
    response.columns[ date_column ].formatter = formatters[ 'date' ];

    ws_ls_log('Weight Entries: Rendering Table: ' + table_id );

    // Apply formatters
    let columns = ws_ls_apply_formatters( response.columns );
    let rows    = response.rows;

    $( table_id ).removeClass( 'ws-ls-loading-table' );

    $( table_id ).footable({    'columns':  columns,
                                'rows':     rows,
                                'state':    { 'enabled' : true, 'key': 'ws-ls-admin-footable' },
                                editing: {
                                              enabled:    true,
                                              alwaysShow: true, // Don't show "Edit Rows" button
                                              allowAdd:   false,
                                              deleteRow: function( row ){
                                                                            if ( true === confirm( ws_user_table_config[ 'label-confirm-delete' ] ) ){

                                                                              let values = row.val();

                                                                              // Fetch the database record ID
                                                                              if ( true === $.isNumeric( values.db_row_id ) &&
                                                                                    true === $.isNumeric( values.user_id ) ) {

                                                                                row.delete();

                                                                                ws_ls_post_data_to_WP('delete_entry', { row_id : values.db_row_id, 'user_id' : values.user_id }, function( response, data ) {
                                                                                    if( 1 !== response ) {
                                                                                      alert( ws_user_table_config[ 'label-error-delete' ] );
                                                                                    }
                                                                                });
                                                                              }
                                                                            }
                                              },
                                              editRow: function( row ) {

                                                let values = row.val();

                                                // If we're in Admin, redirect to the relevant admin screen. Otherwise, toggle edit in front end
                                                if( true === ws_ls_in_front_end() && '1' !== ws_user_table_config[ 'is-admin' ] ) {

                                                    var url = ws_user_table_config[ 'edit-url' ];
                                                    url = url.replace( '|ws-id|', values.db_row_id );

                                                    window.location.href = url + '&user-id=' + values.user_id + '&redirect=' + ws_user_table_config[ 'current-url-base64' ];
                                                } else {
                                                    window.location.href = ws_user_table_config[ 'base-url' ] + '&mode=entry&user-id=' + values.user_id + '&entry-id=' + values.db_row_id + '&redirect=' + ws_user_table_config[ 'current-url-base64' ];
                                                }
                                  }
      }
    });

    $( table_id + ' .footable-filtering-search .input-group .form-control').attr('placeholder', ws_user_table_config[ 'locale-search-text' ] );

    // Replace "No results" string with locale version
    if ( 0 === rows.length ) {
        $( table_id + ' .footable-empty td' ).html( ws_user_table_config[ 'locale-no-results' ] );
    }
  }

  // ------------------------------------------------------------------------------------
  // Meta Fields
  // ------------------------------------------------------------------------------------

  $( '.ws-ls-meta-fields-list-ajax' ).each(function () {

      let table_id = $( this ).attr('id' );

      ws_ls_log( 'Meta Fields: Fetching Data: ' + table_id );

      ws_ls_post_data_to_WP('meta_fields_full_list', { 'table_id': table_id }, ws_ls_callback_meta_fields_list );
  });

  /**
   * Setup table
   * @param response
   * @param data
   */
  function ws_ls_callback_meta_fields_list(response, data) {

    let table_id  = '#meta-fields-list';
    let columns   = ws_ls_apply_formatters(response.columns);
    let rows      = response.rows;

    ws_ls_log('Meta Fields: Rendering Table: ' + table_id );

    $( table_id ).removeClass( 'ws-ls-loading-table' );

    $(table_id).footable({  'columns':  columns,
                            'rows':     rows,
                            'state':    { 'enabled' : true, 'key': 'ws-ls-admin-footable' },
                            editing: {  enabled:    true,
                                        alwaysShow: true,
                                        allowAdd:   true,
                                        addText:    ws_user_table_config[ 'label-meta-fields-add-button' ],
                                        deleteRow: function( row ){
                                          if ( true === confirm( ws_user_table_config[ 'label-confirm-delete' ] ) ){

                                            let values = row.val();

                                            if ( true === $.isNumeric( values.id ) ) {

                                              row.delete();

                                              // Post back to WP and delete from dB
                                              ws_ls_post_data_to_WP('meta_fields_delete', { 'id' : values.id }, function( response, data ) {
                                                if(1 !== response ) {
                                                  alert( ws_user_table_config[ 'label-error-delete' ] );
                                                }
                                              });
                                            }
                                          }
                                        },
                                        editRow: function(row) {
                                          let values = row.val();

                                          window.location.href = ws_user_table_config['base-url-meta-fields'] + '&mode=add-edit&id=' + values.id;
                                        },
                                        addRow: function() {
                                          window.location.href = ws_user_table_config['base-url-meta-fields'] + '&mode=add-edit';
                                        }
                          }
    });

    $( table_id + ' .footable-filtering-search .input-group .form-control').attr('placeholder', ws_user_table_config[ 'locale-search-text' ] );

    // Replace "No results" string with locale version
    if ( 0 === rows.length ) {
      $( table_id + ' .footable-empty td').html( ws_user_table_config[ 'locale-no-results' ] );
    }
  }

  // ------------------------------------------------------------------------------------
  // Awards
  // ------------------------------------------------------------------------------------

  $( '.ws-ls-awards-list-ajax' ).each( function () {

      let table_id = $( this ).attr('id' );

      ws_ls_log( 'Awards: Fetching Data: ' + table_id );

      ws_ls_post_data_to_WP('awards_full_list', { 'table_id' : table_id }, ws_ls_callback_awards_list )
  });

  /**
   * Render Awards table
   * @param response
   * @param data
   */
  function ws_ls_callback_awards_list( response, data ) {

    let table_id  = '#awards-list';
    let columns   = ws_ls_apply_formatters( response.columns );
    let rows      = response.rows;

    ws_ls_log('Awards: Rendering Table: ' + table_id );

    $( table_id ).removeClass( 'ws-ls-loading-table' );

    $( table_id ).footable({  'columns':  columns,
                              'rows':     rows,
                              'state':    { 'enabled' : true, 'key': 'ws-ls-admin-footable' },
                              editing: {
                                          enabled:    true,
                                          alwaysShow: true,
                                          allowAdd:   true,
                                          addText:    ws_user_table_config[ 'label-awards-add-button' ],
                                          deleteRow: function( row ){
                                            if ( true === confirm( ws_user_table_config[ 'label-confirm-delete' ] ) ){

                                              let values = row.val();

                                              if ( true === $.isNumeric( values.id ) ) {

                                                row.delete();

                                                ws_ls_post_data_to_WP('awards_delete', { 'id' : values.id }, function( response, data ) {
                                                  if( 1 !== response ) {
                                                    alert( ws_user_table_config[ 'label-error-delete' ] );
                                                  }
                                                });
                                              }

                                            }
                                          },
                                          editRow: function( row ) {
                                            let values = row.val();

                                            window.location.href = ws_user_table_config['base-url-awards'] + '&mode=add-edit&id=' + values.id;

                                          },
                                          addRow: function() {
                                            window.location.href = ws_user_table_config['base-url-awards'] + '&mode=add-edit';
                                          }
                              }
    });

    $( table_id + ' .footable-filtering-search .input-group .form-control' ).attr('placeholder', ws_user_table_config[ 'locale-search-text' ] );

    // Replace "No results" string with locale version
    if ( 0 === rows.length ) {
      $( table_id + ' .footable-empty td' ).html( ws_user_table_config[ 'locale-no-results' ] );
    }
  }

  // ------------------------------------------------------------------------------------
  // Errors
  // ------------------------------------------------------------------------------------

  $( '.ws-ls-errors-list-ajax' ).each( function () {

        let table_id = $( this ).attr('id' );

        ws_ls_log( 'Errors: Fetching Data: ' + table_id );

        ws_ls_post_data_to_WP('get_errors', { 'table_id' : table_id }, ws_ls_callback_errors_list)
  });

  function ws_ls_callback_errors_list( response, data ){

    let table_id  = '#errors-list';
    let columns   = ws_ls_apply_formatters( response.columns );
    let rows      = response.rows;

    ws_ls_log('Errors: Rendering Table: ' + table_id );

    $( table_id ).removeClass( 'ws-ls-loading-table' );

    $( table_id ).footable({    'columns':  columns,
                                'rows':     rows,
                                'state':    { 'enabled' : true, 'key': 'ws-ls-admin-footable' },
    });

    $( table_id + ' .footable-filtering-search .input-group .form-control' ).attr('placeholder', ws_user_table_config[ 'locale-search-text' ] );

    if ( 0 === rows.length ) {
      $( table_id + ' .footable-empty td' ).html( ws_user_table_config[ 'locale-no-results' ] );
    }

  }

  // ------------------------------------------------------------------------------------
  // Groups
  // ------------------------------------------------------------------------------------

  $( '.ws-ls-settings-groups-list-ajax' ).each(function () {

    let table_id = $( this ).attr('id' );

    ws_ls_log( 'Groups: Fetching Data: ' + table_id );

    ws_ls_post_data_to_WP( 'get_groups', { 'table_id' : table_id }, ws_ls_callback_groups );

  });

  function ws_ls_callback_groups( response, data ){

    let table_id  = '#' + response.table_id;
    let columns   = ws_ls_apply_formatters( response.columns );
    let rows      = response.rows;

    ws_ls_log('Groups: Rendering Table: ' + table_id );

    $( table_id ).removeClass( 'ws-ls-loading-table' );

    $( table_id ).footable({  'columns':  columns,
      'rows':     rows,
      'state':    { 'enabled' : true, 'key': 'ws-ls-admin-footable' },
      editing: {
                  enabled:      true,
                  allowAdd:     true,
                  alwaysShow:   true,
                  addText:      ws_user_table_config[ 'label-add' ],
                  deleteRow: function( row ){
                                                if ( true === confirm( ws_user_table_config[ 'label-confirm-delete' ] ) ){

                                                  let values = row.val();

                                                  // Fetch the database record ID
                                                  if ( true === $.isNumeric( values.id ) ) {

                                                    row.delete();

                                                    // Post back to WP and delete from dB
                                                    ws_ls_post_data_to_WP('groups_delete', { 'id' : values.id }, function( response, data ) {
                                                      if( 1 !== response ) {
                                                        alert( ws_user_table_config[ 'label-error-delete' ] );
                                                      }
                                                    });
                                                  }
                                                }
                  },
                  editRow: function( row ) {

                    let values = row.val();

                    window.location.href = ws_user_table_config[ 'base-url' ] + '&mode=groups&id=' + values.id;

                  }
      }
    });

    $( table_id + ' .footable-filtering-search .input-group .form-control').attr('placeholder', ws_user_table_config[ 'locale-search-text' ] ) ;

    // Replace "No results" string with locale version
    if ( 0 === rows.length ) {
      $( table_id + ' .footable-empty td').html( ws_user_table_config[ 'locale-no-results' ] );
    }
  }
  // ------------------------------------------------------------------------------------
  // Custom Field Groups
  // ------------------------------------------------------------------------------------

  $( '.ws-ls-settings-custom-field-groups-list-ajax' ).each(function () {

    let table_id = $( this ).attr('id' );

    ws_ls_log( 'Custom Fields Groups: Fetching Data: ' + table_id );

    ws_ls_post_data_to_WP( 'get_custom_field_groups', { 'table_id' : table_id }, ws_ls_callback_custom_field_groups );

  });

  function ws_ls_callback_custom_field_groups( response, data ){

    let table_id  = '#' + response.table_id;
    let columns   = ws_ls_apply_formatters( response.columns );
    let rows      = response.rows;

    ws_ls_log('Groups: Rendering Table: ' + table_id );

    $( table_id ).removeClass( 'ws-ls-loading-table' );

    $( table_id ).footable({  'columns':  columns,
      'rows':     rows,
      'state':    { 'enabled' : true, 'key': 'ws-ls-admin-footable' },
      editing: {
        enabled:      true,
        allowAdd:     true,
        alwaysShow:   true,
        addText:      ws_user_table_config[ 'label-add' ],
        deleteRow: function( row ){
          if ( true === confirm( ws_user_table_config[ 'label-confirm-delete' ] ) ){

            let values = row.val();

            // Fetch the database record ID
            if ( true === $.isNumeric( values.id ) ) {

              row.delete();

              // Post back to WP and delete from dB
              ws_ls_post_data_to_WP('custom_field_groups_delete', { 'id' : values.id }, function( response, data ) {
                if( 1 !== response ) {
                  alert( ws_user_table_config[ 'label-error-delete' ] );
                }
              });
            }
          }
        },
      }
    });

    $( table_id + ' .footable-filtering-search .input-group .form-control').attr('placeholder', ws_user_table_config[ 'locale-search-text' ] ) ;

    // Replace "No results" string with locale version
    if ( 0 === rows.length ) {
      $( table_id + ' .footable-empty td').html( ws_user_table_config[ 'locale-no-results' ] );
    }
  }

  // ------------------------------------------------------------------------------------
  // Users within a group
  // ------------------------------------------------------------------------------------

  $( '.ws-ls-settings-groups-users-list-ajax' ).each(function () {

      ws_ls_log( 'Users: Fetching Data' );

      ws_ls_post_data_to_WP('get_groups_users', { 'table_id': $(this).attr( 'id' ), 'group_id': $(this).data( 'group-id' ), 'todays_entries_only': $(this).data( 'todays-entries-only' ) }, ws_ls_callback_groups_users );
  });

  /**
   * Render user table
   * @param response
   * @param data
   */
    function ws_ls_callback_groups_users( response, data ){

      let table_id  = '#' + response.table_id;
      let columns   = ws_ls_apply_formatters( response.columns );
      let rows      = response.rows;

      ws_ls_log('Groups: Rendering Table: ' + table_id );

      $( table_id ).removeClass( 'ws-ls-loading-table' );

      $( table_id ).footable({  'columns':  columns,
            'rows':     rows,
            'state':    { 'enabled' : true, 'key': 'ws-ls-admin-footable' },
            'editing' : {
                            enabled:    true,
                            allowAdd:   true,
                            alwaysShow: true,
                            addText:    ws_user_table_config['label-add'],
                            deleteRow: function( row ){
                                                        if ( true === confirm( ws_user_table_config[ 'label-confirm-delete' ] ) ){

                                                            let values = row.val();

                                                            // Fetch the database record ID
                                                            if ( true === $.isNumeric( values.id ) ) {

                                                                row.delete();

                                                                // Post back to WP and delete from dB
                                                                ws_ls_post_data_to_WP('groups_users_delete', { 'id' : values.id }, function( response, data ) {
                                                                    if( 1 !== response) {
                                                                        alert( ws_user_table_config[ 'label-error-delete' ]);
                                                                    }
                                                                });
                                                            }
                                                        }
                            }
            }
        });

        $( table_id + ' .footable-filtering-search .input-group .form-control').attr('placeholder', ws_user_table_config[ 'locale-search-text' ] );

        // Replace "No results" string with locale version
        if ( 0 === rows.length ) {
            $( table_id + ' .footable-empty td' ).html( ws_user_table_config[ 'locale-no-results' ] );
        }
    }

  // ------------------------------------------------------------------------------------
  // Core functions
  // ------------------------------------------------------------------------------------

  function ws_ls_post_data_to_WP( action, data, callback ) {

    data[ 'action' ]   = action;
    data[ 'security' ] = ws_user_table_config['security'];

    let post_url = ( 'undefined' === typeof( ajaxurl ) && undefined !== ws_user_table_config[ 'ajax-url' ] ) ? ws_user_table_config[ 'ajax-url' ] : ajaxurl;

    $.post( post_url, data, function( response, post_data ) {
      callback && callback( response, post_data );
    });
  }

  /**
   * Apply date formatter
   * @param columns
   * @returns {{length}|*}
   */
    function ws_ls_apply_formatters(columns) {

        if( typeof columns !== 'undefined' && columns.length ) {

            let formatters = {};

            // Date field
            formatters['date'] = ws_ls_format_date;

            for ( let i = 0; i < columns.length; i++ ) {
                if(  'undefined' !== typeof formatters[ columns[ i ].name ] ) {
                    ws_ls_log('Applying formatter for: ' + columns[ i ].name );
                    columns[ i ].formatter = formatters[ 'date' ];
                }
            }
        }
        return columns;
    }

  /**
   * Format date into relevant string format
   * @param value
   * @returns {string}
   */
    function ws_ls_format_date(value) {

        if ( typeof moment !== 'undefined' && value instanceof moment ) {
            value = value._i;
        }

        let date  = value.split( ' ' );
        date      = new Date( date[ 0 ] );

        let day   = date.getUTCDate();
        let month = date.getUTCMonth() + 1;
        let year  = date.getUTCFullYear();

        return ( 'false' === ws_user_table_config[ 'us-date' ] ) ?
                  day + '/' + month + '/' + year :
                    month + '/' + day + '/' + year;

    }

  /**
   * Are we in the front end?
   * @returns {boolean}
   */
  function ws_ls_in_front_end() {
       return ( undefined !== ws_user_table_config['front-end'] && 'true' == ws_user_table_config['front-end']) ? true : false;
    }

});

function ws_ls_log( text ) {
    if ( window.console ) {
        console.log( text );
    }
}
