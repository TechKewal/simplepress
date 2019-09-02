<?php
/*
Simple:Press
Admin Bar plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_admin_bar_do_uninstall() {
    SP()->options->delete('spAdminBar');
	SP()->options->delete('spAkismet');

	# remove the auth
	if (!empty(SP()->core->forumData['auths_map']['bypass_akismet'])) SP()->auths->delete('bypass_akismet');

    # remove our auto update stuff
    $up = SP()->meta->get('autoupdate', 'admin');
    if ($up) SP()->meta->delete($up[0]['meta_id']);

    # empty out the sfwaiting table
	SP()->DB->truncate(SPWAITING);

	# remove glossary entries
	sp_remove_glossary_plugin('sp-adminbar');
}

function sp_admin_bar_do_deactivate() {
	# remove the auth
	SP()->auths->deactivate('bypass_akismet');

    # remove our auto update stuff
    $up = SP()->meta->get('autoupdate', 'admin');
    if ($up) SP()->meta->delete($up[0]['meta_id']);

	# remove glossary entries
	sp_remove_glossary_plugin('sp-adminbar');
}

function sp_admin_bar_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'admin-bar/sp-admin-bar-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'spwo')."'>".__('Uninstall', 'spab').'</a>';
        $url = SPADMINADMIN.'&amp;tab=globaladmin';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'spab')."'>".__('Options', 'spab').'</a>';
    }
	return $actionlink;
}
