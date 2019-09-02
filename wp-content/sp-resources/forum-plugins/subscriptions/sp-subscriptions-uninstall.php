<?php
/*
Simple:Press
Subscriptions plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# this uninstall function is for the post rating plugin uninstall only
function sp_subscriptions_do_uninstall() {
    # remove our db stuff
	SP()->DB->execute('DROP TABLE IF EXISTS '.SPDIGEST);
	SP()->DB->execute('ALTER TABLE '.SPMEMBERS.' DROP subscribe_digest');

	# delete the user activioty records
    SP()->activity->delete('type='.SPACTIVITY_SUBSTOPIC);
    SP()->activity->delete('type='.SPACTIVITY_SUBSFORUM);

	# remove our activity types
	SP()->activity->delete_type('topic subscriptions');
	SP()->activity->delete_type('forum subscriptions');

	# remove our profile tab/meuns
    SP()->profile->delete_tab(__('Subscriptions', 'sp-subs'));

    # remove any scheduled cron jobs
	wp_clear_scheduled_hook('sph_subs_digest_cron');

	# remove the auth
	if (!empty(SP()->core->forumData['auths_map']['subscribe'])) SP()->auths->delete('subscribe');

    # delete our option table
    SP()->options->delete('subscriptions');

    # remove our auto update stuff
    $up = SP()->meta->get('autoupdate', 'subscriptions');
    if ($up) SP()->meta->delete($up[0]['meta_id']);

	# remove glossary entries
	sp_remove_glossary_plugin('sp-subscriptions');
}

function sp_subscriptions_do_deactivate() {
    # remove our auto update stuff
    $up = SP()->meta->get('autoupdate', 'subscriptions');
    if ($up) SP()->meta->delete($up[0]['meta_id']);

    # remove any scheduled cron jobs
	wp_clear_scheduled_hook('sph_subs_digest_cron');

	# remove our profile tab/meuns
    SP()->profile->delete_tab(__('Subscriptions', 'sp-subs'));

    SP()->auths->deactivate('subscribe');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-subscriptions');
}

function sp_subscriptions_do_sp_deactivate() {
    # remove any scheduled cron jobs
	wp_clear_scheduled_hook('sph_subs_digest_cron');
}

function sp_subscriptions_do_sp_uninstall() {
    # make sure sp uninstall initiated
	if (SP()->options->get('sfuninstall')) {
    	SP()->DB->execute('DROP TABLE IF EXISTS '.SPDIGEST);

    	wp_clear_scheduled_hook('sph_subs_digest_cron');
    }
}
