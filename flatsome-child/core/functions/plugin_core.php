<?php

function current_customer_month_count( $user_id=null ) {
    if ( empty($user_id) ){
        $user_id = get_current_user_id();
    }
    // Date calculations to limit the query
    //date_default_timezone_set('UTC');

    $today_year = date( 'Y' );
    $today_month = date( 'm' );
    $day = date( 'd' );
    if ($today_month == '01') {
        $month = '09';
        $year = $today_year - 1;
    } else{
        $month = $today_month - 3;
        $month = sprintf("%02d", $month);
        $year = $today_year - 1;
    }

    // ORDERS FOR LAST 30 DAYS (Time calculations)
    $now = strtotime('now');
    // Set the gap time (here 30 days)
    $gap_days = 90;
    $gap_days_in_seconds = 60*60*24*$gap_days;
    $gap_time = $now - $gap_days_in_seconds;

    $order_statuses = array('wc-on-hold', 'wc-processing', 'wc-completed');

    // The query arguments
    $args = array(
        // WC orders post type
        'post_type'   => 'shop_order',
        // Only orders with status "completed" (others common status: 'wc-on-hold' or 'wc-processing')
        // 'post_status' => 'wc-completed', 
        'post_status' =>  $order_statuses, 
        // all posts
        'numberposts' => -1,
        // for current user id
        'meta_key'    => '_customer_user',
        'meta_value'  => $user_id,
		// 'date_query' => array(
		//         array(
		//             'column' => 'post_date_gmt',
		//             'after'  => '90 days ago',
		//         )
		// ),  

        // 'date_query' => array(
        //     //orders published on last 30 days
        //     'relation' => 'OR',
        //     array(
        //         'year' => $today_year,
        //         'month' => $today_month,
        //     ),
        //     array(
        //         'year' => $year,
        //         'month' => $month,
        //     ),
        // ),

    );



	add_filter( 'posts_where', 'filter_where' );
	$query = new WP_Query( $args );
	$customer_orders = $query->posts;
	remove_filter( 'posts_where', 'filter_where' );

    // Get all customer orders
    //$customer_orders = get_posts( $args );
    $count = 0;

    $res_data = array();

    if (!empty($customer_orders)) {
        $customer_orders_date = array();
        // Going through each current customer orders
        foreach ( $customer_orders as $customer_order ){
            // Conveting order dates in seconds
            $customer_order_date = strtotime($customer_order->post_date);
            
            $all = true;
            // $res_data['post_date'] .= date('d - m - Y', $customer_order_date); 
            // $res_data['gap_time'] .= date('d - m - Y', $gap_time);

            // Only past 30 days orders
            // if ( $customer_order_date > $gap_time ) {
            if ( $all ) {
                $customer_order_date;
                $order = new WC_Order( $customer_order->ID );
                $order_items = $order->get_items();
                // Going through each current customer items in the order
                foreach ( $order_items as $item_id => $item ){
                	
                	$product = $order->get_product_from_item( $item );
					$sku = $product->get_sku();

                	//$res_data['item'] .= $item;
                	//$res_data['sku'] .= $sku;
                	array_push($res_data, $sku);

                    $count++;
                }
            }
        }

        return $res_data;

    }
}



function filter_where( $where = '' ) {
	// $where .= " AND post_date >= '" . date('Y-m-d', strtotime('-90 days')) . "'" . " AND post_date <= '" . date('Y-m-d', strtotime('-30 days')) . "'";
	$where .= " AND post_date >= '" . date('Y-m-d', strtotime('-90 days')) . "' ";
	return $where;
} 


function check_limit_orderproduct_by_sku() {

	$status = false;

	if( is_user_logged_in() ) {
		$cur_user_id = get_current_user_id();
		//var_dump(current_customer_month_count($cur_user_id));

		$current_sku = array();
		$current_sku_inf = '';

		//var_dump( array_count_values(current_customer_month_count($cur_user_id)) );
		$count_values = array_count_values(current_customer_month_count($cur_user_id));


		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			//var_dump( $_product->get_sku() );
			array_push($current_sku, $_product->get_sku());

			

			if ( in_array( $_product->get_sku(), current_customer_month_count($cur_user_id) ) ) {
				
				$key = @$count_values[$_product->get_sku()] ?: null;
				var_dump($key);

				$current_sku_inf .= $_product->get_sku();
				$user = get_user_by('id', $cur_user_id);
				$notice_text = sprintf( 'Hey %1$s You can only order 2 products of the same sku each 3 month period! Product %2$s you already by %3$s ', $user->display_name, $_product->get_sku(), $key );

				wc_print_notice( $notice_text, 'error' );
				//var_dump('yes');
				//var_dump($_product->get_sku());
			}
		}



		// $notice_text_2 = sprintf( 'You need delete Product/s wich sku: %1$s ', $current_sku_inf );
		// wc_print_notice( $notice_text_2, 'notice' );

		//var_dump($current_sku);

	}

}