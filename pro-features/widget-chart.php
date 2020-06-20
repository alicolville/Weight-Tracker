<?php
defined( 'ABSPATH' ) or die( 'Jog on!' );


class ws_ls_widget_chart extends WP_Widget {

	private $field_values;

	function __construct() {

		parent::__construct(
			'ws_ls_widget_chart',
			__( 'Weight Tracker - Chart', WE_LS_SLUG ),
			array( 'description' => __( 'Display a chart to see your current progress.', WE_LS_SLUG ) ) // Args
		);

		$this->field_values = array(
			'title'                 => __( 'Weight Tracker', WE_LS_SLUG ),
			'max-points'            => 5,
			'user-id'               => '',
			'type'                  => 'line',
			'not-logged-in-message' => '',
			'exclude-measurements'  => 'no'
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 *
	 * @see WP_Widget::widget()
	 *
	 */
	public function widget( $args, $instance ) {

		$html = '';

		// User logged in?
		if ( is_user_logged_in() ) {

			$chart_arguments = [ 'user-id'         => get_current_user_id(),
			                     'max-data-points' => ws_ls_option( 'ws-ls-max-points', '25', true )
			];

			if ( false === empty( $instance[ 'user-id' ] ) ) {
				$chart_arguments[ 'user-id' ] = (int) $instance[ 'user-id' ];
			}
			if ( false === empty( $instance[ 'max-points' ] ) ) {
				$chart_arguments[ 'max-data-points' ] = (int) $instance[ 'max-points' ];
			}
			if ( true === in_array( $instance[ 'type' ], [ 'bar', 'line' ] ) ) {
				$chart_arguments[ 'type' ] = $instance[ 'type' ];
			}

			/**
			 * Should we hide meta fields? I've left the old setting name so existing widget placements were not broken.
			 */
			if ( false === empty( $instance[ 'exclude-measurements' ] ) && 'yes' === $instance['exclude-measurements'] ) {
				$chart_arguments[ 'show-meta-fields' ] = false;
			}

			$weight_data = ws_ls_entries_get( [ 'user-id' => $chart_arguments['user-id'], 'limit' => $chart_arguments['max-data-points'], 'prep' => true ] );

			$chart_arguments['height']  = false;

			if ( false === empty( $weight_data ) ) {

				$html = ws_ls_display_chart( $weight_data, $chart_arguments );

			} else {
				echo '<!-- WT Chart: No user data found for given ID -->';
			}
		} elseif ( false === empty( $instance['not-logged-in-message'] ) ) {
			$html = esc_html( $instance['not-logged-in-message'] );
		}

		echo $args['before_widget'];
		echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		echo $html;
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @param array $instance Previously saved values from database.
	 *
	 * @see WP_Widget::form()
	 *
	 */
	public function form( $instance ) {

		// Loop through expected fields and process
		foreach ( $this->field_values as $key => $default ) {
			$field_values[ $key ] = ! empty( $instance[ $key ] ) ? $instance[ $key ] : $default;
		}

		?>
		<p><?php echo __( 'Display a chart and / or form for the current user. The widget will be hidden if the user is <strong>not logged in</strong>.', WE_LS_SLUG ); ?></p>

		<p>
			<?php
				$field_id = $this->get_field_id( 'title' );
			?>
			<label for="<?php echo $field_id; ?>"><?php echo __( 'Title', WE_LS_SLUG ); ?></label>
			<input class="widefat" id="<?php echo $field_id; ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $field_values['title'] ); ?>">
		</p>
		<p>
			<?php
				$field_id = $this->get_field_id( 'type' );
			?>
			<label for="<?php echo $field_id; ?>"><?php echo __( 'Type', WE_LS_SLUG ); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'type' ); ?>" id="<?php echo $field_id; ?>">
				<option value="line" <?php selected( $field_values['type'], 'line' ); ?>><?php echo __( 'Line Chart', WE_LS_SLUG ); ?></option>
				<option value="bar" <?php selected( $field_values['type'], 'bar' ); ?>><?php echo __( 'Bar Chart', WE_LS_SLUG ); ?></option>
			</select>
		</p>
		<p>
			<?php
				$field_id = $this->get_field_id( 'max-points' );
			?>
			<label for="<?php echo $field_id; ?>"><?php echo __( 'Maximum number of plot points on chart', WE_LS_SLUG ); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'max-points' ); ?>" id="<?php echo $field_id; ?>">
				<option value="5" <?php selected( $field_values['max-points'], '5' ); ?>>5</option>
				<option value="10" <?php selected( $field_values['max-points'], '10' ); ?>>10</option>
				<option value="15" <?php selected( $field_values['max-points'], '15' ); ?>>15</option>
				<option value="25" <?php selected( $field_values['max-points'], '25' ); ?>>25</option>
			</select>
		</p>
		<p>
			<?php
				$field_id = $this->get_field_id( 'not-logged-in-message' );
			?>
			<label
				for="<?php echo $field_id; ?>"><?php echo __( 'Message to display if not logged in', WE_LS_SLUG ); ?></label>
			<input class="widefat" id="<?php echo $field_id; ?>" name="<?php echo $this->get_field_name( 'not-logged-in-message' ); ?>" type="text" value="<?php echo esc_attr( $field_values['not-logged-in-message'] ); ?>">
		</p>
		<p>
			<small><?php echo __( 'By default the widget is hidden if the user is not logged in. If you wish, you can display a message to the visitor instead.', WE_LS_SLUG ); ?></small>
		</p>
		<p>
			<?php
			$field_id = $this->get_field_id( 'user-id' );
			?>
			<label for="<?php echo $field_id; ?>"><?php echo __( 'User ID (leave blank to show chart for current user)', WE_LS_SLUG ); ?></label>
			<input class="widefat" id="<?php echo $field_id; ?>" name="<?php echo $this->get_field_name( 'user-id' ); ?>" type="text" value="<?php echo (int) $field_values['user-id']; ?>">
		</p>
		<p>
			<small><?php echo __( 'Note: By default, the chart will be displayed in the weight unit chosen by the logged in user. If not specified the plugin default will be used.', WE_LS_SLUG ); ?></small>
		</p>

		<p><?php
			$field_id = $this->get_field_id( 'exclude-measurements' );
			?>
			<label for="<?php echo $field_id; ?>"><?php echo __( 'Hide Meta Fields (Pro)?', WE_LS_SLUG ); ?></label>
			<select class="widefat" name="<?php echo $this->get_field_name( 'exclude-measurements' ); ?>" id="<?php echo $field_id; ?>">
				<option value="no" <?php selected( $field_values['exclude-measurements'], 'no' ); ?>><?php echo __( 'No', WE_LS_SLUG ); ?></option>
				<option value="yes" <?php selected( $field_values['exclude-measurements'], 'yes' ); ?>><?php echo __( 'Yes', WE_LS_SLUG ); ?></option>
			</select>
		</p>

		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 * @see WP_Widget::update()
	 *
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = array();

		foreach ( $this->field_values as $key => $value ) {
			$instance[ $key ] = ! empty( $new_instance[ $key ] ) ? strip_tags( $new_instance[ $key ] ) : '';
		}

		return $instance;
	}

}
