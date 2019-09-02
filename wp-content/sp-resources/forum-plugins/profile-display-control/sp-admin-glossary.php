<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-profiledisplay';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('User Profiles',$pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-profiles/spa-profiles.php&tab=plugin&admin=sp_profile_display_control_admin&save=sp_profile_display_control_update&form=1";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Exclude profile items you do not wish to make available','$url','$pluginId')";
SP()->DB->execute($sql);
