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

//	if ( false === is_user_logged_in() )	{
//		//$html .= ws_ls_component_alert( __( 'You need to be logged in to record your weight.', WE_LS_SLUG ), 'primary', false, true );
//	}

	$html .= sprintf( '	<ul ykuk-tab>
   							<li><a href="#">Metric</a></li>
    						<li class="%s"><a href="#" >Imperial</a></li>
						</ul>',
						'kg' !== ws_ls_setting( 'weight-unit', $arguments[ 'user-id' ] ) ? 'ykuk-active' : ''
	);

	$html .= '<ul class="ykuk-switcher ykuk-margin">';

	$html .= '<li>';

	$html .= ws_ls_form_field_number( [     'name'          => 'ws-ls-weight-kg',
	                                        'placeholder'   =>  __( 'kg', WE_LS_SLUG ),
	                                      //  'value'         => ( false === empty( $arguments[ 'entry' ][ 'kg' ] ) ) ? $arguments[ 'entry' ][ 'kg' ] : ''
	] );


	$html .= '</li>';

	$html .= '<li>
<form class="ykuk-form-horizontal ykuk-margin-large">

    <div class="ykuk-margin">
        <label class="ykuk-form-label" for="form-horizontal-text">Text</label>
        <div class="ykuk-form-controls ykuk-grid" ykuk-grid>
        	<div class="ykuk-width-1-2">
           	 <input class="ykuk-input" id="form-horizontal-text" type="text" placeholder="Stones">
           	</div>
           	<div class="ykuk-width-1-2">
            <input class="ykuk-input" id="form-horizontal-text" type="text" placeholder="Pounds">
            </div>
        </div>
    </div>

    <div class="ykuk-margin">
        <label class="ykuk-form-label" for="form-horizontal-select">Select</label>
        <div class="ykuk-form-controls">
            <select class="ykuk-select" id="form-horizontal-select">
                <option>Option 01</option>
                <option>Option 02</option>
            </select>
        </div>
    </div>

    <div class="ykuk-margin">
        <div class="ykuk-form-label">Radio</div>
        <div class="ykuk-form-controls ykuk-form-controls-text">
            <label><input class="ykuk-radio" type="radio" name="radio1"> Option 01</label><br>
            <label><input class="ykuk-radio" type="radio" name="radio1"> Option 02</label>
        </div>
    </div>

</form>
</form>
				<form class="ykuk-grid" ykuk-grid>
					<label class="ykuk-width-1-1 ykuk-form-controls">Weight</label>
					<div class="ykuk-width-1-2">';

						$html .= ws_ls_form_field_number( [     'name'          => 'ws-ls-weight-stones',
						                                        'css-class'     => 'ykuk-input',
						                                        'uikit'         => true,
						                                        'placeholder'   => __( 'Stones', WE_LS_SLUG )
						                                        //'value'         => ( false === empty( $arguments[ 'entry' ][ 'stones' ] ) ) ? $arguments[ 'entry' ][ 'stones' ] : '' ]
						]);

	$html .=        '</div>
					<div class="ykuk-width-1-2">';

	$html .= ws_ls_form_field_number( [    'name'          => 'ws-ls-weight-pounds',
	                                       'max'           => '13.99',
	                                       'css-class'     => 'ykuk-input',
	                                       'uikit'         => true,
	                                       'placeholder'   => __( 'Pounds', WE_LS_SLUG )
	                                       //'value' => ( true === isset( $arguments[ 'entry' ][ 'pounds' ] ) ) ? $arguments[ 'entry' ][ 'pounds' ] : '' ]
	]);

	$html .=        '</div>';

	$html .= '</form></li>';

//	// Kg
//	if ( 'kg' ===  $arguments[ 'data-unit' ] ) {

//	}




	$html .= '</ul></div>';

	return $html;

}
add_shortcode( 'wt-bmi-calculator', 'ws_ls_bmi_calculator' );
