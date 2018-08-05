//
// To compress this script, use https://jscompress.com
//
jQuery( document ).ready(function ($) {
    $( ".ws-ls-user-pref-form" ).submit(function( event ) {

        event.preventDefault();

        var post_data = {};

        // This code is specifc to admin
        post_data["security"] = ws_ls_user_pref_config["ajax-security-nonce"];
        post_data["we-ls-in-admin"] = true;

        // Get User ID from form
        post_data["user-id"] = $('#ws-ls-user-id').val();

        if(0 === post_data["user-id"]) {
            alert('Error loading user ID');
            return;
        }

        // ------------------------------------------------------------------------
        // The following code is common between public and admin user preferences
        // ------------------------------------------------------------------------
        $(".ws-ls-user-pref-form select").each(function () {
            post_data[$(this).attr("id")] = $(this).val();
        });

        post_data["action"] = "ws_ls_save_preferences";
        post_data['ws-ls-dob'] = $('#ws-ls-dob').val();

        ws_ls_post_data(post_data, ws_ls_user_preference_callback);
    });

    function ws_ls_user_preference_callback(data, response)	{

        if (1 == response) {
            window.location.replace(ws_ls_user_pref_config["preferences-page"] + '&user-preference-saved=y');
        } else {
            $('#ws-ls-notice p').text(ws_ls_user_pref_config["preferences-save-fail"]);
            $('#ws-ls-notice').removeClass('ws-ls-hide');
            $('#ws-ls-notice').addClass('notice-error');
        }
    }

    function ws_ls_post_data(data, callback)
    {
        jQuery.post(ajaxurl, data, function(response) {

            var response = JSON.parse(response);
            callback(data, response);
        });
    }

});
