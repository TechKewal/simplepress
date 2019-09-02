<?php
/*
Simple:Press Plugin Title: Warnings and Suspensions
Version: 2.1.0
Item Id: 3924
Plugin URI: https://simple-press.com/downloads/warnings-and-suspensions-plugin/
Description: A Simple:Press plugin for warning, suspending and banning users
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2016-03-26 15:14:21 -0700 (Sat, 26 Mar 2016) $
$Rev: 14086 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPWARNDBVERSION', 3);

define('SPWARNINGS', 		SP_PREFIX.'sfwarnings');

define('SPWARNWARNING',     1);
define('SPWARNSUSPENSION',  2);
define('SPWARNBAN',         3);

define('SPWARNDIR', 		SPPLUGINDIR.'warnings-suspensions/');
define('SPWARNADMINDIR',    SPPLUGINDIR.'warnings-suspensions/admin/');
define('SPWARNAJAXDIR', 	SPPLUGINDIR.'warnings-suspensions/ajax/');
define('SPWARNLIBDIR', 	    SPPLUGINDIR.'warnings-suspensions/library/');
define('SPWARNLIBURL', 	    SPPLUGINURL.'warnings-suspensions/library/');
define('SPWARNCSS', 		SPPLUGINURL.'warnings-suspensions/resources/css/');
define('SPWARNIMAGES', 	    SPPLUGINURL.'warnings-suspensions/resources/images/');
define('SPWARNSCRIPT', 	    SPPLUGINURL.'warnings-suspensions/resources/jscript/');

add_action('init', 										                                    'sp_warnings_suspensions_localization');
add_action('sph_activate_warnings-suspensions/sp-warnings-suspensions-plugin.php',          'sp_warnings_suspensions_install');
add_action('sph_deactivate_warnings-suspensions/sp-warnings-suspensions-plugin.php',        'sp_warnings_suspensions_deactivate');
add_action('sph_uninstall_warnings-suspensions/sp-warnings-suspensions-plugin.php',         'sp_warnings_suspensions_uninstall');
add_action('sph_activated', 				                                                'sp_warnings_suspensions_sp_activate');
add_action('sph_deactivated', 				                                                'sp_warnings_suspensions_sp_deactivate');
add_action('sph_uninstalled', 								                                'sp_warnings_suspensions_sp_uninstall');
add_action('sph_plugin_update_warnings-suspensions/sp-warnings-suspensions-plugin.php',     'sp_warnings_suspensions_upgrade_check');
add_action('admin_footer',                                                                  'sp_warnings_suspensions_upgrade_check');
add_action('sph_permissions_reset', 						                                'sp_warnings_suspensions_reset_permissions');
add_action('sph_admin_menu', 									                            'sp_warnings_suspensions_menu');
add_action('sph_scripts_admin_end', 						                                'sp_warnings_suspensions_load_admin_js');
add_action('sph_admin_caps_form', 					     	                                'sp_warnings_suspensions_admin_cap_form', 10, 2);
add_action('sph_admin_caps_list', 						                                    'sp_warnings_suspensions_admin_cap_list', 10, 2);
add_action('wp',                                                                            'sp_warnings_suspensions_scheduler');
add_action('sph_warnings_cron',                                                             'sp_warnings_suspensions_cron_handler');
add_action('sph_admin_menu',                                                                'sp_warnings_suspensions_admin_menu');
add_action('sph_add_style',																	'sp_warnings_suspensions_add_style_icon');

add_filter('sph_plugins_active_buttons',    'sp_warnings_suspensions_uninstall_option', 10, 2);
add_filter('sph_admin_caps_new', 			'sp_warnings_suspensions_admin_caps_new', 10, 2);
add_filter('sph_admin_caps_update', 		'sp_warnings_suspensions_admin_caps_update', 10, 3);
add_filter('sph_ProfileFormTop',            'sp_warnings_suspensions_profile_message', 5, 3);
add_filter('sph_admin_help-admin-plugins', 	'sp_warnings_suspensions_admin_help', 10, 3);
add_action('sph_print_plugin_styles',       'sp_warnings_suspensions_head');
add_action('sph_print_plugin_scripts',      'sp_warnings_suspensions_load_js');
add_filter('sph_add_post_tool',             'sp_warnings_suspensions_forum_tools', 10, 10);
add_filter('sph_ShowAdminLinks', 		    'sp_warnings_suspensions_admin_links', 10, 2);

# Ajax Actions
add_action('wp_ajax_warnings-suspensions-admin',		'sp_warnings_suspensions_ajax_admin');
add_action('wp_ajax_nopriv_warnings-suspensions-admin',	'sp_warnings_suspensions_ajax_admin');

# TODO - handle expirations timing out

function sp_warnings_suspensions_menu() {
	$panels = array(
                __('Options', 'sp-warnings-suspensions') => array('admin' => 'sp_warnings_suspensions_admin_options', 'save' => 'sp_warnings_suspensions_admin_save_options', 'form' => 1, 'id' => 'wsopt'),
                __('Warnings', 'sp-warnings-suspensions') => array('admin' => 'sp_warnings_suspensions_warnings', 'save' => 'sp_warnings_suspensions_warnings_save', 'form' => 0, 'id' => 'add-warning'),
                __('Suspensions', 'sp-warnings-suspensions') => array('admin' => 'sp_warnings_suspensions_suspensions', 'save' => 'sp_warnings_suspensions_suspensions_save', 'form' => 0, 'id' => 'add-suspension'),
                __('Bans', 'sp-warnings-suspensions') => array('admin' => 'sp_warnings_suspensions_bans', 'save' => 'sp_warnings_suspensions_bans_save', 'form' => 0, 'id' => 'add-ban')
				);
    SP()->plugin->add_admin_panel(__('Warnings', 'sp-warnings-suspensions'), 'SPF Manage Warnings', __('Options for Warnings and Supensions', 'sp-warnings-suspensions'), 'icon-Warnings', $panels, 8);
}

function sp_warnings_suspensions_admin_menu($parent) {
    if (!SP()->auths->current_user_can('SPF Manage Warnings')) return;
	add_submenu_page($parent, esc_attr(__('Warnings', 'sp-warnings-suspensions')), esc_attr(__('Warnings', 'sp-warnings-suspensions')), 'read', SP_FOLDER_NAME.'/admin/panel-plugins/spa-plugins.php&tab=plugin&admin=sp_warnings_suspensions_admin_options&save=sp_warnings_suspensions_admin_save_options&form=1&panel='.urlencode(__('Warnings', 'sp-warnings-suspensions')), 'dummy');
}

function sp_warnings_suspensions_admin_links($out, $br) {
	if (SP()->auths->current_user_can('SPF Manage Warnings')) {
		$out.= sp_open_grid_cell();
		$out.= '<p><a href="'.admin_url('admin.php?page='.SP_FOLDER_NAME.'/admin/panel-plugins/spa-plugins.php&tab=plugin&admin=sp_warnings_suspensions_admin_options&save=sp_warnings_suspensions_admin_save_options&form=1').'">';
		$out.= SP()->theme->paint_icon('spIcon', SPWARNIMAGES, "sp_ManageWarnings.png").$br;
		$out.= __('Warnings', 'sp-warnings-suspensions').'</a></p>';
		$out.= sp_close_grid_cell();
	}
    return $out;
}

function sp_warnings_suspensions_add_style_icon() {
	echo ('.spaicon-Warnings:before {content: "\e109";}');
}

function sp_warnings_suspensions_admin_options() {
    require_once SPWARNADMINDIR.'sp-warnings-suspensions-admin-options.php';
	sp_warnings_suspensions_admin_options_form();
}

function sp_warnings_suspensions_admin_save_options() {
    require_once SPWARNADMINDIR.'sp-warnings-suspensions-admin-options-save.php';
    return sp_warnings_suspensions_admin_options_save();
}

function sp_warnings_suspensions_warnings() {
    require_once SPWARNADMINDIR.'sp-warnings-suspensions-admin-warnings.php';
	sp_warnings_suspensions_admin_warnings();
}

function sp_warnings_suspensions_warnings_save() {
    require_once SPWARNLIBDIR.'sp-warnings-suspensions-components.php';
    require_once SPWARNADMINDIR.'sp-warnings-suspensions-admin-warnings-save.php';
    return sp_warnings_suspensions_do_warnings_save();
}

function sp_warnings_suspensions_suspensions() {
    require_once SPWARNADMINDIR.'sp-warnings-suspensions-admin-suspensions.php';
	sp_warnings_suspensions_admin_suspensions();
}

function sp_warnings_suspensions_suspensions_save() {
    require_once SPWARNLIBDIR.'sp-warnings-suspensions-components.php';
    require_once SPWARNADMINDIR.'sp-warnings-suspensions-admin-suspensions-save.php';
    return sp_warnings_suspensions_do_suspensions_save();
}

function sp_warnings_suspensions_bans() {
    require_once SPWARNADMINDIR.'sp-warnings-suspensions-admin-bans.php';
	sp_warnings_suspensions_admin_bans();
}

function sp_warnings_suspensions_bans_save() {
    require_once SPWARNLIBDIR.'sp-warnings-suspensions-components.php';
    require_once SPWARNADMINDIR.'sp-warnings-suspensions-admin-bans-save.php';
    return sp_warnings_suspensions_do_bans_save();
}

function sp_warnings_suspensions_localization() {
	sp_plugin_localisation('sp-warnings-suspensions');
}

function sp_warnings_suspensions_install() {
    require_once SPWARNDIR.'sp-warnings-suspensions-install.php';
    sp_warnings_suspensions_do_install();
}

function sp_warnings_suspensions_deactivate() {
    require_once SPWARNDIR.'sp-warnings-suspensions-uninstall.php';
    sp_warnings_suspensions_do_deactivate();
}

function sp_warnings_suspensions_uninstall() {
    require_once SPWARNDIR.'sp-warnings-suspensions-uninstall.php';
    sp_warnings_suspensions_do_uninstall();
}

function sp_warnings_suspensions_sp_activate() {
	require_once SPWARNDIR.'sp-warnings-suspensions-install.php';
    sp_warnings_suspensions_do_sp_activate();
}

function sp_warnings_suspensions_sp_deactivate() {
	require_once SPWARNDIR.'sp-warnings-suspensions-uninstall.php';
    sp_warnings_suspensions_do_sp_deactivate();
}

function sp_warnings_suspensions_sp_uninstall($admins) {
	require_once SPWARNDIR.'sp-warnings-suspensions-uninstall.php';
    sp_warnings_suspensions_do_sp_uninstall($admins);
}

function sp_warnings_suspensions_upgrade_check() {
    require_once SPWARNDIR.'sp-warnings-suspensions-upgrade.php';
    sp_warnings_suspensions_do_upgrade_check();
}

function sp_warnings_suspensions_uninstall_option($actionlink, $plugin) {
    require_once SPWARNDIR.'sp-warnings-suspensions-uninstall.php';
    $actionlink = sp_warnings_suspensions_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_warnings_suspensions_reset_permissions() {
    require_once SPWARNDIR.'sp-warnings-suspensions-install.php';
    sp_warnings_suspensions_do_reset_permissions();
}

function sp_warnings_suspensions_load_admin_js($footer) {
    require_once SPWARNLIBDIR.'sp-warnings-suspensions-components.php';
	sp_warnings_suspensions_do_load_admin_js($footer);
}

function sp_warnings_suspensions_ajax_admin() {
    require_once SPWARNLIBDIR.'sp-warnings-suspensions-components.php';
    require_once SPWARNAJAXDIR.'sp-warnings-suspensions-ajax-admin.php';
}

function sp_warnings_suspensions_admin_caps_new($newadmin, $user) {
	require_once SPWARNLIBDIR.'sp-warnings-suspensions-components.php';
	$newadmin = sp_warnings_suspensions_do_admin_caps_new($newadmin, $user);
	return $newadmin;
}

function sp_warnings_suspensions_admin_caps_update($still_admin, $remove_admin, $user) {
	require_once SPWARNLIBDIR.'sp-warnings-suspensions-components.php';
	$still_admin = sp_warnings_suspensions_do_admin_caps_update($still_admin, $remove_admin, $user);
	return $still_admin;
}

function sp_warnings_suspensions_admin_cap_form($user) {
	require_once SPWARNLIBDIR.'sp-warnings-suspensions-components.php';
	sp_warnings_suspensions_do_admin_cap_form($user);
}

function sp_warnings_suspensions_admin_cap_list($user) {
	require_once SPWARNLIBDIR.'sp-warnings-suspensions-components.php';
	sp_warnings_suspensions_do_admin_cap_list($user);
}

function sp_warnings_suspensions_profile_message($out, $userid, $thisSlug) {
	require_once SPWARNLIBDIR.'sp-warnings-suspensions-components.php';
    $out = sp_warnings_suspensions_do_profile_message($out, $userid, $thisSlug);
	return $out;
}

function sp_warnings_suspensions_admin_help($file, $tag, $lang) {
    if ($tag == '[warnings-suspensions-options]' || $tag == '[warnings-messages]' || $tag == '[suspension-messages]' || $tag == '[ban-messages]' || $tag == '[add-warnings]' ||
        $tag == '[warning-list]' || $tag == '[add-suspensions]' || $tag == '[suspension-list]' || $tag == '[add-bans]' || $tag == '[ban-list]')
        $file = SPWARNADMINDIR.'sp-warnings-suspensions-admin-help.'.$lang;
    return $file;
}

function sp_warnings_suspensions_scheduler() {
    if (!wp_next_scheduled('sph_warnings_cron')) wp_schedule_event(time(), 'daily', 'sph_warnings_cron');
}

function sp_warnings_suspensions_cron_handler() {
	require_once SPWARNLIBDIR.'sp-warnings-suspensions-components.php';
    sp_warnings_suspensions_do_cron_handler();
}

function sp_warnings_suspensions_head() {
    require_once SPWARNLIBDIR.'sp-warnings-suspensions-components.php';
    sp_warnings_suspensions_do_head();
}

function sp_warnings_suspensions_load_js($footer) {
    require_once SPWARNLIBDIR.'sp-warnings-suspensions-components.php';
	sp_warnings_suspensions_do_load_js($footer);
}

function sp_warnings_suspensions_forum_tools($out, $post, $forum, $topic, $page, $postnum, $useremail, $guestemail, $displayname, $br) {
	require_once SPWARNLIBDIR.'sp-warnings-suspensions-components.php';
    $out = sp_warnings_suspensions_do_forum_tools($out, $post, $forum, $topic, $page, $postnum, $useremail, $guestemail, $displayname, $br);
    return $out;
}
