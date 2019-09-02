<?php
/*
Simple:Press
Who's Online plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_online_do_uninstall() {
	# delete our auth
	SP()->auths->delete('view_online_activity');

    # make sure permalink include pm stuff
    SP()->spPermalinks->update_permalink(true);
}

function sp_online_do_deactivate() {
}

function sp_online_do_sp_deactivate() {
}

function sp_online_do_sp_uninstall() {
}

function sp_online_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'online/sp-online-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'spwo')."'>".__('Uninstall', 'spwo').'</a>';
    }
	return $actionlink;
}
