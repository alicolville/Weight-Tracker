<?php
defined('ABSPATH') or die("Jog on!");

/**
 * Render the user preferences form
 * @param $user_defined_arguments
 * @return string
 */
function ws_ls_user_preferences_form( $user_defined_arguments ) {

	if ( false === WS_LS_IS_PRO ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

    // If not logged in then return no value
    if ( false === is_user_logged_in() )	{
		return ws_ls_display_blockquote( esc_html__('You must be logged in to edit your settings.', WE_LS_SLUG) , '', false, true);
    }

    $html_output = '';

    $arguments = shortcode_atts( [  'disable-save'          => false,
                                    'hide-aim'              => false,
                                    'hide-dob'              => false,
                                    'hide-height'           => false,
                                    'hide-gender'           => false,
                                    'hide-activity-level'   => false,
                                    'hide-preferences'      => false,
                                    'hide-extras'           => false,
									'hide-email-optout'     => false,
                                    'hide-titles'           => false,
                                    'user-id'               => get_current_user_id(),
                                    'uikit'                 => false,
                                    'redirect-url'          => '',
	                                'show-delete-data'      => true,
	                                'show-user-preferences' => true,
	                                'kiosk-mode'            => false
    ], $user_defined_arguments );

	$user_id = (int) $arguments['user-id'];

    // Have user preferences been allowed in Settings?
    if ( false === ws_ls_user_preferences_is_enabled() && false === is_admin() && false === $arguments[ 'kiosk-mode' ]) {
        return ws_ls_display_blockquote( esc_html__( 'To use this shortcode, please ensure you have enabled the setting "Allow user settings".', WE_LS_SLUG) );
    }

	// Delete all the user's data if selected
	if(  true === ws_ls_to_bool( $arguments['show-delete-data'] ) )	{

		ws_ls_stats_update_for_user( $user_id );
		
		ws_ls_delete_user_data( $user_id, $arguments['kiosk-mode'] );
	}

    // Decide which set of labels to render
	$labels = [
                'height'            => esc_html__( 'Your height:', WE_LS_SLUG ),
				'weight'            => esc_html__( 'In which unit would you like to record your weight:', WE_LS_SLUG ),
				'date'              => esc_html__( 'Display dates in the following formats:', WE_LS_SLUG ),
                'gender'            => esc_html__( 'Your Gender:', WE_LS_SLUG ),
                'dob'               => esc_html__( 'Your Date of Birth:', WE_LS_SLUG ),
                'activitylevel'     => esc_html__( 'Your Activity Level:', WE_LS_SLUG ),
                'aim'               => esc_html__( 'Your aim:' , WE_LS_SLUG )
	];

	// If admin, add notice and override labels
	if( is_admin() ) {

		$labels = [     'height'            => esc_html__( 'Height:', WE_LS_SLUG ),
					    'weight'            => esc_html__( 'Weight unit:', WE_LS_SLUG ),
					    'date'              => esc_html__( 'Date format:', WE_LS_SLUG ),
                        'gender'            => esc_html__( 'Gender:', WE_LS_SLUG ),
                        'dob'               => esc_html__( 'Date of Birth:', WE_LS_SLUG ),
                        'activitylevel'     => esc_html__( 'Activity Level:', WE_LS_SLUG ),
                        'aim'               => esc_html__( 'Aim:', WE_LS_SLUG )
		];

        // If we're in Admin screens, then hide "delete data"
        $arguments[ 'show-delete-data' ] = false;

	} else {
	    // Enqueue front end scripts if needed (mainly for datepicker)
        ws_ls_enqueue_files();
    }

	// If enabled, show user preferences form
	if( false === empty( $arguments[ 'show-user-preferences' ] ) ) {

		$html_output .= '<form class="ws-ls-user-pref-form ykuk-grid-small" ykuk-grid method="post" data-redirect-url=' . esc_url( $arguments[ 'redirect-url' ] ) . '>
							<div class="ws-ls-error-summary">
								<ul></ul>
							</div>
						    <input type="hidden" name="ws-ls-user-pref" value="true" />
							<input type="hidden" id="ws-ls-user-id" value="' . (int) $user_id . '" />';

		if ( false === ws_ls_to_bool( $arguments[ 'hide-aim' ] ) ) {
			$html_output .= ws_ls_form_field_select( [ 'uikit' => $arguments[ 'uikit' ], 'key' => 'ws-ls-aim', 'label' => $labels['aim'], 'values' => ws_ls_aims(), 'selected' => ws_ls_user_preferences_get( 'aim', $user_id ), 'css-class' => 'ws-ls-aboutyou-field' ] );
		}

		if ( false === ws_ls_to_bool( $arguments[ 'hide-extras' ] ) ) {
			$html_output .= apply_filters( 'wlt-filter-user-settings-below-aim', '', $user_id );
		}

		if ( false === ws_ls_to_bool( $arguments[ 'hide-height' ] ) ) {
			$html_output .= ws_ls_form_field_select( [ 'uikit' => $arguments[ 'uikit' ], 'key' => 'ws-ls-height', 'label' => $labels[ 'height' ], 'values' => ws_ls_heights(), 'selected' => ws_ls_user_preferences_get( 'height', $user_id ), 'css-class' => 'ws-ls-aboutyou-field' ] );
		}

		if ( false === ws_ls_to_bool( $arguments[ 'hide-gender' ] ) ) {
			$html_output .= ws_ls_form_field_select( [ 'uikit' => $arguments[ 'uikit' ], 'key' => 'ws-ls-gender', 'label' => $labels[ 'gender' ], 'values' => ws_ls_genders(), 'selected' => ws_ls_user_preferences_get( 'gender', $user_id ), 'css-class' => 'ws-ls-aboutyou-field' ] );
		}

		if ( false === ws_ls_to_bool( $arguments[ 'hide-extras' ] ) ) {
			$html_output .= apply_filters( 'wlt-filter-user-settings-below-gender', '', $user_id );
		}

		if ( false === ws_ls_to_bool( $arguments[ 'hide-activity-level' ] ) ) {
			$html_output .= ws_ls_form_field_select( [ 'uikit' => $arguments[ 'uikit' ], 'key' => 'ws-ls-activity_level', 'label' => $labels[ 'activitylevel' ], 'values' => ws_ls_activity_levels(), 'selected' => ws_ls_user_preferences_get( 'activity_level', $user_id ), 'css-class' => 'ws-ls-aboutyou-field' ] );
		}

		if ( false === ws_ls_to_bool( $arguments[ 'hide-dob' ] ) ) {
			$html_output .= ws_ls_form_field_date( [    'name'          => 'ws-ls-dob',
			                                            'id'            => 'ws-ls-dob',
			                                            'title'         => $labels[ 'dob' ],
			                                            'value'         => ws_ls_get_dob_for_display( $user_id ),
			                                            'css-class'     => 'we-ls-datepicker ws-ls-dob-field ws-ls-aboutyou-field ykuk-width-1-1',
			                                            'uikit'         => $arguments[ 'uikit' ],
			                                            'show-label'    => true ] );
		}

		if ( false === ws_ls_to_bool( $arguments[ 'hide-extras' ] ) ) {
			$html_output .= apply_filters( 'wlt-filter-user-settings-below-dob', '', $user_id );
		}

		if ( false === ws_ls_to_bool( $arguments[ 'hide-preferences' ] ) ) {

			$html_output .= ws_ls_form_field_select( [ 'uikit' => $arguments[ 'uikit' ], 'key' => 'WE_LS_DATA_UNITS', 'label' => $labels[ 'weight' ], 'values' => ws_ls_weight_units(), 'selected' => ws_ls_setting( 'weight-unit', $user_id ) ] );

			$html_output .= ws_ls_form_field_select( [  'key'       => 'WE_LS_US_DATE',
			                                            'label'     => $labels[ 'date' ],
			                                            'uikit'     => $arguments[ 'uikit' ],
			                                            'values'    => [ 'false'     => esc_html__( 'UK (DD/MM/YYYY)', WE_LS_SLUG ), 'true' => esc_html__( 'US (MM/DD/YYYY)', WE_LS_SLUG ) ],
			                                            'selected'  => ( true === ws_ls_setting( 'use-us-dates', $user_id ) ) ? 'true' : 'false' ] );

		}

		if ( false === ws_ls_to_bool( $arguments[ 'hide-email-optout' ] ) ) {
			$html_output .=  sprintf( '<div class="ws-ls-form-row ykuk-width-1-1">
											<h3>%s</h3>
											<p class="ws-ls-hide-if-admin">%s</p>
										</div>', esc_html__( 'Email notifications', WE_LS_SLUG ),
										esc_html__( 'Select the email notifications that you would like to receive:', WE_LS_SLUG )
									);
			$html_output .=  ws_ls_emailer_optout_form();
		}	

		if ( true !== $arguments[ 'disable-save' ] ) {

			$html_output .= sprintf( '	<div>
										<input name="we-ls-user-pref-submit" type="submit" id="we-ls-user-pref-submit" tabindex="%1$d" class="ws-ls-cancel-form button ykuk-button ykuk-button-default ws-ls-remove-on-submit" value="%2$s" />
									</div>
									',
				ws_ls_form_tab_index_next(),
				esc_html__( 'Save Settings', WE_LS_SLUG )
			);

		}

		$html_output .= '</form><br />';
	}

	// If enabled, show Delete data
    if( ( true === isset( $arguments[ 'allow-delete-data' ] ) && true === $arguments[ 'allow-delete-data' ] ) ||
            true === ws_ls_to_bool( $arguments[ 'show-delete-data' ] ) ) {

	    if ( false === ws_ls_to_bool( $arguments[ 'hide-titles' ] ) ) {
		    $html_output .= ws_ls_title( esc_html__( 'Delete existing data', WE_LS_SLUG ) );
	    }

	    $post_url = add_query_arg( 'user-delete-all', 'true', ws_ls_get_url() );

	    $html_output .= sprintf( '<form action="%s" class="ws-ls-user-delete-all ykuk-grid-small" ykuk-grid method="post">
											<div class="ws-ls-error-summary">
                								<ul></ul>
                							</div>', esc_url( $post_url ) );

	    $html_output .= ws_ls_form_field_select( [ 'key'        => 'ws-ls-delete-all',
	                                               'label'      => esc_html__( 'The button below allows you to clear your existing weight history. Confirm:', WE_LS_SLUG ),
	                                               'values'     => [ '' => '',
																	    'yes' => esc_html__( 'DELETE ALL DATA', WE_LS_SLUG )
		                                                            ],
	                                               'uikit'      => $arguments[ 'uikit' ],
	                                               'required'   => true ] );

	    $html_output .= sprintf(' 	<div>
										<input name="submit_button" type="submit" id="we-ls-user-pref-submit" tabindex="%1$d" class="button ykuk-button ykuk-button-default" value="%2$s" />
									</div>
								</form>',
		    ws_ls_form_tab_index_next(),
		    esc_html__( 'Delete', WE_LS_SLUG )
	    );
    }

	return $html_output;
}
add_shortcode( 'wlt-user-settings', 'ws_ls_user_preferences_form' );
add_shortcode( 'wt-user-settings', 'ws_ls_user_preferences_form' );
