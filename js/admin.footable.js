jQuery( document ).ready(function ($) {

	ws_ls_log('Processing user data tables..');

	 $(".ws-ls-user-data").each(function () {

	   var table_id = $(this).attr("id");
	   var user_id = $(this).data("user-id");
	   var max_entries = $(this).data("max-entries");

	   ws_ls_log('Setting up table: ' + table_id);

	   // OK we know whether or not we're looking for a user's data or everyones. Let's post back to WP admin
	   // for columns and data!
	   var data = {};
	   data['user_id'] = user_id;
	   data['max_entries'] = max_entries;
	   data['table_id'] = table_id;
	   ws_ls_post_data_to_WP('table_data', data, ws_ls_callback_setup_table)

     });


	 function ws_ls_post_data_to_WP(action, data, callback) {

	 	post_data = {};
	 	post_data['action'] = action;
	 	post_data['security'] = ws_user_table_config['security'];

		// var post_data = $.merge(post_data, data);
		var post_data = obj3 = $.extend(post_data, data);

	    $.post(ajaxurl, post_data, function(response) {
	 	 // Fire back to given callback with response from server
	 	 callback && callback(response);
	    });
	 }

	function ws_ls_callback_setup_table(response) {

		// TODO Validate AJAX response. Display error if needed.

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
	 		"rows": response.rows
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

		var date = new Date(value);
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
