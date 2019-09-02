<?php
/*
Simple:Press
Topic Expire plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the this plugin uninstall only
function sp_topic_expire_do_uninstall() {
	SP()->DB->execute('ALTER TABLE '.SPTOPICS.' DROP expire_date, expire_action');

    # delete our option
    SP()->options->delete('topic-expire');

	# remove the auth
	if (!empty(SP()->core->forumData['auths_map']['set_topic_expire'])) SP()->auths->delete('set_topic_expire');

	wp_clear_scheduled_hook('sph_topic_expire_cron');
}

function sp_topic_expire_do_deactivate() {
    SP()->auths->deactivate('set_topic_expire');

	wp_clear_scheduled_hook('sph_topic_expire_cron');
}

function sp_topic_expire_do_sp_deactivate() {
	wp_clear_scheduled_hook('sph_topic_expire_cron');
}

function sp_topic_expire_do_sp_uninstall() {
	wp_clear_scheduled_hook('sph_topic_expire_cron');
}

function sp_topic_expire_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'topic-expire/sp-topic-expire-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-topic-expire')."'>".__('Uninstall', 'sp-topic-expire').'</a>';
    }
	return $actionlink;
}
