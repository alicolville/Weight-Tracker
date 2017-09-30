//
// To compress this script, use https://jscompress.com
//

$tabs_global = false;

jQuery( document ).ready(function ($) {

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

    function ws_ls_show_you_need_to_refresh_messages() {
        $(".ws-ls-notice-of-refresh").removeClass("ws-ls-hide");
    }

    var default_tab = "tab1";

    // Just saved data or cancelled? If so, set default Tab to be "In Detail"
    if(ws_ls_get_querystring_value('ws-edit-saved') || ws_ls_get_querystring_value('ws-edit-cancel')) {
        default_tab = "tab2";
    }

    var wsLSTabsReady = function(event, item) {
        $("#ws-ls-tabs-loading").addClass("ws-ls-hide");
        $("#" + item.id).addClass("ws-ls-force-show");
        $("#" + item.id).removeClass("ws-ls-hide");
    };

    $tabs_global = $("#ws-ls-tabs").zozoTabs({
        rounded: false,
        multiline: true,
        theme: "silver",
        size: "small",
        minWindowWidth: 3000,				// Force tabs into browser
        responsive: true,
        animation: {
            effects: "slideH",
            easing: "easeInOutCirc",
            type: "jquery"
        },
        defaultTab: default_tab,
        ready: wsLSTabsReady
    });


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
                    "ws-ls-aim": {
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
                    "ws-ls-aim" : ws_ls_config["validation-about-you-aim"],
					"ws-ls-activity-level" : ws_ls_config["validation-about-you-activity-level"],
					"we-ls-dob": ws_ls_config["validation-about-you-dob"],
                },
	            submitHandler: function(form) {
                    ws_ls_submit_preference_form();
	            }
	        });

            $.extend(jQuery.validator.messages, {
                required: ws_ls_config["validation-required"],
                // remote: "Please fix this field.",
                // email: "Please enter a valid email address.",
                // url: "Please enter a valid URL.",
                // date: "Please enter a valid date.",
                // dateISO: "Please enter a valid date (ISO).",
                // number: "Please enter a valid number.",
                // digits: "Please enter only digits.",
                // creditcard: "Please enter a valid credit card number.",
                // equalTo: "Please enter the same value again.",
                // accept: "Please enter a value with a valid extension.",
                // maxlength: jQuery.validator.format("Please enter no more than {0} characters."),
                // minlength: jQuery.validator.format("Please enter at least {0} characters."),
                // rangelength: jQuery.validator.format("Please enter a value between {0} and {1} characters long."),
                // range: jQuery.validator.format("Please enter a value between {0} and {1}."),
                // max: jQuery.validator.format("Please enter a value less than or equal to {0}."),
                //min: jQuery.validator.format("Please enter a value greater than or equal to {0}.")
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

	// ------------------------------------------------------------------------
	// User for file selector labels
	// ------------------------------------------------------------------------
	var inputs = document.querySelectorAll( '.ws-ls-input-file' );
	Array.prototype.forEach.call( inputs, function( input )
	{
		var label	 = input.nextElementSibling,
			labelVal = label.innerHTML;

		input.addEventListener( 'change', function( e )
		{
			var fileName = e.target.value.split( '\\' ).pop();

			if( fileName )
				label.querySelector( 'span' ).innerHTML = fileName;
			else
				label.innerHTML = labelVal;
		});

		// Firefox bug fix
		input.addEventListener( 'focus', function(){ input.classList.add( 'has-focus' ); });
		input.addEventListener( 'blur', function(){ input.classList.remove( 'has-focus' ); });
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
