<?php
defined('ABSPATH') or die("Jog on!");

function ws_ls_convert_to_cm($feet, $inches = 0) {
    $inches = ($feet * 12) + $inches;
    return round($inches / 0.393701, 2);
}
function ws_ls_stones_pounds_to_pounds_only($stones, $pounds)
{
  return ($stones * 14) + $pounds;
}
function ws_ls_to_kg($stones, $pounds)
{
	$pounds += $stones * 14;
	return round($pounds / 2.20462, 2);
}
function ws_ls_pounds_to_kg($pounds)
{
	return round($pounds / 2.20462, 2);
}
function ws_ls_to_lb($kg)
{
	$pounds = $kg * 2.20462;
	return round($pounds, 2);
}
function ws_ls_to_stones($pounds)
{
	$pounds = $pounds / 14;
	return round($pounds, 2);
}
function ws_ls_pounds_to_stone_pounds($lb)
{
	$weight = array ("stones" => 0, "pounds" => 0);
	$weight["stones"] = floor($lb / 14);
 	$weight["pounds"] = Round(fmod($lb, 14), 1);
  return $weight;
}
function ws_ls_to_stone_pounds($kg)
{
		$weight = array ("stones" => 0, "pounds" => 0);
    $totalPounds = Round($kg * 2.20462, 3);
    $weight["stones"] = floor($totalPounds / 14);
    $weight["pounds"] = Round(fmod($totalPounds, 14), 1);
    return $weight;
}
function ws_ls_round_decimals($value)
{
  if (is_numeric($value)) {
    $value = round($value, 2);
  }
  return $value;
}
function ws_ls_heights() {
return [
    0 => '',
    142 => '4\'8" - 142cm',
    145 => '4\'8" - 145cm',
    147 => '4\'9" - 147cm',
    150 => '4\'11" - 150cm',
  	152 => '5\'0" - 152cm',
  	155 => '5\'1" - 155cm',
    157 => '5\'2" - 157cm',
  	160 => '5\'3" - 160cm',
  	163 => '5\'4" - 163cm',
    165 => '5\'5" - 165cm',
  	168 => '5\'6" - 168cm',
  	170 => '5\'7" - 170cm',
    173 => '5\'8" - 173cm',
    175 => '5\'9" - 175cm',
    178 => '5\'10" - 178cm',
    180 => '5\'11" - 180cm',
    183 => '6\'0" - 183cm',
    185 => '6\'1" - 185cm',
    188 => '6\'2" - 188cm',
    191 => '6\'3" - 191cm',
    193 => '6\'4" - 193cm',
    195 => '6\'5" - 195cm',
    198 => '6\'6" - 198cm',
    201 => '6\'7" - 201cm'
  ];
}
