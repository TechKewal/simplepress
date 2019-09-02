<?php
/*
Simple:Press
Moderation Email plugin uninstall routine
$LastChangedDate: 2013-04-17 19:24:03 -0700 (Wed, 17 Apr 2013) $
$Rev: 10182 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the this plugin uninstall only
function sp_moderation_email_do_uninstall() {
    # delete our option
    SP()->options->delete('moderation-email');
}

function sp_moderation_email_do_deactivate() {
}

function sp_moderation_email_do_sp_deactivate() {
}

function sp_moderation_email_do_sp_uninstall() {
}

function sp_moderation_email_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'moderation-email/sp-moderation-email-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-moderation-email')."'>".__('Uninstall', 'sp-moderation-email').'</a>';
 	    $url = SPADMINOPTION.'&amp;tab=email';
	    $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-moderation-email')."'>".__('Options', 'sp-moderation-email').'</a>';
   }
	return $actionlink;
}
