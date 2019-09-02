<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-featured';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('Featured Topics and Posts',$pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-components/spa-components.php&tab=plugin&admin=sp_featured_admin_options&save=sp_featured_admin_save_options&form=1";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Maintain list of featured topics and/or posts','$url','$pluginId')";
SP()->DB->execute($sql);

$id = sp_add_glossary_keyword('Topics',$pluginId);

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Maintain list of featured topics','$url','$pluginId')";
SP()->DB->execute($sql);

$id = sp_add_glossary_keyword('Posts',$pluginId);

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Maintain list of featured posts','$url','$pluginId')";
SP()->DB->execute($sql);
