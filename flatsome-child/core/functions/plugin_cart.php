<?php

add_action( 'woocommerce_before_cart_table', 'before_cart_form_message' );
function before_cart_form_message() {

	if( is_product_limit_message() ) {
		$cur_user_id = get_current_user_id();
		$user = get_user_by('id', $cur_user_id);
		$notice_text = sprintf( 'Hi %1$s! You can only order 2 products of the same sku each 3 month period.', $user->display_name);
		wc_print_notice( $notice_text, 'error' ); 
	}

} 


function woocommerce_button_proceed_to_checkout() {
 $checkout_url = WC()->cart->get_checkout_url(); 

 	if( is_product_limit_message() ) { 
 		$style = ' style="color:#fff;cursor:not-allowed;background-color:#999;"';
 		?>

		 <a class="checkout-button button alt wc-forward" <?php echo $style; ?> >
		 	<?php esc_html_e( 'Proceed to checkout', 'woocommerce' ); ?>
		 </a>

 	<?php } else { ?>

		 <a href="<?php echo esc_url( wc_get_checkout_url() );?>" class="checkout-button button alt wc-forward">
		 	<?php esc_html_e( 'Proceed to checkout', 'woocommerce' ); ?>
		 </a>

 	<?php }

}


add_action( 'woocommerce_proceed_to_checkout', 'message_before_cart_button_new' );
function message_before_cart_button_new() {

	if( is_product_limit_message() ) {
		$notice_text_2 = 'To continue please remove the products with an error';
		wc_print_notice( $notice_text_2, 'error' );
	}
}