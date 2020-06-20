<?php
defined('ABSPATH') or die("Jog on!");

define( 'WE_LS_CACHE_ENABLED', 'yes' === get_option( 'ws-ls-caching', 'yes' ) );
define( 'WE_LS_CACHE_TIME', DAY_IN_SECONDS );

/**
 * User caching. From now on, store an array for each user in cache. Each caache key can then be stored in an array element.
 * To remove all use cache, just need to delete the cache key.
 *
 * @param $user_id
 * @param $key
 * @return null
 */
function ws_ls_cache_user_get( $user_id, $key ) {

	$user_lookup_table = ws_ls_get_cache( $user_id );

	if ( false === is_array( $user_lookup_table ) ) {
		return NULL;
	}

	// Do we have any data for this cache key?
	if ( true === empty( $user_lookup_table[ $key ] ) ) {
		return NULL;
	}

	// Take the cache key and dig further!
	$data_key   = $user_lookup_table[ $key ];
	$data_value = ws_ls_get_cache( $data_key );

	// If no data is found at this key, presume the cache entry has expired, so remove from lookup.
	if ( false === $data_value ) {
		unset( $user_lookup_table[ $key ] );
		ws_ls_set_cache( $user_id, $user_lookup_table, WE_LS_CACHE_TIME );
	}

	return $data_value;
}

/**
 * Return all cache for the given user
 * @param $user_id
 * @return array|bool|mixed|null
 */
function ws_ls_cache_user_get_all( $user_id ) {

	$user_cache = ws_ls_get_cache( $user_id) ;

	return ( true === is_array( $user_cache ) ) ? $user_cache : NULL;
}

/**
 * Cache for user
 * @param $user_id
 * @param $key
 * @param $value
 * @param float|int $time_to_expire
 */
function ws_ls_cache_user_set( $user_id, $key, $value, $time_to_expire = WE_LS_CACHE_TIME ) {

	$user_cache = ws_ls_get_cache( $user_id );

	// Empty cache? Create array
	if ( false === is_array( $user_cache ) ) {
		$user_cache = [];
	}

	/*
	 *  This Cache array will be a lookup. It will contain an array of keys to further cache entries. That way,
	 *  we don't have a monolithic cache object to load on every cache lookup. Just an array of keys. If the relevant key exists, then
	 *  once again, drill down.
	 */

	/*
	 * $key will be the clear text key passed in.
	 * $cache_key will be the subsequent cache key where the data is actually stored.
	 */

	$cache_key          = sprintf( 'wt-item-%s-%s', $user_id, $key );
	$user_cache[ $key ] = $cache_key;

	// Store data
	ws_ls_set_cache( $cache_key, $value, $time_to_expire );

	// Update lookup table
	ws_ls_set_cache( $user_id, $user_cache, $time_to_expire );
}

/**
 * Helper function for use in shortcodes etc. Cache value and return value.
 * @param $user_id
 * @param $key
 * @param $value
 *
 * @return mixed
 */
function ws_ls_cache_user_set_and_return( $user_id, $key, $value ) {

	ws_ls_cache_user_set( $user_id, $key, $value );

	return $value;
}

/**
 * Fetch all keys associated with the user and delete
 * @param $user_id
 */
function ws_ls_cache_user_delete( $user_id ) {

	$all_keys = ws_ls_cache_user_get_all( $user_id );

	if ( true === is_array( $all_keys ) ) {
		$all_keys = array_values( $all_keys );
		array_map( 'ws_ls_delete_cache', $all_keys );
	}

	// Delete cache lookup table
	ws_ls_delete_cache( $user_id );
}


// ----------------------------------------------------------------
// Generic caching
// ----------------------------------------------------------------

/**
 * Fetch Cache
 * @param $key
 * @return bool|mixed
 */
function ws_ls_get_cache( $key ) {

    if( true === WE_LS_CACHE_ENABLED ) {
        $key = ws_ls_cache_generate_key( $key );
        return get_transient( $key );
    }

    return false;
}

/**
 * Set Cache
 * @param $key
 * @param $data
 * @param float|int $time_to_expire
 * @return bool
 */
function ws_ls_set_cache( $key, $data, $time_to_expire = WE_LS_CACHE_TIME ) {

    if( true === WE_LS_CACHE_ENABLED ) {
      $key = ws_ls_cache_generate_key( $key );
      set_transient( $key, $data, $time_to_expire );
    }

    return false;
}

/**
 * Delete cache key
 * @param $key
 * @return bool
 */
function ws_ls_delete_cache( $key ){

	$key = ws_ls_cache_generate_key($key);
	return delete_transient($key);
}

/**
 * Delete the user cache for each user id within the array
 *
 * @param $user_ids
 */
function ws_ls_delete_cache_for_given_users( $user_ids ) {

    if ( true === is_array( $user_ids ) && false === empty( $user_ids ) ) {
        foreach ( $user_ids as $id ) {
            ws_ls_cache_user_delete( $id );
        }
    }

}

/**
 * Delete all weight tracker cache
 */
function ws_ls_delete_all_cache() {

	if ( true === WE_LS_CACHE_ENABLED ) {
		return;
	}

	global $wpdb;

    $sql = "Delete FROM  $wpdb->options WHERE option_name LIKE '%transient_" . WE_LS_SLUG ."%'";

    $wpdb->query($sql);

    $sql = "Delete FROM  $wpdb->options WHERE option_name LIKE '%transient_timeout_" . WE_LS_SLUG ."%'";

    $wpdb->query($sql);

}

/**
 * Generate key for cache
 * @param $key
 *
 * @return string
 */
function ws_ls_cache_generate_key( $key ){
    return sprintf( '%s-%s-%s', WE_LS_SLUG, WE_LS_CURRENT_VERSION, $key );
}

/**
 * Generate an array key based on an array
 * @param string $prefix
 * @param $array
 *
 * @return string
 */
function ws_ls_cache_generate_key_from_array( $prefix = 'wt', $array ) {

	if ( false === is_array( $array ) ) {
		return '';
	}

	return sprintf( '%s-%s', $prefix, md5( json_encode( $array ) ) );
}
