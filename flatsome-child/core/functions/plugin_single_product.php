<?php

function get_current_month_count($user_id) {

	global $product;
	
	//$user_id = get_current_user_id();
	//$month_count = current_customer_month_count($user_id);
	$count_values = array_count_values(current_customer_month_count($user_id));
	$key = @$count_values[$product->get_sku()] ?: null;
	//var_dump($month_count);
	return $key;

}

