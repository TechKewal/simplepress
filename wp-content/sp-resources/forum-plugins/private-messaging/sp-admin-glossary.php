<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-pmessaging';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('Private Messaging',$pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_pm_admin_options&save=sp_pm_admin_save_options&form=1";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Setup private messaging control options','$url','$pluginId'),
	($id,'Setup private messaging addressing options','$url','$pluginId'),
	($id,'Turn on/off auto removal of old messages','$url','$pluginId')";
SP()->DB->execute($sql);

$url = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_pm_admin_stats&save=&form=0";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'View users private mnessaging statistics','$url','$pluginId')";
SP()->DB->execute($sql);

$id = sp_add_glossary_keyword('Statistics',$pluginId);
$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'View users private mnessaging statistics','$url','$pluginId')";
SP()->DB->execute($sql);

if (SP()->plugin->is_active('html-email/sp-html-email-plugin.php')) {
	$id = sp_add_glossary_keyword('Emails', $pluginId);
	$url = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_html_email_admin_newpm&save=sp_html_email_admin_save_newpm&form=1";

	$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
		($id,'Setup User notification of private message custom HTML email template','$url','$pluginId')";
	SP()->DB->execute($sql);
}
