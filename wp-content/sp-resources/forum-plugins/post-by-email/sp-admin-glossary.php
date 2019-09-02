<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-postbyemail';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('Post by Email',$pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-options/spa-options.php&tab=email";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Maintain post by email connection settings','$url','$pluginId'),
	($id,'Test connection settings','$url','$pluginId')";
SP()->DB->execute($sql);

$id = sp_add_glossary_keyword('Forums',$pluginId);
$url = "panel-forums/spa-forums.php";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Add unique forum email addresses (edit forum)','$url','$pluginId')";
SP()->DB->execute($sql);
