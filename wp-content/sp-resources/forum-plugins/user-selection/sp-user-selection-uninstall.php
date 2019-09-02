<?php
/*
User Selection plugin uninstall
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_user_selection_do_uninstall(){
	# remove settings
	SP()->options->delete('user-selection');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-language');
}

function sp_user_selection_do_deactivate() {
	# remove glossary entries
	sp_remove_glossary_plugin('sp-language');
}

function sp_user_selection_do_uninstall_option($actionlink, $plugin) {
	if ($plugin == 'user-selection/sp-user-selection-plugin.php') {
		$url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
		$actionlink.= "&nbsp;&nbsp;<a href='".$url."' title='".__('Uninstall this plugin', 'sp-usel')."'>".__('Uninstall', 'sp-usel')."</a>";
        $url = SPADMINTOOLBOX.'&amp;tab=plugin&amp;admin=sp_user_selection_admin_options&amp;save=sp_user_selection_admin_save_options&amp;form=1';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-usel')."'>".__('Options', 'sp-usel').'</a>';
	}
	return $actionlink;
}
