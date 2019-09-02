<?php
/*
Simple:Press
Post by Email plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_email_do_install() {
	# Check if already exists
	$spemailpost = SP()->options->get('spEmailPost');
	if(empty($spemailpost)) {
		$spemailpost['server'] = '';
		$spemailpost['port'] = 110;
		$spemailpost['pass'] = '';
		$spemailpost['tls'] = false;
		$spemailpost['ssl'] = false;
		$spemailpost['interval'] = 1800; # default 30 minutes
		$spemailpost['dbversion'] = SPPBEDBVERSION;
		SP()->options->add('spEmailPost', $spemailpost);

		SP()->DB->execute("ALTER TABLE ".SPFORUMS. " ADD forum_email varchar(100) default NULL");

		# Create email log table
		$sql = "
			CREATE TABLE IF NOT EXISTS ".SFMAILLOG." (
				email_id bigint(20) NOT NULL auto_increment,
				email_date datetime NOT NULL,
				email_forum varchar(100) NOT NULL,
				email_topic varchar(200) NOT NULL,
				email_user varchar(100) NOT NULL,
				email_log varchar(100) NOT NULL,
				PRIMARY KEY (email_id)
			) ".SP()->DB->charset().";";
		SP()->DB->execute($sql);
	}

    # add our tables to installed list
   	$tables = SP()->options->get('installed_tables');
    if ($tables && !in_array(SFMAILLOG, $tables)) {
        $tables[] = SFMAILLOG;
        SP()->options->update('installed_tables', $tables);
    }

    # add new permissions into the auths table
	SP()->auths->add('post_by_email_reply', __('Can reply to topics using email', 'sp-pbe'), 1, 0, 0, 0, 3);
	SP()->auths->add('post_by_email_start', __('Can start new topics using email', 'sp-pbe'), 1, 0, 0, 0, 3);
    SP()->auths->activate('post_by_email_reply');
    SP()->auths->activate('post_by_email_start');

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';

 	# get cron running
    wp_schedule_event(time(), 'sp_emailpost_interval', 'sph_emailpost_cron');
}

function sp_emailpost_uninstall_option_links($actionlink, $plugin) {
	if ($plugin == 'post-by-email/sp-email-post-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-pbe')."'>".__('Uninstall', 'sp-pbe').'</a>';
        $url = SPADMINOPTION.'&amp;tab=email';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-pbe')."'>".__('Options', 'sp-pbe').'</a>';
    }
	return $actionlink;
}

# sp reactivated. do we need to fire up cron?
function sp_emailpost_do_sp_activate() {
	wp_clear_scheduled_hook('sph_emailpost_cron');
    wp_schedule_event(time(), 'sp_emailpost_interval', 'sph_emailpost_cron');
}

function sp_emailpost_do_reset_permissions() {
	SP()->auths->add('post_by_email_reply', __('Can reply to topics using email', 'sp-pbe'), 1, 0, 0, 0, 3);
	SP()->auths->add('post_by_email_start', __('Can start new topics using email', 'sp-pbe'), 1, 0, 0, 0, 3);
}
