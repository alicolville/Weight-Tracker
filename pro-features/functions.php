<?php

defined('ABSPATH') or die("Jog on!");

function ws_ls_calculate_bmi($cm, $kg) {

	$bmi = false;

	if(is_numeric($cm) && is_numeric($kg)) {

		$bmi = $kg / ($cm * $cm);
		$bmi = $bmi * 10000;
		$bmi = round($bmi, 1);
	}

	return $bmi;
}

function ws_ls_calculate_bmi_label($bmi) {

	if(is_numeric($bmi)) {

		if($bmi < 18.5) {
			return __('Underweight', WE_LS_SLUG);
		} else if ($bmi >= 18.5 && $bmi <= 24.9) {
			return __('Healthy', WE_LS_SLUG);
		}
		else if ($bmi >= 25 && $bmi <= 29.9) {
			return __('Overweight', WE_LS_SLUG);
		} else if ($bmi >= 30) {
			return __('Heavily Overweight', WE_LS_SLUG);
		}
		// } else if ($bmi >= 30 && $bmi <= 39.9) {
		// 	return __('Obese', WE_LS_SLUG);
		// } else if ($bmi >= 39.9) {
		// 	return __('Severely / Morbidly obese', WE_LS_SLUG);
		// }

	}

	return 'Err';
}


// $bmi = ws_ls_calculate_bmi(150, 66);
// $label = ws_ls_calculate_bmi_label($bmi);
