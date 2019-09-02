<?php
/*
$LastChangedDate: 2018-10-24 06:19:24 -0500 (Wed, 24 Oct 2018) $
$Rev: 15767 $
*/

function sp_plupload_config($user) {
	$uploads = SP()->options->get('spPlupload');
	$sfconfig = SP()->options->get('sfconfig');

	$user_slug = (!empty($user->ID)) ? sp_create_slug($user->user_login, false) : '';

	# get folder to store in based on month/year
	$time = current_time('mysql');
	$y = substr($time, 0, 4);
	$m = substr($time, 5, 2);
	$subdir = "$y/$m/";

	# get path and url to uploads
	$image_uploads_link = $sfconfig['image-uploads'].'/'.$user_slug.'/'.$subdir;
	$image_uploads_path = str_replace('\\', '/', SP_STORE_DIR.'/'.$image_uploads_link);
	$image_uploads_basepath = str_replace('\\', '/', SP_STORE_DIR.'/'.$sfconfig['image-uploads'].'/'.$user_slug.'/');
	$image_uploads_baseurl = str_replace('\\', '/', SP_STORE_URL.'/'.$sfconfig['image-uploads'].'/'.$user_slug.'/');

	$media_uploads_link = $sfconfig['media-uploads'].'/'.$user_slug.'/'.$subdir;
	$media_uploads_path = str_replace('\\', '/', SP_STORE_DIR.'/'.$media_uploads_link);
	$media_uploads_basepath = str_replace('\\', '/', SP_STORE_DIR.'/'.$sfconfig['media-uploads'].'/'.$user_slug.'/');
	$media_uploads_baseurl = str_replace('\\', '/', SP_STORE_URL.'/'.$sfconfig['image-uploads'].'/'.$user_slug.'/');

	$file_uploads_link = $sfconfig['file-uploads'].'/'.$user_slug.'/'.$subdir;
	$file_uploads_path = str_replace('\\', '/', SP_STORE_DIR.'/'.$file_uploads_link);
	$file_uploads_basepath = str_replace('\\', '/', SP_STORE_DIR.'/'.$sfconfig['file-uploads'].'/'.$user_slug.'/');
	$file_uploads_baseurl = str_replace('\\', '/', SP_STORE_URL.'/'.$sfconfig['image-uploads'].'/'.$user_slug.'/');

	# set up some global image/media parameters

	global $plup;

	$plup['userid'] = $user->ID;

	$plup['listtype'] = ($uploads['showthumbs']) ? 'thumbs' : 'list';

	$plup['imageresize']['width'] = (!empty($uploads['imagemaxwidth'])) ? $uploads['imagemaxwidth'] : 0;
	$plup['imageresize']['height'] = (!empty($uploads['imagemaxheight'])) ? $uploads['imagemaxheight'] : 0;
	$plup['imagequality'] = (!empty($uploads['imgquality'])) ? $uploads['imgquality'] : 100;

	$sfimage = SP()->options->get('sfimage');
	$plup['thumbsize'] = (!empty($sfimage['thumbsize'])) ? $sfimage['thumbsize'] : 100;
	$plup['thumbquality'] = (!empty($uploads['thumbquality'])) ? $uploads['thumbquality'] : 100;

	$plup['mediasize']['width'] = (!empty($uploads['mediawidth'])) ? $uploads['mediawidth'] : 320;
	$plup['mediasize']['height'] = (!empty($uploads['mediaheight'])) ? $uploads['mediaheight'] : 240;

	# File upload size limit (0 is unlimited)
	$plup['maxsize']['image'] = ($user->admin) ? 0 : $uploads['imagemaxsize'];
	$plup['maxsize']['media'] = ($user->admin) ? 0 : $uploads['mediamaxsize'];
	$plup['maxsize']['file'] = ($user->admin) ? 0 : $uploads['filemaxsize'];

	# User stroage base paths
	$plup['basepath']['image'] = $image_uploads_basepath;
	$plup['basepath']['media'] = $media_uploads_basepath;
	$plup['basepath']['file']  = $file_uploads_basepath;

	# User stroage base urls
	$plup['baseurl']['image'] = $image_uploads_baseurl;
	$plup['baseurl']['media'] = $image_uploads_baseurl;
	$plup['baseurl']['file']  = $file_uploads_baseurl;

	# File upload current storage paths
	$plup['path']['image'] = $image_uploads_path;
	$plup['path']['media'] = $media_uploads_path;
	$plup['path']['file']  = $file_uploads_path;

	# File link paths
	$plup['link']['image'] = SP_STORE_URL.'/'.$image_uploads_link;
	$plup['link']['media'] = SP_STORE_URL.'/'.$media_uploads_link;
	$plup['link']['file'] = SP_STORE_URL.'/'.$file_uploads_link;

	$plup['prohibited'] = explode(',', str_replace(' ', '', $uploads['prohibited']));

	$plup['filetype']['image'] = explode(',', str_replace(' ', '', $uploads['imagetypes']));
	$plup['filetype']['media'] = explode(',', str_replace(' ', '', $uploads['mediatypes']));
	$plup['filetype']['file'] = explode(',', str_replace(' ', '', $uploads['filetypes']));
}

function sp_plupload_do_post_create($newpost) {
	# any attachments?
	if (empty($_POST['sp_file_uploader_count'])) return;

	sp_plupload_config(SP()->user->thisUser);
	global $plup;

	$sfconfig = SP()->options->get('sfconfig');

	for ($index = 0; $index < $_POST['sp_file_uploader_count']; $index++) {
		# make sure the upload was completed
		if ($_POST['sp_file_uploader_'.$index.'_status'] != 'done') continue;

		# get the filename, upload type and verify permission to upload
		$attachment = SP()->filters->str($_POST['sp_file_uploader_'.$index.'_name']);
		$nameparts = explode('.', $attachment);
		$ext = end($nameparts);
		if (!sp_plupload_validate_extension($ext, $plup['filetype']['image'])) {
			$typenow = 'image';
			$match = $sfconfig['image-uploads'];
			if (!SP()->auths->get('upload_images', $newpost['forumid'])) continue;
		} else if (!sp_plupload_validate_extension($ext, $plup['filetype']['media'])) {
			$typenow = 'media';
			$match = $sfconfig['media-uploads'];
			if (!SP()->auths->get('upload_media', $newpost['forumid'])) continue;
		} else if (!sp_plupload_validate_extension($ext, $plup['filetype']['file'])) {
			$typenow = 'file';
			$match = $sfconfig['file-uploads'];
			if (!SP()->auths->get('upload_files', $newpost['forumid'])) continue;
		} else {
			continue;
		}
		$file_name = stripslashes($attachment);
		$file_name = sp_plupload_clean_filename($file_name);

		# make sure the file exists
		$file = $plup['path'][$typenow].$file_name;
		if (!file_exists($file)) continue;

		# just get relative patht to storage location
		$path = explode($match.'/', $plup['path'][$typenow]);

		# get size of attachment
		$size = @filesize($file);

		# save the attachment
		SP()->DB->execute('INSERT INTO '.SPPOSTATTACHMENTS." (post_id, topic_id, type, path, filename, size) VALUES (".$newpost['postid'].", ".$newpost['topicid'].", '$typenow', '$path[1]', '$file_name', $size)");
	}
}

# NOTE: This is legacy function but seems backwards. more like 'check that extension not in types list'
# all uses seem to be using correctly. some day should change title or switch return condition
function sp_plupload_validate_extension($extension, $types) {
	foreach ($types as $type) {
		if (strtolower($extension) == strtolower($type)) return false;
	}
	return true;
}

# function to clean a filename string so it is a valid filename
function sp_plupload_clean_filename($filename) {
	$filename_raw = $filename;
	$special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", chr(0));
	$filename = str_replace($special_chars, '', $filename);
	$filename = preg_replace('/[\s-]+/', '-', $filename);
	$filename = trim($filename, '.-_');

	# Split the filename into a base and extension[s]
	$parts = explode('.', $filename);

	# Return if only one extension
	if (count($parts) <= 2) return $filename;

	# Process multiple extensions
	$filename = array_shift($parts);
	$extension = array_pop($parts);
	$mimes = get_allowed_mime_types();

	# Loop over any intermediate extensions.  Munge them with a trailing underscore if they are a 2 - 5 character
	# long alpha string not in the extension whitelist.
	foreach ((array) $parts as $part) {
		$filename .= '.' . $part;
		if (preg_match("/^[a-zA-Z]{2,5}\d?$/", $part)) {
			$allowed = false;
			foreach ($mimes as $ext_preg => $mime_match) {
				$ext_preg = '!(^' . $ext_preg . ')$!i';
				if (preg_match($ext_preg, $part)) {
					$allowed = true;
					break;
				}
			}
			if (!$allowed) $filename .= '_';
		}
	}
	$filename.= '.'.$extension;
	return $filename;
}

function sp_plup_convert_image($imagetemp, $imagetype) {
	if ($imagetype == 'image/pjpeg' || $imagetype == 'image/jpeg') {
		$cim1 = imagecreatefromjpeg($imagetemp);
	} elseif ($imagetype == 'image/x-png' || $imagetype == 'image/png') {
		$cim1 = imagecreatefrompng($imagetemp);
		imagealphablending($cim1, false);
		imagesavealpha($cim1, true);
	} elseif ($imagetype == 'image/gif') {
		$cim1 = imagecreatefromgif($imagetemp);
	}
	return $cim1;
}

function sp_plup_resize_image($im, $maxwidth, $maxheight, $urlandname, $comp, $imagetype) {
	$width = imagesx($im);
	$height = imagesy($im);
	if (($maxwidth && $width > $maxwidth) || ($maxheight && $height > $maxheight)) {
		if ($maxwidth && $width > $maxwidth) {
			$widthratio = $maxwidth/$width;
			$resizewidth = true;
		} else {
			$resizewidth = false;
		}

		if ($maxheight && $height > $maxheight) {
			$heightratio = $maxheight/$height;
			$resizeheight = true;
		} else {
			$resizeheight = false;
		}

		if ($resizewidth && $resizeheight) {
			if ($widthratio < $heightratio) {
				$ratio = $widthratio;
			} else {
				$ratio = $heightratio;
			}
		} elseif ($resizewidth) {
			$ratio = $widthratio;
		} elseif ($resizeheight) {
			$ratio = $heightratio;
		}

		$newwidth = $width * $ratio;
		$newheight = $height * $ratio;

		if (function_exists('imagecopyresampled') && $imagetype !='image/gif') {
			$newim = imagecreatetruecolor($newwidth, $newheight);
		} else {
			$newim = imagecreate($newwidth, $newheight);
		}

		# additional processing for png / gif transparencies (credit to Dirk Bohl)
		if ($imagetype == 'image/x-png' || $imagetype == 'image/png') {
			imagealphablending($newim, false);
			imagesavealpha($newim, true);
		} elseif ($imagetype == 'image/gif') {
			$originaltransparentcolor = imagecolortransparent( $im );
			if ($originaltransparentcolor >= 0 && $originaltransparentcolor < imagecolorstotal( $im )) {
				$transparentcolor = imagecolorsforindex( $im, $originaltransparentcolor );
				$newtransparentcolor = imagecolorallocate($newim, $transparentcolor['red'], $transparentcolor['green'], $transparentcolor['blue']);
				imagefill($newim, 0, 0, $newtransparentcolor);
				imagecolortransparent($newim, $newtransparentcolor);
			}
		}

		imagecopyresampled($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

		if ($imagetype == 'image/pjpeg' || $imagetype == 'image/jpeg') {
			imagejpeg ($newim, $urlandname, $comp);
		} elseif ($imagetype == 'image/x-png' || $imagetype == 'image/png') {
			imagepng ($newim, $urlandname, substr($comp,0,1));
		} elseif ($imagetype == 'image/gif') {
			imagegif ($newim, $urlandname);
		}

		imagedestroy($newim);
	} else {
		$newwidth = $newheight = 0;
		if ($imagetype == 'image/pjpeg' || $imagetype == 'image/jpeg') {
			imagejpeg ($im, $urlandname, $comp);
		} elseif ($imagetype == 'image/x-png' || $imagetype == 'image/png') {
			imagepng ($im, $urlandname, substr($comp,0,1));
		} elseif ($imagetype == 'image/gif') {
			imagegif($im, $urlandname);
		}
	}
	return array(floor($newwidth), floor($newheight));
}

function sp_plupload_format_size($size) {
	if ($size > 1073741824) return (round($size / 1073741824).' GB');
	if ($size > 1048576) return (round($size / 1048576).' MB');
	if ($size > 1024) return (round($size / 1024).' KB');
	return ($size.' b');
}

function sp_plupload_do_storage_location() {
	$storage = SP()->options->get('sfconfig');
	$path = SP_STORE_DIR.'/'.$storage['image-uploads'];
	spa_paint_storage_input(__('Image uploads folder', 'sp-plup'), 'image-uploads', $storage['image-uploads'], $path, false, false);
	$path = SP_STORE_DIR.'/'.$storage['media-uploads'];
	spa_paint_storage_input(__('Media uploads folder', 'sp-plup'), 'media-uploads', $storage['media-uploads'], $path, false, false);
	$path = SP_STORE_DIR.'/'.$storage['file-uploads'];
	spa_paint_storage_input(__('File uploads folder', 'sp-plup'), 'file-uploads', $storage['file-uploads'], $path, false, false);
}

function sp_plupload_do_storage_save() {
	$storage = SP()->options->get('sfconfig');
	if (!empty($_POST['image-uploads'])) $storage['image-uploads'] = trim(SP()->saveFilters->title(trim($_POST['image-uploads'])), '/');
	if (!empty($_POST['media-uploads'])) $storage['media-uploads'] = trim(SP()->saveFilters->title(trim($_POST['media-uploads'])), '/');
	if (!empty($_POST['file-uploads'])) $storage['file-uploads'] = trim(SP()->saveFilters->title(trim($_POST['file-uploads'])), '/');
	SP()->options->update('sfconfig', $storage);
}

function sp_plupload_do_tooltip($tooltips) {
	$tooltips['image-uploads'] = "If you are electing to allow your members to upload images to your server for use in the forum, it is necessary to create a base folder for the storage. Note that the system is capable of creating individual sub-folders for your members if you so choose.
If this folder did not get created during the installation then you will need to do so manually and will need to set the permissions to '775'. The default name for this folder is 'forum-image-uploads' but you can name it to suit. You might also condsider creating a single 'base' folder and creating the image and other upload folders as a sub-folder of that.
When the folder is in place - ensure that it is correctly entered into this storage location form and updated.
Note: Image Upload will be available to any member granted the correct permission.";
	$tooltips['media-uploads'] = "If you are electing to allow your members to upload media to your server for use in the forum, it is necessary to create a base folder for the storage. Note that the system is capable of creating individual sub-folders for your members if you so choose.
If this folder did not get created during the installation then you will need to do so manually and will need to set the permissions to '775'. The default name for this folder is 'forum-media-uploads' but you can name it to suit. You might also condsider creating a single 'base' folder and creating the media and other upload folders as a sub-folder of that.
When the folder is in place - ensure that it is correctly entered into this storage location form and updated.
Note: Media Upload will be available to any member granted the correct permission.";
	$tooltips['file-uploads'] = "If you are electing to allow your members to upload files to your server for use in the forum, it is necessary to create a base folder for the storage. Note that the system is capable of creating individual sub-folders for your members if you so choose.
If this folder did not get created during the installation then you will need to do so manually and will need to set the permissions to '775'. The default name for this folder is 'forum-file-uploads' but you can name it to suit. You might also condsider creating a single 'base' folder and creating the file and other upload folders as a sub-folder of that.
When the folder is in place - ensure that it is correctly entered into this storage location form and updated.
Note: File Upload will be available to any member granted the correct permission.";
	return $tooltips;
}

function sp_plupload_do_head() {
	$css = SP()->theme->find_css(SPPLUPCSS, 'plupload.css', 'plupload.spcss');
	SP()->plugin->enqueue_style('sp-plupload', $css);

	$css = SP()->theme->find_css(SPPLUPSCRIPT.'jquery.ui.plupload/css/', 'jquery.ui.css');
	SP()->plugin->enqueue_style('sp-plupload-ui', $css);
	$css = SP()->theme->find_css(SPPLUPSCRIPT.'jquery.ui.plupload/css/', 'jquery.ui.plupload.css');
	SP()->plugin->enqueue_style('sp-plupload-plup', $css);
}

function sp_plupload_do_load_js($footer) {
	SP()->plugin->enqueue_script('spplup', SPPLUPSCRIPT.'plupload.full.min.js', array('jquery'), false, $footer);
	SP()->plugin->enqueue_script('spplupui', SPPLUPSCRIPT.'jquery.ui.plupload/jquery.ui.plupload.min.js', array('jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-button', 'jquery-ui-sortable', 'jquery-ui-progressbar'), false, $footer);

	$script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPPLUPSCRIPT.'sp-plupload.js' : SPPLUPSCRIPT.'sp-plupload.min.js';
	SP()->plugin->enqueue_script('spplupjs', $script, array('jquery'), false, $footer);

	$strings = array(
		'confirm' => __('Please cofirm you wish to permanently delete the selected attachments', 'sp-plup'),
	);
	SP()->plugin->localize_script('spplupjs', 'sp_plup_vars', $strings);

	$uploads = SP()->options->get('spPlupload');
	if (!empty($uploads['lang']) && $uploads['lang'] != 'en') {
		if (file_exists(SPPLUPI18NDIR.$uploads['lang'].'.js')) {
			SP()->plugin->enqueue_script('spplupi18n', SPPLUPI18NURL.$uploads['lang'].'.js', array('spplup', 'spplupui'), false, $footer);
		}
	}

	if (SP()->rewrites->pageData['pageview'] == 'profileedit') {
		wp_enqueue_script('sppluptree', SPPLUPSCRIPT.'jstree/jstree.min.js', array('jquery'), false, $footer);
	}
}

function sp_plupload_do_forumview_query($forums, $topics) {
	if (empty($forums) || empty($topics)) return $forums;

	$t = implode(',', $topics);
	$records = SP()->DB->select('SELECT topic_id FROM '.SPPOSTATTACHMENTS." WHERE topic_id IN ($t)", 'col');
	if ($records) {
		foreach ($records as $topic_id) {
			$forums->topics[$topic_id]->attachments = true;
		}
	}
	return $forums;
}

function sp_plupload_do_status_icon($out) {
	if (!empty(SP()->forum->view->thisTopic->attachments)) {
		$p = (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? SPPLUPIMAGESMOB : SPPLUPIMAGES;
		$toolTip = esc_attr(__('This topic has attachments', 'sp-plup'));
		$out.= SP()->theme->paint_icon('spIcon spIconNoAction', $p, "sp_PlupAttachmentStatus.png", $toolTip);
	}
	return $out;
}

function sp_plupload_do_show_attachments($out) {
	if (empty(SP()->forum->view->thisPost->attachments)) return $out;

	$uploads = SP()->options->get('spPlupload');
	$show = false;

	$sfconfig = SP()->options->get('sfconfig');

	$temp = '';

	$temp.= '<div class="spPostIndexAttachments spClear">';
	$temp.= '<fieldset>';
	$icon = SP()->theme->paint_icon('', SPPLUPIMAGES, "sp_PlupAttachments.png");
	$temp.= "<legend>".$icon.__('Attachments', 'sp-plup').'</legend>';
	$temp.= '<ul>';
	foreach (SP()->forum->view->thisPost->attachments as $attachment) {
		$found = false;
		$temp2 = '<li>';
		if ($attachment->type == 'image') {
			if (!$uploads['showinserted']) continue;
			$icon = SP()->theme->paint_icon('', SPPLUPIMAGES, "sp_PlupImage.png");
			$show = $found = true;
			$link = SP()->auths->get('can_view_images', SP()->rewrites->pageData['forumid'], SP()->user->thisUser->ID);
		} else if ($attachment->type == 'media') {
			if (!$uploads['showinserted']) continue;
			$icon = SP()->theme->paint_icon('', SPPLUPIMAGES, "sp_PlupMedia.png");
			$show = $found = true;
			$link = SP()->auths->get('can_view_media', SP()->rewrites->pageData['forumid'], SP()->user->thisUser->ID);
		} else if (SP()->auths->get('download_attachments', SP()->forum->view->thisTopic->forum_id)) {
			if (!$uploads['showinserted']) continue;
			$icon = SP()->theme->paint_icon('', SPPLUPIMAGES, "sp_PlupFile.png");
			$show = $found = $link = true;
		}
		$temp2.= $icon;
		$url = apply_filters('sph_plup_attachment_url', SP_STORE_URL.'/'.$sfconfig["$attachment->type-uploads"].'/'.$attachment->path.$attachment->filename);

		if ($link) {
			$temp2.= "<a href='$url'>{$attachment->filename}</a> ";
		} else {
			$temp2.= $attachment->filename;
		}
		$temp2.= '<span>('.sp_plupload_format_size($attachment->size).')</span>';
		$temp2.= '</li>';
		$sffilters = SP()->options->get('sffilters');
		if ($sffilters['sfnofollow']) $temp2 = SP()->saveFilters->nofollow($temp2);
		if ($sffilters['sftarget']) $temp2 = SP()->saveFilters->target($temp2);
		if ($found) $temp.= $temp2;
	}
	$temp.= '</ul>';
	$temp.= '</fieldset>';
	$temp.= "</div>\n";

	if ($show) $out.= $temp;
	return $out;
}

function sp_plupload_do_post_tool($out, $forum, $topic, $post, $br) {
	if (SP()->user->thisUser->admin || SP()->user->thisUser->moderator) {
		$attachments = SP()->DB->table(SPPOSTATTACHMENTS, 'post_id = '.$post['post_id']);
		if (!empty($attachments)) {
			$out.= sp_open_grid_cell();
			$out.= '<div class="spForumToolsAttachments">';
			$title = esc_attr(__('Remove Attachments', 'sp-plup'));
			$site = wp_nonce_url(SPAJAXURL.'plupload-manage&amp;targetaction=list-attachments&amp;pid='.$post['post_id'], 'plupload-manage');
			$out.= '<a rel="nofollow" class="spOpenDialog" data-site="'.$site.'" data-label="'.$title.'" data-width="400" data-height="0" data-align="center">';
			$out.= SP()->theme->paint_icon('spIcon', SPPLUPIMAGES, "sp_ToolsAttachments.png").$br;
			$out.= $title.'</a>';
			$out.= '</div>';
			$out.= sp_close_grid_cell();
		}
	}
	$out = apply_filters('sph_post_tool_plupload', $out);
	return $out;
}

function sp_plupload_do_process_actions() {
	if (isset($_POST['removeattachments'])) {
		$uid = (!empty($_POST['userid'])) ? SP()->filters->integer($_POST['userid']) : 0;
		if (empty($uid) && !SP()->user->thisUser->admin && !SP()->user->thisUser->moderator) return; # forum tools perm check
		if (SP()->user->thisUser->ID != $uid && !SP()->user->thisUser->admin && !SP()->user->thisUser->moderator) return; # edit post perm check

		$remove = $_POST['spPostAttachments']; # an array so sanitized later below
		if (empty($remove)) return;

		$sfconfig = SP()->options->get('sfconfig');

		foreach ($remove as $cur) {
			$cur = SP()->filters->integer($cur);
			$attachment = SP()->DB->table(SPPOSTATTACHMENTS, "attachment_id=$cur", 'row');
			if (empty($attachment)) continue;

			# delete the file
			$path = untrailingslashit(SP_STORE_DIR.'/'.$sfconfig["$attachment->type-uploads"].'/'.$attachment->path);
			$file = $path.'/'.$attachment->filename;
			if (file_exists($file)) @unlink($file);
			$thumb = $path.'/_thumbs/_'.$attachment->filename;
			if (file_exists($thumb)) @unlink($thumb);

			# delete the directory if empty now
			if (count(glob($path.'/*')) <= 2) SP()->primitives->remove_dir($path); # thumbs dir will still be there

			# clean up post content
			$attachments = SP()->DB->select('SELECT * FROM '.SPPOSTATTACHMENTS." WHERE path='$attachment->path' AND filename='$attachment->filename'");
			if (empty($attachments)) continue;
			foreach ($attachments as $attachment) {
				if ($attachment->type != 'file') {
					$post_content = SP()->DB->select('SELECT post_content FROM '.SPPOSTS." WHERE post_id=$attachment->post_id", 'var');
					$src = untrailingslashit(SP_STORE_URL.'/'.$sfconfig["$attachment->type-uploads"].'/'.$attachment->path.$attachment->filename);
					if ($attachment->type == 'image') {
						$match = '#<img[^>]+src[^>]+'.$src.'+[^>]+>#';
						$replace = apply_filters('sph_attachment_removed_image', '<p class="spImageRemoved">'.__('*** Image attachment removed from post content ***', 'sp-plup').'</p>', $attachment->post_id, $attachment->attachment_id);
						$post_content = preg_replace($match, $replace, $post_content);
					} else {
						$match = '#<audio[^>]+src[^>]+'.$src.'+[^>]+>#';
						$replace = apply_filters('sph_attachment_removed_media', '<p class="spImageRemoved">'.__('*** Media attachment removed from post content ***', 'sp-plup').'</p>', $attachment->post_id, $attachment->attachment_id);
						$post_content = preg_replace($match, $replace, $post_content);

						$match = '#<video[^>]+src[^>]+'.$src.'+[^>]+>#';
						$replace = apply_filters('sph_attachment_removed_media', '<p class="spImageRemoved">'.__('*** Media attachment removed from post content ***', 'sp-plup').'</p>', $attachment->post_id, $attachment->attachment_id);
						$post_content = preg_replace($match, $replace, $post_content);
					}
					SP()->DB->execute('UPDATE '.SPPOSTS." SET post_content='$post_content' WHERE post_id=$attachment->post_id");
				}

				# remove from db
				SP()->DB->execute('DELETE FROM '.SPPOSTATTACHMENTS." WHERE attachment_id=$attachment->attachment_id");
			}
		}
	}
}

function sp_plupload_do_topic_delete($posts, $topicid) {
	if (empty($topicid)) return;

	$sql = 'DELETE FROM '.SPPOSTATTACHMENTS." WHERE topic_id=$topicid";
	SP()->DB->execute($sql);
}

function sp_plupload_do_post_delete($post) {
	if (empty($post)) return;

	$sql = 'DELETE FROM '.SPPOSTATTACHMENTS." WHERE post_id=$post->post_id";
	SP()->DB->execute($sql);
}

function sp_plupload_render_attachment_list($attachments, $echo=true) {
	$out = '<select multiple="multiple" class="spSelect" name="spPostAttachments[]">';
	if (!empty($attachments)) {
		foreach ($attachments as $attachment) {
			$out.= "<option value='".esc_attr($attachment->attachment_id)."'>{$attachment->filename}</option>";
		}
	}
	$out.='</select>';

	if ($echo) {
		echo $out;
	} else {
		return $out;
	}
}

function sp_plupload_do_uploader_photos_save($message, $thisUser, $thisForm) {
	# any attachments?
	if (empty($_POST['sp_file_uploader_count'])) return;

	sp_plupload_config(SP()->user->thisUser);
	global $plup;

	$photos = get_user_meta($thisUser, 'photos', true);
	$numPhotos = (empty($photos)) ? 0 : count($photos);
	for ($index = 0; $index < $_POST['sp_file_uploader_count']; $index++) {
		# make sure the upload was completed
		if ($_POST['sp_file_uploader_'.$index.'_status'] != 'done') continue;

		# get the filename, upload type and verify permission to upload
		$photo = SP()->filters->str($_POST['sp_file_uploader_'.$index.'_name']);
		$nameparts = explode('.', $photo);
		$ext = end($nameparts);
		if (!sp_plupload_validate_extension($ext, $plup['filetype']['image'])) {
			$typenow = 'image';
		} else {
			continue;
		}
		$file_name = stripslashes($photo);
		$file_name = sp_plupload_clean_filename($file_name);
		# make sure the file exists
		$file = $plup['path'][$typenow].$file_name;
		if (!file_exists($file)) continue;

		# make sure we dont exceed max # photos
		$spProfileOptions = SP()->options->get('sfprofile');
		if ($numPhotos < $spProfileOptions['photosmax']) {
			# should be good - prepare for storage in db attachments field
			$photos[$numPhotos] = $plup['link'][$typenow].$file_name;
			$numPhotos++;
		} else {
			# max number exceeded, remove the upload
			$path = untrailingslashit($plup['path'][$typenow]);
			$file = $path.'/'.$file_name;
			if (file_exists($file)) @unlink($file);
			$thumb = $path.'/_thumbs/_'.$file_name;
			if (file_exists($thumb)) @unlink($thumb);

			# delete the directory if empty now
			if (count(glob($path.'/*')) <= 2) SP()->primitives->remove_dir($path); # thumbs dir will still be there

			# notify user of error
			$message['type'] = 'error';
			$message['text'] = __('Max number of photos exceeded', 'sp-plup');
			return $message;
			break;
		}
	}

	# if any valid attachments then save
	update_user_meta($thisUser, 'photos', $photos);

	$message['type'] = 'success';
	$message['text'] = __('Photos updated', 'sp-plup');

	return $message;
}

function sp_plupload_check_prohibited($file, $json=true) {
	global $plup;

	$nameparts = explode('.', $file);
	$ext = end($nameparts);
	if (!sp_plupload_validate_extension($ext, $plup['prohibited'])) {
		if ($json) die('{"jsonrpc" : "2.0", "error" : {"code": "101", "message": "'.__("Prohibited file type", "sp-plup").'"}, "id" : "id"}');
		return false;
	} else {
		return true;
	}
}

function sp_plupload_check_permissions($file, $forumid, $json=true) {
	global $plup;

	$nameparts = explode('.', $file);
	$ext = end($nameparts);

	$data = new stdClass();
	$data->error = '';

	# Get upload type and double check permissions
	if (!sp_plupload_validate_extension($ext, $plup['filetype']['image'])) {
		$data->type = 'image';
		if (!SP()->auths->get('upload_images', $forumid, $plup['userid'])) {
			if ($json) die('{"jsonrpc" : "2.0", "error" : {"code": "102", "message": "'.__("You do not have permission to upload images", "sp-plup").'"}, "id" : "id"}');
			$data->error = __('You do not have permission to upload images', 'sp-plup');
		}
	} else if (!sp_plupload_validate_extension($ext, $plup['filetype']['media'])) {
		$data->type = 'media';
		if (!SP()->auths->get('upload_media', $forumid, $plup['userid'])) {
			if ($json) die('{"jsonrpc" : "2.0", "error" : {"code": "103", "message": "'.__("You do not have permission to upload media", "sp-plup").'"}, "id" : "id"}');
			$data->error = __('You do not have permission to upload images', 'sp-plup');
		}
	} else if (!sp_plupload_validate_extension($ext, $plup['filetype']['file'])) {
		$data->type = 'file';
		if (!SP()->auths->get('upload_files', $forumid, $plup['userid'])) {
			if ($json) die('{"jsonrpc" : "2.0", "error" : {"code": "104", "message": "'.__("You do not have permission to upload files", "sp-plup").'"}, "id" : "id"}');
			$data->error = __('You do not have permission to upload images', 'sp-plup');
		}
	} else {
		if ($json) die('{"jsonrpc" : "2.0", "error" : {"code": "105", "message": "'.__("Invalid file type", "sp-plup").'"}, "id" : "id"}');
		$data->type = 'invalid';
		$data->error = __('Invalid file type', 'sp-plup');
	}

	return $data;
}

function sp_plupload_check_filesize($type, $size, $json=true) {
	global $plup;

	if ($plup['maxsize'][$type] > 0 && $size > $plup['maxsize'][$type]) {
		if ($json) die('{"jsonrpc" : "2.0", "error" : {"code": "106", "message": "'.__("Upload file size exceeds maximum allowed size", "sp-plup").'"}, "id" : "id"}');
		return false;
	} else {
		return true;
	}
}

function sp_plupload_move_upload($name, $srcname, $type, $json=true, $rename=false) {
	global $plup;

	$browsepath = $plup['path'][$type];

	$data = new stdClass();
	$data->path = $browsepath;
	$data->error = '';

	# do we need to create folders
	if (!file_exists($browsepath)) {
		# create file upload folder
		$success = wp_mkdir_p($browsepath); # create the date dirs if needed
		if ($success) {
			if ($type == 'image') {
				$success = wp_mkdir_p($browsepath.'_thumbs/');
				if (!$success) {
					if ($json) die('{"jsonrpc" : "2.0", "error" : {"code": "107", "message": "'.__("Could not create thumbs folder", "sp-plup").'"}, "id" : "id"}');
					$data->error = __('Could not create thumbs folder', 'sp-plup');
				}
			}
		} else {
			if ($json) die('{"jsonrpc" : "2.0", "error" : {"code": "108", "message": "'.__("Could not create user folder", "sp-plup").'"}, "id" : "id"}');
			$data->error = __('Could not create user folder', 'sp-plup');
		}
	}

	# Check file data and copy to temp file for processing
	if ($srcname && $name) {
		$source_file = $srcname;
		$file_name = stripslashes($name);
		$file_name = sp_plupload_clean_filename($file_name);

		# check if file exists
		if (file_exists($browsepath.$file_name)) {
			$exist = true;
			$ver = 0;
			$tempname = pathinfo($file_name);
			while ($exist) {
				$ver++;
				$exist = file_exists($browsepath.$tempname['filename'].'-'.$ver.'.'.$tempname['extension']);
			}
			$file_name = $tempname['filename'].'-'.$ver.'.'.$tempname['extension'];
		}
		$data->filename = $file_name;

		if (is_dir($browsepath)) {
		   if ($rename) {
			   $success = rename($source_file, $browsepath.$file_name.'_');
		   } else {
			   $success = move_uploaded_file($source_file, $browsepath.$file_name.'_');
		   }
			if (!$success) {
				if ($json) die('{"jsonrpc" : "2.0", "error" : {"code": "110", "message": "'.__("Unable to copy file to user folder", "sp-plup").'"}, "id" : "id"}');
				$data->error = __('Unable to copy file to user folder', 'sp-plup');
			}
		} else {
			if ($json) die('{"jsonrpc" : "2.0", "error" : {"code": "111", "message": "'.__("File destination is not a directory", "sp-plup").'"}, "id" : "id"}');
			$data->error = __('File destination is not a directory', 'sp-plup');
		}
	} else {
		if ($json) die('{"jsonrpc" : "2.0", "error" : {"code": "112", "message": "'.__("Uploaded file no longer available", "sp-plup").'"}, "id" : "id"}');
		$data->error = __('Uploaded file no longer available', 'sp-plup');
	}

	return $data;
}

function sp_plupload_process_upload($browsepath, $filename, $type, $sizeCheck='default', $json=true) {
	global $plup;

	$nameparts = explode('.', $filename);
	$ext = end($nameparts);

	$data = new stdClass();
	$data->error = '';

	if ($handle = opendir($browsepath)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && substr($file, -1) == '_') {
				# File Naming
				$tmp_filename = $browsepath.$file;
				$dest_filename = $browsepath.rtrim($file, '_');

				# Rename temp file to dest file
				rename($tmp_filename, $dest_filename);

				$imginfo = array();
				# if image, perform additional processing
				if ($type == 'image') {
					# Good mime-types
					$imginfo = @getimagesize($dest_filename);
					if ($imginfo === false) {
						unlink($dest_filename);
						if ($json) die('{"jsonrpc" : "2.0", "error" : {"code": "113", "message": "'.__("Unable to get image size information", "sp-plup").'"}, "id" : "id"}');
						$data->error = __('Unable to get image size information', 'sp-plup');
					}

					# verify mime type
					$mime_to_ext = apply_filters('sph_mimes_to_exts', array(
						'image/jpeg' => array('jpg', 'jpeg'),
						'image/png'	 => array('png'),
						'image/gif'	 => array('gif'),
						'image/bmp'	 => array('bmp'),
						'image/tiff' => array('tif'),
					));
					$mime = $imginfo['mime'];
					if (empty($mime_to_ext[$mime]) || !in_array(strtolower($ext), (array) $mime_to_ext[$mime])) {
						unlink($dest_filename);
						if ($json) die('{"jsonrpc" : "2.0", "error" : {"code": "109", "message": "'.__("Actual mime type doesnt match file type", "sp-plup").'"}, "id" : "id"}');
						$data->error = __('Actual mime type doesnt match file type', 'sp-plup');
					}

					# resize image to maximum height and width, if set
					switch ($sizeCheck) {
						case 'photos':
							$maxwidth = $plup['imageresize']['width'];
							$maxheight = $plup['imageresize']['height'];
							break;

						default:
							$maxwidth = $plup['imageresize']['width'];
							$maxheight = $plup['imageresize']['height'];
							break;
					}

					if ($maxwidth > 0 || $maxheight > 0) {
						# assign new width and height values, only if they are less than existing image size
						$widthnew = ($maxwidth > 0 && $maxwidth < $imginfo[0] ? $maxwidth : $imginfo[0]);
						$heightnew = ($maxheight > 0 && $maxheight < $imginfo[1] ? $maxheight : $imginfo[1]);

						# only resize if width or height values are different
						if ($widthnew != $imginfo[0] || $heightnew != $imginfo[1]) {
							$im = sp_plup_convert_image($dest_filename, $mime);
							$resized = sp_plup_resize_image($im, $widthnew, $heightnew, $dest_filename, $plup['imagequality'], $mime);
							$imginfo[0] = $resized[0];
							$imginfo[1] = $resized[1];
							imagedestroy($im);
						}
					}

					# generate thumbnail
					$thumbimg = $browsepath.'_thumbs/_'.rtrim($file,'_');
					if (!file_exists($thumbimg)) {
						$im = sp_plup_convert_image($dest_filename, $mime);
						sp_plup_resize_image($im, $plup['thumbsize'], $plup['thumbsize'], $thumbimg, $plup['thumbquality'], $mime);
						imagedestroy($im);
					}

					$data->width = $imginfo[0];
					$data->height = $imginfo[1];
				}
			}
		}
		closedir($handle);
	} else {
		if ($json) die('{"jsonrpc" : "2.0", "error" : {"code": "114", "message": "'.__("Unable to open directory to process image", "sp-plup").'"}, "id" : "id"}');
		$data->error = __('Unable to open directory to process image', 'sp-plup');
	}

	return $data;
}

function sp_plupload_add_attachment($filename, $type, $postid) {
	global $plup;

	# make sure the file exists and sanitize
	$filename = stripslashes($filename);
	$filename = sp_plupload_clean_filename($filename);
	$file = $plup['path'][$type].$filename;
	if (!file_exists($file)) return false;

	# just get relative patht to storage location
	$sfconfig = SP()->options->get('sfconfig');
	$match = $sfconfig[$type.'-uploads'];
	$path = explode($match.'/', $plup['path'][$type]);

	# get size of attachment
	$size = @filesize($file);

	# need topic id
	$topic_id = SP()->DB->table(SPPOSTS, "post_id=$postid", 'topic_id');

	# save the attachment
	$success = SP()->DB->execute('INSERT INTO '.SPPOSTATTACHMENTS." (post_id, topic_id, type, path, filename, size) VALUES ($postid, $topic_id, '$type', '$path[1]', '$filename', $size)");

	return $success;
}

function sp_plupload_do_gd_check() {
	if (extension_loaded('gd') && function_exists('gd_info')) return;
	echo spa_message(__('Warning - You PHP does not appear to have the GD library compiled in. The File Uploader plugin may not function correctly.	 Please contact your host.', 'sp-plup'), 'error');
}

function sp_plupload_return_bytes($val) {
	$val = trim($val);
	$last = strtolower($val[strlen($val)-1]);
	$val = substr($val, 0, -1); # strip the last character
	switch ($last) {
		case 'g':
			$val*= 1024;
		case 'm':
			$val*= 1024;
		case 'k':
			$val*= 1024;
	}

	return $val;
}

function sp_plupload_do_reset_profile_tabs() {
	SP()->profile->add_tab('Attachments', 0, 1, 'manage_attachments');
	SP()->profile->add_menu('Attachments', 'Image Uploads', SPPLUPFORMSDIR.'sp-plupload-images-form.php', 0, 1, 'upload_images');
	SP()->profile->add_menu('Attachments', 'Media Uploads', SPPLUPFORMSDIR.'sp-plupload-media-form.php', 0, 1, 'upload_media');
	SP()->profile->add_menu('Attachments', 'File Uploads', SPPLUPFORMSDIR.'sp-plupload-files-form.php', 0, 1, 'upload_files');
}

function sp_plupload_do_post_records($topics, $postids) {
	if ($postids) {
		$ids = implode(',', $postids);
		$attachments = SP()->DB->table(SPPOSTATTACHMENTS, "post_id IN ($ids)");
		if ($attachments) {
			foreach ($attachments as $attachment) {
				if (!isset($topics->posts[$attachment->post_id]->attachments)) $topics->posts[$attachment->post_id]->attachments = array();
				$topics->posts[$attachment->post_id]->attachments[] = $attachment;
			}
		}
	}

	return $topics;
}

# Is the path to the image in the standard uploads folder?
# If so can we make use fo the file manager thumbs?
function sp_plupload_do_filter_images($image_array, $src, $width, $height, $title, $alt, $style, $class) {
	$sfconfig = SP()->options->get('sfconfig');
	$pos = strpos($src[1], SP_STORE_URL.'/'.$sfconfig['image-uploads'].'/');
	if ($pos !== false) {
		$testsrc = str_replace(SP_STORE_URL.'/'.$sfconfig['image-uploads'].'/', SP_STORE_DIR.'/'.$sfconfig['image-uploads'].'/', $src[1]);
		if (file_exists($testsrc)) {
			$last = strrpos($src[1], '/');
			$left = substr($src[1], 0, $last + 1);
			$right = substr($src[1], $last + 1);

			# check for animated gif and igniore thumbs
			if (substr($right, (strpos($right, '.')+1) == 'gif')) {
				if (sp_is_animated_gif($src[1])) return $image_array;
			}

			$image_array['thissrc'] = 'src="'.$left.'_thumbs/_'.$right.'"';
			$image_array['thiswidth'] = '';
		}
	}

	return $image_array;
}

# counts the franes in a gif to see if animated
function sp_is_animated_gif($filename) {
	$response = wp_remote_get($filename, array('timeout' => 5));
	if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) return false;
	$body = wp_remote_retrieve_body($response);
	$count = preg_match_all('#\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)#s', $body, $matches);
	return $count > 1;
}


# Gets the first attached image in current topic
function sp_og_find_attachment($link) {
	$sql = "SELECT path, filename
			FROM ".SPPOSTATTACHMENTS."
			WHERE topic_id = ".SP()->rewrites->pageData['topicid']."
			AND type = 'image'
			ORDER BY attachment_id
			LIMIT 1";

	$rec = SP()->DB->select($sql, 'row');
	if (!empty($rec)) {
		$sfconfig = SP()->options->get('sfconfig');
		$link = SP_STORE_URL.'/'.$sfconfig['image-uploads'].'/'.$rec->path.$rec->filename;
	}
	return $link;
}

function sp_plupload_do_post_save($content, $original) {
	$found = false;

	# nothing to do if no content
	if (empty($content)) return $content;

	# load into dom document
	$dom = new DomDocument();
	libxml_use_internal_errors(true); # block errros from not having full html doc

	if (function_exists('mb_convert_encoding')) {
		$dom->loadXML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
	} else {
		$dom->loadXML(htmlspecialchars_decode(utf8_decode(htmlentities($content, ENT_COMPAT, 'utf-8', false))));
	}

	libxml_clear_errors(); # turn errors back on

	foreach ($dom->getElementsByTagName('img') as $tag) {
		if ($tag->hasAttribute('data-upload')) {
			$width = $tag->getAttribute('data-width');
			$height = $tag->getAttribute('data-height');
			$tag->setAttribute('width', $width);
			$tag->setAttribute('height', $height);
			$tag->removeAttribute('data-upload');
			$tag->removeAttribute('data-width');
			$tag->removeAttribute('data-height');

			$found = true;
		}
	}
	if ($found) {
		# update content
		$content = utf8_decode($dom->saveXML($dom->documentElement));
	} else {
		$content = $original;
	}
	return $content;
}
