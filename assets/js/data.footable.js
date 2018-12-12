//
// To compress this script, use https://jscompress.com
//
jQuery( document ).ready(function ($, undefined) {

    ws_ls_log('Processing user data tables..');

    // Add Footables to Calories and MacroN tables
    $('.ws-ls-footable').footable({});

    $(".ws-ls-cancel-form").click(function( event ) {
        event.preventDefault();

        var button = $(this);
        var form_id = button.data('form-id');

        if ( undefined !== form_id ) {

            var redirect_url = $('#' + form_id + ' #ws_redirect').val();

            if ( undefined !== redirect_url ) {
                window.location.href = redirect_url.replace('ws-edit-saved', 'ws-edit-cancel');
            }

        }

    });

    $(".ws-ls-user-data-ajax").each(function () {

        var table_id = $(this).attr("id");
        var user_id = $(this).data("user-id");
        var max_entries = $(this).data("max-entries");
        var small_width = $(this).data("small-width");

        ws_ls_log('Setting up user data table: ' + table_id);

        // OK we know whether or not we're looking for a user's data or everyones. Let's post back to WP admin
        // for columns and data!
        var data = {};
        data['user_id'] = user_id;
        data['max_entries'] = max_entries;
        data['small_width'] = small_width;
        data['table_id'] = table_id;
        data['front-end'] = ws_ls_in_front_end();
        ws_ls_post_data_to_WP('table_data', data, ws_ls_callback_setup_table)

    });

    $(".ws-ls-meta-fields-list-ajax").each(function () {

        var table_id = $(this).attr("id");

        ws_ls_log('Setting up meta fields table: ' + table_id);

        // OK we know whether or not we're looking for a user's data or everyones. Let's post back to WP admin
        // for columns and data!
        var data = {};
        data['table_id'] = table_id;
        ws_ls_post_data_to_WP('meta_fields_full_list', data, ws_ls_callback_meta_fields_list)
    });

    $(".ws-ls-awards-list-ajax").each(function () {

        var table_id = $(this).attr("id");

        ws_ls_log('Setting up awards table: ' + table_id);

        // OK we know whether or not we're looking for a user's data or everyones. Let's post back to WP admin
        // for columns and data!
        var data = {};
        data['table_id'] = table_id;
        ws_ls_post_data_to_WP('awards_full_list', data, ws_ls_callback_awards_list)
    });

    $(".ws-ls-errors-list-ajax").each(function () {

        var table_id = $(this).attr("id");

        ws_ls_log('Setting up meta fields table: ' + table_id);

        // OK we know whether or not we're looking for a user's data or everyones. Let's post back to WP admin
        // for columns and data!
        var data = {};
        data['table_id'] = table_id;
        ws_ls_post_data_to_WP('get_errors', data, ws_ls_callback_errors_list)
    });

    $(".ws-ls-settings-groups-list-ajax").each(function () {

        var table_id = $(this).attr("id");

        ws_ls_log('Setting up groups table: ' + table_id);

        // OK we know whether or not we're looking for a user's data or everyones. Let's post back to WP admin
        // for columns and data!
        var data = {};
        data['table_id'] = table_id;
        ws_ls_post_data_to_WP('get_groups', data, ws_ls_callback_groups)
    });

    function ws_ls_post_data_to_WP(action, data, callback) {

        post_data = {};
        post_data['action'] = action;
        post_data['security'] = ws_user_table_config['security'];

        // var post_data = $.merge(post_data, data);
        var post_data = obj3 = $.extend(post_data, data);

        // If we're in the public facing site, set Ajax URL!
        var post_url = ( 'undefined' === typeof(ajaxurl) && undefined !== ws_user_table_config["ajax-url"]) ? ws_user_table_config["ajax-url"] : ajaxurl;

        $.post(post_url, post_data, function(response, post_data) {
            // Fire back to given callback with response from server
            callback && callback(response, post_data);
        });
    }

    function ws_ls_callback_errors_list( response, data ){

        var table_id = '#errors-list';
        var formatters = {};
        console.log(response);
        // Apply formatters
        var columns = ws_ls_apply_formatters(response.columns);
        var rows = response.rows;

        $(table_id).removeClass('ws-ls-loading-table');

        $(table_id).footable({
            "columns": columns,
            "rows": rows,
            "state": {
                "enabled" : true,
                "key": "ws-ls-admin-footable"
            }
        });

        $(table_id + ' .footable-filtering-search .input-group .form-control').attr("placeholder", ws_user_table_config['locale-search-text']);

        // Replace "No results" string with locale version
        if ( 0 === rows.length ) {
            $(table_id + ' .footable-empty td').html(ws_user_table_config['locale-no-results']);
        }

    }

    function ws_ls_callback_groups( response, data ){

        var table_id = '#' + response.table_id;
        var formatters = {};

        // Apply formatters
        var columns = ws_ls_apply_formatters(response.columns);
        var rows = response.rows;

        $(table_id).removeClass('ws-ls-loading-table');

        $(table_id).footable({
            "columns": columns,
            "rows": rows,
            "state": {
                "enabled" : true,
                "key": "ws-ls-admin-footable"
            },
            "editing" : {
                enabled: true,
                allowAdd: true,
                alwaysShow: true,
                addText: ws_user_table_config['label-add'],
                deleteRow: function(row){
                    if ( confirm(ws_user_table_config['label-confirm-delete']) ){

                        var values = row.val();

                        // Fetch the database record ID
                        if ($.isNumeric( values.id ) ) {

                            // OK, we have a Row ID - send to Ajax handler to delete from DB
                            var data = {};
                            data['id'] = values.id;

                            // To keep things looking fast (i.e. so no AJAX lag) delete row instantly from UI
                            row.delete();

                            // Post back to WP and delete from dB
                            ws_ls_post_data_to_WP('groups_delete', data, function(response, data) {
                                if(1 !== response) {
                                    alert(ws_user_table_config['label-error-delete']);
                                }
                            });
                        }
                    }
                },
            }
        });

        $(table_id + ' .footable-filtering-search .input-group .form-control').attr("placeholder", ws_user_table_config['locale-search-text']);

        // Replace "No results" string with locale version
        if ( 0 === rows.length ) {
            $(table_id + ' .footable-empty td').html(ws_user_table_config['locale-no-results']);
        }

    }

    function ws_ls_callback_meta_fields_list(response, data) {

        var table_id = '#meta-fields-list';
        var formatters = {};

        // Apply formatters
        var columns = ws_ls_apply_formatters(response.columns);
        var rows = response.rows;

        $(table_id).removeClass('ws-ls-loading-table');

        $(table_id).footable({
            "columns": columns,
            "rows": rows,
            "state": {
                "enabled" : true,
                "key": "ws-ls-admin-footable"
            },
            editing: {
                enabled: true,
                alwaysShow: true, // Don't show "Edit Rows" button
                allowAdd: true,
                addText: ws_user_table_config['label-meta-fields-add-button'],
                deleteRow: function(row){
                    if (confirm(ws_user_table_config['label-confirm-delete'])){

                        var values = row.val();

                        // Fetch the database record ID
                        if ($.isNumeric(values.id) ) {

                            // OK, we have a Row ID - send to Ajax handler to delete from DB
                            var data = {};
                            data['id'] = values.id;

                            // To keep things looking fast (i.e. so no AJAX lag) delete row instantly from UI
                            row.delete();

                            // Post back to WP and delete from dB
                            ws_ls_post_data_to_WP('meta_fields_delete', data, function(response, data) {
                                if(1 !== response) {
                                    alert(ws_user_table_config['label-error-delete']);
                                }
                            });
                        }
                    }
                },
                editRow: function(row) {
                    var values = row.val();

                    window.location.href = ws_user_table_config['base-url-meta-fields'] + '&mode=add-edit&id=' + values.id;

                },
                addRow: function() {
                    window.location.href = ws_user_table_config['base-url-meta-fields'] + '&mode=add-edit';

                }
            }
        });

        $(table_id + ' .footable-filtering-search .input-group .form-control').attr("placeholder", ws_user_table_config['locale-search-text']);

        // Replace "No results" string with locale version
        if ( 0 === rows.length ) {
            $(table_id + ' .footable-empty td').html(ws_user_table_config['locale-no-results']);
        }
    }

    function ws_ls_callback_awards_list(response, data) {

        var table_id = '#awards-list';
        var formatters = {};

        // Apply formatters
        var columns = ws_ls_apply_formatters(response.columns);
        var rows = response.rows;

        $(table_id).removeClass('ws-ls-loading-table');

        $(table_id).footable({
            "columns": columns,
            "rows": rows,
            "state": {
                "enabled" : true,
                "key": "ws-ls-admin-footable"
            },
            editing: {
                enabled: true,
                alwaysShow: true, // Don't show "Edit Rows" button
                allowAdd: true,
                addText: ws_user_table_config['label-awards-add-button'],
                deleteRow: function(row){
                    if (confirm(ws_user_table_config['label-confirm-delete'])){

                        var values = row.val();

                        // Fetch the database record ID
                        if ($.isNumeric(values.id) ) {

                            // OK, we have a Row ID - send to Ajax handler to delete from DB
                            var data = {};
                            data['id'] = values.id;

                            // To keep things looking fast (i.e. so no AJAX lag) delete row instantly from UI
                            row.delete();

                            // Post back to WP and delete from dB
                            ws_ls_post_data_to_WP('awards_delete', data, function(response, data) {
                                if(1 !== response) {
                                    alert(ws_user_table_config['label-error-delete']);
                                }
                            });
                        }

                    }
                },
                editRow: function(row) {
                    var values = row.val();

                    window.location.href = ws_user_table_config['base-url-awards'] + '&mode=add-edit&id=' + values.id;

                },
                addRow: function() {
                    window.location.href = ws_user_table_config['base-url-awards'] + '&mode=add-edit';

                }
            }
        });

        $(table_id + ' .footable-filtering-search .input-group .form-control').attr("placeholder", ws_user_table_config['locale-search-text']);

        // Replace "No results" string with locale version
        if ( 0 === rows.length ) {
            $(table_id + ' .footable-empty td').html(ws_user_table_config['locale-no-results']);
        }
    }

    function ws_ls_callback_setup_table(response, data) {

        var table_id = '#' + response.table_id;
        var formatters = {};

        formatters['date'] = function(value){
            return "<b>DATE: " + value + "</b>";
        };

        var date_column = (ws_ls_in_front_end()) ? 2 : 3;

        response.columns[date_column].formatter = formatters['date'];


        // Apply formatters
        var columns = ws_ls_apply_formatters(response.columns);
        var rows = response.rows;

        $(table_id).removeClass('ws-ls-loading-table');

        $(table_id).footable({
            "columns": columns,
            "rows": rows,
            "state": {
                "enabled" : true,
                "key": "ws-ls-admin-footable"
            },
            editing: {
                enabled: true,
                alwaysShow: true, // Don't show "Edit Rows" button
                allowAdd: false,
                deleteRow: function(row){
                    if (confirm(ws_user_table_config['label-confirm-delete'])){

                        var values = row.val();

                        // Fetch the database record ID
                        if ($.isNumeric(values.db_row_id) && $.isNumeric(values.user_id)) {

                            // OK, we have a Row ID - send to Ajax handler to delete from DB
                            var data = {};
                            data['row_id'] = values.db_row_id;
                            data['user_id'] = values.user_id;

                            // To keep things looking fast (i.e. so no AJAX lag) delete row instantly from UI
                            row.delete();

                            // Post back to WP and delete from dB
                            ws_ls_post_data_to_WP('delete_entry', data, function(response, data) {
                                if(1 !== response) {
                                    alert(ws_user_table_config['label-error-delete']);
                                }
                            });
                        }

                    }
                },
                editRow: function(row) {
                    var values = row.val();

                    // If we're in Admin, redirect to the relevant admin screen. Otherwise, toggle edit in front end
                    if(true === ws_ls_in_front_end()) {
                        var url = ws_user_table_config['edit-url'];
                        url = url.replace('|ws-id|', values.db_row_id);

                        window.location.href = url + '&user-id=' + values.user_id + '&redirect=' + ws_user_table_config['current-url-base64'];
                    } else {
                        window.location.href = ws_user_table_config['base-url'] + '&mode=entry&user-id=' + values.user_id + '&entry-id=' + values.db_row_id + '&redirect=' + ws_user_table_config['current-url-base64'];
                    }
                }
            }
        });

        $(table_id + ' .footable-filtering-search .input-group .form-control').attr("placeholder", ws_user_table_config['locale-search-text']);

        // Replace "No results" string with locale version
        if ( 0 === rows.length ) {
            $(table_id + ' .footable-empty td').html(ws_user_table_config['locale-no-results']);
        }
    }

    function ws_ls_apply_formatters(columns) {

        if(typeof columns !== 'undefined' && columns.length ) {

            var formatters = {};

            // Date field
            formatters['date'] = ws_ls_format_date;

            for (var i = 0; i < columns.length; i++) {
                if(typeof formatters[columns[i].name] !== 'undefined') {
                    ws_ls_log('Applying formatter for: ' + columns[i].name);
                    columns[i].formatter = formatters['date'];
                }
            }
        }
        return columns;
    }

    function ws_ls_format_date(value) {

        // Strip the timestamp off
        var date = value.split(" ");
        date = new Date(date[0]);

        var day = date.getDate();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();

        // US or UK format?
        if ('false' == ws_user_table_config['us-date']) {
            return day + '/' + month + '/' + year;
        }

        return month + '/' + day + '/' + year;

    }

    function ws_ls_in_front_end() {
        return ( undefined !== ws_user_table_config['front-end'] && 'true' == ws_user_table_config['front-end']) ? true : false;
    }

});


function ws_ls_log(text) {
    if (window.console) {
        console.log(text);
    }
}
