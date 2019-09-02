<?php
/*
Simple:Press
search by user plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the this plugin uninstall only
function sp_search_by_user_do_uninstall() {
    # delete our option
    SP()->options->delete('search-by-user');
}

function sp_search_by_user_do_deactivate() {
}

function sp_search_by_user_do_sp_deactivate() {
}

function sp_search_by_user_do_sp_uninstall() {
}

function sp_search_by_user_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'search-by-user/sp-search-by-user-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-search-by-user')."'>".__('Uninstall', 'sp-search-by-user').'</a>';
    }
	return $actionlink;
}
