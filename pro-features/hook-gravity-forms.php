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

	GFCommon::log_debug( 'Fields in GF form: ' . print_r( $form['fields'] , true ) );

	// Extract any fields from the GF Post that we maybe interested in!
	foreach ( $form['fields'] as $field ) {

		// Newer versions of GF store the $fields as arrays
		$field = (array) $field;

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

		$weight[ 'weight_weight' ] = $matched_fields['wlt-kg'];

	} elseif ( array_key_exists( $prefix . 'pounds', $matched_fields ) && array_key_exists( $prefix . 'stones', $matched_fields )
                    && is_numeric( $matched_fields[ $prefix . 'pounds' ] )  && is_numeric( $matched_fields[ $prefix . 'stones' ] )
                ) {

		$weight[ 'weight_weight' ] = ws_ls_convert_stones_pounds_to_kg( $matched_fields[ $prefix . 'stones' ], $matched_fields[ $prefix . 'pounds' ] );

	} elseif ( array_key_exists( $prefix . 'pounds', $matched_fields ) && is_numeric( $matched_fields[ $prefix . 'pounds' ] ) ) {

		$weight[ 'weight_weight' ] = ws_ls_convert_pounds_to_kg( $matched_fields[ $prefix . 'pounds' ] );

	}

    // Have we got a weight?
	if ( true === empty( $weight[ 'weight_weight' ] ) ) {
        GFCommon::log_debug( 'Could not calculate the weight (in KG) correctly.' );
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

    $meta = [];

    if ( true === ws_ls_meta_fields_is_enabled() ) {

		$enabled_meta_fields = ws_ls_meta_fields_enabled();

		GFCommon::log_debug( 'Looking for the following meta fields: ' . print_r( $enabled_meta_fields, true ) );

        foreach ( $enabled_meta_fields as $meta_field ) {

            $gf_field_key = ws_ls_gravity_forms_meta_fields_key_prefix( $meta_field['field_key'] );

            if ( false === empty( $matched_fields[ $gf_field_key ] ) ) {

            	// Photo?
				if ( 3 === (int) $meta_field['field_type'] ) {

					$photo_id = attachment_url_to_postid( $matched_fields[ $gf_field_key ] );

					$meta[ $meta_field['id'] ] = $photo_id;

					GFCommon::log_debug( sprintf('Adding photo %s ( %s ) to Weight Entry', $matched_fields['wlt-photo'], $photo_id ) );

				} else {

					$meta[ $meta_field['id'] ] = $matched_fields[ $gf_field_key ];

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

	$weight[ 'weight_date' ] = $matched_fields['wlt-date'] ;

	// Do we have a valid ISO date? Try and convert if not
	if ( false === ws_ls_iso_date_valid( $weight[ 'weight_date' ] ) ) {
		$weight[ 'weight_date' ] = ws_ls_convert_date_to_iso( $weight[ 'weight_date' ] );
	}

    // Do we have a weight entry?
    $weight[ 'weight_notes' ] = ( false === empty( $matched_fields['wlt-notes'] ) ? $matched_fields['wlt-notes'] : '' );

    $user_id = get_current_user_id();

    GFCommon::log_debug( 'Attempting to save entry into Weight Tracker for user ID: ' . get_current_user_id() );
    GFCommon::log_debug( 'Data trying to be saved: ' . print_r( $weight, true ) );

	// Add weight entry!
    $entry_id = ws_ls_db_entry_set( $weight , $user_id );

    if ( false === empty( $entry_id ) ) {

        GFCommon::log_debug( 'Weight entry saved!' );

		foreach ( $meta as $key => $value ) {

			$meta_field = [ 'entry_id' => $entry_id, 'key' => $key, 'value' => $value ];

			GFCommon::log_debug( 'Adding meta data: ' . print_r( $meta_field, true ) );

			ws_ls_meta_add_to_entry( $meta_field );
		}

        ws_ls_webhooks_manually_fire_weight( $user_id, $entry_id, 'add' );

    } else {
        GFCommon::log_debug( 'Weight entry did not save correctly :(' );
    }

    // Delete cache for the given user
    ws_ls_cache_user_delete( $user_id );
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

            if ( $css_class === $key ||
                    false !== strpos( $css_class, $key ) ) {
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

    $keys = array_merge( $keys, ws_ls_gravity_forms_weight_keys() );
    $keys = array_merge( $keys, ws_ls_gravity_forms_preferences_keys() );
    return array_merge( $keys, ws_ls_gravity_forms_meta_fields_keys() );
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

    return apply_filters( 'wlt-filters-gf-weight-keys', $keys );
}

/**
 * Preferences Keys
 */
function ws_ls_gravity_forms_preferences_keys() {

    $prefix = ws_ls_gravity_forms_prefix();

    $keys = [];

    $keys[] = $prefix . 'body-type';

    return apply_filters( 'wlt-filters-gf-preferences-keys', $keys );
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
