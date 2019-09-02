<?php
/*
Simple:Press Plugin Title: Membership Subscribe
Version: 2.1.0
Item Id: 3973
Plugin URI: https://simple-press.com/downloads/membership-subscribe-plugin/
Description: A Simple:Press plugin for auto subscribing (requires Subscriptions plugin) usergroup membership to a forum when adding a permission for the userggroup on a forum
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2013-04-17 19:24:03 -0700 (Wed, 17 Apr 2013) $
$Rev: 10182 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPMEMSUBDBVERSION', 0);

define('SPMEMSUBDIR', 		SPPLUGINDIR.'membership-subscribe/');
define('SPMEMSUBADMINDIR',  SPPLUGINDIR.'membership-subscribe/admin/');
define('SPMEMSUBLIBDIR', 	SPPLUGINDIR.'membership-subscribe/library/');
define('SPMEMSUBLIBURL', 	SPPLUGINURL.'membership-subscribe/library/');

add_action('init', 										                                    'sp_membership_subscribe_localization');
add_action('sph_activate_membership-subscribe/sp-membership-subscribe-plugin.php',          'sp_membership_subscribe_install');
add_action('sph_deactivate_membership-subscribe/sp-membership-subscribe-plugin.php',        'sp_membership_subscribe_deactivate');
add_action('sph_uninstall_membership-subscribe/sp-membership-subscribe-plugin.php',         'sp_membership_subscribe_uninstall');
add_action('sph_activated', 				                                                'sp_membership_subscribe_sp_activate');
add_action('sph_deactivated', 				                                                'sp_membership_subscribe_sp_deactivate');
add_action('sph_uninstalled', 								                                'sp_membership_subscribe_sp_uninstall');
add_action('sph_plugin_update_membership-subscribe/sp-membership-subscribe-plugin.php',     'sp_membership_subscribe_upgrade_check');
add_action('admin_footer',                                                                  'sp_membership_subscribe_upgrade_check');

add_filter('sph_plugins_active_buttons',        'sp_membership_subscribe_uninstall_option', 10, 2);

if (SP()->plugin->is_active('subscriptions/sp-subscriptions-plugin.php')) {
    add_action('sph_forums_add_perm_panel',     'sp_membership_subscribe_admin_options');
    add_action('sph_forum_perm_add', 			'sp_membership_subscribe_admin_save_options', 10, 3);
    add_filter('sph_admin_help-admin-forums',   'sp_membership_subscribe_admin_help', 10, 3);
}

function sp_membership_subscribe_localization() {
	sp_plugin_localisation('sp-membership-subscribe');
}

function sp_membership_subscribe_install() {
    require_once SPMEMSUBDIR.'sp-membership-subscribe-install.php';
    sp_membership_subscribe_do_install();
}

function sp_membership_subscribe_deactivate() {
    require_once SPMEMSUBDIR.'sp-membership-subscribe-uninstall.php';
    sp_membership_subscribe_do_deactivate();
}

function sp_membership_subscribe_uninstall() {
    require_once SPMEMSUBDIR.'sp-membership-subscribe-uninstall.php';
    sp_membership_subscribe_do_uninstall();
}

function sp_membership_subscribe_sp_activate() {
	require_once SPMEMSUBDIR.'sp-membership-subscribe-install.php';
    sp_membership_subscribe_do_sp_activate();
}

function sp_membership_subscribe_sp_deactivate() {
	require_once SPMEMSUBDIR.'sp-membership-subscribe-uninstall.php';
    sp_membership_subscribe_do_sp_deactivate();
}

function sp_membership_subscribe_sp_uninstall() {
	require_once SPMEMSUBDIR.'sp-membership-subscribe-uninstall.php';
    sp_membership_subscribe_do_sp_uninstall();
}

function sp_membership_subscribe_upgrade_check() {
    require_once SPMEMSUBDIR.'sp-membership-subscribe-upgrade.php';
    sp_membership_subscribe_do_upgrade_check();
}

function sp_membership_subscribe_uninstall_option($actionlink, $plugin) {
    require_once SPMEMSUBDIR.'sp-membership-subscribe-uninstall.php';
    $actionlink = sp_membership_subscribe_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_membership_subscribe_admin_options() {
    require_once SPMEMSUBADMINDIR.'sp-membership-subscribe-admin-options.php';
	sp_membership_subscribe_admin_options_form();
}

function sp_membership_subscribe_admin_save_options($forum_id, $usergroup_id, $permission) {
    require_once SPMEMSUBADMINDIR.'sp-membership-subscribe-admin-options-save.php';
    sp_membership_subscribe_admin_options_save($forum_id, $usergroup_id, $permission);
}

function sp_membership_subscribe_admin_help($file, $tag, $lang) {
    if ($tag == '[membership_subscribe]') $file = SPMEMSUBADMINDIR.'sp-membership-subscribe-admin-help.'.$lang;
    return $file;
}
