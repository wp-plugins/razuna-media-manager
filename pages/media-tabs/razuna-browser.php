<?php

/*
Plugin Name: Razuna Media Manager
Plugin URI: http://chdorner.com/projects/2010/razuna-media-manager.html
Description: Allows to add Files from your Razuna account into Wordpress posts.
Version: 0.7.0
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

function tabContentBrowser() {
	?>
	<div id="razuna_media_wrapper">
		<form>
			<div class="razuna_media_navigation">
				<a href="#" id="razuna_upload_link" onclick="jQuery(this).razunaOpenUploadDiv({baseUrl: '<?php _e($pluginRootURL); ?>'});">Upload</a>
				<a href="#" onclick="init();"><img src="<?php _e(razuna_plugin_url()) ?>pages/img/refresh.png" alt="Refresh" /></a>
			</div>
			<div id="file_browser"></div>
			<div class="clearer">&nbsp;</div>
		</form>
	</div>
	<div id="razuna_media_wrapper_upload">
		<form action="" name="up" method="post" enctype="multipart/form-data" id="razuna_uploader_form">
			<div class="razuna_media_navigation">
				<a href="#" id="razuna_upload_link" onclick="jQuery(this).razunaCloseUploadDiv();">Close</a>
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
	<?php
}
?>