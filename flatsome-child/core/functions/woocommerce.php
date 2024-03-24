<?php

function sv_change_product_price_display( $price ) {
	 global $product;

    // if($product->is_on_sale()){
    //     $price .= '<span class="onsale soldout">Sale!</span>';
    // }
	$price .= '<span class="onsale soldout sale_search" style="display:none;">Sale!</span>'; 
	return $price;
}
//add_filter( 'woocommerce_get_price_html', 'sv_change_product_price_display' );

// //add_filter( 'woocommerce_cart_item_price', 'sv_change_product_price_display' );



add_filter( 'woocommerce_registration_error_email_exists', 'filter_function_name_1764', 10, 2 );
function filter_function_name_1764( $__, $email ){
	return __( "You already have an account registered to this email address. Please try logging in, or reset your password if you don't know it.", 'woocommerce' );
}

add_filter('woocommerce_structured_data_product_offer','trebacz_add_offer_meta');
function trebacz_add_offer_meta($markup_offer) {
                if ( ! isset( $product ) ) {
                        global $product;
                }
                if ( ! is_a( $product, 'WC_Product' ) ) {
                        return;
                }

				if ( '' !== $product->get_price() ) {
					
					if ( $product->is_type( 'variable' ) ) {
						$lowest  = $product->get_variation_price( 'min', false );
						$highest = $product->get_variation_price( 'max', false );
						//dgamoni
						$max_regular = max( $product->get_variation_prices()['regular_price'] );

						if ( $lowest === $highest ) {
							$markup_offer = array(
								'@type'              => 'Offer',
								'price'              => wc_format_decimal( $max_regular, wc_get_price_decimals() ),
								'priceSpecification' => array(
									'price'                 => wc_format_decimal( $max_regular, wc_get_price_decimals() ),
									'priceCurrency'         => $currency,
									'valueAddedTaxIncluded' => wc_prices_include_tax() ? 'true' : 'false',
								),
							);
						} else {
							$markup_offer = array(
								'@type'     => 'AggregateOffer',
								'lowPrice'  => wc_format_decimal( $lowest, wc_get_price_decimals() ),
								'highPrice' => wc_format_decimal( $highest, wc_get_price_decimals() ),
							);
						}
					

					} else {
						$markup_offer = array(
							'@type'              => 'Offer',
							'price'              => wc_format_decimal( $product->get_price(), wc_get_price_decimals() ),
							'priceSpecification' => array(
								'price'                 => wc_format_decimal( $product->get_price(), wc_get_price_decimals() ),
								'priceCurrency'         => $currency,
								'valueAddedTaxIncluded' => wc_prices_include_tax() ? 'true' : 'false',
							),
						);
					}

	
				}

                return $markup_offer;
}