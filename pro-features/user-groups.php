<?php

	defined('ABSPATH') or die("Jog on!");

	define( 'WE_LS_MYSQL_GROUPS', 'WS_LS_GROUPS' );
	define( 'WE_LS_MYSQL_GROUPS_USER', 'WS_LS_GROUPS_USER' );

	/**
	 * Are Groups enabled?
	 *
	 * @return bool
	 */
	function ws_ls_groups_enabled() {

		if ( false === WS_LS_IS_PRO ) {
			return false;
		}

		return 'no' === get_option('ws-ls-enable-groups', true ) ? false : true;
	}

	/**
	 * Are groups enabled and do we have any setup?
	 */
	function ws_ls_groups_do_we_have_any() {

		if ( false === ws_ls_groups_enabled() ) {
			return false;
		}

		$groups = ws_ls_groups( false );

		if ( count( $groups ) <= 0 ) {
			return false;
		}

		return true;
	}

	/**
	 * Create the relevant database tables required to support groups
	 */
	function ws_ls_groups_create_mysql_tables() {

		if( false === update_option('ws-ls-grwdwjoup-version-number', WE_LS_DB_VERSION ) ) {
			return;
		}

		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$charset_collate = $wpdb->get_charset_collate();

		$table_name = $wpdb->prefix . WE_LS_MYSQL_GROUPS;

		$sql = "CREATE TABLE $table_name (
	                id mediumint(9) NOT NULL AUTO_INCREMENT,
	                name varchar(40) NOT NULL,
	                weight_difference float DEFAULT 0 NULL,
	                UNIQUE KEY id (id)
	            ) $charset_collate;";

		dbDelta( $sql );

		$table_name = $wpdb->prefix . WE_LS_MYSQL_GROUPS_USER;

		$sql = "CREATE TABLE $table_name (
	                id mediumint(9) NOT NULL AUTO_INCREMENT,
	                user_id mediumint(9) NOT NULL,
	                group_id mediumint(9) NOT NULL,
	                UNIQUE KEY id (id) 
	            ) $charset_collate;";

		dbDelta( $sql );
	}
	add_action('ws-ls-rebuild-database-tables', 'ws_ls_groups_create_mysql_tables');
	add_action( 'admin_init', 'ws_ls_groups_create_mysql_tables' );

	/**
	 * Extend user
	 *
	 * @param $rows
	 * @param $user_id
	 *
	 * @return array
	 */
	function ws_ls_groups_hooks_user_side_bar( $rows, $user_id ) {

		if ( true === ws_ls_groups_enabled() ) {

			$text = '';

			$settings_url = ws_ls_get_link_to_user_settings( $user_id );

			$groups = ws_ls_groups_user( $user_id );

			$text = ( false === empty( $groups ) ) ? $groups[ 0 ]['name'] :  __('None', WE_LS_SLUG );

			$rows[] = [
				'th' =>  __('Group', WE_LS_SLUG ),
				'td' => sprintf( '<a href="%s">%s</a>', esc_url( $settings_url ), esc_html( $text ) )
			];

		}

		return $rows;
	}
	add_filter( WE_LS_FILTER_ADMIN_USER_SIDEBAR_MIDDLE,  'ws_ls_groups_hooks_user_side_bar', 10, 2 );

	/**
	 * Add a <select> for Group to user preference form
	 *
	 * @param $html
	 *
	 * @return string
	 */
	function ws_ls_groups_hooks_user_preferences_form( $html, $user_id ) {

		if ( false === is_admin() ) {
			return;
		}

		if ( true === ws_ls_groups_enabled() ) {

			$groups = ws_ls_groups();

			if ( false === empty( $groups ) ) {

				$html .= ws_ls_title( __('Group', WE_LS_SLUG) );

				$current_selection = ws_ls_groups_user( $user_id );

				$current_selection = ( false === empty( $current_selection[0]['id'] ) ) ? (int) $current_selection[0]['id'] : 0;

				$html .= sprintf( '<p><a href="%s">%s</a></p>', ws_ls_groups_link(), __('Add / remove Groups', WE_LS_SLUG) );

				$html .= sprintf( '<select name="ws-ls-group" id="ws-ls-group" tabindex="%d">', ws_ls_get_next_tab_index() );

				foreach ( $groups as $group ) {
					$html .= sprintf( '<option value="%s" %s>%s</option>',
										(int) $group['id'],
										selected( $current_selection, $group['id'] ),
										esc_html( $group['name'] )
					);
				}

				$html .= '</select>';

			} else {
				$html .= __('None', WE_LS_SLUG );
			}

		}

		return $html;
	}
	add_filter( 'wlt-filter-user-settings-below-dob',  'ws_ls_groups_hooks_user_preferences_form', 10, 2 );

	/**
	 * Add a <select> for Group to user preference form
	 *
	 * @param $html
	 *
	 * @return string
	 */
	function ws_ls_groups_hooks_user_preferences_save( $user_id, $is_admin, $fields ) {

		if ( false === ws_ls_groups_enabled() ) {
			return;
		}

		// Update user group?
		if ( true === $is_admin ) {

			$group_id =  ws_ls_ajax_post_value('ws-ls-group');

			ws_ls_groups_add_to_user( (int) $group_id, (int) $user_id );
		}
	}
	add_action( 'ws-ls-hook-user-preference-save',  'ws_ls_groups_hooks_user_preferences_save', 10, 3 );

	/**
	 * @param $user_id
	 * @param $group_id
	 */
	function ws_ls_groups_add_to_user( $group_id, $user_id = NULL ) {

		if ( true === is_numeric( $group_id ) ) {

			$user_id = $user_id ?: get_current_user_id();

			global $wpdb;

			// Delete existing for this / user group

			// If supporting more than one group:
			// $wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_GROUPS_USER, [ 'user_id' => $user_id, 'group_id' => $group_id ], [ '%d', '%d' ] );

			// If supporting only one group:
			$wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_GROUPS_USER, [ 'user_id' => $user_id ], [ '%d' ] );

			// Add group to user
			$result = $wpdb->insert( $wpdb->prefix . WE_LS_MYSQL_GROUPS_USER,
				[ 'user_id' => $user_id, 'group_id' => $group_id ],
				[ '%d', '%d' ]
			);

			return ( false === $result ) ? false : $wpdb->insert_id;
		}

		return false;
	}


	/**
	 * Fetch a group
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	function ws_ls_groups_get( $id ) {

		global $wpdb;

		if ( $cache = ws_ls_cache_user_get( 'groups', $id ) ) {
			return $cache;
		}

		$sql = $wpdb->prepare('Select * from ' . $wpdb->prefix . WE_LS_MYSQL_GROUPS . ' where id = %s limit 0, 1', $id );

		$award = $wpdb->get_row( $sql, ARRAY_A );

		$award = ( false === empty( $award ) ) ? $award : false;

		ws_ls_cache_user_set( 'groups', $id , $award );

		return $award;
	}

	/**
	 * Add a group
	 *
	 * @param $name
	 *
	 * @return bool|void
	 */
	function ws_ls_groups_add( $name ) {

		if ( false === is_admin() ) {
			return false;
		}

		if ( true === empty( $name ) ) {
			return false;
		}

		global $wpdb;

		ws_ls_log_add( 'group', sprintf( 'Adding: %s', $name ) );

		$result = $wpdb->insert( $wpdb->prefix . WE_LS_MYSQL_GROUPS , [ 'name' => $name ], [ '%s' ] );

		ws_ls_cache_user_delete( 'groups' );

		return ( false === $result ) ? false : $wpdb->insert_id;
	}

	/**
	 * Delete a group
	 *
	 * @param $id       award ID to delete
	 * @return bool     true if success
	 */
	function ws_ls_groups_delete( $id ) {

		if ( false === is_admin() ) {
			return;
		}

		global $wpdb;

		ws_ls_log_add( 'group', sprintf( 'Deleting: %d', $id ) );

		do_action( 'wlt-group-deleting', $id );

		$result = $wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_GROUPS, [ 'id' => $id ], [ '%d' ] );

		ws_ls_cache_user_delete( 'groups' );

		return ( 1 === $result );
	}

	/**
	 * When a group is deleted, tidy up
	 *
	 * @param $group_id
	 */
	function ws_ls_groups_user_tidy_up( $group_id ) {

		if ( true === is_numeric( $group_id ) ) {

			ws_ls_log_add( 'group', sprintf( 'Tidying up for: %d', $group_id ) );

			global $wpdb;

			$result = $wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_GROUPS_USER, [ 'group_id' => $group_id ], [ '%d' ] );

		}
	}
	add_action( 'wlt-group-deleting', 'ws_ls_groups_user_tidy_up' );

	/**
	 * Fetch all groups
	 *
	 * @return array
	 */
	function ws_ls_groups( $include_none = true ) {

		global $wpdb;

		if ( false === is_admin() && $cache = ws_ls_cache_user_get( 'groups', 'all' ) ) {
			return $cache;
		}

		$sql = 'Select * from ' . $wpdb->prefix . WE_LS_MYSQL_GROUPS . ' order by name asc';

		$data = $wpdb->get_results( $sql , ARRAY_A );

		ws_ls_cache_user_set( 'groups', 'all' , $data );

		if ( true === $include_none ) {
			$data = array_merge( [ [ 'id' => 0, 'name' => __('None', WE_LS_SLUG ) ] ], $data );
		}

		return $data;
	}

	/**
	 * Calculate total weight loss difference for given group
	 *
	 * @param $group_id
	 */
	function ws_ls_groups_stats_get_weight_difference( $group_id ) {

		global $wpdb;

		$sql = $wpdb->prepare( 'Select sum( weight_difference ) from ' . $wpdb->prefix . WE_LS_MYSQL_GROUPS_USER . ' gu
								Inner Join ' . $wpdb->prefix . WE_LS_USER_STATS_TABLENAME . ' s on gu.user_id = s.user_id where gu.group_id = %d', $group_id );

		$var = $wpdb->get_var( $sql );

		return round( $var, 2 );
	}
	/**
	 * Update stats for each group
	 */
	function ws_ls_groups_stats_cron() {

		$groups = ws_ls_groups( false );

		if ( false === empty( $groups ) ) {

			global $wpdb;

			foreach ( $groups as $group  ) {

				$data = [
							'weight_difference' => ws_ls_groups_stats_get_weight_difference( $group[ 'id' ] )
				];

				$wpdb->update( $wpdb->prefix . WE_LS_MYSQL_GROUPS, $data, [ 'id' => $group[ 'id' ] ], [ '%f' ], [ '%d' ] );

			}
		}

		ws_ls_cache_user_delete( 'groups' );
	}
	add_action( 'wlt-hook-stats-running', 'ws_ls_groups_stats_cron' );

	/**
	 * Fetch all groups for user
	 *
	 * @return array
	 */
	function ws_ls_groups_user( $user_id = NULL ) {

		$user_id = $user_id ?: get_current_user_id();

		global $wpdb;

		if ( false === is_admin() && $cache = ws_ls_cache_user_get( $user_id, 'groups' ) ) {
			return $cache;
		}

		$sql = 'Select g.* from ' . $wpdb->prefix . WE_LS_MYSQL_GROUPS . ' g inner join
		        ' . $wpdb->prefix . WE_LS_MYSQL_GROUPS_USER . ' u on g.id = u.group_id where u.user_id = %d 
		        order by g.name asc';

		$sql = $wpdb->prepare( $sql, $user_id );

		$data = $wpdb->get_results( $sql , ARRAY_A );

		ws_ls_cache_user_set( $user_id, 'groups' , $data );

		return $data;
	}

	/**
		Get Groups
	 **/
	function ws_ls_ajax_groups_get(){

		if ( false === WS_LS_IS_PRO ) {
			return;
		}

		check_ajax_referer( 'ws-ls-user-tables', 'security' );

		$table_id = ws_ls_ajax_post_value('table_id');

		if ( $cache = ws_ls_cache_user_get( 'groups', $table_id ) ) {
			wp_send_json( $cache );
		}

		$columns = [
			[ 'name' => 'id', 'title' => __('ID', WE_LS_SLUG), 'breakpoints'=> '', 'type' => 'number', 'visible' => true ],
			[ 'name' => 'name', 'title' => __('Name', WE_LS_SLUG), 'breakpoints'=> '', 'type' => 'text' ],
		];

		$rows = ws_ls_groups( false );

		// Is this the group stats table? If so , add weight difference column
		if ( 'groups-list-stats' === $table_id ) {
			$columns[] = [ 'name' => 'weight_difference', 'title' => '', 'breakpoints'=> '', 'type' => 'number', 'visible' => false ];
			$columns[] = [ 'name' => 'weight_display', 'title' => __('Total Weight Difference', WE_LS_SLUG), 'breakpoints'=> '', 'type' => 'text' ];

			foreach ( $rows as &$row ) {
				$row[ 'weight_display' ] = ws_ls_convert_kg_into_relevant_weight_String( $row[ 'weight_difference' ], true );
			}
		}

		$data = [
			'columns' => $columns,
			'rows' => $rows,
			'table_id' => $table_id
		];

		ws_ls_cache_user_set( 'groups', $table_id, $data );

		wp_send_json($data);

	}
	add_action( 'wp_ajax_get_groups', 'ws_ls_ajax_groups_get' );


	/**
	 * AJAX: Delete given group
	 */
	function ws_ls_ajax_groups_delete() {

		if ( false === ws_ls_groups_enabled() ) {
			return;
		}

		check_ajax_referer( 'ws-ls-user-tables', 'security' );

		$id = ws_ls_get_numeric_post_value('id');

		if ( false === empty( $id ) ) {

			$result = ws_ls_groups_delete( $id );

			if ( true === $result ) {
				wp_send_json( 1 );
			}
		}

		wp_send_json( 0 );

	}
	add_action( 'wp_ajax_groups_delete', 'ws_ls_ajax_groups_delete' );

	/**
	 * Sortcode to display total weight difference for group
	 */
	function ws_ls_groups_shortcode( $user_defined_arguments ) {

		$arguments = shortcode_atts( [ 'id' => 0 ], $user_defined_arguments );

		if ( false === empty( $arguments['id'] ) ) {

			$difference = ws_ls_groups_get( $arguments['id'] );

			if ( false === empty( $difference[ 'weight_difference' ] ) ) {
				return ws_ls_convert_kg_into_relevant_weight_String( $difference[ 'weight_difference' ], true );
			}
		}

		return __('Group ID not found', WE_LS_SLUG);
	}
	add_shortcode( 'wlt-group-weight-difference', 'ws_ls_groups_shortcode' );

    /**
     * Shortcode to display the user's current group
     *
     * @param $user_defined_arguments
     * @return mixed
     */
	function ws_ls_groups_current( $user_defined_arguments ) {

        $arguments = shortcode_atts( [ 'user-id' => 0, 'no-group-text' => __('No Group', WE_LS_SLUG) ], $user_defined_arguments );

        $arguments[ 'user-id'] = ws_ls_force_numeric_argument( $arguments[ 'user-id'], NULL );

        $groups = ws_ls_groups_user( $arguments[ 'user-id'] );

        $group_text = ( false === empty( $groups ) ) ? $groups[ 0 ][ 'name' ] : $arguments[ 'no-group-text' ];

        return esc_html( $group_text );
    }
    add_shortcode( 'wlt-group', 'ws_ls_groups_current' );