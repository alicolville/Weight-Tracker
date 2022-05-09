<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Component to display the user's latest weight
 * @param array $args
 *
 * @return string
 */
function ws_ls_component_latest_weight( $args = [] ) {

    $args           = wp_parse_args( $args, [ 'user-id' => get_current_user_id() ] );
    $latest_entry   = ws_ls_entry_get_latest( $args );

    $text_date      = '';
    $text_data      = __( 'No data', WE_LS_SLUG );

    if( false === empty( $latest_entry ) ) {

        $text_data  = $latest_entry[ 'display' ];
        $text_date  = sprintf ( '<br />
									<span class="ykuk-info-box-meta">
										<a href="%s">%s</a>
									</span>', ws_ls_wt_link_edit_entry( $latest_entry[ 'id' ] ), $latest_entry[ 'display-date' ] );

        $difference = ws_ls_shortcode_difference_in_weight_previous_latest( [   'display'                   => 'percentage',
                                                                                'include-percentage-sign'   => false,
	                                                                            'invert'                    => true,
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

            $text_data .= sprintf( ' <span class="ykuk-label %s" ykuk-tooltip="%s">%s%%</span>',
                                    $class,
                                    __( 'The difference between your latest weight and previous.', WE_LS_SLUG ),
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
                    __( 'Latest Weight', WE_LS_SLUG )
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
	$text_data      = __( 'No data', WE_LS_SLUG );

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
		__( 'Previous Weight', WE_LS_SLUG )
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
	$target_weight  = ws_ls_target_get( $args );

	$text_date      = '';
	$text_data      = __( 'Not set', WE_LS_SLUG );

	if( false === empty( $target_weight ) ) {
		$text_data  = $target_weight[ 'display' ];
	}

	return sprintf( '<div>
                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
                                <span class="ykuk-info-box-header">%3$s</span><br />
                                <span class="ykuk-text-bold">
                                    %1$s
                                </span>
                                %2$s
                                <br /><span class="ykuk-info-box-meta"><a href="#" class="ws-ls-tab-change" data-tab="settings">Adjust</a></span>
                        </div>
                    </div>',
		$text_data,
		$text_date,
		__( 'Target Weight', WE_LS_SLUG )
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
	$text_data      = __( 'Not set', WE_LS_SLUG );

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
		__( 'Start Weight', WE_LS_SLUG )
	);
}


/**
 * Component to display latest versus target weight
 * @param array $args
 * @return string
 */
function ws_ls_component_latest_versus_target( $args = [] ) {

	$args           = wp_parse_args( $args, [ 'user-id' => get_current_user_id() ] );
	$latest_entry   = ws_ls_entry_get_latest( $args );
	$target_weight  = ws_ls_target_get( $args );
	$text_data      = __( 'No data', WE_LS_SLUG );

	if( true === empty( $latest_entry ) ) {
		$text_data = __('No entries', WE_LS_SLUG);
	} elseif( true === empty( $target_weight ) ) {
		$text_data = __( 'No target set', WE_LS_SLUG );
	} elseif ( false === empty( $latest_entry ) ) {

		$kg_difference 	= $latest_entry[ 'kg' ] - $target_weight[ 'kg' ];

		$weight_display = ws_ls_weight_display( $kg_difference, $args[ 'user-id' ], false, false, true );

		$text_data		= $weight_display[ 'display' ];

		$percentage_difference	= ws_ls_calculate_percentage_difference( $target_weight[ 'kg' ], $latest_entry[ 'kg' ] );

		$percentage_difference	= ( true === $percentage_difference[ 'increase' ] ) ?  $percentage_difference[ 'percentage' ] : -$percentage_difference[ 'percentage' ];

		$percentage_difference 	= ws_ls_round_number( $percentage_difference, 1 );

		if ( false === empty( $percentage_difference ) ) {

			$user_aim = (int) ws_ls_user_preferences_get( 'aim' );

			if ( ( 2 === $user_aim && (float) $percentage_difference <= 0 ) ||
				( 3 === $user_aim && (float) $percentage_difference >= 0 ) ) {
				$class = 'ykuk-label-success';
			} else {
				$class = 'ykuk-label-warning';
			}

			$text_data .= sprintf( ' <span class="ykuk-label %s" ykuk-tooltip="%s">%s%%</span>',
				$class,
				__( 'The difference between your latest weight and target.', WE_LS_SLUG ),
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
		__( 'Latest vs Target', WE_LS_SLUG )
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
						__( 'No data', WE_LS_SLUG );

	return sprintf( '<div>
                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
                                <span class="ykuk-info-box-header">%2$s</span><br />
                                <span class="ykuk-text-bold">
                                    %1$s
                                </span>
                        </div>
                    </div>',
		$text_data,
		__( 'No. weight entries', WE_LS_SLUG )
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
	$days   = ws_ls_shortcode_days_between_start_and_latest( [ 'user-id' => $args[ 'user-id' ] ] );

	return sprintf( '<div>
                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
                                <span class="ykuk-info-box-header">%2$s</span><br />
                                <span class="ykuk-text-bold">
                                    %1$s %3$s
                                </span>
                        </div>
                    </div>',
		ws_ls_round_number( $days ),
		__( 'Tracking for', WE_LS_SLUG ),
		__( 'days', WE_LS_SLUG )
	);
}

/**
 * Display alert
 * @param $message
 * @param string $type
 *
 * @return string
 */
function ws_ls_component_alert( $message, $type = 'success' ) {

	// Types: danger, warning, success, primary

	return sprintf( '<div class="ykuk-alert-%s" ykuk-alert>
		                <a class="ykuk-alert-close" ykuk-close></a>
		                <p>%s</p>
					</div>',
					esc_attr( $type ),
					esc_html( $message )
	);
}

/**
 * BMI component
 * @param array $args
 *
 * @return string
 */
function ws_ls_component_bmi( $args = [] ) {

	$args           = wp_parse_args( $args, [ 'user-id' => get_current_user_id() ] );
	$text_link      = '';
	$text_data      = ws_ls_shortcode_bmi( [ 'display' => $args[ 'bmi-format' ], 'no-height-text' => '', 'no-weight-text' => '' ] );
	$status         = ( false !== strpos( $text_data, 'Healthy' ) ) ? 'success' : 'warning';

	if( true === empty( $args[ 'hide-advanced-narrative' ] ) ) {

		$text_link  = sprintf ( '<br />
									<span class="ykuk-info-box-meta">
										<a href="#" ykuk-toggle="target: #modal-bmi">%s</a>
									</span>', __( 'What is BMI?', WE_LS_SLUG ) );

		$text_link .= ws_ls_component_modal(    'modal-bmi',
												__( 'Body Mass Index (BMI)', WE_LS_SLUG ),
												__('The BMI (Body Mass Index) is used by the medical profession to quickly determine a person’s weight in regard to their height. From a straight forward calculation the BMI factor can be gained and may be used to determine if a person is underweight, of normal weight, overweight or obese.', WE_LS_SLUG )
		);

	}

	return sprintf( '<div>
	                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
	                                <span class="ykuk-info-box-header">%3$s</span><br />
	                                <span class="ykuk-label ykuk-label-%4$s">%1$s</span>
	                                %2$s
	                        </div>
                        </div>',
						$text_data,
						$text_link,
						__( 'BMI', WE_LS_SLUG ),
						$status
	);
}

/**
 * BMR component
 * @param array $args
 *
 * @return string
 */
function ws_ls_component_bmr( $args = [] ) {

	$args           = wp_parse_args( $args, [ 'user-id' => get_current_user_id() ] );
	$text_link      = '';
	$text_data      = '1882';

	if( true === empty( $args[ 'hide-advanced-narrative' ] ) ) {

		$text_link  = sprintf ( '<br />
									<span class="ykuk-info-box-meta">
										<a href="#" ykuk-toggle="target: #modal-bmr">%s</a>
									</span>', __( 'What is BMR?', WE_LS_SLUG ) );

		$text_link .= ws_ls_component_modal(    'modal-bmr',
			__( 'Basal Metabolic Rate (BMR)', WE_LS_SLUG ),
			__( 'BMR is short for Basal Metabolic Rate. The Basal Metabolic Rate is the number of calories required to keep your body functioning at rest, also known as your metabolism. We calculate your BMR using formulas provided by www.diabetes.co.uk.', WE_LS_SLUG )
		);

	}

	return sprintf( '<div>
	                        <div class="ykuk-card ykuk-card-small ykuk-card-body ykuk-box-shadow-small">
	                                <span class="ykuk-info-box-header">%1$s</span><br />
	                                <span class="ykuk-text-bold">%2$s</span>
	                          		%3$s
                        	</div>
                     </div>',
		__( 'BMR', WE_LS_SLUG ),
		$text_data,
		$text_link
	);
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
