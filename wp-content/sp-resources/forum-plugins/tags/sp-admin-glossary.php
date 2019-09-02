<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-tags';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('Tags',$pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_tags_admin_options&save=sp_tags_admin_save_options&form=1";
$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Setup topic tag options','$url','$pluginId')";
SP()->DB->execute($sql);

$url = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_tags_admin_manage&save=&form=0";
$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Manage current topic tags','$url','$pluginId')";
SP()->DB->execute($sql);

$url = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_tags_admin_edit&save=&form=1";
$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Mass edit topic tags','$url','$pluginId')";
SP()->DB->execute($sql);

$id = sp_add_glossary_keyword('Forums',$pluginId);
$url = "panel-forums/spa-forums.php";
$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Enable use of tags on forums (edit forum)','$url','$pluginId')";
SP()->DB->execute($sql);
