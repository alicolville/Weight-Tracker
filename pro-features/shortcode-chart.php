<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_shortcode_chart ($user_defined_arguments ) {

    if( false === WS_LS_IS_PRO ) {
       return false;
    }

    ws_ls_enqueue_files();

    $chart_arguments = shortcode_atts(
        array(
            'user-id' => get_current_user_id(),
            'max-data-points' => WE_LS_CHART_MAX_POINTS,
            'type' => get_option( 'ws-ls-chart-type', 'line' ),
            'height' => 250,
            'weight-line-color' => get_option( 'ws-ls-line-colour', '#aeaeae' ),
            'weight-fill-color' => get_option( 'ws-ls-line-fill-colour', '#f9f9f9' ),
            'weight-target-color' => get_option( 'ws-ls-target-colour', '#76bada' ),
            'show-gridlines' => ws_ls_option_to_bool( 'ws-ls-grid-lines' ),
            'bezier' => ws_ls_option_to_bool( 'ws-ls-bezier-curve' ),
			'ignore-login-status' => false
           ), $user_defined_arguments );

	// Make sure they are logged in
	if ( false === ws_ls_to_bool( $chart_arguments[ 'ignore-login-status'] ) &&
		false === is_user_logged_in() )	{
		return ws_ls_blockquote_login_prompt();
	}

    // Tidy up a few configs
   // $chart_arguments['bezier'] = ws_ls_force_bool_argument($chart_arguments['bezier']);
    $chart_arguments['show-gridlines'] = ws_ls_force_bool_argument($chart_arguments['show-gridlines']);
	$chart_arguments['ignore-login-status'] = ws_ls_force_bool_argument($chart_arguments['ignore-login-status']);

    // Validate height
    if (!is_numeric($chart_arguments['height']) || $chart_arguments['height'] < 50) {
       $chart_arguments['height'] = 250;
    }

    // Validate max points
    if (!is_numeric($chart_arguments['max-data-points']) || $chart_arguments['max-data-points'] < 2) {
       $chart_arguments['max-data-points'] = WE_LS_CHART_MAX_POINTS;
    }
    // Fetch data for chart
    $weight_data = ws_ls_get_weights($chart_arguments['user-id'], $chart_arguments['max-data-points'], -1, 'desc');

    // Reverse array so in cron order
	if(is_array($weight_data) && !empty($weight_data)) {
    	$weight_data = array_reverse($weight_data);
	}

    // Render chart
    if ($weight_data){
        return ws_ls_display_chart($weight_data, $chart_arguments);
    } else {
        return '<p>' . __('No weight data was found for the specified user.', WE_LS_SLUG) . '</p>';
    }
}
