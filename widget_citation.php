<?php
/*
Plugin Name: Widget Citation 
Plugin URI: https://www.andrebrum.com.br/plugins
Version: 1.0 
Description: Plugin to show a random citations widget in your Wordpress blog. 
Author: André Brum
Author URI: https://www.andrebrum.com.br/
Text Domain: widget-citation
Domain Path: /languages/
*/

# We define some constants that we will use throughout the code.
define('WDCTT_PATH', plugin_dir_path(__FILE__) );
define('WDCTT_TABLE', "widget_citation");
define('WDCTT_URL', plugin_dir_url(__FILE__) );

# we include our function file 
include( WDCTT_PATH . '\includes\functions.php');

# we include our class 
include( WDCTT_PATH . '\includes\widget_citation.class.php');

# we link our activation and deactivation functions in the respective hooks
register_activation_hook(__FILE__,'wdctt_activate_plugin');
register_deactivation_hook(__FILE__,'wdctt_deactivate_plugin');

# activates the plugin intenationalization.
add_action('plugins_loaded', function(){

	load_plugin_textdomain( 'widget-citation', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

});

# includes the style sheet to the front end.
add_action('wp_enqueue_scripts', 'wdctt_enqueue_style');

# includes the stylesheet from the plugin's administrative panel.
add_action( 'admin_enqueue_scripts', 'wdctt_admin_enqueue_style');


?>