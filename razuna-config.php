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
			<?php wp_nonce_field('update-options'); ?>
		
			<h3>Connection and Credentials</h3>
		
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="razuna_hostname"><?php _e('Hostname'); ?></label></th>
					<td><input name="razuna_hostname" type="text" id="razuna_hostname" value="<?php echo get_option('razuna_hostname'); ?>" class="regular-text code" /> <span class="description"><?php _e('Example: yourcompany.razuna.com'); ?></span></td>
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
			
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="razuna_hostname,razuna_username,razuna_password" />
			
			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	<?php
}

function razuna_config_css() {
	echo "<style type='text/css'>";
	echo "#icon-razuna { background: transparent url(" . razuna_plugin_url() . "img/razuna-logo-32.png) no-repeat; }";
	echo "</style>";
}
add_action('admin_head', 'razuna_config_css');

?>