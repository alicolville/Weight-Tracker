<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Populate the photos tab of [wlt] shortcode
 *
 * @param null $user_id
 * @return string
 */
function ws_ls_shortcode_wlt_display_photos_tab( $user_id = null ) {

	if( false === WS_LS_IS_PRO ) {
		return '';
	}

	$user_id = ( NULL === $user_id ) ? get_current_user_id() : $user_id;

	$html = '';

	if ( $user_id ) {

        $html .= sprintf('<h3>%s</h3>', __( 'Photos', WE_LS_SLUG ) );

		$photo_count = ws_ls_photos_db_count_photos( $user_id, true );

		if ( $photo_count > 0 ) {

			$html .= sprintf('<p>%s <strong>%s</strong> %s:</p>',
									__('You have uploaded', WE_LS_SLUG),
									$photo_count,
									$photo_count > 1 ? __('photos', WE_LS_SLUG) : __('photo', WE_LS_SLUG)

			);

			$html .= ws_ls_photos_shortcode_gallery( ['user-id' => $user_id] );

		} else {
			$html .= '<p>' . __('It looks like you haven\'t uploaded any photos yet', WE_LS_SLUG) . '.</p>';
		}
	}

	return $html;
}

/**
 * Populate the advanced tab of [wlt] shortcode
 *
 * @param $arguments
 * @return string
 */
function ws_ls_shortcode_wlt_display_advanced_tab( $arguments ) {

	if( false === WS_LS_IS_PRO_PLUS ) {
		return '';
	}

    $html = '';

	$user_id = ( true === empty( $arguments[ 'user-id' ] ) ) ? get_current_user_id() : (int) $arguments[ 'user-id' ];

    $include_narrative = ! ws_ls_to_bool( $arguments[ 'hide-advanced-narrative'] );

	if ( $user_id ) {

		// BMI
		$html .= sprintf('<h3>%s</h3>', __( 'BMI (Body Mass Index)', WE_LS_SLUG ) );

		if ( ws_ls_shortcode_if_value_exist( $user_id, [ 'weight', 'height' ] ) ) {

			if ( true === $include_narrative ) {
				$html .= sprintf( '<p>%s</p>',
											__('The BMI (Body Mass Index) is used by the medical profession to quickly determine a person’s weight in regard to their height. From a straight forward calculation the BMI factor can be gained and may be used to determine if a person is underweight, of normal weight, overweight or obese.', WE_LS_SLUG ) );
			}

			$html .= sprintf('	<div class="ws-ls-tab-advanced-data">
										<p>%s: <span>%s</span></p>
									</div>',
				__('Your current BMI is', WE_LS_SLUG),
				ws_ls_shortcode_bmi( [ 'user-id' => $user_id, 'display' => 'both' ] )

			);

		} else {
			$html .= sprintf( '<p>%s</p>', __( 'Before we can calculate your BMI, we need your current weight and height.', WE_LS_SLUG ) );
		}

		$got_bmr = ws_ls_shortcode_if_value_exist( $user_id, 'bmr' );
		$bmr_missing_text = sprintf( '<p>%s</p>', __( 'To allow us to calculate this, we need your latest weight, date of birth, height and gender.', WE_LS_SLUG ) );

		// BMR
		$html .= sprintf('<h3>%s</h3>', __( 'BMR (Basal Metabolic Rate)', WE_LS_SLUG ) );

		if ( true === $got_bmr ) {
			if ( true === $include_narrative ) {
				$html .= sprintf( '<p>%s</p>',  __( 'BMR is short for Basal Metabolic Rate. The Basal Metabolic Rate is the number of calories required to keep your body functioning at rest, also known as your metabolism. We calculate your BMR using formulas provided by www.diabetes.co.uk.', WE_LS_SLUG ) );
			}

			$html .= sprintf('	<div class="ws-ls-tab-advanced-data">
										<p>%s: <span>%s</span></p>
									</div>',
				__('Your current BMR is', WE_LS_SLUG),
				ws_ls_shortcode_bmr(['user-id' => $user_id])
			);
		} else {
			$html .= $bmr_missing_text;
		}

		// Calories
		$html .= sprintf( '<h3>%s</h3>', __('Suggested Calorie Intake', WE_LS_SLUG ) );

		if ( true === $got_bmr ) {

			if ( true === $include_narrative ) {
				$html .= sprintf( '<p>%s</p>', __('Once we know your BMR (the number of calories to keep you functioning at rest), we can go on to give you suggestions on how to spread your calorie intake across the day. Firstly we split the figures into daily calorie intake to maintain weight and daily calorie intake to lose weight. Daily calorie intake to lose weight is calculated based on NHS advice – they suggest to lose 1 – 2lbs a week you should subtract 600 calories from your BMR. The two daily figures can be further broken down by recommending how to split calorie intake across the day i.e. breakfast, lunch, dinner and snacks.', WE_LS_SLUG ) );
			}

			$html .= sprintf('	<div class="ws-ls-tab-advanced-data">
										%s
									</div>',
									ws_ls_harris_benedict_render_table( $user_id, false,  'ws-ls-footable' )
			);

		} else {
			$html .= $bmr_missing_text;
		}

		// Macro N
		$html .= sprintf('<h3>%s</h3>', __('Macronutrients', WE_LS_SLUG ) );

		if ( true === $got_bmr ) {

			if ( true === $include_narrative ) {

				$html .= sprintf('%s %s %s %s %s %s %s.',
					__('With calories calculated, the we can recommend how those calories should be split into Fats, Carbohydrates and Proteins.  Based on 2010 Dietary Guidelines for Americans we have split it in the following manner:' , WE_LS_SLUG),
					__('Carbohydrates', WE_LS_SLUG),
					ws_ls_harris_benedict_setting( 'ws-ls-macro-carbs' ) . '%',
					__('of calories, Fat', WE_LS_SLUG),
					ws_ls_harris_benedict_setting( 'ws-ls-macro-fats' ) . '%',
					__('and Protein', WE_LS_SLUG),
					ws_ls_harris_benedict_setting( 'ws-ls-macro-proteins' ) . '%'
				);

			}

			$html .= sprintf('	<div class="ws-ls-tab-advanced-data">
										%s
									</div>',
									ws_ls_macro_render_table($user_id, false,  'ws-ls-footable')
			);

		} else {
			$html .= $bmr_missing_text;
		}
	}

    return $html;
}
