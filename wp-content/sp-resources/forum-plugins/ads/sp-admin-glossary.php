<?php

/*
  Simple:Press
  Desc: Database - admin glossary
 */

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('Access denied - you cannot directly call this file');

$pluginId = 'sp-ads';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('Ads', $pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-components/spa-components.php&tab=plugin&admin=sp_ads_ad_set_list&form=0";

$sql = "INSERT INTO " . SPADMINTASKS . " (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Setup Ads','$url','$pluginId')";
SP()->DB->execute($sql);
