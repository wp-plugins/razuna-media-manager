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

function media_upload_razuna_form() {?>
	<div id="razuna_media_wrapper">
		<form>
			<div id="file_browser"></div>
		</form>
	</div>
	<div id="razuna_file_info"></div>
	<script type="text/javascript" src="<?php _e(razuna_plugin_url()); ?>razuna_media_manager.js"></script>
	<script type="text/javascript">
		jQuery(document).ready( function() {
		    jQuery('#file_browser').fileTree({
		        script: '<?php _e(razuna_plugin_url()); ?>ajax/razuna-file-tree.php',
				loadMessage: 'Logging into the Razuna service...',
				collapseSpeed: 500,
		    });
		});
	</script>
	<?php
}

function razuna_media_css() {
	echo "
	<style type=\"text/css\">
		UL.jqueryFileTree { padding: 0px; margin: 0px; }
		UL.jqueryFileTree LI {
			list-style: none;
			padding: 0px;
			padding-left: 20px;
			margin: 0px;
			white-space: nowrap;
		}

		UL.jqueryFileTree A.asset {
			color: #333;
			text-decoration: none;
			display: block;
			padding: 0px 2px;
		}

		UL.jqueryFileTree A.asset:hover {
			background: #BDF;
		}

		/* Core Styles */
		.jqueryFileTree LI.directory { background: url(" . razuna_plugin_url() . "img/directory.png) left top no-repeat; }
		.jqueryFileTree LI.expanded { background: url(" . razuna_plugin_url() . "img/folder_open.png) left top no-repeat; }
		.jqueryFileTree LI.file { background: url(" . razuna_plugin_url() . "img/file.png) left top no-repeat; }
		.jqueryFileTree LI.wait { background: url(" . razuna_plugin_url() . "img/spinner.gif) left top no-repeat; }
		/* File Extensions*/
		.jqueryFileTree LI.kind_vid { background: url(" . razuna_plugin_url() . "img/film.png) left top no-repeat; }
		.jqueryFileTree LI.kind_img { background: url(" . razuna_plugin_url() . "img/picture.png) left top no-repeat; }
		.jqueryFileTree LI.kind_doc { background: url(" . razuna_plugin_url() . "img/doc.png) left top no-repeat; }
		
		#razuna_media_wrapper {
			width: 640px;
			top: 0;
			position: absolute;
		}
		.asset_info, .asset_info table { width: 100%; }
		.asset_info table { border: 1px solid #DFDFDF; }
		.asset_info td { vertical-align: top; }
		.asset_info .image-size-item { width: 30%; float: left; }
		.describe input[type=\"text\"] { width: 100%; }
		.razuna_link_text_empty_error { color: red; display: none; font-size: 80%; }
		.razuna_setting_to_shared_message { font-size: 80%; display: none; }
		.razuna_share_loading { display: none; }
		.razuna_share_answer {
			display: inline;
		}
	</style>";
}

?>