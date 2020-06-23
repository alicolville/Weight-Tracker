<?php

defined('ABSPATH') or die("Jog on!");

/**
 * This file contains code for deprecated functionality - or to support it.
 */

function ws_ls_migrate_measurements_into_meta_fields() {

	if ( false === WS_LS_IS_PRO ) {
		return;
	}

	$force_run = ( false === empty( $_GET[ 'custom-fields-migrate' ] ) );

	if ( false === $force_run && false === update_option( 'ws-ls-migrate-meta-fields-completed', 'y' ) ) {
		ws_ls_log_add('migration', 'Measurements have already been upgraded.' );
		return;
	}
	// Are measurements already enabled?
	if ( 'no' == get_option('ws-ls-allow-measurements', 'no' ) ) {
		ws_ls_log_add('migration', 'Measurements are not enabled. No need to migrate to custom fields.' );
		return;
	}

	ws_ls_log_add('migration', 'Measurements are enabled. Looking to migrate to custom fields.' );

	// Scan for enabled measurement fields.
	$measurements = get_option( 'ws-ls-measurement', false );

	if ( false === empty( $measurements['enabled'] ) ) {

		$keys = array_keys( $measurements['enabled'] );

		$order 		= 100;
		$unit 		= get_option( 'ws-ls-measurement-units', 'cm' );
		$mandatory	= ( 'yes' == get_option( 'ws-ls-measurements-mandatory', 'no' ) ) ? 2 : 1;

		global $wpdb;

		// Reset Migrate flag on DB
		$result = $wpdb->query ( 'Update ' . $table_name = $wpdb->prefix . WE_LS_TABLENAME . ' set migrate = 0' );

		foreach ( $keys as $key ) {

			// Load details for existing measurement fields
			$details = ws_ls_migrate_measurement_details( $key );

			// Key not found? Skip.
			if ( true === empty( $details ) ) {
				continue;
			}

			// If this key already exists, then assume we've added it already!
			$sanitized_key = ws_ls_meta_fields_key_sanitise( $details[ 'title' ] );

			if ( true === ws_ls_meta_fields_key_exist( $sanitized_key ) ) {
				ws_ls_log_add('migration', 'Skipping measurement. Found already in custom fields. ' . print_r( $sanitized_key, true ) );
				continue;
			}

			$field = [
						'field_type'	=> 0,
						'abv'			=> $details[ 'abv' ],
						'field_name'	=> $details[ 'title' ],
						'sort'			=> $order,
						'suffix'		=> $unit,
						'mandatory'		=> $mandatory,
						'enabled'		=> 2,
						'plot_on_graph'	=> 1,
						'plot_colour'	=> ( false === empty( $measurements[ 'colors' ][ $key ] ) ) ? $measurements[ 'colors' ][ $key ] : '#000000',
						'migrate'		=> 1
			];

			ws_ls_log_add('migration', 'Adding measurement. ' . print_r( $field, true ) );

			$meta_field_id = ws_ls_meta_fields_add( $field );

			if ( false !== $meta_field_id ) {
				ws_ls_log_add('migration', 'Success. ' . $key );

				$conversion = ( 'cm' === $unit ) ? '' : ' / 2.54';

				$result = $wpdb->query( 'INSERT INTO ' . $wpdb->prefix . WE_LS_MYSQL_META_ENTRY . ' ( entry_id, meta_field_id, value, migrate )
				SELECT id as entry_id, ' . (int) $meta_field_id  . ' as meta_field_id, ROUND( ' . $key . $conversion . ', 2 ) as value, 1 as migrate FROM ' . $table_name = $wpdb->prefix . WE_LS_TABLENAME . ' where ' . $key . ' is not null' );

				ws_ls_log_add('migration', sprintf( 'Migrating data across: %s, copied: %d', $key, $result ) );

			} else {
				ws_ls_log_add('migration', 'Fail. ' . $key );
			}

			$order += 10;
		}

		$result = $wpdb->get_var( 'select count(id) from ' . $table_name = $wpdb->prefix . WE_LS_TABLENAME . ' where migrate = 1' );

		ws_ls_log_add('migration', sprintf( 'Number of data rows identified for migration: %d', $result ) );

	}
}
add_action( 'ws-ls-migrate-old-measurements', 'ws_ls_migrate_measurements_into_meta_fields' );

/**
 * Return details about old measurement fields
 * @param $key
 * @return mixed|null
 */
function ws_ls_migrate_measurement_details( $key ) {

	$old_measurements = [
								'left_forearm' 	=> [ 'title' => __('Forearm - Left', WE_LS_SLUG), 'abv' => __('FL', WE_LS_SLUG) ],
								'right_forearm' => [ 'title' => __('Forearm - Right', WE_LS_SLUG), 'abv' => __('FR', WE_LS_SLUG) ],
								'left_bicep' 	=> [ 'title' => __('Biceps - Left', WE_LS_SLUG), 'abv' => __('BL', WE_LS_SLUG) ],
								'right_bicep' 	=> [ 'title' => __('Biceps - Right', WE_LS_SLUG), 'abv' => __('BR', WE_LS_SLUG) ],
								'left_calf' 	=> [ 'title' => __('Calf - Left', WE_LS_SLUG), 'abv' => __('CL', WE_LS_SLUG) ],
								'right_calf' 	=> [ 'title' => __('Calf - Right', WE_LS_SLUG), 'abv' => __('CR', WE_LS_SLUG) ],
								'left_thigh' 	=> [ 'title' => __('Thigh - Left', WE_LS_SLUG), 'abv' => __('TL', WE_LS_SLUG) ],
								'right_thigh' 	=> [ 'title' => __('Thigh - Right', WE_LS_SLUG), 'abv' => __('TR', WE_LS_SLUG) ],
								'waist' 		=> [ 'title' => __('Waist', WE_LS_SLUG), 'abv' => __('W', WE_LS_SLUG ) ],
								'bust_chest' 	=> [ 'title' => __('Bust / Chest', WE_LS_SLUG), 'abv' => __('BC', WE_LS_SLUG) ],
								'shoulders' 	=> [ 'title' => __('Shoulders', WE_LS_SLUG), 'abv' => __('S', WE_LS_SLUG) ],
								'buttocks' 		=> [ 'title' => __('Buttocks', WE_LS_SLUG), 'abv' => __('B', WE_LS_SLUG) ],
								'hips' 			=> [ 'title' => __('Hips', WE_LS_SLUG), 'abv' => __('HI', WE_LS_SLUG) ],
								'navel' 		=> [ 'title' => __('Navel', WE_LS_SLUG), 'abv' => __('NA', WE_LS_SLUG) ],
								'neck'			=> [ 'title' => __('Neck', WE_LS_SLUG), 'abv' => __('NE', WE_LS_SLUG) ]
	];

	return array_key_exists( $key, $old_measurements ) ? $old_measurements[ $key ] : NULL;
}

/**
 * Display a message when old shortcode names are used.
 */
function ws_ls_shortcode_old_names() {
	return ws_ls_display_blockquote( __( 'You are using an old shortcode. It is now deprecated but will exist under a different names. Please view the plugin documentation and use a suitable replacement: https://weight.yeken.uk/shortcodes/', WE_LS_SLUG) );
}
add_shortcode( 'weight-loss-tracker-chart', 'ws_ls_shortcode_old_names' );
add_shortcode( 'weightloss_target_weight', 'ws_ls_shortcode_old_names' );
add_shortcode( 'weightlosstracker', 'ws_ls_shortcode_old_names' );
add_shortcode( 'weightloss_weight_start', 'ws_ls_shortcode_old_names' );
add_shortcode( 'weightloss_weight_most_recent', 'ws_ls_shortcode_old_names' );
add_shortcode( 'weightloss_weight_difference_from_target', 'ws_ls_shortcode_old_names' );
add_shortcode( 'weightlosstracker', 'ws_ls_shortcode_old_names' );
add_shortcode( 'weightloss_weight_difference', 'ws_ls_shortcode_old_names' );
add_shortcode( 'weight-loss-tracker-most-recent-bmi', 'ws_ls_shortcode_old_names' );
add_shortcode( 'wlt-recent-bmi', 'ws_ls_shortcode_old_names' );
add_shortcode( 'weight-loss-tracker-form', 'ws_ls_shortcode_old_names' );
add_shortcode( 'weight-loss-tracker', 'ws_ls_shortcode_old_names' );
add_shortcode( 'weight-loss-tracker-table', 'ws_ls_shortcode_old_names' );
