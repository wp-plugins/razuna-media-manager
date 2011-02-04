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

require_once('media-tabs/razuna-browser.php');
require_once('media-tabs/razuna-slideshow-builder.php');

add_action('media_buttons_context', 'razuna_media_buttons');
add_action('media_upload_razuna', 'media_upload_razuna');
add_action('admin_head', 'razuna_media_css');
add_action('admin_init', 'razuna_load_js');

function razuna_media_buttons($context) {
	global $post_ID, $temp_ID;
	$dir = dirname(__FILE__);
	$pluginRootURL = get_option('siteurl').substr($dir, strpos($dir, '/wp-content'));
	$image_btn = $pluginRootURL.'/img/razuna-logo-bw-16.png';
	$image_title = 'Razuna';
	
	$uploading_iframe_ID = (int) (0 == $post_ID ? $temp_ID : $post_ID);

	$media_upload_iframe_src = "media-upload.php?post_id=$uploading_iframe_ID";
	$out = ' <a href="'.$media_upload_iframe_src.'&tab=razuna&TB_iframe=true" class="thickbox" title="'.$image_title.'"><img src="'.$image_btn.'" alt="'.$image_title.'" /></a>';
	return $context.$out;
}

function media_upload_razuna() {
	return wp_iframe('media_upload_razuna_form', $errors );
}

function media_upload_razuna_form() {
	if(!isset($_GET['razuna_subtab'])) {
		$_SERVER['QUERY_STRING'] = "razuna_subtab=browser&". $_SERVER['QUERY_STRING'];
		$_GET['razuna_subtab'] = 'browser';
	}
		
	$dir = dirname(__FILE__);
	$pluginRootURL = str_replace('pages', '', get_option('siteurl').substr($dir, strpos($dir, '/wp-content')));
	?>
	<!--script type="text/javascript" src="<?php _e(razuna_plugin_url()); ?>/pages/js/razuna-media-manager.js"></script-->
	<div id="media-upload-header">
		<ul id="sidemenu">
			<li><a href="?<?php _e(preg_replace('/[\\?&]razuna_subtab=([^&#]*)/', 'razuna_subtab=browser', '?'.$_SERVER['QUERY_STRING'])) ?>"<?php if($_GET['razuna_subtab'] == 'browser') { _e('class="current"'); } ?>>Browser</a></li>
			<li><a href="?<?php _e(preg_replace('/[\\?&]razuna_subtab=([^&#]*)/', 'razuna_subtab=slideshowbuilder', '?'.$_SERVER['QUERY_STRING'])) ?>"<?php if($_GET['razuna_subtab'] == 'slideshowbuilder') { _e('class="current"'); } ?>>Slideshow Builder</a></li>
		</ul>
	</div>
	<?php
	if($_GET['razuna_subtab'] == 'browser')
		tabContentBrowser();
	else if($_GET['razuna_subtab'] == 'slideshowbuilder')
		tabContentSlideshowBuilder();
}

function razuna_media_css() {
	echo "
	<style type=\"text/css\">
		.clearer { clear: both; height: 1px; }
		ul.razunaMediaBrowser { padding: 0px; margin: 0px; }
		ul.razunaMediaBrowser li {
			list-style: none;
			padding: 0px;
			padding-left: 20px;
			margin: 0px;
			white-space: nowrap;
		}

		ul.razunaMediaBrowser a.asset {
			color: #333;
			text-decoration: none;
			display: block;
			padding: 0px 2px;
		}

		ul.razunaMediaBrowser a.asset:hover {
			background: #BDF;
		}

		/* Core Styles */
		.razunaMediaBrowser li.directory { background: url(" . razuna_plugin_url() . "pages/img/directory.png) left top no-repeat; }
		.razunaMediaBrowser li.expanded { background: url(" . razuna_plugin_url() . "pages/img/folder_open.png) left top no-repeat; }
		.razunaMediaBrowser li.file { background: url(" . razuna_plugin_url() . "pages/img/file.png) left top no-repeat; }
		.razunaMediaBrowser li.wait, .razuna_media_navigation .wait { background: url(" . razuna_plugin_url() . "pages/img/spinner.gif) left top no-repeat; }
		/* File Extensions*/
		.razunaMediaBrowser li.kind_vid { background: url(" . razuna_plugin_url() . "pages/img/film.png) left top no-repeat; }
		.razunaMediaBrowser li.kind_img { background: url(" . razuna_plugin_url() . "pages/img/picture.png) left top no-repeat; }
		.razunaMediaBrowser li.kind_doc { background: url(" . razuna_plugin_url() . "pages/img/doc.png) left top no-repeat; }
		.razunaMediaBrowser li.kind_aud { background: url(" . razuna_plugin_url() . "pages/img/aud.png) left top no-repeat; }
		
		html { background-color: #FFF; }
		form { width: 98%; }
		#razuna_media_wrapper {
			width: 640px;
			top: 0;
			position: relative;
		}
		.asset_info, .asset_info table { width: 100%; }
		.asset_info table { border: 1px solid #DFDFDF; }
		.asset_info td { vertical-align: top; }
		.asset_info .image-size-item { width: 50%; float: left; }
		.describe input[type=\"text\"] { width: 100%; }
		.razuna_link_text_empty_error { color: red; display: none; font-size: 80%; }
		.razuna_setting_to_shared_message { font-size: 80%; display: none; }
		.razuna_share_loading { display: none; }
		.razuna_share_failed { display: none; color: red; padding-left: 4px; }
		.razuna_share_answer { display: inline; }
		.razuna_media_navigation { text-align: right; float: right; line-height: 16px; }
		.razuna_media_navigation .wait { padding-left: 16px; height: 16px; text-decoration: none; }
		#razuna_upload_fields { float: left; }
		#razuna_media_wrapper_upload {
			display: none;
			position: absolute;
			bottom: 2px;
			padding-bottom: 5px;
			width: 100%;
			background-color: #F5F5F5;
		}
		#razuna_slideshow_builder {
			width: 100%;
			background-color: #F5F5F5;
			padding: 2px;
			margin-bottom: 10px;
			min-height: 120px;
		}
		#file_browser { width: 570px; }
		.error, .updated { margin-left: 0; }
		
		#sortable_images { width: 100%; display: block; }
		#sortable_images .item { float: left; padding-right: 10px; }
		#razuna_slideshow_delay { text-align: right; }
	</style>";
}

function razuna_load_js() {
	wp_enqueue_script('razuna-media-manager', razuna_plugin_url()."pages/js/razuna-media-manager.js");
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-sortable');
}

?>