<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-sharethis';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('Sharing',$pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-components/spa-components.php&tab=plugin&admin=sp_share_this_admin_options&save=sp_share_this_admin_save_options&form=1&id=shareopt";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Setup share services, styling and display options','$url','$pluginId')";
SP()->DB->execute($sql);

$id = sp_add_glossary_keyword('Social Networks',$pluginId);

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Setup share services, styling and display options','$url','$pluginId')";
SP()->DB->execute($sql);
