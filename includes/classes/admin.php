<?php

if (! defined('PC_VERSION') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}//END IF

class PC_Admin {
	
	private $page =	'';
	private $actionsFile =	'';
	private $status =	1;
	private $error =	'';
	
	//Constructor
	public function __construct() {
		
		$this->actionsFile =	PC_PLUGIN_DIR . 'includes/actions.php';
		$this->page =	admin_url( 'admin.php?page=' . (isset($_GET['page']) ? $_GET['page'] : 'plug-custom') );
		
		$this->setup_hooks(); 
	}
	
	public function setup_hooks() {
		add_action( 'admin_menu', array( $this, 'build_menu' ) );
		add_action( 'init', array( $this, 'process_action' ) );
	}//END FUNCTION
	
	public function build_menu() {
		$required_cap = apply_filters('pc_settings_cap', 'manage_options');
		add_menu_page('Plugin Customizer', 'Plugin Customizer', $required_cap, 'plug-custom', array ($this, 'get_customizer_page') );
		
		//add submenu pages
		add_submenu_page('plug-custom', 'Customizer - Plugin Customizer', 'Customizer', $required_cap, 'plug-custom', array($this, 'get_customizer_page') );
		add_submenu_page('plug-custom', 'Settings - Plugin Customizer', 'Settings', $required_cap, 'plug-custom-settings', array($this, 'get_settings_page') );
	}//END FUNCTION
	
	public function get_customizer_page() {
		require( PC_PLUGIN_DIR . 'pages/customizer.php' );
	}//END PUBLIC FUNCTION
	
	public function get_settings_page() {
		require( PC_PLUGIN_DIR . 'pages/settings.php' );
	}//END FUNCTION
	
	public function process_action() {
		$pa =	(isset($_POST['pa']) ? $_POST['pa'] : (isset($_GET['pa']) ? $_GET['pa'] : ''));
		
		//check if we have an action to work with
		if ( $pa === '' ) {
			return;
		}//END IF
		
		if (! file_exists( $this->actionsFile ) ) {
			$this->status =	0;
			$this->error =		"missingActionFile";
			return;
		}//END IF
		
		require( $this->actionsFile );
		
	}//END FUNCTION
	
}//END CLASS

?>