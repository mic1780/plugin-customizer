<?php

if (! defined('PC_VERSION') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}//END IF

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
		return "Invalid value '$rw' for second argument of pc_format_code function. Code unchanged:\n\n" . $code;
	}//END IF
	
	$regexTagsToChange =	'(?=' . implode('|', pc_get_tags_to_change()) . ')';
	$code =	str_replace(array('\\"', "\\'"), array('"', "'"), $code);
	
	$output =	preg_replace("/".$charOpen."(\/?)(?!')".$regexTagsToChange."([^\n\r".$charClose."]*)".$charClose."/", $replaceOpen."$1$2".$replaceClose, $code);
	return	$output;
}//END FUNCTION

//FUNCTION to write to the generated infoArray file
function pc_generate_array_file($infoArray) {
	
	$output =	'$infoArray =	array(' . "\n";
	foreach ($infoArray as $id => $fileData) {
		$output .=	"\t" . $id . " => array(\n";
		foreach ($fileData as $index => $row) {
			$row['OldCode'] = str_replace("\\\\", "\\", str_replace("'", "\'", pc_format_code($row['OldCode'], 'write')));
			$row['NewCode'] = str_replace("\\\\", "\\", str_replace("'", "\'", pc_format_code($row['NewCode'], 'write')));
			$output .=	'' .
							"\t\t" . $index . " => array(\n" .
							"\t\t\t'Applied' =>		" . ($row['Applied'] ? 'true' : 'false') . ",\n" .
							"\t\t\t'CustomName' =>	'" . $row['CustomName'] . "',\n" .
							"\t\t\t'FilePath' =>		'" . $row['FilePath'] . "',\n" .
							"\t\t\t'FileName' =>		'" . $row['FileName'] . "',\n" .
							"\t\t\t'OldCode' =>		'" . $row['OldCode'] . "',\n" .
							"\t\t\t'NewCode' =>		'" . $row['NewCode'] . "',\n" .
							"\t\t\t'Description' =>	'" . pc_format_code($row['Description'], 'write') . "',\n" .
							"\t\t\t'Version' =>		'" . $row['Version'] . "'\n" .
							"\t\t),\n" .
							"";
		}//END FOREACH LOOP
		$output =	rtrim($output, ",\n") . "\n\t),\n";
	}//END FOREACH LOOP
	$output =	rtrim($output, ",\n") . "\n);";
	
	$output =	"" .
					"<?php\n" . $nL .
					"//" . str_replace(PC_PLUGIN_DIR, "", PC_PLUGIN_ARRAY_FILE)  . "\n" .
					"//This file is generated and should not be changed by you. Thanks!\n" .
					"if (! defined('PC_VERSION') ) {\n" .
					"	header('Status: 403 Forbidden');\n" .
					"	header('HTTP/1.1 403 Forbidden');\n" .
					"	exit;\n" .
					"}//END IF\n" .
					$output .
					"\n?>" .
					"";
	$fp = fopen(PC_PLUGIN_ARRAY_FILE, 'w');
	$bytes =	fwrite($fp, $output);
	fclose($fp);
	
	return	($bytes === false ? false : true);
}//END FUNCTION
?>