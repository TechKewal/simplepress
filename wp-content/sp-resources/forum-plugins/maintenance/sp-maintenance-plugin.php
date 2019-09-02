<?php
/*
Simple:Press Plugin Title: Maintenance Mode
Version: 2.1.0
Item Id: 3976
Plugin URI: https://simple-press.com/downloads/maintenance-mode-plugin/
Description: A Simple:Press plugin for temporarily putting your forum into maintenance mode and displaying a custom message
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPMAINTENANCEDBVERSION', 1);

define('SPMAINTENANCEDIR', 		SPPLUGINDIR.'maintenance/');
define('SPMAINTENANCEADMINDIR', SPPLUGINDIR.'maintenance/admin/');

add_action('init', 										                'sp_maintenance_localization');
add_action('sph_activate_maintenance/sp-maintenance-plugin.php',        'sp_maintenance_install');
add_action('sph_deactivate_maintenance/sp-maintenance-plugin.php',      'sp_maintenance_deactivate');
add_action('sph_uninstall_maintenance/sp-maintenance-plugin.php',       'sp_maintenance_uninstall');
add_action('sph_activated', 				                            'sp_maintenance_sp_activate');
add_action('sph_deactivated', 				                            'sp_maintenance_sp_deactivate');
add_action('sph_uninstalled', 								            'sp_maintenance_sp_uninstall');
add_action('sph_plugin_update_maintenance/sp-maintenance-plugin.php',   'sp_maintenance_upgrade_check');
add_action('admin_footer',                                              'sp_maintenance_upgrade_check');
add_action('sph_permissions_reset', 						            'sp_maintenance_reset_permissions');
add_action('sph_admin_menu',										    'sp_maintenance_admin_menu');
add_action('sph_admin_panel_header',								    'sp_maintenance_notice');

add_filter('sph_plugins_active_buttons',    'sp_maintenance_uninstall_option', 10, 2);
add_filter('sph_admin_help-admin-toolbox',	'sp_maintenance_admin_help', 10, 3);

$mmoptions = SP()->options->get('maintenance');
if ($mmoptions['mmenable']) add_filter('sph_alternate_forum_content',   'sp_maintenance_display_message');

function sp_maintenance_localization() {
	sp_plugin_localisation('sp-maintenance');
}

function sp_maintenance_install() {
    require_once SPMAINTENANCEDIR.'sp-maintenance-install.php';
    sp_maintenance_do_install();
}

function sp_maintenance_deactivate() {
    require_once SPMAINTENANCEDIR.'sp-maintenance-uninstall.php';
    sp_maintenance_do_deactivate();
}

function sp_maintenance_uninstall() {
    require_once SPMAINTENANCEDIR.'sp-maintenance-uninstall.php';
    sp_maintenance_do_uninstall();
}

function sp_maintenance_sp_activate() {
	require_once SPMAINTENANCEDIR.'sp-maintenance-install.php';
    sp_maintenance_do_sp_activate();
}

function sp_maintenance_sp_deactivate() {
	require_once SPMAINTENANCEDIR.'sp-maintenance-uninstall.php';
    sp_maintenance_do_sp_deactivate();
}

function sp_maintenance_sp_uninstall() {
	require_once SPMAINTENANCEDIR.'sp-maintenance-uninstall.php';
    sp_maintenance_do_sp_uninstall();
}

function sp_maintenance_upgrade_check() {
    require_once SPMAINTENANCEDIR.'sp-maintenance-upgrade.php';
    sp_maintenance_do_upgrade_check();
}

function sp_maintenance_uninstall_option($actionlink, $plugin) {
    require_once SPMAINTENANCEDIR.'sp-maintenance-uninstall.php';
    $actionlink = sp_maintenance_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_maintenance_reset_permissions() {
    require_once SPMAINTENANCEDIR.'sp-maintenance-install.php';
    sp_maintenance_do_reset_permissions();
}

function sp_maintenance_admin_menu() {
    $subpanels = array(__('Maintenance Mode', 'sp-maintenance') => array('admin' => 'sp_maintenance_admin_options', 'save' => 'sp_maintenance_admin_save_options', 'form' => 1, 'id' => 'maintenance'));
	SP()->plugin->add_admin_subpanel('toolbox', $subpanels);
}

function sp_maintenance_notice() {
    $mmoptions = SP()->options->get('maintenance');
    if ($mmoptions['mmenable']) echo spa_message(__('Simple:Press Maintenance Mode is currently enabled.', 'sp-maintenance'), 'notice notice-info');
}

function sp_maintenance_admin_options() {
    require_once SPMAINTENANCEADMINDIR.'sp-maintenance-admin-options.php';
	sp_maintenance_admin_options_form();
}

function sp_maintenance_admin_save_options() {
    require_once SPMAINTENANCEADMINDIR.'sp-maintenance-admin-options-save.php';
    return sp_maintenance_admin_options_save();
}

function sp_maintenance_admin_help($file, $tag, $lang) {
    if ($tag == '[maintenance-options]') $file = SPMAINTENANCEADMINDIR.'sp-maintenance-admin-help.'.$lang;
    return $file;
}

function sp_maintenance_display_message($out) {
    $mmoptions = SP()->options->get('maintenance');

	$out.= '<div id="spMainContainer">';
	$out.= '<div class="spMessage spMaintenanceMode">';
	$out.= '<p>'.SP()->theme->paint_icon('', SPTHEMEICONSURL, 'sp_Information.png').'</p>';
	$out.= SP()->displayFilters->text($mmoptions['mmmessage']);
	$out.= '</div>';
	$out.= '</div>';
	return $out;
}
