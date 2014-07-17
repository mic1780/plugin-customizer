<?php

if (! defined('PC_VERSION') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}//END IF

if (! PC_DEBUG_MODE)
	return;
?>
<span> (DEBUG MODE) </span>
<a href="<?php echo $this->page; ?>&pa=resetDebugFiles">Reset Debugging Files</a>