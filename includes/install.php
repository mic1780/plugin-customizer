<?php
//install.php

// Check if get_plugins() function exists. This is required on the front end of the
// site, since it is in a file that is normally only loaded in the admin.
if ( ! function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

function pc_activate_plugin() {
	$nL =	'
';
	
	if (! file_exists(PC_PLUGIN_LIVE_ARRAY_FILE) ) {
		$fp =	fopen(PC_PLUGIN_LIVE_ARRAY_FILE, 'w');
		fwrite($fp, "<?php" . $nL . "?>");
		fclose($fp);
	}//END IF
	
	$allPlugins =	get_plugins();
	$plugins =	array();
	if ( isset($allPlugins) && count($allPlugins) > 0 ) {
		foreach ($allPlugins as $plugin_path => $plugin_data) {
			list($plugin_folder, $plugin_file) =	explode('/', $plugin_path);
			$plugins[$plugin_folder] =	$plugin_data['Version'];
		}//END FOREACH LOOP
	}//END IF
	
	if (file_exists(PC_PLUGIN_LIVE_ARRAY_FILE))
		require(PC_PLUGIN_LIVE_ARRAY_FILE);
	
	if (count($infoArray) > 0) {
		foreach ($infoArray as $id => $customArray) {
			
			foreach ($customArray as $key => $row) {
				$plugin_version =	$plugins[ reset( explode('/', $row['FilePath']) ) ];
				
				if ($row['Applied'] === true && strcmp($row['Version'], $plugin_version) === 0) {
					pc_do_customization($row, 'custom');
				}//END IF
				
			}//END FOREACH LOOP
			
		}//END FOREACH LOOP
	}//END IF
	
}//END FUNCTION

function pc_deactivate_plugin() {
	$nL =	'
';
	
	$allPlugins =	get_plugins();
	$plugins =	array();
	if ( isset($allPlugins) && count($allPlugins) > 0 ) {
		foreach ($allPlugins as $plugin_path => $plugin_data) {
			list($plugin_folder, $plugin_file) =	explode('/', $plugin_path);
			$plugins[$plugin_folder] =	$plugin_data['Version'];
		}//END FOREACH LOOP
	}//END IF
	
	if (file_exists(PC_PLUGIN_LIVE_ARRAY_FILE))
		require(PC_PLUGIN_LIVE_ARRAY_FILE);
	
	if (count($infoArray) > 0) {
		foreach ($infoArray as $id => $customArray) {
			
			foreach ($customArray as $key => $row) {
				$plugin_version =	$plugins[ reset( explode('/', $row['FilePath']) ) ];
				
				if ($row['Applied'] === true && strcmp($row['Version'], $plugin_version) === 0) {
					pc_do_customization($row, 'original');
				}//END IF
				
			}//END FOREACH LOOP
			
		}//END FOREACH LOOP
	}//END IF
	
}//END FUNCTION






?>