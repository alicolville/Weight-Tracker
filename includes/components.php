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
        $text_date  = sprintf ( '<br /><span class="ykuk-info-box-meta"><a href="#" ykuk-switcher-item="next">%s</a></span>', $latest_entry[ 'display-date' ] );

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
                                <span class="ykuk-info-box-header" ykuk-tooltip="The weight you have entered most recently.">Latest Weight</span><br />
                                <span class="ykuk-text-bold">
                                    %1$s
                                </span>
                                %2$s
                        </div>
                    </div>',
                    $text_data,
                    $text_date
    );
}


