<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-buddypress';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('BuddyPress',$pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-components/spa-components.php&tab=plugin&admin=sp_buddypress_admin_options&save=sp_buddypress_admin_save_options&form=1";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Set up BuddyPress/Simple:Press integration options','$url','$pluginId')";
SP()->DB->execute($sql);

$id = sp_add_glossary_keyword('User Profiles',$pluginId);

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Set up BuddyPress/Simple:Press integration profile options','$url','$pluginId'),
	($id,'Set up BuddyPress/Simple:Press integration avatar options','$url','$pluginId')";
SP()->DB->execute($sql);

$id = sp_add_glossary_keyword('Avatars',$pluginId);

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Set up BuddyPress/Simple:Press integration avatar options','$url','$pluginId')";
SP()->DB->execute($sql);
