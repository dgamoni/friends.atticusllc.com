<?php


add_filter( 'woocommerce_order_button_html', 'replace_order_button_html', 10, 2 );
function replace_order_button_html( $order_button ) {

    if( !is_product_limit_message() ) return $order_button;

    $order_button_text = __( "Place order", "woocommerce" );

    $style = ' style="color:#fff;cursor:not-allowed;background-color:#999;"';
    return '<a class="button alt"'.$style.' name="woocommerce_checkout_place_order" id="place_order" >' . esc_html( $order_button_text ) . '</a>';
} 


add_action( 'woocommerce_before_checkout_form', 'before_checkout_form_message' );
function before_checkout_form_message() {

	if( is_product_limit_message() ) {
		$cur_user_id = get_current_user_id();
		$user = get_user_by('id', $cur_user_id);
		$notice_text = sprintf( 'Hey %1$s! You can only order 2 products of the same sku each 3 month period!', $user->display_name);
		wc_print_notice( $notice_text, 'error' ); 
	}

}

function product_limit_message($sku) {

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
			$key = @$count_values[$_product->get_sku()] ?: null;
			//var_dump($key);

			if ( in_array( $_product->get_sku(), current_customer_month_count($cur_user_id) ) && $key > 2 ) {
				
				$current_sku_inf .= $_product->get_sku();
				$user = get_user_by('id', $cur_user_id);
				$notice_text2 = sprintf( 'The product with the SKU: %1$s has already been purchased %2$s times', $_product->get_sku(), $key );

				//wc_print_notice( $notice_text, 'error' );
				if ( $sku == $_product->get_sku()) {
					wc_print_notice( $notice_text2, 'error' );
				}
				// wc_print_notice( $notice_text2, 'error' );

			}
		}


	}

}

function is_product_limit_message() {

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
			$key = @$count_values[$_product->get_sku()] ?: null;
			//var_dump($key);

			if ( in_array( $_product->get_sku(), current_customer_month_count($cur_user_id) ) && $key > 2 ) {
				
				$current_sku_inf .= $_product->get_sku();
				$user = get_user_by('id', $cur_user_id);
				$notice_text = sprintf( 'Hey %1$s! You can only order 2 products of the same sku each 3 month period!', $user->display_name);
				$notice_text2 = sprintf( 'Product SKU: %1$s you already by %2$s ', $_product->get_sku(), $key );

				//wc_print_notice( $notice_text, 'error' );
				// if ( $sku == $_product->get_sku()) {
				// 	wc_print_notice( $notice_text2, 'error' );
				// }
				// wc_print_notice( $notice_text2, 'error' );

				$status = true;

			}
		}

		return $status;


	} else {
		return $status;
	}

}


//add_action( 'woocommerce_review_order_before_submit', 'message_before_checkout_button' );
function message_before_checkout_button() {

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
			$key = @$count_values[$_product->get_sku()] ?: null;
			//var_dump($key);

			if ( in_array( $_product->get_sku(), current_customer_month_count($cur_user_id) ) && $key > 2 ) {
				
	
				

				$current_sku_inf .= '<p>'.$_product->get_name(). '</p>';
				$user = get_user_by('id', $cur_user_id);
				$notice_text = sprintf( 'Hey %1$s You can only order 2 products of the same sku each 3 month period! Product %2$s you already by %3$s ', $user->display_name, $_product->get_sku(), $key );

				//wc_print_notice( $notice_text, 'error' );
				//var_dump('yes');
				//var_dump($_product->get_sku());
			}
		}



		//$notice_text_2 = sprintf( 'To continue, go to the Cart and remove the Products %1$s', $current_sku_inf );
		$notice_text_2 = sprintf( 'To continue please remove the products with an error', $current_sku_inf );
		wc_print_notice( $notice_text_2, 'error' );

		//var_dump($current_sku);

	}

}

add_action( 'woocommerce_review_order_before_submit', 'message_before_checkout_button_new' );
function message_before_checkout_button_new() {

	if( is_product_limit_message() ) {
		$notice_text_2 = 'To continue please remove the products with an error';
		wc_print_notice( $notice_text_2, 'error' );
	}
}