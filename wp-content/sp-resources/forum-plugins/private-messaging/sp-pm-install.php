<?php
/*
Simple:Press
PM plugin install/upgrade routine
$LastChangedDate: 2018-11-05 07:41:08 -0600 (Mon, 05 Nov 2018) $
$Rev: 15809 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_pm_do_install() {
	$oldpm = SP()->options->get('sfpm');
	$pmdata = SP()->options->get('pm');
	if (empty($oldpm) && empty($pmdata)) {
        # brand new install - create table for messages
        # Table sfpmthreads
        $sql = '
            CREATE TABLE IF NOT EXISTS '.SPPMTHREADS.' (
                thread_id BIGINT(20) NOT NULL AUTO_INCREMENT,
                title VARCHAR(200) NOT NULL,
                thread_slug VARCHAR(200) NOT NULL,
                message_count INT NULL,
                PRIMARY KEY (thread_id)
            ) '.SP()->DB->charset();
		SP()->DB->execute($sql);

        $sql = '
            CREATE TABLE IF NOT EXISTS '.SPPMMESSAGES.' (
                message_id BIGINT(20) NOT NULL AUTO_INCREMENT,
                thread_id BIGINT(20) NULL,
                user_id BIGINT(20) NULL,
                sent_date DATETIME NULL,
                message TEXT NULL,
                attachment_id BIGINT(20) NULL,
                PRIMARY KEY (message_id),
                KEY thread_id_idx (thread_id),
                KEY user_id_idx (user_id)
            ) '.SP()->DB->charset();
		SP()->DB->execute($sql);

        # Table sfpmrecipients
        $sql = '
            CREATE TABLE IF NOT EXISTS '.SPPMRECIPIENTS.' (
                recipient_id BIGINT(20) NOT NULL AUTO_INCREMENT,
                thread_id BIGINT(20) NULL,
                message_id BIGINT(20) NULL,
                user_id BIGINT(20) NULL,
                read_status TINYINT(1)  NULL,
                pm_type INT NULL,
                PRIMARY KEY (recipient_id),
                KEY thread_id_idx (thread_id),
                KEY message_id_idx (message_id),
                KEY user_id_idx (user_id)
            ) '.SP()->DB->charset();
		SP()->DB->execute($sql);

        # need new table in sfmembers for buddies
        SP()->DB->execute('ALTER TABLE '.SPMEMBERS.' ADD (buddies longtext)');

        # need new table for adversaries
		$sql = "
			CREATE TABLE IF NOT EXISTS ".SPPMADVERSARIES." (
				id bigint(20) NOT NULL auto_increment,
				user_id bigint(20) NOT NULL,
				adversary_id bigint(20) NOT NULL,
				PRIMARY KEY  (id),
				KEY user_id_idx (user_id),
				KEY adversary_id_idx (adversary_id)
			) ".SP()->DB->charset().";";
		SP()->DB->execute($sql);

		# and buddies
		$sql = "
			CREATE TABLE IF NOT EXISTS ".SPPMBUDDIES." (
				id bigint(20) NOT NULL auto_increment,
				user_id bigint(20) NOT NULL,
				buddy_id bigint(20) NOT NULL,
				PRIMARY KEY  (id),
				KEY user_id_idx (user_id),
				KEY buddy_id_idx (buddy_id)
			) ".SP()->DB->charset().";";
		SP()->DB->execute($sql);

        # need new table for attachments
		$sql = "
			CREATE TABLE IF NOT EXISTS ".SPPMATTACHMENTS." (
				attachment_id bigint(20) NOT NULL auto_increment,
				attachments text,
				PRIMARY KEY  (attachment_id)
			) ".SP()->DB->charset().";";
		SP()->DB->execute($sql);

		$sppm = array();
		$sppm['email'] = true;
		$sppm['max'] = 0;
		$sppm['maxrecipients'] = 0;
		$sppm['cc'] = true;
		$sppm['bcc'] = true;
        $sppm['keep'] = 365;
        $sppm['limitedsend'] = false;
        $sppm['limitedug'] = false;
        $sppm['remove'] = false;
        $sppm['uploads'] = false;
        $sppm['threadpaging'] = 15;
        $sppm['messagepaging'] = 10;
        $sppm['accessposts'] = 0;
        $sppm['pmexport'] = false;

        $sppm['dbversion'] = SPDBVERSION;
		SP()->options->add('pm', $sppm);
    } elseif (empty($pmdata)) {
    	# upgrade from pre sp 5.0 when pm was in core
        # upgrade from when it was part of core
        $sfsupport = array();
        $sfsupport = SP()->options->get('sfsupport');
        $include = $sfsupport['sfusingpmtags'];
        unset($sfsupport['sfusingpmtags']);
    	SP()->options->update('sfsupport', $sfsupport);

		$pmdata['email'] = $sppm['sfpmemail'];
		$pmdata['max'] = $sppm['sfpmmax'];
		$pmdata['maxrecipients'] = $sppm['sfpmmaxrecipients'];
		$pmdata['cc'] = $sppm['sfpmcc'];
		$pmdata['bcc'] = $sppm['sfpmbcc'];
		$pmdata['keep'] = $sppm['sfpmkeep'];
		$pmdata['limitedsend'] = $sppm['sfpmlimitedsend'];
        $pmdata['remove'] = $sppm['sfpmremove'];
        $pmdata['uploads'] = false;
        $pmdata['limitedug'] = false;
        $pmdata['threadpaging'] = 15;
        $pmdata['messagepaging'] = 10;
        $pmdata['accessposts'] = 0;
        $pmdata['pmexport'] = false;

        $pmdata['dbversion'] = SPDBVERSION;
		SP()->options->add('pm', $pmdata);

        # upgrade old pm db
        define('SFOLDMESSAGES', SP_PREFIX.'sfmessages');

        # Table sfpmthreads
        $sql = '
            CREATE TABLE IF NOT EXISTS '.SPPMTHREADS.' (
                thread_id BIGINT(20) NOT NULL AUTO_INCREMENT,
                title VARCHAR(200) NOT NULL,
                thread_slug VARCHAR(200) NOT NULL,
                message_count INT NULL,
                PRIMARY KEY (thread_id)
            ) '.SP()->DB->charset();
		SP()->DB->execute($sql);

        # Table sfpmmessages
        $sql = '
            CREATE TABLE IF NOT EXISTS '.SPPMMESSAGES.' (
                message_id BIGINT(20) NOT NULL AUTO_INCREMENT,
                thread_id BIGINT(20) NULL,
                user_id BIGINT(20) NULL,
                sent_date DATETIME NULL,
                message TEXT NULL,
                attachment_id BIGINT(20) NULL,
                PRIMARY KEY (message_id),
                KEY thread_id_idx (thread_id),
                KEY user_id_idx (user_id)
            ) '.SP()->DB->charset();
		SP()->DB->execute($sql);

        # Table sfpmrecipients
        $sql = '
            CREATE TABLE IF NOT EXISTS '.SPPMRECIPIENTS.' (
                recipient_id BIGINT(20) NOT NULL AUTO_INCREMENT,
                thread_id BIGINT(20) NULL,
                message_id BIGINT(20) NULL,
                user_id BIGINT(20) NULL,
                read_status TINYINT(1)  NULL,
                pm_type INT NULL,
                PRIMARY KEY (recipient_id),
                KEY thread_id_idx (thread_id),
                KEY message_id_idx (message_id),
                KEY user_id_idx (user_id)
            ) '.SP()->DB->charset();
		SP()->DB->execute($sql);

        # Perfrom any cleanups to existing data (remove self sent pms)
        SP()->DB->execute('DELETE FROM '.SFOLDMESSAGES.' WHERE to_id = from_id');

        # Threads 1 - Create main thread records
        SP()->DB->execute('INSERT INTO '.SPPMTHREADS.' (title, thread_slug) SELECT DISTINCT title, message_slug FROM '.SFOLDMESSAGES.' ORDER BY message_id');

        # Messages 1 - Create the message records
        SP()->DB->execute('INSERT INTO '.SPPMMESSAGES.' (thread_id, user_id, sent_date, message)
            SELECT DISTINCT thread_id, from_id, sent_date, message FROM '.SFOLDMESSAGES.'
            JOIN '.SPPMTHREADS.' ON '.SFOLDMESSAGES.'.message_slug = '.SPPMTHREADS.'.thread_slug ORDER BY message_id');

        # Threads 2 - Add message count to thread records
        SP()->DB->execute('UPDATE '.SPPMTHREADS.' SET message_count = (SELECT COUNT(*) FROM '.SPPMMESSAGES.' WHERE '.SPPMMESSAGES.'.thread_id = '.SPPMTHREADS.'.thread_id)');

        # Recipients 1 - Add From users
        SP()->DB->execute('INSERT INTO '.SPPMRECIPIENTS.' (thread_id, message_id, user_id, read_status, pm_type)
            SELECT DISTINCT thread_id, '.SPPMMESSAGES.'.message_id, from_id, 1, TYPE FROM '.SFOLDMESSAGES.'
            JOIN '.SPPMMESSAGES.' ON '.SFOLDMESSAGES.'.sent_date = '.SPPMMESSAGES.'.sent_date WHERE sentbox=1');

        # Recipients 2 - Add To users
        SP()->DB->execute('INSERT INTO '.SPPMRECIPIENTS.' (thread_id, message_id, user_id, read_status, pm_type)
            SELECT DISTINCT thread_id, '.SPPMMESSAGES.'.message_id, to_id, message_status, TYPE FROM '.SFOLDMESSAGES.'
            JOIN '.SPPMMESSAGES.' ON '.SFOLDMESSAGES.'.sent_date = '.SPPMMESSAGES.'.sent_date WHERE inbox=1');

        # Remove the old messages table
        SP()->DB->execute('DROP TABLE IF EXISTS '.SFOLDMESSAGES);

        # need new table for adversaries
		$sql = "
			CREATE TABLE IF NOT EXISTS ".SPPMADVERSARIES." (
				id bigint(20) NOT NULL auto_increment,
				user_id bigint(20) NOT NULL,
				adversary_id bigint(20) NOT NULL,
				PRIMARY KEY  (id),
				KEY user_id_idx (user_id),
				KEY adversary_id_idx (adversary_id)
			) ".SP()->DB->charset().";";
		SP()->DB->execute($sql);

		# and Buddies
		$sql = "
			CREATE TABLE IF NOT EXISTS ".SPPMBUDDIES." (
				id bigint(20) NOT NULL auto_increment,
				user_id bigint(20) NOT NULL,
				buddy_id bigint(20) NOT NULL,
				PRIMARY KEY  (id),
				KEY user_id_idx (user_id),
				KEY buddy_id_idx (buddy_id)
			) ".SP()->DB->charset().";";
		SP()->DB->execute($sql);

		# Query current buddies column to see if any data to move
		$sql = "SELECT user_id, buddies
				FROM ".SPMEMBERS."
				WHERE buddies <> 's:0:\"\";'
				AND buddies <> '';";

		$records = SP()->DB->select($sql);

		# And if any then loop through and create new rows

		if($records) {
			foreach($records as $r) {
				$buddies = unserialize($r->buddies);
				if($buddies) {
					foreach($buddies as $b) {
						$sql = "INSERT INTO ".SPPMBUDDIES."
						(user_id, buddy_id) VALUES
						($r->user_id, $b);";
						SP()->DB->execute($sql);
					}
				}
			}
		}

		SP()->options->delete('sfpm');
		SP()->options->delete('sfprivatemessaging');

        wp_clear_scheduled_hook('spf_cron_pm'); # clear old cron
    }

    # add our tables to installed list
   	$tables = SP()->options->get('installed_tables');
    if ($tables) {
        if (!in_array(SPPMTHREADS, $tables)) $tables[] = SPPMTHREADS;
        if (!in_array(SPPMMESSAGES, $tables)) $tables[] = SPPMMESSAGES;
        if (!in_array(SPPMRECIPIENTS, $tables)) $tables[] = SPPMRECIPIENTS;
        if (!in_array(SPPMATTACHMENTS, $tables)) $tables[] = SPPMATTACHMENTS;
        if (!in_array(SPPMADVERSARIES, $tables)) $tables[] = SPPMADVERSARIES;
        if (!in_array(SPPMBUDDIES, $tables)) $tables[] = SPPMBUDDIES;
        SP()->options->update('installed_tables', $tables);
    }

   	global $wp_roles;
    $wp_roles->add_cap('administrator', 'SPF Manage PM', false);

    # do we need to give activater Manage PM capability
    if (!SP()->auths->current_user_can('SPF Manage PM')) {
		$user = new WP_User(SP()->user->thisUser->ID);
		$user->add_cap('SPF Manage PM');
    }

    # add pm options to possible profile display control list if in use
    if (function_exists('sp_profile_display_control_add_item')) {
        sp_profile_display_control_add_item('options-pm-email', true, __('PM Email (posting options form)', 'sp-pm'), 'sph_ProfileUserPMEmail', 'sph_ProfileUserPMEmailUpdate');
        sp_profile_display_control_add_item('options-pm-opt', true, __('PM Opt Out (posting options form)', 'sp-pm'), 'sph_ProfileUserPMOptOut', 'sph_ProfileUserPMOptOutUpdate');
        sp_profile_display_control_add_item('options-pm-size', true, __('PM Inbox Size (display options form)', 'sp-pm'), 'sph_ProfileUserPMInboxSize', 'sph_ProfileUserPMInboxSizeUpdate');
    }

	# add profile tabs/menus
   	SP()->profile->add_tab('Buddies and Adversaries', 0, 1, 'use_pm');
	SP()->profile->add_menu('Buddies and Adversaries', 'Manage Buddies', PMFORMSDIR.'sp-pm-buddies-form.php', 0, 1, 'use_pm');
	SP()->profile->add_menu('Buddies and Adversaries', 'Manage Adversaries', PMFORMSDIR.'sp-pm-adversaries-form.php', 0, 1, 'use_pm');

	# start pm cron if needed
    if ($pmdata['remove']) {
    	wp_schedule_event(time(), 'daily', 'sph_pm_cron'); # new cron name
    }

 	# get auto update running
 	$autoup = array('spj.pmupdate', 'pm-manage&amp;target=inbox');
	SP()->meta->add('autoupdate', 'inbox', $autoup);

	# flush rewrite rules for pretty permalinks
	global $wp_rewrite;
	$wp_rewrite->flush_rules();

    # add a new permission into the auths table
	SP()->auths->add('use_pm', __('Can use the private messaging system', 'sp-pm'), 1, 1, 0, 0, 1);

    # activation so make our auth active
    SP()->auths->activate('use_pm');

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# sp reactivated. do we need to fire up cron?
function sp_pm_do_sp_activate() {
	# pm auto removal cron job
	wp_clear_scheduled_hook('sph_pm_cron');
   	$pmdata = SP()->options->get('pm');
    if ($pmdata['remove']) wp_schedule_event(time(), 'daily', 'sph_pm_cron');
}

function sp_pm_do_reset_permissions() {
	SP()->auths->add('use_pm', __('Can use the private messaging system', 'sp-pm'), 1, 1, 0, 0, 1);
}
