<?php
/*
Simple:Press
Topic Subscriptions plugin database routines
$LastChangedDate: 2017-11-12 17:27:20 -0600 (Sun, 12 Nov 2017) $
$Rev: 15584 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

require_once SLIBDIR.'sp-subscriptions-components.php';

function sp_subscriptions_do_member_add($userid) {
    $opts = array();
    $opts = SP()->memberData->get($userid, 'user_options');
    $subs = SP()->options->get('subscriptions');

	$opts['autosubpost'] = ($subs['autosub']) ? 1 : 0;
	$opts['autosubstart'] = 0;
	$opts['subnewtopics'] = ($subs['defnewtopics']) ? 1 : 0;
    SP()->memberData->update($userid, 'user_options', $opts);

     # start with nothing subscribed
	$sql = 'UPDATE '.SPMEMBERS." SET subscribe_digest=0 WHERE user_id=".$userid;
	SP()->DB->execute($sql);
}

function sp_subscriptions_do_member_del($userid) {
    # remove forum subscriptions
    SP()->activity->delete('type='.SPACTIVITY_SUBSFORUM."&uid=$userid");

    # remove topic subscriptions
    SP()->activity->delete('type='.SPACTIVITY_SUBSTOPIC."&uid=$userid");
}

function sp_subscriptions_do_forum_delete($forum) {
    SP()->activity->delete('type='.SPACTIVITY_SUBSFORUM."&item=$forum->forum_id");

    # any digests to remove?
	SP()->DB->execute('DELETE FROM '.SPDIGEST." WHERE forum_id=$forum->forum_id");
}

function sp_subscriptions_do_topic_delete($posts) {
	# We need to check for subscriptions on this topic
	$thisTopic = (is_object($posts)) ? $posts : $posts[0];
    SP()->activity->delete('type='.SPACTIVITY_SUBSFORUM."&item=$thisTopic->topic_id");

    # any digests to remove?
	SP()->DB->execute('DELETE FROM '.SPDIGEST." WHERE topic_id=$thisTopic->topic_id");
}

function sp_subscriptions_do_topic_moved($currenttopicid, $currentforumid, $targetforumid) {
    SP()->DB->execute('UPDATE '.SPDIGEST." SET forum_id=$targetforumid WHERE forum_id=$currentforumid");
}

function sp_subscriptions_do_post_moved($oldtopicid, $newtopicid, $newforumid, $oldforumid, $postid) {
    SP()->DB->execute('UPDATE '.SPDIGEST." SET forum_id=$newforumid WHERE post_id=$postid AND forum_id=$oldforumid");
    SP()->DB->execute('UPDATE '.SPDIGEST." SET topic_id=$newtopicid WHERE post_id=$postid AND topic_id=$oldtopicid");
}

function sp_subscriptions_do_post_delete($post) {
    # any digests to remove?
	SP()->DB->execute('DELETE FROM '.SPDIGEST." WHERE post_id=$post->post_id");
}

function sp_subscriptions_remove_forum_subscription($forumid, $userid, $retmessage=true) {
	if (!$userid || !$forumid) return '';
    SP()->activity->delete('type='.SPACTIVITY_SUBSFORUM."&item=$forumid");

	if ($retmessage) SP()->notifications->message(0, __('Subscription removed', 'sp-subs'));
}

function sp_subscriptions_remove_subscription($topicid, $userid, $retmessage=true) {
	if (!$userid || !$topicid) return '';
	if (!SP()->user->thisUser->admin && SP()->user->thisUser->ID != $userid) return;

    SP()->activity->delete('type='.SPACTIVITY_SUBSTOPIC."&item=$topicid");

	if ($retmessage) SP()->notifications->message(0, __('Subscription removed', 'sp-subs'));
}

function sp_subscriptions_save_forum_subscription($forumid, $userid, $retmessage=true) {
	if (!$userid || !$forumid) return '';
	if (SP()->user->thisUser->guest || !SP()->auths->get('subscribe', $forumid, $userid)) return;
	if (!SP()->user->thisUser->admin && SP()->user->thisUser->ID != $userid) return;

	# is user already subscribed to this forum?
	if (SP()->activity->exist('type='.SPACTIVITY_SUBSFORUM."&uid=$userid&item=$forumid")) {
		if ($retmessage) {
			SP()->notifications->message(1, __('You are already subscribed to this forum', 'sp-subs'));
			return;
		}
	}

	# OK  -subscribe them to the forum
	SP()->activity->add($userid, SPACTIVITY_SUBSFORUM, $forumid, '', false);
	if ($retmessage) SP()->notifications->message(0, __('Subscription added', 'sp-subs'));
}

function sp_subscriptions_save_subscription($topicid, $userid, $retmessage=true) {
	if (!$userid || !$topicid) return '';
	$forumid = SP()->DB->table(SPTOPICS, "topic_id=$topicid", 'forum_id');
	if (empty($forumid) || !SP()->auths->get('subscribe', $forumid, $userid)) return;

	# is user already subscribed to this topic?
	if (SP()->activity->exist('type='.SPACTIVITY_SUBSTOPIC."&uid=$userid&item=$topicid")) {
		if ($retmessage) {
			SP()->notifications->message(1, __('You are already subscribed to this topic', 'sp-subs'));
			return;
		}
	}

	# OK  -subscribe them to the topic
	SP()->activity->add($userid, SPACTIVITY_SUBSTOPIC, $topicid, '', false);
	if ($retmessage) SP()->notifications->message(0, __('Subscription added', 'sp-subs'));
}

function sp_subscriptions_remove_user_subscriptions($userid) {
	# make sure user is doing this
	if (SP()->user->thisUser->admin || $userid == SP()->user->thisUser->ID) {
		# Remove subscriptions
	    SP()->activity->delete('type='.SPACTIVITY_SUBSTOPIC."&uid=$userid");
	}
}

function sp_subscriptions_remove_forum_subscriptions($forumid) {
    SP()->activity->delete('type='.SPACTIVITY_SUBSFORUM."&item=$forumid");
}

function sp_subscriptions_remove_topic_subscriptions($topicid) {
    SP()->activity->delete('type='.SPACTIVITY_SUBSTOPIC."&item=$topicid");
}


function sp_subscriptions_get_topic_subscriptions($filter, $groups, $forums, $curpage, $search) {
    global $wpdb;

	# setup where we are in the list (paging)
	$startlimit = 0;
	if ($curpage != 1) $startlimit = ($curpage - 1) * 20;

	$where = 'WHERE ';
	$limit = ' LIMIT '.$startlimit.', 20';

	# create the join based on all, group or forum filter
	if ($filter == 'groups' && $groups[0] != -1) {
		$where.= SPGROUPS.".group_id IN (".implode(",", $groups).") AND ";
	} elseif ($filter == 'forums' && $forums[0] != -1) {
		$where.= SPFORUMS.".forum_id IN (".implode(",", $forums).") AND ";
	}
	$where.='item_id IS NOT NULL AND type_id = '.SPACTIVITY_SUBSTOPIC;

	# any search terms?
	$like = '';
	if (!empty($search)) $like = ' AND topic_name LIKE "%'.SP()->filters->esc_sql($wpdb->esc_like($search)).'%"';

	# retrieve watched topic records
	$query = 'SELECT SQL_CALC_FOUND_ROWS DISTINCT
			 topic_id, topic_name, topic_slug, group_name, forum_name, forum_slug
			 FROM '.SPTOPICS.'
			 JOIN '.SPFORUMS.' ON '.SPFORUMS.'.forum_id = '.SPTOPICS.'.forum_id
			 JOIN '.SPGROUPS.' ON '.SPGROUPS.'.group_id = '.SPFORUMS.'.group_id
			 JOIN '.SPUSERACTIVITY.' ON '.SPTOPICS.'.topic_id = '.SPUSERACTIVITY.'.item_id '.
			 $where.$like.' ORDER BY topic_id DESC'.$limit;
	$records['data'] = SP()->DB->select($query);
	$records['count'] = SP()->DB->select('SELECT FOUND_ROWS()', 'var');

	return $records;
}

function sp_subscriptions_get_forum_subscriptions($curpage, $search) {
    global $wpdb;

	# setup where we are in the list (paging)
	$startlimit = 0;
	if ($curpage != 1) $startlimit = ($curpage - 1) * 20;

	$limit = " LIMIT $startlimit, 20";

	# any search terms?
	$where = '';
	if (!empty($search)) $where = ' AND forum_name LIKE "%'.SP()->filters->esc_sql($wpdb->esc_like($search)).'%"';

	# retrieve watched topic records
	$query = 'SELECT SQL_CALC_FOUND_ROWS DISTINCT
			 group_name, forum_name, forum_id,
			 (SELECT GROUP_CONCAT(user_id) FROM '.SPUSERACTIVITY.' WHERE type_id='.SPACTIVITY_SUBSFORUM.' AND item_id=forum_id) AS members
			 FROM '.SPFORUMS.'
			 JOIN '.SPGROUPS.' ON '.SPGROUPS.'.group_id = '.SPFORUMS.'.group_id
			 JOIN '.SPUSERACTIVITY.' ON '.SPFORUMS.'.forum_id = '.SPUSERACTIVITY.'.item_id '.
			 $where.' ORDER BY forum_id DESC'.$limit;
	$records['data'] = SP()->DB->select($query);
	$records['count'] = SP()->DB->select('SELECT FOUND_ROWS()', 'var');

	return $records;
}

function sp_subscriptions_get_user_subscriptions($curpage, $search) {
    global $wpdb;

	# setup where we are in the list (paging)
	$startlimit = 0;
	if ($curpage != 1) $startlimit = ($curpage - 1) * 20;

	$limit = ' LIMIT '.$startlimit.', 20';

	# any search terms to add tp where clause?
	$like = 'WHERE item_id IS NOT NULL AND type_id = '.SPACTIVITY_SUBSTOPIC.' ';
	if (!empty($search)) $like.= 'AND display_name LIKE "%'.SP()->filters->esc_sql($wpdb->esc_like($search)).'%"';

	# retrieve watched topic records
	$query = 'SELECT SQL_CALC_FOUND_ROWS DISTINCT
			 '.SPMEMBERS.'.user_id, display_name,
			 (SELECT GROUP_CONCAT(item_id) FROM '.SPUSERACTIVITY.' WHERE type_id='.SPACTIVITY_SUBSTOPIC.' AND '.SPUSERACTIVITY.'.user_id='.SPMEMBERS.'.user_id) AS topics
			 FROM '.SPMEMBERS.'
			 JOIN '.SPUSERACTIVITY.' ON '.SPMEMBERS.'.user_id = '.SPUSERACTIVITY.'.user_id '.
			 $like.' ORDER BY '.SPMEMBERS.'.user_id DESC'.$limit;
	$records['data'] = SP()->DB->select($query);
	$records['count'] = SP()->DB->select('SELECT FOUND_ROWS()', 'var');

	return $records;
}

function sp_subscriptions_get_user_digests($curpage, $search) {
    global $wpdb;

	# setup where we are in the list (paging)
	$startlimit = 0;
	if ($curpage != 1) $startlimit = ($curpage - 1) * 20;

	$limit = " LIMIT $startlimit, 20";

	$where = ' WHERE subscribe_digest = 1';

	# any search terms?
	$like = '';
	if (!empty($search)) $like = ' AND display_name LIKE "%'.SP()->filters->esc_sql($wpdb->esc_like($search)).'%"';

	# retrieve watched topic records
	$query = 'SELECT SQL_CALC_FOUND_ROWS user_id, display_name, subscribe_digest
			 FROM '.SPMEMBERS.
			 $where.$like.' ORDER BY user_id DESC'.$limit;
	$records['data'] = SP()->DB->select($query);
	$records['count'] = SP()->DB->select('SELECT FOUND_ROWS()', 'var');
	return $records;
}
