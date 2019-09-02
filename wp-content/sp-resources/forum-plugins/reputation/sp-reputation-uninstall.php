<?php
/*
Simple:Press
Reputation System plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the this plugin uninstall only
function sp_reputation_do_uninstall() {
	# remove our storage locations
	SP()->plugin->remove_storage('reputation');

    # delete our option
    SP()->options->delete('reputation');

    # remove our auths
    SP()->auths->delete('use_reputation');
    SP()->auths->delete('get_reputation');

	# remove our activity type
	SP()->activity->delete_type('reputation');

	# remove our column in sfmembers
	SP()->DB->execute('ALTER TABLE '.SPMEMBERS.' DROP reputation');

    # remove any of our activity
    SP()->activity->delete('type='.SPACTIVITY_REPUTATION);

	# remove any admin capabilities
	$admins = SP()->DB->table(SPMEMBERS, "admin=1");
	foreach ($admins as $admin) {
		$user = new WP_User($admin->user_id);
		$user->remove_cap('SPF Manage Reputation');
	}

    # remove any usermeta we have added
    SP()->DB->execute('DELETE FROM '.SPUSERMETA. 'WHERE meta_key="sp_reputation_daily"');
    SP()->DB->execute('DELETE FROM '.SPUSERMETA. 'WHERE meta_key="sp_reputation_posts"');
    SP()->DB->execute('DELETE FROM '.SPUSERMETA. 'WHERE meta_key="sp_reputation_registration"');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-reputation');
}

function sp_reputation_do_deactivate() {
	# remove glossary entries
	sp_remove_glossary_plugin('sp-reputation');

    # deactivation so make our auth not active
    SP()->auths->deactivate('use_reputation');
    SP()->auths->deactivate('get_reputation');
}

function sp_reputation_do_sp_deactivate() {
}

function sp_reputation_do_sp_uninstall() {
}

function sp_reputation_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'reputation/sp-reputation-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-reputation')."'>".__('Uninstall', 'sp-reputation').'</a>';
        $url = SPADMINPLUGINS.'&amp;tab=plugin&amp;admin=sp_reputation_admin_options&amp;save=sp_reputation_admin_save_options&amp;form=1';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-reputation')."'>".__('Options', 'sp-reputation').'</a>';
    }
	return $actionlink;
}
