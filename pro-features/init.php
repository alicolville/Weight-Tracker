<?php

defined('ABSPATH') or die("Jog on!");

// Include relevant files
if(defined('WS_LS_ABSPATH')){
	include WS_LS_ABSPATH . 'pro-features/functions.php';
	include WS_LS_ABSPATH . 'pro-features/user-preferences.php';
	include WS_LS_ABSPATH . 'pro-features/ajax-handler-public.php';
	include WS_LS_ABSPATH . 'pro-features/ajax-handler-admin.php';
	include WS_LS_ABSPATH . 'pro-features/shortcode-chart.php';
	include WS_LS_ABSPATH . 'pro-features/shortcode-form.php';
	include WS_LS_ABSPATH . 'pro-features/shortcode-footable.php';
	include WS_LS_ABSPATH . 'pro-features/shortcode-various.php';
	include WS_LS_ABSPATH . 'pro-features/shortcode-stats.php';
	include WS_LS_ABSPATH . 'pro-features/shortcode-reminders.php';
	include WS_LS_ABSPATH . 'pro-features/shortcode-progress-bar.php';
	include WS_LS_ABSPATH . 'pro-features/shortcode-messages.php';
	include WS_LS_ABSPATH . 'pro-features/shortcode-if.php';
	include WS_LS_ABSPATH . 'pro-features/widget-chart.php';
	include WS_LS_ABSPATH . 'pro-features/widget-form.php';
	include WS_LS_ABSPATH . 'pro-features/widget-progress.php';
	include WS_LS_ABSPATH . 'pro-features/footable.php';
	include WS_LS_ABSPATH . 'pro-features/db.php';
	include WS_LS_ABSPATH . 'pro-features/functions.measurements.php';
	include WS_LS_ABSPATH . 'pro-features/functions.stats.php';
	include WS_LS_ABSPATH . 'pro-features/export.php';

	// Admin pages for managing user data
	include WS_LS_ABSPATH . 'pro-features/functions.pages.php';
	include WS_LS_ABSPATH . 'pro-features/admin-pages/data-home.php';
	include WS_LS_ABSPATH . 'pro-features/admin-pages/data-summary.php';
	include WS_LS_ABSPATH . 'pro-features/admin-pages/data-view-all.php';
	include WS_LS_ABSPATH . 'pro-features/admin-pages/data-add-edit-entry.php';
	include WS_LS_ABSPATH . 'pro-features/admin-pages/data-edit-target.php';
	include WS_LS_ABSPATH . 'pro-features/admin-pages/data-user.php';
	include WS_LS_ABSPATH . 'pro-features/admin-pages/data-user-edit-settings.php';
    include WS_LS_ABSPATH . 'pro-features/admin-pages/data-search-results.php';
    include WS_LS_ABSPATH . 'pro-features/admin-pages/data-photos.php';

    // Include files for those that have a Pro Plus license
    if(WS_LS_IS_PRO_PLUS) {
        include WS_LS_ABSPATH . 'pro-features/plus/bmr.php';
		include WS_LS_ABSPATH . 'pro-features/plus/harris.benedict.php';
        include WS_LS_ABSPATH . 'pro-features/plus/macronutrient.calculator.php';

		// Photos enabled?
		if(WE_LS_PHOTOS_ENABLED) {
			include WS_LS_ABSPATH . 'pro-features/plus/photos.php';
			include WS_LS_ABSPATH . 'pro-features/plus/photos.gallery.php';
		}
    }

	// Email notifications enabled?
	if(WE_LS_EMAIL_ENABLE) {
		include WS_LS_ABSPATH . 'pro-features/emails.php';
	}

}

// Register shortcodes
function ws_ls_register_pro_shortcodes(){

    /*
        [wlt-chart] - Displays a chart
        [wlt-form] - Displays a form
        [wlt-table] - Displays a data table
		[wlt-recent-bmi] - Displays the user's BMI for most recent weight
		[wlt-total-lost] - Total lost / gained by the entire community.
		[wlt-league-table] - Show a league table of weight loss users.
		[wlt-reminder] - Show a reminder to either enter weight for today or target weight
		[wlt-progress-bar] - Show a progress bar indicating progress towards target weight.
		[wlt-message] - Show a message if the user meets certain criteria (e.g. put weight on).
		[wlt-user-settings] - Shows a form for user settings.
        [wlt-gender] - display the user's gender
        [wlt-dob] - display the user's Date of Birth
        [wlt-activity-level] - display the user's Activity Level
        [wlt-new-users] - display the number of new WP users in last x days
    	[wlt-if] - display content conditionally
    */

    add_shortcode( 'weight-loss-tracker-chart', 'ws_ls_shortcode_chart' );
	add_shortcode( 'wlt-chart', 'ws_ls_shortcode_chart' );
    add_shortcode( 'weight-loss-tracker-form', 'ws_ls_shortcode_form' );
	add_shortcode( 'wlt-form', 'ws_ls_shortcode_form' );
    add_shortcode( 'weight-loss-tracker-table', 'ws_ls_shortcode_table' );
	add_shortcode( 'wlt-table', 'ws_ls_shortcode_table' );
	add_shortcode( 'weight-loss-tracker-most-recent-bmi', 'ws_ls_get_user_bmi' );
	add_shortcode( 'wlt-recent-bmi', 'ws_ls_get_user_bmi' );
	add_shortcode( 'wlt-bmi', 'ws_ls_get_user_bmi' );
	add_shortcode( 'weight-loss-tracker-total-lost', 'ws_ls_shortcode_stats_total_lost' );
	add_shortcode( 'wlt-total-lost', 'ws_ls_shortcode_stats_total_lost' );
	add_shortcode( 'weight-loss-tracker-league-table', 'ws_ls_shortcode_stats_league_total' );
	add_shortcode( 'wlt-league-table', 'ws_ls_shortcode_stats_league_total' );
	add_shortcode( 'weight-loss-tracker-reminder', 'ws_ls_shortcode_reminder' );
	add_shortcode( 'wlt-reminder', 'ws_ls_shortcode_reminder' );
	add_shortcode( 'weight-loss-tracker-progress-bar', 'ws_ls_shortcode_progress_bar' );
	add_shortcode( 'wlt-progress-bar', 'ws_ls_shortcode_progress_bar' );
	add_shortcode( 'weight-loss-tracker-message', 'ws_ls_shortcode_message' );
	add_shortcode( 'wlt-message', 'ws_ls_shortcode_message' );
	add_shortcode( 'wlt-user-settings', 'ws_ls_user_preferences_form' );

	add_shortcode( 'wlt-gender', 'ws_ls_shortcode_gender' );
	add_shortcode( 'wlt-dob', 'ws_ls_shortcode_dob' );
	add_shortcode( 'wlt-height', 'ws_ls_shortcode_height' );
	add_shortcode( 'wlt-activity-level', 'ws_ls_shortcode_activity_level' );
    add_shortcode( 'wlt-new-users', 'ws_ls_shortcode_new_users' );
	add_shortcode( 'wlt-if', 'ws_ls_shortcode_if' );
}
add_action( 'init', 'ws_ls_register_pro_shortcodes');

function ws_ls_admin_enqueue_pro_scripts(){
	wp_enqueue_style('ws-ls-admin-style', plugins_url( '/css/admin.css', dirname(__FILE__) ), array(), WE_LS_CURRENT_VERSION);
}
add_action( 'admin_enqueue_scripts', 'ws_ls_admin_enqueue_pro_scripts');

function we_ls_register_widgets()
{
    register_widget( 'ws_ls_widget_chart' );
    register_widget( 'ws_ls_widget_form' );
	register_widget( 'ws_ls_widget_progress_bar' );
}
add_action( 'after_setup_theme', 'we_ls_register_widgets', 20 );


function wlt_user_action_links($actions, $user_object) {
	$actions['edit_badges'] = "<a href='" . ws_ls_get_link_to_user_profile($user_object->ID) . "'>" . __( 'View weight entries', WE_LS_SLUG ) . "</a>";
	return $actions;
}
add_filter('user_row_actions', 'wlt_user_action_links', 10, 2);
