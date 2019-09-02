<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-linking';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('Blog Linking',$pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-components/spa-components.php&tab=plugin&admin=sp_linking_admin_form&save=sp_linking_admin_save&form=1";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Set Blog Post Linking options','$url','$pluginId'),
	($id,'Set Blog Post Linking post types','$url','$pluginId'),
	($id,'Set Blog Post comment options','$url','$pluginId'),
	($id,'Set the canonical URL for linked posts/topic','$url','$pluginId'),
	(22,'Set the canonical URL for linked posts/topic','$url','$pluginId')";
SP()->DB->execute($sql);
