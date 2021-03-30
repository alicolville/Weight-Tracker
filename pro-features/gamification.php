<?php

defined('ABSPATH') or die( 'Jog on!' );

/*
 * Gamification - support for myCred
 *
 * https://codex.mycred.me/chapter-vi/functions/
 */

/**
 * Add hooks for Target and Weight Entry
 * @param $installed
 * @param $point_type
 *
 * @return mixed
 */
function ws_ls_mycred_add_hooks( $installed, $point_type ) {

	// Weight added
	$installed[ 'weight_entry' ] = [	'title'        => __( 'Weight Tracker: Weight Entry Added', WE_LS_SLUG ),
										'description'  => __( 'Reward a user when they have recorded a new weight entry.', WE_LS_SLUG ),
										'callback'     => [ 'ws_ls_mycred_weight_entry_class' ]
	];

	// Target added
	$installed[ 'target_set' ] = 	[	'title'        => __( 'Weight Tracker: Target set', WE_LS_SLUG ),
										'description'  => __( 'Reward a user when they have set their target.', WE_LS_SLUG ),
										'callback'     => [ 'ws_ls_mycred_target_set_class' ]
	];



	return $installed;

}
add_filter( 'mycred_setup_hooks', 'ws_ls_mycred_add_hooks', 10, 2 );

/**
 * Load custom myCred hooks
 */
function ws_ls_mycred_load_hooks() {

	// Weight entry award
	class ws_ls_mycred_weight_entry_class extends myCRED_Hook {

		/**
		 * Construct
		 * Used to set the hook id and default settings.
		 */
		function __construct( $hook_prefs, $type ) {

			parent::__construct( array(
				'id'       => 'weight_entry',
				'defaults' => array( 'weight_entry'    => [	'creds'  => 10,
															'log'    => __( 'Weight entry added', WE_LS_SLUG ),
															'limit'  => '0/x'
															]
				)
			), $hook_prefs, $type );

		}

		/**
		 * Run
		 * Fires by myCRED when the hook is loaded.
		 * Used to hook into any instance needed for this hook
		 * to work.
		 */
		public function run() {
			// Hook on to WT add/edit entry
			add_action( 'wlt-hook-data-added-edited', [ $this, 'weight_entry_add' ] );
		}

		/**
		 * Add award for Weight entry add
		 * @param $entry
		 */
		public function weight_entry_add( $entry ) {

			// Only interested in weight entries
			if ( 'weight-measurements' !== $entry[ 'type' ] ) {
				return;
			}

			// Only interested in weight entries that have been added
			if ( 'add' !== $entry[ 'mode' ] ) {
				return;
			}

			// Have we reached the limit defined by admin against the myCred hook?
			if ( true === $this->over_hook_limit( 'weight_entry', 'weight_entry', $entry[ 'user-id' ] ) ) {
				return;
			}

			$this->core->add_creds(	'weight_entry',
									$entry[ 'user-id' ],
									$this->prefs[ 'weight_entry' ][ 'creds' ],
									$this->prefs[ 'weight_entry' ][ 'log' ],
									'weight_entry'
			);
		}

		/**
		 * Hook Settings
		 * Needs to be set if the hook has settings.
		 */
		public function preferences() {

			// Our settings are available under $this->prefs
			$prefs = $this->prefs;

			?>

			<label class="subheader"><?php _e( 'Log template', WE_LS_SLUG ); ?></label>
			<ol>
				<li>
					<div class="h2"><input type="text" name="<?php echo $this->field_name( [ 'weight_entry' => 'log' ] ); ?>" id="<?php echo $this->field_id( [ 'weight_entry' => 'log' ] ); ?>" value="<?php echo esc_attr( $prefs[ 'weight_entry' ][ 'log' ] ); ?>" class="long" /></div>
					<span class="description"></span>
				</li>
			</ol>
			<label class="subheader"><?php _e( 'Points', WE_LS_SLUG ); ?></label>
			<ol>
				<li>
					<div class="h2"><input type="number" name="<?php echo $this->field_name( [ 'weight_entry' => 'creds' ] ); ?>" id="<?php echo $this->field_id( [ 'weight_entry' => 'creds' ] ); ?>" value="<?php echo esc_attr( $prefs['weight_entry']['creds'] ); ?>" class="long" /></div>
				</li>
			</ol>
			<label class="subheader"><?php _e( 'Limit', WE_LS_SLUG ); ?></label>
			<ol>
				<li>
					<div class="h2"><?php echo $this->hook_limit_setting( $this->field_name( [ 'weight_entry' => 'limit' ] ), $this->field_id( [ 'weight_entry' => 'limit' ]  ), $prefs['weight_entry']['limit'] ); ?></div>
					<span class="description"><?php _e( 'Limit the number of times this award can be given within the specified time limit.', WE_LS_SLUG ); ?></span>
				</li>
			</ol>

			<?php
		}

		/**
		 * Sanitize Preferences
		 * If the hook has settings, this method must be used
		 * to sanitize / parsing of settings.
		 */
		public function sanitise_preferences( $data ) {

			if ( isset( $data['weight_entry']['limit'] ) && isset( $data['weight_entry']['limit_by'] ) ) {

				$limit = sanitize_text_field( $data['weight_entry']['limit'] );
				if ( $limit == '' ) {
					$limit = 0;
				}
				$data['weight_entry']['limit'] = $limit . '/' . $data['weight_entry']['limit_by'];
				unset( $data['weight_entry']['limit_by'] );
			}

			return $data;
		}
	}

	// Target set award
	class ws_ls_mycred_target_set_class extends myCRED_Hook {

		/**
		 * Construct
		 * Used to set the hook id and default settings.
		 */
		function __construct( $hook_prefs, $type ) {

			parent::__construct( [
									'id'       => 'target_set',
									'defaults' => [ 'target_set'    => [	'creds'  => 10,
																			'log'    => __( 'Weight target set', WE_LS_SLUG ),
																			'limit'  => '0/x'
									]
								]
			], $hook_prefs, $type );

		}

		/**
		 * Run
		 * Fires by myCRED when the hook is loaded.
		 * Used to hook into any instance needed for this hook
		 * to work.
		 */
		public function run() {
			// Hook on to WT add/edit entry
			add_action( 'wlt-hook-data-added-edited', [ $this, 'target_set' ] );
		}

		/**
		 * Add award for Weight entry add
		 * @param $entry
		 */
		public function target_set( $entry ) {

			// Only interested in target
			if ( 'target' !== $entry[ 'type' ] ) {
				return;
			}

			if ( 'update' !== $entry[ 'mode' ] ) {
				return;
			}

			// Have we reached the limit defined by admin against the myCred hook?
			if ( true === $this->over_hook_limit( 'target_set', 'target_set', $entry[ 'user-id' ] ) ) {
				return;
			}

			$this->core->add_creds(	'target_set',
				$entry[ 'user-id' ],
				$this->prefs[ 'target_set' ][ 'creds' ],
				$this->prefs[ 'target_set' ][ 'log' ],
				'target_set'
			);
		}

		/**
		 * Hook Settings
		 * Needs to be set if the hook has settings.
		 */
		public function preferences() {

			// Our settings are available under $this->prefs
			$prefs = $this->prefs;

			?>

			<label class="subheader"><?php _e( 'Log template', WE_LS_SLUG ); ?></label>
			<ol>
				<li>
					<div class="h2"><input type="text" name="<?php echo $this->field_name( [ 'target_set' => 'log' ] ); ?>" id="<?php echo $this->field_id( [ 'target_set' => 'log' ] ); ?>" value="<?php echo esc_attr( $prefs[ 'target_set' ][ 'log' ] ); ?>" class="long" /></div>
					<span class="description"></span>
				</li>
			</ol>
			<label class="subheader"><?php _e( 'Points', WE_LS_SLUG ); ?></label>
			<ol>
				<li>
					<div class="h2"><input type="number" name="<?php echo $this->field_name( [ 'target_set' => 'creds' ] ); ?>" id="<?php echo $this->field_id( [ 'target_set' => 'creds' ] ); ?>" value="<?php echo esc_attr( $prefs['target_set']['creds'] ); ?>" class="long" /></div>
				</li>
			</ol>
			<label class="subheader"><?php _e( 'Limit', WE_LS_SLUG ); ?></label>
			<ol>
				<li>
					<div class="h2"><?php echo $this->hook_limit_setting( $this->field_name( [ 'target_set' => 'limit' ] ), $this->field_id( [ 'target_set' => 'limit' ]  ), $prefs['target_set']['limit'] ); ?></div>
					<span class="description"><?php _e( 'Limit the number of times this award can be given within the specified time limit.', WE_LS_SLUG ); ?></span>
				</li>
			</ol>

			<?php
		}

		/**
		 * Sanitize Preferences
		 * If the hook has settings, this method must be used
		 * to sanitize / parsing of settings.
		 */
		public function sanitise_preferences( $data ) {

			if ( isset( $data['target_set']['limit'] ) && isset( $data['target_set']['limit_by'] ) ) {

				$limit = sanitize_text_field( $data['target_set']['limit'] );
				if ( $limit == '' ) {
					$limit = 0;
				}
				$data['target_set']['limit'] = $limit . '/' . $data['target_set']['limit_by'];
				unset( $data['target_set']['limit_by'] );
			}

			return $data;
		}
	}
}
add_action( 'mycred_load_hooks', 'ws_ls_mycred_load_hooks' );
