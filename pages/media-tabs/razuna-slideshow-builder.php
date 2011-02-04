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

function tabContentSlideshowBuilder() {
	?>
	<div id="razuna_media_wrapper">
		<form>
			<div id="razuna_slideshow_builder">
				<small><i>Drag and drop for reordering or add images from the browser below.</i></small>
				<div class="clearer">&nbsp;</div>
				<ul id="sortable_images"></ul>
			</div>
			<div class="razuna_media_navigation">
				Delay:
				<input type="text" size="2" id="razuna_slideshow_delay" value="2" />s
				<button class="button" type="button" onclick="jQuery(this).razunaSlideShowBuilderInsert({ baseUrl: '<?php _e(razuna_plugin_url()) ?>', widgetMode: <?php echo (isset($_GET['widgetMode'])) ? $_GET['widgetMode'].", widgetTextareaId: '".$_GET['widgetTextareaId']."'" : "false" ?> });">Insert into Post</button>
			</div>
			<p class="clearer">&nbsp;</p>
			<div class="razuna_media_navigation">
				<a href="#" onclick="init();"><img src="<?php _e(razuna_plugin_url()) ?>pages/img/refresh.png" alt="Refresh" /></a>
			</div>
			<div id="file_browser"></div>
			<div class="clearer">&nbsp;</div>
		</form>
	</div>
	<script type="text/javascript">
		jQuery(document).ready( function() {
			init();
		});
		
		function init() {
			jQuery('#file_browser').razunaInit({
				baseUrl: '<?php _e(razuna_plugin_url()); ?>',
				tab: 'slideshowbuilder',
				<?php if($_GET['widgetMode'] == 'true') { ?>
					widgetMode: true,
					widgetTextareaId: '<?php _e($_GET['widgetTextareaId']); ?>'
				<?php } ?>
			});
			
			jQuery("#sortable_images").sortable({
				axis: 'x'
			});
		}
	</script>
	<?php
}
?>