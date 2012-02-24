<?php

/*
Copyright (C) 2012 Razuna APS

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

add_action('media_buttons_context', 'razuna_media_buttons');
add_action('media_upload_razuna', 'media_upload_razuna');
add_action('admin_head', 'razuna_media_css');

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
	$dir = dirname(__FILE__);
	$pluginRootURL = str_replace('pages', '', get_option('siteurl').substr($dir, strpos($dir, '/wp-content')));
	?>
	<div id="razuna_media_wrapper">
		<form>
			<div class="razuna_media_navigation">
				<a href="#" id="razuna_upload_link" onclick="jQuery(this).razunaOpenUploadDiv({baseUrl: '<?php _e($pluginRootURL); ?>'});">Upload</a>
				<a href="#" onclick="init();">Refresh</a>
			</div>
			<div id="file_browser"></div>
			<div class="clearer">&nbsp;</div>
		</form>
	</div>
	<div id="razuna_media_wrapper_upload">
		<form action="" name="up" method="post" enctype="multipart/form-data" id="razuna_uploader_form">
			<div class="razuna_media_navigation">
				<a href="#" id="razuna_upload_link" onclick="jQuery(this).razunaCloseUploadDiv();" style="padding-right:15px;">Close</a>
			</div>
			<input type="hidden" name="fa" value="c.apiupload" />
			<input type="hidden" name="sessiontoken" id="razuna_upload_sessiontoken" />
			<input type="hidden" name="redirectto" id="razuna_upload_redirecturl" />
			<input type="file" id="filedata" name="filedata" />
			into folder
			<select name="destfolderid" id="razuna_upload_folders"></select>
			<input type="submit" class="button" value="Upload" id="razuna_upload_button">
		</form>
	</div>
	<script type="text/javascript" src="<?php _e(razuna_plugin_url()); ?>/pages/js/razuna-media-manager.js"></script>
	<script type="text/javascript">
		jQuery(document).ready( function() {
			init();
		});
		
		function init() {
			jQuery('#file_browser').razunaInit({
				baseUrl: '<?php _e(razuna_plugin_url()); ?>',
				<?php if($_GET['widgetMode'] == 'true') { ?>
					widgetMode: true,
					widgetTextareaId: '<?php _e($_GET['widgetTextareaId']); ?>'
				<?php } ?>
			});
		}
	</script>
	<?php
}

function razuna_media_css() {
	echo "
	<style type=\"text/css\">
		.clearer { clear: both; }
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
		
		html, body { background-color: #FFF; height: 100%; width: 100%; }
		body { margin: 0px; padding: 0px; }
		form { width: 98%; }
		#razuna_media_wrapper {
			width: 640px;
			top: 0;
			position: absolute;
			margin: 0;
			padding: 0;
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
		.razuna_share_answer {
			display: inline;
		}
		
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

?>