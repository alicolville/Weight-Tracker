<?php

defined('ABSPATH') or die("Jog on!");

/**
 * Return a default email template
 *
 * @return string
 */
function ws_ls_emailer_default_template() {

	$email = '<!doctype html>
				<html>
				  <head>
					<meta name="viewport" content="width=device-width" />
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
					<title>{site-title}</title>
					<style>
					  /* -------------------------------------
						  GLOBAL RESETS
					  ------------------------------------- */

					  /*All the styling goes here*/

					  img {
						border: none;
						-ms-interpolation-mode: bicubic;
						max-width: 100%;
					  }

					  body {
						background-color: #f6f6f6;
						font-family: sans-serif;
						-webkit-font-smoothing: antialiased;
						font-size: 14px;
						line-height: 1.4;
						margin: 0;
						padding: 0;
						-ms-text-size-adjust: 100%;
						-webkit-text-size-adjust: 100%;
					  }

					  table {
						border-collapse: separate;
						mso-table-lspace: 0pt;
						mso-table-rspace: 0pt;
						width: 100%; }
						table td {
						  font-family: sans-serif;
						  font-size: 14px;
						  vertical-align: top;
					  }

					  /* -------------------------------------
						  BODY & CONTAINER
					  ------------------------------------- */

					  .body {
						background-color: #f6f6f6;
						width: 100%;
					  }

					  /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
					  .container {
						display: block;
						Margin: 0 auto !important;
						/* makes it centered */
						max-width: 580px;
						padding: 10px;
						width: 580px;
					  }

					  /* This should also be a block element, so that it will fill 100% of the .container */
					  .content {
						box-sizing: border-box;
						display: block;
						Margin: 0 auto;
						max-width: 580px;
						padding: 10px;
					  }

					  /* -------------------------------------
						  HEADER, FOOTER, MAIN
					  ------------------------------------- */
					  .main {
						background: #ffffff;
						border-radius: 3px;
						width: 100%;
					  }

					  .wrapper {
						box-sizing: border-box;
						padding: 20px;
					  }

					  .content-block {
						padding-bottom: 10px;
						padding-top: 10px;
					  }

					  .footer {
						clear: both;
						Margin-top: 10px;
						text-align: center;
						width: 100%;
					  }
						.footer td,
						.footer p,
						.footer span,
						.footer a {
						  color: #999999;
						  font-size: 12px;
						  text-align: center;
					  }

					  /* -------------------------------------
						  TYPOGRAPHY
					  ------------------------------------- */
					  h1,
					  h2,
					  h3,
					  h4 {
						color: #000000;
						font-family: sans-serif;
						font-weight: 400;
						line-height: 1.4;
						margin: 0;
						margin-bottom: 30px;
					  }

					  h1 {
						font-size: 35px;
						font-weight: 300;
						text-align: center;
						text-transform: capitalize;
					  }
					   h3 {
						font-size: 20px;
						font-weight: 300;
						text-align: center;
						text-transform: capitalize;
					}
					  h4 {
						font-size: 17px;
						font-weight: 300;
						text-align: center;
						text-transform: capitalize;
						border-bottom: 1px dotted #000000;
					  }

					   h5 {
						font-size: 14px;
						font-weight: bold;
						text-align: center;
						text-transform: capitalize;
					  }

					  p,
					  ul,
					  ol {
						font-family: sans-serif;
						font-size: 14px;
						font-weight: normal;
						margin: 0;
						margin-bottom: 15px;
					  }
						p li,
						ul li,
						ol li {
						  list-style-position: inside;
						  margin-left: 5px;
					  }

					  a {
						color: #3498db;
						text-decoration: underline;
					  }

					  /* -------------------------------------
						  BUTTONS
					  ------------------------------------- */
					  .btn {
						box-sizing: border-box;
						width: 100%; }
						.btn > tbody > tr > td {
						  padding-bottom: 15px; }
						.btn table {
						  width: auto;
					  }
						.btn table td {
						  background-color: #ffffff;
						  border-radius: 5px;
						  text-align: center;
					  }
						.btn a {
						  background-color: #ffffff;
						  border: solid 1px #3498db;
						  border-radius: 5px;
						  box-sizing: border-box;
						  color: #3498db;
						  cursor: pointer;
						  display: inline-block;
						  font-size: 14px;
						  font-weight: bold;
						  margin: 0;
						  padding: 12px 25px;
						  text-decoration: none;
						  text-transform: capitalize;
					  }

					  .btn-primary table td {
						background-color: #3498db;
					  }

					  .btn-primary a {
						background-color: #3498db;
						border-color: #3498db;
						color: #ffffff;
					  }

					  /* -------------------------------------
						  OTHER STYLES THAT MIGHT BE USEFUL
					  ------------------------------------- */
					  .last {
						margin-bottom: 0;
					  }

					  .first {
						margin-top: 0;
					  }

					  .align-center {
						text-align: center;
					  }

					  .align-right {
						text-align: right;
					  }

					  .align-left {
						text-align: left;
					  }

					  .clear {
						clear: both;
					  }

					  .mt0 {
						margin-top: 0;
					  }

					  .mb0 {
						margin-bottom: 0;
					  }

					  .preheader {
						color: transparent;
						display: none;
						height: 0;
						max-height: 0;
						max-width: 0;
						opacity: 0;
						overflow: hidden;
						mso-hide: all;
						visibility: hidden;
						width: 0;
					  }

					  .powered-by a {
						text-decoration: none;
					  }

					  hr {
						border: 0;
						border-bottom: 1px solid #f6f6f6;
						Margin: 20px 0;
					  }

					  /* -------------------------------------
						  RESPONSIVE AND MOBILE FRIENDLY STYLES
					  ------------------------------------- */
					  @media only screen and (max-width: 620px) {
						table[class=body] h1 {
						  font-size: 28px !important;
						  margin-bottom: 10px !important;
						}
						table[class=body] p,
						table[class=body] ul,
						table[class=body] ol,
						table[class=body] td,
						table[class=body] span,
						table[class=body] a {
						  font-size: 16px !important;
						}
						table[class=body] .wrapper,
						table[class=body] .article {
						  padding: 10px !important;
						}
						table[class=body] .content {
						  padding: 0 !important;
						}
						table[class=body] .container {
						  padding: 0 !important;
						  width: 100% !important;
						}
						table[class=body] .main {
						  border-left-width: 0 !important;
						  border-radius: 0 !important;
						  border-right-width: 0 !important;
						}
						table[class=body] .btn table {
						  width: 100% !important;
						}
						table[class=body] .btn a {
						  width: 100% !important;
						}
						table[class=body] .img-responsive {
						  height: auto !important;
						  max-width: 100% !important;
						  width: auto !important;
						}
					  }

					  /* -------------------------------------
						  PRESERVE THESE STYLES IN THE HEAD
					  ------------------------------------- */
					  @media all {
						.ExternalClass {
						  width: 100%;
						}
						.ExternalClass,
						.ExternalClass p,
						.ExternalClass span,
						.ExternalClass font,
						.ExternalClass td,
						.ExternalClass div {
						  line-height: 100%;
						}
						.apple-link a {
						  color: inherit !important;
						  font-family: inherit !important;
						  font-size: inherit !important;
						  font-weight: inherit !important;
						  line-height: inherit !important;
						  text-decoration: none !important;
						}
						.btn-primary table td:hover {
						  background-color: #34495e !important;
						}
						.btn-primary a:hover {
						  background-color: #34495e !important;
						  border-color: #34495e !important;
						}
					  }

					</style>
				  </head>
				  <body class="">
					<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
					  <tr>
						<td>&nbsp;</td>
						<td class="container">
						  <div class="content">

							<!-- START CENTERED WHITE CONTAINER -->

							<table role="presentation" class="main">

							  <!-- START MAIN CONTENT AREA -->
							  <tr>
								<td class="wrapper">
								  <table role="presentation" border="0" cellpadding="0" cellspacing="0">
									<tr>
									  <td style="text-align: center">
									   {message}
									  </td>
									</tr>
								  </table>
								</td>
							  </tr>

							<!-- END MAIN CONTENT AREA -->
							</table>

							<!-- START FOOTER -->
							<div class="footer">
							  <table role="presentation" border="0" cellpadding="0" cellspacing="0">
								<tr>
								  <td class="content-block">
									   Contact us: <a href="mailto:{admin_email}">{admin_email}</a>
								  </td>
								</tr>
								<tr>
								  <td class="content-block powered-by">
								   sent by <a href="{url}">{name}</a>
								  </td>
								</tr>
							  </table>
							</div>
							<!-- END FOOTER -->

						  <!-- END CENTERED WHITE CONTAINER -->
						  </div>
						</td>
						<td>&nbsp;</td>
					  </tr>
					</table>
				  </body>
				</html>';

	return apply_filters( 'wlt-emailer-default-template', $email );

}

/**
 * Replace placeholders in email
 *
 * @param $email
 * @param $placeholders
 *
 * @return mixed
 */
function ws_ls_emailer_replace_placeholders( $email, $placeholders ) {

	if( false === empty( $email ) && false === empty( $placeholders ) ) {

		foreach ( $placeholders as $key => $value ) {
	
			if ( NULL === $value ) {
				$value = '';
			}	

			$email = str_replace('{' . $key . '}', $value, $email );
		}

	}

	return $email;
}

/**
 * Build a list of place holders
 *
 * @param $placeholders
 *
 * @return array
 */
function ws_ls_emailer_placeholders( $placeholders = [] ) {

	$default_placeholders = [
								'name'          => get_bloginfo('name'),
								'url'           => get_bloginfo('url'),
								'admin_email'   => get_bloginfo('admin_email')
	];

	return array_merge( $placeholders, $default_placeholders );
}

/**
 * Fetch an email template
 *
 * @param $slug
 *
 * @return bool
 */
function ws_ls_emailer_get( $slug ) {

	global $wpdb;

	$sql = $wpdb->prepare('Select * from ' . $wpdb->prefix . WE_LS_EMAIL_TABLENAME . ' where slug = %s limit 0, 1', $slug );

	$email = $wpdb->get_row( $sql, ARRAY_A );

	if ( false === empty( $email ) ) {
		$email = array_map( 'stripslashes', $email );
	}

	return ( false === empty( $email ) ) ? $email : false;
}

/**
 * Get all email templates
 * @param bool $only_editable
 * @return array|bool|object|null
 */
function ws_ls_emailer_get_all( $only_editable = true ) {

	global $wpdb;

	$sql = 'Select * from ' . $wpdb->prefix . WE_LS_EMAIL_TABLENAME;

	if ( $only_editable ) {
		$sql .= ' where editable = 1';
	}

	return $wpdb->get_results( $sql . ' order by slug asc', ARRAY_A );
}

/**
 * Return the link for managing Groups page
 * @param $slug
 * @return string
 */
function ws_ls_emailer_edit_link( $slug = '' ) {
	$url = admin_url( 'admin.php?page=ws-ls-settings&mode=email-manager&slug=' . $slug );

	return esc_url( $url );
}

/**
 * Insert an email template
 *
 * @param $slug
 * @param $subject
 * @param $email
 *
 * @param $display_name
 *
 * @return bool|int
 */
function ws_ls_emailer_add( $slug, $subject, $email, $display_name = '' ) {

	if ( false === is_admin() ) {
		return false;
	}

	global $wpdb;

	$email_template     = ws_ls_emailer_default_template();
	$email     			= ws_ls_emailer_replace_placeholders( $email_template, [ 'message' => $email ] );

	$data = [
				'slug' 		    => $slug,
				'subject' 	    => $subject,
				'email' 	    => $email,
				'display_name'  => ( false === empty( $display_name ) ) ? $display_name : $subject,
				'editable'	    => 1
	];

	$result = $wpdb->insert( $wpdb->prefix . WE_LS_EMAIL_TABLENAME , $data, [ '%s', '%s', '%s', '%s', '%d' ] );

	return ( false === $result ) ? false : $wpdb->insert_id;
}

/**
 * Update an email template
 * @param $slug
 * @param $subject
 * @param $email
 *
 * @return bool|false|int
 */
function ws_ls_emailer_update( $slug, $subject, $email ) {

	if ( false === is_admin() ) {
		return false;
	}

	global $wpdb;

	$data = [ 'subject' 	=> $subject, 'email' 	=> $email ];

	return $wpdb->update( $wpdb->prefix . WE_LS_EMAIL_TABLENAME , $data, ['slug' => $slug ], [ '%s', '%s', '%s', '%d' ], [ '%s' ] );
}

/**
 * Send email
 *
 * @param $to
 * @param $subject
 * @param $message
 * @param array $placeholders
 * @return bool
 */
function ws_ls_emailer_send( $to, $subject, $message, $placeholders = [] ) {

	if ( false === empty( $message ) ) {

		$placeholders       = ws_ls_emailer_placeholders( $placeholders );

		$message            = ws_ls_emailer_replace_placeholders( $message, $placeholders );

		$result             = wp_mail( $to,  $subject,  $message, [ 'Content-Type: text/html; charset=UTF-8' ] );

		ws_ls_log_add('email-sent', sprintf('To: %s / Subject: %s', print_r( $to, true ), $subject ) );

		return $result;
	}

	return false;
}

/**
 * Has the user opted in for the given list?
 *
 * @param $list_name
 * @param $user_id
 * @return array
 */
function ws_ls_emailer_user_has_optedin( $list_name, $user_id = NULL ) {

	if ( true === empty( $list_name ) ) {
		return false;
	}

	$user_id = ( NULL === $user_id ) ? get_current_user_id() : $user_id;

	// Ensure emails are enabled globally
	if ( false === ws_ls_email_enabled() ) {
		return false;
	}

	$lists = ws_ls_emailer_user_lists( $user_id );

	// If we don't have a value in the user settings, we can assume we haven't saved their preferences 
	// yet, or, we potentially have a new mailing list (so look up default)
	if ( false === array_key_exists( $list_name, $lists ) ) {

		$defaults = ws_ls_emailer_lists_default_setting();

		// Invalid email list?
		if ( ! array_key_exists( $list_name, $defaults ) ) {
			return false;
		}

		$lists[ $list_name ] = $defaults[ $list_name ];

	}

	return ws_ls_to_bool( $lists[ $list_name ] );
}

/**
 * Fetch user's email list preferences from settings
 *
 * @param $user_id
 * @return array
 */
function ws_ls_emailer_user_lists( $user_id = NULL ) {

	$user_id 	= ( NULL === $user_id ) ? get_current_user_id() : $user_id;

	$lists 		= ws_ls_user_preferences_get( 'email_lists', $user_id );

	return ( false === empty( $lists ) ) ? json_decode( $lists, true ) : ws_ls_emailer_lists_default_setting();
}

/**
 * Fetch default email list preferences
 *
 * Note: All new mailing lists must be added here
 * 
 * @return array
 */
function ws_ls_emailer_lists_default_setting() {
	$lists = [
				'awards' 	=> true,
				'notes'		=> true,
				'birthdays' => true
	];

	return apply_filters( 'wlt-filter-email-lists-default-settings', $lists );
}

/**
 * Fetch labels for email lists
 *
 * Note: All new mailing lists must be added here
 *
 * @return array
 */
function ws_ls_emailer_lists_default_labels() {
	$labels = [
				'awards' 	=> esc_html__( 'Notifications about new awards', WE_LS_SLUG ),
				'birthdays' => esc_html__( 'Birthday emails', WE_LS_SLUG ),
				'notes' 	=> esc_html__( 'Notifications about new notes', WE_LS_SLUG )
	];

	return apply_filters( 'wlt-filter-email-lists-default-labels', $labels );
}

/**
 * Return form HTML for email opt in lists
 *
 * @return string
 */
function ws_ls_emailer_optout_form( $user_id = NULL, $uikit = true ) {

	$user_id 	= ( NULL === $user_id ) ? get_current_user_id() : $user_id;	
	
	$lists 	= ws_ls_emailer_lists_default_setting();
	$labels = ws_ls_emailer_lists_default_labels();

	$html_output = '';

	foreach( $lists as $key => $value ) {

		$html_output .= ws_ls_form_field_select( [  'key'       => sprintf( 'email-optin-%s', $key ),
													'label'     => sprintf( '%s:', $labels[ $key ] ),
													'uikit'     => $uikit,
													'values'    => [ 'true' => esc_html__( 'Yes', WE_LS_SLUG ), 'false' => esc_html__( 'No', WE_LS_SLUG ) ],
													'selected'  => ( true === ws_ls_emailer_user_has_optedin( $key, $user_id ) ) ? 'true' : 'false' ] );
												
	}

	return $html_output;
}

/**
 * Filter user preferences form and check for user optin fields.
 * 
 */
add_filter( 'wlt-filter-user-settings-save-fields', function ( $fields ) {

	if ( true === empty( $fields[ 'user_id' ] ) ) {
		return $fields;
	}

	$lists 	= ws_ls_emailer_lists_default_setting();
	$values = [];

	foreach ( $lists as $list => $default_value ) {

		$form_key 			= sprintf( 'email-optin-%s', $list );
		$values[ $list ] 	= ws_ls_post_value( $form_key, 'missing' ); 

		// If any optin field is missing then let's be safe and assume there is an issue with the form
		if ( 'missing' === $values[ $list ] ) {
			return $fields;
		}
	}
	
	$fields[ 'email_lists'] = $values;

	return $fields;
});