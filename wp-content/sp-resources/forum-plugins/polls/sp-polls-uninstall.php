<?php
/*
Simple:Press
Polls plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the report post plugin uninstall only
function sp_polls_do_uninstall() {
	# remove the auth
	if (!empty(SP()->core->forumData['auths_map']['create_poll'])) SP()->auths->delete('create_poll');
	if (!empty(SP()->core->forumData['auths_map']['vote_poll'])) SP()->auths->delete('vote_poll');

	# remove any admin capabilities
	$admins = SP()->DB->table(SPMEMBERS, "admin=1");
	foreach ($admins as $admin) {
		$user = new WP_User($admin->user_id);
		$user->remove_cap('SPF Manage Polls');
	}

	SP()->DB->execute('ALTER TABLE '.SPFORUMS.' DROP polls');
	SP()->DB->execute('ALTER TABLE '.SPPOSTS.' DROP poll');

    # remove our auths
    SP()->auths->delete('create_poll');
    SP()->auths->delete('vote_poll');

    # delete our option table
    SP()->options->delete('polls');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-polls');

    # remove our tables
	SP()->DB->execute('DROP TABLE IF EXISTS '.SPPOLLS);
	SP()->DB->execute('DROP TABLE IF EXISTS '.SPPOLLSANSWERS);
	SP()->DB->execute('DROP TABLE IF EXISTS '.SPPOLLSVOTERS);
}

function sp_polls_do_deactivate() {
    SP()->auths->deactivate('create_poll');
    SP()->auths->deactivate('vote_poll');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-polls');
}

function sp_polls_do_sp_deactivate() {
}

function sp_polls_do_sp_uninstall($admins) {
	# remove any admin capabilities
	foreach ($admins as $admin) {
		$user = new WP_User($admin->user_id);
		$user->remove_cap('SPF Manage Polls');
	}
}

function sp_polls_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'polls/sp-polls-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-polls')."'>".__('Uninstall', 'sp-polls').'</a>';
        $url = SPADMINPLUGINS.'&amp;tab=plugin&amp;admin=sp_polls_admin_options&amp;save=sp_polls_admin_options_save&amp;form=1';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-polls')."'>".__('Options', 'sp-polls').'</a>';
    }
	return $actionlink;
}
