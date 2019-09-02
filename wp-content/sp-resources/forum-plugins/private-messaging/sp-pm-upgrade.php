<?php
/*
Simple:Press
PM plugin install/upgrade routine
$LastChangedDate: 2018-11-05 07:41:08 -0600 (Mon, 05 Nov 2018) $
$Rev: 15809 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_pm_do_upgrade_check() {
	if (!SP()->plugin->is_active('private-messaging/sp-pm-plugin.php')) return;

	$pmdata = SP()->options->get('pm');

	$db = $pmdata['dbversion'];
	if (empty($db)) $db = 0;

	# quick bail check
	if ($db == SPDBVERSION ) return;

	# apply upgrades as needed
	if ($db < 1) {
		SP()->DB->execute("UPDATE ".SPMEMBERS." SET buddies='' WHERE buddies='0'");
	}

	if ($db < 2) {
		SP()->DB->execute("ALTER TABLE ".SPPMMESSAGES." ADD KEY inbox_idx (inbox)");
		SP()->DB->execute("ALTER TABLE ".SPPMMESSAGES." ADD KEY sentbox_idx (sentbox)");
		SP()->DB->execute("ALTER TABLE ".SPPMMESSAGES." ADD KEY message_status_idx (message_status)");
	}

	if ($db < 3) {
		# make sure adversaries table exists due to previous install bug where it might not have gotten created
		$sql = "
			CREATE TABLE IF NOT EXISTS ".SPPMADVERSARIES." (
				id bigint(20) NOT NULL auto_increment,
				user_id bigint(20) NOT NULL,
				adversary_id bigint(20) NOT NULL,
				PRIMARY KEY	 (id),
				KEY user_id_idx (user_id),
				KEY adversary_id_idx (adversary_id)
			) ".SP()->DB->charset().";";
		SP()->DB->execute($sql);
	}

	if ($db < 4) {
	   SP()->DB->execute('UPDATE '.SPAUTHS." SET auth_cat=1 WHERE auth_name='use_pm'");
	}

	if ($db < 5) {
		# attachments in pms
		$sql = "
			CREATE TABLE IF NOT EXISTS ".SPPMATTACHMENTS." (
				attachment_id bigint(20) NOT NULL auto_increment,
				attachments text,
				PRIMARY KEY	 (attachment_id)
			) ".SP()->DB->charset().";";
		SP()->DB->execute($sql);

		SP()->DB->execute('ALTER TABLE '.SPPMMESSAGES.' ADD (attachment_id bigint(20))');

		$pmdata['uploads'] = false;
	}

	if ($db < 6) {
		SP()->DB->execute("ALTER TABLE ".SPPMATTACHMENTS." DROP sender_id");
	}

	if ($db < 7) {
		$pmdata['limitedug'] = false;
	}

	if ($db < 8) {
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
				read_status TINYINT(1)	NULL,
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
		SP()->DB->execute('INSERT INTO '.SPPMMESSAGES.' (thread_id, user_id, sent_date, message, attachment_id)
			SELECT DISTINCT thread_id, from_id, sent_date, message, attachment_id FROM '.SFOLDMESSAGES.'
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
	}

	if ($db < 9) {
		# need new table for buddies
		$sql = "
			CREATE TABLE IF NOT EXISTS ".SPPMBUDDIES." (
				id bigint(20) NOT NULL auto_increment,
				user_id bigint(20) NOT NULL,
				buddy_id bigint(20) NOT NULL,
				PRIMARY KEY	 (id),
				KEY user_id_idx (user_id),
				KEY buddy_id_idx (buddy_id)
			) ".SP()->DB->charset().";";
		SP()->DB->execute($sql);


		# add table to installed list
		$tables = SP()->options->get('installed_tables');
		if ($tables) {
			if (!in_array(SPPMBUDDIES, $tables)) $tables[] = SPPMBUDDIES;
			SP()->options->update('installed_tables', $tables);
		}

		# 2 - Query current buddies column to see if any data to move
		$sql = "SELECT user_id, buddies
				FROM ".SPMEMBERS."
				WHERE buddies <> 's:0:\"\";'
				AND buddies <> '';";

		$records = SP()->DB->select($sql);

		# 3 - And if any then loop through and create new rows

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

		# 4 - Now delete the old buddies column from sfmemnbers table

		SP()->DB->execute("ALTER TABLE ".SPMEMBERS." DROP buddies;");
	}

	if ($db < 10) {
		# give new manage pm cap to any admin with manage options cap since they already can manage pm
		$admins = SP()->DB->table(SPMEMBERS, 'admin = 1');
		if ($admins) {
		   foreach ($admins as $admin) {
				$user = new WP_User($admin->user_id);
				if (user_can($user, 'SPF Manage Components')) {
					$user->add_cap('SPF Manage PM');
				}
			}
		}
	}

	if ($db < 11) {
		# set autoload flag to true for autoupdates
		$meta = SP()->meta->get('autoupdate', 'inbox');
		if (!empty($meta[0])) SP()->meta->update('autoupdate', 'inbox', $meta[0]['meta_value'], $meta[0]['meta_id']);
	}

	if ($db < 12) {
		$tables = SP()->options->get('installed_tables');
		if ($tables) {
			define('SFOLDMESSAGES', SP_PREFIX.'sfmessages');
			if (in_array(SFOLDMESSAGES, $tables)) unset($tables[SFOLDMESSAGES]);

			if (!in_array(SPPMMESSAGES, $tables)) $tables[] = SPPMMESSAGES;
			if (!in_array(SPPMTHREADS, $tables)) $tables[] = SPPMTHREADS;
			if (!in_array(SPPMRECIPIENTS, $tables)) $tables[] = SPPMRECIPIENTS;
			SP()->options->update('installed_tables', $tables);
		}
	}

	if ($db < 13) {
		global $wp_roles;
		$wp_roles->add_cap('administrator', 'SPF Manage PM', false);
	}

	if ($db < 14) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

	if ($db < 15) {
		# add thread count for paging
		$pmdata['threadpaging'] = 15;
		$pmdata['messagepaging'] = 10;
		$pmdata['accessposts'] = 0;
	}

    if ($db < 16) {
		# update to namespaced js
	    $up = SP()->meta->get('autoupdate', 'inbox');
		$up[0]['meta_value'][0] = 'spj.pmupdate';
	    SP()->meta->update('autoupdate', 'inbox', $up[0]['meta_value'], $up[0]['meta_id']);
	}

	if ($db < 17) {
		# remove slug index from threads table
		$sql = 'DROP INDEX thread_slug_idx ON '.SPPMTHREADS;
		$success = SP()->DB->execute($sql);
	}

	if ($db < 18) {
		$pmdata['pmexport'] = false;
	}

	# save data
	$pmdata['dbversion'] = SPDBVERSION;
	SP()->options->update('pm', $pmdata);
}
