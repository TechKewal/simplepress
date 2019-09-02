<?php
/*
Simple:Press
Plupload plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_plupload_do_upgrade_check() {
    if (!SP()->plugin->is_active('plupload/sp-plupload-plugin.php')) return;

	$uploads = SP()->options->get('spPlupload');

    $db = empty($uploads['dbversion']) ? 0 : $uploads['dbversion'];

    # quick bail check
    if ($db == SPPLUPDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades
    if ($db < 1) {
        # add a new permission into the auths table
    	SP()->auths->add('download_attachments', __('Can download other file type attachments', 'sp-plup'), 1, 0, 0, 0, 2);
        $uploads['lang'] = 'en';
        $uploads['useforphotos'] = true;
    }

    if ($db < 2) {
        # download other file attachments should not ignore guests
        SP()->DB->execute('UPDATE '.SPAUTHS.' SET ignored = 0 WHERE auth_name = "download_attachments"');
    }

    if ($db < 3) {
	   SP()->DB->execute('UPDATE '.SPAUTHS." SET auth_cat=8 WHERE auth_name='upload_images'");
	   SP()->DB->execute('UPDATE '.SPAUTHS." SET auth_cat=8 WHERE auth_name='upload_media'");
	   SP()->DB->execute('UPDATE '.SPAUTHS." SET auth_cat=8 WHERE auth_name='upload_files'");
	   SP()->DB->execute('UPDATE '.SPAUTHS." SET auth_cat=8 WHERE auth_name='upload_signatures'");
	   SP()->DB->execute('UPDATE '.SPAUTHS." SET auth_cat=1 WHERE auth_name='download_attachments'");
    }

    if ($db < 4) {
    	SP()->auths->add('manage_attachments', __('Can manage uploaded attachments in profile', 'sp-plup'), 1, 1, 0, 0, 8);

   	    SP()->profile->add_tab('Attachments', 0, 1, 'manage_attachments');
        SP()->profile->add_menu('Attachments', 'Image Uploads', SPPLUPFORMSDIR.'sp-plupload-images-form.php', 0, 1, 'upload_images');
        SP()->profile->add_menu('Attachments', 'Media Uploads', SPPLUPFORMSDIR.'sp-plupload-media-form.php', 0, 1, 'upload_media');
        SP()->profile->add_menu('Attachments', 'File Uploads', SPPLUPFORMSDIR.'sp-plupload-files-form.php', 0, 1, 'upload_files');
    }

    if ($db < 5) {
    	$uploads['fileinsert'] = false;
    }

    if ($db < 6) {
    	unset($uploads['thumbsize']);
    }

    if ($db < 7) {
    	$uploads['showthumbs'] = true;
    }

    if ($db < 8) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

	if ($db < 9) {
		# move photos to standard usermeta record
		$old = get_user_meta(SP()->user->thisUser->ID, 'sp_profile_photos', true);
		if (!empty($old)) {
			$new = get_user_meta(SP()->user->thisUser->ID, 'photos', true);
			if (empty($new)) $new = array();
			foreach($old as $photo) {
				$url = $photo['path'].$photo['file'];
				$new[] = $url;
			}
			update_user_meta(SP()->user->thisUser->ID, 'photos', $new);
		}
	}

    # save data
    $uploads['dbversion'] = SPPLUPDBVERSION;
    SP()->options->update('spPlupload', $uploads);
}
