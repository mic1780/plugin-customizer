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
		
		if ( substr($newCustom['FilePath'], -1) !== '/' ) {
			$newCustom['FilePath'] .=	'/';
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
		
		$version_holder =	$infoArray[$id][$index]['Version'];
		
		$infoArray[$id][$index]['Applied'] = true;
		$infoArray[$id][$index]['Version'] = $plugins[ reset( explode('/', $infoArray[$id][$index]['FilePath']) ) ];
		$res =	pc_generate_array_file($infoArray);
		
		if ($res === false) {
			$this->status =	0;
			$this->error =		"generateFileFailed";
			break;
		}//END IF
		
		//apply code changes here
		$res =	pc_do_customization($infoArray[$id][$index], 'custom', $thisArray);
		$this->status =	$thisArray['status'];
		$this->error =		$thisArray['error'];
		if ($res === false) {
			$infoArray[$id][$index]['Applied'] =	false;
			$infoArray[$id][$index]['Version'] =	$version_holder;
			pc_generate_array_file($infoArray);
			unset($version_holder);
			break;
		}//END IF
		
		$res =	pc_log_action($infoArray[$id][$index], 'Activate Customization');
		if ($res === false) {
			$this->error =		"actionLogFailed";
		}//END IF
		
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
		
		$version_holder =	$infoArray[$id][$index]['Version'];
		$infoArray[$id][$index]['Version'] = $plugins[ reset( explode('/', $infoArray[$id][$index]['FilePath']) ) ];
		$res =	pc_generate_array_file($infoArray);
		
		if ($res === false) {
			$this->status =	0;
			$this->error =		"generateFileFailed";
			break;
		}//END IF
		
		//reapply code here
		$res =	pc_do_customization($infoArray[$id][$index], 'custom', $thisArray);
		$this->status =	$thisArray['status'];
		$this->error =		$thisArray['error'];
		if ($res === false) {
			$infoArray[$id][$index]['Version'] =	$version_holder;
			pc_generate_array_file($infoArray);
			unset($version_holder);
			break;
		}//END IF
		
		$res =	pc_log_action($infoArray[$id][$index], 'Reapply Customization');
		if ($res === false) {
			$this->error =		"actionLogFailed";
		}//END IF
		
		
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
		
		//now if the version the customization was for is older then the current plugin version code is not applied to file so skip this
		if (strcmp($infoArray[$id][$index]['Version'], $plugins[ reset( explode('/', $infoArray[$id][$index]['FilePath']) ) ]) >= 0) {
			
			//unapply code changes here
			$res =	pc_do_customization($infoArray[$id][$index], 'original', $thisArray);
			$this->status =	$thisArray['status'];
			$this->error =		$thisArray['error'];
			if ($res === false) {
				$infoArray[$id][$index]['Applied'] =	true;
				pc_generate_array_file($infoArray);
				break;
			}//END IF
			
		}//END IF
		
		$res =	pc_log_action($infoArray[$id][$index], 'Dectivate Customization');
		if ($res === false) {
			$this->error =		"actionLogFailed";
		}//END IF
		
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
		
		$row =	$infoArray[$id][$index];
		
		//remove and shift the array elements
		$elCount =	count($infoArray[$id]);
		for ($i = $index; $i < $elCount - 1; $i++) {
			$infoArray[$id][$i] =	$infoArray[$id][$i+1];
		}//END FOR LOOP
		unset($infoArray[$id][$elCount-1]);
		
		
		$res =	pc_generate_array_file($infoArray);
		if ($res === false) {
			$this->status =	0;
			$this->error =		"generateFileFailed";
			break;
		}//END IF
		
		$res =	pc_log_action($row, 'Delete Customization');
		if ($res === false) {
			$this->error =		"actionLogFailed";
		}//END IF
		
		break;
	case	'resetdebugfiles':
		if (! PC_DEBUG_MODE) {
			wp_redirect( $this->page );
			exit;
			break;
		}//END IF
		
		$infoArraySrcFile =	PC_PLUGIN_DIR . 'includes/infoArray.php';
		$changedFiles =	glob(PC_PLUGIN_DIR . PC_PLUGIN_DEBUG_DIR . 'changedFiles/*');
		//echo_print_r($changedFiles);
		$res =	array();
		
		if (file_exists(PC_PLUGIN_ARRAY_FILE)) {
			$res[] =	unlink(PC_PLUGIN_ARRAY_FILE);
		}//END IF
		if (file_exists(PC_PLUGIN_LOG_FILE)) {
			$res[] =	unlink(PC_PLUGIN_LOG_FILE);
		}//END IF
		
		$res[] =	copy($infoArraySrcFile, PC_PLUGIN_ARRAY_FILE);
		foreach ($changedFiles as $filePath) {
			if ($filePath == PC_PLUGIN_DIR . PC_PLUGIN_DEBUG_DIR . 'changedFiles/index.php') {
				continue;
			}//END IF
			$res[] =	unlink($filePath);
		}//END FOREACH LOOP
		
		foreach ($res as $status) {
			if ($status === false) {
				$this->status =	0;
				$this->error =		"resetDebugFailed";
				break;
			}//END IF
		}//END FOREACH LOOP
		
		break;
}//END SWITCH
?>