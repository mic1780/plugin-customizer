<?php
//actions.php

if (! defined('PC_VERSION') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}//END IF

$allPlugins =	get_plugins();
$plugins =	array();
if ( isset($allPlugins) && count($allPlugins) > 0 ) {
	foreach ($allPlugins as $plugin_path => $plugin_data) {
		list($plugin_folder, $plugin_file) =	explode('/', $plugin_path);
		$plugins[$plugin_folder] =	$plugin_data['Version'];
	}//END FOREACH LOOP
}//END IF
$pa =	(isset($_POST['pa']) ? $_POST['pa'] : (isset($_GET['pa']) ? $_GET['pa'] : ''));

switch (strtolower($pa)) {
	case	'newcustom':
		$newCustom =	$_POST['newCustom'];
		$newCustom['Version'] =	'';
		
		if (
			$newCustom['FilePath'] == '' ||
			$newCustom['FileName'] == '' ||
			$newCustom['OldCode'] == '' ||
			$newCustom['NewCode'] == ''
		) {
			$this->status =	0;
			$this->error =		"missingVariables";
			break;
		}//END IF
		
		if (file_exists( PC_PLUGIN_ARRAY_FILE )) {
			require(PC_PLUGIN_ARRAY_FILE);
		}//END IF
		
		$plugin_folder =	reset(explode('/', $newCustom['FilePath']));
		
		if (count($allPlugins) > 0) {
			foreach ($allPlugins as $plugin_path => $plugin_data) {
				$plugin_dir =	reset(explode('/', $plugin_path));
				if ($plugin_dir === $plugin_folder) {
					$newCustom['Version'] =	$plugin_data['Version'];
					break;
				}//END IF
			}//END FOREACH LOOP
		}//END IF
		
		if ($newCustom['Version'] === '') {
			$this->status =	0;
			$this->error =		'invalidPluginFolder';
			break;
		}//END IF
		
		$infoArray[$newCustom['ID']][] =	array(
															'Applied' =>		false,
															'CustomName' =>	$newCustom['CustomName'],
															'FilePath' =>		$newCustom['FilePath'],
															'FileName' =>		$newCustom['FileName'],
															'OldCode' =>		$newCustom['OldCode'],
															'NewCode' =>		$newCustom['NewCode'],
															'Description' =>	$newCustom['Description'],
															'Version' =>		$newCustom['Version']
															);
		$res =	pc_generate_array_file($infoArray);
		
		if ($res === false) {
			$this->status =	0;
			$this->error =		'generateFileFailed';
		}//END IF
		
		break;
	case	'apply':
		$id =	(isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? $_GET['id'] : ''));
		$index =	(isset($_POST['index']) ? $_POST['index'] : (isset($_GET['index']) ? $_GET['index'] : ''));
		
		if ($id === '' || $index === '') {
			$this->status =	0;
			$this->error =		"missingVariables";
			break;
		}//END IF
		
		$id =		intval($id);
		$index =	intval($index);
		
		if (file_exists(PC_PLUGIN_ARRAY_FILE)) {
			require(PC_PLUGIN_ARRAY_FILE);
		}//END IF
		
		if (! isset($infoArray[$id]) ) {
			$this->status =	0;
			$this->error =		"invalidArrayID";
			break;
		} else if (! isset($infoArray[$id][$index]) ) {
			$this->status =	0;
			$this->error =		"invalidArrayIndex";
			break;
		} else if ($infoArray[$id][$index]['Applied'] === true) {
			$this->status =	0;
			$this->error =		"alreadyApplied";
			break;
		}//END IF
		
		$infoArray[$id][$index]['Applied'] = true;
		$infoArray[$id][$index]['Version'] = $plugins[ reset( explode('/', $infoArray[$id][$index]['FilePath']) ) ];
		$res =	pc_generate_array_file($infoArray);
		
		if ($res === false) {
			$this->status =	0;
			$this->error =		"generateFileFailed";
			break;
		}//END IF
		
		//apply code changes here
		
		break;
	case	'reapply':
		$id =	(isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? $_GET['id'] : ''));
		$index =	(isset($_POST['index']) ? $_POST['index'] : (isset($_GET['index']) ? $_GET['index'] : ''));
		
		if ($id === '' || $index === '') {
			$this->status =	0;
			$this->error =		"missingVariables";
			break;
		}//END IF
		
		$id =		intval($id);
		$index =	intval($index);
		
		if (file_exists(PC_PLUGIN_ARRAY_FILE)) {
			require(PC_PLUGIN_ARRAY_FILE);
		}//END IF
		
		if (! isset($infoArray[$id]) ) {
			$this->status =	0;
			$this->error =		"invalidArrayID";
			break;
		} else if (! isset($infoArray[$id][$index]) ) {
			$this->status =	0;
			$this->error =		"invalidArrayIndex";
			break;
		} else if ($infoArray[$id][$index]['Applied'] === true && strcmp($infoArray[$id][$index]['Version'], $plugins[ reset( explode('/', $infoArray[$id][$index]['FilePath']) ) ]) >= 0) {
			//if applied and was applied on to this version or a later version, dont let then apply it twice
			$this->status =	0;
			$this->error =		"alreadyApplied";
			break;
		}//END IF
		
		$infoArray[$id][$index]['Version'] = $plugins[ reset( explode('/', $infoArray[$id][$index]['FilePath']) ) ];
		$res =	pc_generate_array_file($infoArray);
		
		if ($res === false) {
			$this->status =	0;
			$this->error =		"generateFileFailed";
			break;
		}//END IF
		
		//reapply code here
		
		break;
	case	'unapply':
		$id =	(isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? $_GET['id'] : ''));
		$index =	(isset($_POST['index']) ? $_POST['index'] : (isset($_GET['index']) ? $_GET['index'] : ''));
		
		if ($id === '' || $index === '') {
			$this->status =	0;
			$this->error =		"missingVariables";
			break;
		}//END IF
		
		$id =		intval($id);
		$index =	intval($index);
		
		if (file_exists(PC_PLUGIN_ARRAY_FILE)) {
			require(PC_PLUGIN_ARRAY_FILE);
		}//END IF
		
		if (! isset($infoArray[$id]) ) {
			$this->status =	0;
			$this->error =		"invalidArrayID";
			break;
		} else if (! isset($infoArray[$id][$index]) ) {
			$this->status =	0;
			$this->error =		"invalidArrayIndex";
			break;
		} else if ($infoArray[$id][$index]['Applied'] === false) {
			$this->status =	0;
			$this->error =		"alreadyNotApplied";
			break;
		}//END IF
		
		$infoArray[$id][$index]['Applied'] = false;
		$res =	pc_generate_array_file($infoArray);
		
		if ($res === false) {
			$this->status =	0;
			$this->error =		"generateFileFailed";
			break;
		}//END IF
		
		//unapply code changes here
		
		break;
	case	'remove':
		$id =	(isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? $_GET['id'] : ''));
		$index =	(isset($_POST['index']) ? $_POST['index'] : (isset($_GET['index']) ? $_GET['index'] : ''));
		
		if ($id === '' || $index === '') {
			$this->status =	0;
			$this->error =		"missingVariables";
			break;
		}//END IF
		
		$id =		intval($id);
		$index =	intval($index);
		
		if (file_exists(PC_PLUGIN_ARRAY_FILE)) {
			require(PC_PLUGIN_ARRAY_FILE);
		}//END IF
		
		if (! isset($infoArray[$id]) ) {
			$this->status =	0;
			$this->error =		"invalidArrayID";
			break;
		} else if (! isset($infoArray[$id][$index]) ) {
			$this->status =	0;
			$this->error =		"invalidArrayIndex";
			break;
		} else if ($infoArray[$id][$index]['Applied'] === true) {
			$this->status =	0;
			$this->error =		"cantRemoveApplied";
			break;
		}//END IF
		
		unset($infoArray[$id][$index]);
		
		echo_print_r($infoArray);
		
		break;
}//END SWITCH
?>