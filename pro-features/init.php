<?php

defined('ABSPATH') or die("Jog on!");

// Include relevant files
if(defined('WS_LS_ABSPATH')){
  include WS_LS_ABSPATH . 'pro-features/user-preferences.php';
  include WS_LS_ABSPATH . 'pro-features/ajax-handler-public.php';
  include WS_LS_ABSPATH . 'pro-features/shortcode-chart.php';
  include WS_LS_ABSPATH . 'pro-features/shortcode-form.php';
  include WS_LS_ABSPATH . 'pro-features/advanced_table.php';
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

function ws_ls_enqeue_pro_scripts(){

  if(WS_LS_ADVANCED_TABLES) {

    //TODO: Tidy up
  //  wp_enqueue_style('ws-ls-datatables', plugins_url( '../pro-features/DataTables/datatables.min.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);
    //wp_enqueue_style('ws-ls-datatables-responsive', plugins_url( '../pro-features/DataTables/Responsive-1.0.7/css/responsive.dataTables.min.css', __FILE__ ), array(), WE_LS_CURRENT_VERSION);
  //  wp_enqueue_script('ws-ls-datatables', plugins_url( '../pro-features/DataTables/datatables.min.js', __FILE__ ), array('jquery'), WE_LS_CURRENT_VERSION);


    //wp_enqueue_script('ws-ls-datatables-moment', '//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js', array('jquery'), WE_LS_CURRENT_VERSION);
    //wp_enqueue_script('ws-ls-datatables-moment-date', '//cdn.datatables.net/plug-ins/1.10.7/sorting/datetime-moment.js', array('jquery'), WE_LS_CURRENT_VERSION);

    //wp_enqueue_script('ws-ls-datatables', 'https://cdn.datatables.net/1.10.9/js/jquery.dataTables.min.js', array('jquery'), WE_LS_CURRENT_VERSION);
    wp_enqueue_script('ws-ls-datatables-responsive', 'https://cdn.datatables.net/r/dt/dt-1.10.9,r-1.0.7/datatables.min.js', array('jquery'), WE_LS_CURRENT_VERSION);

    //wp_enqueue_style('ws-ls-datatables',  'https://cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css', array(), WE_LS_CURRENT_VERSION);
    wp_enqueue_style('ws-ls-datatables-responsive', 'https://cdn.datatables.net/r/dt/dt-1.10.9,r-1.0.7/datatables.min.css', array(), WE_LS_CURRENT_VERSION);

  }
}
add_action( 'wp_enqueue_scripts', 'ws_ls_enqeue_pro_scripts');
