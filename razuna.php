<?php
/*
Plugin Name: Razuna Media Manager
Plugin URI: http://razuna.org
Description: Allows to add Files from your Razuna account into Wordpress posts.
Version: 0.0.1
Author: Christof Dorner
Author URI: http://chdorner.com
*/

require_once('RazunaAPI.class.php');
require_once('razuna-config.php');
require_once('razuna-media.php');

function razuna_init() {
	add_action('admin_menu', 'razuna_config_page');
	add_action('admin_init', 'razuna_start_session');
}
add_action('init', 'razuna_init');

function razuna_config_page() {
	if(function_exists('add_submenu_page')) {
		add_submenu_page('options-general.php', __('Razuna'), __('Razuna'), 'manage_options', 'razuna-config', 'razuna_config');
	}
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