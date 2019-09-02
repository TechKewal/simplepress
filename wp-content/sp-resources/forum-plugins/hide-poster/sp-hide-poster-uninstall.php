<?php
/*
Simple:Press
Hide Posters plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the this plugin uninstall only
function sp_hide_poster_do_uninstall() {
	SP()->DB->execute('ALTER TABLE '.SPFORUMS.' DROP forum_hide_posters');
	SP()->DB->execute('ALTER TABLE '.SPTOPICS.' DROP topic_hide_posters');

	if (!empty(SP()->core->forumData['auths_map']['hide_posters'])) SP()->auths->delete('hide_posters');

    # delete our option
    SP()->options->delete('hide-poster');
}

function sp_hide_poster_do_deactivate() {
    SP()->auths->deactivate('hide_posters');
}

function sp_hide_poster_do_sp_deactivate() {
}

function sp_hide_poster_do_sp_uninstall() {
}

function sp_hide_poster_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'hide-poster/sp-hide-poster-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-hide-poster')."'>".__('Uninstall', 'sp-hide-poster').'</a>';
    }
	return $actionlink;
}
