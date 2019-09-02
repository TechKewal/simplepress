<?php
/*
Simple:Press
Post As plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the topic description plugin uninstall only
function sp_post_as_do_uninstall() {
    # delete our option
    SP()->options->delete('post-as');

	# remove the auth
	if (!empty(SP()->core->forumData['auths_map']['post_as_user'])) SP()->auths->delete('post_as_user');
}

function sp_post_as_do_deactivate() {
    SP()->auths->deactivate('post_as_user');
}

function sp_post_as_do_sp_deactivate() {
}

function sp_post_as_do_sp_uninstall() {
}

function sp_post_as_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'post-as/sp-post-as-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-post-as')."'>".__('Uninstall', 'sp-post-as').'</a>';
    }
	return $actionlink;
}
