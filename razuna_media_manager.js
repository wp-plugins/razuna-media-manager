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

if(jQuery) (function($){
	
	$.extend($.fn, {
		fileTree: function(o, h) {
			// Defaults
			if( !o ) var o = {};
			if( o.root == undefined ) o.root = '0';
			if( o.script == undefined ) o.script = null;
			if( o.folderEvent == undefined ) o.folderEvent = 'click';
			if( o.expandSpeed == undefined ) o.expandSpeed= 500;
			if( o.collapseSpeed == undefined ) o.collapseSpeed= 500;
			if( o.expandEasing == undefined ) o.expandEasing = null;
			if( o.collapseEasing == undefined ) o.collapseEasing = null;
			if( o.multiFolder == undefined ) o.multiFolder = true;
			if( o.loadMessage == undefined ) o.loadMessage = 'Loading...';
			
			$(this).each( function() {
				
				function showTree(c, t) {
					$(c).addClass('wait');
					$.post(o.script, { dir: t }, function(data) {
						$(c).find('.start').html('');
						$(".jqueryFileTree.start").remove();
						$(c).removeClass('wait').append(data);
						if( o.root == t ) $(c).find('ul:hidden').show(); else $(c).find('ul:hidden').slideDown({ duration: o.expandSpeed, easing: o.expandEasing });
						bindTree(c);
					});
				}
				
				function bindTree(t) {
					$(t).find('li a.asset').bind(o.folderEvent, function() {
						if( $(this).parent().hasClass('directory') ) {
							if( $(this).parent().hasClass('collapsed') ) {
								// Expand
								if( !o.multiFolder ) {
									$(this).parent().parent().find('ul').slideUp({ duration: o.collapseSpeed, easing: o.collapseEasing });
									$(this).parent().parent().find('li.directory').removeClass('expanded').addClass('collapsed');
								}
								$(this).parent().find('ul').remove(); // cleanup
								showTree($(this).parent(), escape($(this).attr('rel')));
								$(this).parent().removeClass('collapsed').addClass('expanded');
							} else {
								// Collapse
								$(this).parent().find('ul').slideUp({ duration: o.collapseSpeed, easing: o.collapseEasing });
								$(this).parent().removeClass('expanded').addClass('collapsed');
							}
						} else {
							if($(this).parent().find('div.asset_info').hasClass('expanded')) {
								$(this).parent().find('div.asset_info').slideUp({ duration: o.collapseSpeed, easing: o.collapseEasing });
								$(this).parent().find('div.asset_info').removeClass('expanded').addClass('collapsed');
							} else {
								$('div.asset_info').slideUp({ duration: o.collapseSpeed, easing: o.collapseEasing });
								$(this).parent().find('div.asset_info').slideDown({ duration: o.collapseSpeed, easing: o.collapseEasing });
								$(this).parent().find('div.asset_info').removeClass('collapsed').addClass('expanded');
							}
						}
						return false;
					});
					// Prevent A from triggering the # on non-click events
					//if( o.folderEvent.toLowerCase != 'click' ) $(t).find('LI A').bind('click', function() { return false; });
				}
				// Loading message
				$(this).html('<ul class="jqueryFileTree start"><li class="wait">' + o.loadMessage + '<li></ul>');
				// Get the initial file list
				showTree( $(this), escape(o.root) );
			});
		}
	});
	
})(jQuery);

function razuna_insert(button) {
	div = jQuery(button).parents().find('.asset_info');
	id = jQuery(div).find('.razuna-file-id').val();
	type = jQuery(div).find('.razuna-file-kind').val();
	
	if(jQuery(div).find('.razuna-file-shared').val() == 'f') {
		jQuery(div).find('#razuna_setting_to_shared_message-' + id).attr('style', 'display: inline;');
		return false;
	}
	
	content = '';
	if(type == 'img') {
		content = razuna_build_img_code(div);
	} else {
		content = jQuery(div).find('.link-text').val();
		if(content == '') {
			jQuery(div).find('#razuna_link_text_empty_error-' + id).show();
			return false;
		}
	}
	linkTag = razuna_build_link_code(div, type, content);
	
	var win = window.dialogArguments || opener || parent || top;
	if (typeof win.send_to_editor == 'function') {
		win.send_to_editor(linkTag);
		if (typeof win.tb_remove == 'function') 
			win.tb_remove();
		return false;
	}
	tinyMCE = win.tinyMCE;
	if ( typeof tinyMCE != 'undefined' && tinyMCE.getInstanceById('content') ) {
		tinyMCE.selectedInstance.getWin().focus();
		tinyMCE.execCommand('mceInsertContent', false, linkTag);
	} else win.edInsertContent(win.edCanvas, linkTag);

	return false;
}

function razuna_build_img_code(div) {
	imgTag = "<img src=\"";
	
	if(jQuery(div).find('.image-size-original').is(':checked')) {
		imgTag += jQuery(div).find('.razuna-file-original').val();
	} else {
		imgTag += jQuery(div).find('.razuna-file-thumbnail').val();
	}
	imgTag += "\"" + " alt=\"" + jQuery(div).find('.alt').val() + "\"";
	
	imgTag += " />";
	return imgTag;
}

function razuna_build_link_code(div, type, content) {
	linkTag = "<a href=\"";
	
	if(type == 'img') {
		if(jQuery(div).find('.urlfield').val() == '') {
			return content;
		}
		linkTag += jQuery(div).find('.urlfield').val();
	} else {
		linkTag += jQuery(div).find('.razuna-file-original').val();
	}
	
	linkTag += "\">" + content + "</a>";
	return linkTag;
}

function razuna_share_item(element) {
	div = jQuery(element).parents().find('.asset_info');
	id = jQuery(div).find('.razuna-file-id').val();
	kind = jQuery(div).find('.razuna-file-kind').val();
	dir = jQuery(div).find('.razuna-file-dir').val();
	
	jQuery("#razuna_share_answer-" + id).hide();
	jQuery("#razuna_share_loading-" + id).show();
	
	script = razuna_plugin_url + "ajax/razuna-file-share.php";
	jQuery.post(script, { assetid: id, assetkind: kind, dir: dir }, function(data) {
		if(data.status == "ok") {
			// check if link needs to be replaced
			urlfield = jQuery(div).find('.urlfield').val();
			private_url = jQuery(div).find('.razuna-file-original').val();
			if(urlfield == private_url) {
				jQuery(div).find('.urlfield').val(data.original);
			}
			
			jQuery(div).find('.razuna-file-original').val(data.original);
			jQuery(div).find('.razuna-file-thumbnail').val(data.thumbnail);
			jQuery(div).find('.razuna-file-shared').val('t');
			
			razuna_insert(jQuery(div).find('.insert_into_post'));
		} else {
			jQuery(div).find('.razuna_share_loading').hide();
			jQuery(div).find('.razuna_share_failed').show();
		}
	}, "json");
	
}