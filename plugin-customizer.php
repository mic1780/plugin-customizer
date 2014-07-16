<?php
/*
	Plugin Name: Plugin Customizer
	Plugin URI: 
	Description: This is a custom plugin that allows quick rewrites to active plugins (DO NOT DELETE OR CHANGE THIS)
	Author: Michael Cummins
	Version: 1.0.0
	Author URI: https://github.com/mic1780/
	Text Domain: 
 */
if( ! defined('ABSPATH') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}//END IF

function plugin_customizer_plugin() {
	
	define( 'PC_VERSION', '1.0.0' );
	define( 'PC_PLUGIN_FILE', __FILE__ );
	define( 'PC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	define( 'PC_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
	define( 'PC_PLUGIN_ARRAY_FILE', PC_PLUGIN_DIR . 'includes/infoArray.php' );
	define( 'PC_PLUGIN_ERROR_HANDLERS', PC_PLUGIN_DIR . 'pages/errorHandlers.php' );
	define( 'PC_PLUGIN_SUCCESS_HANDLERS', PC_PLUGIN_DIR . 'pages/successHandlers.php' );
	
	if (! file_exists(PC_PLUGIN_DIR . 'includes/functions.php') ) {
		exit('ERROR: Could not find Plugin Customizer function file: includes/functions.php');
	}//END IF
	require_once (PC_PLUGIN_DIR . 'includes/functions.php');
	
	add_stylesheet('PC_stylesheet', 'style.css');
	
	// Only load the Admin class on admin requests, excluding AJAX.
	if( is_admin() && ( false === defined( 'DOING_AJAX' ) || false === DOING_AJAX ) ) {
		// Initialize Admin Class
		require_once PC_PLUGIN_DIR . 'includes/classes/admin.php';
		new PC_Admin();
	}//END IF
	
}//END FUNCTION

add_action('plugins_loaded', 'plugin_customizer_plugin', 10);

?>