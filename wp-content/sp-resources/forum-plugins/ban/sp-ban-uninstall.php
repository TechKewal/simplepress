<?php
/*
Simple:Press
Ban plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the topic description plugin uninstall only
function sp_ban_do_uninstall() {
    # delete our option and bans
    SP()->options->delete('ban');
    SP()->options->delete('banned_ips');
    SP()->options->delete('banned_ip_ranges');
    SP()->options->delete('banned_hostnames');
    SP()->options->delete('banned_agents');
    SP()->options->delete('banned_users');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-ban');
}

function sp_ban_do_deactivate() {
	# remove glossary entries
	sp_remove_glossary_plugin('sp-ban');
}

function sp_ban_do_sp_deactivate() {
}

function sp_ban_do_sp_uninstall() {
}

function sp_ban_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'ban/sp-ban-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-ban')."'>".__('Uninstall', 'sp-ban').'</a>';
        $url = SPADMINUSER.'&amp;tab=plugin&amp;admin=sp_ban_admin&amp;save=sp_ban_admin_save&amp;form=0&amp;id=banpanel';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-ban')."'>".__('Options', 'sp-ban').'</a>';
    }
	return $actionlink;
}
