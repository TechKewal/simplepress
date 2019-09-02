<?php
/*
Simple:Press
Name plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the this plugin uninstall only
function sp_html_email_do_uninstall() {
    # delete our option
    SP()->options->delete('html-email');

	# remove any admin capabilities
	$admins = SP()->DB->table(SPMEMBERS, "admin=1");
	foreach ($admins as $admin) {
		$user = new WP_User($admin->user_id);
		$user->remove_cap('SPF Manage Emails');
	}

	# remove glossary entries
	sp_remove_glossary_plugin('sp-emails');
}

function sp_html_email_do_deactivate() {
	# remove glossary entries
	sp_remove_glossary_plugin('sp-emails');
}

function sp_html_email_do_sp_deactivate() {
}

function sp_html_email_do_sp_uninstall() {
}

function sp_html_email_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'html-email/sp-html-email-plugin.php') {
	    $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
	    $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-html-email')."'>".__('Uninstall', 'sp-html-email').'</a>';
        $url = SPADMINPLUGINS.'&amp;tab=plugin&amp;admin=sp_html_email_global_settings&amp;save=sp_html_email_admin_save_global&amp;form=1';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-pm')."'>".__('Options', 'sp-pm').'</a>';
    }
	return $actionlink;
}
