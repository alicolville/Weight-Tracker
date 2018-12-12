<?php
	defined('ABSPATH') or die('Jog on!');


class ws_ls_widget_progress_bar extends WP_Widget {

    private $field_values = array();

	function __construct() {
		parent::__construct(
			'ws_ls_widget_progress_bar',
			__('Weight Tracker - Progress Bar', WE_LS_SLUG),
			array( 'description' => __('A progress bar to indicate weight loss towards target.', WE_LS_SLUG) ) // Args
		);

        $this->field_values = array(
            'title' => __('Weight Progress', WE_LS_SLUG),
			'type' => 'line',
			'stroke-width' => 3,
			'stroke-colour' => '#FFEA82',
			'trail-width' => 1,
			'trail-colour' => '#EEE',
			'text-colour' => '#000',
			'animation-duration' => 1400,
			'percentage-text' => __('towards your target of {t}.', WE_LS_SLUG)
        );
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
        if(is_user_logged_in()) {

			echo $args['before_widget'];
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];

			if(('circle' == $instance['type'])) {
				echo '<center>';
			}

			echo ws_ls_shortcode_progress_bar($instance);

			if(('circle' == $instance['type'])) {
				echo '</center>';
			}

            echo $args['after_widget'];
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
        foreach($this->field_values as $key => $default) {
            $field_values[$key] = !empty($instance[$key]) ? $instance[$key] : $default;
        }
		?>
        <p>Display a progress bar towards the user's weight target.</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title', WE_LS_SLUG); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $field_values['title'] ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e('Chart type', WE_LS_SLUG); ?></label>
            <select class="widefat" name="<?php echo $this->get_field_name( 'type' ); ?>" id="<?php echo $this->get_field_id( 'type' ); ?>">
				<option value="line" <?php selected( $field_values['type'], 'line'); ?>>Line</option>
				<option value="circle" <?php selected( $field_values['type'], 'circle'); ?>>Circle</option>
            </select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'stroke-width' ); ?>"><?php _e('Stroke Width', WE_LS_SLUG); ?></label>
            <select class="widefat" name="<?php echo $this->get_field_name( 'stroke-width' ); ?>" id="<?php echo $this->get_field_id( 'stroke-width' ); ?>">
				<?php for ($i=1; $i <= 10; $i++): ?>
					<option value="<?php echo $i; ?>" <?php selected( $field_values['stroke-width'], $i); ?>><?php echo $i; ?></option>
				<?php endfor; ?>
            </select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'stroke-colour' ); ?>"><?php _e('Stroke Colour', WE_LS_SLUG); ?></label> <br />
			<input class="widefat" id="<?php echo $this->get_field_id( 'stroke-colour' ); ?>" name="<?php echo $this->get_field_name( 'stroke-colour' ); ?>" type="color" value="<?php echo esc_attr($field_values['stroke-colour']); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'trail-width' ); ?>"><?php _e('Trail Width', WE_LS_SLUG); ?></label>
            <select class="widefat" name="<?php echo $this->get_field_name( 'trail-width' ); ?>" id="<?php echo $this->get_field_id( 'trail-width' ); ?>">
				<?php for ($i=1; $i <= 10; $i++): ?>
					<option value="<?php echo $i; ?>" <?php selected( $field_values['trail-width'], $i); ?>><?php echo $i; ?></option>
				<?php endfor; ?>
            </select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'trail-colour' ); ?>"><?php _e('Trail Colour', WE_LS_SLUG); ?></label> <br />
			<input class="widefat" id="<?php echo $this->get_field_id( 'trail-colour' ); ?>" name="<?php echo $this->get_field_name( 'trail-colour' ); ?>" type="color" value="<?php echo esc_attr($field_values['trail-colour']); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'text-colour' ); ?>"><?php _e('Text Colour', WE_LS_SLUG); ?></label> <br />
			<input class="widefat" id="<?php echo $this->get_field_id( 'text-colour' ); ?>" name="<?php echo $this->get_field_name( 'text-colour' ); ?>" type="color" value="<?php echo esc_attr($field_values['text-colour']); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'animation-duration' ); ?>"><?php _e('Animation Duration', WE_LS_SLUG); ?></label>
            <select class="widefat" name="<?php echo $this->get_field_name( 'animation-duration' ); ?>" id="<?php echo $this->get_field_id( 'animation-duration' ); ?>">
				<?php for ($i=200; $i <= 3000; $i+=200): ?>
					<option value="<?php echo $i; ?>" <?php selected( $field_values['animation-duration'], $i); ?>><?php echo $i; ?>ms</option>
				<?php endfor; ?>
            </select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'percentage-text' ); ?>"><?php _e('Text to be displayed under bar. Use {t} to display target weight.', WE_LS_SLUG); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'percentage-text' ); ?>" name="<?php echo $this->get_field_name( 'percentage-text' ); ?>" type="text" value="<?php echo esc_attr( $field_values['percentage-text'] ); ?>">
		</p>
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

		$instance = array();

	    foreach($this->field_values as $key => $value) {
	        $instance[$key] = !empty($new_instance[$key]) ? strip_tags($new_instance[$key]) : '';
	    }

		return $instance;
	}

}
