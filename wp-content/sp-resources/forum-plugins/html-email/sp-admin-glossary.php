<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-emails';

# keywords
# ------------------------------------------------------------
$emailid = sp_add_glossary_keyword('Emails', $pluginId);
$regid = sp_add_glossary_keyword('Registration and Login', $pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_html_email_global_settings&save=sp_html_email_admin_save_global&form=1";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($emailid,'Setup global CSS style rules, header and footer fopr custom HTML emails','$url','$pluginId')";
SP()->DB->execute($sql);

$url = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_html_email_admin_posts&save=sp_html_email_admin_save_posts&form=1";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($emailid,'Setup the Admin notification of new posts custom HTML email template','$url','$pluginId')";
SP()->DB->execute($sql);

$url = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_html_email_admin_new_user&save=sp_html_email_admin_save_new_user&form=1";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($emailid,'Setup Admin/User notification of new user custom HTML email template','$url','$pluginId'),
	($regid,'Setup Admin/User notification of new user custom HTML email template','$url','$pluginId')";
SP()->DB->execute($sql);

$url = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_html_email_admin_pw_change&save=sp_html_email_admin_save_pw_change&form=1";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($emailid,'Setup Admin/User notification of password change custom HTML email template','$url','$pluginId'),
	($regid,'Setup Admin/User notification of password change custom HTML email template','$url','$pluginId')";
SP()->DB->execute($sql);
