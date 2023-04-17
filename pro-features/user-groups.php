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

	return WS_LS_IS_PRO;

	// return 'no' === get_option('ws-ls-enable-groups', true ) ? false : true;
}

/**
 * Are Groups enabled?
 *
 * @return bool
 */
function ws_ls_groups_can_users_edit() {

	if ( false === WS_LS_IS_PRO ) {
		return false;
	}

	return 'yes' === get_option('ws-ls-enable-groups-user-edit', false ) ? true : false;
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

	if( false === update_option('ws-ls-group-version-number', WE_LS_CURRENT_VERSION ) ) {
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
add_filter( 'wlt-filter-admin-user-sidebar-middle',  'ws_ls_groups_hooks_user_side_bar', 10, 2 );

/**
* Add a <select> for Group to user preference form
*
* @param $html
*
* @param $user_id
* @return string
*/
function ws_ls_groups_hooks_user_preferences_form( $html, $user_id ) {

	global $kiosk_mode;

	if ( false === is_admin() && false === ws_ls_groups_can_users_edit() && true === empty( $kiosk_mode )) {
		return $html;
	}

	if ( true === ws_ls_groups_enabled() ) {

		$groups = ws_ls_groups( true );

		if ( false === empty( $groups ) ) {

			$current_selection = ws_ls_groups_user( $user_id );

			$current_selection = ( false === empty( $current_selection[0]['id'] ) ) ? (int) $current_selection[0]['id'] : 0;

			if ( true === is_admin() ) {
				$html .= sprintf( '<p><a href="%s">%s</a></p>', ws_ls_groups_link(), __('Add / remove Groups', WE_LS_SLUG) );
			}

			$groups = wp_list_pluck( $groups, 'name', 'id' );

			$html .= ws_ls_form_field_select( [ 'key'           => 'ws-ls-group',
			                                    'label'         => __( 'Group', WE_LS_SLUG ),
												'values'        => $groups,
												'selected'      => $current_selection,
												'uikit'         => true ] );

		} else {
			$html .= __('None', WE_LS_SLUG );
		}

	}

	return $html;
}
add_filter( 'wlt-filter-user-settings-below-dob',  'ws_ls_groups_hooks_user_preferences_form', 15, 2 );

/**
* Save user's group on preference page
*
* @param $user_id
* @param $is_admin
* @param $fields
* @return string
*/
function ws_ls_groups_hooks_user_preferences_save( $user_id, $is_admin, $fields ) {

	if ( false === ws_ls_groups_enabled() ) {
		return;
	}

	$group_id = ws_ls_post_value('ws-ls-group');

	ws_ls_groups_add_to_user( (int) $group_id, (int) $user_id );

	ws_ls_cache_user_delete( $user_id );
	ws_ls_cache_user_delete( 'groups-user-for-given' );
}
add_action( 'ws-ls-hook-user-preference-save',  'ws_ls_groups_hooks_user_preferences_save', 10, 3 );

/**
* @param $user_id
* @param $group_id
* @return bool|int
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
 * Remove group from a given user
 *
 * @param $user_id
 *
 * @return bool|int
 */
function ws_ls_groups_remove_all_from_user( $user_id ) {

	global $wpdb;

	$wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_GROUPS_USER, [ 'user_id' => $user_id ], [ '%d' ] );

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
	ws_ls_cache_user_delete( 'groups-user-for-given' );

	return $award;
}

/**
 * Fetch count of number of users in a group
 *
 * @param $id
 *
 * @return int
 */
function ws_ls_groups_count( $id ) {

	global $wpdb;

	if ( $cache = ws_ls_cache_user_get( 'groups', 'count-' . $id ) ) {
		return $cache;
	}

	$sql = $wpdb->prepare('Select count( user_id ) from ' . $wpdb->prefix . WE_LS_MYSQL_GROUPS_USER . ' where group_id = %d', $id );

	$count = $wpdb->get_var( $sql );

	$count = (int) $count;

	ws_ls_cache_user_set( 'groups', 'count-' .$id , $count );

	return $count;
}

/**
 * Add a group
 *
 * @param $name
 *
 * @return bool|void
 */
function ws_ls_groups_add( $name ) {

	if ( false === WS_LS_IS_PRO ) {
		return false;
	}

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
	ws_ls_cache_user_delete( 'groups-user-for-given' );

	return ( false === $result ) ? false : $wpdb->insert_id;
}

/**
* Update group name
*
* @param $id
* @param $name
*
* @return bool|void
*/
function ws_ls_groups_update_name( $id, $name ) {

if ( false === WS_LS_IS_PRO ) {
	return false;
}

if ( false === is_admin() ) {
	return false;
}

if ( true === empty( $name ) ) {
	return false;
}

global $wpdb;

$result = $wpdb->update( $wpdb->prefix . WE_LS_MYSQL_GROUPS , [ 'name' => $name ], [ 'id' => $id ], [ '%s' ], [ '%d' ] );

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
	ws_ls_cache_user_delete( 'groups-user-for-given' );

	return ( 1 === $result );
}

/**
 * Delete a user / group association
 *
 * @param $id       relationship ID to delete
 * @return bool     true if success
 */
function ws_ls_groups_users_delete( $id ) {

    if ( false === is_admin() ) {
        return;
    }

    global $wpdb;

    ws_ls_log_add( 'group-user', sprintf( 'Deleting: %d', $id ) );

    do_action( 'wlt-groupiuser-deleting', $id );

    $result = $wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_GROUPS_USER, [ 'id' => $id ], [ '%d' ] );

    ws_ls_cache_user_delete( 'groups' );
    ws_ls_cache_user_delete( 'groups-user-for-given' );

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

		$wpdb->delete( $wpdb->prefix . WE_LS_MYSQL_GROUPS_USER, [ 'group_id' => $group_id ], [ '%d' ] );

	}
}
add_action( 'wlt-group-deleting', 'ws_ls_groups_user_tidy_up' );

/**
 * Fetch all groups
 *
 * @param bool $include_none
 * @param bool $include_all_groups
 *
 * @return array
 */
function ws_ls_groups( $include_none = true, $include_all_groups = false ) {

	global $wpdb;

	$cache_key = sprintf( 'all-%d', $include_none );

	if ( false === is_admin() && $cache = ws_ls_cache_user_get( 'groups', $cache_key ) ) {
		return $cache;
	}

	$sql = 'Select * from ' . $wpdb->prefix . WE_LS_MYSQL_GROUPS . ' order by name asc';

	$data = $wpdb->get_results( $sql , ARRAY_A );

	if ( true === $include_none ) {
		$data = array_merge( [ [ 'id' => 0, 'name' => __('No Group', WE_LS_SLUG ) ] ], $data );
	}

	if ( true === $include_all_groups ) {
		$data = array_merge( [ [ 'id' => -1, 'name' => __('All Groups', WE_LS_SLUG ) ] ], $data );
	}

	ws_ls_cache_user_set( 'groups', $cache_key , $data );

	return $data;
}

/**
* Calculate total weight loss difference for given group
*
* @param $group_id
* @return float
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
* @param null $user_id
* @return array
*/
function ws_ls_groups_user( $user_id = NULL ) {

	$user_id = $user_id ?: get_current_user_id();

	global $wpdb;

	if ( $cache = ws_ls_cache_user_get( $user_id, 'groups' ) ) {
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
* Add group to CSV / JSON export
* @param $row
* @return mixed
*/
function ws_ls_groups_export_add( $row ) {

    if ( false === ws_ls_groups_enabled () ) {
        return $row;
    }

    $row[ 'group' ] = '';

    $group = ws_ls_groups_user( $row[ 'user_id' ] );

    if ( false === empty( $group[ 0 ][ 'name' ] ) ) {
        $row[ 'group' ] = $group[ 0 ][ 'name' ];
    } else {
	    $row[ 'group' ] = __( 'No Group', WE_LS_SLUG );
    }

    return $row;
}
add_filter( 'wlt-export-row', 'ws_ls_groups_export_add' );

/**
 * Add Group to export columns
 * @param $columns
 * @return mixed
 */
function ws_ls_groups_export_columns( $columns ) {

    if ( false === ws_ls_groups_enabled() ) {
        return $columns;
    }

    $columns[ 'group' ] = __( 'Group', WE_LS_SLUG );
    return $columns;
}
add_filter( 'wlt-export-columns', 'ws_ls_groups_export_columns' );

/**
 * Fetch all user ids for given group
 *
 * @param $group_id
 *
 * @param null $with_entry_date
 *
 * @return array|null|object
 */
function ws_ls_groups_users_for_given_group( $group_id, $with_entry_date = NULL ) {

	if ( false === is_numeric( $group_id ) ) {
		return NULL;
	}

	global $wpdb;

	$cache_key = sprintf( '%s-%s', $group_id, $with_entry_date );

	if ( false === is_admin() && $cache = ws_ls_cache_user_get( $cache_key, $group_id ) ) {
		return $cache;
	}

	$sql        = 'Select u.id, user_id, display_name from ' . $wpdb->prefix . WE_LS_MYSQL_GROUPS_USER . ' u
	                    inner join ' . $wpdb->prefix . 'users wpu on wpu.id = u.user_id';

	$where = [];

	if ( false === empty( $with_entry_date ) ) {
		$sql        .= ' inner join ' . $wpdb->prefix . WE_LS_TABLENAME . ' entries on entries.weight_user_id = u.user_id';
		$where[]    = $wpdb->prepare( 'entries.weight_date = %s', $with_entry_date );
	}

	if ( -1 !== (int) $group_id ) {
		$where[] = 'u.group_id = ' . (int) $group_id;
	}

	$sql    .= ' where ' . implode( ' and ', $where );

	$sql    .= ' order by wpu.display_name';

	$data   = $wpdb->get_results( $sql , ARRAY_A );

	$cache_time = ( false === empty( $with_entry_date ) ) ? MINUTE_IN_SECONDS : 5 * MINUTE_IN_SECONDS;

	ws_ls_cache_user_set( $cache_key, $group_id, $data, $cache_time );

	return $data;
}

/**
 * Fetch user IDs for all user's that don't have a group record
 *
 * Note:    This is a one off helper function, it's isn't aimed at identifying users that have 0 for their group. This is to detect
 *          all users that have no row wp_WS_LS_GROUPS_USER. This function was used in an upgrade script so we could add a row for all users (set to 0)
 *          for those that didn't currently have a group assigned. This was to make things easier when querying for users that don't have a group
 *          (to save checking for a 0 or a missing row)
 *
 * @return array
 */
function ws_ls_groups_get_users_with_no_group() {

	global $wpdb;

	return $wpdb->get_col( 'Select ID from ' . $wpdb->prefix . WE_LS_TABLENAME . ' where ID not in ( SELECT distinct `user_id` FROM ' . $wpdb->prefix . WE_LS_MYSQL_GROUPS_USER . ' )' );
}

/**
	Get Groups
 **/
function ws_ls_ajax_groups_get(){

	check_ajax_referer( 'ws-ls-user-tables', 'security' );

	$table_id = ws_ls_post_value('table_id');

	if ( $cache = ws_ls_cache_user_get( 'groups', $table_id ) ) {
		wp_send_json( $cache );
	}

	$columns = [
		[ 'name' => 'id', 'title' => __('Group ID', WE_LS_SLUG), 'breakpoints'=> '', 'type' => 'number', 'visible' => true ],
		[ 'name' => 'name', 'title' => __('Name', WE_LS_SLUG), 'breakpoints'=> '', 'type' => 'text' ],
		[ 'name' => 'count', 'title' => __('No. Users', WE_LS_SLUG), 'breakpoints'=> '', 'type' => 'number' ],
		[ 'name' => 'weight_difference', 'title' => '', 'breakpoints'=> '', 'type' => 'number', 'visible' => false ],
		[ 'name' => 'weight_display', 'title' => __('Total Weight Difference', WE_LS_SLUG), 'breakpoints'=> '', 'type' => 'text' ]
	];

	$rows = [];

	if ( true === WS_LS_IS_PRO ) {

		$rows = ws_ls_groups( false );

		foreach ( $rows as &$row ) {
			$row[ 'name' ]              = ws_ls_render_link( ws_ls_groups_link_to_page( $row[ 'id' ] ) , $row[ 'name' ] );
			$row[ 'weight_display' ]    = ws_ls_weight_display( $row[ 'weight_difference' ], NULL, 'display', false, true );
			$row[ 'count' ]             = ws_ls_groups_count( $row[ 'id' ] );
		}

	}

	$data = [
		'columns' => $columns,
		'rows' => $rows,
		'table_id' => $table_id
	];

	ws_ls_cache_user_set( 'groups', $table_id, $data );

	wp_send_json( $data );

}
add_action( 'wp_ajax_get_groups', 'ws_ls_ajax_groups_get' );

/**
	Get users for given group
 **/
function ws_ls_ajax_groups_users_get(){

	check_ajax_referer( 'ws-ls-user-tables', 'security' );

	$table_id               	= ws_ls_post_value( 'table_id' );
	$group_id               	= ws_ls_post_value( 'group_id' );
	$is_admin               	= ws_ls_post_value( 'is-admin' );
	$todays_entries_only   		= ws_ls_post_value( 'todays_entries_only' );
	$hide_col_diff_from_prev	= ws_ls_post_value_to_bool( 'hide_column_diff_from_prev' );
	$hide_column_gains			= ws_ls_post_value_to_bool( 'hide_column_gains' );
	$hide_column_losses			= ws_ls_post_value_to_bool( 'hide_column_losses' );

	$cache_key = sprintf( 'ajax-group-%d-%d', $group_id, $todays_entries_only );

	if ( $cache = ws_ls_cache_user_get( 'groups-user-for-given', $cache_key ) ) {
		wp_send_json( $cache );
	}

	$diff_label = ( true === ws_ls_to_bool( $todays_entries_only ) ) ? __('Diff from start', WE_LS_SLUG) : __('Diff from prev', WE_LS_SLUG);

	$columns = [
		[ 'name' => 'id', 'title' => __('ID', WE_LS_SLUG), 'breakpoints'=> '', 'type' => 'number', 'visible' => false ],
		[ 'name' => 'display_name', 'title' => __('User', WE_LS_SLUG), 'breakpoints'=> '', 'type' => 'text' ],
        [ 'name' => 'number-of-entries', 'title' => __('No. entries', WE_LS_SLUG), 'breakpoints'=> 'md', 'type' => 'number' ],
        [ 'name' => 'start-weight', 'title' => __('Start', WE_LS_SLUG), 'breakpoints'=> 'md', 'type' => 'text' ],
		[ 'name' => 'previous-weight', 'title' => __('Previous', WE_LS_SLUG), 'breakpoints'=> 'md', 'type' => 'text' ],
        [ 'name' => 'latest-weight', 'title' => __('Latest', WE_LS_SLUG), 'breakpoints'=> '', 'type' => 'text' ] ];
	
	if ( false === $hide_col_diff_from_prev ) {
		$columns[] = [ 'name' => 'diff-weight', 'title' => $diff_label, 'breakpoints'=> 'md', 'type' => 'text' ];
	}

	if ( false === $hide_column_losses ) {
		$columns[] = [ 'name' => 'losses', 'title' => __('Losses', WE_LS_SLUG), 'breakpoints'=> 'md', 'type' => 'text' ];
	}

	if ( false === $hide_column_gains ) {
		$columns[] = [ 'name' => 'gains', 'title' => __('Gains', WE_LS_SLUG), 'breakpoints'=> 'md', 'type' => 'text' ];
	}

    $columns[] = [ 'name' => 'target', 'title' => __('Target', WE_LS_SLUG), 'breakpoints'=> '', 'type' => 'text' ];
	$columns[] = [ 'name' => 'awards', 'title' => __('Awards', WE_LS_SLUG), 'breakpoints'=> 'md', 'type' => 'text' ];
	
	$rows               = [];
	$total_difference   = 0;

	if ( true === WS_LS_IS_PRO ) {

		$todays_entries_only    = ( true === ws_ls_to_bool( $todays_entries_only ) ) ? date('Y-m-d' ) : NULL;
		$rows                   = ws_ls_groups_users_for_given_group( $group_id, $todays_entries_only );
		$is_admin               = ws_ls_to_bool( $is_admin );

		$loss_total = 0;
		$gain_total = 0;

		foreach ( $rows as &$row ) {

			$row[ 'display_name' ] = ( $is_admin ) ? ws_ls_get_link_to_user_profile( $row[ 'user_id' ], $row[ 'display_name' ] ) : $row[ 'display_name' ];

			$stats = ws_ls_db_entries_count( $row[ 'user_id' ] );

			if ( false === empty( $stats ) ) {

				$awards = ws_ls_awards_db_given_get( $row[ 'user_id' ], 'value', $todays_entries_only );
				$awards = wp_list_pluck( $awards, 'title' );
				$awards = ( false === empty( $awards ) ) ? implode( ', ', $awards ) : '';

				$row[ 'number-of-entries' ] = $stats['number-of-entries'];
				$row[ 'latest-weight' ]     = ws_ls_entry_get_latest( [ 'user-id' => $row[ 'user_id' ], 'meta' => false ] );

				if ( false === empty( $row[ 'latest-weight' ] ) ) {
					$row[ 'start-weight' ]      = ws_ls_weight_display( $row[ 'latest-weight' ][ 'first_weight' ], NULL, 'display' );
					$row[ 'previous-weight' ]   = ws_ls_entry_get_previous( [ 'user-id' => $row[ 'user_id' ], 'meta' => false ] );
					
					if ( (int) $row[ 'number-of-entries' ] > 1 ) {
						
						if ( false === empty( $todays_entries_only ) ) {
							
							$difference             = $row[ 'latest-weight' ][ 'kg' ] - $row[ 'previous-weight' ][ 'kg' ];
							$row[ 'diff-weight' ]   = ws_ls_weight_display( $difference, NULL, 'display', false, true );

						} else {
							$difference             = $row[ 'latest-weight' ][ 'difference_from_start_kg' ];
							$row[ 'diff-weight' ]   = ws_ls_weight_display( $row[ 'latest-weight' ][ 'difference_from_start_kg' ], NULL, 'display' );
						}

						$total_difference += $difference;

						$row[ 'losses' ] 	= NULL;
						$row[ 'gains' ] 	= NULL;

						if ( $difference > 0 ) {

							$gain_total += $difference;

							$row[ 'gains' ] = ws_ls_weight_display( $difference, NULL, 'display', false, true );

						} else {
							$row[ 'losses' ] = ws_ls_weight_display( $difference, NULL, 'display', false, true );

							$loss_total += $difference;
					}
					}

					$row[ 'previous-weight' ]   = $row[ 'previous-weight' ][ 'display' ];
					$row[ 'latest-weight' ]     = $row[ 'latest-weight' ][ 'display' ];

				}

				$row[ 'target' ]            = ws_ls_target_get( $row[ 'user_id' ], 'display' );
				$row[ 'awards' ]            = $awards;
			}
		}

	}

	$data = [
		'columns'           => $columns,
		'rows'              => $rows,
		'table_id'          => $table_id,
		'total_difference'  => ws_ls_weight_display( $total_difference, NULL, 'display', false, true ),
		'total_gains'       => ws_ls_weight_display( $gain_total, NULL, 'display', false, true ),
		'total_losses'      => ws_ls_weight_display( $loss_total, NULL, 'display', false, true ),
	];

	ws_ls_cache_user_set( 'groups', $cache_key, $data, MINUTE_IN_SECONDS );

	wp_send_json( $data );

}
add_action( 'wp_ajax_get_groups_users', 'ws_ls_ajax_groups_users_get' );

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
 * AJAX: Delete given group
 */
function ws_ls_ajax_groups_users_delete() {

    if ( false === ws_ls_groups_enabled() ) {
        return;
    }

    check_ajax_referer( 'ws-ls-user-tables', 'security' );

    $id = ws_ls_get_numeric_post_value('id');

    if ( false === empty( $id ) ) {

        $result = ws_ls_groups_users_delete( $id );

        if ( true === $result ) {
            wp_send_json( 1 );
        }
    }

    wp_send_json( 0 );

}
add_action( 'wp_ajax_groups_users_delete', 'ws_ls_ajax_groups_users_delete' );

/**
* Shortcode to display total weight difference for group
* @param $user_defined_arguments
* @return string|void
*/
function ws_ls_groups_shortcode( $user_defined_arguments ) {

	$arguments = shortcode_atts( [  'id' => 0,
	                                'auto-detect' => false,
	                                'text-no-difference' => __( 'There is no weight difference for this group.', WE_LS_SLUG )
	], $user_defined_arguments );

	if ( true === ws_ls_to_bool( $arguments[ 'auto-detect' ] ) ) {

		$current_selection = ws_ls_groups_user();

		if ( false === empty( $current_selection[0]['id'] ) ) {

			$arguments['id'] = (int) $current_selection[0]['id'];
		}
	}

	if ( false === empty( $arguments['id'] ) ) {

		$difference = ws_ls_groups_get( $arguments['id'] );

		if ( false === empty( $difference[ 'weight_difference' ] ) ) {
			return ws_ls_weight_display( $difference[ 'weight_difference' ], NULL, 'display', false, true );
		}
	}

	return $arguments[ 'text-no-difference' ];
}
add_shortcode( 'wlt-group-weight-difference', 'ws_ls_groups_shortcode' );
add_shortcode( 'wt-group-weight-difference', 'ws_ls_groups_shortcode' );
add_shortcode( 'wt-group-total-weight-loss', 'ws_ls_groups_shortcode' );

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
add_shortcode( 'wt-group', 'ws_ls_groups_current' );

/**
 * @param $user_defined_arguments
 *
 * @return string
 */
function ws_ls_groups_view_as_table( $user_defined_arguments ) {

	if ( false === ws_ls_groups_enabled() ) {
		return ws_ls_display_pro_upgrade_notice_for_shortcode();
	}

	$arguments = shortcode_atts( [  'default-to-users-group'    	=> false,
									'disable-theme-css'         	=> false,
	                                'disable-main-font'         	=> false,
	                                'group-id'                  	=> NULL,
	                                'enable-group-select'       	=> true,
									'todays-entries-only'       	=> false,
									'hide-column-gains'       		=> false,
									'hide-column-losses'       		=> false,
									'hide-column-diff-from-prev'	=> false,
									'hide-summary-row'				=> false
	], $user_defined_arguments );

	return ws_ls_component_group_view_entries( $arguments );
}
add_shortcode( 'wt-group-view-as-table', 'ws_ls_groups_view_as_table' );

/**
 * Given a group ID, return a link to the group page
 * @param  int $group_id
 * @return string
 */
function ws_ls_groups_link_to_page( $group_id ) {

	$url = admin_url( 'admin.php?page=ws-ls-data-home&mode=groups&id=' . (int) $group_id );

	return esc_url( $url );
}

/**
 * Hooked on to admin_init, this will add a row for each user to the groups table.
 */
function ws_ls_groups_fix_set_no_group() {

	if( false === update_option( 'ws-ls-group-add-missing-group-row', WE_LS_CURRENT_VERSION ) ) {
		return;
	}

	$users_needing_group_row = ws_ls_groups_get_users_with_no_group();

	foreach( $users_needing_group_row as $user_id ) {
		ws_ls_groups_add_to_user( 0, $user_id );
	}
}
add_action( 'admin_init', 'ws_ls_groups_fix_set_no_group' );

/**
 * Hook on to an attempted save of entry, if we have no group for this user, then add.
 * @param $user_id
 */
function ws_ls_groups_add_missing_group( $user_id ) {

	$current_selection = ws_ls_groups_user( $user_id );

	// If we have a group, do nothing.
	if ( false === empty( $current_selection[0]['id'] ) ) {
		return;
	}

	ws_ls_groups_add_to_user( 0, $user_id );
}
add_action( 'wlt-hook-data-attempting-added-edited', 'ws_ls_groups_add_missing_group', 10, 1 );
