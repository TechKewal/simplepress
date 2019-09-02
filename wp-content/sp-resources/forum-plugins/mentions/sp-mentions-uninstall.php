<?php
/*
Simple:Press
Mentions plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the topic description plugin uninstall only
function sp_mentions_do_uninstall() {
    # delete our option
    SP()->options->delete('mentions');

    # delete all activity records
    SP()->activity->delete('type='.SPACTIVITY_MENTIONED);

	SP()->activity->delete_type('mentions');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-mentions');
}

function sp_mentions_do_deactivate() {
	# remove glossary entries
	sp_remove_glossary_plugin('sp-mentions');
}

function sp_mentions_do_sp_deactivate() {
}

function sp_mentions_do_sp_uninstall() {
}

function sp_mentions_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'mentions/sp-mentions-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-mentions')."'>".__('Uninstall', 'sp-mentions').'</a>';
        $url = SPADMINOPTION.'&amp;tab=members';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-mentions')."'>".__('Options', 'sp-mentions').'</a>';
    }
	return $actionlink;
}
