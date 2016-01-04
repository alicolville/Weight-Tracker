<?php

	defined('ABSPATH') or die("Jog on!");

function ws_ls_get_measurement_settings()
{
    if(defined('WE_LS_MEASUREMENTS')) {
        
        $settings = json_decode(WE_LS_MEASUREMENTS, true);
       
        // Settings specified in admin? 
        if (get_option('ws-ls-measurement') != false) {
            
            $user_defined = get_option('ws-ls-measurement');
            if(isset($user_defined['colors'])) {
                foreach($user_defined['colors'] as $slug => $colour) {
                    $settings[$slug]['chart_colour'] = $colour;
                }
            }
            if(isset($user_defined['enabled'])) {
                foreach($user_defined['enabled'] as $slug => $enabled) { 
                    if ('on' == $enabled) {
                        $settings[$slug]['enabled'] = true;
                    } else {
                        $settings[$slug]['enabled'] = false;
                    }                  
                }
            }
        }
        
        return $settings;
    }
    return false;
}