//
// To compress this script, use https://jscompress.com
//

$tabs_global = false;

jQuery( document ).ready(function ($) {
    if ("true" == ws_ls_config["advanced-tables-enabled"]) {

        if ("true" == ws_ls_config["us-date"]) {
            $.fn.dataTable.moment( "MM/DD/YYYY" );
        }
        else {
            $.fn.dataTable.moment( "DD/MM/YYYY" );
        }

        $(".ws-ls-advanced-data-table").DataTable( {
            "responsive":true,
            "order": [[ 1, "desc" ]],
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "columnDefs": ws_ls_config_advanced_datatables["columns"],
            "language": ws_ls_table_locale
        });

    }

    // Delete / Edit links on tables
    $('.ws-ls-advanced-data-table').on('click', '.ws-ls-edit-row', function ( event ) {
        event.preventDefault();

        $post_data = {};

        $post_data["action"] = "ws_ls_get_entry";
        $post_data["security"] = ws_ls_config["ajax-security-nonce"];
        $post_data["user-id"] = ws_ls_config["user-id"];
        $post_data["row-id"] = $(this).data("row-id");
        $post_data["form-id"] = $(".ws-ls-main-weight-form").attr("id");

        ws_ls_post_data($post_data, ws_ls_edit_row_callback);
    });

    $('.ws-ls-advanced-data-table').on('click', '.ws-ls-delete-row', function ( event ) {
        event.preventDefault();

        if(!confirm(ws_ls_config["confirmation-delete"])){
            return;
        }

        var tr = $(this).closest("tr");
        var table = $(this).closest("table");

        $post_data = {};

        $post_data["action"] = "ws_ls_delete_weight_entry";
        $post_data["security"] = ws_ls_config["ajax-security-nonce"];
        $post_data["user-id"] = ws_ls_config["user-id"];
        $post_data["table-row-id"] = tr.attr("id");
        $post_data["table-id"] = table.attr("id");
        $post_data["row-id"] = $(this).data("row-id");

        ws_ls_post_data($post_data, ws_ls_delete_row_callback);
    });

    function ws_ls_delete_row_callback(data, response)
    {
        if (response == 1) {

            var table = $("#" + data["table-id"]).DataTable();
            var tr = $("#" + data["table-row-id"]);

            table
                .row(tr)
                .remove()
                .draw();

            ws_ls_show_you_need_to_refresh_messages();
        }
        else
        {
            console.log("Error deleting entry :(", data, response);
        }
    }
    $(".ws-ls-clear-target").click(function( event ) {
        event.preventDefault();

        if(!confirm(ws_ls_config["clear-target"])){
            return;
        }

        $post_data = {};
        $post_data["action"] = "ws_ls_clear_target";
        $post_data["security"] = ws_ls_config["ajax-security-nonce"];
        $post_data["user-id"] = ws_ls_config["user-id"];

        ws_ls_post_data($post_data, ws_ls_clear_target_callback);
    });

    function ws_ls_clear_target_callback(data, response)
    {
        if (response == 1) {
            ws_ls_show_you_need_to_refresh_messages();
        }
        else
        {
            console.log("Error clearing target :(", data, response);
        }
    }

    function ws_ls_edit_row_callback(data, response)
    {
        if (response != 0) {

            if ("true" == ws_ls_config["us-date"]) {
                $("#" + data["form-id"] + " .we-ls-datepicker").val(response["date-us"]);
            }
            else {
                $("#" + data["form-id"] + " .we-ls-datepicker").val(response["date-uk"]);
            }

            $weight_unit = $("#" + data["form-id"]).data("metric-unit");

            if("imperial-pounds" == $weight_unit) {
                $("#" + data["form-id"] + " #we-ls-weight-pounds").val(response["only_pounds"]);
            }
            if("imperial-both" == $weight_unit)
            {
                $("#" + data["form-id"] + " #we-ls-weight-stones").val(response["stones"]);
                $("#" + data["form-id"] + " #we-ls-weight-pounds").val(response["pounds"]);
            }
            if("metric" == $weight_unit)
            {
                $("#" + data["form-id"] + " #we-ls-weight-kg").val(response["kg"]);
            }
            $("#" + data["form-id"] + " #we-ls-notes").val(response["notes"]);

            if ("true" == ws_ls_config["tabs-enabled"]) {
                $tabs_global.data("zozoTabs").first();
            }

            // Measurements?
            if(response["measurements"] != false) {
                var measurements = response["measurements"];
                for (var k in measurements) {
                    if (measurements.hasOwnProperty(k)) {
                        $("#" + data["form-id"] + " #ws-ls-" + k).val(measurements[k]);
                    }
                }
            }

            // Set focus on edit form
            $(".ws-ls-main-weight-form input:visible:nth-child(3)").focus();
        }
        else
        {
            console.log("Error loading entry :(", data, response);
        }
    }

    function ws_ls_show_you_need_to_refresh_messages() {
        $(".ws-ls-notice-of-refresh").removeClass("ws-ls-hide");
    }

    if ("true" == ws_ls_config["tabs-enabled"]) {

        $default_tab = "tab1";

        var wsLSTabsReady = function(event, item) {
            $("#ws-ls-tabs-loading").addClass("ws-ls-hide");
            $("#" + item.id).attr("style", "");
        };

        $tabs_global = $("#ws-ls-tabs").zozoTabs({
            rounded: false,
            multiline: true,
            theme: "silver",
            size: "medium",
            responsive: true,
            animation: {
                effects: "slideH",
                easing: "easeInOutCirc",
                type: "jquery"
            },
            defaultTab: $default_tab,
            ready: wsLSTabsReady
        });

    }

    // User preference form
    if ("true" == ws_ls_config["is-pro"]) {

        $( ".ws-ls-user-delete-all" ).validate({
            errorClass: "ws-ls-invalid",
            validClass: "ws-ls-valid",
            errorContainer: ".ws-ls-user-delete-all .ws-ls-error-summary",
            errorLabelContainer: ".ws-ls-user-delete-all .ws-ls-error-summary ul",
            wrapper: "li",
            messages: {
                "ws-ls-delete-all": ws_ls_config["validation-we-ls-history"],
            },
            submitHandler: function(form) {
                form.submit();
            }
        });

        $( ".ws-ls-user-pref-form" ).submit(function( event ) {

            event.preventDefault();

            if ("true" != ws_ls_config['validation-about-you-mandatory']) {
                ws_ls_submit_preference_form();
            }

        });

		var form_preference_validation = false;

		// // Do we want to force all About You fields in user preferences to be madatory?
		if ("true" == ws_ls_config['validation-about-you-mandatory']) {

			form_preference_validation = $( ".ws-ls-user-pref-form" ).validate({
	            errorClass: "ws-ls-invalid",
	            validClass: "ws-ls-valid",
	            errorContainer: ".ws-ls-user-pref-form .ws-ls-error-summary",
	            errorLabelContainer: ".ws-ls-user-pref-form .ws-ls-error-summary ul",
	            wrapper: "li",
				rules: {
	                "ws-ls-gender": {
						"required" : true,
						min: 1
					},
					"we-ls-height": {
						"required" : true,
						min: 1
					},
					"ws-ls-activity-level": {
						"required" : true,
						min: 1
					}
	            },
				messages: {
					"ws-ls-gender" : ws_ls_config["validation-about-you-gender"],
					"we-ls-height" : ws_ls_config["validation-about-you-height"],
					"ws-ls-activity-level" : ws_ls_config["validation-about-you-activity-level"],
					"we-ls-dob": ws_ls_config["validation-about-you-dob"]
				},
	            submitHandler: function(form) {
                    ws_ls_submit_preference_form();
	            }
	        });

			//If a datepicker is on this form
            if ($(".ws-ls-user-pref-form .we-ls-datepicker").length) {
                // Validate date
                if ("true" == ws_ls_config["us-date"]) {
                    $(".ws-ls-user-pref-form .we-ls-datepicker").rules( "add", {
                        required: true,
                        date: true
                    });
                }
                else {
                    $(".ws-ls-user-pref-form .we-ls-datepicker").rules( "add", {
                        required: true,
                        dateITA: true
                    });
                }
            }
		}

		/**
		* Post user preferences to AJAX handler
		**/
        function ws_ls_submit_preference_form() {

            var post_data = {};

            // This code is specifc to front end
            post_data["security"] = ws_ls_config["ajax-security-nonce"];
            post_data["user-id"] = ws_ls_config["user-id"];

            // ------------------------------------------------------------------------
            // The following code is common between public and admin user preferences
            // ------------------------------------------------------------------------
            $(".ws-ls-user-pref-form select").each(function () {
                post_data[$(this).attr("id")] = $(this).val();
            });

            post_data['ws-ls-dob'] = $('#ws-ls-dob').val();
            post_data["action"] = "ws_ls_save_preferences";

            ws_ls_post_data(post_data, ws_ls_user_preference_callback);

        }

    }


    $(".ws-ls-reload-page-if-clicked").click(function( event ) {
        event.preventDefault();
        window.location.replace(ws_ls_config["current-url"]);
    });


    // Progress Bar Shortcodes
    $(".ws-ls-progress").each(function () {
        var id = $(this).attr("id");
        var progress = $(this).data("progress");
        var type = $(this).data("type");

        var options = {
            strokeWidth: $(this).data("stroke-width"),
            easing: "easeInOut",
            duration: $(this).data("animation-duration"),
            color: $(this).data("stroke-colour"),
            trailColor: $(this).data("trail-colour"),
            trailWidth: $(this).data("trail-width"),
            svgStyle: {width: $(this).data("width"), height: $(this).data("height")},
            text: {
                style: {
                    color: $(this).data("text-colour")
                },
            },
            step: function(state, bar, id) {
                bar.setText(Math.round(bar.value() * 100) + "% " + $('#' + bar._container.id).data("precentage-text"));
            }
        };

        if("circle" == type) {
            var progress_bar = new ProgressBar.Circle("#" + id, options);
        } else {
            var progress_bar = new ProgressBar.Line("#" + id, options);
        }
        progress_bar.animate(progress);

    });
});

function ws_ls_post_data(data, callback)
{
    var ajaxurl = ws_ls_config["ajax-url"];

    jQuery.post(ajaxurl, data, function(response) {

        var response = JSON.parse(response);
        callback(data, response);
    });
}

function ws_ls_user_preference_callback(data, response)
{
    if (response == 1) {

        // Is there a redirect url specified on the form itself? If so, redirect to that URL.
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

function ws_ls_get_querystring_value(name)
{
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split("&");
    for (var i = 0; i < sURLVariables.length; i++)
    {
        var sParameterName = sURLVariables[i].split("=");
        if (sParameterName[0] == name) {
            return sParameterName[1];
        }
    }
}
