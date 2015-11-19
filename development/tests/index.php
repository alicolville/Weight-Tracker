<?php

defined('ABSPATH') or die("Jog on!");

$sample_latin = array(
'Nullam nulla eros, ultricies sit amet, nonummy id, imperdiet feugiat, pede. Proin viverra, ligula sit amet ultrices semper.',
'ligula arcu tristique sapien, a accumsan nisi mauris ac eros.',
'Proin faucibus arcu quis ante. In dui magna, posuere eget, vestibulum et, tempor auctor, justo. Duis vel nibh at velit scelerisque suscipit.',
'Nam eget dui. Pellentesque egestas, neque sit amet convallis pulvinar, justo nulla eleifend augue, ac auctor orci leo non est. Morbi mattis ullamcorper velit.',
'Pellentesque libero tortor.',
'Tincidunt et, tincidunt eget, semper nec, quam. Nulla consequat massa quis enim.',
'Curabitur a felis in nunc fringilla tristique. Etiam feugiat lorem non metus. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; In ac dui quis mi consectetuer lacinia. Ut non enim eleifend felis pretium feugiat. Duis arcu tortor, suscipit eget, imperdiet nec, imperdiet iaculis, ipsum.',
'Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Fusce id purus.',
'Vestibulum ante ipsum primis in faucibus orci luctus',
'Etiam feugiat lorem non metus.'
);

$user_id = 5;
$i = 1;
while ($i < 900)
{
   $weight_date =  date('Y-m-d', strtotime('+' . $i++ . ' day'));

   $pounds = rand(30,400);
   $latin_index = rand(0,count($sample_latin) -1);
   $weight_object = ws_ls_weight_object($user_id, 0, 0, 0, $pounds, $sample_latin[$latin_index],	$weight_date, true);


   ws_ls_save_data($user_id, $weight_object);


}
