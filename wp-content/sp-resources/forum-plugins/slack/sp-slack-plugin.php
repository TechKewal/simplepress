<?php
/*
Simple:Press Plugin Title: Slack Integration
Version: 2.1.0
Item Id: 4422
Plugin URI: https://simple-press.com/downloads/slack-integration-plugin/
Description: A Simple:Press plugin for adding slack integration for forum activity
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPSLACKDBVERSION', 1);

define('SPSLACKDIR', 		SPPLUGINDIR.'slack/');
define('SPSLACKADMINDIR',   SPPLUGINDIR.'slack/admin/');
define('SPSLACKLIBDIR',     SPPLUGINDIR.'slack/library/');

add_action('init', 										     'sp_slack_localization');
add_action('sph_activate_slack/sp-slack-plugin.php',         'sp_slack_install');
add_action('sph_deactivate_slack/sp-slack-plugin.php',       'sp_slack_deactivate');
add_action('sph_uninstall_slack/sp-slack-plugin.php',        'sp_slack_uninstall');
add_action('sph_activated', 				                 'sp_slack_sp_activate');
add_action('sph_deactivated', 				                 'sp_slack_sp_deactivate');
add_action('sph_uninstalled', 								 'sp_slack_sp_uninstall');
add_action('sph_plugin_update_slack/sp-slack-plugin.php',    'sp_slack_upgrade_check');
add_action('admin_footer',                                   'sp_slack_upgrade_check');
add_action('sph_admin_menu',								 'sp_slack_menu');

add_filter('sph_plugins_active_buttons',        'sp_slack_uninstall_option', 10, 2);
add_filter('sph_new_post',                      'sp_slack_new_post_notify');
add_filter('sph_member_created',                'sp_slack_new_user_notify');
add_filter('sph_admin_help-admin-components',	'sp_slack_admin_help', 10, 3);

function sp_slack_menu() {
	$subpanels = array(__('Slack Integration', 'sp-slack') => array('admin' => 'sp_slack_admin_options', 'save' => 'sp_slack_admin_save_options', 'form' => 1, 'id' => 'slackopt'));
	SP()->plugin->add_admin_subpanel('components', $subpanels);
}

function sp_slack_admin_options() {
	require_once SPSLACKADMINDIR.'sp-slack-admin-options.php';
	sp_slack_admin_options_form();
}

function sp_slack_admin_save_options() {
	require_once SPSLACKADMINDIR.'sp-slack-admin-options-save.php';
	return sp_slack_admin_options_save();
}

function sp_slack_admin_help($file, $tag, $lang) {
	if ($tag == '[slack]' || $tag == '[slack-notify]') $file = SPSLACKADMINDIR.'sp-slack-admin-help.'.$lang;
	return $file;
}

function sp_slack_localization() {
	sp_plugin_localisation('sp-slack');
}

function sp_slack_install() {
    require_once SPSLACKDIR.'sp-slack-install.php';
    sp_slack_do_install();
}

function sp_slack_deactivate() {
    require_once SPSLACKDIR.'sp-slack-uninstall.php';
    sp_slack_do_deactivate();
}

function sp_slack_uninstall() {
    require_once SPSLACKDIR.'sp-slack-uninstall.php';
    sp_slack_do_uninstall();
}

function sp_slack_sp_activate() {
	require_once SPSLACKDIR.'sp-slack-install.php';
    sp_slack_do_sp_activate();
}

function sp_slack_sp_deactivate() {
	require_once SPSLACKDIR.'sp-slack-uninstall.php';
    sp_slack_do_sp_deactivate();
}

function sp_slack_sp_uninstall() {
	require_once SPSLACKDIR.'sp-slack-uninstall.php';
    sp_slack_do_sp_uninstall();
}

function sp_slack_upgrade_check() {
    require_once SPSLACKDIR.'sp-slack-upgrade.php';
    sp_slack_do_upgrade_check();
}

function sp_slack_uninstall_option($actionlink, $plugin) {
    require_once SPSLACKDIR.'sp-slack-uninstall.php';
    $actionlink = sp_slack_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_slack_new_post_notify($newpost) {
    require_once SPSLACKLIBDIR.'sp-slack-components.php';
    sp_slack_do_new_post_notify($newpost);
}

function sp_slack_new_user_notify($userid) {
    require_once SPSLACKLIBDIR.'sp-slack-components.php';
    sp_slack_do_new_user_notify($userid);
}
