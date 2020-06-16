<?php

defined('ABSPATH') or die("Jog on!");

// Register shortcodes
function ws_ls_register_pro_shortcodes(){

    if ( false === WS_LS_IS_PRO ) {
        return;
    }

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

    add_shortcode( 'weight-loss-tracker-form', 'ws_ls_shortcode_form' );
    add_shortcode( 'wlt-form', 'ws_ls_shortcode_form' );
    add_shortcode( 'weight-loss-tracker-table', 'ws_ls_shortcode_table' );
    add_shortcode( 'wlt-table', 'ws_ls_shortcode_table' );
    add_shortcode( 'weight-loss-tracker-total-lost', 'ws_ls_shortcode_stats_total_lost' );
    add_shortcode( 'wlt-total-lost', 'ws_ls_shortcode_stats_total_lost' );
    add_shortcode( 'weight-loss-tracker-league-table', 'ws_ls_shortcode_stats_league_total' );
    add_shortcode( 'wlt-league-table', 'ws_ls_shortcode_stats_league_total' );
    add_shortcode( 'weight-loss-tracker-reminder', 'ws_ls_shortcode_reminder' );
    add_shortcode( 'wlt-reminder', 'ws_ls_shortcode_reminder' );
    add_shortcode( 'weight-loss-tracker-message', 'ws_ls_shortcode_message' );
    add_shortcode( 'wlt-message', 'ws_ls_shortcode_message' );
    add_shortcode( 'wlt-user-settings', 'ws_ls_user_preferences_form' );



    add_shortcode( 'wlt-if', 'ws_ls_shortcode_if' );
}
add_action( 'init', 'ws_ls_register_pro_shortcodes');

function we_ls_register_widgets() {

    if ( false === WS_LS_IS_PRO ) {
        return;
    }

    register_widget( 'ws_ls_widget_chart' );
    register_widget( 'ws_ls_widget_form' );
    register_widget( 'ws_ls_widget_progress_bar' );
}
add_action( 'after_setup_theme', 'we_ls_register_widgets', 20 );
