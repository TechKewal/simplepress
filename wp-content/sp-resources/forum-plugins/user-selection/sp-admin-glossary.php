<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-language';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('Translations', $pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-toolbox/spa-toolbox.php&tab=plugin&admin=sp_user_selection_admin_options&save=sp_user_selection_admin_save_options&form=1";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Set language names for the language switcher','$url','$pluginId')";
SP()->DB->execute($sql);
