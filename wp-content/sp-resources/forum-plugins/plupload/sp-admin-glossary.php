<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-plupload';
$url = "panel-components/spa-components.php&tab=plugin&admin=sp_plupload_admin_options&save=sp_plupload_admin_save_options&form=1";

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('File Uploader',$pluginId);

# tasks
# ------------------------------------------------------------
$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Set image upload type and size constraints','$url','$pluginId'),
	($id,'Set media upload type and size constraints','$url','$pluginId'),
	($id,'Set file upload type and size constraints','$url','$pluginId'),
	($id,'Control prohibited upload file types','$url','$pluginId'),
	(11,'Set image upload type and size constraints','$url','$pluginId'),
	(10,'Control attachments list display in posts','$url','$pluginId'),
	(11,'Image display options','panel-options/spa-options.php&tab=content','$pluginId')";
SP()->DB->execute($sql);

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('Video and Sound',$pluginId);

# tasks
# ------------------------------------------------------------
$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Set media upload type and size constraints','$url','$pluginId')";
SP()->DB->execute($sql);
