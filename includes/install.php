<?php
//install.php

function pc_activate_plugin() {
	$nL =	'
';
	
	//prevent writing to files in debug mode
	if (PC_DEBUG_MODE) {
		return false;
	}//END IF
	
	if (! file_exists(PC_PLUGIN_ARRAY_FILE) ) {
		$fp =	fopen(PC_PLUGIN_ARRAY_FILE, 'w');
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
	
	require(PC_PLUGIN_ARRAY_FILE);
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
	
	//prevent writing to files in debug mode
	if (PC_DEBUG_MODE) {
		return false;
	}//END IF
	
	if (! file_exists(PC_PLUGIN_ARRAY_FILE) ) {
		$fp =	fopen(PC_PLUGIN_ARRAY_FILE, 'w');
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
	
	require(PC_PLUGIN_ARRAY_FILE);
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