<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-eventlog';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('Event Logging',$pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_logger_admin_options&save=sp_logger_admin_options_save&form=1";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Setup and manage post and topic events to log','$url','$pluginId')";
SP()->DB->execute($sql);

$url = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_logger_admin_view&save=&form=0";
$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'View the post and topic events log','$url','$pluginId')";
SP()->DB->execute($sql);
