<?php
/*
Simple:Press
Subscriptions plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_subscriptions_do_install() {
	SP()->activity->add_type('topic subscriptions');
	SP()->activity->add_type('forum subscriptions');

	$subs = SP()->options->get('subscriptions');
	if (empty($subs)) {
        # need new columns
		SP()->DB->execute('ALTER TABLE '.SPMEMBERS." ADD (subscribe_digest smallint(1) NOT NULL default '0')");

        # need new table for digest subscriptions
		$sql = '
			CREATE TABLE IF NOT EXISTS '.SPDIGEST.' (
			digest_id bigint(20) NOT NULL auto_increment,
			forum_id bigint(20) default NULL,
            forum_name text default NULL,
			topic_id bigint(20) default NULL,
            topic_name text default NULL,
			post_id bigint(20) default NULL,
			subscriptions text default NULL,
			permalink text default NULL,
			PRIMARY KEY (digest_id),
			KEY forum_id_idx (forum_id),
			KEY topic_id_idx (topic_id),
			KEY post_id_idx (post_id)
			) '.SP()->DB->charset().';';
		SP()->DB->execute($sql);

		$subs = array();
		$subs['autosub'] = false;
        $subs['forumsubs'] = false;
        $subs['defnewtopics'] = false;
        $subs['digestsub'] = false;
        $subs['digesttype'] = 1; # daily
        $subs['digestforce'] = false; # user choose
        $subs['digestcontent'] = false;
        $subs['dbversion'] = SPSUBSDBVERSION;
		SP()->options->add('subscriptions', $subs);
    }

    # add our tables to installed list
   	$tables = SP()->options->get('installed_tables');
    if ($tables && !in_array(SPDIGEST, $tables)) {
        $tables[] = SPDIGEST;
        SP()->options->update('installed_tables', $tables);
    }

    # add options to possible profile display control list if in use
    if (function_exists('sp_profile_display_control_add_item')) {
        sp_profile_display_control_add_item('options-sub-auto', true, __('Auto Subscribe (posting options form)', 'sp-subs'), 'sph_ProfileUserSubsAutoSub', 'sph_ProfileUserSubsAutoSubUpdate');
    }

 	# get cron running if using
    if ($subs['digestsub']) {
        wp_schedule_event(time(), 'sp_subs_digest_interval', 'sph_subs_digest_cron');
    }

	# add profile tabs/menus
   	SP()->profile->add_tab('Subscriptions');
	SP()->profile->add_menu('Subscriptions', 'Subscription Options', SFORMSDIR.'sp-subscriptions-options-form.php');
	SP()->profile->add_menu('Subscriptions', 'Topic Subscriptions', SFORMSDIR.'sp-subscriptions-manage-form.php');

	# add in auto update stuff
    $autoup = array('spj.subsupdate', 'subs-manage&amp;target=subs');
    SP()->meta->add('autoupdate', 'subscriptions', $autoup);

    # add a new permission into the auths table
	SP()->auths->add('subscribe', __('Can subscribe to forums (if enabled) and topics', 'sp-subs'), 1, 1, 0, 0, 1);

    # activation so make our auth active
    SP()->auths->activate('subscribe');

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';

	# flush rewrite rules for pretty permalinks
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

# sp reactivated. do we need to fire up cron?
function sp_subscriptions_do_sp_activate() {
	wp_clear_scheduled_hook('sph_subs_digest_cron');
    wp_schedule_event(time(), 'sp_subs_digest_interval', 'sph_subs_digest_cron');
}

function sp_subscriptions_do_reset_permissions() {
	SP()->auths->add('subscribe', __('Can subscribe to forums (if enabled) and topics', 'sp-subs'), 1, 1, 0, 0, 1);
}
