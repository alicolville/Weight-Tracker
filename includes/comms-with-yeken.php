<?php

defined('ABSPATH') or die('Jog on!');

// If cache key not found time to send communication to YeKen
if (WE_LS_ALLOW_STATS && !ws_ls_get_cache(WE_LS_CACHE_COMMS_KEY)) {

 //var_dump(WE_LS_ALLOW_STATS, WE_LS_CACHE_COMMS_KEY);
	ws_ls_set_cache(WE_LS_CACHE_COMMS_KEY, 1);
}
