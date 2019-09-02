<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-topicstatus';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('Topic Status',$pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-components/spa-components.php&tab=plugin&admin=spa_topicstatus_admin_form&save=spa_topicstatus_admin_save&form=1";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Create and maintain topic status sets','$url','$pluginId')";
SP()->DB->execute($sql);

$id = sp_add_glossary_keyword('Topics',$pluginId);
$url = "panel-forums/spa-forums.php";
$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Create and maintain topic status sets','$url','$pluginId')";
SP()->DB->execute($sql);

$id = sp_add_glossary_keyword('Forums',$pluginId);
$url = "panel-forums/spa-forums.php";
$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Enable use of a topic status set on forums (edit forum)','$url','$pluginId')";
SP()->DB->execute($sql);
