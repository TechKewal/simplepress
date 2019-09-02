<?php
/*
Simple:Press
Uploads Viewer Plugin components Routine
$LastChangedDate: 2018-10-21 05:53:39 -0500 (Sun, 21 Oct 2018) $
$Rev: 15761 $
*/

function sp_uploads_viewer_do_load_js($footer) {
	$script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPUVSCRIPT.'jqueryFileTree.js' : SPUVSCRIPT.'jqueryFileTree.min.js';
	SP()->plugin->enqueue_script('spuvft', $script, array('jquery'), false, $footer);

	$script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPUVSCRIPT.'sp-uploads-viewer.js' : SPUVSCRIPT.'sp-uploads-viewer.min.js';
	SP()->plugin->enqueue_script('spuv', $script, array('jquery'), false, $footer);
}

function sp_uploads_viewer_do_head() {
	$css = SP()->theme->find_css(SPUVCSS, 'sp-uploads-viewer.css', 'sp-uploads-viewer.spcss');
	SP()->plugin->enqueue_style('sp-uploads-viewer', $css);
}

function sp_uploads_viewer_do_button($out, $type, $uploadImages, $uploadMedia, $uploadFiles) {
	$user_slug = sp_create_slug(SP()->user->thisUser->user_login, false);
	$sfconfig = SP()->options->get('sfconfig');
	$uploads_path = str_replace('\\', '/', SP_STORE_DIR.'/');

	if (empty($type) || $type == 'edit') {
		if (empty(SP()->forum->view->thisForum)) {
			if (empty(SP()->forum->view->thisTopic)) {
				$fid = '';
			} else {
				$fid = SP()->forum->view->thisTopic->forum_id;
			}
		} else {
			$fid = SP()->forum->view->thisForum->forum_id;
		}

		if ($uploadImages) {
			$uploads_link = $sfconfig['image-uploads'].'/'.$user_slug.'/';
			$checkPath = $uploads_path.$uploads_link;
			if (file_exists($checkPath)) {
				$toolTip = __('View Existing Image Uploads', 'sp-uv');
				$site = wp_nonce_url(SPAJAXURL."uploads-viewer-view&amp;fid=$fid&amp;type=images", 'uploads-viewer-view');
				$out.= "<a rel='nofollow' class='spUploadsViewerButton spButton spUploadsOpenDialog' title='$toolTip' data-site='$site' data-label='$toolTip' data-width='400' data-height='0' data-align='center'>".$toolTip.'</a>';
			}
		}

		if ($uploadMedia) {
			$uploads_link = $sfconfig['media-uploads'].'/'.$user_slug.'/';
			$checkPath = $uploads_path.$uploads_link;
			if (file_exists($checkPath)) {
				$toolTip = __('View Existing Media Uploads', 'sp-uv');
				$site = wp_nonce_url(SPAJAXURL."uploads-viewer-view&amp;fid=$fid&amp;type=media", 'uploads-viewer-view');
				$out.= "<a rel='nofollow' class='spUploadsViewerButton spButton spUploadsOpenDialog' title='$toolTip' data-site='$site' data-label='$toolTip' data-width='400' data-height='0' data-align='center'>".$toolTip.'</a>';
			}
		}

		if ($uploadFiles) {
			$uploads_link = $sfconfig['file-uploads'].'/'.$user_slug.'/';
			$checkPath = $uploads_path.$uploads_link;
			if (file_exists($checkPath)) {
				$toolTip = __('View Existing File Uploads', 'sp-uv');
				$site = wp_nonce_url(SPAJAXURL."uploads-viewer-view&amp;fid=$fid&amp;type=files", 'uploads-viewer-view');
				$out.= "<a rel='nofollow' class='spUploadsViewerButton spButton spUploadsOpenDialog' title='$toolTip' data-site='$site' data-label='$toolTip' data-width='400' data-height='0' data-align='center'>".$toolTip.'</a>';
			}
		}
	}

	return $out;
}

function sp_uploads_viewer_do_post_create($newpost) {
	# any attachments inserted?
	if (empty($_POST['sp_uv_count'])) return;

    require_once SPPLUPLIBDIR.'sp-plupload-components.php';

    sp_plupload_config(SP()->user->thisUser);
    global $plup;

	$sp_uv_count = SP()->filters->integer($_POST['sp_uv_count']);
	
    $sfconfig = SP()->options->get('sfconfig');

    for ($index = 1; $index <= $sp_uv_count; $index++) {
		# get the filename, upload type and verify permission to upload
    	$attachment = SP()->filters->str($_POST['sp_uvfile_name_'.$index]);
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
    	$path = SP()->filters->str($_POST['sp_uvfile_path_'.$index]);
    	$file = $path.$file_name;
		if (!file_exists($file)) continue;

        # just get relative patht to storage location
        $path = explode($match.'/', $path);

        # get size of attachment
    	$size = @filesize($file);

    	# save the attachment
        SP()->DB->execute('INSERT INTO '.SPPOSTATTACHMENTS." (post_id, topic_id, type, path, filename, size) VALUES (".$newpost['postid'].", ".$newpost['topicid'].", '$typenow', '$path[1]', '$file_name', $size)");
   	}
}
