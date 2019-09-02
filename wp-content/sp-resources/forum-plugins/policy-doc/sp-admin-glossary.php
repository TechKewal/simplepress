<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-policies';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('Policies',$pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-components/spa-components.php&tab=plugin&admin=sp_policy_doc_admin_options&save=sp_policy_doc_admin_save_options&form=1";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Setup registration and privacy policy text and options','$url','$pluginId')";
SP()->DB->execute($sql);
