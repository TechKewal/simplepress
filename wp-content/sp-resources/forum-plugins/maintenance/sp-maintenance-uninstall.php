<?php
/*
Simple:Press
Maintenance Mode plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the topic description plugin uninstall only
function sp_maintenance_do_uninstall() {
    # delete our option
    SP()->options->delete('maintenance');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-maintenance');
}

function sp_maintenance_do_deactivate() {
	# remove glossary entries
	sp_remove_glossary_plugin('sp-maintenance');
}

function sp_maintenance_do_sp_deactivate() {
}

function sp_maintenance_do_sp_uninstall() {
}

function sp_maintenance_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'maintenance/sp-maintenance-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-maintenance')."'>".__('Uninstall', 'sp-maintenance').'</a>';
        $url = SPADMINTOOLBOX.'&amp;tab=plugin&amp;admin=sp_maintenance_admin_options&amp;save=sp_maintenance_admin_save_options&amp;form=1';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-maintenance')."'>".__('Options', 'sp-maintenance').'</a>';
    }
	return $actionlink;
}
