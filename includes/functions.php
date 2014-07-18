<?php

if (! defined('PC_VERSION') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}//END IF

global $nL;
$nL =	'
';

function pc_get_tags_to_change() {
	return array('script');
}//END FUNCTION

function getIDOptions() {
	global	$nL;
	
	$return =	'';
	
	if (file_exists(PC_PLUGIN_ARRAY_FILE)) {
		require(PC_PLUGIN_ARRAY_FILE);
	}//END IF
	
	$infoArray_c =	count($infoArray);
	for ($i=0; $i < $infoArray_c; $i++) {
		$return .=	'<option value="' . $i . '">' . $i . '</option>' . $nL;
	}//END FOR LOOP
	$return .=	'<option value="' . $infoArray_c . '" selected="selected">' . $infoArray_c . '</option>' . $nL;
	
	return	$return;
}//END FUNCTION

function add_stylesheet( $hook = '', $name = '' ) {
	$css_path =	'includes/css/';
	
	//do nothing with empty arguments
	if ($hook == '' || $name == '') {
		return;
	}//END IF
	
	//if file does not exist, dont attempt to add it
	if (! file_exists(PC_PLUGIN_DIR . $css_path . $name) ) {
		return;
	}//END IF
	
	wp_register_style( $hook, PC_PLUGIN_URL . $css_path . $name );
	wp_enqueue_style( $hook );
}//END FUNCTION

function add_script( $hook = '', $name = '' ) {
	$script_path =	'includes/js/';
	
	//do nothing with empty arguments
	if ($hook == '' || $name == '') {
		return;
	}//END IF
	
	//if file does not exist, dont attempt to add it
	if (! file_exists(PC_PLUGIN_DIR . $script_path . $name) ) {
		return;
	}//END IF
	
	wp_register_script( $hook, PC_PLUGIN_URL . $script_path . $name );
	wp_enqueue_script( $hook );
}//END FUNCTION

function echo_print_r($array = array(), $return = false) {
	$output =	'<pre>' . print_r($array, true) . '</pre>';
		
	if ($return === false) {
		echo	$output;
	} else {
		return	$output;
	}//END IF
	
}//END FUNCTION

/*
 * Code formatting function
 * @Description:	This function is used to get around apaches mod security when posting form data
 *						We can convert specific problem tags from html to '[' and ']' which bypasses security.
 * @Arguments
 * $code: The string containing code to be formatted.
 * $rw:
 *		'read':	Will change html braces to '[' and ']'. Use this when showing code on screen.
 *		'write':	Will change '[' and ']' from submitted code back to html braces to store code exactly how it should be.
 *
 *	'read' is used to read code to screen and 'write' when you are inserting into the array file.
 */
function pc_format_code($code, $rw) {
	global $nL;
	
	if ($rw === 'read') {
		$charOpen =			"<";
		$charClose =		">";
		$replaceOpen =		"[";
		$replaceClose =	"]";
	} else if ($rw === 'write') {
		$charOpen =			"\[";
		$charClose =		"\]";
		$replaceOpen =		"<";
		$replaceClose =	">";
	} else {
		return "Invalid value '$rw' for second argument of pc_format_code function. Code unchanged:" . $nL . $nL . $code;
	}//END IF
	
	$regexTagsToChange =	'(?=' . implode('|', pc_get_tags_to_change()) . ')';
	$code =	str_replace(array('\\"', "\\'"), array('"', "'"), $code);
	
	$output =	preg_replace("/".$charOpen."(\/?)(?!')".$regexTagsToChange."([^\n\r".$charClose."]*)".$charClose."/", $replaceOpen."$1$2".$replaceClose, $code);
	return	$output;
}//END FUNCTION

//The bread and butter of this entire plugin. DO NOT CHANGE THIS FUNCTION
//The author of this plugin is not responsible for any unwanted changes to your plugins.
//If your site stops functioning due to the altering of this function, the author is free
//of any compensation to the user. You have been warned.
function pc_do_customization($info, $action, &$array = array()) {
	global $nL;
	$new_content =	'';
	
	if (PC_DEBUG_MODE) {
		
		if (file_exists(PC_PLUGIN_DIR . PC_PLUGIN_DEBUG_DIR . 'changedFiles/' . $info['FileName'])) {
			$old_content =	file_get_contents( PC_PLUGIN_DIR . PC_PLUGIN_DEBUG_DIR . 'changedFiles/' . $info['FileName'] );
		} else {
			$old_content =	file_get_contents( dirname(PC_PLUGIN_DIR) . '/' . $info['FilePath'] . $info['FileName'] );
		}//END IF
		
		//we (should) have the contents of the file we want to change. now make changes
		if (strtolower($action) === 'custom') {
			
			if (substr_count($old_content, $info['OldCode']) > 0) {
				$new_content =	str_replace($info['OldCode'], $info['NewCode'], $old_content);
			} else {
				$array['status'] =	0;
				$array['error'] =		"invalidCustomization";
				return false;
			}//END IF
			
		} else if (strtolower($action) === 'original') {
			
			$new_content =	str_replace($info['NewCode'], $info['OldCode'], $old_content);
			
		} else {
			$array['status'] =	0;
			$array['error'] =		"invalidArgument";
			return false;
		}//END IF
		
		$bytes =	file_put_contents( PC_PLUGIN_DIR . PC_PLUGIN_DEBUG_DIR . 'changedFiles/' . $info['FileName'], $new_content, LOCK_EX );
		if ($bytes === false) {
			$array['status'] =	0;
			$array['error'] =		"customizationFailed";
			return false;
		}//END IF
		
	} else {
		
		$filePath =	dirname(PC_PLUGIN_DIR) . '/' . $info['FilePath'] . $info['FileName'];
		if (! file_exists($filePath) ) {
			$array['status'] =	0;
			$array['error'] =		"missingPluginFile";
			return false;
		}//END IF
		
		//retrieve file to change
		$old_content =	file_get_contents( $filePath );
		
		if (strtolower($action) === 'custom') {
			if (substr_count($old_content, $info['OldCode']) > 0) {
				$new_content =	str_replace($info['OldCode'], $info['NewCode'], $old_content);
			} else {
				$array['status'] =	0;
				$array['error'] =		"invalidCustomization";
				return false;
			}//END IF
		} else if (strtolower($action) === 'original') {
			$new_content =	str_replace($info['NewCode'], $info['OldCode'], $old_content);
		} else {
			$array['status'] =	0;
			$array['error'] =		"invalidArgument";
			return false;
		}//END IF
		
		if ($new_content === '') {
			$array['status'] =	0;
			$array['error'] =		"invalidCustomization";
			return false;
		}//END IF
		
		$bytes =	file_put_contents( $filePath, $new_content, LOCK_EX );
		if ($bytes === false) {
			$array['status'] =	0;
			$array['error'] =		"customizationFailed";
			return false;
		}//END IF
		
	}//END IF
	
	$array['status'] =	1;
	$array['error'] =		"";
	return true;
}//END FUNCTION

//FUNCTION to write to the generated infoArray file
function pc_generate_array_file($infoArray) {
	global $nL;
	
	$output =	'$infoArray =	array(' . $nL;
	foreach ($infoArray as $id => $fileData) {
		$output .=	"\t" . $id . " => array(" . $nL;
		foreach ($fileData as $index => $row) {
			$row['OldCode'] = str_replace("\\\\", "\\", str_replace("'", "\'", pc_format_code($row['OldCode'], 'write')));
			$row['NewCode'] = str_replace("\\\\", "\\", str_replace("'", "\'", pc_format_code($row['NewCode'], 'write')));
			$output .=	'' .
							"\t\t" . $index . " => array(" . $nL .
							"\t\t\t'Applied' =>		" . ($row['Applied'] ? 'true' : 'false') . "," . $nL .
							"\t\t\t'CustomName' =>	'" . $row['CustomName'] . "'," . $nL .
							"\t\t\t'FilePath' =>		'" . $row['FilePath'] . "'," . $nL .
							"\t\t\t'FileName' =>		'" . $row['FileName'] . "'," . $nL .
							"\t\t\t'OldCode' =>		'" . $row['OldCode'] . "'," . $nL .
							"\t\t\t'NewCode' =>		'" . $row['NewCode'] . "'," . $nL .
							"\t\t\t'Description' =>	'" . pc_format_code($row['Description'], 'write') . "'," . $nL .
							"\t\t\t'Version' =>		'" . $row['Version'] . "'" . $nL .
							"\t\t)," . $nL .
							"";
		}//END FOREACH LOOP
		$output =	rtrim($output, ",".$nL) . $nL . "\t)," . $nL;
	}//END FOREACH LOOP
	$output =	rtrim($output, ",".$nL) . $nL . ");";
	
	$output =	"" .
					"<?php" . $nL .
					"//" . str_replace(PC_PLUGIN_DIR, "", PC_PLUGIN_ARRAY_FILE) . $nL .
					"//This file is generated and should not be changed by you. Thanks!" . $nL .
					"if (! defined('PC_VERSION') ) {" . $nL .
					"	header('Status: 403 Forbidden');" . $nL .
					"	header('HTTP/1.1 403 Forbidden');" . $nL .
					"	exit;" . $nL .
					"}//END IF" . $nL .
					$output . $nL .
					"?>" .
					"";
	$fp = fopen(PC_PLUGIN_ARRAY_FILE, 'w');
	$bytes =	fwrite($fp, $output);
	fclose($fp);
	
	return	($bytes === false ? false : true);
}//END FUNCTION

function pc_log_action($info, $action) {
	global $nL;
	
	$txt =	'' .
				'Date: ' . date('m/d/Y g:ia') . $nL .
				'Action: ' . $action . $nL .
				'File: ' . dirname(PC_PLUGIN_DIR) . '/' . $info['FilePath'] . $info['FileName'] . $nL .
				'Original Code:' . $nL .
				'-----CODE START-----' . $nL .
				pc_format_code($info['OldCode'], 'write') . $nL .
				'-----CODE END-----' . $nL .
				'Custom Code:' . $nL .
				'-----CODE START-----' . $nL .
				pc_format_code($info['NewCode'], 'write') . $nL .
				'-----CODE END-----' . $nL . $nL .
				'';
	
	$fp =	fopen(PC_PLUGIN_LOG_FILE, 'a');
	$bytes =	fwrite($fp, $txt);
	fclose($fp);
	
	return $bytes;
}//END FUNCTION
?>