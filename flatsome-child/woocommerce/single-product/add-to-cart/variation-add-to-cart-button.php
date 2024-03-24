<?php
/**
 * Single variation cart button
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;
?>
<div class="woocommerce-variation-add-to-cart variations_button">
	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	<?php
	do_action( 'woocommerce_before_add_to_cart_quantity' );

	woocommerce_quantity_input( array(
		'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
		'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
		'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
	) );

	do_action( 'woocommerce_after_add_to_cart_quantity' );
	?>

	<?php 

	//dgamoni

	if( is_user_logged_in() && get_current_month_count(get_current_user_id()) > 2 ) { 
			$style = ' style="color:#fff;cursor:not-allowed;background-color:#999;" disabled';
		?>
		
		<button type="submit" class="single_add_to_cart_button button alt" <?php echo $style; ?> ><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
		
		<?php 
			
			$notice_text2 = sprintf( 'The product has already been purchased %1$s times', get_current_month_count(get_current_user_id()) );
			wc_print_notice( $notice_text2, 'error' );

		?>
	<?php } else {

	?>
		<button type="submit" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
		
		<ul class="plugin-message woocommerce-error message-wrapper" role="alert">
			<li>
				<div class="message-container container alert-color medium-text-center">
					<!-- The product with the SKU: 0001-1 has already been purchased 3 times -->
				</div>
			</li>
		</ul>

	<?php } ?>


	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

	<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
	<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
	<input type="hidden" name="variation_id" class="variation_id" value="0" />
</div>

<?php 
//var_dump(max($product->get_variation_prices()['regular_price'])); 
?>