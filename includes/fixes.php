<?php

defined('ABSPATH') or die('Jog on!');

function ws_ls_fix_to_kg() {

	if ( !current_user_can( 'manage_options' ) )  {
		return;
	}

	// Ensure we are in stones_pounds
	if ( 'stones_pounds' !== WE_LS_DATA_UNITS ) {
		return;
	}

	global $wpdb;
	$data_table = $wpdb->prefix . WE_LS_TABLENAME;
	$pref_table = $wpdb->prefix . WE_LS_USER_PREFERENCES_TABLENAME;
    $target_table = $wpdb->prefix . WE_LS_TARGETS_TABLENAME;

	$results = $wpdb->get_results("SELECT id, weight_stones, weight_pounds, weight_weight FROM $data_table where weight_user_id not in (
										SELECT user_id FROM $pref_table where settings like '%pounds_only%' or settings like '%kg%'
									)", ARRAY_A);

	$count = 0;

	foreach ($results as $result) {

		$new_kg = ws_ls_to_kg( $result['weight_stones'], $result['weight_pounds'] );

		if ( $new_kg <> $result['weight_weight']) {

			// echo '<p>Updating ' . $result['weight_stones'] .'st ' . $result['weight_pounds'] .'lbs from ' . $result['weight_weight'] . ' to ' . $new_kg;
			$wpdb->update( $data_table, ['weight_weight' => $new_kg], ['id' => $result['id']], ['%f'], ['%d'] );
			$count++;
		}
	}

	echo '<p>Data rows processed: ' . $count . '</p>';

    $results = $wpdb->get_results("SELECT id, target_weight_stones, target_weight_pounds, target_weight_weight FROM $target_table where weight_user_id not in (
										SELECT user_id FROM $pref_table where settings like '%pounds_only%' or settings like '%kg%'
									)", ARRAY_A);

    $count = 0;

    foreach ($results as $result) {

        $new_kg = ws_ls_to_kg( $result['target_weight_stones'], $result['target_weight_pounds'] );

        if ( $new_kg <> $result['target_weight_weight']) {

            // echo '<p>Updating ' . $result['target_weight_stones'] .'st ' . $result['target_weight_pounds'] .'lbs from ' . $result['target_weight_weight'] . ' to ' . $new_kg;
            $wpdb->update( $target_table, ['target_weight_weight' => $new_kg], ['id' => $result['id']], ['%f'], ['%d'] );
            $count++;
        }
    }

    echo '<p>Target rows processed: ' . $count . '</p>';

    ws_ls_delete_all_cache();
    ws_ls_stats_run_cron();

}