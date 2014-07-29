<?php
//errorHandlers.php
//include this file when you want to handle some error codes ($this->status will be 0 when an error occurs)

if (! defined('PC_VERSION') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}//END IF


$status =	$this->status;
$pa =	(isset($_POST['pa']) ? $_POST['pa'] : (isset($_GET['pa']) ? $_GET['pa'] : ''));
$id =	intval(isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? $_GET['id'] : 0));
$index =	intval(isset($_POST['index']) ? $_POST['index'] : (isset($_GET['index']) ? $_GET['index'] : 0));

//if we do not have an error, just dont bother with the rest of the script
if ($status !== 1 || $pa === '') {
	return;
}//END IF

$output =	'';

/*
 *	Error List:
 *		alreadyApplied
 *		alreadyNotApplied
 *		cantRemoveApplied
 *		generateFileFailed
 *		invalidArrayID
 *		invalidArrayIndex
 *		invalidPluginFolder
 *		missingActionFile
 *		missingVariables
 */

switch (strtolower($pa)) {
	case	'newcustom':
		$output =	"Successfully created your customization. However, it still needs to be activated to use it.";
		break;
	case	'apply':
		$output =	"Successfully applied customization: " . $infoArray[$id][$index]['CustomName'];
		break;
	case	'reapply':
		$output =	"Successfully reapplied customization: " . $infoArray[$id][$index]['CustomName'];
		break;
	case	'remove':
		$output =	"Successfully removed customization";
		break;
	case	'unapply':
		$output =	"Successfully unapplied customization: " . $infoArray[$id][$index]['CustomName'];
		break;
	case	'resetdebugfiles':
		$output =	"Successfully reset the debug files.";
		break;
	default:
		$output =	"We dont know what just succeeded but it did. But should it have?";
		break;
}//END SWITCH

if ($this->error !== '') {
	
	switch (strtolower($this->error)) {
		case	'actionlogfailed':
			$output .=	"<br />Warning: Failed to create a log entry for this action.";
			break;
	}//END SWITCH
	
}//END IF

if ($output === '') {
	return;
}//END IF

pc_add_stylesheet('PC_handler_styles', 'handler_styles.css');
pc_add_script('PC_handler_script', 'handler_script.js');
?>
<div id="handlerContainer" class="dn">
	<div id="successText"><?php echo $output; ?></div>
</div>