<?php
/*
Simple:Press Plugin Title: Profanity Filter
Version: 2.1.0
Item Id: 3950
Plugin URI: https://simple-press.com/downloads/profanity-filter/
Description: A Simple:Press plugin for filtering profanity or other key words out of post content
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPPFDBVERSION', 2);

define('PFDIR', 		SPPLUGINDIR.'profanity-filter/');
define('PFADMINDIR', 	SPPLUGINDIR.'profanity-filter/admin/');
define('PFLIBDIR', 		SPPLUGINDIR.'profanity-filter/library/');

add_action('init', 											                      'sp_profanity_filter_localization');
add_action('sph_activate_profanity-filter/sp-profanity-filter-plugin.php', 	      'sp_profanity_filter_install');
add_action('sph_deactivate_profanity-filter/sp-profanity-filter-plugin.php',      'sp_profanity_filter_deactivate');
add_action('sph_uninstall_profanity-filter/sp-profanity-filter-plugin.php',       'sp_profanity_filter_uninstall');
add_action('sph_options_content_right_panel', 								      'sp_profanity_filter_admin_options');
add_action('sph_option_content_save', 										      'sp_profanity_filter_admin_save_options');
add_action('admin_footer',                                                        'sp_profanity_filter_upgrade_check');
add_action('sph_plugin_update_profanity-filter/sp-profanity-filter-plugin.php',   'sp_profanity_filter_upgrade_check');

add_filter('sph_plugins_active_buttons', 		'sp_profanity_filter_uninstall_option', 10, 2);
add_filter('sph_admin_help-admin-options', 		'sp_profanity_filter_admin_help', 10, 3);
add_filter('sph_display_post_content_filter', 	'sp_profanity_filter_filter');
add_filter('sph_display_title_filter',          'sp_profanity_filter_filter');
add_filter('sph_display_tooltip_filter',		'sp_profanity_filter_filter');

function sp_profanity_filter_admin_options() {
    require_once PFADMINDIR.'sp-profanity-filter-admin-options.php';
	sp_profanity_filter_admin_options_form();
}

function sp_profanity_filter_admin_save_options() {
    require_once PFADMINDIR.'sp-profanity-filter-admin-options-save.php';
    return sp_profanity_filter_admin_options_save();
}

function sp_profanity_filter_admin_help($file, $tag, $lang) {
    if ($tag == '[profanity-filter]') $file = PFADMINDIR.'sp-profanity-filter-admin-help.'.$lang;
    return $file;
}

function sp_profanity_filter_uninstall_option($actionlink, $plugin) {
    require_once PFLIBDIR.'sp-profanity-filter-components.php';
    $actionlink = sp_profanity_filter_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_profanity_filter_localization() {
	sp_plugin_localisation('sp-profanity');
}

function sp_profanity_filter_uninstall() {
    require_once PFDIR.'sp-profanity-filter-uninstall.php';
    sp_profanity_filter_do_uninstall();
}

function sp_profanity_filter_deactivate() {
    require_once PFDIR.'sp-profanity-filter-uninstall.php';
    sp_profanity_filter_do_deactivate();
}

function sp_profanity_filter_install() {
    require_once PFDIR.'sp-profanity-filter-install.php';
    sp_profanity_filter_do_install();
}

function sp_profanity_filter_filter($content) {
    require_once PFLIBDIR.'sp-profanity-filter-components.php';
    $content = sp_profanity_filter_do_filter($content);
	return $content;
}

function sp_profanity_filter_upgrade_check() {
    require_once PFDIR.'sp-profanity-filter-upgrade.php';
    sp_profanity_filter_do_upgrade_check();
}
