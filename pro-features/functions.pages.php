<?php

defined('ABSPATH') or die('Naw ya dinnie!');

// ------------------------------------------------------------------------------
// User search Search box
// ------------------------------------------------------------------------------

function ws_ls_box_user_search_form() {

	?>	<p><?php echo __('Enter a user\'s email address, display name or username and click Search.', WE_LS_SLUG); ?></p>
		<form method="get" action="<?php echo ws_ls_get_link_to_user_data(); ?>">
			<input type="text" name="search" placeholder=""  />
            <input type="hidden" name="page" value="ws-ls-wlt-data-home"  />
            <input type="hidden" name="mode" value="search-results"  />
			<input type="submit" value="Search" />
		</form>
	<?php

}
