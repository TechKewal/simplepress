<?php
/*
Simple:Press
Ranks Info plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the topic description plugin uninstall only
function sp_rank_info_do_uninstall() {
    # delete our option
    SP()->options->delete('rank-info');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-rankinfo');
}

function sp_rank_info_do_deactivate() {
	# remove glossary entries
	sp_remove_glossary_plugin('sp-rankinfo');
}

function sp_rank_info_do_sp_deactivate() {
}

function sp_rank_info_do_sp_uninstall() {
}

function sp_rank_info_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'rank-info/sp-rank-info-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-rank-info')."'>".__('Uninstall', 'sp-rank-info').'</a>';
        $url = SPADMINCOMPONENTS.'&amp;tab=forumranks';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-rank-info')."'>".__('Options', 'sp-rank-info').'</a>';
    }
	return $actionlink;
}
