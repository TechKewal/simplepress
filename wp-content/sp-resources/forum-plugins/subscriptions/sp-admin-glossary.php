<?php
/*
Simple:Press
Desc: Database - admin glossary
$LastChangedDate: 2014-05-24 09:12:47 +0100 (Sat, 24 May 2014) $
$Rev: 11461 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

$pluginId = 'sp-subscriptions';

# keywords
# ------------------------------------------------------------
$id = sp_add_glossary_keyword('Subscriptions',$pluginId);

# tasks
# ------------------------------------------------------------
$url = "panel-components/spa-components.php&tab=plugin&admin=sp_subscriptions_admin_members&save=sp_subscriptions_admin_save_members&form=1";

$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
	($id,'Setup subscription and subscription digest options','$url','$pluginId')";
SP()->DB->execute($sql);

if (SP()->plugin->is_active('html-email/sp-html-email-plugin.php')) {
	$id = sp_add_glossary_keyword('Emails', $pluginId);

	$url = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_html_email_admin_subs&save=sp_html_email_admin_save_subs&form=1";
	$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
		($id,'Setup user notification of new subscribed post HTML email template','$url','$pluginId')";
	SP()->DB->execute($sql);

	$subs = SP()->options->get('subscriptions');
	if ($subs['digestsub']) {
		$url = "panel-plugins/spa-plugins.php&tab=plugin&admin=sp_html_email_admin_digests&save=sp_html_email_admin_save_digests&form=1";
		$sql = "INSERT INTO ".SPADMINTASKS." (`keyword_id`, `task`, `url`, `plugin`) VALUES
			($id,'Setup user subscription digest email HTML email template','$url','$pluginId')";
		SP()->DB->execute($sql);
    }
}
