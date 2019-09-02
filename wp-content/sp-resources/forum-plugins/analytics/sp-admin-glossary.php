<?php
/*
Simple:Press
Desc: Database - admin glossary
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-analytics';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword( 'Analytics', $pluginId );

# tasks
# ------------------------------------------------------------

$url = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_analytics_main_view&save=0&form=0";


$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES  ($id, \"Charts\",'$url','$pluginId')";
SP()->DB->execute($sql);


$charts = sp_analytics_get_charts();
sp_analytics_add_charts_glossary_items( $charts );

