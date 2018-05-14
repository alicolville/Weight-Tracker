<?php

defined('ABSPATH') or die("Jog on!");


function ws_ls_gravity_forms_process( $entry, $form ) {

	$prefix = ws_ls_gravity_forms_prefix();
	$matched_fields = [];

	// Ensure user is logged in
	if ( false === is_user_logged_in() ) {
		return;
	}

	// Extract any fields from the GF Post that we maybe interested in!
	foreach ( $form['fields'] as $field ) {
		// Field we're interested in?
		if ( $wlt_field = ws_ls_gravity_forms_identify_field( $field['cssClass'] ) ) {
			$matched_fields[ $wlt_field ] = $entry[ $field['id'] ];
		}
	}

	// Do we an entry date?
	if ( false === array_key_exists( $prefix . 'date', $matched_fields ) ) {
		return;
	}

    // --------------------------------------------------------------------------------
    // Calculate weight in different versions.
    // --------------------------------------------------------------------------------

    $weight = [];

    if ( array_key_exists( $prefix . 'kg', $matched_fields ) && is_numeric( $matched_fields[ $prefix . 'kg' ] ) ) {

        $weight['kg'] = $matched_fields['wlt-kg'];

        $weight['only_pounds'] = ws_ls_to_lb( $weight['kg'] );

        $conversion = ws_ls_to_stone_pounds( $weight['kg'] );
        $weight['stones'] = $conversion['stones'];
        $weight['pounds'] = $conversion['pounds'];

	} elseif ( array_key_exists( $prefix . 'pounds', $matched_fields ) && array_key_exists( $prefix . 'stones', $matched_fields )
                    && is_numeric( $matched_fields[ $prefix . 'pounds' ] )  && is_numeric( $matched_fields[ $prefix . 'stones' ] )
                ) {

        $weight['stones'] = $matched_fields[ $prefix . 'stones' ];
        $weight['pounds'] = $matched_fields[ $prefix . 'pounds' ];

        $weight['kg'] = ws_ls_to_kg( $weight['stones'], $weight['pounds'] );
        $weight['only_pounds'] = ws_ls_stones_pounds_to_pounds_only( $weight['stones'], $weight['pounds'] );

	} elseif ( array_key_exists( $prefix . 'pounds', $matched_fields ) && is_numeric( $matched_fields[ $prefix . 'pounds' ] ) ) {

        $weight['kg'] = ws_ls_pounds_to_kg( $matched_fields[ $prefix . 'pounds' ] );

        $conversion = ws_ls_pounds_to_stone_pounds( $matched_fields[ $prefix . 'pounds' ] );
        $weight['stones'] = $conversion[ 'stones' ];
        $weight['pounds'] = $conversion[ 'pounds' ];

        $weight['only_pounds'] = $matched_fields[ $prefix . 'pounds' ];
	}

    // Have we got a weight?
	if ( 4 !== count( $weight ) ) {
		return;
	}

    // --------------------------------------------------------------------------------
    // Add Measurements
    // --------------------------------------------------------------------------------

	$measurements = false; //TODO
	$user_id = get_current_user_id();

    // --------------------------------------------------------------------------------
    // Add additional fields
    // --------------------------------------------------------------------------------

    $weight['date'] = $matched_fields['wlt-date'];

    // Do we have a weight entry?
    $weight['notes'] = ( false === empty( $matched_fields['wlt-notes'] ) ? $matched_fields['wlt-notes'] : '' );

	// Add weight entry!
    ws_ls_save_data($user_id, $weight, $is_target_form = false );


}
add_action( 'gform_after_submission', 'ws_ls_gravity_forms_process', 10, 2 );

/**
 * Determine the WLT field we maybe looking at
 *
 * @param $css_class
 * @return bool
 */
function ws_ls_gravity_forms_identify_field( $css_class ) {

    if ( false === empty( $css_class ) ) {

        $keys = ws_ls_gravity_forms_keys();

        foreach ( $keys as $key ) {

            if ( false !== strpos( $css_class, $key ) ) {
                echo $key;
                return $key;
            }

        }
    }

    return false;
}

/**
 * Keys to identify
 */
function ws_ls_gravity_forms_keys() {

    $prefix = ws_ls_gravity_forms_prefix();

    $keys = [];
    $keys[] = $prefix . 'kg';
    $keys[] = $prefix . 'pounds';
    $keys[] = $prefix . 'stones';
    $keys[] = $prefix . 'date';
    $keys[] = $prefix . 'notes';

    return $keys;
}

/**
 * Prefix used to identify WLT field in GF
 * @return mixed
 */
function ws_ls_gravity_forms_prefix( ) {
    return apply_filters( 'wlt-filter-gf-prefix', 'wlt-' );
}