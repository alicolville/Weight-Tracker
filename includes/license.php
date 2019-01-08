<?php

defined('ABSPATH') or die('Jog on!');

/**
 * Determines whether the user has a valid license (Pro or Pro plus)
 *
 * @return bool|string
 */
function ws_ls_has_a_valid_license() {

	// Do we have an Pro Plus license?
	$license_type = ws_ls_has_a_valid_subscription_license();

	if(false !== $license_type) {
		return $license_type;
	}

	// Do we have a valid old Pro license?
	if(ws_ls_has_a_valid_old_pro_license()) {
		return 'pro-old';
	}

    return false;
}

/**
* Returns true if the user has a proper Pro plus license
**/
function ws_ls_has_a_valid_pro_plus_license() {
	return ('pro-plus' == ws_ls_has_a_valid_license());
}

/**
 * Return the number of days until the license expires
 *
 * @return int|null
 */
function ws_ls_how_many_days_until_license_expires() {

    $license_type = ws_ls_has_a_valid_license();

    // Only Pro or Pro plus licenses can expire
    if ( true === in_array( $license_type, ['pro', 'pro-plus'] ) ) {

        $license = ws_ls_license();

        $license_decoded = ws_ls_license_decode( $license );

        if ( false === empty( $license_decoded ) ) {

            $expiry_date = new DateTime( $license_decoded['expiry-date'] );
            $todays_date = new DateTime();

            $difference = $todays_date->diff( $expiry_date );

            return ( true === isset( $difference->days ) ) ? (int) $difference->days : NULL;

        }
    }
    return NULL;
}

/**
 * Display an admin notice if license is expiring within 14 days
 */
function ws_ls_display_license_expiry_warning() {

    $days_until_expiry = ws_ls_how_many_days_until_license_expires();

    if ( true === empty( $days_until_expiry ) ) {
        return;
    }

    if ( $days_until_expiry > 14 ) {
        return;
    }

    printf('<div class="notice notice-warning" id="ws-ls-admin-notice" data-wsmd5="">
                <p><strong>%s</strong>: %s. <a href="%s?hash=%s" rel="noopener noreferrer" target="_blank" >Renew your license now</a></p>
            </div>',
                __('Weight Tracker License', WE_LS_SLUG ),
                __('Your license expires in less than 14 days. Please renew your license as soon as possible', WE_LS_SLUG ),
                WE_LS_UPGRADE_TO_PRO_PLUS_URL,
                ws_ls_generate_site_hash()

    );

}
add_action('admin_notices', 'ws_ls_display_license_expiry_warning');

// ------------------------------------------------------------------------------------------------------------
// Current licensing
// ------------------------------------------------------------------------------------------------------------

define('WS_LS_LICENSE_2', 'ws-ls-license-2');
define('WS_LS_LICENSE_2_TYPE', 'ws-ls-license-2-type');
define('WS_LS_LICENSE_2_VALID', 'ws-ls-license-2-valid');

/**
* Do we have a valid subscription license?
**/
function ws_ls_has_a_valid_subscription_license() {

    $license_valid = get_option(WS_LS_LICENSE_2_VALID);

	if(false === empty($license_valid) && 'y' == $license_valid) {
		$license_type = get_option(WS_LS_LICENSE_2_TYPE);
		return (true === in_array($license_type, ['pro', 'pro-plus']) ? $license_type : false);
	}

    return false;
}

/**
*	Return stored license
**/
function ws_ls_license() {
	return get_option(WS_LS_LICENSE_2);
}

/**
 * Get the current (activate) license
 * @return string
 */
function ws_ls_license_get_old_or_new() {

    $license = ws_ls_license();

    if(false === empty($license)) {
        return $license;
    }

    $license = ws_ls_get_license();

    if(false === empty($license)) {
        return $license;
    }

    return '';

}

/**
*	Return stored license type
**/
function ws_ls_license_type() {
	return get_option(WS_LS_LICENSE_2_TYPE);
}

/**
* Validate and apply a license
**/
function ws_ls_license_apply( $license ) {

	// Validate license
	$license_result = ws_ls_license_validate($license);

	if(true === $license_result) {

		$license_decoded = ws_ls_license_decode( $license );

		ws_ls_log_add( 'license', sprintf( 'Valid License added: %s', $license ) );

		update_option(WS_LS_LICENSE_2, $license);
		update_option(WS_LS_LICENSE_2_TYPE, $license_decoded['type']);
		update_option(WS_LS_LICENSE_2_VALID, 'y');

		return true;
	} else {

		ws_ls_log_add( 'license', sprintf( 'Removed invalid / expired license: %s', $license ) );

		// Remove relevant options from WP
		delete_option(WS_LS_LICENSE_2);
		delete_option(WS_LS_LICENSE_2_TYPE);
		delete_option(WS_LS_LICENSE_2_VALID);
	}

	return $license_result;
}

/**
 * Remove new and old licenses
 */
function ws_ls_license_remove( $type = 'both' ) {

	ws_ls_log_add( 'license', sprintf( 'License removed: %s', $type ) );

    if(true === in_array($type, ['new', 'both'])) {
        delete_option(WS_LS_LICENSE_2);
        delete_option(WS_LS_LICENSE_2_TYPE);
        delete_option(WS_LS_LICENSE_2_VALID);

		// Fire comms to Yeken to record expire
		do_action(WE_LS_HOOK_LICENSE_EXPIRED);
	}

	if(true === in_array($type, ['old', 'both'])) {
        delete_option(WS_LS_LICENSE);
        delete_option(WS_LS_LICENSE_VALID);

		// Fire comms to Yeken to record expire
		do_action(WE_LS_HOOK_LICENSE_EXPIRED);
    }
}

/**
*	Check an existing license's hash is still valid
**/
function ws_ls_license_validate($license) {

	if(true === empty($license)) {
		return __('License missing', WE_LS_SLUG);
	}

	// Decode license
	$license = ws_ls_license_decode($license);

	if (true === empty($license)) {
		return __('Could not decode / verify license', WE_LS_SLUG);
	}

	// Does site hash in license meet this site's actual hash?
	if ( true === empty($license['site-hash'])) {
		return __('Invalid license hash', WE_LS_SLUG);
	}

	// Match this site hash?
	if ( ws_ls_generate_site_hash() !== $license['site-hash']) {
		return __('This license doesn\'t appear to be for this site (no match on site hash).', WE_LS_SLUG);
	}

	// Valid date?
	$today_time = strtotime(date("Y-m-d"));
	$expire_time = strtotime($license['expiry-date']);

	if ($expire_time < $today_time) {
		return __('This license has expired.', WE_LS_SLUG);
	}

	return true;
}

/**
* Validate and decode a license
**/
function ws_ls_license_decode($license) {

	if(true === empty($license)) {
		return NULL;
	}

	// Base64 and JSON decode
	$license = base64_decode($license);

	if(false === $license) {
		return NULL;
	}

	$license = json_decode($license, true);

	if(true === empty($license)) {
		return NULL;
	}

	// Validate hash!
	$verify_hash = md5('yeken.uk' . $license['type'] . $license['expiry-days'] . $license['site-hash'] . $license['expiry-date']);

	return ( $license['hash'] == $verify_hash && false === empty($license) ) ? $license : NULL;
}

/**
*	Generate a site hash to identify this site.
**/
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

/**
 * Display a name for license slug
 * @param bool $license
 * @return mixed
 */
function ws_ls_license_display_name($license = false) {

    $return_value = __('None', WE_LS_SLUG);

    if( true === empty($license) ) {
        $license = ws_ls_license();
    }

    if(false === empty($license)) {

        switch ($license) {
            case 'pro':
                $return_value = __('Yearly Pro', WE_LS_SLUG);
                break;
            case 'pro-old':
                $return_value = __('Legacy Pro', WE_LS_SLUG);
                break;
            case 'pro-plus':
                $return_value = __('Pro Plus', WE_LS_SLUG);
                break;
        }

    }

    return $return_value;
}

// ------------------------------------------------------------------------------------------------------------
// Old licensing
// ------------------------------------------------------------------------------------------------------------

define('WS_LS_LICENSE_SITE_HASH', 'ws-ls-license-site-hash');
define('WS_LS_LICENSE', 'ws-ls-license');
define('WS_LS_LICENSE_VALID', 'ws-ls-license-valid');

/**
*	Check for old valid pro license
**/
function ws_ls_has_a_valid_old_pro_license() {

	$valid_license = get_option(WS_LS_LICENSE_VALID);

    if(true == $valid_license) {
      return true;
    }

    return false;
}

/**
*	Generate an old Pro license so it can be compared against one entered.
**/
function ws_ls_generate_old_pro_license($site_hash) {
	return md5('yeken.co.uk' . $site_hash);
}

/**
*	Validate and store an old Pro license
**/
function ws_ls_is_validate_old_pro_license($license_key_from_yeken) {
    $site_hash = ws_ls_generate_site_hash();
    $comparison_license = ws_ls_generate_old_pro_license($site_hash);
    if ($comparison_license == $license_key_from_yeken){
        update_option(WS_LS_LICENSE, $license_key_from_yeken);
        update_option(WS_LS_LICENSE_VALID, true);
        return true;
    }
    return false;
}

/**
* 	Fetch old PRO license from WP Options
**/
function ws_ls_get_license() {
	return get_option(WS_LS_LICENSE);
}
