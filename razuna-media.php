<?php

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
	$out = ' <a href="'.$media_upload_iframe_src.'&tab=razuna&TB_iframe=true&height=496&width=640" class="thickbox" title="'.$image_title.'"><img src="'.$image_btn.'" alt="'.$image_title.'" /></a>';
	return $context.$out;
}

function media_upload_razuna() {
	return wp_iframe('media_upload_razuna_form', $errors );
}

function media_upload_razuna_form() {
	$razuna_api = new RazunaAPI(get_option('razuna_hostname'), get_option('razuna_username'), get_option('razuna_password'), false);

	try {
		if($_SESSION['razuna-sessiontoken'] == '') {
			$razuna_api->login();
			$_SESSION['razuna-sessiontoken'] = $razuna_api->get_session_token();
		} else {
			$razuna_api->set_session_token($_SESSION['razuna-sessiontoken']);
		}
	?>
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
	        script: '<?php _e(razuna_plugin_url()); ?>razuna-file-tree.php',
			loadMessage: 'Logging into the Razuna service...',
	    });
	});
	</script>
	<?php
	} catch(RazunaAccessDeniedException $e) {
	?>
	<form>
		<div id="message" class="error">
			<p><strong>Access Denied, check settings</strong></p>
		</div>
	</form>
	<?php
	}
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

		UL.jqueryFileTree A {
			color: #333;
			text-decoration: none;
			display: block;
			padding: 0px 2px;
		}

		UL.jqueryFileTree A:hover {
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
		
		#razuna_file_info {
			position: absolute;
			bottom: 0;
			height: 150px;
			display: none;
		}
	</style>";
}

?>