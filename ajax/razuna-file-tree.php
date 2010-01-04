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

$_POST['dir'] = urldecode($_POST['dir']);

require_once('../../../../wp-load.php');
require_once('../razuna.php');
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
				?><li class="directory collapsed"><a class="asset" href="#" rel="<?php _e($file->get_id()); ?>"><?php _e($file->get_name()); ?></a></li><?php
			} else if($file instanceof RazunaAsset) {
				$link = razuna_plugin_url() . "razuna-file-info.php?id=" . $file->get_id() . "&dir=" . $file->get_folder_id();
				?><li class="file kind_<?php _e($file->get_kind()); ?>">
					<a class="asset" href="#" rel="<?php _e($link); ?>"><?php _e($file->get_name()); ?></a>
					<div class="asset_info" style="display: none;">
						<input type="hidden" class="razuna-file-id" value="<?php _e($file->get_id()); ?>" />
						<input type="hidden" class="razuna-file-kind" value="<?php _e($file->get_kind()); ?>" />
						<input type="hidden" class="razuna-file-thumbnail" value="<?php _e($file->get_thumbnail()); ?>" />
						<input type="hidden" class="razuna-file-original" value="<?php _e($file->get_url()); ?>" />
						<input type="hidden" class="razuna-file-shared" value="<?php _e(($file->is_shared()) ? 't' : 'f'); ?>" />
						<input type="hidden" class="razuna-file-dir" value="<?php _e($file->get_folder_id()); ?>" />
						<table class="describe">
							<tr>
								<?php if($file->get_kind() == 'img') { ?>
								<td>
									<img src="<?php _e($file->get_thumbnail()); ?>" />
								</td>
								<?php } ?>
								<td<?php if($file->get_kind() != 'img') { _e(' colspan="2"'); } ?>>
									<strong><?php _e('ID'); ?></strong> <?php _e($file->get_id()); ?><br />
									<strong><?php _e('File name:'); ?></strong> <?php _e($file->get_name()); ?><br />
									<strong><?php _e('Kind:'); ?></strong> <?php _e($file->get_kind_description()); ?><br />
									<strong><?php _e('Shared:'); ?></strong> <?php _e((($file->is_shared()) ? 'Yes' : 'No')); ?>
								</td>
							</tr>
							<?php if($file->get_kind() == 'img') { ?>
							<tr>
								<td><strong><?php _e('Size:'); ?></strong></td>
								<td>
									<div class="image-size-item">
										<input id="image-size" type="radio" value="thumbnail" class="image-size-thumbnail" name="image-size-<?php _e($file->get_id()); ?>" checked="checked" />
										<label for="image-size">Thumbnail</label>
									</div>
									<div class="image-size-item">
										<input id="image-size" type="radio" value="original" class="image-size-original" name="image-size-<?php _e($file->get_id()); ?>" />
										<label for="image-size">Original</label>
									</div>
								</td>
							</tr>
							<tr>
								<td><strong><?php _e('Alternate text:'); ?></strong></td>
								<td><input type="text" class="alt" /></td>
							</tr>
							<tr>
								<td><strong><?php _e('Link URL:'); ?></strong></td>
								<td>
									<input type="text" class="urlfield text" /><br />
									<button class="button" type="button" onclick="jQuery(this).parent().find('input').val('');">None</button>
									<button class="button" type="button" onclick="jQuery(this).parent().find('input').val('<?php _e($file->get_url()); ?>');">File Original Size</button>
									<button class="button" type="button" onclick="jQuery(this).parent().find('input').val('<?php _e($file->get_thumbnail()); ?>');">File Thumbnail Size</button>
								</td>
							</tr>
							<?php } else { ?>
							<tr>
								<td><strong><?php _e('Link Text'); ?></strong></td>
								<td><input type="text" class="link-text" onkeyup="if(this.value != '') { jQuery(this).parents().find('.asset_info').find('.razuna_link_text_empty_error').removeAttr('style'); }"/></td>
							</tr>
							<?php } ?>
							<tr>
								<td>&nbsp;</td>
								<td>
									<button class="button insert_into_post" type="button" onclick="razuna_insert(this);">Insert into Post</button>
									<span class="razuna_link_text_empty_error" id="razuna_link_text_empty_error-<?php _e($file->get_id()); ?>">The Link Text is empty.</span>
									<span class="razuna_setting_to_shared_message" id="razuna_setting_to_shared_message-<?php _e($file->get_id()); ?>">
										<br />
										<i>
											This asset needs to be shared, do you want to share this asset?
											<a href="#insert" onclick="razuna_share_item(this);" class="razuna_share_answer" id="razuna_share_answer-<?php _e($file->get_id()); ?>">Yes</a>
										</i>
										<span class="razuna_share_loading" id="razuna_share_loading-<?php _e($file->get_id()); ?>"><img src="<?php _e(razuna_plugin_url()); ?>img/spinner.gif" /></span>
										<span class="razuna_share_failed" id="razuna_share_failed-<?php _e($file->get_id()); ?>">Failed</span>
									</span>
								</td>
							</tr>
						</table>
					</div>
				</li><?php
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
} catch(RazunaNotAvailableException $e) {
?>
<form>
	<div id="message" class="error">
		<p><strong>Razuna service is not available at the moment</strong></p>
	</div>
</form>
<?php
}
?>