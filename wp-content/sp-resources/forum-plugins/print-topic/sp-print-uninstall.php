<?php
/*
Simple:Press
Print Topic plugin uninstall routine
$LastChangedDate: 2013-02-17 20:50:25 +0000 (Sun, 17 Feb 2013) $
$Rev: 9859 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_print_topic_do_uninstall() {
    # delete our option
    SP()->options->delete('print_topic');

    # make sure permalink include pm stuff
    SP()->spPermalinks->update_permalink(true);
}

function sp_print_topic_do_deactivate() {
}

function sp_print_topic_do_sp_deactivate() {
}

function sp_print_topic_do_sp_uninstall() {
}

function sp_print_topic_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'print-topic/sp-print-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-print-topic')."'>".__('Uninstall', 'sp-print-topic').'</a>';
    }
	return $actionlink;
}
