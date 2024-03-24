<?php

add_action( 'wp_ajax_plugin_check_var_id', 'plugin_check_var_id' );
add_action( 'wp_ajax_nopriv_plugin_check_var_id', 'plugin_check_var_id' );

function plugin_check_var_id() {

	if ( isset($_POST['var_id']) &&  is_user_logged_in() ){
		$var_id = $_POST['var_id'];
		$user_id = get_current_user_id();
		$sku = get_post_meta( $var_id, '_sku', true );

		$count_values = array_count_values(current_customer_month_count($user_id));
		$key = @$count_values[$sku] ?: null;
		$a['count'] = $key;
		$a['sku'] = $sku;
		//echo json_encode( $key );
		// echo json_encode( $a );
	} else {
		$a['count'] = false;
		$a['sku'] = null;
	}

	echo json_encode( $a );
	exit;

}