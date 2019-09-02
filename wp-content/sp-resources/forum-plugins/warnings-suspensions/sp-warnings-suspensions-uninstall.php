<?php
/*
Simple:Press
Name plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the this plugin uninstall only
function sp_warnings_suspensions_do_uninstall() {
	# remove any admin capabilities
	$admins = SP()->DB->table(SPMEMBERS, "admin=1");
	foreach ($admins as $admin) {
		$user = new WP_User($admin->user_id);
		$user->remove_cap('SPF Manage Warnings');
	}

	# remove glossary entries
	sp_remove_glossary_plugin('sp-warnings');

	wp_clear_scheduled_hook('sph_warnings_cron');
}

function sp_warnings_suspensions_do_deactivate() {
	# remove glossary entries
	sp_remove_glossary_plugin('sp-warnings');

	wp_clear_scheduled_hook('sph_warnings_cron');
}

function sp_warnings_suspensions_do_sp_deactivate() {
	wp_clear_scheduled_hook('sph_warnings_cron');
}

function sp_warnings_suspensions_do_sp_uninstall() {
	# remove any admin capabilities
	foreach ($admins as $admin) {
		$user = new WP_User($admin->user_id);
		$user->remove_cap('SPF Manage Warnings');
	}

	wp_clear_scheduled_hook('sph_warnings_cron');
}

function sp_warnings_suspensions_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'warnings-suspensions/sp-warnings-suspensions-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-warnings-suspensions')."'>".__('Uninstall', 'sp-warnings-suspensions').'</a>';
        $url = SPADMINPLUGINS.'&amp;tab=plugin&amp;admin=sp_warnings_suspensions_admin_options&amp;save=sp_warnings_suspensions_admin_save_options&amp;form=1';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-warnings-suspensions')."'>".__('Options', 'sp-warnings-suspensions').'</a>';
    }
	return $actionlink;
}
