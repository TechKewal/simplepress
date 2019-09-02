<?php
/*
Simple:Press
Birthdays plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the topic description plugin uninstall only
function sp_birthdays_do_uninstall() {
    # delete our option
    SP()->options->delete('birthdays');

	wp_clear_scheduled_hook('sph_birthdays_cron');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-birthday');
}

function sp_birthdays_do_deactivate() {
	wp_clear_scheduled_hook('sph_birthdays_cron');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-birthday');
}

function sp_birthdays_do_sp_deactivate() {
	wp_clear_scheduled_hook('sph_birthdays_cron');
}

function sp_birthdays_do_sp_uninstall() {
	wp_clear_scheduled_hook('sph_birthdays_cron');
}

function sp_birthdays_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'birthdays/sp-birthdays-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-birthdays')."'>".__('Uninstall', 'sp-birthdays').'</a>';
        $url = SPADMINOPTION.'&amp;tab=display';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-gsm')."'>".__('Options', 'sp-birthdays').'</a>';
    }
	return $actionlink;
}
