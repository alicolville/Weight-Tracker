<?php

defined('ABSPATH') or die("Jog on!");

/**
 * [wt-calculator]
 *
 * @param $user_defined_arguments
 *
 * @return string
 * @throws Exception
 */
function ws_ls_shortcode_calculator( $user_defined_arguments ) {

	if( false === WS_LS_IS_PRO_PLUS ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	$arguments = shortcode_atts([   'bmi-display'               => 'both',                          // Specifies what to show for BMI: "index", "label" or "both".
	                                'load'                      => true,                            // If the user is logged in, their use their user preferences
	                                'responsive-tables'         => true,                            // If set to true, responsive HTML tables will be used. If false, plain HTML tables.
	                                'results-show-bmi'          => true,                            // Show BMI on results page
	                                'results-show-bmr'          => true,                            // Show BMR on results page
	                                'results-show-calories'     => true,                            // Show calories on results page
	                                'results-show-macros'       => true,                            // Show macros on results page
	                                'results-show-form'         => true,                            // If true, always show the form when rendering results
	                                'text-bmi'                  => __( 'Your BMI is:', WE_LS_SLUG ),
	                                'text-bmr'                  => __( 'Your BMR is:', WE_LS_SLUG ),
	                                'text-calories'             => __( 'The following table illustrates your recommended calorie intake:', WE_LS_SLUG ),
	                                'text-macros'               => __( 'The following table illustrates your recommended macronutrient intake:', WE_LS_SLUG ),
	], $user_defined_arguments );

	// Enqueue front end scripts if needed (mainly for datepicker)
	ws_ls_enqueue_files();

	$arguments[ 'responsive-tables' ]               = ws_ls_to_bool( $arguments[ 'responsive-tables' ] );
	$arguments[ 'responsive-tables-css-class' ]     = '';


	if ( true === $arguments[ 'responsive-tables' ] ) {
		ws_ls_data_table_enqueue_scripts();
		$arguments[ 'responsive-tables-css-class' ] = 'ws-ls-footable';
	}


	// If the user is logged in and we want to load existing data, then default to their preferences
	$user_id = ( true === ws_ls_to_bool( $arguments[ 'load' ] ) ) ?
		get_current_user_id() : NULL;

	// Form submitted?
	$form_submitted = ! empty( ws_ls_querystring_value( 'ws-ls-submit' ) );
	$entry          = [ 'ws-ls-weight-stones' => '', 'ws-ls-weight-pounds' => '', 'ws-ls-weight-kg' => '', 'kg' => '' ];
	$form_validated = false;
	$form_visible   = false;
	$html_output    = '<div class="ws-ls-macro-calculator">';

	// Form submitted?
	if ( true === $form_submitted ) {
		$entry = $_GET;

		$entry[ 'kg' ] = ws_ls_form_post_handler_extract_weight( 'get' );

		$form_validated = NULL !== $entry[ 'kg' ] &&
		                  ! empty( $entry[ 'ws-ls-height' ] ) &&
		                  ! empty( $entry[ 'ws-ls-gender' ] ) &&
		                  ! empty( $entry[ 'ws-ls-activity-level' ] ) &&
		                  ! empty( $entry[ 'ws-ls-dob' ] );

		$form_visible = ! $form_validated;

	} else if ( false === $form_submitted &&
	            false === empty( $user_id ) ) {

		$entry                              = ws_ls_entry_get_latest( [ 'user-id' => $user_id, 'meta' => false ] );
		$entry[ 'ws-ls-weight-stones' ]     = ( false === empty( $entry[ 'stones' ] ) ) ? $entry[ 'stones' ] : '';
		$entry[ 'ws-ls-weight-pounds' ]     = ( false === empty( $entry[ 'pounds' ] ) ) ? $entry[ 'pounds' ] : '';
		$entry[ 'ws-ls-weight-kg' ]         = ( false === empty( $entry[ 'kg' ] ) ) ? $entry[ 'kg' ] : '';
		$entry[ 'ws-ls-height' ]            = ws_ls_user_preferences_get( 'height', $user_id );
		$entry[ 'ws-ls-gender' ]            = ws_ls_user_preferences_get( 'gender', $user_id );
		$entry[ 'ws-ls-activity-level' ]    = ws_ls_user_preferences_get( 'activity_level', $user_id );
		$entry[ 'ws-ls-dob' ]               = ws_ls_get_dob_for_display( $user_id );

		$form_visible = true;
	}

	// Show results?
	if ( true === $form_validated ) {

		$dob = ws_ls_convert_date_to_iso( $entry[ 'ws-ls-dob' ] );

		$age = ws_ls_age_from_dob( $dob );

		$bmr = ws_ls_calculate_bmr_raw( $entry[ 'ws-ls-gender' ], $entry[ 'kg' ], $entry[ 'ws-ls-height' ], $age );

		if ( true === ws_ls_to_bool( $arguments[ 'results-show-bmr' ] ) ) {
			$html_output .= sprintf( '<p class="ws-ls-calc-bmr">%1$s <span>%2$s</span>.</p>', esc_html( $arguments[ 'text-bmr' ] ), esc_html( $bmr ) );
		}

		if ( true === ws_ls_to_bool( $arguments[ 'results-show-bmi' ] ) ) {

			$bmi = ws_ls_calculate_bmi( $entry[ 'ws-ls-height' ], $entry[ 'kg' ] );
			$bmi = ws_ls_bmi_display( $bmi, $arguments[ 'bmi-display' ] );

			$html_output .= sprintf( '<p class="ws-ls-calc-bmi">%1$s <span>%2$s</span>.</p>', esc_html( $arguments[ 'text-bmi' ] ), esc_html( $bmi ) );
		}

		$calories = ws_ls_harris_benedict_calculate_calories_raw( $bmr, $entry[ 'ws-ls-gender' ], $entry[ 'ws-ls-activity-level' ], false );

		if ( true === ws_ls_to_bool( $arguments[ 'results-show-calories' ] ) ) {

			$html_output .= sprintf( '<p class="ws-ls-calc-cals">%1$s</p>', esc_html( $arguments[ 'text-calories' ] ) );

			$html_output .= ws_ls_harris_benedict_render_table( $user_id, false, $arguments[ 'responsive-tables-css-class' ], false, true, $calories );
		}

		if ( true === ws_ls_to_bool( $arguments[ 'results-show-macros' ] ) ) {

			$html_output    .= sprintf( '<p class="ws-ls-calc-macros">%1$s</p>', esc_html( $arguments[ 'text-macros' ] ) );

			$macros         = ws_ls_macro_calculate_raw( $calories );

			$html_output    .= ws_ls_macro_render_table( $user_id, false, $arguments[ 'responsive-tables-css-class' ], $macros );
		}
	}

	// Show form
	if ( true === $form_visible || true === ws_ls_to_bool( $arguments[ 'results-show-form' ] ) ) {

		$html_output .= sprintf( '<form class="we-ls-weight-form ws-ls-calculator-form" method="get" action="%s">', esc_url( get_permalink() ) );

		if ( true === $form_submitted &&
		     false === $form_validated ) {
			$html_output .= sprintf( '<div class="ws-ls-error-summary" style="display: block;">
						        <p>%1$s</p>
                                <ul></ul>
                            </div>',
				__( 'Please ensure you have completed all of the fields.', WE_LS_SLUG ) );
		}

		//-------------------------------------------------------
		// Weight
		//-------------------------------------------------------

		$data_unit = ws_ls_setting( 'weight-unit', $user_id );

		$html_output .= sprintf( '<label class="yk-mt__label">%1$s:</label>', __( 'Current weight', WE_LS_SLUG ) );

		// Stones field?
		if ( 'stones_pounds' === $data_unit ) {
			$html_output .= ws_ls_form_field_number( [      'name'          => 'ws-ls-weight-stones',
			                                                'placeholder'   => __( 'st', WE_LS_SLUG ),
			                                                'value'         => $entry[ 'ws-ls-weight-stones' ] ]);
		}

		// Pounds?
		if ( true === in_array( $data_unit, [ 'stones_pounds', 'pounds_only' ] ) ) {
			$html_output .= ws_ls_form_field_number( [      'name'          => 'ws-ls-weight-pounds',
			                                                'placeholder'   => __( 'lb', WE_LS_SLUG ),
			                                                'max'           => ( 'stones_pounds' ===  $data_unit ) ? '13.99' : '5000',
			                                                'value' => $entry[ 'ws-ls-weight-pounds' ] ] );
		}

		// Kg
		if ( 'kg' ===  $data_unit ) {
			$html_output .= ws_ls_form_field_number( [      'name'          => 'ws-ls-weight-kg',
			                                                'placeholder'   => __( 'kg', WE_LS_SLUG ),
			                                                'value'         => $entry[ 'ws-ls-weight-kg' ] ]);
		}

		//-------------------------------------------------------
		// Height
		//-------------------------------------------------------

		$html_output .= ws_ls_form_field_select( [ 'key' => 'ws-ls-height', 'label' => __( 'Your height:', WE_LS_SLUG ), 'values' => ws_ls_heights(), 'empty-option' => true, 'include-div' => true,
		                                           'selected' => ( false === empty( $entry[ 'ws-ls-height' ] ) ) ? $entry[ 'ws-ls-height' ] : '', 'css-class' => 'ws-ls-aboutyou-field' ] );

		//-------------------------------------------------------
		// Gender
		//-------------------------------------------------------

		$html_output .= ws_ls_form_field_select( [ 'key' => 'ws-ls-gender', 'required' => true, 'label' => __( 'Your Gender:', WE_LS_SLUG ), 'values' => ws_ls_genders(), 'include-div' => true,
		                                           'selected' => ( false === empty( $entry[ 'ws-ls-gender' ] ) ) ? $entry[ 'ws-ls-gender' ] : '', 'css-class' => 'ws-ls-aboutyou-field' ] );

		//-------------------------------------------------------
		// Activity Level
		//-------------------------------------------------------

		$html_output .= ws_ls_form_field_select( [ 'key' => 'ws-ls-activity-level', 'required' => true, 'label' => __( 'Your Activity Level:', WE_LS_SLUG ), 'values' => ws_ls_activity_levels(), 'include-div' => true,
		                                           'selected' => ( false === empty( $entry[ 'ws-ls-activity-level' ] ) ) ? $entry[ 'ws-ls-activity-level' ] : '', 'css-class' => 'ws-ls-aboutyou-field' ] );

		//-------------------------------------------------------
		// Date of Birth
		//-------------------------------------------------------

		$html_output .= ws_ls_form_field_date( [    'name'          => 'ws-ls-dob',
		                                            'id'            => 'ws-ls-dob',
													'include-div' => true,
		                                            'title'         => __( 'Your Date of Birth:', WE_LS_SLUG ),
		                                            'value'         => ( false === empty( $entry[ 'ws-ls-dob' ] ) ) ? $entry[ 'ws-ls-dob' ] : '',
		                                            'css-class'     => 'we-ls-datepicker ws-ls-dob-field ws-ls-aboutyou-field',
		                                            'show-label'    => true ] );

		// Page ID in querystring?
		$page_id = ws_ls_querystring_value( 'page_id' );

		if ( false === empty( $page_id ) ) {
			$html_output .= sprintf( '<input type="hidden" name="page_id" value="%d" />', $page_id );
		}

		$html_output .= sprintf('<input type="submit" tabindex="%1$d" value="%2$s" name="ws-ls-submit" />',
			ws_ls_form_tab_index_next(),
			__( 'Calculate', WE_LS_SLUG )
		);

		$html_output .= '</form>';
	}

	$html_output .= '</div>';

	return $html_output;
}
add_shortcode( 'wt-calculator', 'ws_ls_shortcode_calculator' );
