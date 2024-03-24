<?php
// Add custom Theme Functions here


// ----------- load core
define('CORE_PATH', get_stylesheet_directory() . '/core');
define('CORE_URL', get_stylesheet_directory_uri()  . '/core');

$dirs = array(
    CORE_PATH . '/functions/',
);
foreach ($dirs as $dir) {
    $other_inits = array();
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (false !== ($file = readdir($dh))) {
                if ($file != '.' && $file != '..' && stristr($file, '.php') !== false) {
                    list($nam, $ext) = explode('.', $file);
                    if ($ext == 'php')
                        $other_inits[] = $file;
                }
            }
            closedir($dh);
        }
    }
    asort($other_inits);
    foreach ($other_inits as $other_init) {
        if (file_exists($dir . $other_init))
            include_once $dir . $other_init;
    }
}
// ----------- end load ore

add_action('wp_footer', 'add_custom_css');
function add_custom_css() { ?>
    <script>
        jQuery( document ).ajaxStop(function() {
            $('.search-price ins').html('<span class="onsale soldout">Sale!</span>');
            $('.search-price ins').show();
        });

        jQuery(document).ready(function($) {
            $('.product-summary .price ins').addClass('ex');
            $('.price ins').not('.ex').html('<span class="onsale soldout">Sale!</span>');
            $('.price ins').show();
            // $('.search-price ins').html('<span class="onsale soldout">Sale!</span>');
            // $('.search-price ins').show();
            $('.product_list_widget ins .woocommerce-Price-amount.amount').html('<span class="onsale soldout">Sale!</span>');
            $('.product_list_widget ins .woocommerce-Price-amount.amount').show();
    

            $('.product-video-popup').magnificPopup({
                type: 'iframe',
                iframe: {
                    patterns: {
                        youtube: {
                            index: 'youtube.com/', 
                            id: function(url) {        
                                var m = url.match(/[\\?\\&]v=([^\\?\\&]+)/);
                                //preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", url, m);
                                if ( !m || !m[1] ) return null;
                                return m[1];
                                //return get_youtube_id_from_url(url);
                            },
                            src: '//www.youtube.com/embed/%id%?autoplay=1'
                        },
                        vimeo: {
                            index: 'vimeo.com/', 
                            id: function(url) {        
                                var m = url.match(/(https?:\/\/)?(www.)?(player.)?vimeo.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/);
                                if ( !m || !m[5] ) return null;
                                return m[5];
                            },
                            src: '//player.vimeo.com/video/%id%?autoplay=1'
                        }
                    }
                }
            });    

        });
    </script>
    <style>

    </style>
    <?php
}




/* - - -*/


/**
 * Credits: https://squelchdesign.com/web-design-newbury/woocommerce-detecting-order-complete-on-order-completion/
 */
add_action('woocommerce_order_status_processing', 'internal_payment_complete', 10, 1);
function internal_payment_complete($order_id) {

    $order = wc_get_order($order_id);
    $user = $order->get_user();
    if($user) {


        $to = 'digital@getskybound.com';
        $bcc = 'dev@digitalclap.com';
        $subject = 'AFF - JSON post copy';
        $message = '';


        $userInfo = get_userdata($user->ID);
        $jsonData = array(
            'name' => $userInfo->first_name . ' ' . $userInfo->last_name,
            'email' => $userInfo->user_email,
            'stripeOrderId' => get_post_meta($order_id, '_transaction_id', true),
            'donation' => '0.00',
            'tax' => floatval(get_post_meta($order_id, '_order_tax', true)),
            'total' => floatval(get_post_meta($order_id, '_order_total', true)),
            'shipping' => array(
                'street1' => get_post_meta($order_id, '_shipping_address_1', true),
                'street2' => get_post_meta($order_id, '_shipping_address_2', true),
                'city' => get_post_meta($order_id, '_shipping_city', true),
                'state' => get_post_meta($order_id, '_shipping_state', true),
                'zip' => get_post_meta($order_id, '_shipping_postcode', true)
            ),
        );

        $idx = 0;
        foreach ($order->get_items() as $item_id => $item_data) {

            $product = $item_data->get_product();
            $item_sku = $product->get_sku();

            $item_quantity = $item_data->get_quantity(); // Get the item quantity

            $jsonData['products'][$idx]['sku'] = $item_sku;
            $jsonData['products'][$idx]['quantity'] = $item_quantity;

            $idx++;

        }

        $jsonObj = json_encode($jsonData);
        $message .= print_r($jsonObj, true);

        /*
        $message .= $userInfo->first_name . ' ' . $userInfo->last_name;
        $message .= "\n";
        $message .= $userInfo->user_email;
        $message .= "\n";
        $message .= $order_id;
        $message .= "\n";
        $message .= print_r(get_post_meta($order_id, '_stripe_source_id', true), true);

        $message .= "\n";
        $message .= print_r(get_post_meta($order_id, '_order_total', true), true);
        $message .= "\n";
        $message .= print_r(get_post_meta($order_id, '_order_tax', true), true);


        $message .= "\n";
        $message .= print_r(get_post_meta($order_id, '_shipping_address_1', true), true);
        $message .= "\n";
        $message .= print_r(get_post_meta($order_id, '_shipping_address_2', true), true);
        $message .= "\n";
        $message .= print_r(get_post_meta($order_id, '_shipping_city', true), true);
        $message .= "\n";
        $message .= print_r(get_post_meta($order_id, '_shipping_state', true), true);
        $message .= "\n";
        $message .= print_r(get_post_meta($order_id, '_shipping_postcode', true), true);

        $message .= "\n";
        $message .= "\n";
        */

        /*
        $message .= '<br>- - -<br>';
        $message = print_r($order, true);
        $message .= '<br>- - -<br>';
        $message .= print_r($user, true);
        $message .= '<br>- - -<br>';
        $message .= print_r(get_class_methods($order), true);
        */


        $ch = curl_init('https://mypetdefense.com/api/v1/orders');
        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => $jsonObj
        ));

        // Send the request
        $response = curl_exec($ch);

        // Check for errors
        if($response === FALSE){
            //die(curl_error($ch));
            $message .= (string)curl_error($ch);
            $message .= "\n";
            $message .= "\n";
            $message .= '(#1)';
        }

        // Decode the response
        $responseData = json_decode($response, TRUE);

        $message .= print_r($response, true);
        $message .= "\n";
        $message .= "\n";
        $message .= '(#2)';

        $headers = array('Content-Type: text/html; charset=UTF-8');
        $headers[] = 'Bcc: dev@digitalclap.com';

        wp_mail($to, $subject, $message, $headers);

        //wp_mail($to, $subject, $message);
    }

}

