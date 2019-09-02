<?php
/*
Simple:Press
Push Notifications Plugin Admin Members Save Routine
$LastChangedDate: 2019-02-04 14:35:02 -0500 (Mon, 04 Fed 2019) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_push_notifications_admin_members_save() {
	
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$subs = SP()->options->get('push-notifications');

	if (isset($_POST['pushover'])) { $subs['pushover'] = SP()->filters->str($_POST['pushover']); } else { $subs['pushover'] = false; }
	if (isset($_POST['pushbullet'])) { $subs['pushbullet'] = SP()->filters->str($_POST['pushbullet']); } else { $subs['pushbullet'] = false; }
	if (isset($_POST['onesignal'])) { $subs['onesignal'] = SP()->filters->str($_POST['onesignal']); } else { $subs['onesignal'] = false; }
	if (isset($_POST['onesignal_rest_api_key'])) { $subs['onesignal_rest_api_key'] = SP()->filters->str($_POST['onesignal_rest_api_key']); } else { $subs['onesignal_rest_api_key'] = false; }
	if (isset($_POST['forumpushover'])) { $subs['forumpushover'] = SP()->filters->str($_POST['forumpushover']); } else { $subs['forumpushover'] = false; }
	if (isset($_POST['forumpushbullet'])) { $subs['forumpushbullet'] = SP()->filters->str($_POST['forumpushbullet']); } else { $subs['forumpushbullet'] = false; }
	if (isset($_POST['forumonesignal'])) { $subs['forumonesignal'] = SP()->filters->str($_POST['forumonesignal']); } else { $subs['forumonesignal'] = false; }

	SP()->options->update('push-notifications', $subs);

	return __('Options updated', 'sp-pushnotifications');
}
