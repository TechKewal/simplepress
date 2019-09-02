<?php
/*
Simple:Press Plugin Title: Profile Display Control
Version: 2.1.0
Item Id: 3977
Plugin URI: https://simple-press.com/downloads/profile-display-control-plugin/
Description: A Simple:Press plugin that adds an admin panel for turning off profile elements (edit forms)
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

define('PDCDIR', 		SPPLUGINDIR.'profile-display-control/');
define('PDCADMINDIR', 	SPPLUGINDIR.'profile-display-control/admin/');
define('PDCLIBDIR', 	SPPLUGINDIR.'profile-display-control/library/');

add_action('sph_admin_menu', 															    'sp_profile_display_control_menu');
add_action('init', 																		    'sp_profile_display_control_localization');
add_action('sph_activate_profile-display-control/sp-profile-display-control-plugin.php', 	'sp_profile_display_control_install');
add_action('sph_uninstall_profile-display-control/sp-profile-display-control-plugin.php',	'sp_profile_display_control_uninstall');
add_action('sph_deactivate_profile-display-control/sp-profile-display-control-plugin.php',  'sp_profile_display_control_deactivate');
add_action('sph_ProfileStart', 													            'sp_profile_display_control_profile_start');
add_action('sph_ProfileSaveStart', 													        'sp_profile_display_control_profile_save');

add_filter('sph_admin_help-admin-profiles',		'sp_profile_display_control_help', 10, 3);
add_filter('sph_plugins_active_buttons', 		'sp_profile_display_control_uninstall_option', 10, 2);

function sp_profile_display_control_menu() {
    $subpanels = array(
                __('Display Control', 'sp-pdc') => array('admin' => 'sp_profile_display_control_admin', 'save' => 'sp_profile_display_control_update', 'form' => 1, 'id' => 'sppdc')
                            );
    SP()->plugin->add_admin_subpanel('profiles', $subpanels);
}

function sp_profile_display_control_admin() {
    require_once PDCLIBDIR.'sp-profile-display-control-components.php';
    require_once PDCADMINDIR.'sp-profile-display-control-admin.php';
	sp_profile_display_control_admin_form();
}

function sp_profile_display_control_update() {
    require_once PDCLIBDIR.'sp-profile-display-control-components.php';
    require_once PDCADMINDIR.'sp-profile-display-control-admin-save.php';
	return sp_profile_display_control_admin_save();
}

function sp_profile_display_control_help($file, $tag, $lang) {
    if ($tag == '[profile-display-control]') $file = PDCADMINDIR.'sp-profile-display-control-admin-help.'.$lang;
    return $file;
}

function sp_profile_display_control_localization() {
	sp_plugin_localisation('sp-pdc');
}

function sp_profile_display_control_install() {
    require_once PDCDIR.'sp-profile-display-control-install.php';
    sp_profile_display_control_do_install();
}

function sp_profile_display_control_deactivate() {
    require_once PDCDIR.'sp-profile-display-control-uninstall.php';
    sp_profile_display_control_do_deactivate();
}

function sp_profile_display_control_uninstall() {
    require_once PDCDIR.'sp-profile-display-control-uninstall.php';
    sp_profile_display_control_do_uninstall();
}

function sp_profile_display_control_uninstall_option($actionlink, $plugin) {
    require_once PDCLIBDIR.'sp-profile-display-control-components.php';
    $actionlink = sp_profile_display_control_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_profile_display_control_profile_start() {
    require_once PDCLIBDIR.'sp-profile-display-control-components.php';
    sp_profile_display_control_do_profile_start();
}

function sp_profile_display_control_profile_save() {
    require_once PDCLIBDIR.'sp-profile-display-control-components.php';
    sp_profile_display_control_do_profile_save();
}

function sp_profile_display_control_remove_content($out, $userid=0, $slug='') {
    return '';
}

function sp_profile_display_control_remove_save($flag) {
    return false;
}

# create a function for plugins/themes to add and remove items that can be excluded from profile forms
function sp_profile_display_control_add_item($key, $display, $title, $filter, $save) {
    require_once PDCLIBDIR.'sp-profile-display-control-components.php';
    sp_profile_display_control_do_add_item($key, $display, $title, $filter, $save);
}

function sp_profile_display_control_remove_item($key) {
    require_once PDCLIBDIR.'sp-profile-display-control-components.php';
    sp_profile_display_control_do_remove_item($key);
}
