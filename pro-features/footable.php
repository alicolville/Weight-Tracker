<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Render HTML for user entry table
 *
 * @param $arguments
 *
 * @return string
 */
function ws_ls_data_table_render( $arguments = [] ) {

	$arguments = wp_parse_args( $arguments, [   'user-id'                       => NULL,
	                                            'limit'                         => NULL,
	                                            'bmi-format'                    => 'label',
	                                            'smaller-width'                 => false,
	                                            'enable-add-edit'               => true,
												'enable-meta-fields'            => ( true === ws_ls_meta_fields_is_enabled() &&
												                                        ws_ls_meta_fields_number_of_enabled() > 0 ),
												'enable-bmi'                    => true,
												'enable-notes'                  => true,
												'enable-weight'                 => true,
												'page-size'                     => 10,
												'week'                          => NULL,
												'custom-field-col-size'         => NULL,
												'weight-mandatory'              => true,
												'custom-field-restrict-rows'    => '',      // Only fetch entries that have either all custom fields completed (all), one or more (any) or leave blank if not concerned.
	                                            'custom-field-groups'           => '',      // If specified, only show custom fields that are within these groups
												'custom-field-slugs'            => '',      // If specified, only show the custom fields that are specified
	] );

	ws_ls_data_table_enqueue_scripts();

	$html = '';

	// Saved data?
	if ( false === is_admin() ) {
		$html .= ws_ls_display_data_saved_message();
	}

	$html = '';
	$entry_id = ws_ls_querystring_value('ws-edit-entry', true);

	// Saved data?
	if (false === is_admin()) {
		$html = ws_ls_display_data_saved_message();
	}

	// Are we in front end and editing enabled, and of course we want to edit, then do so!
	if( false === empty( $entry_id ) && false === is_admin() ) {

		// If we have a Redirect URL, base decode.
		$redirect_url = ws_ls_querystring_value('redirect');

		if( false === empty( $redirect_url ) ) {
			$redirect_url = base64_decode( $redirect_url );
		}

		$html .= ws_ls_form_weight( [ 'entry-id' => $entry_id, 'redirect-url' => $redirect_url, 'weight-mandatory' => $arguments[ 'weight-mandatory' ],
		                                    'custom-field-groups' => $arguments[ 'custom-field-groups' ], 'custom-field-slugs' => $arguments[ 'custom-field-slugs' ],
		                                        'type' => ( false === ws_ls_to_bool( $arguments[ 'enable-weight' ] ) ) ? 'custom-fields' : 'weight' ] );

	} else {

		$html .= sprintf('<table class="ws-ls-user-data-ajax table ws-ls-loading-table ykuk-table" id="%1$s"
									data-paging="true"
									data-paging-size="%7$d"
									data-filtering="true"
									data-sorting="true"
									data-editing="%2$s"
									data-cascade="true"
									data-toggle="true"
									data-use-parent-width="true"
									data-user-id="%3$d",
									data-max-entries="%4$d"
									data-small-width="%5$s"
									data-enable-bmi="%12$s"
									data-enable-notes="%13$s"
									data-enable-weight="%14$s"
									data-enable-meta-fields="%6$s"
									data-week="%8$d"
									data-bmi-format="%9$s"
									data-custom-field-slugs="%10$s"
									data-custom-field-groups="%11$s"
									data-custom-field-col-size="%15$s"
									data-custom-field-restrict-rows="%16$s" >
		</table>',
			ws_ls_component_id(),
			true === $arguments[ 'enable-add-edit' ] ? 'true' : 'false',
			false === empty( $arguments[ 'user-id' ] ) ? $arguments[ 'user-id' ] : 'false',
			false === empty( $arguments[ 'limit' ] ) ? $arguments[ 'limit' ] : 'false',
			true === $arguments[ 'smaller-width' ] ? 'true' : 'false',
			true === $arguments[ 'enable-meta-fields' ] ? 'true' : 'false',
			$arguments[ 'page-size' ],
			$arguments[ 'week' ],
			esc_attr( $arguments[ 'bmi-format' ] ),
			esc_attr( $arguments[ 'custom-field-slugs' ] ),
			esc_attr( $arguments[ 'custom-field-groups' ] ),
			true === ws_ls_to_bool($arguments[ 'enable-bmi' ] ) ? 'true' : 'false',
			true === ws_ls_to_bool($arguments[ 'enable-notes' ] ) ? 'true' : 'false',
			true === ws_ls_to_bool( $arguments[ 'enable-weight' ] ) ? 'true' : 'false',
			esc_attr( $arguments[ 'custom-field-col-size' ] ),
			esc_attr( $arguments[ 'custom-field-restrict-rows' ] )
		);

		if ( true === empty( $arguments[ 'user-id' ] ) ) {
			$html .= sprintf( '<p><small>%s</small></p>', __( 'Please note: For performance reasons, this table will only update every 5 minutes.', WE_LS_SLUG ) );
		}
	}

	return $html;
}

/**
 * Fetch the rows for the data table
 * @param $arguments
 *
 * @return array|null
 */
function ws_ls_datatable_rows( $arguments ) {

	$arguments = wp_parse_args( $arguments, [	    'custom-field-restrict-rows'    => '',      // Should we SQL OR or AND each meta fields (i.e. OR means return any row that has one or more meta field populated, AND means all)
													'custom-field-value-exists'     => [],
													'user-id'                       => NULL,
	                                                'limit'                         => NULL,
	                                                'smaller-width'                 => false,
	                                                'week'                          => NULL,
	                                                'front-end'                     => false,
	                                                'sort'                          => 'desc',
	                                                'enable-bmi'                    => true,
	                                                'enable-meta'                   => true,
	                                                'enable-notes'                  => true,
	                                                'enable-weight'                 => true,
	                                                'bmi-format'                    => 'index',
												    'in-admin'                      => false    // Has this request come from the admin area (used to render dates differently)
	] );

	$arguments[ 'enable-bmi' ]      = ws_ls_to_bool( $arguments[ 'enable-bmi' ] );
	$arguments[ 'enable-notes' ]    = ws_ls_to_bool( $arguments[ 'enable-notes' ] );
	$arguments[ 'enable-weight' ]   = ws_ls_to_bool( $arguments[ 'enable-weight' ] );

	$cache_key  = ws_ls_cache_generate_key_from_array( 'footable', $arguments );
	$rows       = NULL;

	if ( $cache = ws_ls_cache_user_get( $arguments[ 'user-id' ], $cache_key ) ) {
		$rows = $cache;
	} else {

		$entries                = ws_ls_db_entries_get( $arguments );
		$rows                   = [];
		$previous_user_weight   = [];
		$user_name              = NULL;
		$user_profile_link      = '';

		if ( false === $arguments[ 'front-end' ] &&
		        false === empty( $arguments[ 'user-id' ] ) ) {
			$user_name          = ws_ls_user_display_name( $arguments[ 'user-id' ] ) ;
			$user_profile_link  = sprintf('<a href="%s" rel="noopener noreferrer" target="_blank">%s</a>', ws_ls_get_link_to_user_profile( $arguments[ 'user-id' ] ), $user_name );
		}

		$entries = array_reverse( $entries );

		foreach ( $entries as $entry ) {

			// Build a row up for given columns
			$row = [ 'date' => $entry[ 'weight_date' ], 'db_row_id' => $entry[ 'id' ], 'user_id' => $entry[ 'user_id' ] ];

			if ( false === $arguments[ 'front-end' ] ) {

				if ( null === $user_name ) {
					$entry[ 'user_name' ]    = ws_ls_user_display_name( $entry[ 'user_id' ] );
					$entry[ 'user_profile' ] = sprintf( '<a href="%s" rel="noopener noreferrer" target="_blank">%s</a>', ws_ls_get_link_to_user_profile( $entry['user_id'] ), $entry['user_name'] );
				} else {
					$entry[ 'user_name' ]    = $user_name;
					$entry[ 'user_profile' ] = $user_profile_link;
				}

				$row[ 'user_nicename' ] = [
					'options' => [ 'sortValue' => $entry[ 'user_name' ] ],
					'value'   => $entry[ 'user_profile' ]
				];
			}

			if( true === $arguments[ 'enable-weight' ] ) {
				// Compare to previous weight and determine if a gain / loss in weight
				$gain_loss = '';
				$gain_class = '';

				if ( true === empty( $entry[ 'kg' ] ) ) {
					$gain_class = 'same';
					$gain_loss = __( 'No weight recorded', WE_LS_SLUG );
				} elseif( false === empty( $previous_user_weight[ $entry[ 'user_id' ] ] ) ) {

					if ( false === empty( $entry[ 'kg' ] ) ) {
						$row[ 'previous-weight' ] = $previous_user_weight[ $entry[ 'user_id' ] ];

						if ( $entry['kg'] > $previous_user_weight[ $entry[ 'user_id' ] ] ) {
							$gain_class = 'gain';
						} elseif ( $entry[ 'kg' ] < $previous_user_weight[ $entry[ 'user_id' ] ] ) {
							$gain_class = 'loss';
						} elseif ( $entry['kg'] == $previous_user_weight[ $entry[ 'user_id' ] ] ) {
							$gain_class = 'same';
							$gain_loss = __( 'No Change', WE_LS_SLUG );
						}

						$row[ 'previous-weight-diff' ] = $entry['kg'] - $previous_user_weight[ $entry[ 'user_id' ] ];
					}

				} elseif ( true === empty( $arguments[ 'user-id' ] )) {
					$gain_loss = $entry[ 'user_profile' ] = sprintf('<a href="%s" rel="noopener noreferrer" target="_blank">%s</a>', ws_ls_get_link_to_user_profile( $entry[ 'user_id' ] ), __( 'Check record', WE_LS_SLUG ) );
				} elseif ( false === empty( $entry[ 'kg' ] ) ) {
					$gain_loss = __( 'First weight entry', WE_LS_SLUG );
				} else {
					$gain_loss = '';
				}

				if ( false === empty( $entry[ 'kg' ] ) ) {
					$previous_user_weight[ $entry[ 'user_id' ] ] = $entry[ 'kg' ];
				}

				$row[ 'gainloss' ][ 'value']                = $gain_loss;
				$row[ 'gainloss' ][ 'options']['classes']   = 'ws-ls-' . $gain_class .  ws_ls_blur();
			}
			if( true === $arguments[ 'enable-notes' ] ) {
				$row[ 'notes' ] = wp_kses_post( $entry[ 'notes' ] );
			}

			if( true === $arguments[ 'enable-bmi' ] &&
			        true === ws_ls_bmi_in_tables() ) {

				if ( false === empty( $entry[ 'kg' ] ) ) {
					$row[ 'bmi' ] = [   'value' => ws_ls_get_bmi_for_table( ws_ls_user_preferences_get( 'height', $entry[ 'user_id' ] ), $entry[ 'kg' ], __( 'No height', WE_LS_SLUG ), $arguments[ 'bmi-format'] ),
						'options' => [ 'classes' => '' ]
					];
				} else {
					$row[ 'bmi' ] = '';
				}
			}
			if( true === $arguments[ 'enable-weight' ] ) {
				$row[ 'kg' ] = [ 'value' => $entry['kg'], 'options' => [ 'classes' => ws_ls_blur(), 'sortValue' => $entry['kg'] ] ];
			}

			if ( true === $arguments[ 'enable-meta' ] &&
			        true === ws_ls_meta_fields_is_enabled() ) {

				$meta_data = ws_ls_meta( $entry['id'] );

				// Pluck to meta_id => value
				if ( false === empty( $meta_data ) ) {
					$meta_data = wp_list_pluck( $meta_data, 'value', 'meta_field_id' );

					foreach ( $meta_data as $key => $field ) {

						$row[ 'meta-' . $key ] = ws_ls_fields_display_field_value( $field, $key );
					}
				}
			}

			array_push( $rows, $row );
		}

		ws_ls_cache_user_set( $arguments[ 'user-id' ], $cache_key, $rows );
	}

	// Reverse the array so most recent entries are shown first (as default)
	$rows = array_reverse( $rows );

	// Localise the row for the user viewing
	$rows = array_map( 'ws_ls_datatable_rows_localise', $rows );

	return $rows;
}

/**
 * Take a table row and localise for the person viewing it
 * @param $row
 *
 * @return mixed
 */
function ws_ls_datatable_rows_localise( $row ) {

	global $ws_ls_request_from_admin_screen;

	if ( false === empty( $row[ 'previous-weight-diff' ] ) ) {
		$row[ 'gainloss' ][ 'value' ] = ws_ls_blur_text( ws_ls_weight_display( $row[ 'previous-weight-diff' ], NULL, 'display', $ws_ls_request_from_admin_screen, true ) );
	}

	if ( false === empty( $row[ 'kg' ][ 'value' ] ) ) {
		$row[ 'kg' ][ 'value' ] = ws_ls_blur_text( ws_ls_weight_display( $row[ 'kg' ][ 'value' ], NULL, 'display', $ws_ls_request_from_admin_screen ) );
	}

	return $row;
}

/**
 * Depending on settings, return relevant columns for data table
 *
 * @param array $arguments
 *
 * @return array - column definitions
 */
function ws_ls_datatable_columns( $arguments = [] ) {

	$arguments = wp_parse_args( $arguments, [   'small-width'           => false,
	                                            'front-end'             => false,
	                                            'enable-bmi'            => true,
	                                            'enable-meta'           => true,
	                                            'enable-notes'          => true,
	                                            'enable-weight'         => true,
	                                            'custom-field-col-size' => NULL,
	                                            'custom-field-slugs'    => NULL,
	                                            'custom-field-groups'   => NULL
	] );

	$arguments[ 'enable-bmi' ]      = ws_ls_to_bool( $arguments[ 'enable-bmi' ] );
	$arguments[ 'enable-notes' ]    = ws_ls_to_bool( $arguments[ 'enable-notes' ] );
	$arguments[ 'enable-weight' ]   = ws_ls_to_bool( $arguments[ 'enable-weight' ] );

	$columns = [
					[ 'name' => 'db_row_id', 'title' => 'ID', 'visible' => false, 'type' => 'number' ],
					[ 'name' => 'user_id', 'title' => 'USER ID', 'visible' => false, 'type' => 'number' ]
	];

	// If not front end, add nice nice name
	if ( false === $arguments[ 'front-end' ] ) {
		$columns[] = [ 'name' => 'user_nicename', 'title' => __( 'User', WE_LS_SLUG ), 'breakpoints'=> '', 'type' => 'text' ];
	} else {
		// If in the front end, switch to smaller width (hide meta fields etc)
		$arguments[ 'small-width' ] = $arguments[ 'front-end' ];
	}

	$columns[] = [ 'name' => 'date', 'title' => __( 'Date', WE_LS_SLUG ), 'breakpoints'=> '', 'type' => 'date' ];

	if ( true === $arguments[ 'enable-weight' ] ) {
		$columns[] = [ 'name' => 'kg', 'title' => __( 'Weight', WE_LS_SLUG ), 'visible'=> true, 'type' => 'text' ];
		$columns[] = [ 'name' => 'gainloss', 'title' => ws_ls_tooltip('+/-', __( 'Difference', WE_LS_SLUG ) ), 'visible'=> true, 'breakpoints'=> 'xs', 'type' => 'text' ];
	}

	// Add BMI?
	if( true === $arguments[ 'enable-bmi' ] &&
	        true === ws_ls_bmi_in_tables() ) {
		array_push($columns, [ 'name' => 'bmi', 'title' => ws_ls_tooltip( __( 'BMI', WE_LS_SLUG ), __( 'Body Mass Index', WE_LS_SLUG ) ), 'breakpoints'=> 'xs', 'type' => 'text' ] );
	}

    if ( true === $arguments[ 'enable-meta' ] &&
            true === ws_ls_meta_fields_is_enabled() ) {

	    // Custom field filtering?
	    $custom_field_groups    = ws_ls_meta_fields_groups_slugs_to_ids( $arguments[ 'custom-field-groups' ] );
	    $custom_field_slugs     = ws_ls_meta_fields_slugs_to_ids( $arguments[ 'custom-field-slugs' ] );
	    $filter_by_group        = ( false === empty( $custom_field_groups) );
	    $filter_by_id           = ( false === empty( $custom_field_slugs ) );

	    $column_size = ( false === empty( $arguments[ 'custom-field-col-size' ] ) ) ?
		                    $column_size = $arguments[ 'custom-field-col-size' ] :
		                        'lg';

	    foreach ( ws_ls_meta_fields_enabled() as $field ) {
        	if ( true === apply_filters( 'wlt-filter-column-include', true, $field ) ) {

		        // Filter by ID?
		        if ( true === $filter_by_id &&
		             false === in_array( $field[ 'id' ], $custom_field_slugs ) ) {
			        continue;
		        }

		        // Filter by group?
		        if ( true === $filter_by_group &&
		             ( 0 === (int) $field[ 'group_id' ] || false === in_array( $field[ 'group_id' ], $custom_field_groups ) ) ) {
			        continue;
		        }

		        array_push($columns, [ 'name' => 'meta-' . $field['id'], 'title' => stripslashes( $field['field_name'] ), 'breakpoints'=> $column_size, 'type' => 'text' ] );
			}
        }
    }

	if ( true === $arguments[ 'enable-notes' ] ) {
		array_push($columns, [ 'name' => 'notes', 'title' => __( 'Notes', WE_LS_SLUG ), 'breakpoints'=> 'lg', 'type' => 'text' ] );
	}

	$columns = apply_filters( 'wlt-filter-front-end-data-table-columns', $columns );

	return $columns;
}


/**
 * Enqueue relevant CSS / JS when needed to make footables work
 */
function ws_ls_data_table_enqueue_scripts() {

	$minified = ws_ls_use_minified();

	wp_enqueue_style('ws-ls-footables', plugins_url( '/assets/css/libraries/footable.standalone.min.css', __DIR__  ), [], WE_LS_CURRENT_VERSION);
    wp_enqueue_style('ws-ls-footables-wlt', plugins_url( '/assets/css/footable' . $minified . '.css', __DIR__ ), [ 'ws-ls-footables' ], WE_LS_CURRENT_VERSION);
    wp_enqueue_script('ws-ls-footables-js', plugins_url( '/assets/js/libraries/footable.min.js', __DIR__ ), [ 'jquery' ], WE_LS_CURRENT_VERSION, true);
	wp_enqueue_script('ws-ls-footables-admin', plugins_url( '/assets/js/data.footable' .     $minified . '.js', __DIR__ ), [ 'ws-ls-footables-js' ], WE_LS_CURRENT_VERSION, true);
	wp_localize_script('ws-ls-footables-admin', 'ws_user_table_config', ws_ls_data_js_config() );
    wp_enqueue_style('fontawesome', WE_LS_CDN_FONT_AWESOME_CSS, [], WE_LS_CURRENT_VERSION);
}

/**
 * Used to embed config settings for jQuery front end
 * @return array of settings
 */
function ws_ls_data_js_config() {
	$config =   [   'is-admin'                      => is_admin(),
					'security'                      => wp_create_nonce( 'ws-ls-user-tables' ),
					'base-url'                      => ws_ls_get_link_to_user_data(),
					'base-url-meta-fields'          => ws_ls_meta_fields_base_url(),
					'base-url-awards'               => ws_ls_awards_base_url(),
					'label-add'                     =>  __( 'Add' , WE_LS_SLUG ),
                    'label-meta-fields-add-button'  =>  __( 'Add Custom Field', WE_LS_SLUG ),
					'label-awards-add-button'       =>  __( 'Add Award', WE_LS_SLUG ),
					'label-confirm-delete'          =>  __( 'Are you sure you want to delete the row?', WE_LS_SLUG ),
					'label-error-delete'            =>  __( 'Unfortunately there was an error deleting the row.', WE_LS_SLUG ),
                    'locale-search-text'            =>  __( 'Search', WE_LS_SLUG ),
					'locale-no-results'             =>  __( 'No data found', WE_LS_SLUG ),
					'hide-display-name'             => false
				];
	// Add some extra config settings if not in admin
    if ( false === is_admin() ) {

    	$config[ 'front-end' ]              = 'true';
        $config[ 'ajax-url' ]               = admin_url('admin-ajax.php');
        $edit_link                          = ws_ls_get_url();

        // Strip old edit and cancel QS values
		$edit_link                          = remove_query_arg( ['ws-edit-entry', 'ws-edit-cancel', 'ws-edit-saved'], $edit_link );

		$config[ 'edit-url' ]               = esc_url( add_query_arg( 'ws-edit-entry', '|ws-id|', $edit_link ) );
		$config[ 'current-url-base64' ]     = add_query_arg( 'ws-edit-saved', 'true', $edit_link );
		$config[ 'current-url-base64' ]     = base64_encode($config['current-url-base64']);
        $config[ 'us-date' ]                = ( false === ws_ls_setting('use-us-dates', get_current_user_id() ) ) ? 'false' : 'true';

    } else {
		$config[ 'current-url-base64' ]     = ws_ls_get_url( true );
        $config[ 'us-date' ]                = ( true == ws_ls_settings_date_is_us() ) ? 'true' : 'false';

	    // Have we detected were in Admin, on a user profile?
	    if ( true === ws_ls_datatable_is_user_profile() ) {
		    $config[ 'front-end' ]  = 'true';
	    }
    }

	return $config;
}

/**
 * Are we on a user profile page?
 * @return bool
 */
function ws_ls_datatable_is_user_profile() {
	return ( 'ws-ls-data-home' === ws_ls_querystring_value( 'page' ) && 'user' === ws_ls_querystring_value( 'mode' ) );
}
