<?php

defined('ABSPATH') or die("Jog on!");

// Include relevant files
if(defined('WS_LS_ABSPATH')){
  include WS_LS_ABSPATH . 'pro-features/user-preferences.php';
  include WS_LS_ABSPATH . 'pro-features/ajax-handler-public.php';
  include WS_LS_ABSPATH . 'pro-features/shortcode-chart.php';  
  include WS_LS_ABSPATH . 'pro-features/shortcode-form.php';  
}

// Register shortcodes
function ws_ls_register_pro_shortcodes(){

    /*
        [weight-loss-tracker-chart] - Displays a chart 
        [weight-loss-tracker-form] - Displays a form
    */

    add_shortcode( 'weight-loss-tracker-chart', 'ws_ls_shortcode_chart' ); 
    add_shortcode( 'weight-loss-tracker-form', 'ws_ls_shortcode_form' ); 
}
add_action( 'init', 'ws_ls_register_pro_shortcodes');
