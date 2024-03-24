
jQuery(document).ready(function($) {

    $('.single-product .product-summary ins').append('<span class="price_after_click">Click to view price</span><span class="price_after_click_helper">Click on the button to view<br> Atticus Friends & Family price</span>');
    $('.price_after_click').click(function(event) {
        $(this).hide();
        $('.price_after_click_helper').hide();
        $('.product-summary  ins .woocommerce-Price-amount.amount').show();
    });

    $(document).on( 'click', '.swatch', function ( e ) {
		var id = $('.single-product .variation_id').val();
		// console.log(id); 	
  //   });

	//$('.single-product .variation_id').change(function(event) {
		//console.log( $(this).val() );
		    
		    $.ajax({
                type    : "POST",
                url     : MyAjax.ajaxurl,
                dataType: "json",
                //data    : "action=plugin_check_var_id&var_id=" + $(this).val(),
                data    : "action=plugin_check_var_id&var_id=" + id,
                success : function (a) {
                		//console.log(a);
                        if( a.sku && a.count > 2 ) {
                        	console.log(a.count);
                        	console.log(a.sku);
                        	$('.single_add_to_cart_button').addClass('add_to_cart_disable');
                        	$('.single_add_to_cart_button').attr('disabled', 'disabled');
                        	$('.plugin-message').show();
                        	$('.plugin-message .message-container').text('The product with the SKU: '+ a.sku +' has already been purchased 3 times');
                        } else {
                        	//console.log('nolimit');
                        	$('.single_add_to_cart_button').removeClass('add_to_cart_disable');
                        	$('.single_add_to_cart_button').removeAttr('disabled');
                        	$('.plugin-message').hide();
                        }

                }
            });//end ajax

	});

}); //end ready