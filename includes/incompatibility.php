<?php

	defined('ABSPATH') or die("Jog on!");


	/*
		Wrapper around ws_ls_incompatibility_datatables_check() returns true if Safe to include dataTables JS
	*/
	function ws_ls_incompatibility_include_datatables() {

		return (false === ws_ls_incompatibility_datatables_check()) ? true : false;

	}
	/*
		Determine whether or not to include dataTables JS library

		Return array of issues otherwise false if none found
	*/
	function ws_ls_incompatibility_datatables_check() {

		$issues = array();

		// Is Ultimate Tables (https://en-gb.wordpress.org/plugins/ultimate-tables/developers/) installed?
		if (wp_script_is('ultimatetables')) {
			$issues[] = array(
								'title' => 'Ultimate Tables',
								'addressed' => true,
								'detail' => ''
							);
		}

		return (count($issues) > 0) ? $issues : false;
	}
