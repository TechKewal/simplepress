<?php
/*
Simple:Press Plugin Title: Moderation Email
Version: 2.1.0
Item Id: 47059
Plugin URI: https://simple-press.com/downloads/moderation-email-plugin/
Description: A Simple:Press plugin for sending email notifications to users when one of their posts held in moderation is approved
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2013-04-17 19:24:03 -0700 (Wed, 17 Apr 2013) $
$Rev: 10182 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPMODEMAILDBVERSION', 0);

define('SPMODEMAILDIR', 		SPPLUGINDIR.'moderation-email/');
define('SPMODEMAILADMINDIR',    SPPLUGINDIR.'moderation-email/admin/');
define('SPMODEMAILLIBDIR', 	    SPPLUGINDIR.'moderation-email/library/');
define('SPMODEMAILLIBURL', 	    SPPLUGINURL.'moderation-email/library/');

add_action('init', 										                        'sp_moderation_email_localization');
add_action('sph_activate_moderation-email/sp-moderation-email-plugin.php',      'sp_moderation_email_install');
add_action('sph_deactivate_moderation-email/sp-moderation-email-plugin.php',    'sp_moderation_email_deactivate');
add_action('sph_uninstall_moderation-email/sp-moderation-email-plugin.php',     'sp_moderation_email_uninstall');
add_action('sph_activated', 				                                    'sp_moderation_email_sp_activate');
add_action('sph_deactivated', 				                                    'sp_moderation_email_sp_deactivate');
add_action('sph_uninstalled', 								                    'sp_moderation_email_sp_uninstall');
add_action('sph_plugin_update_moderation-email/sp-moderation-email-plugin.php', 'sp_moderation_email_upgrade_check');
add_action('admin_footer',                                                      'sp_moderation_email_upgrade_check');
add_action('sph_options_email_right_panel', 				                    'sp_moderation_email_admin_options');
add_action('sph_option_email_save', 							                'sp_moderation_email_admin_save_options');
add_action('sph_post_approved',                                                 'sp_moderation_email_posts_approved');

add_filter('sph_plugins_active_buttons',        'sp_moderation_email_uninstall_option', 10, 2);
add_filter('sph_admin_help-admin-options', 	    'sp_moderation_email_admin_help', 10, 3);

function sp_moderation_email_admin_options() {
    require_once SPMODEMAILADMINDIR.'sp-moderation-email-admin-options.php';
	sp_moderation_email_admin_options_form();
}

function sp_moderation_email_admin_save_options() {
    require_once SPMODEMAILADMINDIR.'sp-moderation-email-admin-options-save.php';
    return sp_moderation_email_admin_options_save();
}

function sp_moderation_email_admin_help($file, $tag, $lang) {
    if ($tag == '[moderation-email]') $file = SPMODEMAILADMINDIR.'sp-moderation-email-admin-help.'.$lang;
    return $file;
}

function sp_moderation_email_localization() {
	sp_plugin_localisation('sp-moderation-email');
}

function sp_moderation_email_install() {
    require_once SPMODEMAILDIR.'sp-moderation-email-install.php';
    sp_moderation_email_do_install();
}

function sp_moderation_email_deactivate() {
    require_once SPMODEMAILDIR.'sp-moderation-email-uninstall.php';
    sp_moderation_email_do_deactivate();
}

function sp_moderation_email_uninstall() {
    require_once SPMODEMAILDIR.'sp-moderation-email-uninstall.php';
    sp_moderation_email_do_uninstall();
}

function sp_moderation_email_sp_activate() {
	require_once SPMODEMAILDIR.'sp-moderation-email-install.php';
    sp_moderation_email_do_sp_activate();
}

function sp_moderation_email_sp_deactivate() {
	require_once SPMODEMAILDIR.'sp-moderation-email-uninstall.php';
    sp_moderation_email_do_sp_deactivate();
}

function sp_moderation_email_sp_uninstall() {
	require_once SPMODEMAILDIR.'sp-moderation-email-uninstall.php';
    sp_moderation_email_do_sp_uninstall();
}

function sp_moderation_email_upgrade_check() {
    require_once SPMODEMAILDIR.'sp-moderation-email-upgrade.php';
    sp_moderation_email_do_upgrade_check();
}

function sp_moderation_email_uninstall_option($actionlink, $plugin) {
    require_once SPMODEMAILDIR.'sp-moderation-email-uninstall.php';
    $actionlink = sp_moderation_email_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_moderation_email_posts_approved($approved_posts) {
	require_once SPMODEMAILLIBDIR.'sp-moderation-email-components.php';
    sp_moderation_email_do_posts_approved($approved_posts);
}
