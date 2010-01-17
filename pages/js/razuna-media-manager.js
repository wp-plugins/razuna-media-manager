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
		razunaInit: function(o, h) {
			var error_messages = new Array();
			error_messages['default'] = 'Unknown Error happened';
			error_messages['RazunaAccessDeniedException'] = 'Access Denied, check the configurations page';
			error_messages['RazunaNotAvailableException'] = 'Razuna service is not available at the moment';
			
			if(!o) var o = {};
			if(o.root == undefined) o.root = '0';
			if(o.collapseExpandSpeed == undefined) o.collapseExpandSpeed = 500;
			if(o.baseUrl == undefined) o.baseUrl = './';
			
			$(this).each(function() {
				
				function showTree(c, t) {
					$(c).addClass('wait');
					$.post(o.baseUrl + 'pages/ajax/razuna-file-tree.php', { dir: t }, function(response) {
						if(!preProcessAPIRequest(response)) return false;
						$(c).find('.start').html('');
						$(".razunaMediaBrowser.start").remove();
						$(c).removeClass('wait').append(getHtmlTree(response));
						if(o.root == t) $(c).find('ul:hidden').show(); else $(c).find('ul:hidden').slideDown({ duration: o.collapseExpandSpeed, easing: o.expandEasing });
						bindTree(c);
					}, 'json');
				}
				
				function bindTree(t) {
					$(t).find('li a.asset').bind('click', function() {
						if($(this).parent().hasClass('directory')) {
							if($(this).parent().hasClass('collapsed')) { // Expand
								// First close all opened folderes
								$(this).parent().parent().find('ul').slideUp({ duration: o.collapseSpeed, easing: o.collapseEasing });
								$(this).parent().parent().find('li.directory').removeClass('expanded').addClass('collapsed');
								$(this).parent().find('ul').remove(); // cleanup
								
								showTree($(this).parent(), escape($(this).attr('rel')));
								$(this).parent().removeClass('collapsed').addClass('expanded');
							} else { // Collapse
								$(this).parent().find('ul').slideUp({ duration: o.collapseSpeed, easing: o.collapseEasing });
								$(this).parent().removeClass('expanded').addClass('collapsed');
							}
						} else {
							if($(this).parent().find('div.asset_info').hasClass('expanded')) { // Collapse
								$(this).parent().find('div.asset_info').slideUp({ duration: o.collapseSpeed, easing: o.collapseEasing });
								$(this).parent().find('div.asset_info').removeClass('expanded').addClass('collapsed');
							} else { // Expand
								$('div.asset_info').slideUp({ duration: o.collapseSpeed, easing: o.collapseEasing });
								$(this).parent().find('div.asset_info').slideDown({ duration: o.collapseSpeed, easing: o.collapseEasing });
								$(this).parent().find('div.asset_info').removeClass('collapsed').addClass('expanded');
							}
						}
						return false;
					});
				}
				
				function preProcessAPIRequest(response) {
					if(response.status == 1) {
						if(error_messages[response.exception] == undefined) var message = error_messages['default'];
						if(error_messages[response.exception] != undefined) var message = error_messages[response.exception];
						error = '<div id="message" class="error"><p><strong>' + message + '</strong></p></div>';
						$('.razunaMediaBrowser').empty().append(error);
						return false;
					}
					return true;
				}
				
				function getHtmlTree(json) {
					response = '<ul class="razunaMediaBrowser" style="display: none;">';
					for(var i = 0; i < json.files.length; i++) {
						file = JSON.parse(json.files[i].obj);
						if(json.files[i].type == 'RazunaFolder') {
							response += '<li class="directory collapsed"><a class="asset" href="#" rel="' + file.id + '">' + file.name + '</a></li>'
						} else if(json.files[i].type == 'RazunaAsset') {
							response += '<li class="file kind_' + file.kind + '">';
							response += 	'<a class="asset" href="#">' + file.filename + '</a>';
							response += 	'<div class="asset_info" id="asset_info-' + file.id + '" style="display: none;">';
							response += 		'<input type="hidden" id="asset-' + file.id + '" value=\'' + json.files[i].obj + '\' />';
							response += 		'<table class="describe">';
							response += 			'<tr>';
							if(file.kind == 'img') {
								response += 			'<td><img src="' + file.thumbnail + '" /></td>';
								response +=				'<td>';
							} else {
								response += 			'<td colspan="2">';
							}
							response +=						'<strong>File name:</strong> ' + file.filename + '<br />';
							response += 					'<strong>Kind:</strong> ' + json.files[i]['kind_description'] + '<br />';
							response += 					'<strong>Shared:</strong> ' + json.files[i]['shared_description'] + '<br />';
							response += 				'</td>';
							response += 			'</tr>';
							if(file.kind == 'img') {
								
								response += 		'<tr>';
								response += 			'<td><strong>Size:</strong></td>';
								response += 			'<td>';
								response += 				'<div class="image-size-item">';
								response += 					'<input id="image-size" type="radio" value="thumbnail" class="image-size-thumbnail" name="image-size-' + file.id + '" checked="checked" />';
								response += 					'<label for="image-size">Thumbnail</label>';
								response += 				'</div>';
								response += 				'<div class="image-size-item">';
								response += 					'<input id="image-size" type="radio" value="original" class="image-size-original" name="image-size-' + file.id + '" />';
								response += 					'<label for="image-size">Original</label>';
								response += 				'</div>';
								response += 			'</td>';
								response += 		'</tr>';
								response += 		'<tr>';
								response += 			'<td><strong>Alternate text:</strong></td>';
								response += 			'<td><input type="text" class="alt" /></td>';
								response += 		'</tr>';
								response += 		'<tr>';
								response += 			'<td><strong>Link URL:</strong></td>';
								response += 			'<td>';
								response += 				'<input type="text" class="urlfield text" /><br />';
								response += 				'<button class="button" type="button" onclick="jQuery(this).parent().find(\'input\').val(\'\');">None</button>';
								response += 				'<button class="button" type="button" onclick="jQuery(this).parent().find(\'input\').val(\'' + file.url + '\');">File Original Size</button>';
								response += 				'<button class="button" type="button" onclick="jQuery(this).parent().find(\'input\').val(\'' + file.thumbnail + '\');">File Thumbnail Size</button>'
								response += 			'</td>';
								response += 		'</tr>';
							} else {
								response += 		'<tr>';
								response += 			'<td><strong>Link Text</strong></td>';
								response += 			'<td><input type="text" class="link-text" onkeyup="if(this.value != \'\') { jQuery(this).parents().find(\'.asset_info\').find(\'.razuna_link_text_empty_error\').removeAttr(\'style\'); }"/></td>';
								response += 		'</tr>';
							}
							response += 			'<tr>';
							response += 				'<td>&nbsp;</td>';
							response += 				'<td>';
							response += 					'<button class="button insert_into_post" type="button" onclick="jQuery(this).razunaInsert({ id: \'' + file.id + '\'});">Insert into Post</button>';
							response += 					'<span class="razuna_link_text_empty_error" id="razuna_link_text_empty_error-' + file.id + '">The Link Text is empty.</span>';
							response += 					'<span class="razuna_setting_to_shared_message" id="razuna_setting_to_shared_message-' + file.id + '">';
							response += 						'<i><br />This asset needs to be shared, do you want to share this asset? <a href="#insert" onclick="jQuery(this).razunaShare({ id: \'' + file.id + '\', baseUrl: \'' + o.baseUrl + '\'})" class="razuna_share_answer" id="razuna_share_answer-' + file.id + '">Yes</a></i>';
							response += 						'<span class="razuna_share_loading" id="razuna_share_loading-' + file.id + '"><img src="' + o.baseUrl + 'pages/img/spinner.gif" /></span>';
							response += 						'<span class="razuna_share_failed" id="razuna_share_failed-' + file.id + '">Failed</span>';
							response += 					'</span>';
							response += 				'</td>';
							response += 			'</tr>';
							response += 		'</table>';
							
							response += 	'</div>';
							response += '</li>';
						}
					}
					response += '</ul>';
					return response;
				}
				
				$(this).html('<ul class="razunaMediaBrowser start"><li class="wait">Logging into the Razuna service...<li></ul>');
				showTree($(this), escape(o.root));
				
			});
		},
		
		razunaInsert: function(o) {
			
			$(this).each(function() {
				function getImageTag(asset, div) {
					html = "<img src=\"";
					if($(div).find('.image-size-original').is(':checked')) {
						html += asset.url;
					} else {
						html += asset.thumbnail;
					}
					html += "\"" + " alt=\"" + $(div).find('.alt').val() + "\" />";
					return html;
				}
				
				function getLinkTag(asset, div, content) {
					html = "<a href=\"";
					if(asset.kind == 'img') {
						if($(div).find('.urlfield').val() == '') {
							return content;
						}
						html += $(div).find('.urlfield').val();
					} else {
						html += $(asset).url;
					}
					html += "\">" + content + "</a>";
					return html;
				}
				
				div = $('#asset_info-' + o.id);
				asset = JSON.parse($('#asset-' + o.id).val());

				if(!asset.shared) { jQuery(div).find('#razuna_setting_to_shared_message-' + o.id).attr('style', 'display: inline;'); return false; }

				content = '';
				if(asset.kind == 'img') { 
					content += getImageTag(asset, div);
				} else {
					content = $(div).find('.link-text').val();
					if(content == '') {
						$(div).find('#razuna_link_text_empty_error-' + o.id).show();
						return false;
					}
				}
				linkTag = getLinkTag(asset, div, content);

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
			});
		},
		
		razunaShare: function(o) {
			div = $('#asset_info-' + o.id);
			asset = JSON.parse($('#asset-' + o.id).val());

			$("#razuna_share_answer-" + o.id).hide();
			$("#razuna_share_loading-" + o.id).show();

			script = o.baseUrl + "pages/ajax/razuna-file-share.php";
			jQuery.post(script, { assetid: asset.id, assetkind: asset.kind, dir: asset.folder_id }, function(response) {
				if(response.status == '0') {
					new_asset = JSON.parse(response.obj);
					// check if link needs to be replaced
					urlfield = $(div).find('.urlfield').val();
					if(urlfield == asset.url) {
						$(div).find('.urlfield').val(new_asset.url);
					}
					
					new_asset.shared = true;
					$("#asset-" + o.id).val(JSON.stringify(new_asset));
					$(div).razunaInsert({id: o.id});
				} else {
					$(div).find('.razuna_share_loading').hide();
					$(div).find('.razuna_share_failed').show();
				}
			}, "json");
		}
		
	});
})(jQuery);