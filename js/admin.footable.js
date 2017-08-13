//
// To compress this script, use https://jscompress.com
//
jQuery( document ).ready(function ($) {

	ws_ls_log('Processing user data tables..');

	 $(".ws-ls-user-data").each(function () {

	   var table_id = $(this).attr("id");
	   var user_id = $(this).data("user-id");
	   var max_entries = $(this).data("max-entries");
	   var small_width = $(this).data("small-width");

	   ws_ls_log('Setting up table: ' + table_id);

	   // OK we know whether or not we're looking for a user's data or everyones. Let's post back to WP admin
	   // for columns and data!
	   var data = {};
	   data['user_id'] = user_id;
	   data['max_entries'] = max_entries;
	   data['small_width'] = small_width;
	   data['table_id'] = table_id;
	   ws_ls_post_data_to_WP('table_data', data, ws_ls_callback_setup_table)

     });


	 function ws_ls_post_data_to_WP(action, data, callback) {

	 	post_data = {};
	 	post_data['action'] = action;
	 	post_data['security'] = ws_user_table_config['security'];

		// var post_data = $.merge(post_data, data);
		var post_data = obj3 = $.extend(post_data, data);

	    $.post(ajaxurl, post_data, function(response, post_data) {
	 	 // Fire back to given callback with response from server
	 	 callback && callback(response, post_data);
	    });
	 }

	function ws_ls_callback_setup_table(response, data) {

		var table_id = '#' + response.table_id;
		var formatters = {};

		formatters['date'] = function(value){
				return "<b>DATE: " + value + "</b>";
		}

		response.columns[3].formatter = formatters['date'];

		// Apply formatters
		var columns = ws_ls_apply_formatters(response.columns);

		$(table_id).removeClass('ws-ls-loading-table');

		$(table_id).footable({
	 		"columns": columns,
	 		"rows": response.rows,
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
					window.location.href = ws_user_table_config['base-url'] + '&mode=entry&user-id=' + values.user_id + '&entry-id=' + values.db_row_id + '&redirect=' + ws_user_table_config['current-url-base64'];
				}
			}
	 	});

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
		} else {
			return month + '/' + day + '/' + year;
		}

		return value;
	}

});


function ws_ls_log(text) {
	console.log(text);
}
