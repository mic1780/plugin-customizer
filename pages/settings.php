<?php

if (! defined('PC_VERSION') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}//END IF

global $nL;

//all plugins w/ info arrays: $GLOBALS['wp_object_cache']->cache['plugins']['plugins']['']
//this is so I can dev without worry of others looking in
if (PC_DEBUG_MODE && current_user_can('edit_plugins') === false ) {
	wp_redirect( admin_url('admin.php') );
	exit;
}//END IF

?>
<div id="pc-admin">
	<?php require(PC_PLUGIN_ERROR_HANDLERS); ?>
	<?php require(PC_PLUGIN_SUCCESS_HANDLERS); ?>
	<h1>
		<?php echo get_admin_page_title(); ?><?php if (PC_DEBUG_MODE) echo ' (DEBUG MODE)'; ?>
	</h1>
	<hr>
	<h2>
		File Locations
	</h2>
	<h4>
		Note: Debug mode uses different locations so changing some values will only effect live or debug mode.
	</h4>
	<table class="padCells">
		<thead>
			<tr>
				<th>File Type</th>
				<th>Location</th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Customizer File</td>
				<td><?php echo str_replace(PC_PLUGIN_DIR, "", PC_PLUGIN_ARRAY_FILE); ?></td>
				<td>This file holds all of your plugin customizations.</td>
			</tr>
			<tr>
				<td>Log File</td>
				<td><?php echo str_replace(PC_PLUGIN_DIR, "", PC_PLUGIN_LOG_FILE); ?></td>
				<td>This file contains previous actions made by using this plugin.</td>
			</tr>
			<tr>
				<td>Error Handlers</td>
				<td><?php echo str_replace(PC_PLUGIN_DIR, "", PC_PLUGIN_ERROR_HANDLERS); ?></td>
				<td>This file is used to report errors based on error code.</td>
			</tr>
			<tr>
				<td>Success Handlers</td>
				<td><?php echo str_replace(PC_PLUGIN_DIR, "", PC_PLUGIN_SUCCESS_HANDLERS); ?></td>
				<td>This file is used to report when an action was successful.</td>
			</tr>
		</tbody>
	</table>
</div>