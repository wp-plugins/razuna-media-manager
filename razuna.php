<?php

/*
Plugin Name: Razuna Media Manager
Plugin URI: http://chdorner.com/projects/2010/razuna-media-manager.html
Description: Allows to add Files from your Razuna account into Wordpress posts.
Version: 0.6.0
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

require_once('util/razuna-functions.php');
require_once('api/Razuna.class.php');
require_once('pages/razuna-config.php');
require_once('pages/razuna-media.php');
require_once('pages/razuna-widget.php');

add_action('init', 'razuna_admin_init');
add_filter('the_content','razuna_player_content');

add_action('widgets_init', create_function('', 'return register_widget("RazunaWidget");'));

function razuna_admin_init() {
	add_action('admin_menu', 'razuna_admin_config_page');
	add_action('admin_init', 'razuna_admin_start_session');
	add_action('admin_init', 'razuna_admin_register_settings');
}

function razuna_admin_config_page() {
	if(function_exists('add_submenu_page')) {
		add_submenu_page('options-general.php', __('Razuna'), __('Razuna'), 'manage_options', 'razuna-config', 'razuna_config');
	}
}

function razuna_admin_register_settings() {
	register_setting('razuna','razuna_hostname');
	register_setting('razuna', 'razuna_username');
	register_setting('razuna', 'razuna_password');
}

function razuna_admin_start_session() {
	if (!session_id()) {
		session_start();
	}
}

function razuna_player_content($content) {
	$regex = '/\[RAZUNA_PLAYER=([a-z0-9\:\.\-\&\_\/\|]+)\,([0-9]+)\,([a-z]+)\,([0-9]+)\,([0-9]+)\]/i';
	$matches = array();

	preg_match_all($regex, $content, $matches);
	
	if($matches[0][0] != '') {
		foreach($matches[0] as $key => $data) {
			$url = $matches[1][$key];
			$id = $matches[2][$key];
			$type = $matches[3][$key];
			$width = $matches[4][$key];
			$height = $matches[5][$key];
			
			$replace = "<a href=\"$url\" style=\"display:block;width:".$width."px;height:".$height."px;\" id=\"razuna_asset_".$id."\"></a>";
			if($type == 'vid')
				$replace .= "<script type=\"text/javascript\">flowplayer(\"razuna_asset_".$id."\", \"". razuna_plugin_url() ."pages/swf/flowplayer-3.1.5.swf\",{ clip: { autoPlay: false } });</script>";
			else
				$replace .= "<script type=\"text/javascript\">flowplayer(\"razuna_asset_".$id."\", \"". razuna_plugin_url() ."pages/swf/flowplayer-3.1.5.swf\",{ plugins: { controls: { fullscreen: false, height: 30 } }, clip: { autoPlay: false } });</script>";
				
			$content = str_replace($matches[0][$key], $replace, $content);
		}	
	}
	return $content;
}

function razuna_plugin_url() {
	return get_option('siteurl') . "/wp-content/plugins/razuna-media-manager/";
}

?>