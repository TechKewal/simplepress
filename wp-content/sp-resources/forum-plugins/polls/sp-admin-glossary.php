<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-polls';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('Polls',$pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_polls_admin_options&save=sp_polls_admin_options_save&form=1";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Setup poll mechanics and display options','$url','$pluginId')";
SP()->DB->execute($sql);

$url = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_polls_admin_manage&save=&form=0";
$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Manage and edit existing polls','$url','$pluginId')";
SP()->DB->execute($sql);

$id = sp_add_glossary_keyword('Forums',$pluginId);
$url = "panel-forums/spa-forums.php";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Enable poll creation (edit forum)','$url','$pluginId')";
SP()->DB->execute($sql);
