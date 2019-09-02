<?php
/*
Simple:Press
Push Notification Plugin Admin Members Form
$LastChangedDate: 2019-02-04 14:35:02 -0500 (Mon, 04 Fed 2019) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_push_notifications_admin_members_form() {

	$subs = array();
	$subs = SP()->options->get('push-notifications');

	spa_paint_options_init();

	spa_paint_open_tab(__('Components', 'sp-pushnotifications').' - '.__('Push Notification', 'sp-pushnotifications'));
    	spa_paint_open_panel();
    		spa_paint_open_fieldset(__('Options', 'sp-pushnotifications'), true, 'push-notifications-options');
				spa_paint_wide_textarea(__('PushOver API Key', 'sp-pushnotifications'), 'pushover', $subs['pushover'], '', 1);
				spa_paint_wide_textarea(__('PushBullet API Key', 'sp-pushnotifications'), 'pushbullet', $subs['pushbullet'], '', 1);
				spa_paint_wide_textarea(__('OneSignal APP ID', 'sp-pushnotifications'), 'onesignal', $subs['onesignal'], '', 1);
				spa_paint_wide_textarea(__('OneSignal REST API KEY', 'sp-pushnotifications'), 'onesignal_rest_api_key', $subs['onesignal_rest_api_key'], '', 1);
			spa_paint_close_fieldset();
    	spa_paint_close_panel();
	spa_paint_close_container();

}