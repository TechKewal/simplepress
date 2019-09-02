<?php
/*
Simple:Press
buddypress plugin uninstall routine
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the topic description plugin uninstall only
function sp_buddypress_do_uninstall() {
    # delete our option
    SP()->options->delete('buddypress');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-buddypress');
}

function sp_buddypress_do_deactivate() {
	# remove glossary entries
	sp_remove_glossary_plugin('sp-buddypress');
}

function sp_buddypress_do_sp_deactivate() {
}

function sp_buddypress_do_sp_uninstall() {
}

function sp_buddypress_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'buddypress/sp-buddypress-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-buddypress')."'>".__('Uninstall', 'sp-buddypress').'</a>';
        $url = SPADMINCOMPONENTS.'&amp;tab=plugin&amp;admin=sp_buddypress_admin_options&amp;save=sp_buddypress_admin_save_options&amp;form=1';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-buddypress')."'>".__('Options', 'sp-buddypress').'</a>';
    }
	return $actionlink;
}
