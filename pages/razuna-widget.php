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

class RazunaWidget extends WP_Widget {
	function RazunaWidget() {
		parent::WP_Widget(false, 'Razuna Asset', array('description' => 'Add an asset from your Razuna service'));
	}
	
	function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$text = apply_filters('widget_text', $instance['text'], $instance);
		
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		echo $text . $after_widget;
	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['text'] =  $new_instance['text'];
		return $instance;
	}
	
	function form($instance) {
		$title = esc_attr($instance['title']);
		$text = esc_attr($instance['text']);
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
		<a onclick="tb_show('Razuna','media-upload.php?tab=razuna&widgetMode=true&widgetTextareaId=<?php echo $this->get_field_id('text'); ?>&TB_iframe=true&width=640',null);return false;" href="#" title="Razuna" style="text-decoration: none;"><img src="<?php _e(razuna_plugin_url() . 'pages/img/razuna-logo-bw-16.png'); ?>" alt="Razuna" /> Browse</a> | <a href="#" style="text-decoration: none;" onclick="jQuery('#<?php echo $this->get_field_id('text'); ?>').val('');">Clear</a><br />
		<textarea class="widefat" rows="6" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
		<?php
	}
}
?>