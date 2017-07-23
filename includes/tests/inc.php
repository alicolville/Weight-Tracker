<?php

defined('ABSPATH') or die('Jog on.');

include WS_LS_ABSPATH . 'includes/tests/create-test-data.php';

function ws_run_tests(){

	// Add QS value of ?tests=y to allow tests to run

	if(!isset($_GET['tests'])) {
		return;
	}

	// Add users to WP and add some weight / measurement entries
	//  ws_ls_test_create_test_data($number_of_users_to_add = 0, $max_number_of_entries_per_user = 10)
	ws_ls_test_create_test_data(20, 20);


}
add_action( 'admin_init', 'ws_run_tests' );
