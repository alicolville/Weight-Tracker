<?php

defined('ABSPATH') or die("Jog on!");

/**
 * As of 9.3.7, macro percentages can be specified based upon aim (i.e. maintain, gain, lose). To ensure values are migrated properly, take the existing
 * macro percentages and port to the three types.
 */
function ws_ls_harris_benedict_migrate_old_macro_percentages() {

	if ( true === update_option( 'ws-ls-migrated-macro-percentages', 'y' ) ) {

		ws_ls_log_add('migration', 'Migrating macro percentages to each aim.' );

		foreach ( ['maintain', 'lose', 'gain' ] as $key ) {

			update_option( 'ws-ls-macro-proteins-' . $key, ws_ls_harris_benedict_setting( 'ws-ls-macro-proteins' ) );
			update_option( 'ws-ls-macro-carbs-' . $key, ws_ls_harris_benedict_setting( 'ws-ls-macro-carbs' ) );
			update_option( 'ws-ls-macro-fats-' . $key, ws_ls_harris_benedict_setting( 'ws-ls-macro-fats' ) );
		}
	}
}
add_action( 'admin_init', 'ws_ls_harris_benedict_migrate_old_macro_percentages' );
