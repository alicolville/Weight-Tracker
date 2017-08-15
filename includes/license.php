<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Determines whether the user has a valid license (Pro or Pro plus)
 *
 * @return bool|string
 */
function ws_ls_has_a_valid_license() {

	// Do we have an Pro Plus license?
	if(ws_ls_has_a_valid_pro_plus_license()) {
		return 'pro-plus';
	}

	// Do we have an Pro  license?
	if(ws_ls_has_a_valid_pro_license()) {
		return 'pro';
	}

    return false;
}

// ------------------------------------------------------------------------------------------------------------
// New licensing
// ------------------------------------------------------------------------------------------------------------

function ws_ls_has_a_valid_pro_plus_license() {

	// TODO: Replace this in version 6.0
	if( defined('WS_LS_PRO_PLUS') && true === WS_LS_PRO_PLUS ) {
		return true;
	}

    return false;
}

// ------------------------------------------------------------------------------------------------------------
// Old licensing
// ------------------------------------------------------------------------------------------------------------

define('WS_LS_LICENSE_SITE_HASH', 'ws-ls-license-site-hash');
define('WS_LS_LICENSE', 'ws-ls-license');
define('WS_LS_LICENSE_VALID', 'ws-ls-license-valid');

function ws_ls_has_a_valid_pro_license() {

	$valid_license = get_option(WS_LS_LICENSE_VALID);

    if(true == $valid_license) {
      return true;
    }

    return false;
}

function ws_ls_generate_site_hash() {

    $site_hash = get_option(WS_LS_LICENSE_SITE_HASH);

    // Generate a basic site key from URL and plugin slug
    if(false == $site_hash) {
      $site_hash = md5(WE_LS_SLUG . '-' . site_url());
      $site_hash = substr($site_hash, 0, 6);
      update_option(WS_LS_LICENSE_SITE_HASH, $site_hash);
    }
    return $site_hash;
}


function ws_ls_generate_license($site_hash) {
	return md5('yeken.co.uk' . $site_hash);
}

function ws_ls_is_validate_license($license_key_from_yeken) {

	$site_hash = ws_ls_generate_site_hash();
    $comparison_license = ws_ls_generate_license($site_hash);

    if ($comparison_license == $license_key_from_yeken){
      update_option(WS_LS_LICENSE, $license_key_from_yeken);
      update_option(WS_LS_LICENSE_VALID, true);
      return true;
    }

    return false;
}

function ws_ls_get_license() {
	return get_option(WS_LS_LICENSE);
}
