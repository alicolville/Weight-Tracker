<?php

    defined('ABSPATH') or die("Jog on!");

    /**
     *
     * Validate the macro percentages
     *
     * @return bool
     */
    function ws_ls_macro_validate_percentages() {

        // All numeric?
        if (false === is_numeric(WS_LS_MACRO_PROTEINS) ||
            false === is_numeric(WS_LS_MACRO_CARBS) ||
            false === is_numeric(WS_LS_MACRO_FATS)) {
            return false;
        }

        // Is their sum 100 (i.e. 100%)
        return (100 == (WS_LS_MACRO_PROTEINS + WS_LS_MACRO_CARBS + WS_LS_MACRO_FATS)) ? true : false;
    }
