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

	if ( false === WS_LS_IS_PRO ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	$arguments = shortcode_atts( [    'disable-main-font'         => false,                       // If set to true, don't include the main font
	                                  'disable-theme-css'         => false,                       // If set to true, don't include the additional theme CSS used
	                                  'user-id'					=> get_current_user_id() ],
	$user_defined_arguments );

	ws_ls_enqueue_uikit( ! $arguments[ 'disable-theme-css' ], ! $arguments[ 'disable-main-font' ] );

	$html = '<div class="ws-ls-tracker">
				';
	$html .= sprintf( '	<ul ykuk-tab>
   							<li><a href="#">Metric</a></li>
    						<li class="%s"><a href="#" >Imperial</a></li>
    					</ul>',
						'kg' !== ws_ls_setting( 'weight-unit', $arguments[ 'user-id' ] ) ? 'ykuk-active' : ''
	);

	$html .= '<ul class="ykuk-switcher ykuk-margin">
				<li>
					<form class="ykuk-form-horizontal ykuk-margin-large">
						<div class="ykuk-alert-primary" ykuk-alert>
						    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt.</p>
						</div>
					    <div class="ykuk-margin">
					        <label class="ykuk-form-label" for="form-horizontal-text">Weight</label>
					        <div class="ykuk-form-controls ykuk-grid" ykuk-grid>
					            <div class="ykuk-width-1-1">
					             <input class="ykuk-input" id="kg" type="number" min="0" max="400" placeholder="Kg">
					            </div>
					        </div>
					    </div>
					    <div class="ykuk-margin">
					        <label class="ykuk-form-label" for="form-horizontal-select">Height</label>
					        <div class="ykuk-form-controls ykuk-grid" ykuk-grid>
					            <div class="ykuk-width-1-1">
					            <input class="ykuk-input" id="cm" type="number" min="10" max="250" placeholder="Cm">
					            </div>
					        </div>
					    </div>
					</form>
				</li>
				<li>
					<form class="ykuk-form-horizontal ykuk-margin-large">
					    <div class="ykuk-margin">
					        <label class="ykuk-form-label" for="form-horizontal-text">Weight</label>
					        <div class="ykuk-form-controls ykuk-grid" ykuk-grid>
					            <div class="ykuk-width-1-2">
					             <input class="ykuk-input" id="stones" type="number" placeholder="Stones" min="0" max="50">
					            </div>
					            <div class="ykuk-width-1-2">
					            <input class="ykuk-input" id="pounds" type="number" placeholder="Pounds" min="0" max="14">
					            </div>
					        </div>
					    </div>
					    <div class="ykuk-margin">
					        <label class="ykuk-form-label" for="form-horizontal-select">Height</label>
					        <div class="ykuk-form-controls ykuk-grid" ykuk-grid>
					            <div class="ykuk-width-1-2">
					             <input class="ykuk-input" id="feet" type="text" placeholder="Feet" min="3" max="10">
					            </div>
					            <div class="ykuk-width-1-2">
					            <input class="ykuk-input" id="inches" type="text" placeholder="Inches" min="0" max="12">
					            </div>
					        </div>
					    </div>
					</form>
				</li>';

//	// Kg
//	if ( 'kg' ===  $arguments[ 'data-unit' ] ) {

//	}




	$html .= '</ul></div>';

	return $html;

}
add_shortcode( 'wt-bmi-calculator', 'ws_ls_bmi_calculator' );
