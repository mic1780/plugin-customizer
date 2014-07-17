<?php
//errorHandlers.php
//include this file when you want to handle some error codes ($this->status will be 0 when an error occurs)

if (! defined('PC_VERSION') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}//END IF


$status =	$this->status;
$error =		$this->error;

//if we do not have an error, just dont bother with the rest of the script
if ($status === 1) {
	return;
}//END IF

$output =	'';

//first lets do an if statement to see if the status is 0 (maybe add different codes later on?)
if ($status === 0) {
	/*
	 *	Error List:
	 *		alreadyApplied
	 *		alreadyNotApplied
	 *		cantRemoveApplied
	 *		customizationFailed
	 *		generateFileFailed
	 *		invalidArgument
	 *		invalidArrayID
	 *		invalidArrayIndex
	 *		invalidCustomization
	 *		invalidPluginFolder
	 *		missingActionFile
	 *		missingVariables
	 *		resetDebugFailed
	 */
	
	switch (strtolower($error)) {
		case	'alreadyapplied':
			$output =	"Could not " . strtolower($_GET['pa']) . " your customization because it has already been applied.";
			break;
		case	'alreadynotapplied':
			$output =	"Could not deactivate your customization because it is not active.";
			break;
		case	'cantremoveapplied':
			$output =	"ERROR: You must deactivate your customization before you can remove it from the list of available customizations.";
			break;
		case	'customizationfailed':
			$output =	"ERROR: Customization failed. This was caused because we were unable to overwrite the file.";
			break;
		case	'generatefilefailed':
			$output =	"ERROR: Failed to make changes to the file holding your customizations. Please try again.";
			break;
		case	'invalidargument':
			$output =	"ERROR: Action failed because an invalid arugment was passed to a function. Make sure everything is correct then try again.";
			break;
		case	'invalidarrayid':
			$output =	"ERROR: Could not complete requested action. Invalid id parameter. Please provide a valid customization ID next time.";
			break;
		case	'invalidarrayindex':
			$output =	"ERROR: Could not complete requested action. Invalid index parameter. Please provide a valid customization index next time.";
			break;
		case	'invalidcustomization':
			$output =	"ERROR: Applying this customization did not change the file or left it entirly blank. File was left as is.";
			break;
		case	'invalidpluginfolder':
			$output =	"ERROR: Tried to apply a customization to a plugin that does not exist.";
			break;
		case	'missingactionfile':
			$output =	"ERROR: Cannot perform actions because the actions file does not exist! Did you delete it or change where it is located?";
			break;
		case	'missingvariables':
			$output =	"ERROR: Some of the parameters required to complete your request are missing. Make sure you have everything you need and try again.";
			break;
		case	'resetdebugfailed':
			$output =	"ERROR: Failed to reset debug files. One or more copy/unlink actions returned false.";
			break;
		default:
			$output =	"An unknown error has occured. Sorry. Error was: " . $error;
			break;
	}//END SWITCH
	
}//END IF

if ($output === '') {
	return;
}//END IF

add_stylesheet('PC_handler_styles', 'handler_styles.css');
add_script('PC_handler_script', 'handler_script.js');
?>
<div id="handlerContainer" class="dn">
	<div id="errorText"><?php echo $output; ?></div>
</div>