<?php
/*
Simple:Press
Post Anonymously plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the this plugin uninstall only
function sp_anonymous_do_uninstall() {
     # remove our option
    sp_delete_option('anonymous');

	# remove our auth
	sp_delete_auth('post_anonymous');

	# remove our user activity
    SP()->activity->delete('type='.SPACTIVITY_ANON);

	# remove our activity type
	SP()->activity->delete_type('anonymous poster');

}

function sp_anonymous_do_deactivate() {
    SP()->auths->deactivate('post_anonymous');
}

function sp_anonymous_do_sp_deactivate() {
}

function sp_anonymous_do_sp_uninstall() {
}

function sp_anonymous_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'anonymous/sp-anonymous-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-anonymous')."'>".__('Uninstall', 'sp-anonymous').'</a>';
    }
	return $actionlink;
}
