<?php

defined('ABSPATH') or die("Jog on!");

/**
 *      Hook onto a Gravity Form submission. Scan the fields CSS classes for the following. If found, build up a weight entry
 *      to save against the current user.
 *
 *      [0] => wlt-date
        [1] => wlt-notes
        [2] => wlt-kg
        [3] => wlt-pounds
        [4] => wlt-stones
        [5] => wlt-left-forearm
        [6] => wlt-right-forearm
        [7] => wlt-left-bicep
        [8] => wlt-right-bicep
        [9] => wlt-left-calf
        [10] => wlt-right-calf
        [11] => wlt-left-thigh
        [12] => wlt-right-thigh
        [13] => wlt-waist
        [14] => wlt-shoulders
        [15] => wlt-height
        [16] => wlt-buttocks
        [17] => wlt-hips
        [18] => wlt-navel
        [19] => wlt-neck
 *      [20] => wlt-bust-chest
 *      [*] => wlt-meta-[meta key]
 *
 * @param $entry
 * @param $form
 */
function ws_ls_gravity_forms_process( $entry, $form ) {

	$prefix = ws_ls_gravity_forms_prefix();
	$matched_fields = [];

	// Ensure user is logged in
	if ( false === is_user_logged_in() ) {
        GFCommon::log_debug( 'Weight Tracker: User not logged in. Do not process form.' );
		return;
	}

    GFCommon::log_debug( 'Scanning form for these Weight Tracker fields ' . print_r( ws_ls_gravity_forms_keys() , true ) );

	// Extract any fields from the GF Post that we maybe interested in!
	foreach ( $form['fields'] as $field ) {
		// Field we're interested in?
		if ( $wlt_field = ws_ls_gravity_forms_identify_field( $field['cssClass'] ) ) {
			$matched_fields[ $wlt_field ] = $entry[ $field['id'] ];
		}
	}

    GFCommon::log_debug( 'Identified these fields: ' . print_r( $matched_fields , true ) );

    // --------------------------------------------------------------------------------
    // Validation
    // --------------------------------------------------------------------------------

    // Any fields of interest?
    if ( true === empty( $matched_fields ) ) {
        GFCommon::log_debug( 'No Weight Tracker fields were found.' );
    }

    $keys_to_ensure_numeric = ws_ls_gravity_forms_weight_keys();

    GFCommon::log_debug( 'If found, ensuring the following fields are numeric: ' . print_r( $keys_to_ensure_numeric, true) );

    // Are relevant fields are numeric?
    foreach ( $keys_to_ensure_numeric as $key ) {
        if ( false === empty( $matched_fields[ $key ] ) && false === is_numeric( $matched_fields[ $key ] ) ) {
            GFCommon::log_debug( sprintf('Weight Tracker field %s is not numeric: %s', $key, $matched_fields[ $key ] ) );
            return;
        }
    }

	// Do we an entry date?
	if ( false === array_key_exists( $prefix . 'date', $matched_fields ) || true === empty( $matched_fields[ $prefix . 'date' ] ) ) {
        GFCommon::log_debug( 'No Date was found for Weight Entry' );
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
        GFCommon::log_debug( 'Weight Entries were not calculated correctly.' );
		return;
	}

    // --------------------------------------------------------------------------------
    // Preferences
    // --------------------------------------------------------------------------------

    $preferences = ws_ls_gravity_forms_preferences_keys();

    foreach ($preferences as $key) {

        if( false === empty( $matched_fields[ $key ] ) ) {

            $db_key = str_replace( $prefix, '', $key);
            $db_key = str_replace( '-', '_', $db_key);

            ws_ls_set_user_preference( $db_key, $matched_fields[ $key ] );

        }
    }

	// --------------------------------------------------------------------------------
	// Meta Fields
	// --------------------------------------------------------------------------------

    $weight[ 'meta-keys' ] = [];

    if ( true === ws_ls_meta_fields_is_enabled() ) {

		$enabled_meta_fields = ws_ls_meta_fields_enabled();

		GFCommon::log_debug( 'Looking for the following meta fields: ' . print_r( $enabled_meta_fields, true ) );

        foreach ( $enabled_meta_fields as $meta_field ) {

            $gf_field_key = ws_ls_gravity_forms_meta_fields_key_prefix( $meta_field['field_key'] );

            if ( false === empty( $matched_fields[ $gf_field_key ] ) ) {

            	// Photo?
				if ( 3 === $meta_field['field_key'] ) {

					$photo_id = attachment_url_to_postid( $matched_fields[ $gf_field_key ] );

					$weight[ 'meta-keys' ][ $meta_field['id'] ] = $photo_id;

					GFCommon::log_debug( sprintf('Adding photo %s ( %s ) to Weight Entry', $matched_fields['wlt-photo'], $photo_id ) );

				} else {

					$weight[ 'meta-keys' ][ $meta_field['id'] ] = $matched_fields[ $gf_field_key ];

					GFCommon::log_debug( sprintf('Found meta field %s', $gf_field_key ) );
				}

            } else {
                GFCommon::log_debug( sprintf('Could not find meta field %s', $gf_field_key ) );
            }
        }
    }

    // --------------------------------------------------------------------------------
    // Add additional fields
    // --------------------------------------------------------------------------------

    $weight['date'] = $matched_fields['wlt-date'];

    // Do we have a weight entry?
    $weight['notes'] = ( false === empty( $matched_fields['wlt-notes'] ) ? $matched_fields['wlt-notes'] : '' );

    $user_id = get_current_user_id();

    GFCommon::log_debug( 'Attempting to save entry into Weight Tracker for user ID: ' . get_current_user_id() );
    GFCommon::log_debug( 'Data trying to be saved: ' . print_r( $weight, true ) );

	// Add weight entry!
    $result = ws_ls_save_data( $user_id , $weight, $is_target_form = false );

    if ( true === $result ) {
        GFCommon::log_debug( 'Weight entry saved!' );
    } else {
        GFCommon::log_debug( 'Weight entry did not save correctly :(' );
    }


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
                return $key;
            }

        }
    }

    return false;
}

/**
 * Keys to look out for in Gravity forms
 */
function ws_ls_gravity_forms_keys() {

    $prefix = ws_ls_gravity_forms_prefix();

    $keys = [];
    $keys[] = $prefix . 'date';
    $keys[] = $prefix . 'notes';
    $keys[] = $prefix . 'photo';

    $keys = array_merge( $keys, ws_ls_gravity_forms_weight_keys() );
    $keys = array_merge( $keys, ws_ls_gravity_forms_preferences_keys() );
    $keys = array_merge( $keys, ws_ls_gravity_forms_meta_fields_keys() );

    return $keys;
}

/**
 * Weight Keys
 */
function ws_ls_gravity_forms_weight_keys() {

    $prefix = ws_ls_gravity_forms_prefix();

    $keys = [];
    $keys[] = $prefix . 'kg';
    $keys[] = $prefix . 'pounds';
    $keys[] = $prefix . 'stones';

    $keys = apply_filters( 'wlt-filters-gf-weight-keys', $keys );

    return $keys;
}

/**
 * Preferences Keys
 */
function ws_ls_gravity_forms_preferences_keys() {

    $prefix = ws_ls_gravity_forms_prefix();

    $keys = [];

    $keys[] = $prefix . 'body-type';

    $keys = apply_filters( 'wlt-filters-gf-preferences-keys', $keys );

    return $keys;
}

/**
 * Build meta field keys
 *
 * @return array
 */
function ws_ls_gravity_forms_meta_fields_keys() {

    $keys = [];

    if ( true === ws_ls_meta_fields_is_enabled() ) {

        $meta_fields = ws_ls_meta_fields_enabled();

        if ( false === empty( $meta_fields ) ) {
            $keys = wp_list_pluck( $meta_fields, 'field_key');
            $keys = array_map( 'ws_ls_gravity_forms_meta_fields_key_prefix', $keys );
        }
    }

    return $keys;
}

function ws_ls_gravity_forms_meta_fields_key_prefix( $key ) {
    return 'wlt-meta-' . $key;
}

/**
 * Prefix used to identify WLT field in GF
 * @return mixed
 */
function ws_ls_gravity_forms_prefix( ) {
    return apply_filters( 'wlt-filter-gf-prefix', 'wlt-' );
}
