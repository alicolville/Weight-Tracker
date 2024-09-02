<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Display summary boxes
 * @param $arguments
 * @param array $boxes
 *
 * @return string
 */
function ws_ls_uikit_summary_boxes( $arguments, $boxes = [] ) {

	// Default box selection
	if ( true === empty( $boxes ) ) {
		$boxes = [ 'number-of-entries', 'number-of-days-tracking', 'latest-weight', 'start-weight' ];
	}

	if ( true === empty( $boxes ) ) {
		return '<!-- No valid summary boxes -->';
	}

	$arguments      = wp_parse_args( $arguments, [ 'user-id' => get_current_user_id(), 'breakpoint_s' => 2 ] );
	$no_boxes       = count( $boxes );

	$breakpoint_m = min( $no_boxes, 4 );
	$breakpoint_s = $no_boxes < 3 ? $no_boxes : (int) $arguments[ 'breakpoint_s' ];

	$divider_count = 0;

	$html = ws_ls_uikit_open_grid( $breakpoint_s, $breakpoint_m, $divider_count );

	foreach ( $boxes as $box ) {

        $custom_field = ws_ls_component_is_custom_field( $box );

        if ( false !== $custom_field ) {

            $html .= ws_ls_component_custom_field_render( [ 'custom-field'  => $custom_field, 'user-id' => $arguments[ 'user-id' ] ] );
            continue;
        }

		switch ( $box ) {
			case 'weight-difference-since-previous':
				$html .= ws_ls_component_weight_difference_since_previous( [ 'user-id'   => $arguments[ 'user-id' ] ] );
				break;
			case 'gender':
				$html .= ws_ls_component_user_setting( [    'user-id'   => $arguments[ 'user-id' ],
				                                            'title'     => esc_html__( 'Gender', WE_LS_SLUG ) ,
				                                            'setting'   => 'gender'
				]);
				break;
			case 'aim':
				$html .= ws_ls_component_user_setting( [    'user-id'   => $arguments[ 'user-id' ],
				                                            'title'     => esc_html__( 'Aim', WE_LS_SLUG ) ,
				                                            'setting'   => 'aim'
				]);
				break;
			case 'activity-level':
				$html .= ws_ls_component_user_setting( [    'user-id'   => $arguments[ 'user-id' ],
				                                            'title'     => esc_html__( 'Activity Level', WE_LS_SLUG ),
															'setting'   => 'activity_level'
				]);
				break;
			case 'calories-lose':
				$html .= ws_ls_component_calories( [ 'user-id' => $arguments[ 'user-id' ] ] );
				break;
			case 'calories-maintain':
				$html .= ws_ls_component_calories( [ 'user-id' => $arguments[ 'user-id' ], 'progress' => 'maintain' ] );
				break;
			case 'calories-gain':
				$html .= ws_ls_component_calories( [ 'user-id' => $arguments[ 'user-id' ], 'progress' => 'gain' ] );
				break;
			case 'calories-auto':
				$html .= ws_ls_component_calories( [ 'user-id' => $arguments[ 'user-id' ], 'progress' => 'auto' ] );
				break;
			case 'group':
				$html .= ws_ls_component_user_setting( [    'user-id'   => $arguments[ 'user-id' ],
				                                            'title'     => esc_html__( 'Group', WE_LS_SLUG ),
				                                            'setting'   => 'group'
				]);
				break;
			case 'height':
				$html .= ws_ls_component_user_setting( [ 'user-id'   => $arguments[ 'user-id' ] ] );
				break;
			case 'number-of-entries':
				$html .= ws_ls_component_number_of_entries( [ 'user-id' => $arguments[ 'user-id' ] ] );
				break;
			case 'number-of-weight-entries':
				$html .= ws_ls_component_number_of_weight_entries( [ 'user-id' => $arguments[ 'user-id' ] ] );
				break;
			case 'divider':

					$html .= '		<div class="ykuk-divider-icon ykuk-width-1-1"></div>
								</div>';

					$divider_count++;

					$html .= ws_ls_uikit_open_grid( $breakpoint_s, $breakpoint_m,  $divider_count );
				break;
			case 'number-of-days-tracking':
				$html .= ws_ls_component_number_of_days_tracking( [ 'user-id' => $arguments[ 'user-id' ] ] );
				break;
			case 'latest-weight':
			case 'latest-weight-difference-as-percentage':
				$html .= ws_ls_component_latest_weight( [ 'user-id' => $arguments[ 'user-id' ] ] );
				break;
			case 'latest-weight-difference-as-weight':
				$html .= ws_ls_component_latest_weight( [ 'user-id' => $arguments[ 'user-id' ], 'difference-display' => 'weight' ] );
				break;	
			case 'latest-award':
				$html .= ws_ls_component_latest_award( [ 'user-id' => $arguments[ 'user-id' ] ] );
				break;
			case 'number-of-awards':
				$html .= ws_ls_component_number_of_awards( [ 'user-id' => $arguments[ 'user-id' ] ] );
				break;
			case 'start-weight':
				$html .= ws_ls_component_start_weight( [ 'user-id' => $arguments[ 'user-id' ] ] );
				break;
			case 'target-weight':
				$html .= ws_ls_component_target_weight( [ 'user-id' => $arguments[ 'user-id' ] ] );
				break;
			case 'previous-weight':
				$html .= ws_ls_component_previous_weight( [ 'user-id' => $arguments[ 'user-id' ] ] );
				break;
			case 'latest-versus-target':
				$html .= ws_ls_component_latest_versus_another( [ 'user-id' => $arguments[ 'user-id' ] ] );
				break;
			case 'latest-versus-start':
				$html .= ws_ls_component_latest_versus_another( [   'user-id'               => $arguments[ 'user-id' ],
																	'compare-against'       => 'start',
																	'compare-missing-text'  => esc_html__( 'Missing data', WE_LS_SLUG ),
	                                                                'title'                 => esc_html__( 'Latest vs Start', WE_LS_SLUG )
				]);
				break;
			case 'bmi':
				$html .= ws_ls_component_bmi( [ 'bmi-type'  => 'current', 'user-id' => $arguments[ 'user-id' ] ] );
				break;
			case 'start-bmi':
				$html .= ws_ls_component_bmi( [ 'bmi-type'  => 'start', 'user-id' => $arguments[ 'user-id' ] ] );
				break;
			case 'bmr':
				$html .= ws_ls_component_bmr( [ 'bmr-type'  => 'current', 'user-id' => $arguments[ 'user-id' ] ] );
				break;
			case 'start-bmr':
				$html .= ws_ls_component_bmr( [ 'bmr-type'  => 'start', 'user-id' => $arguments[ 'user-id' ] ] );
				break;
			case 'name-and-email':
				$html .= ws_ls_component_name_and_email( $arguments );
				break;
			case 'user-id':
				$html .= ws_ls_component_user_id( $arguments );
				break;
			case 'age-dob':
				$html .= ws_ls_component_age_dob( [ 'user-id' => $arguments[ 'user-id' ] ] );
				break;
		}

	}

	$html .= '</div>';

	return $html;
}

/**
 * Is the component specified a custom field?
 * @param $box
 * @return array|false
 */
function ws_ls_component_is_custom_field( $box ) {

    if ( false === strpos( $box, 'custom-field-' ) )  {
        return false;
    }

    $custom_field = [   'mode' => 'latest',
                        'slug' => str_replace( [ 'custom-field-latest-', 'custom-field-previous-', 'custom-field-oldest-' ], [ '', '', '' ], $box )
    ];

    if ( null === ws_ls_meta_fields_slug_to_id( $custom_field[ 'slug' ] ) ) {
        return false;
    }

    if ( false !== strpos( $box, 'custom-field-oldest-' ) ) {
        $custom_field[ 'mode' ] = 'oldest';
    } else if ( false !== strpos( $box, 'custom-field-previous' ) )  {
        $custom_field[ 'mode' ] = 'previous';
    }

    return $custom_field;
}

/**
 * Render a custom field component
 * @param $args
 * @return string
 */
function ws_ls_component_custom_field_render( $args) {

    if ( true === empty( $args ) ) {
        return '';
    }

    $args           = wp_parse_args( $args, [ 'custom-field' => $args, 'user-id' => get_current_user_id() ] );
    $custom_field   = ws_ls_meta_fields_shortcode_value_latest( [  'slug' => $args[ 'custom-field' ]['slug'], 'user-id' => $args[ 'user-id' ], 'which' => $args[ 'custom-field' ]['mode'], 'return-as-array' => true ] );

    $title  = sprintf( '%s %s', ucwords( $args[ 'custom-field' ]['mode'] ), ws_ls_meta_fields_get_column( $custom_field[ 'id' ], 'field_name' ) );
    $value  = sprintf( '%s%s', $custom_field[ 'display' ], ws_ls_meta_fields_get_column( $custom_field[ 'id' ], 'suffix' ) );

    return sprintf( '<div>
                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
                                <span class="ykuk-info-box-header">%1$s</span><br />
                                <span class="ykuk-text-bold">
                                    %2$s
                                </span>
                        </div>
                    </div>',
        $title,
        $value
    );

}

/**
 * Component to display the user's latest weight
 * @param array $args
 *
 * @return string
 */
function ws_ls_component_latest_weight( $args = [] ) {

    $args           = wp_parse_args( $args, [ 'difference-display' => 'percentage', 'user-id' => get_current_user_id() ] );
    $latest_entry   = ws_ls_entry_get_latest( $args );

    $text_date      = '';
    $text_data      = esc_html__( 'No data', WE_LS_SLUG );

    if( false === empty( $latest_entry[ 'kg' ] ) ) {

        $text_data  = $latest_entry[ 'display' ];
        $text_date  = sprintf ( '<br />
									<span class="ykuk-info-box-meta">
										<a href="%s">%s</a>
									</span>', ws_ls_wt_link_edit_entry( $latest_entry[ 'id' ] ), $latest_entry[ 'display-date' ] );

        $difference = ws_ls_shortcode_difference_in_weight_previous_latest( [   'display'                   => $args[ 'difference-display' ],
                                                                                'include-percentage-sign'   => true,
	                                                                            'invert'                    => ( 'percentage' === $args[ 'difference-display' ] ),
                                                                                'user-id'                   => $args[ 'user-id']
        ] );

        if ( false === empty( $difference ) ) {

            $user_aim = (int) ws_ls_user_preferences_get( 'aim' );

	        if ( ( 2 === $user_aim && (float) $difference <= 0 ) ||
	                ( 3 === $user_aim && (float) $difference >= 0 ) ) {
	        	$class = 'ykuk-label-success';
	        } else {
		        $class = 'ykuk-label-warning';
	        }

            $text_data .= sprintf( ' <span class="ykuk-label %s ykuk-width-1-1" ykuk-tooltip="%s">%s</span>',
                                    $class,
                                    esc_html__( 'The difference between your latest weight and previous.', WE_LS_SLUG ),
                                    $difference
            );
        }
    }

    return sprintf( '<div>
                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
                                <span class="ykuk-info-box-header">%3$s</span><br />
                                <span class="ykuk-text-bold">
                                    %1$s
                                </span>
                                %2$s
                        </div>
                    </div>',
                    $text_data,
                    $text_date,
                    esc_html__( 'Latest Weight', WE_LS_SLUG )
    );
}

/**
 * Component to display the latest / previous
 * @param array $args
 *
 * @return string
 */
function ws_ls_component_weight_difference_since_previous( $args = [] ) {

	$args = wp_parse_args( $args, [ 'user-id' => get_current_user_id() ] );

	$text_data = ws_ls_shortcode_difference_in_weight_previous_latest( [    'display'                   => 'percentage',
	                                                                        'include-percentage-sign'   => false,
	                                                                        'invert'                    => false,
	                                                                        'user-id'                   => $args[ 'user-id'],
																			'kiosk-mode'                => true
	] );

	if ( true === empty( $text_data ) ) {
		$text_data = esc_html__( 'No data', WE_LS_SLUG );
	}

	return sprintf( '<div>
                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
                                <span class="ykuk-info-box-header">%2$s</span><br />
                                <span class="ykuk-text-bold">
                                    %1$s
                                </span>
                        </div>
                    </div>',
		$text_data,
		esc_html__( 'Latest / Previous', WE_LS_SLUG )
	);
}

/**
 * Display number of awards
 * @param array $args
 *
 * @return string
 */
function ws_ls_component_number_of_awards( $args = [] ) {

	$args = wp_parse_args( $args, [ 'user-id' => get_current_user_id() ] );

	return sprintf( '<div>
                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
                                <div class="ykuk-info-box-header">%2$s</div>
                                <div class="ykuk-text-bold ykuk-text-large ykuk-margin-top">
                                    %1$s
                                </div>
                        </div>
                    </div>',
		ws_ls_awards_count( $args[ 'user-id' ] ),
		esc_html__( 'No. of awards', WE_LS_SLUG )
	);
}

/**
 * Component to display the latest award
 * @param array $args
 */
function ws_ls_component_latest_award( $args = [] ) {

	$args           = wp_parse_args( $args, [ 'user-id' => get_current_user_id() ] );
	$awards         = ws_ls_awards_previous_awards( $args[ 'user-id' ], 50, 50, 'timestamp' );
	$html_thumbnail = '';
	$html_title     = esc_html__( 'n/a', WE_LS_SLUG );

	if ( false === empty( $awards[0] ) ) {

		$award = $awards[0];

		$thumbnail = NULL;

		if ( false === empty( $award[ 'thumb-with-url' ] ) ) {
			$thumbnail = $award[ 'thumb-with-url' ];
		} else if ( false === empty( $award[ 'thumb' ] ) ) {
			$thumbnail = $award[ 'thumb' ];
		}

		if ( false === empty( $award['url'] ) ) {
			$html_title = sprintf( '<a href="%s" target="_blank" rel="noopener">%s</a>', esc_url( $award['url'] ), esc_html( $award['title'] ) );
		} else {
			$html_title = esc_html( $award['title'] );
		}

		if ( false === empty( $thumbnail ) && false === $award['no-badge'] ) {
			$html_thumbnail = sprintf('<div class="ws-ls-award-latest-img">%s</div>', $thumbnail ) ;
		}
	}

	return sprintf( '<div>
                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
                                <span class="ykuk-info-box-header">%3$s</span><br />
                                <span class="ykuk-text-bold">
                                    %1$s
                                </span>
                                %2$s
                        </div>
                    </div>',
		$html_thumbnail,
		$html_title,
		esc_html__( 'Latest Award', WE_LS_SLUG )
	);

}
/**
 * Component to display the previous weight
 * @param array $args
 *
 * @return string
 */
function ws_ls_component_previous_weight( $args = [] ) {

	$args           = wp_parse_args( $args, [ 'user-id' => get_current_user_id() ] );
	$previous_entry = ws_ls_entry_get_previous( $args );

	$text_date      = '';
	$text_data      = esc_html__( 'No data', WE_LS_SLUG );

	if( false === empty( $previous_entry ) ) {

		$text_data  = $previous_entry[ 'display' ];
		$text_date  = sprintf ( '<br />
									<span class="ykuk-info-box-meta">
										<a href="%s">%s</a>
									</span>', ws_ls_wt_link_edit_entry( $previous_entry[ 'id' ] ), $previous_entry[ 'display-date' ] );

	}

	return sprintf( '<div>
                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
                                <span class="ykuk-info-box-header">%3$s</span><br />
                                <span class="ykuk-text-bold">
                                    %1$s
                                </span>
                                %2$s
                        </div>
                    </div>',
		$text_data,
		$text_date,
		esc_html__( 'Previous Weight', WE_LS_SLUG )
	);
}

/**
 * Component to display the target weight
 * @param array $args
 *
 * @return string
 */
function ws_ls_component_target_weight( $args = [] ) {

	$args           = wp_parse_args( $args, [ 'user-id' => get_current_user_id() ] );
	$target_weight  = ws_ls_target_get( $args[ 'user-id' ] );

	$text_date      = '';
	$text_data      = esc_html__( 'Not set', WE_LS_SLUG );

	if( false === empty( $target_weight ) ) {
		$text_data  = $target_weight[ 'display' ];
	}

	if ( false === ws_ls_targets_enabled() ) {
		$text_data = esc_html__( 'Targets not enabled in settings', WE_LS_SLUG );
	}

	return sprintf( '<div>
                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
                                <span class="ykuk-info-box-header %4$s">%3$s</span><br />
                                <span class="ykuk-text-bold">
                                    %1$s
                                </span>
                                %2$s
                                <br /><span class="ykuk-info-box-meta %4$s"><a href="#" class="ws-ls-tab-change" data-tab="settings">Adjust</a></span>
                        </div>
                    </div>',
		$text_data,
		$text_date,
		esc_html__( 'Target Weight', WE_LS_SLUG ),
		! ws_ls_targets_enabled() ? 'ws-ls-hide' : ''
	);
}

/**
 * Component to display a user settings
 * @param array $args
 *
 * @return string
 */
function ws_ls_component_user_setting( $args = [] ) {

	$args = wp_parse_args( $args, [ 'user-id' => get_current_user_id(), 'setting' => 'height', 'title' => esc_html__( 'Height', WE_LS_SLUG ) ] );

	if ( 'group' === $args[ 'setting' ] ) {
		$groups = ws_ls_groups_user( $args[ 'user-id'] );

		$setting = ( false === empty( $groups ) ) ? $groups[ 0 ][ 'name' ] : esc_html__( 'Not set', WE_LS_SLUG );

	} else {
		$setting = ws_ls_display_user_setting( $args[ 'user-id' ], $args[ 'setting' ], esc_html__( 'Not set', WE_LS_SLUG ), true );
	}

	return sprintf( '<div>
                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
                                <span class="ykuk-info-box-header">%2$s</span><br />
                                <span class="ykuk-text-bold">
                                    %1$s
                                </span>
                                <br /><span class="ykuk-info-box-meta"><a href="#" class="ws-ls-tab-change" data-tab="settings">Adjust</a></span>
                        </div>
                    </div>',
		esc_html( $setting ),
		esc_html( $args[ 'title' ] )
	);
}

/**
 * Component to display the start weight
 * @param array $args
 *
 * @return string
 */
function ws_ls_component_start_weight( $args = [] ) {

	$args           = wp_parse_args( $args, [ 'user-id' => get_current_user_id() ] );
	$start_weight   = ws_ls_entry_get_oldest( $args );

	$text_date      = '';
	$text_data      = esc_html__( 'Not set', WE_LS_SLUG );

	if( false === empty( $start_weight[ 'display' ] ) ) {
		$text_data  = $start_weight[ 'display' ];

		$text_date  = sprintf ( '<br />
									<span class="ykuk-info-box-meta">
										<a href="%s">%s</a>
									</span>', ws_ls_wt_link_edit_entry( $start_weight[ 'id' ] ), $start_weight[ 'display-date' ] );

	}

	return sprintf( '<div>
                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
                                <span class="ykuk-info-box-header">%3$s</span><br />
                                <span class="ykuk-text-bold">
                                    %1$s
                                </span>
                                %2$s
                        </div>
                    </div>',
		$text_data,
		$text_date,
		esc_html__( 'Starting Weight', WE_LS_SLUG )
	);
}

/**
 * Component to display latest versus target weight
 * @param array $args
 * @return string
 */
function ws_ls_component_latest_versus_another( $args = [] ) {

	$args           = wp_parse_args( $args, [ 'user-id'                 => get_current_user_id(),
	                                          'compare-against'         => 'target',
	                                          'compare-missing-text'    => esc_html__( 'No target set', WE_LS_SLUG ),
	                                          'title'                   => esc_html__( 'Latest vs Target', WE_LS_SLUG )
	] );
	$comparison_weight  = NULL;
	$text_data          = esc_html__( 'No data', WE_LS_SLUG );
	$latest_entry       = ws_ls_entry_get_latest( $args );

	if ( 'target' === $args[ 'compare-against' ] ) {
		$comparison_weight = ws_ls_target_get( $args[ 'user-id' ] );
	} elseif ( (int) ws_ls_db_entries_count( $args[ 'user-id' ] )[ 'number-of-entries' ] >= 2 ) { // Start weight: Ensure we have 2 or more entries to compare
		$comparison_weight  = ws_ls_entry_get_oldest( [ 'user-id' => $args[ 'user-id' ] ] );
	}

	if( true === empty( $latest_entry ) ) {
		$text_data = esc_html__('No entries', WE_LS_SLUG);
	} elseif( true === empty( $comparison_weight ) ) {
		$text_data = $args[ 'compare-missing-text' ];
	} elseif ( false === empty( $latest_entry ) ) {

		$comparison_weight = $comparison_weight[ 'kg' ];

		$kg_difference 	= $latest_entry[ 'kg' ] - $comparison_weight;

		$weight_display = ws_ls_weight_display( $kg_difference, NULL, false, false, true );

		$text_data = $weight_display[ 'display' ];

		$percentage_difference	= ws_ls_calculate_percentage_difference( $comparison_weight, $latest_entry[ 'kg' ] );

		if ( NULL === $percentage_difference ) {
			$percentage_difference = 0;
		} else {

			$percentage_difference	= ( true === $percentage_difference[ 'increase' ] ) ?  $percentage_difference[ 'percentage' ] : -$percentage_difference[ 'percentage' ];

			$percentage_difference 	= ws_ls_round_number( $percentage_difference, 1 );
		}

		if ( false === empty( $percentage_difference ) ) {

			$user_aim = (int) ws_ls_user_preferences_get( 'aim', $args[ 'user-id' ] );

			if ( ( 2 === $user_aim && (float) $percentage_difference <= 0 ) ||
				( 3 === $user_aim && (float) $percentage_difference >= 0 ) ) {
				$class = 'ykuk-label-success';
			} else {
				$class = 'ykuk-label-warning';
			}

			$text_data .= sprintf( ' <span class="ykuk-label %s" ykuk-tooltip="%s">%s%%</span>',
				$class,
				esc_html__( 'The difference between your latest weight and target.', WE_LS_SLUG ),
				$percentage_difference
			);
		}
	}

	return sprintf( '<div>
                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
                                <span class="ykuk-info-box-header">%2$s</span><br />
                                <span class="ykuk-text-bold">
                                    %1$s
                                </span>
                        </div>
                    </div>',
		$text_data,
		$args[ 'title' ]
	);
}

/**
 * Component to display the number of entries
 * @param array $args
 *
 * @return string
 */
function ws_ls_component_number_of_entries( $args = [] ) {

	$args   = wp_parse_args( $args, [ 'user-id' => get_current_user_id() ] );
	$counts = ws_ls_db_entries_count( $args[ 'user-id' ] );

	$text_data = ( false === empty( $counts[ 'number-of-entries' ] ) ) ?
					(int) $counts[ 'number-of-entries' ] :
						esc_html__( 'No data', WE_LS_SLUG );

	return sprintf( '<div>
                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
                                <span class="ykuk-info-box-header">%2$s</span><br />
                                <span class="ykuk-text-bold">
                                    %1$s
                                </span>
                        </div>
                    </div>',
		$text_data,
		esc_html__( 'No. entries', WE_LS_SLUG )
	);
}

/**
 * Component to display calories to lose/gain/maintain
 * @param array $args
 *
 * @return string
 */
function ws_ls_component_calories( $args = [] ) {

	$args   = wp_parse_args( $args, [ 'user-id' => get_current_user_id(), 'progress' => 'lose', 'type' => 'total', 'add-unit' => true, 'error-message' => esc_html__( 'No data', WE_LS_SLUG ) ] );

	$text_data = ws_ls_shortcode_harris_benedict( $args );

	switch ( $args[ 'progress' ] ) {
		case 'auto':
			$title = esc_html__( 'Calories for meeting aim', WE_LS_SLUG );
			break;
		case 'maintain':
			$title = esc_html__( 'Calories for maintaining', WE_LS_SLUG );
			break;
		case 'gain':
			$title = esc_html__( 'Calories for gain', WE_LS_SLUG );
			break;
		default:
			$title = esc_html__( 'Calories for loss', WE_LS_SLUG );
	}

	return sprintf( '<div>
                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
                                <span class="ykuk-info-box-header">%2$s</span><br />
                                <span class="ykuk-text-bold">
                                    %1$s
                                </span>
                        </div>
                    </div>',
		$text_data,
		$title
	);
}

/**
 * Component to display the number of weight entries
 * @param array $args
 *
 * @return string
 */
function ws_ls_component_number_of_weight_entries( $args = [] ) {

	$args   = wp_parse_args( $args, [ 'user-id' => get_current_user_id() ] );
	$counts = ws_ls_db_entries_count( $args[ 'user-id' ] );

	$text_data = ( false === empty( $counts[ 'number-of-weight-entries' ] ) ) ?
		(int) $counts[ 'number-of-weight-entries' ] :
		esc_html__( 'No data', WE_LS_SLUG );

	return sprintf( '<div>
                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
                                <span class="ykuk-info-box-header">%2$s</span><br />
                                <span class="ykuk-text-bold">
                                    %1$s
                                </span>
                        </div>
                    </div>',
		$text_data,
		esc_html__( 'No. weight entries', WE_LS_SLUG )
	);
}

/**
 * Component to display the number of days tracking
 * @param array $args
 *
 * @return string
 */
function ws_ls_component_number_of_days_tracking( $args = [] ) {

	$args   = wp_parse_args( $args, [ 'user-id' => get_current_user_id() ] );
	$days   = ws_ls_shortcode_days_between_start_and_latest( [ 'user-id' => $args[ 'user-id' ] ], true );

	if ( true === empty( $days ) ) {
		$days = 0;
	}

	return sprintf( '<div>
                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
                                <span class="ykuk-info-box-header">%2$s</span><br />
                                <span class="ykuk-text-bold">
                                    %1$s %3$s
                                </span>
                        </div>
                    </div>',
		ws_ls_round_number( $days ),
		esc_html__( 'Tracking for', WE_LS_SLUG ),
		esc_html__( 'days', WE_LS_SLUG )
	);
}

/**
 * Component to display age/dob
 * @param array $args
 *
 * @return string
 */
function ws_ls_component_age_dob( $args = [] ) {

	$args   = wp_parse_args( $args, [ 'user-id' => get_current_user_id() ] );
	$dob    = ws_ls_get_dob( $args[ 'user-id'] );

	$text_link  = esc_html__( 'Set DoB', WE_LS_SLUG );
	$age        = esc_html__( 'DoB missing', WE_LS_SLUG );

	if( false === empty( $dob ) ) {
		$text_link  = ws_ls_iso_date_into_correct_format( $dob );
		$age        = ws_ls_age_from_dob( $dob );
	}

	return sprintf( '<div>
                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
                                <span class="ykuk-info-box-header">%2$s</span><br />
                                <span class="ykuk-text-bold">
                                    %1$s
                                </span>
                                <br /><span class="ykuk-info-box-meta"><a href="#" class="ws-ls-tab-change" data-tab="settings">%3$s</a></span>
                        </div>
                    </div>',
		$age,
		esc_html__( 'Age', WE_LS_SLUG ),
		$text_link
	);
}

/**
 * Display alert
 *
 * @param $message
 * @param string $type
 *
 * @param bool $closable
 * @param bool $include_log_link
 *
 * @param null $notification_id
 *
 * @param string $additional_css_classes
 *
 * @return string
 */
function ws_ls_component_alert( $args ) {

	$args   = wp_parse_args( $args, [   'message'               => '',
	                                    'disable-main-font'     => false,                       // If set to true, don't include the main font
	                                    'disable-theme-css'     => false,                       // If set to true, don't include the additional theme CSS used
	                                    'type'                  => 'success',
										'closable'              => true,
										'include-login-link'    => false,
										'notification-id'       => NULL,
										'css-classes'           => '',
										'uikit'                 => true,

	] );

	if ( true === $args[ 'uikit' ] ) {
		ws_ls_enqueue_uikit( ! $args[ 'disable-theme-css' ], ! $args[ 'disable-main-font' ], 'alert' );
	}

	// Types: danger, warning, success, primary

	return sprintf( '<div class="ykuk-display-block ykuk-alert-%1$s %6$s" ykuk-alert>
		                <a class="ykuk-alert-close" %3$s data-notification-id="%5$d"></a>
		                %2$s%4$s
					</div>',
					esc_attr( $args[ 'type' ] ),
					wp_kses_post( $args[ 'message' ] ),
					true === $args[ 'closable' ] ? 'ykuk-close' : '',
					( true === $args[ 'include-login-link' ] ) ?
						sprintf( ' <a class="ws-ls-login-link" href="%1$s">%2$s</a>.', esc_url( wp_login_url( get_permalink() ) ), esc_html__( 'Login' , WE_LS_SLUG ) ) :
						'',
					$args[ 'notification-id' ],
					esc_attr( $args[ 'css-classes' ] )
	);
}

/**
 * BMI component
 * @param array $args
 *
 * @return string
 */
function ws_ls_component_bmi( $args = [] ) {

	$args           = wp_parse_args( $args, [ 'bmi-format' => 'start', 'user-id' => get_current_user_id() ] );
	$text_link      = '';
	$text_data      = ws_ls_shortcode_bmi( [ 'user-id' => $args[ 'user-id' ], 'display' => $args[ 'bmi-format' ], 'bmi-type' => $args[ 'bmi-type' ], 'no-height-text' => '', 'no-weight-text' => '' ] );
	$status         = ( false !== strpos( $text_data, 'Healthy' ) ) ? 'ykuk-label ykuk-label-success' : 'ykuk-label ykuk-label-warning';

	if ( true === empty( $text_data ) ) {
		$text_data  = esc_html__( 'Missing data', WE_LS_SLUG );
		$status     = 'ykuk-text-bold';
	}

	if( true === empty( $args[ 'hide-advanced-narrative' ] ) ) {

		$text_link  = sprintf ( '<br />
									<span class="ykuk-info-box-meta">
										<a href="#" ykuk-toggle="target: #modal-bmi">%s</a>
									</span>', esc_html__( 'What is BMI?', WE_LS_SLUG ) );

		$text_link .= ws_ls_component_modal(    'modal-bmi',
												esc_html__( 'Body Mass Index (BMI)', WE_LS_SLUG ),
												esc_html__('The BMI (Body Mass Index) is used by the medical profession to quickly determine a person’s weight in regard to their height. From a straight forward calculation the BMI factor can be gained and may be used to determine if a person is underweight, of normal weight, overweight or obese.', WE_LS_SLUG )
		);

	}

	return sprintf( '<div>
	                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
	                                <span class="ykuk-info-box-header">%3$s</span><br />
	                                <span class="%4$s">%1$s</span>
	                                %2$s
	                        </div>
                        </div>',
						$text_data,
						$text_link,
						( 'start' === $args[ 'bmi-type' ] ) ? esc_html__( 'Starting BMI', WE_LS_SLUG ) : esc_html__( 'Current BMI', WE_LS_SLUG ),
						$status
	);
}

/**
 * Display warning notifications for BMI if they fall above or below a certain value
 * @param $args
 *
 * @return string
 */
function ws_ls_component_bmi_warning_notifications( $args ) {

	$args = wp_parse_args( $args, [
		'bmi-alert-if-above' => null,
		'bmi-alert-if-below' => null,
		'user-id'            => get_current_user_id()
	] );

	$bmi    = ws_ls_get_bmi_for_user( $args[ 'user-id' ] );

	if ( true === empty( $bmi ) ) {
		return '';
	}

	$html       = '';
	$prefix     = ( false === empty( $args[ 'kiosk-mode'] ) ) ? esc_html__( 'User\'s ', WE_LS_SLUG ) : esc_html__( 'Your ', WE_LS_SLUG );

	if ( false === empty( $args[ 'bmi-alert-if-above' ] ) &&
	        (float) $bmi > (float) $args[ 'bmi-alert-if-above' ] ) {
		$html .= ws_ls_component_alert( [ 'message' => $prefix . sprintf( esc_html__( 'BMI is above %s.', WE_LS_SLUG ), $args[ 'bmi-alert-if-above' ] ), 'type' => 'danger' ] );
	}

	if ( false === empty( $args[ 'bmi-alert-if-below' ] ) &&
	        $bmi < (float) $args[ 'bmi-alert-if-below' ] ) {
		$html .= ws_ls_component_alert( [ 'message' => $prefix . sprintf( esc_html__( 'BMI is below %s.', WE_LS_SLUG ), $args[ 'bmi-alert-if-below' ] ), 'type' => 'danger' ] );
	}

	return $html;

}

/**
 * BMR component
 * @param array $args
 *
 * @return string
 */
function ws_ls_component_bmr( $args = [] ) {

	$args           = wp_parse_args( $args, [ 'user-id' => get_current_user_id(), 'bmr-type' => 'current' ] );
	$text_link      = '';
	$text_data      = ws_ls_shortcode_bmr( [ 'user-id' => $args[ 'user-id' ], 'bmr-type' => $args[ 'bmr-type' ], 'suppress-errors' => true ] );

	if ( true === empty( $text_data ) ) {
		$text_data = esc_html__( 'Missing data', WE_LS_SLUG );
	}

	if( true === empty( $args[ 'hide-advanced-narrative' ] ) ) {

		$text_link  = sprintf ( '<br />
									<span class="ykuk-info-box-meta">
										<a href="#" ykuk-toggle="target: #modal-bmr">%s</a>
									</span>', esc_html__( 'What is BMR?', WE_LS_SLUG ) );

		$text_link .= ws_ls_component_modal(    'modal-bmr',
			esc_html__( 'Basal Metabolic Rate (BMR)', WE_LS_SLUG ),
			esc_html__( 'BMR is short for Basal Metabolic Rate. The Basal Metabolic Rate is the number of calories required to keep your body functioning at rest, also known as your metabolism. We calculate your BMR using formulas provided by www.diabetes.co.uk.', WE_LS_SLUG )
		);

	}

	return sprintf( '<div>
	                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
	                                <span class="ykuk-info-box-header">%1$s</span><br />
	                                <span class="ykuk-text-bold">%2$s</span>
	                          		%3$s
                        	</div>
                     </div>',
		( 'start' === $args[ 'bmr-type' ] ) ?  esc_html__( 'Starting BMR', WE_LS_SLUG ) : esc_html__( 'Current BMR', WE_LS_SLUG ),
		$text_data,
		$text_link
	);
}

/**
 * Name and email component
 * @param array $args
 *
 * @return string
 */
function ws_ls_component_name_and_email( $args = [] ) {

	$args       = wp_parse_args( $args, [ 'user-id' => get_current_user_id() ] );
	$user       = get_user_by( 'id', $args[ 'user-id' ] );
	$name       = ws_ls_user_get_name( $args[ 'user-id' ] );

	return sprintf( '<div>
	                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small ykuk-overflow-auto">
	                                <span class="ykuk-info-box-header">%1$s</span><br />
	                                <span class="ykuk-text-bold">%2$s</span><br />
	                          		<a href="mailto:%3$s">%3$s</a>
                        	</div>
                     </div>',
		esc_html__( 'Name', WE_LS_SLUG ),
		esc_html( $name ),
		esc_html( $user->user_email )
	);
}

/**
 * User ID component
 * @param array $args
 *
 * @return string
 */
function ws_ls_component_user_id( $args = [] ) {

	$args = wp_parse_args( $args, [ 'user-id' => get_current_user_id() ] );

	return sprintf( '<div>
	                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small ykuk-overflow-auto">
	                                <span class="ykuk-info-box-header">%1$s</span><br />
	                                <span class="ykuk-text-bold">%2$d</span>
                        	</div>
                     </div>',
		esc_html__( 'User ID', WE_LS_SLUG ),
		$args[ 'user-id' ]
	);
}

/**
 * Render link with modal
 *
 * @param $title
 * @param $description
 *
 * @param bool $preceeding_br
 *
 * @return string
 */
function ws_ls_component_modal_with_text_link( $title, $description, $preceeding_br = true ) {

	$id     = ws_ls_component_id();
	$title  = esc_html( $title );
	$html   = ( true === $preceeding_br ) ? '<br />' : '';

	$html   .= sprintf ( '<span class="ykuk-info-box-meta">
								<a href="#" ykuk-toggle="target: #modal-%2$s">%1$s</a>
							</span>', $title, $id );

	$html .= ws_ls_component_modal('modal-' . $id, $title, esc_html( $description ) );

	return $html;
}

/**
 * Render HTML for a dialog box
 * @param $element_id
 * @param $title
 * @param $body
 *
 * @return string
 */
function ws_ls_component_modal( $element_id, $title, $body ) {

	return sprintf( '<div id="%s" ykuk-modal>
									<div class="ykuk-modal-dialog ykuk-modal-body">
										<h2 class="ykuk-modal-title">%s</h2>
										<p>%s</p>
										<p class="ykuk-text-right">
											<button class="ykuk-button ykuk-button-default ykuk-modal-close" type="button">Close</button>
										</p>
									</div>
								</div>',
					esc_attr( $element_id ),
					esc_html( $title ),
					esc_html( $body )
	);
}

/**
 * Component for expanding text
 * @param $leading_text
 * @param $main_text
 *
 * @return string
 */
function ws_ls_component_expanding_text( $leading_text, $main_text ) {

	return sprintf( '<p>
						<a ykuk-toggle="cls: ykuk-hidden; target: #%1$s; animation: ykuk-animation-slide-bottom" class="ykuk-text-right ykuk-icon-link" ykuk-icon="triangle-down">
							%2$s
						</a>
					</p>
					<p id="%1$s" class="ykuk-hidden ykuk-text-left">%3$s</p>',
					ws_ls_component_id(),
					esc_html( $leading_text ),
					esc_html( $main_text )
	);
}

/**
 * Add a info box with header and footer
 *
 * @param $args
 *
 * @return string
 */
function ws_ls_ui_kit_info_box_with_header_footer( $args = [] ) {

	$args = wp_parse_args( $args, [ 'header'        => '',
	                                'body'          => '',
	                                'body-class'    => '',
	                                'footer'        => '',
	                                'footer-link'   => '',
	                                'footer-text'   => '',
	                                'tab-changer'   => ''
	] );

	$html = '<div class="ykuk-card ykuk-card-main ykuk-card-small ykuk-card-default ykuk-margin-top">';

	if ( false === empty( $args[ 'header' ] ) ) {
		$html .= sprintf( ' <div class="ykuk-card-header">
								<div class="ykuk-grid-small ykuk-flex-middle" ykuk-grid>
									<div class="ykuk-width-expand">
										<h5 class="ykuk-margin-remove-bottom ykuk-margin-remove-top">%s</h5>
									</div>
								</div>
							</div>',
			esc_html( $args[ 'header' ] )
		);
	}

	$html .= sprintf( '<div class="ykuk-card-body%s">%s</div>', ( false === empty( $args[ 'body-class' ] ) ) ? ' ' . esc_attr( $args[ 'body-class' ] ) : '', $args[ 'body' ] );

	if ( false === empty( $args[ 'footer-link' ] )
	     && false === empty( $args[ 'footer-text' ] ) ) {

		$args[ 'footer' ] = sprintf( '<a href="%s" class="ykuk-button ykuk-button-text%s" data-tab="%s">%s</a>',
			esc_url( $args[ 'footer-link' ] ),
			( false === empty( $args[ 'tab-changer' ] ) ) ? ' ws-ls-tab-change' : '',
			( false === empty( $args[ 'tab-changer' ] ) ) ? $args[ 'tab-changer' ] : '',
			esc_html( $args[ 'footer-text' ] )
		);
	}

	if ( false === empty( $args[ 'footer' ] ) ) {
		$html .= sprintf( '<div class="ykuk-card-footer">%s</div>', wp_kses_post( $args[ 'footer' ] ) );
	}

	$html .= '</div>';

	return $html;

}

/**
 * Display summary boxes
 *
 * @param $key
 * @param array $arguments
 *
 * @return string
 */
function ws_ls_uikit_data_summary_boxes_display( $key, $arguments = [] ) {

	if ( true === empty( $arguments[ $key ] ) ) {
		return '';
	}

	$boxes = explode( ',', $arguments[ $key ] );

	return ws_ls_uikit_summary_boxes( $arguments, $boxes );
}

/**
 * Display a notice about data being exposed
 *
 * @param string $key
 *
 * @return string
 */
function ws_ls_uikit_data_exposed_notice( $key = 'ws-ls-kiosk-wt-notice' ) {

	if ( !current_user_can( 'manage_options' ) )  {
		return '';
	}

	if ( 'y' === ws_ls_querystring_value( $key ) ) {
		update_option( $key, 'n' );
	}

	if ( 'n' === get_option( $key, 'y' ) ) {
		return '';
	}

	$link = ws_ls_get_url();

	$link = add_query_arg($key, 'y', $link );

	return '<div class="ykuk-child-width-1-1@s" ykuk-grid>
			    <div>
			        <div class="ykuk-background-muted ykuk-padding">
				        <div class="ykuk-alert-danger" ykuk-alert>
						    <p><strong>Note: Only administrators can see this message.</strong></p>
						</div>
			            <p>Please note, in "kiosk-mode" your user data is exposed unless you ensure this page is secured from the general public.</p>
			             <a class="ykuk-button ykuk-button-default" href="' . esc_url( $link ) . '">Hide this message</a>
			        </div>
			    </div>
			</div>';

}
/**
 * Show user search component
 * @param $arguments
 *
 * @return string
 */
function ws_ls_component_user_search( $arguments ) {

	$arguments = wp_parse_args( $arguments, [   'disable-theme-css'         => false,
	                                            'disable-main-font'         => false,
	                                            'disable-not-logged-in'     => false,
	                                            'preload-max'               => 1200,        // Preload the user list via Ajax if total user count is less than this.
	                                            'placeholder'               => esc_html__( 'Search for a user...', WE_LS_SLUG ),
	                                            'previous-search'           => '',
												'querystring-key-user-id'   => 'wt-user-id'
	]);

	ws_ls_enqueue_uikit( ! $arguments[ 'disable-theme-css' ], ! $arguments[ 'disable-main-font' ], 'user-search' );

	if ( false === is_user_logged_in() ) {
		return ( false === ws_ls_to_bool( $arguments[ 'disable-not-logged-in' ] ) ) ?
					ws_ls_component_alert( [ 'message' => esc_html__( 'You need to be logged in to search for users.', WE_LS_SLUG ), 'type' => 'primary', 'closable' => false, 'include=login-link' => true ] ) :
						'';
	}

	wp_enqueue_style( 'wt-selectize', plugins_url( '../assets/css/libraries/selectize.default.min.css', __FILE__ ), [], WE_LS_CURRENT_VERSION );
	wp_enqueue_script( 'wt-selectize', plugins_url( '../assets/js/libraries/selectize.min.js', __FILE__ ), [ 'yk-uikit' ], WE_LS_CURRENT_VERSION, true );
	wp_enqueue_script( 'wt-user-search', plugins_url( '../assets/js/user-search.' . ws_ls_use_minified() . 'js', __FILE__ ), [ 'jquery', 'wt-selectize' ], WE_LS_CURRENT_VERSION, true );

	$data_stats = ws_ls_db_entries_count();

	wp_localize_script( 'wt-selectize', 'wt_user_search_config', [  'current-url'               =>  get_permalink(),
																	'preload'                   => ( (int) $data_stats[ 'number-of-users' ] < (int) $arguments[ 'preload-max' ] ) ? 'true' : 'false',
																	'placeholder'               => $arguments[ 'placeholder' ],
																	'querystring-key-user-id'   => $arguments[ 'querystring-key-user-id' ]
	]);

	$reset_link = remove_query_arg( $arguments[ 'querystring-key-user-id' ], ws_ls_get_url() );

	return sprintf( '<div class="ykuk-margin ws-ls-component-user-search ykuk-grid" ykuk-grid>
				        <div class="ykuk-width-expand">
				            <select id="%1$s">
				            </select>
				        </div>
				        <div class="ykuk-text-right">
				        	<a href="%2$s" class="ykuk-button ykuk-button-%3$s%5$s">%4$s</a>
				        	<a onclick="wt_barcode_reader_show()" class="ykuk-button ykuk-button-barcode ykuk-button-secondary%6$s" ykuk-icon="icon: camera" ></a>
				        	<a onclick="wt_barcode_lazer_show()" class="ykuk-button ykuk-button-barcode ykuk-button-secondary%7$s" ykuk-icon="icon: credit-card" ></a>
				        </div>
				    </div>
				    <div class="ykuk-divider-icon"></div>',
					ws_ls_component_id(),
					esc_url( $reset_link ),
					( NULL === ws_ls_querystring_value( $arguments[ 'querystring-key-user-id' ] ) ) ? 'default' : 'secondary',
					( false === $arguments[ 'kiosk-barcode-scanner' ] ) ? esc_html__( 'Clear Screen', WE_LS_SLUG ) : esc_html__( 'Clear', WE_LS_SLUG ),
					( false === $arguments[ 'user-loaded' ] ) ? ' ws-ls-hide' : '',
					( false === $arguments[ 'kiosk-barcode-scanner' ] || false === $arguments[ 'kiosk-barcode-scanner-camera' ] ) ? ' ws-ls-hide' : '',
					( false === $arguments[ 'kiosk-barcode-scanner' ] || false === $arguments[ 'kiosk-barcode-scanner-lazer' ] ) ? ' ws-ls-hide' : ''
	);
}
/**
 * Component to render group view
 * @param $arguments
 *
 * @return string
 */
function ws_ls_component_group_view_entries( $arguments ) {

	$arguments = wp_parse_args( $arguments, [   'default-to-users-group'    	=> false,
												'disable-theme-css'        		=> false,
	                                            'disable-main-font'        	 	=> false,
	                                            'group-id'                  	=> NULL,
												'table-allow-delete'        	=> false,
												'uikit'                     	=> true,
												'enable-group-select'       	=> true,
												'todays-entries-only'       	=> false,
												'hide-column-gains'       		=> false,
												'hide-column-losses'       		=> false,
												'hide-column-diff-from-prev'	=> false,
												'hide-summary-row'				=> false
	]);

	if ( true === $arguments[ 'uikit' ] ) {
		ws_ls_enqueue_uikit( ! $arguments[ 'disable-theme-css' ], ! $arguments[ 'disable-main-font' ] );
	}

	ws_ls_data_table_enqueue_scripts();

	$arguments[ 'group-id' ] = ws_ls_querystring_value( 'group-id', true, $arguments[ 'group-id' ] );

	// If we have no group id, and it's enabled, default to the current user's group.
	if( true === empty( $arguments[ 'group-id' ] ) &&
			true === ws_ls_to_bool( $arguments[ 'default-to-users-group' ] ) ) {

		$groups = ws_ls_groups_user( get_current_user_id() );

		$arguments[ 'group-id' ] = ( false === empty( $groups[0]['id'] ) ) ? (int) $groups[0]['id'] : NULL;
	}

	$html = '';

	if ( true === $arguments[ 'enable-group-select' ] ) {
		$html .= ws_ls_component_group_select( [ 'selected' => $arguments[ 'group-id' ], 'uikit' => $arguments[ 'uikit' ] ] );
	}

	$display_text = ( true === ws_ls_to_bool( $arguments[ 'todays-entries-only' ] ) ) ?
					esc_html__( 'Total weight difference (between previous/latest)', WE_LS_SLUG ) :
						esc_html__( 'Total weight difference (between start/latest)', WE_LS_SLUG );
	
	$message = ws_ls_component_alert( [ 'message' 		=> esc_html__( 'Total losses', WE_LS_SLUG ) . ': <strong><span></span>.</strong>',
										'css-classes' 	=> 'ykuk-invisible ws-ls-total-losses-count', 
										'uikit' 		=> $arguments[ 'uikit']
	]);

	$message .= ws_ls_component_alert( [ 'message' 		=> esc_html__( 'Total gains', WE_LS_SLUG ) . ': <strong><span></span>.</strong>',
										'css-classes' 	=> 'ykuk-invisible ws-ls-total-gains-count', 
										'uikit' 		=> $arguments[ 'uikit']
	]);

	$message .= ws_ls_component_alert( [ 'message' 		=> $display_text . ': <strong><span></span>.</strong>',
	                                    'css-classes' 	=> 'ykuk-invisible ws-ls-total-lost-count', 
										'uikit' 		=> $arguments[ 'uikit']
	]);

	$html .= sprintf('<div id="-row" class="ws-ls-form-row ykuk-width-1-1">
						<table class="ws-ls-settings-groups-users-list-ajax ykuk-table table ws-ls-loading-table" id="groups-users-list"
                           data-group-id="%1$d"
                           data-todays-entries-only="%3$s"
                           data-paging="true"
                           data-filtering="false"
                           data-sorting="true"
                           data-editing-allow-add="false"
                           data-editing-allow-edit="false"
                           data-editing-allow-delete="%2$s"
                           data-paging-size="40"
                           data-cascade="true"
                           data-toggle="true"
                           data-is-admin="%5$s"
						   data-hide-column-gains="%6$s"
						   data-hide-column-losses="%7$s"
						   data-hide-column-diff-from-prev="%8$s"
						   data-hide-summary-row="%9$s"
                           data-use-parent-width="true">
                    	</table>
                    	<div class="ykuk-divider-icon"></div>
						%4$s
                    </div>',
					$arguments[ 'group-id'],
					( true === ws_ls_to_bool( $arguments[ 'table-allow-delete' ] ) ) ? 'true' : 'false',
					( true === ws_ls_to_bool( $arguments[ 'todays-entries-only' ] ) ) ? 'true' : 'false',
					$message,
					( true === is_admin() ) ? 'true' : 'false',
					$arguments[ 'hide-column-gains' ],
					$arguments[ 'hide-column-losses' ],
					$arguments[ 'hide-column-diff-from-prev' ],
					$arguments[ 'hide-summary-row' ]
	);

	return $html;
}

/**
 * Component to render a select drop down for groups
 * @param $arguments
 *
 * @return string
 */
function ws_ls_component_group_select( $arguments ) {

	$arguments  = wp_parse_args( $arguments, [ 'selected' => 0, 'include-empty' => true, 'include-all-groups' => true, 'reload-page-on-select' => true, 'uikit' => true ] );
	$groups     = ws_ls_groups( $arguments[ 'include-empty' ], $arguments[ 'include-all-groups' ] );
	$groups     = wp_list_pluck( $groups, 'name', 'id' );

	$select_args = [    'key'                           => ws_ls_component_id(),
						'values'                        => $groups,
	                    'selected'                      => $arguments[ 'selected' ],
	                    'uikit'                         => $arguments[ 'uikit' ],
						'reload-page-on-select'         => true,
		                'reload-page-on-select-qs-key'  => 'group-id'
	];

	return ws_ls_form_field_select( $select_args );
}

/**
 * Opening for Grid
 * @param $breakpoint_s
 * @param $breakpoint_m
 * @param string $id
 *
 * @return string
 */
function ws_ls_uikit_open_grid( $breakpoint_s, $breakpoint_m, $id = '' ) {
	return sprintf( '<div class="ykuk-grid-small ykuk-text-center ykuk-child-width-1-1 ykuk-child-width-1-%1$d@s ykuk-child-width-1-%2$d@m ykuk-grid-match ykuk-text-small ws-ls-section-%3$s" ykuk-grid>',
		$breakpoint_s,
		$breakpoint_m,
		$id
	);
}
