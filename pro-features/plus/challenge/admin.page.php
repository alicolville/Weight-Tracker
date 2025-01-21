<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_challenges_admin_page() {

    ws_ls_permission_check_message();

    ws_ls_data_table_enqueue_scripts();

    $challenge_id   = ws_ls_querystring_value( 'challenge-id', true );
    $mode           = ws_ls_querystring_value( 'mode', false, 'list' );
    $error          = NULL;

    if ( 'close' === $mode && false !== $challenge_id ) {
        ws_ls_challenges_enabled( $challenge_id, false );
    }

    if ( 'delete' === $mode && false !== $challenge_id ) {
        ws_ls_challenges_delete( $challenge_id );
    }

    if ( 'true' === ws_ls_post_value( 'add-challenge', false ) ) {

        // Do we have a name? If so, insert Challenge otherwise show error
        $name = ws_ls_post_value( 'ws-ls-name' );

        if( true === empty( $name ) ) {
            $error = esc_html__( 'Please ensure you enter a name for the challenge.', WE_LS_SLUG );
        }

        if( true === empty( $error ) ) {

            $start_date = ws_ls_post_value( 'ws-ls-start-date' );
            $end_date   = ws_ls_post_value( 'ws-ls-end-date' );

            if ( true === ws_ls_challenges_add( $name, ws_ls_convert_date_to_iso( $start_date ), ws_ls_convert_date_to_iso( $end_date ) ) ) {

                ws_ls_challenges_process();

                $mode = 'processing';
            } else {
                $error = esc_html__( 'There was an error saving the challenge to the database.', WE_LS_SLUG );
            }
        }
    }
    
    ?>
    <div class="wrap ws-ls-challenges ws-ls-admin-page">
    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
	                <?php
		                if ( true !== WS_LS_IS_PREMIUM ) {
			                ws_ls_display_pro_upgrade_notice( 'pro-plus' );
		                }
	                ?>
                    <?php if ( true === in_array( $mode, [ 'delete', 'close', 'list' ] ) ): ?>
                        <div class="postbox">
                            <h2 class="hndle"><span><?php echo esc_html__( 'Current Challenges', WE_LS_SLUG ); ?></span></h2>
                            <div class="inside">
                                <p>
                                    <a href="<?php echo ws_ls_challenge_link( 1, 'add' ); ?>" class="btn btn-default button-primary">
                                        <i class="fa fa-plus"></i>
                                        <?php echo esc_html__( 'Add a challenge', WE_LS_SLUG ); ?>
                                    </a>
	                            <p><?php echo esc_html__( 'Why not set challenges for your user\'s within a given time period? Display Total Weight Lost, BMI Change, %Body Weight, Weight Tracker Streaks and Meal Tracker streaks achieved by each user in a league table.', WE_LS_SLUG ); ?>
		                            <?php echo esc_html__( 'Besides viewing all your challenges and their data, the shortcode will allow you to display the league table in the public facing website.', WE_LS_SLUG ); ?> <a href="https://docs.yeken.uk/challenges.html" target="_blank"><?php echo esc_html__( 'Read more about Challenges', WE_LS_SLUG ); ?></a>
								</p>
                                <?php ws_ls_challenges_table(); ?>
                            </div>
                        </div>
                        <div class="postbox">
                            <h2 class="hndle"><span><?php echo esc_html__( 'Notes', WE_LS_SLUG ); ?></span></h2>
                            <div class="inside">
	                            <h4><?php echo esc_html__( 'Overview', WE_LS_SLUG ); ?></h4>
	                            <p><?php echo esc_html__( 'Why not set challenges for your user\'s within a given time period? Display Total Weight Lost, BMI Change, %Body Weight, Weight Tracker Streaks and Meal Tracker streaks achieved by each user in a league table.', WE_LS_SLUG ); ?>
	                            <?php echo esc_html__( 'Besides viewing all your challenges and their data, the shortcode will allow you to display the league table in the public facing website.', WE_LS_SLUG ); ?></p>

                               <h4><?php echo esc_html__('User Opt-in', WE_LS_SLUG ); ?></h4>
                                <p>
                                    <?php echo esc_html__('By default, all of your users are opted out of challenges. This saves their name and data being displayed in public challenge tables.
                                                    The user will need to opt-in to participate. To do this, they can either update their preferences (a new option has been added)
                                                      or you can place this shortcode [wlt-challenges-optin] to provide simple links allowing them to opt in, or out.
                                                        ', WE_LS_SLUG ); ?>
                                </p>
                               <h4><?php echo esc_html__('Performance', WE_LS_SLUG ); ?></h4>
                               <p>
                                   <?php echo esc_html__('Performance: Please be aware, that every time a user updates their profile by adding or editing a weight, their statistics are
                                                        recalculated for every challenge that isn\'t closed. As the number of challenges grow and remain open, the greater the work load on your web server.
                                                        Please ensure you close (or delete) every challenge when expired.
                                                        ', WE_LS_SLUG ); ?>
									<strong> <?php echo esc_html__('Note: Challenge tables will only update every hour.
                                                        ', WE_LS_SLUG ); ?></strong>

                               </p>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ( 'add' === $mode ): ?>
                        <div class="postbox">
                            <h2 class="hndle"><span><?php echo esc_html__('Add a new challenge', WE_LS_SLUG ); ?></span></h2>
                            <div class="inside">
                                <p>
                                    <a href="<?php echo ws_ls_challenge_link( 1, 'list' ); ?>" class="btn btn-default button">
                                        <i class="fa fa-arrow-left"></i>
                                        <?php echo esc_html__( 'Cancel', WE_LS_SLUG ); ?>
                                    </a>
                                </p>
                                <?php
                                    if ( false === empty( $error ) ) {
                                        printf( '<p class="ws-ls-validation-error">%s</p>', $error );
                                    }
                                ?>
                                <form method="post" action="<?php echo ws_ls_challenge_link( 1, 'add' ); ?>" class="we-ls-weight-form ws_ls_display_form">
                                    <label for="ws-ls-name"><?php echo esc_html__( 'Name of challenge', WE_LS_SLUG ); ?></label>
                                    <input type="text" name="ws-ls-name" id="ws-ls-name" tabindex="1" value="<?php echo ws_ls_post_value( 'ws-ls-name', '' ); ?>" />
                                    <label for="ws-ls-start-date"><?php echo esc_html__( 'Start Date (only consider entries from this date)', WE_LS_SLUG ); ?></label>
                                    <input type="text" name="ws-ls-start-date" id="ws-ls-start-date" tabindex="2" value="<?php echo ws_ls_post_value( 'ws-ls-start-date', false,'' ); ?>" class="we-ls-datepicker we-ls-challenge-datepicker" />
                                    <label for="ws-ls-end-date"><?php echo esc_html__( 'End Date (only consider entries to this date)', WE_LS_SLUG ); ?></label>
                                    <input type="text" name="ws-ls-end-date" id="ws-ls-end-date" tabindex="3" value="<?php echo ws_ls_post_value( 'ws-ls-end-date', false,'' ); ?>" class="we-ls-datepicker we-ls-challenge-datepicker" />
                                    <br />
                                    <input type="hidden" name="add-challenge" value="true" />
                                    <input type="submit" class="button" value="Add" <?php if ( false === ws_ls_challenges_is_enabled() ) { echo ' disabled'; } ?>  />
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ( 'view' === $mode && false !== $challenge_id ): ?>
                        <div class="postbox">
                            <h2 class="hndle"><span><?php echo esc_html__('Entries for this challenge', WE_LS_SLUG ); ?></span></h2>
                            <div class="inside">
                                <p>
                                    <a href="<?php echo ws_ls_challenge_link( 1, 'list' ); ?>" class="btn btn-default button">
                                        <i class="fa fa-arrow-left"></i>
                                        <?php echo esc_html__( 'Back', WE_LS_SLUG ); ?>
                                    </a>
                                </p>
                                <?php
                                    echo ws_ls_challenges_view_entries( [ 'id'  => $challenge_id ] );

                                    ws_ls_challenges_view_entries_guide();
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ( 'processing' === $mode ): ?>
                        <div class="postbox">
                            <h2 class="hndle"><span><?php echo esc_html__('Processing...', WE_LS_SLUG ); ?></span></h2>
                            <div class="inside">
                                <p><?php echo esc_html__('Processing existing entries for this challenge. This page will keep refreshing until the initial challenge data has been processed.', WE_LS_SLUG ); ?>...</p>
                                <?php
                                    $entries_processed = ws_ls_challenges_process( NULL, true, 150 );

                                    if ( false !== $entries_processed ) {
                                        printf( '<p>- %d %s</p>', $entries_processed, esc_html__('entries processed', WE_LS_SLUG ) );

                                        $redirect_url = ws_ls_challenge_link( 1, 'processing' );
                                    } else {
                                        $redirect_url = ws_ls_challenge_link( 1, 'list' );
                                    }
                                    
                                    ws_ls_js_redirect( $redirect_url );

                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
    <?php
}
function ws_ls_challenges_admin_disabled() { ?>

<div class="wrap ws-ls-challenges ws-ls-admin-page">
    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <?php
                    if ( true !== WS_LS_IS_PREMIUM ) {
                        ws_ls_display_pro_upgrade_notice();
                    }
                    ?>
                    <div class="postbox">
                        <h2 class="hndle"><span><?php echo esc_html__( 'Challenges Disabled', WE_LS_SLUG ); ?></span></h2>
	                    <div class="inside">

		                    <p>
			                    <strong>
				                    <?php echo esc_html__( 'Challenges are currently disabled. Please enable via the', WE_LS_SLUG ); ?>
			                        <a href="<?php echo ws_ls_get_link_to_settings(); ?>"><?php echo esc_html__( 'settings', WE_LS_SLUG ); ?></a> <?php echo esc_html__( 'page', WE_LS_SLUG ); ?>.
			                    </strong>
		                    </p>
		                    <p><?php echo esc_html__( 'Why not set challenges for your user\'s within a given time period? Display Total Weight Lost, BMI Change, %Body Weight, Weight Tracker Streaks and Meal Tracker streaks achieved by each user in a league table.', WE_LS_SLUG ); ?></p>
		                    <p><?php echo esc_html__( 'Besides viewing all your challenges and their data, the shortcode will allow you to display the league table in the public facing website.', WE_LS_SLUG ); ?></p>
		                    <a href="https://docs.yeken.uk/challenges.html" class="button" target="_blank"><?php echo esc_html__( 'Read more about Challenges', WE_LS_SLUG ); ?></a>
		                    <p>


                            </p>
                        </div>
                    </div>
            </div>
        </div>
        <br class="clear">
    </div>
    <?php
}
