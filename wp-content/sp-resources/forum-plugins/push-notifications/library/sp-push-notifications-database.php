<?php
/*
Simple:Press
Topic Push Notifications plugin database routines
$LastChangedDate: 2019-02-04 14:35:02 -0500 (Mon, 04 Fed 2019) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

require_once SPPNLIBDIR.'sp-push-notifications-components.php';

function sp_push_notifications_remove_forum_subscription($forumid, $userid, $retmessage=true, $type='') {

	if (!$userid || !$forumid) return '';
    SP()->activity->delete('type='.$type."&item=$forumid");

	if ($retmessage) SP()->notifications->message(0, __('Subscription removed', 'sp-pushnotifications'));

}

function sp_push_notifications_remove_subscription($topicid, $userid, $retmessage=true, $sub_action='', $type='') {

	if (!$userid || !$topicid) return '';
	if (!SP()->user->thisUser->admin && SP()->user->thisUser->ID != $userid) return;

    SP()->activity->delete('type='.$type."&item=$topicid&uid=$userid");

	if ($retmessage) SP()->notifications->message(0, __('Subscription removed', 'sp-'.$sub_action));

}

function sp_push_notifications_save_forum_subscription($forumid, $userid, $retmessage=true, $auth='', $type='') {

	if (!$userid || !$forumid) return '';
	if (SP()->user->thisUser->guest || !SP()->auths->get($auth, $forumid, $userid)) return;
	if (!SP()->user->thisUser->admin && SP()->user->thisUser->ID != $userid) return;

	# is user already subscribed to this forum?
	if (SP()->activity->exist('type='.$type."&uid=$userid&item=$forumid")) {
		if ($retmessage) {
			SP()->notifications->message(1, __('You are already subscribed to this forum', 'sp-pushnotifications'));
			return;
		}
	}

	# OK  -subscribe them to the forum
	SP()->activity->add($userid, $type, $forumid, '', false);
	if ($retmessage) SP()->notifications->message(0, __('Subscription added', 'sp-pushnotifications'));

}

function sp_push_notifications_save_subscription($topicid, $userid, $retmessage=true, $sub_action='', $type='') {

	if (!$userid || !$topicid) return '';

	# is user already subscribed to this topic?
	if (SP()->activity->exist('type='.$type."&uid=$userid&item=$topicid")) {
		if ($retmessage) {
			SP()->notifications->message(1, __('You are already subscribed to this topic', 'sp-'.$sub_action));
			return;
		}
	}

	# OK - subscribe them to the topic
	SP()->activity->add($userid, $type, $topicid, '');

	if ($retmessage) SP()->notifications->message(0, __('Subscription added', 'sp-'.$sub_action));

}