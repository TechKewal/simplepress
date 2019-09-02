<?php
/*
Simple:Press
Plupload plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_plupload_do_install() {
	$olduploads = SP()->options->get('sfuploads');
	$uploads = SP()->options->get('spPlupload');
	if (empty($olduploads) && empty($uploads)) {
		$sql = "
			CREATE TABLE IF NOT EXISTS ".SPPOSTATTACHMENTS." (
				attachment_id BIGINT(20) NOT NULL auto_increment,
				post_id BIGINT(20) NOT NULL,
				topic_id BIGINT(20) NOT NULL,
                type VARCHAR(10) NOT NULL,
				path VARCHAR(250) NOT NULL,
				filename VARCHAR(100) NOT NULL,
				size INT NOT NULL,
				PRIMARY KEY (attachment_id),
                KEY post_id_idx (post_id),
                KEY topic_id_idx (topic_id)
			) ".SP()->DB->charset().";";
		SP()->DB->execute($sql);

		$tables = SP()->options->get('installed_tables');
		if ($tables) {
			if (!in_array(SPPOSTATTACHMENTS, $tables)) $tables[] = SPPOSTATTACHMENTS;
			SP()->options->update('installed_tables', $tables);
		}

		$uploads = array();
		$uploads['showinserted'] = true;
		$uploads['imgquality'] = 100;
		$uploads['thumbquality'] = 100;
		$uploads['imagetypes'] = 'jpg, jpeg, gif, png';
		$uploads['imagemaxsize'] = 51200;
		$uploads['imagemaxwidth'] = 0;
		$uploads['imagemaxheight'] = 0;
    	$uploads['imageinsert'] = true;
    	$uploads['mediainsert'] = true;
    	$uploads['fileinsert'] = false;
		$uploads['mediatypes'] = 'swf, dcr, mov, qt, mpg, mp3, mp4, mpeg, avi, wmv, wm, asf, asx, wmx, wvx, rm, ra, ram';
		$uploads['mediamaxsize'] = 0;
		$uploads['mediawidth'] = 320;
		$uploads['mediaheight'] = 240;
		$uploads['filetypes'] = 'txt, rtf, doc, pdf';
		$uploads['filemaxsize'] = 51200;
		$uploads['prohibited'] = 'php, php3, php5, js, html, htm, phtml, asp, aspx, ascx, jsp, cfm, cfc, pl, bat, exe, dll, reg, cgi, sh, py';
        $uploads['useforphotos'] = true;
        $uploads['showthumbs'] = true;
        $uploads['lang'] = sp_plupload_get_language();

        $uploads['dbversion'] = SPPLUPDBVERSION;
		SP()->options->add('spPlupload', $uploads);

		# storage location
		$newpath = SP()->plugin->add_storage('forum-image-uploads', 'image-uploads');
		$newpath = SP()->plugin->add_storage('forum-media-uploads', 'media-uploads');
		$newpath = SP()->plugin->add_storage('file-image-uploads', 'file-uploads');
    } elseif (empty($uploads)) { # upgrade from pre sp 5.0 when pm was in core
		$sql = "
			CREATE TABLE IF NOT EXISTS ".SPPOSTATTACHMENTS." (
				attachment_id BIGINT(20) NOT NULL auto_increment,
				post_id BIGINT(20) NOT NULL,
				topic_id BIGINT(20) NOT NULL,
                type VARCHAR(10) NOT NULL,
				path VARCHAR(250) NOT NULL,
				filename VARCHAR(100) NOT NULL,
				size INT NOT NULL,
				PRIMARY KEY (attachment_id),
                KEY post_id_idx (post_id),
                KEY topic_id_idx (topic_id)
			) ".SP()->DB->charset().";";
		SP()->DB->execute($sql);

		$tables = SP()->options->get('installed_tables');
		if ($tables) {
			if (!in_array(SPPOSTATTACHMENTS, $tables)) $tables[] = SPPOSTATTACHMENTS;
			SP()->options->update('installed_tables', $tables);
		}

		$olduploads['showinserted'] = true;
    	$olduploads['imageinsert'] = true;
		$olduploads['imgquality'] = 100;
		$olduploads['thumbquality'] = 100;
		$olduploads['imagemaxwidth'] = 0;
		$olduploads['imagemaxheight'] = 0;
    	$olduploads['mediainsert'] = true;
		$olduploads['mediawidth'] = 320;
		$olduploads['mediaheight'] = 240;
        $olduploads['useforphotos'] = true;
        $olduploads['lang'] = sp_plupload_get_language();
		SP()->options->add('spPlupload', $olduploads);

		SP()->options->delete('sfuploads');
    }

    # add profile tabs
    SP()->profile->add_tab('Attachments', 0, 1, 'manage_attachments');
    SP()->profile->add_menu('Attachments', 'Image Uploads', SPPLUPFORMSDIR.'sp-plupload-images-form.php', 0, 1, 'upload_images');
    SP()->profile->add_menu('Attachments', 'Media Uploads', SPPLUPFORMSDIR.'sp-plupload-media-form.php', 0, 1, 'upload_media');
    SP()->profile->add_menu('Attachments', 'File Uploads', SPPLUPFORMSDIR.'sp-plupload-files-form.php', 0, 1, 'upload_files');

    # add a new permission into the auths table
	SP()->auths->add('upload_images', __('Can upload images in posts', 'sp-plup'), 1, 1, 0, 0, 8);
	SP()->auths->add('upload_media', __('Can upload media in posts', 'sp-plup'), 1, 1, 0, 0, 8);
	SP()->auths->add('upload_files', __('Can upload files in posts', 'sp-plup'), 1, 1, 0, 0, 8);
	SP()->auths->add('upload_signatures', __('Can upload signature images', 'sp-plup'), 1, 1, 0, 0, 8);
   	SP()->auths->add('download_attachments', __('Can download other file type attachments', 'sp-plup'), 1, 0, 0, 0, 2);
	SP()->auths->add('manage_attachments', __('Can manage their uploaded attachments in profile', 'sp-plup'), 1, 1, 0, 0, 8);

    # activation so make our auth active
    SP()->auths->activate('upload_images');
    SP()->auths->activate('upload_media');
    SP()->auths->activate('upload_files');
    SP()->auths->activate('upload_signatures');
    SP()->auths->activate('download_attachments');
    SP()->auths->activate('manage_attachments');

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# sp reactivated.
function sp_plupload_do_sp_activate() {
    # add profile tabs
    SP()->profile->add_tab('Attachments', 0, 1, 'manage_attachments');
    SP()->profile->add_menu('Attachments', 'Image Uploads', SPPLUPFORMSDIR.'sp-plupload-images-form.php', 0, 1, 'upload_images');
    SP()->profile->add_menu('Attachments', 'Media Uploads', SPPLUPFORMSDIR.'sp-plupload-media-form.php', 0, 1, 'upload_media');
    SP()->profile->add_menu('Attachments', 'File Uploads', SPPLUPFORMSDIR.'sp-plupload-files-form.php', 0, 1, 'upload_files');

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

function sp_plupload_do_reset_permissions() {
	SP()->auths->add('upload_images', __('Can upload images in posts', 'sp-plup'), 1, 1, 0, 0, 8);
	SP()->auths->add('upload_media', __('Can upload media in posts', 'sp-plup'), 1, 1, 0, 0, 8);
	SP()->auths->add('upload_files', __('Can upload files in posts', 'sp-plup'), 1, 1, 0, 0, 8);
	SP()->auths->add('upload_signatures', __('Can upload signature images', 'sp-plup'), 1, 1, 0, 0, 8);
   	SP()->auths->add('download_attachments', __('Can download other file type attachments', 'sp-plup'), 1, 0, 0, 0, 2);
	SP()->auths->add('manage_attachments', __('Can manage their uploaded attachments in profile', 'sp-plup'), 1, 1, 0, 0, 8);
}

function sp_plupload_get_language() {
	$lang = get_locale();
	if ($lang == 'en' || $lang == 'en_GB' || $lang == 'en_US') return 'en';

	# attempt to make a match
	require_once SP_PLUGIN_DIR.'/admin/library/sp-languages.php';

	$langCode = 'en';
	foreach ($langSets as $code => $wpCode) {
		if ($wpCode['wpCode'] == $lang) {
			$langCode = $code;
			break;
		}
	}
	return $langCode;
}
