<?php
/*
Simple:Press Plugin Title: Birthdays
Version: 2.1.0
Item Id: 3964
Plugin URI: https://simple-press.com/downloads/birthdays-plugin/
Description: A Simple:Press plugin for allowing users to enter their birthday then display users birthdays for week/day
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-21 08:48:39 -0500 (Tue, 21 Aug 2018) $
$Rev: 15716 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPBDAYDBVERSION', 1);

define('SPBDAYDIR', 		SPPLUGINDIR.'birthdays/');
define('SPBDAYADMINDIR',    SPPLUGINDIR.'birthdays/admin/');
define('SPBDAYAJAXDIR', 	SPPLUGINDIR.'birthdays/ajax/');
define('SPBDAYLIBDIR', 	    SPPLUGINDIR.'birthdays/library/');
define('SPBDAYLIBURL', 	    SPPLUGINURL.'birthdays/library/');
define('SPBDAYCSS', 		SPPLUGINURL.'birthdays/resources/css/');
define('SPBDAYSCRIPT', 	    SPPLUGINURL.'birthdays/resources/jscript/');
define('SPBDAYIMAGES', 	    SPPLUGINURL.'birthdays/resources/images/');
define('SPBDAYTAGS', 	    SPPLUGINDIR.'birthdays/template-tags/');
define('SPBDAYTEMP', 	    SPPLUGINDIR.'birthdays/template-files/');

add_action('init', 										                'sp_birthdays_localization');
add_action('sph_activate_birthdays/sp-birthdays-plugin.php',            'sp_birthdays_install');
add_action('sph_uninstall_birthdays/sp-birthdays-plugin.php',           'sp_birthdays_uninstall');
add_action('sph_deactivate_birthdays/sp-birthdays-plugin.php',          'sp_birthdays_deactivate');
add_action('sph_activated', 				                            'sp_birthdays_sp_activate');
add_action('sph_deactivated', 				                            'sp_birthdays_sp_deactivate');
add_action('sph_uninstalled', 								            'sp_birthdays_sp_uninstall');
add_action('sph_plugin_update_birthdays/sp-birthdays-plugin.php',       'sp_birthdays_upgrade_check');
add_action('sph_UpdateProfileProfile', 									'sp_birthdays_profile_save', 10, 2);
add_action('sph_birthdays_cron', 				                        'sp_birthdays_fill_cache');
add_action('sph_options_display_right_panel', 							'sp_birthdays_admin_options');
add_action('sph_option_display_save',                                   'sp_birthdays_admin_save_options');
add_action('sph_print_plugin_styles',							        'sp_birthdays_header');
add_action('sph_print_plugin_scripts', 								    'sp_birthdays_load_js');
add_action('sph_stats_scheduler',                                       'sp_birthdays_scheduler');

add_filter('sph_plugins_active_buttons',    'sp_birthdays_uninstall_option', 10, 2);
add_filter('sph_user_class_meta', 			'sp_birthdays_load_meta');
add_filter('sph_ProfileProfileFormBottom',  'sp_birthdays_profile_form', 10, 2);
add_filter('sph_admin_help-admin-options',	'sp_birthdays_help', 10, 3);

# Personal Data Export
add_filter('sp_privacy_profile_data', 				'sp_privacy_birthday_profile', 10, 4);

# Mycred Support
add_action('mycred_pre_init',				'sp_birthdays_load_mycred', 2);
add_filter('add_sp_mycred_extension',		'sp_birthdays_extend_mycred');
add_action('prefs_sp_mycred_extension', 	'sp_birthdays_prefs_create');
add_action('sph_todays_birthday_list',		'sp_birthdays_save_mycred', 1);

function sp_birthdays_admin_options() {
    require_once SPBDAYADMINDIR.'sp-birthdays-admin.php';
	sp_birthdays_admin_options_form();
}

function sp_birthdays_admin_save_options() {
    require_once SPBDAYADMINDIR.'sp-birthdays-admin-save.php';
    sp_birthdays_admin_options_save();
}

function sp_birthdays_localization() {
	sp_plugin_localisation('sp-birthdays');
}

function sp_birthdays_help($file, $tag, $lang) {
    if ($tag == '[birthdays]') $file = SPBDAYADMINDIR.'sp-birthdays-admin-help.'.$lang;
    return $file;
}

function sp_birthdays_install() {
    require_once SPBDAYDIR.'sp-birthdays-install.php';
    sp_birthdays_do_install();
}

function sp_birthdays_uninstall() {
    require_once SPBDAYDIR.'sp-birthdays-uninstall.php';
    sp_birthdays_do_uninstall();
}

function sp_birthdays_deactivate() {
    require_once SPBDAYDIR.'sp-birthdays-uninstall.php';
    sp_birthdays_do_deactivate();
}

function sp_birthdays_sp_activate() {
	require_once SPBDAYDIR.'sp-birthdays-install.php';
    sp_birthdays_do_sp_activate();
}

function sp_birthdays_sp_deactivate() {
	require_once SPBDAYDIR.'sp-birthdays-uninstall.php';
    sp_birthdays_do_sp_deactivate();
}

function sp_birthdays_sp_uninstall() {
	require_once SPBDAYDIR.'sp-birthdays-uninstall.php';
    sp_birthdays_do_sp_uninstall();
}

function sp_birthdays_uninstall_option($actionlink, $plugin) {
    require_once SPBDAYDIR.'sp-birthdays-uninstall.php';
    $actionlink = sp_birthdays_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_birthdays_fill_cache() {
    require_once SPBDAYLIBDIR.'sp-birthdays-components.php';
    sp_birthdays_do_fill_cache();
}

function sp_birthdays_load_meta($list) {
    require_once SPBDAYLIBDIR.'sp-birthdays-components.php';
	$list = sp_birthdays_do_load_meta($list);
    return $list;
}

function sp_birthdays_profile_form($out, $userid) {
	require_once SPBDAYLIBDIR.'sp-birthdays-components.php';
    $out = sp_birthdays_do_profile_form($out, $userid);
    return $out;
}

function sp_birthdays_profile_save($message, $thisUser) {
    require_once SPBDAYLIBDIR.'sp-birthdays-components.php';
    $message = sp_birthdays_do_profile_save($message, $thisUser);
    return $message;
}

function sp_birthdays_header() {
    require_once SPBDAYLIBDIR.'sp-birthdays-components.php';
    sp_birthdays_do_header();
}

function sp_birthdays_load_js($footer) {
    require_once SPBDAYLIBDIR.'sp-birthdays-components.php';
	sp_birthdays_do_load_js($footer);
}

function sp_birthdays_scheduler() {
    if (!wp_next_scheduled('sph_birthdays_cron')) {
        wp_schedule_event(time(), 'hourly', 'sph_birthdays_cron');
    }
}

function sp_birthdays_upgrade_check() {
    require_once SPBDAYDIR.'sp-birthdays-upgrade.php';
    sp_birthdays_do_upgrade_check();
}

# personal data export
function sp_privacy_birthday_profile($exportItems, $spUserData, $groupID, $groupLabel) {
    require_once SPBDAYLIBDIR.'sp-birthdays-components.php';
	return sp_privacy_do_birthday_profile($exportItems, $spUserData, $groupID, $groupLabel);
}

# MyCred Support
function sp_birthdays_load_mycred() {
    require_once SPBDAYLIBDIR.'sp-birthdays-mycred.php';
}

function sp_birthdays_extend_mycred($defs) {
    return sp_birthdays_do_extend_mycred($defs);
}

function sp_birthdays_prefs_create($args) {
	sp_birthdays_do_prefs_create($args);
}

function sp_birthdays_save_mycred($birthdays) {
    require_once SPBDAYLIBDIR.'sp-birthdays-mycred.php';
    if($birthdays) {
    	foreach($birthdays as $b) {
    		if($b['days'] == 0) {
				sp_birthdays_do_save_mycred($b['user_id']);
			}
		}
	}
}

# template function for display
function sp_ListBirthdays($args='', $headerLabel='', $todayLabel='', $upcomingLabel='') {
	require_once SPBDAYLIBDIR.'sp-birthdays-components.php';
    require_once SPBDAYTAGS.'sp-birthdays-display-tag.php';
    sp_do_ListBirthdays($args, $headerLabel, $todayLabel, $upcomingLabel);
}
