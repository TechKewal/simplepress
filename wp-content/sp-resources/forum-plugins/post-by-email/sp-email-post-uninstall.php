<?php
/*
Simple:Press
Post by Email plugin uninstall routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_emailpost_do_uninstall() {
	# Remove all data
	SP()->options->delete('spEmailPost');

	# remove forum column and log table
	SP()->DB->execute('ALTER TABLE '.SPFORUMS.' DROP forum_email');
	SP()->DB->execute('DROP TABLE IF EXISTS '.SFMAILLOG);

	# remove the auths
	SP()->auths->delete('post_by_email_reply');
	SP()->auths->delete('post_by_email_start');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-postbyemail');

    # remove any scheduled cron jobs
	wp_clear_scheduled_hook('sph_emailpost_cron');
}

function sp_emailpost_do_sp_uninstall() {
	SP()->DB->execute('DROP TABLE IF EXISTS '.SFMAILLOG);

    # remove any scheduled cron jobs
	wp_clear_scheduled_hook('sph_emailpost_cron');
}

function sp_emailpost_do_deactivate() {
	# deactivation so make our auths not active
    SP()->auths->deactivate('post_by_email_reply');
    SP()->auths->deactivate('post_by_email_start');

	# remove glossary entries
	sp_remove_glossary_plugin('sp-postbyemail');

    # remove any scheduled cron jobs
	wp_clear_scheduled_hook('sph_emailpost_cron');
}

function sp_emailpost_do_sp_deactivate() {
    # remove any scheduled cron jobs
	wp_clear_scheduled_hook('sph_emailpost_cron');
}
