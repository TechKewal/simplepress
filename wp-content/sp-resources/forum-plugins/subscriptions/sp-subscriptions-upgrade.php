<?php
/*
Simple:Press
Subscriptions plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_subscriptions_do_upgrade_check() {
    if (!SP()->plugin->is_active('subscriptions/sp-subscriptions-plugin.php')) return;

    $subs = SP()->options->get('subscriptions');

    $db = $subs['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPSUBSDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades
    if ($db < 1) {
       	SP()->profile->add_tab('Subscriptions');
    	SP()->profile->add_menu('Subscriptions', 'Subscription Options', SFORMSDIR.'sp-subscriptions-options-form.php');
    	SP()->profile->add_menu('Subscriptions', 'Topic Subscriptions', SFORMSDIR.'sp-subscriptions-manage-form.php');
    }

    if ($db < 2) {
		SP()->DB->execute('ALTER TABLE '.SPFORUMS.' ADD (forum_subs longtext)');
		SP()->DB->execute('ALTER TABLE '.SPMEMBERS.' ADD (forum_subscribe longtext)');
        $auth = SP()->DB->table(SPAUTHS, "auth_name='subscribe'", 'auth_desc');
        $auth = __('Can subscribe to forums (if enabled) and topics', 'sp-subs');
	    SP()->DB->execute('UPDATE '.SPAUTHS." SET auth_desc = '$auth' WHERE auth_name='subscribe'");
        $subs['forumsubs'] = false;
    }

    if ($db < 3) {
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
		SP()->DB->execute('ALTER TABLE '.SPMEMBERS." ADD (subscribe_digest smallint(1) NOT NULL default '0')");
        $subs['digestsub'] = false;
        $subs['digesttype'] = 1;
    }

    if ($db < 4) {
	   SP()->DB->execute('UPDATE '.SPAUTHS." SET auth_cat=1 WHERE auth_name='subscribe'");
    }

    if ($db < 5) {
        $subs = SP()->options->get('subscriptions');
        $subs['digestcontent'] = false;
    	SP()->options->update('subscriptions', $subs);
    }

    if ($db < 6) {
        $subs = SP()->options->get('subscriptions');
        $subs['digestforce'] = false;
    	SP()->options->update('subscriptions', $subs);
    }

	if ($db < 7) {
        $subs = SP()->options->get('subscriptions');
        $subs['includepost'] = true;
    	SP()->options->update('subscriptions', $subs);
    }

    if ($db < 8) {
        # set autoload flag to true for autoupdates
    	$meta = SP()->meta->get('autoupdate', 'subscriptions');
    	if (!empty($meta[0])) SP()->meta->update('autoupdate', 'subscriptions', $meta[0]['meta_value'], $meta[0]['meta_id']);
    }

	if ($db < 9) {
		# upgrade topic subs to new activity format
		$sql = "SELECT user_id, subscribe FROM ".SPMEMBERS." WHERE subscribe != ''";
		$r = SP()->DB->select($sql);
		if($r) {
			foreach($r as $sub) {
				$t = unserialize($sub->subscribe);
				if($t) {
					foreach($t as $topicid) {
						SP()->activity->add($sub->user_id, SPACTIVITY_SUBSTOPIC, $topicid);
					}
				}
			}
		}
		# upgrade forum subs to new activity format
		$sql = "SELECT user_id, forum_subscribe FROM ".SPMEMBERS." WHERE forum_subscribe != ''";
		$r = SP()->DB->select($sql);
		if($r) {
			foreach($r as $sub) {
				$t = unserialize($sub->forum_subscribe);
				if($t) {
					foreach($t as $forumid) {
						SP()->activity->add($sub->user_id, SPACTIVITY_SUBSFORUM, $forumid);
					}
				}
			}
		}

		SP()->DB->execute('ALTER TABLE '.SPTOPICS.' DROP topic_subs');
		SP()->DB->execute('ALTER TABLE '.SPMEMBERS.' DROP subscribe');
		SP()->DB->execute('ALTER TABLE '.SPFORUMS.' DROP forum_subs');
		SP()->DB->execute('ALTER TABLE '.SPMEMBERS.' DROP forum_subscribe');

		# Finally clean out any old entries (topics) that failed to be deleted in the past
		$sql = "SELECT id FROM ".SPUSERACTIVITY."
				LEFT JOIN ".SPTOPICS." ON ".SPUSERACTIVITY.".item_id = ".SPTOPICS.".topic_id
				WHERE type_id = ".SPACTIVITY_SUBSTOPIC." AND topic_id IS NULL";
		$dead = SP()->DB->select($sql, 'col');
		if ($dead) {
			foreach($dead as $id) {
				SP()->activity->delete("id=$id");
			}
		}
		# Finally clean out any old entries (forums) that failed to be deleted in the past
		$sql = "SELECT id FROM ".SPUSERACTIVITY."
				LEFT JOIN ".SPFORUMS." ON ".SPUSERACTIVITY.".item_id = ".SPFORUMS.".forum_id
				WHERE type_id = ".SPACTIVITY_SUBSFORUM." AND forum_id IS NULL";
		$dead = SP()->DB->select($sql, 'col');
		if ($dead) {
			foreach($dead as $id) {
				SP()->activity->delete("id=$id");
			}
		}
	}

    if ($db < 10) {
		# admin task glossary entries
		require_once 'sp-admin-glossary.php';
	}

    if ($db < 11) {
        $subs = SP()->options->get('subscriptions');
        $subs['defnewtopics'] = false;
    	SP()->options->update('subscriptions', $subs);
	}

    if ($db < 12) {
		# update to namespaced js
	    $up = SP()->meta->get('autoupdate', 'subscriptions');
		$up[0]['meta_value'][0] = 'spj.subsupdate';
	    SP()->meta->update('autoupdate', 'subscriptions', $up[0]['meta_value'], $up[0]['meta_id']);
	}

	# save data
    $subs['dbversion'] = SPSUBSDBVERSION;
    SP()->options->update('subscriptions', $subs);
}
