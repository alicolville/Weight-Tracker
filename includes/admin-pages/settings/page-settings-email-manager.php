<?php

defined('ABSPATH') or die('Jog on!');

function ws_ls_settings_email_manager() {

	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' , WE_LS_SLUG) );
	}

	?>
	<div class="wrap ws-ls-admin-page">


	<div id="icon-options-general" class="icon32"></div>

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-3 ws-ls-settings">

			<!-- main content -->
			<div id="post-body-content">

				<div class="meta-box-sortables ui-sortable">

					<div class="postbox">
						<h3 class="hndle"><span><?php echo esc_html__( 'Email Manager', WE_LS_SLUG ); ?></span></h3>

						<div class="inside">
							<?php

								if ( 'y' === ws_ls_post_value( 'save' ) ){

									$slug 		= ws_ls_post_value( 'slug' );
									$subject 	= ws_ls_post_value( 'subject' );
									$email 		= ws_ls_post_value( 'email' );

									if ( false === ws_ls_emailer_update( $slug, $subject, $email ) ) {
										ws_ls_display_notice(esc_html__( 'There was an error saving your changes.', WE_LS_SLUG ), 'error' );
									} else {
										ws_ls_display_notice(esc_html__( 'Email template has been updated.', WE_LS_SLUG ) );
									}
								}

								$slug = ws_ls_querystring_value( 'slug' );

								if ( false === empty( $slug ) ) {
									ws_ls_settings_email_manager_edit_form( $slug );
								} else {
									ws_ls_settings_email_manager_all_list();
								}
							?>
						</div>
						<!-- .inside -->

					</div>
					<!-- .postbox -->

					<div class="postbox">
				</div>
				<!-- .meta-box-sortables .ui-sortable -->

			</div>
			<!-- post-body-content -->

		</div>
		<!-- #post-body .metabox-holder .columns-2 -->

		<br class="clear">
	</div>
	<!-- #poststuff -->

</div> <!-- .wrap -->
	<?php

}

/**
 * Render all email templates
 */
function ws_ls_settings_email_manager_all_list() {

	$email_templates = ws_ls_emailer_get_all();

	if ( true === empty( $email_templates ) ) {
		printf( '<p>%s</p>', esc_html__( 'No email templates were found in the database.', WE_LS_SLUG ) );
	}

	printf( '<p>%s:</p><ul>', esc_html__( 'Select an email to edit', WE_LS_SLUG ) );

	foreach ( $email_templates as $template ) {

		printf( '<li><a href="%s">%s</a></li>', ws_ls_emailer_edit_link( $template[ 'slug' ] ), esc_html( $template[ 'display_name' ] ) );

	}

	echo '</ul>';
}

/**
 * Render a form to edit email
 * @param $slug
 */
function ws_ls_settings_email_manager_edit_form( $slug ) {


	$template = ws_ls_emailer_get( $slug );

	if ( true === empty( $template ) ) {
		echo esc_html__( 'An error occurred loading the email template', WE_LS_SLUG );
	}

	printf( '<form action="%1$s" method="POST">
						<input type="hidden" name="save" value="y" />
						<input type="hidden" name="slug" value="%2$s" />',
						ws_ls_emailer_edit_link(),
						esc_attr( $slug )
	);

	if ( 'email-notify' !== $slug ) {
		echo ws_ls_form_field_text( [ 'name' => 'subject', 'value' => $template[ 'subject' ], 'title' => esc_html__( 'Subject', WE_LS_SLUG ), 'show-label' => true, 'css-class' => 'widefat', 'required' => true ] );
	} else {
		echo '<input type="hidden" name="subject" value="" />' ;
	}

	echo ws_ls_form_field_textarea( [ 'name' => 'email', 'value' => $template[ 'email' ], 'title' => esc_html__( 'Body', WE_LS_SLUG ), 'show-label' => true, 'css-class' => 'widefat', 'required' => true, 'rows' => 20  ] );

	printf( '<button name="submit_button" type="submit" tabindex="%1$d" class="button ws-ls-remove-on-submit" >%2$s</button>', ws_ls_form_tab_index_next(), esc_html__( 'Save', WE_LS_SLUG ) );

	echo '</form>';
}

