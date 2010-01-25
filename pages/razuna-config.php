<?php

/*
Copyright (C) 2010 Christof Dorner

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

function razuna_config() {
	if($_GET['updated'] == true) {
		$_SESSION['razuna-sessiontoken'] = '';
	}
	?>
	<div class="wrap">
		<div id="icon-razuna" class="icon32"><br /></div>
		<h2>Razuna Configuration</h2>
		
		<form method="post" action="options.php">
			<?php settings_fields('razuna'); ?>
			<?php do_settings_sections('razuna'); ?>
		
			<h3>Connection and Credentials</h3>
		
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="razuna_servertype"><?php _e('Server Type'); ?></label></th>
					<td>
						<input id="razuna_servertype_hosted" type="radio" value="hosted" onchange="checkServerType();" name="razuna_servertype"<?php if(get_option('razuna_servertype') == 'hosted') { _e('checked="checked"'); } ?> />
						<label for="razuna_servertype"> Hosted (razuna.com)</label>
						<input id="razuna_servertype_self" type="radio" value="self" onchange="checkServerType();" name="razuna_servertype"<?php if(get_option('razuna_servertype') == 'self') { _e('checked="checked"'); } ?> />
						<label for="razuna_servertype"> Self hosted</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="razuna_hostname"><?php _e('Hostname'); ?></label></th>
					<td><input name="razuna_hostname" type="text" id="razuna_hostname" value="<?php echo get_option('razuna_hostname'); ?>" class="regular-text code" /> <span class="description"><?php _e('Example: yourcompany.razuna.com or localhost:8080/razuna'); ?></span></td>
				</tr>
				<tr valign="top" class="fields_self" style="display: none;">
					<th scope="row"><label for="razuna_hostid"><?php _e('Host ID'); ?></label></th>
					<td><input name="razuna_hostid" type="text" id="razuna_hostid" value="<?php echo get_option('razuna_hostid'); ?>" class="regular-text code" /> <span class="description"><?php _e('Example: 496'); ?></span></td>
				</tr>
				<tr valign="top" class="fields_self" style="display: none;">
					<th scope="row"><label for="razuna_hostid"><?php _e('DAM Path'); ?></label></th>
					<td><input name="razuna_dampath" type="text" id="razuna_dampath" value="<?php echo get_option('razuna_dampath'); ?>" class="regular-text code" /> <span class="description"><?php _e('Example: /demo/dam'); ?></span></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="razuna_username"><?php _e('Username'); ?></label></th>
					<td><input name="razuna_username" type="text" id="razuna_username" value="<?php echo get_option('razuna_username'); ?>" class="regular-text code" /></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="razuna_password"><?php _e('Password'); ?></label></th>
					<td><input name="razuna_password" type="password" id="razuna_password" value="<?php echo get_option('razuna_password'); ?>" class="regular-text code" /></td>
				</tr>
			</table>
			<script type="text/javascript">
				function checkServerType() {
					if(jQuery("#razuna_servertype_self").is(":checked")) {
						jQuery(".fields_self").show();
					} else {
						jQuery(".fields_self").hide();
					}
				}
				checkServerType();
			</script>
			
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="razuna_hostname,razuna_hostid,razuna_username,razuna_password" />
			
			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
				<input type="button" name="test" class="button" id="razuna-test-configuration-btn" value="<?php esc_attr_e('Test Configuration') ?>" />
			</p>
		</form>
		<script type="text/javascript" src="<?php _e(razuna_plugin_url()); ?>/pages/js/razuna-media-manager.js"></script>
		<script type="text/javascript">
			jQuery(document).ready( function() {
				jQuery('#razuna-test-configuration-btn').razunaTestConfiguration({ baseUrl: '<?php _e(razuna_plugin_url()); ?>' });
			});
		</script>
	</div>
	<?php
}

function razuna_config_css() {
	echo "<style type='text/css'>";
	echo "#icon-razuna { background: transparent url(" . razuna_plugin_url() . "img/razuna-logo-32.png) no-repeat; }";
	echo ".wait { background: url(" . razuna_plugin_url() . "pages/img/spinner.gif) left top no-repeat; padding-left: 16px; height: 16px; text-decoration: none; }";
	echo "#razuna_message, #razuna_message p { display: inline; padding: 2px; }";
	echo "#razuna_message.success { background-color: lightgreen; -moz-border-radius: 3px; border: 1px solid green; }";
	echo "</style>";
}
add_action('admin_head', 'razuna_config_css');

?>