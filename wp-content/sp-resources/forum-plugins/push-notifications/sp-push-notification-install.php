<?php
/*
Simple:Press
Push Notifications plugin install/upgrade routine
$LastChangedDate: 2019-02-04 14:35:02 -0500 (Mon, 04 Fed 2019) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_push_notifications_do_install() {

	SP()->activity->add_type('topic pushover notifications');
	SP()->activity->add_type('forum pushover notifications');
	SP()->activity->add_type('topic pushbullet notifications');
	SP()->activity->add_type('forum pushbullet notifications');
	SP()->activity->add_type('topic onesignal notifications');
	SP()->activity->add_type('forum onesignal notifications');

	$subs = SP()->options->get('push-notifications');

	if (empty($subs)) {
		
		$subs = array();
		$subs['pushover'] = false;
        $subs['pushbullet'] = false;
		$subs['onesignal'] = false;
		$subs['onesignal_rest_api_key'] = false;
		$subs['forumpushover'] = false;
        $subs['forumpushbullet'] = false;
		$subs['forumonesignal'] = false;
		$subs['dbversion'] = SPPNSUBSDBVERSION;
		
		SP()->options->add('push-notifications', $subs);
    }

    # add options to possible profile display control list if in use
    if (function_exists('sp_profile_display_control_add_item')) {
        sp_profile_display_control_add_item(
			'options-sub-auto', 
			true, 
			__('Auto Subscribe (posting options form)', 'sp-pushnotifications'), 
			'sph_ProfileUserSubsAutoSub', 
			'sph_ProfileUserSubsAutoSubUpdate'
		);
    }

	# add profile tabs/menus
	SP()->profile->add_tab('Push Notifications');
	SP()->profile->add_menu('Push Notifications', 'Topic Pushover Subscriptions', SPPNFORMSDIR.'sp-push-notifications-pushover-form.php');
	SP()->profile->add_menu('Push Notifications', 'Topic Pushbullet Subscriptions', SPPNFORMSDIR.'sp-push-notifications-pushbullet-form.php');
	SP()->profile->add_menu('Push Notifications', 'Topic Onesignal Subscriptions', SPPNFORMSDIR.'sp-push-notifications-onesignal-form.php');
	
	SP()->auths->add('pushover', __('Can subscribe to PUSHOVER notifications', 'sp-pushover'), 1, 1, 0, 0, 1);
	SP()->auths->add('pushbullet', __('Can subscribe to PUSHBULLET notfications', 'sp-pushbullet'), 1, 1, 0, 0, 1);
	SP()->auths->add('onesignal', __('Can subscribe to ONESIGNAL notifications', 'sp-onesignal'), 1, 1, 0, 0, 1);

	SP()->profile->add_menu('Profile', 'Push Notifications API Keys', SPPNFORMSDIR.'sp-push-notifications-api-keys-form.php');

    # activation so make our auth active
    SP()->auths->activate('pushover');
    SP()->auths->activate('pushbullet');
	SP()->auths->activate('onesignal');

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';

	# flush rewrite rules for pretty permalinks
	global $wp_rewrite;
	$wp_rewrite->flush_rules();

	if(SPPNSITEPROTOCOL == 'https'){
		$fileList = glob(get_home_path().'wp-content/sp-resources/forum-plugins/push-notifications/resources/jscript/onesignal/*');
		foreach($fileList as $filepath){
			$filename = explode('/', $filepath);
			$new_filepath = get_home_path().$filename[count($filename) - 1];
			rename($filepath, $new_filepath);
		}
	}
}

function sp_push_notifications_do_reset_permissions() {
	SP()->auths->add('subscribe', __('Can subscribe to forums (if enabled) and topics', 'sp-pushnotifications'), 1, 1, 0, 0, 1);
}