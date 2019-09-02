<?php
/*
Simple:Press
Membership Subscribe plugin uninstall routine
$LastChangedDate: 2013-04-17 19:24:03 -0700 (Wed, 17 Apr 2013) $
$Rev: 10182 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the this plugin uninstall only
function sp_membership_subscribe_do_uninstall() {
    # delete our option
    SP()->options->delete('membership-subscribe');
}

function sp_membership_subscribe_do_deactivate() {
}

function sp_membership_subscribe_do_sp_deactivate() {
}

function sp_membership_subscribe_do_sp_uninstall() {
}

function sp_membership_subscribe_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'membership-subscribe/sp-membership-subscribe-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-membership-subscribe')."'>".__('Uninstall', 'sp-membership-subscribe').'</a>';
    }
	return $actionlink;
}
