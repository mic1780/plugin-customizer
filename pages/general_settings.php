<?php

if (! defined('PC_VERSION') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}//END IF

//all plugins w/ info arrays: $GLOBALS['wp_object_cache']->cache['plugins']['plugins']['']
//this is so I can dev without worry of others looking in
if (! isset($GLOBALS['wp_object_cache']->cache['userslugs']['mic1780']) ) {
	wp_redirect( admin_url('admin.php') );
	exit;
}//END IF

add_script('PC_general_settings', 'general_settings.js');

$pluginsArray =	get_plugins();
/**/
$plugins =	array();
if ( isset($pluginsArray) && count($pluginsArray) > 0 ) {
	foreach ($pluginsArray as $plugin_path => $plugin_data) {
		list($plugin_folder, $plugin_file) =	explode('/', $plugin_path);
		$plugins[$plugin_folder] =	$plugin_data['Version'];
	}//END FOREACH LOOP
}//END IF
/**/

$customizeRows =	'';

/**
if (! file_exists(PC_PLUGIN_ARRAY_FILE) ) {
	$fname =	explode('/', PC_PLUGIN_ARRAY_FILE);
	$fname =	$fname[count($fname) - 1];
	//$fp =		fopen(PC_PLUGIN_ARRAY_FILE, 'w');
	$txt =	'' .
				"<?php\n" . $nL .
				"//" . $fname . "\n" . $nL .
				"//This file is generated and should not be changed by you. Thanks!\n" . $nL .
				'$infoArray[0][] =	array(' . "\n" . $nL .
				"'Applied' =>		true,\n" . $nL .
				"'CustomName' =>	'MC4WP widget title',\n" . $nL .
				"'FilePath' =>		'mailchimp-for-wp-pro/includes/',\n" . $nL .
				"'FileName' =>		'class-widget.php',\n" . $nL .
				'\'OldCode\' =>		\'' . str_replace("'", "\'", '$title = apply_filters( \'widget_title\', $instance[\'title\'] );') . '\',' . "\n" . $nL .
				'\'NewCode\' =>		\'' . str_replace("'", "\'", '$title = (stripos($instance[\'title\'], \'<img\') === false ? apply_filters( \'widget_title\', $instance[\'title\'] ) : $instance[\'title\']);') . '\',' . "\n" . $nL .
				"'Description' =>	'This allows Mailchimp to use image tags in widget titles.',\n" . $nL .
				");\n" . $nL .
				"?>" .
				'';
	//fwrite($fp, $txt);
	//fclose($fp);
	unset($txt);
}//END IF
/**/

if (file_exists(PC_PLUGIN_ARRAY_FILE)) {
	require(PC_PLUGIN_ARRAY_FILE);
}//END IF

if (isset($infoArray) && count($infoArray) > 0) {
	add_stylesheet('PC_settings_styles', 'settings_styles.css');
	foreach ($infoArray as $key => $changeArray) {
		foreach ($changeArray as $index => $row) {
			$plugin_version =	$plugins[reset( explode('/', $row['FilePath']) )];
			//create rows for table
			$customizeRows .=	'' .
									'<tr>' . $nL .
										'<td class="vt l selectCell">' . $nL .
											$key . $nL .
											'<input type="hidden" name="ac_o['.$key.']['.$index.'][ID]" value="'.$key.'" />' . $nL .
											'<input type="hidden" name="ac_o['.$key.']['.$index.'][Index]" value="'.$index.'" />' . $nL .
										'</td>' . $nL .
										'<td class="vt c">' . $nL .
											($row['Applied'] === false ? '<a href="' . $this->page . '&pa=apply&id='.$key.'&index='.$index . '">Activate</a><br />' . $nL : '') .
											($row['Applied'] === false ? '<a href="' . $this->page . '&pa=remove&id='.$key.'&index='.$index . '">Remove</a><br />' . $nL : '') .
											
											($row['Applied'] === true ? '<a href="' . $this->page . '&pa=unapply&id='.$key.'&index='.$index . '">Deactivate</a><br />' . $nL : '') .
											($row['Applied'] === true && strcmp($row['Version'], $plugin_version) < 0 ?
												'<a href="' . $this->page . '&pa=reapply&id='.$key.'&index='.$index . '">Reapply</a><br />' . $nL : '') .
											
											//'<input type="checkbox" id="" name="ac_i['.$key.']['.$index.'][Applied]" placeholder="" value="1"' . ($row['Applied'] == true ? ' checked="checked"' : '') . ' />' . $nL .
										'</td>' . $nL .
										'<td class="vt l textCell">' . $nL .
											$row['CustomName'] . $nL .
										'</td>' . $nL .
										'<td class="vt l textCell">' . $nL .
											$row['FileName'] . $nL .
											'<input type="hidden" name="ac_o['.$key.']['.$index.'][FileName]" value="'.$row['FileName'].'" />' . $nL .
											'<input type="hidden" name="ac_o['.$key.']['.$index.'][FilePath]" value="'.$row['FilePath'].'" />' . $nL .
										'</td>' . $nL .
										'<td class="vt l textareaCell">' . $nL .
											'<pre>' . $nL .
												'<textarea cols="35" rows="2" readonly>' . htmlspecialchars(pc_format_code($row['OldCode'], 'read')) . '</textarea>' . $nL .
											'</pre>' . $nL .
										'</td>' . $nL .
										'<td class="vt l textareaCell">' . $nL .
											'<pre>' . $nL .
												'<textarea cols="35" rows="2" readonly>' . htmlspecialchars(pc_format_code($row['NewCode'], 'read')) . '</textarea>' . $nL .
											'</pre>' . $nL .
										'</td>' . $nL .
										'<td class="vt l maxTextareaCell">' . $nL .
											htmlspecialchars(pc_format_code($row['Description'], 'read')) . $nL .
											($row['Applied'] === true && strcmp($row['Version'], $plugin_version) < 0 ?
												'<br /><br />' .
												'NOTICE: This customization is active but needs to be reapplied.<br />' .
												'Applied version: ' . $row['Version'] . '<br />' .
												'Plugin version: ' . $plugin_version .
												'' : '') .
										'</td>' . $nL .
									'</tr>' . $nL .
									'';
		}//END FOREACH LOOP
	}//END FOREACH LOOP
}//END IF

//echo '<pre>' . print_r($GLOBALS['menu'], true) . '</pre>';
?>
<div id="pc-admin">
	<?php require(PC_PLUGIN_ERROR_HANDLERS); ?>
	<?php require(PC_PLUGIN_SUCCESS_HANDLERS); ?>
	<h1>
		Plugin Customizer
	</h1>
	<hr>
	<h2>
		Available Customizations
	</h2>
	<table class="padCells">
		<thead>
			<tr>
				<th>ID</th>
				<th>Actions</th>
				<th>Custom Name</th>
				<th>File Name</th>
				<th>Original Code</th>
				<th>Custom Code</th>
				<th>Description</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $customizeRows; ?>
		</tbody>
	</table>
	<hr>
	<h2>
		Add New Customization
	</h2>
	<form action="<?php echo admin_url('admin.php?page=' . $_GET['page']); ?>" method="post">
		<table class="ct padCells">
			<thead>
				<tr>
					<th>ID</th>
					<th>Custom Name</th>
					<th>Path To File</th>
					<th>File Name</th>
					<th>Original Code</th>
					<th>Custom Code</th>
					<th>Description</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="vt">
						<select name="newCustom[ID]" style="width: 50px;">
							<?php echo getIDOptions(); ?>
						</select>
					</td>
					<td class="vt">
						<input type="text" name="newCustom[CustomName]" value="" />
					</td>
					<td class="vt">
						<input type="text" name="newCustom[FilePath]" placeholder="[plugin folder]/path/" value="" />
					</td>
					<td class="vt">
						<input type="text" name="newCustom[FileName]" value="" />
					</td>
					<td class="vt">
						<textarea name="newCustom[OldCode]" placeholder="MUST BE EXACT!" cols="35" rows="10"></textarea>
					</td>
					<td class="vt">
						<textarea name="newCustom[NewCode]" placeholder="MUST BE EXACT!" cols="35" rows="10"></textarea>
					</td>
					<td class="vt">
						<textarea name="newCustom[Description]" cols="35" rows="10"></textarea>
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td class="vt c" colspan="100%">
						<input type="hidden" name="pa" value="newCustom" />
						<input type="submit" name="submit" value="Create Customization" />
						<input type="button" id="newCustomReset" value="Reset Form" />
					</td>
				</tr>
			</tfoot>
		</table>
	</form>
</div>