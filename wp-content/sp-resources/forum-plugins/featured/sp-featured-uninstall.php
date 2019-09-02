<?php
/*
Simple:Press
Featured Topics and Posts Plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the topic description plugin uninstall only
function sp_featured_do_uninstall() {
    # delete our option
    SP()->options->delete('featured');

    # remove our sfmeta
	SP()->meta->delete(0, 'featured');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-featured');
}

function sp_featured_do_deactivate() {
	# remove glossary entries
	sp_remove_glossary_plugin('sp-featured');
}

function sp_featured_do_sp_deactivate() {
}

function sp_featured_do_sp_uninstall() {
}

function sp_featured_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'featured/sp-featured-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-featured')."'>".__('Uninstall', 'sp-featured').'</a>';
        $url = SPADMINCOMPONENTS.'&amp;tab=plugin&amp;admin=sp_featured_admin_options&amp;save=sp_featured_admin_save_options&amp;form=1';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-featured')."'>".__('Options', 'sp-featured').'</a>';
    }
	return $actionlink;
}
