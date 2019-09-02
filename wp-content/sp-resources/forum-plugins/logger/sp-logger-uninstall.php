<?php
/*
Simple:Press
Event Logger plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the this plugin uninstall only
function sp_logger_do_uninstall() {
    # delete our option
    SP()->options->delete('logger');

    SP()->DB->execute('DROP TABLE IF EXISTS '.SPEVENTLOG);

	# remove any admin capabilities
	$admins = SP()->DB->table(SPMEMBERS, "admin=1");
	foreach ($admins as $admin) {
		$user = new WP_User($admin->user_id);
		$user->remove_cap('SPF Manage Logger');
	}

	# remove glossary entries
	sp_remove_glossary_plugin('sp-eventlog');
}

function sp_logger_do_deactivate() {
	# remove glossary entries
	sp_remove_glossary_plugin('sp-eventlog');
}

function sp_logger_do_sp_deactivate() {
}

function sp_logger_do_sp_uninstall() {
	SP()->DB->execute('DROP TABLE IF EXISTS '.SPEVENTLOG);
}

function sp_logger_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'logger/sp-logger-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-logger')."'>".__('Uninstall', 'sp-logger').'</a>';
        $url = SPADMINPLUGINS.'&amp;tab=plugin&amp;admin=sp_logger_admin_options&amp;save=sp_logger_admin_options_save&amp;form=1';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-logger')."'>".__('Options', 'sp-logger').'</a>';
    }
	return $actionlink;
}
