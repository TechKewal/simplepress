<?php
/*
Simple:Press
Plupload plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the pm plugin uninstall only
function sp_plupload_do_uninstall() {
	# remove our storage locations
	SP()->plugin->remove_storage('image-uploads');
	SP()->plugin->remove_storage('media-uploads');
	SP()->plugin->remove_storage('file-uploads');

	# remove the auths
	if (!empty(SP()->core->forumData['auths_map']['upload_images'])) SP()->auths->delete('upload_images');
	if (!empty(SP()->core->forumData['auths_map']['upload_media'])) SP()->auths->delete('upload_media');
	if (!empty(SP()->core->forumData['auths_map']['upload_files'])) SP()->auths->delete('upload_files');
	if (!empty(SP()->core->forumData['auths_map']['download_attachments'])) SP()->auths->delete('download_attachments');
	if (!empty(SP()->core->forumData['auths_map']['manage_attachments'])) SP()->auths->delete('manage_attachments');

    SP()->DB->execute('DROP TABLE IF EXISTS '.SPPOSTATTACHMENTS);

    SP()->profile->delete_tab('Attachments');

    # delete our option table
    SP()->options->delete('spPlupload');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-plupload');
}

function sp_plupload_do_deactivate() {
    # deactivation so make our auth not active
    SP()->auths->deactivate('upload_images');
    SP()->auths->deactivate('upload_media');
    SP()->auths->deactivate('upload_files');
    SP()->auths->deactivate('upload_signatures');
    SP()->auths->deactivate('download_attachments');
    SP()->auths->deactivate('manage_attachments');

    SP()->profile->delete_tab('Attachments');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-plupload');
}

function sp_plupload_do_sp_deactivate() {
}

function sp_plupload_do_sp_uninstall() {
}

function sp_plupload_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'plupload/sp-plupload-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".esc_attr(__('Uninstall this plugin', 'sp-plup'))."'>".__('Uninstall', 'sp-plup').'</a>';
        $url = SPADMINCOMPONENTS.'&amp;tab=plugin&amp;admin=sp_plupload_admin_options&amp;save=sp_plupload_admin_save_options&amp;form=1';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".esc_attr(__('Options', 'sp-plup'))."'>".__('Options', 'sp-plup').'</a>';
    }
	return $actionlink;
}
