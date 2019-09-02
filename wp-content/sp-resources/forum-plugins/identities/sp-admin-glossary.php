<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-identities';

# tasks
# ------------------------------------------------------------
$url = "panel-profiles/spa-profiles.php&tab=plugin&admin=sp_identities_admin&save=sp_identities_update&form=0";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	(6,'Add new user identity','$url','$pluginId'),
	(15,'Add new user identity','$url','$pluginId'),
	(33,'Add new user identity','$url','$pluginId')";
SP()->DB->execute($sql);
