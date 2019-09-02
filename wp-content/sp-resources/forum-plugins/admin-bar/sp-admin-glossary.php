<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-adminbar';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('Admins and Moderators',$pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-admins/spa-admins.php&tab=globaladmin";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Turn on Akismet for post spam checking','$url','$pluginId'),
	($id,'Turn on the Admin Bar (admin Post Bag)','panel-admins/spa-admins.php','$pluginId')";
SP()->DB->execute($sql);
