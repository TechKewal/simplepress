<?php
/*
Simple:Press
Name plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the topic description plugin uninstall only
function sp_autolink_do_uninstall() {
    # delete our option
    SP()->options->delete('autolink');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-autolink');
}

function sp_autolink_do_deactivate() {
	# remove glossary entries
	sp_remove_glossary_plugin('sp-autolink');
}

function sp_autolink_do_sp_deactivate() {
}

function sp_autolink_do_sp_uninstall() {
}

function sp_autolink_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'autolink/sp-autolink-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-autolink')."'>".__('Uninstall', 'sp-autolink').'</a>';
        $url = SPADMINCOMPONENTS.'&amp;tab=plugin&amp;admin=sp_autolink_admin_form&amp;save=sp_autolink_admin_save&amp;form=1';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-autolink')."'>".__('Options', 'sp-autolink').'</a>';
    }
	return $actionlink;
}
