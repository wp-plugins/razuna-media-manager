<?php

/*
Plugin Name: Razuna Media Manager
Plugin URI: http://chdorner.com/projects/2010/razuna-media-manager.html
Description: Allows to add Files from your Razuna account into Wordpress posts.
Version: 0.5.1
Author: Christof Dorner
Author URI: http://chdorner.com

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

require_once('RazunaAPI.class.php');
require_once('razuna-config.php');
require_once('razuna-media.php');

function razuna_init() {
	add_action('admin_menu', 'razuna_config_page');
	add_action('admin_init', 'razuna_start_session');
	add_action('admin_init', 'razuna_register_settings');
}
add_action('init', 'razuna_init');

function razuna_config_page() {
	if(function_exists('add_submenu_page')) {
		add_submenu_page('options-general.php', __('Razuna'), __('Razuna'), 'manage_options', 'razuna-config', 'razuna_config');
	}
}

function razuna_register_settings() {
	register_setting('razuna','razuna_hostname');
	register_setting('razuna', 'razuna_username');
	register_setting('razuna', 'razuna_password');
}

function razuna_start_session() {
	if (!session_id()) {
		session_start();
	}
}

function razuna_plugin_url() {
	return get_option('siteurl') . "/wp-content/plugins/razuna-media-manager/";
}

?>