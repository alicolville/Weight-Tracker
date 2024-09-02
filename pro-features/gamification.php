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
	$installed[ 'ws_ls_weight_entry' ] = [	'title'        => esc_html__( 'Weight Tracker: Weight Entry Added', WE_LS_SLUG ),
											'description'  => esc_html__( 'Reward a user when they have recorded a new weight entry.', WE_LS_SLUG ),
											'callback'     => [ 'ws_ls_mycred_weight_entry_class' ]
	];

	// Target added
	$installed[ 'ws_ls_target_set' ] = 	[	'title'        => esc_html__( 'Weight Tracker: Target set', WE_LS_SLUG ),
												'description'  => esc_html__( 'Reward a user when they have set their target.', WE_LS_SLUG ),
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

			parent::__construct( [
				'id'       => 'ws_ls_weight_entry',
				'defaults' => [ 'ws_ls_weight_entry'    => [	'creds'  => 10,
															'log'    => esc_html__( 'Weight entry added', WE_LS_SLUG ),
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
			if ( true === $this->over_hook_limit( 'ws_ls_weight_entry', 'ws_ls_weight_entry', $entry[ 'user-id' ] ) ) {
				return;
			}

			$this->core->add_creds(	'ws_ls_weight_entry',
									$entry[ 'user-id' ],
									$this->prefs[ 'ws_ls_weight_entry' ][ 'creds' ],
									$this->prefs[ 'ws_ls_weight_entry' ][ 'log' ],
									'ws_ls_weight_entry'
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
					<div class="h2"><input type="text" name="<?php echo $this->field_name( [ 'ws_ls_weight_entry' => 'log' ] ); ?>" id="<?php echo $this->field_id( [ 'ws_ls_weight_entry' => 'log' ] ); ?>" value="<?php echo esc_attr( $prefs[ 'ws_ls_weight_entry' ][ 'log' ] ); ?>" class="long" /></div>
					<span class="description"></span>
				</li>
			</ol>
			<label class="subheader"><?php _e( 'Points', WE_LS_SLUG ); ?></label>
			<ol>
				<li>
					<div class="h2"><input type="number" name="<?php echo $this->field_name( [ 'ws_ls_weight_entry' => 'creds' ] ); ?>" id="<?php echo $this->field_id( [ 'ws_ls_weight_entry' => 'creds' ] ); ?>" value="<?php echo esc_attr( $prefs['ws_ls_weight_entry']['creds'] ); ?>" class="long" /></div>
				</li>
			</ol>
			<label class="subheader"><?php _e( 'Limit', WE_LS_SLUG ); ?></label>
			<ol>
				<li>
					<div class="h2"><?php echo $this->hook_limit_setting( $this->field_name( [ 'ws_ls_weight_entry' => 'limit' ] ), $this->field_id( [ 'ws_ls_weight_entry' => 'limit' ]  ), $prefs['ws_ls_weight_entry']['limit'] ); ?></div>
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

			if ( isset( $data['ws_ls_weight_entry']['limit'] ) && isset( $data['ws_ls_weight_entry']['limit_by'] ) ) {

				$limit = sanitize_text_field( $data['ws_ls_weight_entry']['limit'] );
				if ( $limit == '' ) {
					$limit = 0;
				}
				$data['ws_ls_weight_entry']['limit'] = $limit . '/' . $data['ws_ls_weight_entry']['limit_by'];
				unset( $data['ws_ls_weight_entry']['limit_by'] );
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
									'id'       => 'ws_ls_target_set',
									'defaults' => [ 'ws_ls_target_set'    => [	'creds'  => 10,
																			'log'    => esc_html__( 'Weight target set', WE_LS_SLUG ),
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
			if ( true === $this->over_hook_limit( 'ws_ls_target_set', 'ws_ls_target_set', $entry[ 'user-id' ] ) ) {
				return;
			}

			$this->core->add_creds(	'ws_ls_target_set',
				$entry[ 'user-id' ],
				$this->prefs[ 'ws_ls_target_set' ][ 'creds' ],
				$this->prefs[ 'ws_ls_target_set' ][ 'log' ],
				'ws_ls_target_set'
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
					<div class="h2"><input type="text" name="<?php echo $this->field_name( [ 'ws_ls_target_set' => 'log' ] ); ?>" id="<?php echo $this->field_id( [ 'ws_ls_target_set' => 'log' ] ); ?>" value="<?php echo esc_attr( $prefs[ 'ws_ls_target_set' ][ 'log' ] ); ?>" class="long" /></div>
					<span class="description"></span>
				</li>
			</ol>
			<label class="subheader"><?php _e( 'Points', WE_LS_SLUG ); ?></label>
			<ol>
				<li>
					<div class="h2"><input type="number" name="<?php echo $this->field_name( [ 'ws_ls_target_set' => 'creds' ] ); ?>" id="<?php echo $this->field_id( [ 'ws_ls_target_set' => 'creds' ] ); ?>" value="<?php echo esc_attr( $prefs['ws_ls_target_set']['creds'] ); ?>" class="long" /></div>
				</li>
			</ol>
			<label class="subheader"><?php _e( 'Limit', WE_LS_SLUG ); ?></label>
			<ol>
				<li>
					<div class="h2"><?php echo $this->hook_limit_setting( $this->field_name( [ 'ws_ls_target_set' => 'limit' ] ), $this->field_id( [ 'ws_ls_target_set' => 'limit' ]  ), $prefs['ws_ls_target_set']['limit'] ); ?></div>
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

			if ( isset( $data['ws_ls_target_set']['limit'] ) && isset( $data['ws_ls_target_set']['limit_by'] ) ) {

				$limit = sanitize_text_field( $data['ws_ls_target_set']['limit'] );
				if ( $limit == '' ) {
					$limit = 0;
				}
				$data['ws_ls_target_set']['limit'] = $limit . '/' . $data['ws_ls_target_set']['limit_by'];
				unset( $data['ws_ls_target_set']['limit_by'] );
			}

			return $data;
		}
	}
}
add_action( 'mycred_load_hooks', 'ws_ls_mycred_load_hooks' );

/**
 * Expand custom references in log filters
 * @param $list
 *
 * @return mixed
 */
function ws_ls_mycred_custom_references( $list ) {

	$list[ 'ws_ls_target_set' ] 	= 'Weight Tracker: Target set';
	$list[ 'ws_ls_weight_entry' ] 	= 'Weight Tracker: Weight Entry Added';

	return $list;
}
add_filter( 'mycred_all_references', 'ws_ls_mycred_custom_references' );
