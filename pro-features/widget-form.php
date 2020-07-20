<?php

defined('ABSPATH') or die('Jog on!');

class ws_ls_widget_form extends WP_Widget {

    private $field_values;

	function __construct() {

		parent::__construct( 'ws_ls_widget_form', __( 'Weight Tracker - Form', WE_LS_SLUG ),
										[ 'description' => __('Display a quick entry form for logged in users to record their weight for today.', WE_LS_SLUG ) ] );

        $this->field_values = [
                                    'title'                     => __( 'Your weight today', WE_LS_SLUG ),
									'force_todays_date'         => 'yes',
			                        'not-logged-in-message'     => '',
									'exclude-measurements'      => 'no',
                                    'exclude-meta-fields'       => 'yes',
									'hide-notes'                => get_option( 'ws-ls-allow-user-notes', 'no' ),
									'redirect-url'              => ''
        ];

	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

        // User logged in?
        if( true === is_user_logged_in() ) {

			ws_ls_enqueue_files();

			echo $args[ 'before_widget' ];
			echo $args[ 'before_title' ] . apply_filters( 'widget_title', $instance[ 'title' ] ). $args[ 'after_title' ];

			$force_to_todays_date   = ( 'yes' == $instance[ 'force_todays_date' ] );
			$exclude_measurements   = ( false === empty( $instance[ 'exclude-measurements' ] ) && 'yes' == $instance[ 'exclude-measurements' ] );
            $exclude_meta_fields    = ( true === empty( $instance[ 'exclude-meta-fields' ] ) || 'yes' == $instance[ 'exclude-meta-fields'] );
			$hide_notes             = ( false === empty( $instance[ 'hide-notes' ]) && 'yes' == $instance[ 'hide-notes' ] );
			$redirect_url           = ( false === empty( $instance[ 'redirect-url' ] ) ) ? $instance[ 'redirect-url' ]  : '';

	        echo ws_ls_form_weight( [    'redirect-url'         => $redirect_url,
	                                     'hide-login-message'   => true,
		                                 'hide-fields-meta'     => ( true === $exclude_meta_fields || true === $exclude_measurements ),
		                                 'option-force-today'   => $force_to_todays_date,
		                                 'hide-notes'           => $hide_notes
	        ] );

            echo $args[ 'after_widget' ];

        } else if ( false === empty( $instance[ 'not-logged-in-message' ] ) ) {
            echo $args[ 'before_widget' ];
            echo $args[ 'before_title' ] . apply_filters( 'widget_title', $instance[ 'title' ] ). $args[ 'after_title' ];
            printf( '<p>%s</p>', esc_html( $instance[ 'not-logged-in-message' ] ) );
            echo $args[ 'after_widget' ];
        }
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

        // Loop through expected fields and process
        foreach( $this->field_values as $key => $default ) {
            $field_values[ $key ] = false === empty( $instance[ $key ] ) ? $instance[ $key ] : $default;
        }

		?>
        <p>Display a quick entry form for logged in users to record their weight for today. The widget will be hidden if the user is <strong>not logged in</strong>.</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title', WE_LS_SLUG); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $field_values['title'] ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'force_todays_date' ); ?>"><?php _e('Allow user to specify a date?', WE_LS_SLUG ); ?></label>
            <select class="widefat" name="<?php echo $this->get_field_name( 'force_todays_date' ); ?>" id="<?php echo $this->get_field_id( 'force_todays_date' ); ?>">
                    <option value="yes" <?php selected( $field_values[ 'force_todays_date' ], 'yes' ); ?>><?php _e('No. Automatically set to today\'s date', WE_LS_SLUG); ?></option>
                    <option value="no" <?php selected( $field_values[ 'force_todays_date' ], 'no' ); ?>><?php _e('Allow user to specify a date', WE_LS_SLUG); ?></option>
            </select>
		</p>
		<p>
            <label for="<?php echo $this->get_field_id( 'exclude-meta-fields' ); ?>"><?php _e('Hide Meta Fields (Pro)?', WE_LS_SLUG ); ?></label>
            <select class="widefat" name="<?php echo $this->get_field_name( 'exclude-meta-fields' ); ?>" id="<?php echo $this->get_field_id( 'exclude-meta-fields' ); ?>">
                <option value="no" <?php selected( $field_values[ 'exclude-meta-fields' ], 'no' ); ?>><?php _e('No', WE_LS_SLUG ); ?></option>
                <option value="yes" <?php selected( $field_values[ 'exclude-meta-fields' ], 'yes' ); ?>><?php _e('Yes', WE_LS_SLUG ); ?></option>
            </select>
        </p>
		<p><?php
				$hide_notes = ( false === empty( $field_values['hide-notes'] ) ) ? $field_values['hide-notes'] : 'no';
			?>
			<label for="<?php echo $this->get_field_id( 'hide-notes' ); ?>"><?php _e('Hide notes field?', WE_LS_SLUG); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'hide-notes' ); ?>" id="<?php echo $this->get_field_id( 'hide-notes' ); ?>">
				<option value="no" <?php selected( $hide_notes, 'no'); ?>><?php _e('No', WE_LS_SLUG); ?></option>
				<option value="yes" <?php selected( $hide_notes, 'yes'); ?>><?php _e('Yes', WE_LS_SLUG); ?></option>
			</select>
		</p>
        <p>
			<label for="<?php echo $this->get_field_id( 'not-logged-in-message' ); ?>"><?php _e('Message to display if not logged in', WE_LS_SLUG); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'not-logged-in-message' ); ?>" name="<?php echo $this->get_field_name( 'not-logged-in-message' ); ?>" type="text" value="<?php echo esc_attr( $field_values['not-logged-in-message'] ); ?>">
        </p>
        <p><small><?php _e('By default the widget is hidden if the user is not logged in. If you wish, you can display a message to the visitor instead.', WE_LS_SLUG); ?></small></p>
		<p>
			<label for="<?php echo $this->get_field_id( 'redirect-url' ); ?>"><?php _e('Redirect URL (Defaults to current page)', WE_LS_SLUG); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'redirect-url' ); ?>" name="<?php echo $this->get_field_name( 'redirect-url' ); ?>" type="text" value="<?php echo esc_attr( $field_values['redirect-url'] ); ?>">
		</p>
        <p><small><?php _e('Specify where the user should be redirected to after completing the form. Note: The URL must be within the site domain.', WE_LS_SLUG); ?></small></p>

		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = [];

		foreach( $this->field_values as $key => $value ) {
			$instance[ $key ] = false === empty( $new_instance[ $key ] ) ? strip_tags( $new_instance[ $key ] ) : '';
		}

		return $instance;
	}

}
