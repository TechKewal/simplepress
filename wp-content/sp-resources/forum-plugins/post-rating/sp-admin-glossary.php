<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-rating';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('Post Rating',$pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-options/spa-options.php&tab=display";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Select rating style - thumbs or stars','$url','$pluginId')";
SP()->DB->execute($sql);

$url = "panel-forums/spa-forums.php";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Enable post rating on each forum required (edit forum)','$url','$pluginId')";
SP()->DB->execute($sql);

$id = sp_add_glossary_keyword('Forums',$pluginId);

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Enable post rating on each forum required (edit forum)','$url','$pluginId')";
SP()->DB->execute($sql);
