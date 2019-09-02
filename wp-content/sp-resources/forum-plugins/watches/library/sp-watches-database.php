<?php
/*
Simple:Press
Topic Watches plugin database routines
$LastChangedDate: 2017-11-12 17:27:20 -0600 (Sun, 12 Nov 2017) $
$Rev: 15584 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

require_once WLIBDIR.'sp-watches-components.php';

function sp_watches_add_watches($topicid, $users) {
	if (!SP()->user->thisUser->admin && !SP()->user->thisUser->moderator) return;
    if (empty($topicid) || empty($users)) return;

    foreach ($users as $user) {
        sp_watches_save_watch($topicid, $user, false);
    }
	SP()->notifications->message(0, __('Topic watch(es) added', 'sp-watches'));
}

function sp_watches_do_member_del($userid) {
    SP()->activity->delete('type='.SPACTIVITY_WATCH."&uid=$userid");
}

function sp_watches_remove_watch($topicid, $userid, $retmessage=true) {
	if (!$userid || !$topicid) return '';
	if (!SP()->user->thisUser->admin && SP()->user->thisUser->ID != $userid) return;
    $message = SP()->activity->delete('type='.SPACTIVITY_WATCH."&uid=$userid&item=$topicid");
	if ($retmessage && $message) SP()->notifications->message(0, __('Topic watch removed', 'sp-watches'));
}

function sp_watches_save_watch($topicid, $userid, $retmessage=true) {
	if (!$userid || !$topicid) return;
	$forumid = SP()->DB->table(SPTOPICS, "topic_id=$topicid", 'forum_id');
	if (empty($forumid) || !SP()->auths->get('watch', $forumid, $userid)) return;

	# is user already watching this topic?
	if (SP()->activity->exist('type='.SPACTIVITY_WATCH."&uid=$userid&item=$topicid")) {
		if ($retmessage) SP()->notifications->message(1, __('You are already watching this topic', 'sp-watches'));
		return;
	}

	$message = SP()->activity->add($userid, SPACTIVITY_WATCH, $topicid);
	if ($retmessage && $message) SP()->notifications->message(0, __('Topic watch added', 'sp-watches'));
}

function sp_watches_remove_user_watches($userid) {
	# make sure user is doing this
	if (SP()->user->thisUser->admin || $userid == SP()->user->thisUser->ID) {
	    SP()->activity->delete('type='.SPACTIVITY_WATCH."&uid=$userid");
	}
}

function sp_watches_get_topic_watches($filter, $groups, $forums, $curpage, $search) {
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
	$where.='item_id IS NOT NULL AND type_id = '.SPACTIVITY_WATCH;

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

function sp_watches_get_user_watches($curpage, $search) {
    global $wpdb;

	# setup where we are in the list (paging)
	$startlimit = 0;
	if ($curpage != 1) $startlimit = ($curpage - 1) * 20;

	$limit = ' LIMIT '.$startlimit.', 20';

	# any search terms to add tp where clause?
	$like = 'WHERE item_id IS NOT NULL AND type_id = '.SPACTIVITY_WATCH.' ';
	if (!empty($search)) $like.= 'AND display_name LIKE "%'.SP()->filters->esc_sql($wpdb->esc_like($search)).'%"';

	# retrieve watched topic records
	$query = 'SELECT SQL_CALC_FOUND_ROWS DISTINCT
			 '.SPMEMBERS.'.user_id, display_name,
			 (SELECT GROUP_CONCAT(item_id) FROM '.SPUSERACTIVITY.' WHERE type_id=1 AND '.SPUSERACTIVITY.'.user_id='.SPMEMBERS.'.user_id) AS topics
			 FROM '.SPMEMBERS.'
			 JOIN '.SPUSERACTIVITY.' ON '.SPMEMBERS.'.user_id = '.SPUSERACTIVITY.'.user_id '.
			 $like.' ORDER BY '.SPMEMBERS.'.user_id DESC'.$limit;
	$records['data'] = SP()->DB->select($query);
	$records['count'] = SP()->DB->select('SELECT FOUND_ROWS()', 'var');

	return $records;
}

function sp_watches_remove_topic_watches($topicid) {
    SP()->activity->delete('type='.SPACTIVITY_WATCH."&item=$topicid");
}
