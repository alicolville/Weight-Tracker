<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Output for [wt-bmi-calculator]
 * @param $user_defined_arguments
 * @param null $content
 *
 * @return string|null
 * @throws Exception
 */
function ws_ls_waist_to_hip_ratio_calculator( $user_defined_arguments ) {

	$arguments = shortcode_atts( [  'disable-main-font'         => false,                       // If set to true, don't include the main font
	                                'disable-theme-css'         => false,                       // If set to true, don't include the additional theme CSS used
	                                'default-tab'               => 'metric',                    // Default tab to display: metric or imperial
	                                'user-id'					=> get_current_user_id() ],
		$user_defined_arguments );

	ws_ls_enqueue_uikit( ! $arguments[ 'disable-theme-css' ], ! $arguments[ 'disable-main-font' ], 'shortcode-calculator' );

	if ( false === WS_LS_IS_PRO_PLUS ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode( true );
	}

	wp_localize_script( 'yk-uikit-shortcode-calculator', 'ws_ls_calc_config', ws_ls_calculator_js_config() );

	$html = '<div class="ws-ls-hip-waist-calculator ws-ls-tracker-force-font">
				';
	$html .= sprintf( '	<ul ykuk-tab ykuk-switcher>
   							<li><a href="#">Metric</a></li>
    						<li class="%s"><a href="#" >Imperial</a></li>
    					</ul>',
						'metric' !== $arguments[ 'default-tab' ] ? 'ykuk-active' : ''
	);

	$gender = (int) ws_ls_user_preferences_get('gender', $arguments[ 'user-id' ] );

	$html .= sprintf( '<ul class="ykuk-switcher ykuk-margin" >
						<li>
							<form id="ws-ls-hip-waist-metric" class="ykuk-form-horizontal ykuk-margin-large form-calculate" data-unit="metric" data-action="ws_ls_waist_to_hip_ratio_calculator">
								<div class="ykuk-hidden bmi-alert" ykuk-alert></div>
							    <div class="ykuk-margin">
							        <label class="ykuk-form-label" for="metric_gender">%6$s</label>
							        <div class="ykuk-form-controls ykuk-grid" ykuk-grid>
							            <div class="ykuk-width-1-1">
							             	<select class="ykuk-select ykuk-input" name="metric_gender" id="metric_gender">
										        <option value="1">%7$s</option>
										        <option value="2" %9$s>%8$s</option>
										    </select>
							            </div>
							        </div>
							    </div>
							    <div class="ykuk-margin">
							        <label class="ykuk-form-label" for="metric_hip">%1$s</label>
							        <div class="ykuk-form-controls ykuk-grid" ykuk-grid>
							            <div class="ykuk-width-1-1">
							             	<input class="ykuk-input" id="metric_hip" type="number" min="50" max="300" value="50" required onchange="updateInputValue(this.value, \'metric_hip_range\');">
							             	<input class="ykuk-range" id="metric_hip_range" type="range" value="50" min="50" max="300" step="1" onchange="updateInputValue(this.value, \'metric_hip\');">
							            </div>
							        </div>
							    </div>
							    <div class="ykuk-margin">
							        <label class="ykuk-form-label" for="metric_waist">%3$s</label>
							        <div class="ykuk-form-controls ykuk-grid" ykuk-grid>
							            <div class="ykuk-width-1-1">
								            <input class="ykuk-input" id="metric_waist" type="number" min="50" max="300" value="50" required onchange="updateInputValue(this.value, \'metric_waist_range\');">
								            <input class="ykuk-range" id="metric_waist_range" type="range" value="50" min="50" max="300" step="1" onchange="updateInputValue(this.value, \'metric_waist\');">
								        </div>
							        </div>
							    </div>
							    <div class="ykuk-margin">
							        <button type="button" class="button-calculate ykuk-button ykuk-button-default">%5$s</button>
							    </div>
							</form>
						</li>
						<li>
							<form id="ws-ls-hip-waist-imperial" class="ykuk-form-horizontal ykuk-margin-large form-calculate" data-unit="imperial" data-action="ws_ls_waist_to_hip_ratio_calculator">
								<div class="ykuk-hidden bmi-alert" ykuk-alert>
								    <p></p>
								</div>
								<div class="ykuk-margin">
							        <label class="ykuk-form-label" for="imperial_gender">%6$s</label>
							        <div class="ykuk-form-controls ykuk-grid" ykuk-grid>
							            <div class="ykuk-width-1-1">
							             	<select class="ykuk-select ykuk-input" name="imperial_gender" id="imperial_gender">
										        <option value="1">%7$s</option>
										        <option value="2" %9$s>%8$s</option>
										    </select>
							            </div>
							        </div>
							    </div>
							    <div class="ykuk-margin">
							        <label class="ykuk-form-label" for="imperial_hip">%2$s</label>
							        <div class="ykuk-form-controls ykuk-grid" ykuk-grid>
							            <div class="ykuk-width-1-1">
							             	<input class="ykuk-input" id="imperial_hip" type="number" min="50" max="300" value="50" required onchange="updateInputValue(this.value, \'imperial_hip_range\');">
							             	<input class="ykuk-range" id="imperial_hip_range" type="range" value="50" min="50" max="300" step="1" onchange="updateInputValue(this.value, \'imperial_hip\');">
							            </div>
							        </div>
							    </div>
							    <div class="ykuk-margin">
							        <label class="ykuk-form-label" for="imperial_waist">%4$s</label>
							        <div class="ykuk-form-controls ykuk-grid" ykuk-grid>
							           	<div class="ykuk-width-1-1">
								            <input class="ykuk-input" id="imperial_waist" type="number" min="50" max="300" value="50" required onchange="updateInputValue(this.value, \'imperial_waist_range\');">
								            <input class="ykuk-range" id="imperial_waist_range" type="range" value="50" min="50" max="300" step="1" onchange="updateInputValue(this.value, \'imperial_waist\');">
								        </div>
							        </div>
							    </div>
							    <div class="ykuk-margin">
							        <button type="button" class="button-calculate ykuk-button ykuk-button-default">%5$s</button>
							    </div>
							</form>
						</li>',
		esc_html__( 'Waist (cm)', WE_LS_SLUG ),
		esc_html__( 'Waist (inches)', WE_LS_SLUG ),
		esc_html__( 'Hip (cm)', WE_LS_SLUG ),
		esc_html__( 'Hip (inches)', WE_LS_SLUG ),
		esc_html__( 'Calculate', WE_LS_SLUG ),
		esc_html__( 'Gender', WE_LS_SLUG ),
		esc_html__( 'Female', WE_LS_SLUG ),
		esc_html__( 'Male', WE_LS_SLUG ),
		selected( 2, $gender, false )
	);

	$html .= '</ul></div>';

	return $html;

}
add_shortcode( 'wt-waist-to-hip-ratio-calculator', 'ws_ls_waist_to_hip_ratio_calculator' );

/**
 * AJAX handler for calculating BMI
 */
function ws_ls_waist_to_hip_ratio_calculator_ajax() {

	check_ajax_referer( 'ws-ls-nonce', 'security' );

	$text   = 'ERROR';
	$class  = 'ykuk-alert-warning';
	$prefix = ws_ls_post_value('unit' ) . '_';



	$hip    = ws_ls_post_value( $prefix. 'hip', NULL, false, false, 'int' );
	$waist  = ws_ls_post_value( $prefix . 'waist', NULL, false, false, 'int' );
	$ratio  = $hip / $waist;

	if ( false === empty( $ratio ) ) {

		$gender = ws_ls_post_value( $prefix . 'gender', NULL, false, false, 'int' );

		$class  = ws_ls_calculate_waist_to_hip_ratio_uikit_class( $ratio, $gender );

		switch ( $class ){
			case 'ykuk-alert-warning':
					$description = esc_html__( 'Moderate Health Risk', WE_LS_SLUG );
				break;
			case 'ykuk-alert-danger':
				$description = esc_html__( 'High Health Risk', WE_LS_SLUG );
				break;
			default:
				$description = esc_html__( 'Low Health Risk', WE_LS_SLUG );
		}

		$text   = sprintf( '<h3>%s: %s</h3><p>%s.</p>',
			esc_html__( 'Your ratio', WE_LS_SLUG ),
			ws_ls_round_number( $ratio, 2 ),
			$description
		);

	}

	$data = [   'css-class' => $class, 'text' => $text ];

	wp_send_json( $data );
}
add_action( 'wp_ajax_ws_ls_waist_to_hip_ratio_calculator', 'ws_ls_waist_to_hip_ratio_calculator_ajax' );
add_action( 'wp_ajax_nopriv_ws_ls_waist_to_hip_ratio_calculator', 'ws_ls_waist_to_hip_ratio_calculator_ajax' );

/**
 * Determine the uikit class to represent the given Hip to Waist ration
 *
 * @param $ratio
 *
 * Based on: https://www.healthline.com/health/waist-to-hip-ratio#calculate
 *
 * @param $gender
 *
 * @return string|void
 */
function ws_ls_calculate_waist_to_hip_ratio_uikit_class( $ratio, $gender ) {

	if( true === is_numeric( $ratio ) ) {

		$ratio = (float) $ratio;

		// Female
		if ( 1 === (int) $gender ) {
			$lower_threshold    = 0.80;
			$higher_threshold   = 0.86;
		} else {
			$lower_threshold    = 0.95;
			$higher_threshold   = 1.0;
		}

		if ( $ratio < $lower_threshold ) {
			return 'ykuk-alert-success';
		} else if ( $ratio < $higher_threshold ) {
			return 'ykuk-alert-warning';
		} else {
			return 'ykuk-alert-danger';
		}

	}

	return esc_html__( 'Err', WE_LS_SLUG );
}
