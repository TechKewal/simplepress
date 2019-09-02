<?php
/*
Simple:Press Plugin Title: Event Logger
Version: 2.1.0
Item Id: 3975
Plugin URI: https://simple-press.com/downloads/event-logger-plugin/
Description: A Simple:Press plugin for logging forum events and hooks to the database
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-11-13 20:42:11 -0600 (Tue, 13 Nov 2018) $
$Rev: 15818 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPLOGGERDBVERSION', 3);

define('SPLOGGERDIR', 		SPPLUGINDIR.'logger/');
define('SPLOGGERADMINDIR',  SPPLUGINDIR.'logger/admin/');
define('SPLOGGERAJAXDIR', 	SPPLUGINDIR.'logger/ajax/');
define('SPLOGGERLIBDIR', 	SPPLUGINDIR.'logger/library/');
define('SPLOGGERLIBURL', 	SPPLUGINURL.'logger/library/');
define('SPLOGGERIMAGES', 	SPPLUGINURL.'logger/resources/images/');
define('SPLOGGERSCRIPT', 	SPPLUGINURL.'logger/resources/jscript/');

define('SPEVENTLOG', 		SP_PREFIX.'sfeventlog');

add_action('init', 										       'sp_logger_localization');
add_action('sph_activate_logger/sp-logger-plugin.php',         'sp_logger_install');
add_action('sph_deactivate_logger/sp-logger-plugin.php',       'sp_logger_deactivate');
add_action('sph_uninstall_logger/sp-logger-plugin.php',        'sp_logger_uninstall');
add_action('sph_activated', 				                   'sp_logger_sp_activate');
add_action('sph_deactivated', 				                   'sp_logger_sp_deactivate');
add_action('sph_uninstalled', 								   'sp_logger_sp_uninstall');
add_action('sph_plugin_update_logger/sp-logger-plugin.php',    'sp_logger_upgrade_check');
add_action('admin_footer',                                     'sp_logger_upgrade_check');
add_action('sph_permissions_reset', 						   'sp_logger_reset_permissions');
add_action('sph_admin_menu', 								   'sp_logger_menu');
add_action('sph_add_style',									   'sp_logger_add_style_icon');
add_action('sph_admin_menu',                                   'sp_logger_admin_menu');
add_action('sph_admin_caps_form', 					     	   'sp_logger_admin_cap_form', 10, 2);
add_action('sph_admin_caps_list', 						       'sp_logger_admin_cap_list', 10, 2);
add_action('sph_scripts_admin_end', 					       'sp_logger_load_admin_js');

add_filter('sph_plugins_active_buttons',    'sp_logger_uninstall_option', 10, 2);
add_filter('sph_admin_help-admin-plugins', 	'sp_logger_admin_help', 10, 3);
add_filter('sph_admin_caps_new', 			'sp_logger_admin_caps_new', 10, 2);
add_filter('sph_admin_caps_update',         'sp_logger_admin_caps_update', 10, 3);
add_filter('sph_ShowAdminLinks', 		    'sp_logger_admin_links', 10, 2);

# Ajax Handler
add_action('wp_ajax_logger-manage',			'sp_logger_ajax_manage');
add_action('wp_ajax_nopriv_logger-manage',	'sp_logger_ajax_manage');


# NOTE - runs at load...  set up our hooks
require_once SPLOGGERLIBDIR.'sp-logger-components.php';
sp_logger_set_up_logging();

function sp_logger_admin_menu($parent) {
    if (!SP()->auths->current_user_can('SPF Manage Logger')) return;
	add_submenu_page($parent, esc_attr(__('Event Logger', 'sp-logger')), esc_attr(__('Event Logger', 'sp-logger')), 'read', SP_FOLDER_NAME.'/admin/panel-plugins/spa-plugins.php&tab=plugin&admin=sp_logger_admin_options&save=sp_logger_admin_options_save&form=1&panel='.urlencode(__('Event Logger', 'sp-logger')), 'dummy');
}

function sp_logger_menu() {
	$panels = array(
                __('Options', 'sp-logger') => array('admin' => 'sp_logger_admin_options', 'save' => 'sp_logger_admin_options_save', 'form' => 1, 'id' => 'loggeroptions'),
                __('View Log', 'sp-logger') => array('admin' => 'sp_logger_admin_view', 'save' => '', 'form' => 0, 'id' => 'logview')
				);
    SP()->plugin->add_admin_panel(__('Event Logger', 'sp-logger'), 'SPF Manage Logger', __('Event Logging', 'sp-logger'), 'icon-Log', $panels, 7);
}

function sp_logger_add_style_icon() {
	echo('.spaicon-Event:before {content: "\e111";}');
}

function sp_logger_admin_links($out, $br) {
	if (SP()->auths->current_user_can('SPF Manage Logger')) {
		$out.= sp_open_grid_cell();
		$out.= '<p><a href="'.admin_url('admin.php?page='.SP_FOLDER_NAME.'/admin/panel-plugins/spa-plugins.php&tab=plugin&admin=sp_logger_admin_options&save=sp_logger_admin_options_save&form=1').'">';
		$out.= SP()->theme->paint_icon('spIcon', SPLOGGERIMAGES, "sp_ManageLogs.png").$br;
		$out.= __('Event Logger', 'sp-logger').'</a></p>';
		$out.= sp_close_grid_cell();
	}
    return $out;
}

function sp_logger_admin_options() {
    require_once SPLOGGERADMINDIR.'sp-logger-admin-options.php';
	sp_logger_admin_options_form();
}

function sp_logger_admin_options_save() {
    require_once SPLOGGERADMINDIR.'sp-logger-admin-options-save.php';
    return sp_logger_admin_options_save_form();
}

function sp_logger_admin_view() {
    require_once SPLOGGERADMINDIR.'sp-logger-admin-view.php';
	sp_logger_admin_view_form();
}

function sp_logger_admin_help($file, $tag, $lang) {
    if ($tag == '[logger-options]' || $tag == '[logger-events]' || $tag == '[logger-hook]' || $tag == '[logger-actions]') $file = SPLOGGERADMINDIR.'sp-logger-admin-help.'.$lang;
    return $file;
}

function sp_logger_localization() {
	sp_plugin_localisation('sp-logger');
}

function sp_logger_install() {
    require_once SPLOGGERDIR.'sp-logger-install.php';
    sp_logger_do_install();
}

function sp_logger_deactivate() {
    require_once SPLOGGERDIR.'sp-logger-uninstall.php';
    sp_logger_do_deactivate();
}

function sp_logger_uninstall() {
    require_once SPLOGGERDIR.'sp-logger-uninstall.php';
    sp_logger_do_uninstall();
}

function sp_logger_sp_activate() {
	require_once SPLOGGERDIR.'sp-logger-install.php';
    sp_logger_do_sp_activate();
}

function sp_logger_sp_deactivate() {
	require_once SPLOGGERDIR.'sp-logger-uninstall.php';
    sp_logger_do_sp_deactivate();
}

function sp_logger_sp_uninstall() {
	require_once SPLOGGERDIR.'sp-logger-uninstall.php';
    sp_logger_do_sp_uninstall();
}

function sp_logger_upgrade_check() {
    require_once SPLOGGERDIR.'sp-logger-upgrade.php';
    sp_logger_do_upgrade_check();
}

function sp_logger_uninstall_option($actionlink, $plugin) {
    require_once SPLOGGERDIR.'sp-logger-uninstall.php';
    $actionlink = sp_logger_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_logger_reset_permissions() {
    require_once SPLOGGERDIR.'sp-logger-install.php';
    sp_logger_do_reset_permissions();
}

function sp_logger_ajax_manage() {
    require_once SPLOGGERAJAXDIR.'sp-logger-ajax-manage.php';
}

function sp_logger_admin_cap_form($user) {
	require_once SPLOGGERLIBDIR.'sp-logger-components.php';
	sp_logger_do_admin_cap_form($user);
}

function sp_logger_admin_cap_list($user) {
	require_once SPLOGGERLIBDIR.'sp-logger-components.php';
	sp_logger_do_admin_cap_list($user);
}

function sp_logger_admin_caps_new($newadmin, $user) {
	require_once SPLOGGERLIBDIR.'sp-logger-components.php';
	$newadmin = sp_logger_do_admin_caps_new($newadmin, $user);
	return $newadmin;
}

function sp_logger_admin_caps_update($still_admin, $remove_admin, $user) {
	require_once SPLOGGERLIBDIR.'sp-logger-components.php';
	$still_admin = sp_logger_do_admin_caps_update($still_admin, $remove_admin, $user);
	return $still_admin;
}

function sp_logger_load_admin_js() {
	wp_enqueue_script('splogger', SPLOGGERSCRIPT.'sp-logger-admin.min.js', array('jquery'), false, false);
}

# global functon for others to use
function sp_logger_write_log($event, $data) {
   	SP()->DB->execute("INSERT INTO ".SPEVENTLOG." (log_event, log_date, log_data) VALUES ('$event', '".current_time('mysql')."', '".wp_slash(maybe_serialize($data))."')");

	$logger = SP()->options->get('logger');
   	if (SP()->rewrites->pageData['insertid'] > ($logger['logentries'])) {
		$sql = 'DELETE FROM '.SPEVENTLOG.' WHERE log_id <= '.(SP()->rewrites->pageData['insertid'] - $logger['logentries']);
		SP()->DB->execute($sql);
	}
}
