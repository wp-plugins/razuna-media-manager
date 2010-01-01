<?php

$_POST['dir'] = urldecode($_POST['dir']);

require_once('../../../wp-load.php');
require_once('razuna.php');
$razuna_api = new RazunaAPI(get_option('razuna_hostname'), get_option('razuna_username'), get_option('razuna_password'), false);

try {
	if($_SESSION['razuna-sessiontoken'] == '') {
		$razuna_api->login();
		$_SESSION['razuna-sessiontoken'] = $razuna_api->get_session_token();
	} else {
		$razuna_api->set_session_token($_SESSION['razuna-sessiontoken']);
	}
	
	$files_array = $razuna_api->list_files($_POST['dir']);
	if(count($files_array) > 0) {
		?><ul class="jqueryFileTree" style="display: none;"><?php
		foreach($files_array as $file) {
			if($file instanceof RazunaFolder) {
				?><li class="directory collapsed"><a href="#" rel="<?php _e($file->get_id()); ?>"><?php _e($file->get_name()); ?></a></li><?php
			} else if($file instanceof RazunaAsset) {
				$link = razuna_plugin_url() . "razuna-file-info.php?id=" . $file->get_id() . "&dir=" . $file->get_folder_id();
				?><li class="file kind_<?php _e($file->get_kind()); ?>"><a href="#" rel="<?php _e($link); ?>"><?php _e($file->get_name()); ?></a></li><?php
			}
		}
		?></ul><?php
	}
	
} catch(RazunaAccessDeniedException $e) {
?>
<form>
	<div id="message" class="error">
		<p><strong>Access Denied, check settings</strong></p>
	</div>
</form>
<?php
}
?>