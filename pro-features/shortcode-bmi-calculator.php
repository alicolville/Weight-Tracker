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
function ws_ls_bmi_calculator( $user_defined_arguments ) {

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

	$html = '<div class="ws-ls-bmi-calculator ws-ls-tracker-force-font">
				';
	$html .= sprintf( '	<ul ykuk-tab ykuk-switcher>
   							<li><a href="#">Metric</a></li>
    						<li class="%s"><a href="#" >Imperial</a></li>
    					</ul>',
						'metric' !== $arguments[ 'default-tab' ] ? 'ykuk-active' : ''
	);

	$html .= sprintf( '<ul class="ykuk-switcher ykuk-margin" >
						<li>
							<form id="ws-ls-bmi-calc-metric" class="ykuk-form-horizontal ykuk-margin-large form-calculate" data-unit="metric" data-action="ws_ls_bmi_calculator">
								<div class="ykuk-hidden bmi-alert" ykuk-alert></div>
							    <div class="ykuk-margin">
							        <label class="ykuk-form-label" for="form-horizontal-text">%1$s</label>
							        <div class="ykuk-form-controls ykuk-grid" ykuk-grid>
							            <div class="ykuk-width-1-1">
							             <input class="ykuk-input" id="kg" type="number" min="0" max="400" placeholder="%2$s" required>
							            </div>
							        </div>
							    </div>
							    <div class="ykuk-margin">
							        <label class="ykuk-form-label" for="form-horizontal-select">%3$s</label>
							        <div class="ykuk-form-controls ykuk-grid" ykuk-grid>
							            <div class="ykuk-width-1-1">
							            <input class="ykuk-input" id="cm" type="number" min="10" max="250" placeholder="%4$s" required>
							            </div>
							        </div>
							    </div>
							    <div class="ykuk-margin">
							        <button type="button" class="button-calculate ykuk-button ykuk-button-default">%5$s</button>
							    </div>
							</form>
						</li>
						<li>
							<form id="ws-ls-bmi-calc-imperial" class="ykuk-form-horizontal ykuk-margin-large form-calculate" data-unit="imperial" data-action="ws_ls_bmi_calculator">
								<div class="ykuk-hidden bmi-alert" ykuk-alert>
								    <p></p>
								</div>
							    <div class="ykuk-margin">
							        <label class="ykuk-form-label" for="form-horizontal-text">%1$s</label>
							        <div class="ykuk-form-controls ykuk-grid" ykuk-grid>
							            <div class="ykuk-width-1-2">
							             <input class="ykuk-input" id="stones" type="number" placeholder="%6$s" min="0" max="50">
							            </div>
							            <div class="ykuk-width-1-2">
							            <input class="ykuk-input" id="pounds" type="number" placeholder="%7$s" min="0" max="14">
							            </div>
							        </div>
							    </div>
							    <div class="ykuk-margin">
							        <label class="ykuk-form-label" for="form-horizontal-select">%3$s</label>
							        <div class="ykuk-form-controls ykuk-grid" ykuk-grid>
							            <div class="ykuk-width-1-2">
							             <input class="ykuk-input" id="feet" type="text" placeholder="%8$s" min="3" max="10">
							            </div>
							            <div class="ykuk-width-1-2">
							            <input class="ykuk-input" id="inches" type="text" placeholder="%9$s" min="0" max="12">
							            </div>
							        </div>
							    </div>
							    <div class="ykuk-margin">
							        <button type="button" class="button-calculate ykuk-button ykuk-button-default">%5$s</button>
							    </div>
							</form>
						</li>',
						__( 'Weight', WE_LS_SLUG ),
						__( 'Kg', WE_LS_SLUG ),
						__( 'Height', WE_LS_SLUG ),
						__( 'Cm', WE_LS_SLUG ),
						__( 'Calculate BMI', WE_LS_SLUG ),
						__( 'Stones', WE_LS_SLUG ),
						__( 'Pounds', WE_LS_SLUG ),
						__( 'Feet', WE_LS_SLUG ),
						__( 'Inches', WE_LS_SLUG )
	);

	$html .= '</ul></div>';

	return $html;

}
add_shortcode( 'wt-bmi-calculator', 'ws_ls_bmi_calculator' );

/**
 * JS config for shortcodes
 */
function ws_ls_calculator_js_config() {
	return [ 'ajax-security-nonce'  => wp_create_nonce( 'ws-ls-nonce' ),
	         'ajax-url'             => admin_url( 'admin-ajax.php' )
	];
}

/**
 * AJAX handler for calculating BMI
 */
function ws_ls_bmi_calculator_ajax() {

	check_ajax_referer( 'ws-ls-nonce', 'security' );

	$unit   = ws_ls_post_value('unit' );
	$text   = 'ERROR';
	$class  = 'ykuk-alert-warning';

	if ( 'metric' === $unit ) {

		$cm     = ws_ls_post_value( 'cm', NULL, false, false, 'int' );
		$kg     = ws_ls_post_value( 'kg', NULL, false, false, 'int' );
		$bmi    = ws_ls_calculate_bmi( $cm, $kg );
	} else {

		$stones     = ws_ls_post_value( 'stones', NULL, false, false, 'int' );
		$pounds     = ws_ls_post_value( 'pounds', NULL, false, false, 'int' );

		$kg         = ws_ls_convert_stones_pounds_to_kg( $stones, $pounds );

		$feet       = ws_ls_post_value( 'feet', NULL, false, false, 'int' );
		$inches     = ws_ls_post_value( 'inches', NULL, false, false, 'int' );

		$cm         = ws_ls_heights_imperial_metric( $feet, $inches );

		$bmi        = ws_ls_calculate_bmi( $cm, $kg );

	}

	if ( false === empty( $bmi ) ) {
		$text   = sprintf( '<h3>%s</h3><p>%s <strong>%s</strong>.</p>',
							ws_ls_round_number( $bmi, 1 ),
							__( 'Your weight suggests you are', WE_LS_SLUG ),
							strtolower( ws_ls_bmi_display( $bmi, 'label' ) )
		);
		$class  = ws_ls_calculate_bmi_uikit_class( $bmi );
	}

	$data = [   'css-class' => $class, 'text' => $text ];

	wp_send_json( $data );
}
add_action( 'wp_ajax_ws_ls_bmi_calculator', 'ws_ls_bmi_calculator_ajax' );
add_action( 'wp_ajax_nopriv_ws_ls_bmi_calculator', 'ws_ls_bmi_calculator_ajax' );
