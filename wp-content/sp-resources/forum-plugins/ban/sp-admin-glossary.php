<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-ban';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('Ban',$pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-users/spa-users.php&tab=plugin&admin=sp_ban_admin&save=sp_ban_admin_save&form=0&id=banpanel";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Set up, modify and remove user bans','$url','$pluginId')";
SP()->DB->execute($sql);

$id = sp_add_glossary_keyword('Users - Members',$pluginId);
$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Set up, modify and remove user bans','$url','$pluginId')";
SP()->DB->execute($sql);
