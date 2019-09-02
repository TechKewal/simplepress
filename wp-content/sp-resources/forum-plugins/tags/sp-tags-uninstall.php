<?php
/*
Simple:Press
Tags plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for when the parent SP plugin is uninstalled
function sp_tags_do_sp_uninstall($admins) {
	# remove any admin capabilities
	foreach ($admins as $admin) {
		$user = new WP_User($admin->user_id);
		$user->remove_cap('SPF Manage Tags');
	}
}

# this uninstall function is for the tags plugin uninstall only
function sp_tags_do_uninstall() {
	# remove any admin capabilities
	$admins = SP()->DB->table(SPMEMBERS, "admin=1");
	foreach ($admins as $admin) {
		$user = new WP_User($admin->user_id);
		$user->remove_cap('SPF Manage Tags');
	}

    # remove our db stuff
	SP()->DB->execute('DROP TABLE IF EXISTS '.SPTAGS);
	SP()->DB->execute('DROP TABLE IF EXISTS '.SPTAGSMETA);
	SP()->DB->execute('ALTER TABLE '.SPFORUMS.' DROP use_tags');

    SP()->auths->delete('edit_tags');

   	# delete our option table
    SP()->options->delete('tags');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-tags');
}

function sp_tags_do_deactivate() {
    SP()->auths->deactivate('edit_tags');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-tags');
}

function sp_tags_do_sp_deactivate() {
}

function sp_tags_do_uninstall_option($actionlink, $plugin) {
	if ($plugin == 'tags/sp-tags-plugin.php') {
        $url = SPADMINPLUGINS."&amp;action=uninstall&amp;plugin=$plugin&amp;sfnonce=".wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-tags')."'>".__('Uninstall', 'sp-tags')."</a>";
        $url = SPADMINPLUGINS.'&amp;tab=plugin&amp;admin=sp_tags_admin_options&amp;save=sp_tags_admin_save_options&amp;form=1';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-tags')."'>".__('Options', 'sp-tags')."</a>";
    }
	return $actionlink;
}
