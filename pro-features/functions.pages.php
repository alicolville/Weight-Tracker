<?php

defined('ABSPATH') or die('Naw ya dinnie!');

// ------------------------------------------------------------------------------
// User search Search box
// ------------------------------------------------------------------------------

function ws_ls_box_user_search_form() {

	?>	<p><?php echo __('Enter a user\'s email address, display name or username and click Search.', WE_LS_SLUG); ?></p>
		<form>
			<input type="hidden" name="nonce" />
			<input type="text" name="username" placeholder=""  />
			<input type="submit" value="Search" />
		</form>
	<?php

}
