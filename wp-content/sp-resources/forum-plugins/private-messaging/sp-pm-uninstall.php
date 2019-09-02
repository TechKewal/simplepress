<?php
/*
Simple:Press
PM plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for when the parent SP plugin is uninstalled
function sp_pm_do_sp_uninstall($admins) {
	wp_clear_scheduled_hook('sph_pm_cron');

	# remove any admin capabilities
	foreach ($admins as $admin) {
		$user = new WP_User($admin->user_id);
		$user->remove_cap('SPF Manage PM');
	}
}

# this uninstall function is for the pm plugin uninstall only
function sp_pm_do_uninstall() {
    # remove pm options to possible profile display control list if in use
    if (function_exists('sp_profile_display_control_remove_item')) {
        sp_profile_display_control_remove_item('options-pm-email');
        sp_profile_display_control_remove_item('options-pm-opt');
        sp_profile_display_control_remove_item('options-pm-size');
    }

	# remove any admin capabilities
	$admins = SP()->DB->table(SPMEMBERS, "admin=1");
	foreach ($admins as $admin) {
		$user = new WP_User($admin->user_id);
		$user->remove_cap('SPF Manage PM');
	}

    # remove our db stuff
	SP()->DB->execute('DROP TABLE IF EXISTS '.SPPMTHREADS);
	SP()->DB->execute('DROP TABLE IF EXISTS '.SPPMMESSAGES);
	SP()->DB->execute('DROP TABLE IF EXISTS '.SPPMRECIPIENTS);
	SP()->DB->execute('DROP TABLE IF EXISTS '.SPPMADVERSARIES);
	SP()->DB->execute('DROP TABLE IF EXISTS '.SPPMATTACHMENTS);
	SP()->DB->execute('DROP TABLE IF EXISTS '.SPPMBUDDIES);

	# remove the auth
	if (!empty(SP()->core->forumData['auths_map']['use_pm'])) SP()->auths->delete('use_pm');

    # delete our option table
    SP()->options->delete('pm');
	wp_clear_scheduled_hook('sph_pm_cron');

	# remove our profile tab/meuns
    SP()->profile->delete_tab(__('Buddies and Adversaries', 'sp-pm'));

    # remove our auto update stuff
    $up = SP()->meta->get('autoupdate', 'inbox');
    if ($up) SP()->meta->delete($up[0]['meta_id']);

    # make sure permalink include pm stuff
    SP()->spPermalinks->update_permalink(true);

	# remove glossary entries
	sp_remove_glossary_plugin('sp-pmessaging');
}

function sp_pm_do_deactivate() {
	# remove profile tabs/menus
   	SP()->profile->delete_tab(__('Buddies and Adversaries', 'sp-pm'));

	# remove the pm cron
    wp_clear_scheduled_hook('sph_pm_cron');

    # remove our auto update stuff
    $up = SP()->meta->get('autoupdate', 'inbox');
    if ($up) SP()->meta->delete($up[0]['meta_id']);

    # deactivation so make our auth not active
    SP()->auths->deactivate('use_pm');

    # make sure permalink include pm stuff
    SP()->spPermalinks->update_permalink(true);

	# remove glossary entries
	sp_remove_glossary_plugin('sp-pmessaging');
}

function sp_pm_do_sp_deactivate() {
    # remove any scheduled cron jobs
	wp_clear_scheduled_hook('sph_pm_cron');
}

function sp_pm_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'private-messaging/sp-pm-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-pm')."'>".__('Uninstall', 'sp-pm').'</a>';
        $url = SPADMINPLUGINS.'&amp;tab=plugin&amp;admin=sp_pm_admin_options&amp;save=sp_pm_admin_save_options&amp;form=1';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-pm')."'>".__('Options', 'sp-pm').'</a>';
    }
	return $actionlink;
}
