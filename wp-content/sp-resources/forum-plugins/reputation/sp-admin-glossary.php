<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-reputation';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('User Reputation',$pluginId);
$uid = sp_add_glossary_keyword('Users - Members',$pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_reputation_admin_options&save=sp_reputation_admin_save_options&form=1";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Setup the user reputation options and controls','$url','$pluginId')";
SP()->DB->execute($sql);
$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($uid,'Setup the user reputation options and controls','$url','$pluginId')";
SP()->DB->execute($sql);

$url = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_reputation_admin_levels&save=&form=0";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Setup the user reputation levels','$url','$pluginId')";
SP()->DB->execute($sql);
$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($uid,'Setup the user reputation levels','$url','$pluginId')";
SP()->DB->execute($sql);

$url = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_reputation_admin_reset&save=&form=0";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Reset the user reputation options, controls and user data','$url','$pluginId')";
SP()->DB->execute($sql);
$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($uid,'Reset the user reputation options, controls and user data','$url','$pluginId')";
SP()->DB->execute($sql);
