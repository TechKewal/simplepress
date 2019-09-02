<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2019-02-04 14:35:02 -0500 (Mon, 04 Fed 2019) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-push-notifications';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('Push Notifications',$pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-components/spa-components.php&tab=plugin&admin=sp_push_notifications_admin_members&save=sp_push_notifications_admin_save_members&form=1";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Setup push notifications API keys for PUSHBULLET, PUSHOVER and ONESIGNAL','$url','$pluginId')";
SP()->DB->execute($sql);

if (SP()->plugin->is_active('html-email/sp-html-email-plugin.php')) {
	$id = sp_add_glossary_keyword('Emails', $pluginId);

	$url = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_html_email_admin_subs&save=sp_html_email_admin_save_subs&form=1";
	$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
		($id,'Setup user notification of new subscribed post HTML email template','$url','$pluginId')";
	SP()->DB->execute($sql);

	$subs = SP()->options->get('subscriptions');
}
