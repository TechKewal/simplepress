<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-reportpost';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('Report Post',$pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-options/spa-options.php&tab=email";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Maintain list of recipient email addresses for reported posts','$url','$pluginId')";
SP()->DB->execute($sql);

if (SP()->plugin->is_active('html-email/sp-html-email-plugin.php')) {
	$id = sp_add_glossary_keyword('Emails', $pluginId);
	$url = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_html_email_admin_report&save=sp_html_email_admin_save_report&form=1";

	$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
		($id,'Setup Admin notification of reported post HTML email template','$url','$pluginId')";
	SP()->DB->execute($sql);
}
