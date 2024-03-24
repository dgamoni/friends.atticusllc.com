<?php
function custom_child_scripts() {
	//wp_enqueue_style('style_css', get_stylesheet_directory_uri() . '/js/style.css');
	wp_enqueue_script(
	    'plugin_script_js',
	    get_stylesheet_directory_uri() . '/core/js/plugin_script_js.js',
        array(), // array('jquery')
        null, // no ver
        true  // footer
	    );

	wp_localize_script( 'plugin_script_js', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	
}
add_action( 'wp_enqueue_scripts', 'custom_child_scripts' ); 