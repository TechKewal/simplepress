<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-warnings';

# keywords
# ------------------------------------------------------------

$opsurl = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_warnings_suspensions_admin_options&save=sp_warnings_suspensions_admin_save_options&form=1";
$warurl = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_warnings_suspensions_warnings&save=sp_warnings_suspensions_warnings_save&form=0";
$susurl = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_warnings_suspensions_suspensions&save=sp_warnings_suspensions_suspensions_save&form=0";
$banurl = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_warnings_suspensions_bans&save=sp_warnings_suspensions_bans_save&form=0";

$id = sp_add_glossary_keyword('User Warnings',$pluginId);
$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Setup the user warning options and messages','$opsurl','$pluginId'),
	($id,'Add new and review current user warnings','$warurl','$pluginId')";
SP()->DB->execute($sql);

$id = sp_add_glossary_keyword('User Suspensions',$pluginId);
$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Setup the user suspension options and messages','$opsurl','$pluginId'),
	($id,'Add new and review current user suspensions','$susurl','$pluginId')";
SP()->DB->execute($sql);

$id = sp_add_glossary_keyword('User Bans',$pluginId);
$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Setup the user ban options and messages','$opsurl','$pluginId'),
	($id,'Add new and review current user bans','$banurl','$pluginId')";
SP()->DB->execute($sql);

$id = sp_add_glossary_keyword('Users - Members',$pluginId);
$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Add new and review current user warnings','$warurl','$pluginId'),
	($id,'Add new and review current user suspensions','$susurl','$pluginId'),
	($id,'Add new and review current user bans','$banurl','$pluginId')";
SP()->DB->execute($sql);
