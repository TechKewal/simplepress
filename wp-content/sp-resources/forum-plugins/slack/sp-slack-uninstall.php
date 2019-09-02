<?php
/*
Simple:Press
slack integration plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the this plugin uninstall only
function sp_slack_do_uninstall() {
    # delete our option
    SP()->options->delete('slack');
	# remove glossary entries
	sp_remove_glossary_plugin('sp-slack');
}

function sp_slack_do_deactivate() {
	# remove glossary entries
	sp_remove_glossary_plugin('sp-slack');
}

function sp_slack_do_sp_deactivate() {
}

function sp_slack_do_sp_uninstall() {
}

function sp_slack_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'slack/sp-slack-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-slack')."'>".__('Uninstall', 'sp-slack').'</a>';
        $url = SPADMINCOMPONENTS.'&amp;tab=plugin&amp;admin=sp_slack_admin_options&amp;save=sp_slack_admin_save_options&amp;form=1';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-slack')."'>".__('Options', 'sp-slack').'</a>';
    }
	return $actionlink;
}
