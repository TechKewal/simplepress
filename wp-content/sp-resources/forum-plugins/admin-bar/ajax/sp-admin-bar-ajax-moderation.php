<?php
/*
Simple:Press
Admin Moderation and mark as read control
$LastChangedDate: 2011-07-17 22:08:22 +0100 (Sun, 17 Jul 2011) $
$Rev: 6704 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();
if (isset($_GET['targetaction'])) $action = SP()->filters->integer($_GET['targetaction']);
if (isset($_GET['pid'])) $postid = SP()->filters->integer($_GET['pid']);
if (isset($_GET['tid'])) $topicid = SP()->filters->integer($_GET['tid']);
if (isset($_GET['fid'])) $forumid = SP()->filters->integer($_GET['fid']);

if (empty($topicid)) die();

if (!SP()->auths->get('moderate_posts', $forumid)) die();

# actions:
#	0 = approve
#	1 = mark as read
#	2 = delete

switch ($action) {
	case 0:
		sp_approve_post(true, 0, $topicid, false, $forumid);
		echo __('All topic posts marked as approved', 'spab');
		break;

	case 1:
		sp_remove_from_waiting(true, $topicid);
		echo __('All topic posts marked as read', 'spab');
		break;

	case 2:
    if (empty($forumid) || empty($postid)) die();
		sp_delete_post($postid, $topicid, $forumid, false);
		echo __('Post deleted', 'spab');
		break;
}

# we always need to remove from users new posts list
sp_remove_users_newposts($topicid, SP()->user->thisUser->ID, true);

die();
