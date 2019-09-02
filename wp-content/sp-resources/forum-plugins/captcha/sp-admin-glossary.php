<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-captcha';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('Captcha',$pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-components/spa-components.php&tab=login";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Use Captcha on the WP registration form','$url','$pluginId')";
SP()->DB->execute($sql);

$id = sp_add_glossary_keyword('Registration and Login',$pluginId);

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Use Captcha on the WP registration form','$url','$pluginId')";
SP()->DB->execute($sql);
