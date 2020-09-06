<?php

defined('ABSPATH') or die('Jog on.');

/**
 * Link to export page
 *
 * @param string $mode
 *
 * @return string|void
 */
function ws_ls_export_link( $mode = 'view' ) {

	$url = sprintf( 'admin.php?page=ws-ls-export-data&mode=%1$s', $mode );

	$url = admin_url( $url );

	return $url;
}

/**
 * Export date range options
 * @return array
 */
function ws_ls_export_date_ranges() {

	return [
				'' 			=> '',
				'today' 	=> __( 'Today', WE_LS_SLUG ),
				'last-7' 	=> __( 'Last 7 Days', WE_LS_SLUG ),
				'last-31' 	=> __( 'Last 31 Days', WE_LS_SLUG ),
				'custom'	=> __( 'Custom date range', WE_LS_SLUG )
	];
}

/**
 * Insert new criteria for an export
 * @param $options
 * @return bool
 */
function ws_ls_export_insert( $options ) {

	if ( false === WS_LS_IS_PRO ) {
		return false;
	}

	return  ws_ls_db_export_insert( $options,
									ws_ls_export_file_generate_folder_name( $options ),
									ws_ls_export_file_generate_file_name( $options )
	);
}

/**
 * Generate a file for export
 * @param $options
 * @return string
 */
function ws_ls_export_file_generate_file_name( $options ) {

	$file_name = ( false === empty( $options[ 'title'] ) ) ? sanitize_title( $options[ 'title'] ) :  mt_rand();

	return sprintf( '%s.%s', $file_name, $options[ 'format' ] );
}

/**
 * Generate folder name
 * @param $options
 * @return string
 */
function ws_ls_export_file_generate_folder_name( $options ) {

	return sprintf( 'weight-tracker/%s/', mt_rand() );
}

/**
 * Fetch physical path for export files
 * @param $id
 * @return string|null
 */
function ws_ls_export_file_physical_path( $id ) {

	$export = ws_ls_db_export_criteria_get( $id );

	if( true === empty( $export[ 'file' ] ) ) {
		return NULL;
	}

	return sprintf( '%s/%s', ws_ls_export_file_physical_folder( $id ), $export[ 'file' ] );
}

/**
 * Fetch physical path for export folder
 * @param $id
 * @return string|null
 */
function ws_ls_export_file_physical_folder( $id ) {

	$export = ws_ls_db_export_criteria_get( $id );

	if( true === empty( $export[ 'folder' ] ) ) {
		return NULL;
	}

	$upload_dir = wp_upload_dir();

	return sprintf( '%s/%s', $upload_dir[ 'basedir' ], $export[ 'folder' ] );
}


//function ws_ls_export_file_generate( $options ) {
//
//	$file_name 	= ( false === empty( $options[ 'title'] ) ) ? sanitize_title( $options[ 'title'] ) :  mt_rand();
//
//	$upload_dir = wp_upload_dir();
//
//	$file_name .= '-' . md5( print_r( $options ) );
//
//	$folder 	= sprintf( '%s/weight-tracker/%s', $upload_dir[ 'basedir' ], mt_rand() );
//
//	return sprintf( '%s/%s', $folder, $file_name );
//
//}

function ws_ls_export_update_export_row( $export_id, $row ) {

	return false;
}

function ws_ls_export_output_create_file() {


//	$upload_dir = wp_upload_dir();
//	$user_dirname = $upload_dir['basedir'] . '/' . $current_user->user_login;
//	if(!file_exists($user_dirname)) wp_mkdir_p($user_dirname);

}
