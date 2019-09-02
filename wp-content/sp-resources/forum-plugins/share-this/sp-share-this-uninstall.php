<?php
/*
Simple:Press
Share This plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the topic description plugin uninstall only
function sp_share_this_do_uninstall() {
    # delete our option
    SP()->options->delete('share-this');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-sharethis');
}

function sp_share_this_do_deactivate() {
	# remove glossary entries
	sp_remove_glossary_plugin('sp-sharethis');
}

function sp_share_this_do_sp_deactivate() {
}

function sp_share_this_do_sp_uninstall() {
}

function sp_share_this_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'share-this/sp-share-this-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-share-this')."'>".__('Uninstall', 'sp-share-this').'</a>';
        $url = SPADMINCOMPONENTS.'&amp;tab=plugin&amp;admin=sp_share_this_admin_options&amp;save=sp_share_this_admin_save_options&amp;form=1&amp;id=shareopt';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-ban')."'>".__('Options', 'sp-ban').'</a>';
    }
	return $actionlink;
}
