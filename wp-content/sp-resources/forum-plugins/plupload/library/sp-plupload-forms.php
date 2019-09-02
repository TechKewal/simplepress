<?php
/*
$LastChangedDate: 2018-02-06 20:15:21 -0600 (Tue, 06 Feb 2018) $
$Rev: 15637 $
*/

function sp_plupload_do_uploader_form($out, $fid, $uid='', $pid='', $type='') {
	if ((isset(SP()->rewrites->pageData['pageview']) && (SP()->rewrites->pageData['pageview']=='forum' || SP()->rewrites->pageData['pageview']=='topic') && SP()->core->forumData['display']['editor']['toolbar']) || (isset(SP()->rewrites->pageData['pageview']) && ((SP()->rewrites->pageData['pageview']=='pm' || SP()->rewrites->pageData['pageview']=='pmthread')))) {
		$toolbar = true;
		$class = ' class="spInlineSection"';
	} else {
		$toolbar = false;
	}

	sp_plupload_config(SP()->user->thisUser);
	global $plup;

	$uploads = SP()->options->get('spPlupload');

	# clean up some inputs
	$forumid = (is_object($fid)) ? $fid->forum_id : $fid;
	if (empty($ui)) $uid = SP()->user->thisUser->ID;

	# permissions check for uploader and allowed types
	$canUpload = false;
	$filters = '[ ';
	$uploadImages = ((SP()->auths->get('upload_images', $forumid) && (empty($type) || $type == 'edit')) || (SP()->auths->get('upload_signatures', '', $uid) && $type == 'sig') || $type == 'photos');
	$uploadMedia = (SP()->auths->get('upload_media', $forumid) && (empty($type) || $type == 'edit'));
	$uploadFiles = (SP()->auths->get('upload_files', $forumid) && (empty($type) || $type == 'edit'));
	if ($uploadImages) {
		$filters.= '{title : "'.esc_attr(__('Image files', 'sp-plup')).'", extensions : "'.str_replace(' ', '', $uploads['imagetypes']).'"}, ';
		$canUpload = true;
	}
	if ($uploadMedia) {
		$filters.= '{title : "'.esc_attr(__('Media files', 'sp-plup')).'", extensions : "'.str_replace(' ', '', $uploads['mediatypes']).'"}, ';
		$canUpload = true;
	}
	if ($uploadFiles) {
		$filters.= '{title : "'.esc_attr(__('Other files', 'sp-plup')).'", extensions : "'.str_replace(' ', '', $uploads['filetypes']).'"}';
		$canUpload = true;
	}
	if (!$canUpload) return $out;
	$filters.= ' ]';

	$tout = '';
	if ($toolbar) $tout.= "<div id='spUploadsBox'$class>";

	$tout.= '<div class="spEditorSection sp_file_uploader">';
	switch ($type) {
		case 'sig':
			$imageInsert = $uploads['imageinsert'];
			$mediaInsert = false;
			$fileInsert = false;
			$item = 'postitem';
			$button = apply_filters('sph_editor_signature_button', __('Upload Image', 'sp-plup'));
			break;

		case 'photos':
			$imageInsert = false;
			$mediaInsert = false;
			$fileInsert = false;
			$item = '';
			$forumid = '';
			$button = apply_filters('sph_editor_photos_button', __('Upload Photos', 'sp-plup'));
			break;

		default:
			$imageInsert = $uploads['imageinsert'];
			$item = 'postitem';
			$mediaInsert = $uploads['mediainsert'];
			$fileInsert = $uploads['fileinsert'];
			$button = apply_filters('sph_editor_attachment_button', __('Upload Attachments', 'sp-plup'));
			break;
	}
	$tout.= '<a class="spPlupEditorAttachments spButton" id="spUploadToggle" data-type="'.$type.'" data-target1="sp_file_uploader" data-target-2="sp_uploader_info">'.$button.'</a>';
	if ($type == 'edit') {
		$attachments = SP()->DB->table(SPPOSTATTACHMENTS, "post_id=$pid");
		if (!empty($attachments)) $tout.= '<a class="spButton spFileUploaderRemoveButton" data-target="sp_uploader_attachments">'.__('Remove Attachments', 'sp-plup').'</a>';
	}
	if ($type == 'photos') {
		$tout.= '<div class="spClear"></div><br /><b><u>'.__('After uploading photos, be sure to click on the Update Photos button', 'sp-plup').'</u></b>';
	}
	$out.= apply_filters('sph_uploader_editor_section', $tout, $type, $uploadImages, $uploadMedia, $uploadFiles);
	$out.= '<div style="clear:both"></div>';
	$out.= '<div id="sp_file_uploader"></div>';

	$plup['type'] = $type;
	$plup['fid'] = $forumid;
?>
	<script>
		(function(spj, $, undefined) {
			spj.plup = {
				item : '<?php echo($item); ?>',
				imageInsert : '<?php echo($imageInsert); ?>',
				mediaInsert : '<?php echo($mediaInsert); ?>',
				fileInsert : '<?php echo($fileInsert); ?>',
				filters : <?php echo($filters); ?>
			};
		}(window.spj = window.spj || {}, jQuery));
	</script>
<?php
	if (empty($type) || $type == 'edit') {
		add_action('wp_footer', 'sp_plupload_script');
	} else {
		$out.= sp_plupload_script(true);
	}

	$out.= '<div id="sp_uploader_errors"></div>';
	$out.= '<div id="sp_uploader_status"></div>';
	$out.= '<div id="sp_uploader_info">';
	$temp_out = '<table>';
	if ($uploadImages) {
		$temp_out.= '<tr>';
		$temp_out.= '<td class="plup-header">';
		$size = ($plup['maxsize']['image'] == 0) ? 'unlimited' : $plup['maxsize']['image'];
		$width = ($plup['imageresize']['width'] == 0) ? 'unlimited' : $plup['imageresize']['width'];
		$height = ($plup['imageresize']['height'] == 0) ? 'unlimited' : $plup['imageresize']['height'];
		$temp_out.= __('Image upload constraints', 'sp-plup').':';
		$temp_out.= '</td>';
		$temp_out.= '<td class="plup-data">';
		$temp_out.= __('Size (bytes)', 'sp-plup').':&nbsp;&nbsp;'.$size;
		$temp_out.= '</td>';
		$temp_out.= '<td class="plup-data">';
		$temp_out.= __('Width (pixels)', 'sp-plup').':&nbsp;&nbsp;'.$width;
		$temp_out.= '</td>';
		$temp_out.= '<td class="plup-data">';
		$temp_out.= __('Height (pixels)', 'sp-plup').':&nbsp;&nbsp;'.$height;
		$temp_out.= '</td>';
		$temp_out.= '</tr>';
		$temp_out.= '<tr>';
		$temp_out.= '<td class="plup-header">';
		$temp_out.= __('Image allowed types', 'sp-plup').':';
		$temp_out.= '</td>';
		$temp_out.= '<td class="plup-data" colspan="3">';
		$temp_out.= $uploads['imagetypes'];
		$temp_out.= '</td>';
		$temp_out.= '</tr>';
	}
	if ($uploadMedia) {
		$temp_out.= '<tr>';
		$temp_out.= '<td class="plup-header">';
		$size = ($plup['maxsize']['media'] == 0) ? 'unlimited' : $plup['maxsize']['media'];
		$width = ($plup['mediasize']['width'] == 0) ? 'unlimited' : $plup['mediasize']['width'];
		$height = ($plup['mediasize']['height'] == 0) ? 'unlimited' : $plup['mediasize']['height'];
		$temp_out.= __('Media upload constraints', 'sp-plup').':';
		$temp_out.= '</td>';
		$temp_out.= '<td class="plup-data">';
		$temp_out.= __('Size (bytes)', 'sp-plup').':&nbsp;&nbsp;'.$size;
		$temp_out.= '</td>';
		$temp_out.= '<td class="plup-data">';
		$temp_out.= __('Width (pixels)', 'sp-plup').':&nbsp;&nbsp;'.$width;
		$temp_out.= '</td>';
		$temp_out.= '<td class="plup-data">';
		$temp_out.= __('Height (pixels)', 'sp-plup').':&nbsp;&nbsp;'.$height;
		$temp_out.= '</td>';
		$temp_out.= '</tr>';
		$temp_out.= '<tr>';
		$temp_out.= '<td class="plup-header">';
		$temp_out.= __('Media allowed types', 'sp-plup').':';
		$temp_out.= '</td>';
		$temp_out.= '<td class="plup-data" colspan="3">';
		$temp_out.= $uploads['mediatypes'];
		$temp_out.= '</td>';
		$temp_out.= '</tr>';
	}
	if ($uploadFiles) {
		$temp_out.= '<tr>';
		$temp_out.= '<td class="plup-header">';
		$size = ($plup['maxsize']['file'] == 0) ? 'unlimited' : $plup['maxsize']['file'];
		$temp_out.= __('File upload constraints', 'sp-plup').':';
		$temp_out.= '</td>';
		$temp_out.= '<td class="plup-data" colspan="3">';
		$temp_out.= __('Size (bytes)', 'sp-plup').':&nbsp;&nbsp;'.$size;
		$temp_out.= '</td>';
		$temp_out.= '</tr>';
		$temp_out.= '<tr>';
		$temp_out.= '<td class="plup-header">';
		$temp_out.= __('File allowed types', 'sp-plup').':';
		$temp_out.= '</td>';
		$temp_out.= '<td class="plup-data" colspan="3">';
		$temp_out.= $uploads['filetypes'];
		$temp_out.= '</td>';
		$temp_out.= '</tr>';
	}

	$temp_out.= '</table>';
	$out.= apply_filters('sph_uploader_info', $temp_out);
	$out.= '</div>';

	if ($type == 'edit' && !empty($attachments)) {
		$out.= '<div id="sp_uploader_attachments">';
		$attachments = SP()->DB->table(SPPOSTATTACHMENTS, "post_id=$pid");
		$post = SP()->DB->table(SPPOSTS, "post_id=".$attachments[0]->post_id, 'row');
		$out.= '<p class="spPlupHeaderTitle">'.__('Select attachment(s) you want to remove from this post', 'sp-plup').':</p>';
		$out.= '<form action="'.SP()->spPermalinks->build_url(SP()->forum->view->thisTopic->forum_slug, SP()->forum->view->thisTopic->topic_slug, '', $post->post_id, $post->post_index).' method="post" name="removepostattachments">';
		$out.= '<input type="hidden" name="userid" value="'.$post->user_id.'" />';
		$out.= sp_plupload_render_attachment_list($attachments, false).'<br /><br />';
		$out.= '<input type="submit" class="spSubmit" name="removeattachments" value="'.esc_attr(__('Remove Attachments', 'sp-plup')).'" />';
		$out.= '<input type="button" class="spSubmit spPlupCancelRemove spCancelScript" name="cancel" value="'.esc_attr(__('Cancel', 'sp-plup')).'" />';
		$out.= '<p class="spPlupHeaderTitle">'.__('WARNING: Any other edits to this post will be lost, so you may want to save them first', 'sp-plup');
		$out.= '<br />'.__('You will also need to manually remove the image from the post content', 'sp-plup').'</p>';
		$out.= '</form>';
		$out.= '</div>';
	}

	$out.= '</div>';
	if ($toolbar) $out.= "</div>";

	return $out;
}

function sp_plupload_script($get=false) {
	global $plup;

	$url = htmlspecialchars_decode(wp_nonce_url(SPAJAXURL.'plupload&type='.$plup['type'].'&fid='.$plup['fid'], 'plupload'));
	$url2 = htmlspecialchars_decode(wp_nonce_url(SPAJAXURL.'plupload-remove&fid='.$plup['fid'].'&name=', 'plupload-remove'));
	$runtimes = apply_filters('sph_plugload_runtimes' , 'html5,silverlight,html4', $plup['type']);

	# get php max settings
	$max_upload = sp_plupload_return_bytes(ini_get('upload_max_filesize'));
	$max_post = sp_plupload_return_bytes(ini_get('post_max_size'));
	$max_file_size = min($max_upload, $max_post);

	$out = "
		<script>
			(function(spj, $, undefined) {
				$(document).ready(function() {
					$('#sp_file_uploader').plupload({
						runtimes : '$runtimes',
						url : '$url',
						max_file_size : '$max_file_size',
						silverlight_xap_url : '".SPPLUPSCRIPT."Moxie.xap',
						filters : spj.plup.filters,
						sortable: true,
						dragdrop: true,
						rename: true,
						resize: {preserve_headers: false},
						views: {
							list: true,
							thumbs: true,
							active: '".$plup['listtype']."',
							remember: true
						},
					});
					var uploader = $('#sp_file_uploader').plupload('getUploader');

					uploader.bind('FilesRemoved', function(up, files) {
						$.get('$url2' + files[0]['name']);
					});

					uploader.bind('FileUploaded', function(up, file, response) {
						try {
							var msg;
							var status = $.parseJSON(response.response);
							if (status['error']['code'] != 0) {
								msg = file.name + ': ".esc_attr(__('Error', 'sp-plup'))." ' + status['error']['code'] + ' - ' + status['error']['message'] + '<br />';
								$('#sp_uploader_errors').append(msg);
								if (status['error']['code'] == 109) {
									uploader.unbind('FilesRemoved');
									up.removeFile(file);
									uploader.bind('FilesRemoved', function(up, files) {
										$.get('$url2' + files[0]['name']);
									});
								} else {
									up.removeFile(file);
								}
							} else {
								file.name = status['file'];
								var x = spj.plup.imageInsert;
								var y = spj.plup.mediaInsert;
								var z = spj.plup.fileInsert;
								if (status['type'] == 'image' && x == '1') {
									spj.editorInsertAttachment(status['file'], status['file'], '".esc_attr($plup['link']['image'])."', spj.plup.item, status['width'], status['height'], status['twidth'], status['theight']);
								} else if (status['type'] == 'media' && y == '1') {
									spj.editorInsertMediaAttachment(status['file'], '".esc_attr($plup['link']['media'])."', '".esc_attr($plup['mediasize']['width'])."', '".esc_attr($plup['mediasize']['height'])."');
								} else if (status['type'] == 'file' && z == '1') {
									spj.editorInsertFileAttachment(status['file'], '".esc_attr($plup['link']['file'])."');
								}
								$('#sp_uploader_status').show().html('".esc_attr(__('Upload completed successfully', 'sp-plup'))."').fadeOut(5000);
							}
						}
						catch (e) {
							alert(e.message);
							msg = file.name + ': ".__('Error', 'sp-plup')." ".esc_attr(__('999 - Unknown server error (check image size - may be exhausting memory)', 'sp-plup'))."<br />';
							$('#sp_uploader_errors').append(msg);
							up.removeFile(file);
						}
					});
				});
			}(window.spj = window.spj || {}, jQuery));
		</script>
	";
	if ($get) {
		return $out;
	} else {
		echo $out;
	}
}

function sp_plupload_do_uploader_profile_form($userid) {
	$out = '';

	# display current photos
	$photos = get_user_meta($userid, 'photos', true);
	if ($photos) {
		$index = 0;
		$out.= '<table>';
		foreach ($photos as $photo) {
            $parts = explode('/', $photo);
            $node = implode('/', array_slice($parts, -3, 3));

			$out.= "<tr id='sp-photo-$index'>";
			$out.= '<td class="spPlupPhoto"><img src="'.$photo.'" width="200" /></td>';
			$msg = esc_attr(__('Are you sure you want to delete this profile photo?', 'sp-plup'));
			$site = wp_nonce_url(SPAJAXURL.'plupload-manage&targetaction=remove-photo&uid='.$userid.'&node='.$node.'&pid='.urlencode($photo), 'plupload-manage');
			$out.= '<td class="spPlupPhotoDel"><img class="spPlupProfilePhotoDelete" style="cursor:pointer;" title="'.__('Delete Photo', 'sp-plup').'" data-msg="'.$msg.'" data-url="'.$site.'" data-target="sp-photo-'.$index.'" src="'.SPCOMMONIMAGES.'delete.png" /></td>';
			$out.= '</tr>';
			$index++;
		}
		$out.= '</table>';
	} else {
		$out.= '<p class="spLabel">'.__('No photos currently uploaded', 'sp-plup').'<br /><br /></p>';
	}

	return $out.sp_plupload_do_uploader_form('', '', $userid, '', 'photos');
}
