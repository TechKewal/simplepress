<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-tinymce';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('TinyMCE Editor',$pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-components/spa-components.php&tab=plugin&admin=sp_tinymce_form&save=sp_tinymce_save&form=1";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Add/Remove TinyMCE editor toolbar options','$url','$pluginId'),
	($id,'Control embedded formatting when pasting content using TinyMCE','$url','$pluginId'),
	(43,'Add/Remove TinyMCE editor toolbar options','$url','$pluginId'),
	(43,'Control embedded formatting when pasting content using TinyMCE','$url','$pluginId')";
SP()->DB->execute($sql);
