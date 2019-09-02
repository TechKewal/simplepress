<?php
/*
Simple:Press Plugin Title: Custom Profile Fields
Version: 2.1.0
Item Id: 3926
Plugin URI: https://simple-press.com/downloads/custom-profile-fields-plugin/
Description: A Simple:Press plugin for creating custom profile fields
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-21 08:48:39 -0500 (Tue, 21 Aug 2018) $
$Rev: 15716 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

define('CPFDIR', 		SPPLUGINDIR.'custom-profile-fields/');
define('CPFADMINDIR', 	SPPLUGINDIR.'custom-profile-fields/admin/');
define('CPFAJAXDIR', 	SPPLUGINDIR.'custom-profile-fields/ajax/');
define('CPFLIBDIR', 	SPPLUGINDIR.'custom-profile-fields/library/');
define('CPFTAGSDIR', 	SPPLUGINDIR.'custom-profile-fields/template-tags/');
define('CPFSCRIPT', 	SPPLUGINURL.'custom-profile-fields/resources/jscript/');

add_action('sph_admin_menu', 															'sp_custom_profile_fields_menu');
add_action('init', 																		'sp_custom_profile_fields_localization');
add_action('sph_activate_custom-profile-fields/sp-custom-profile-fields-plugin.php', 	'sp_custom_profile_fields_install');
add_action('sph_uninstall_custom-profile-fields/sp-custom-profile-fields-plugin.php',	'sp_custom_profile_fields_uninstall');
add_action('sph_deactivate_custom-profile-fields/sp-custom-profile-fields-plugin.php',	'sp_custom_profile_fields_deactivate');
add_action('sph_uninstalled', 								                            'sp_custom_profile_fields_sp_uninstall');
add_action('sph_UpdateProfile', 														'sp_custom_profile_fields_submit', 10, 2);
add_action('sph_buddypress_forum_profile_end',                                          'sp_custom_profile_fields_bp_profile');

add_filter('sph_admin_help-admin-profiles',		    'sp_custom_profile_fields_help', 10, 3);
add_filter('sph_plugins_active_buttons', 		    'sp_custom_profile_fields_uninstall_option', 10, 2);
add_filter('sph_ProfileFormBottom', 			    'sp_custom_profile_fields_show', 10, 3);
add_filter('sph_user_class_meta', 			        'sp_custom_profile_load_meta');
add_filter('sph_buddypress_updated_forum_profile',  'sp_custom_profile_fields_bp_profile_save', 10, 2);

# Personal Data Export
add_filter('sp_privacy_profile_data', 						'sp_privacy_custom_profile', 10, 4);

# Ajax Handler
add_action('wp_ajax_cpf',				'sp_custom_profile_fields_ajax');
add_action('wp_ajax_nopriv_cpf',		'sp_custom_profile_fields_ajax');

function sp_custom_profile_fields_menu() {
    $subpanels = array(
                __('Profile Custom Fields', 'cpf') => array('admin' => 'sp_custom_profile_fields_admin', 'save' => 'sp_custom_profile_fields_update', 'form' => 1, 'id' => 'spcpf')
                            );
    SP()->plugin->add_admin_subpanel('profiles', $subpanels);
}

function sp_custom_profile_fields_admin() {
    require_once CPFLIBDIR.'sp-custom-profile-fields-components.php';
    require_once CPFADMINDIR.'sp-custom-profile-fields-admin.php';
	sp_custom_profile_fields_admin_form();
}

function sp_custom_profile_fields_update() {
    require_once CPFLIBDIR.'sp-custom-profile-fields-components.php';
    require_once CPFADMINDIR.'sp-custom-profile-fields-admin-save.php';
	return sp_custom_profile_fields_admin_save();
}

function sp_custom_profile_fields_help($file, $tag, $lang) {
    if ($tag == '[custom-fields]') $file = CPFADMINDIR.'sp-custom-profile-fields-admin-help.'.$lang;
    return $file;
}

function sp_custom_profile_fields_localization() {
	sp_plugin_localisation('cpf');
}

function sp_custom_profile_fields_ajax() {
    require_once CPFLIBDIR.'sp-custom-profile-fields-components.php';
    require_once CPFAJAXDIR.'sp-custom-profile-fields-ajax.php';
}

function sp_custom_profile_fields_install() {
    require_once CPFDIR.'sp-custom-profile-fields-install.php';
    sp_custom_profile_fields_do_install();
}

function sp_custom_profile_fields_uninstall() {
    require_once CPFDIR.'sp-custom-profile-fields-uninstall.php';
    sp_custom_profile_fields_do_uninstall();
}

function sp_custom_profile_fields_deactivate() {
    require_once CPFDIR.'sp-custom-profile-fields-uninstall.php';
    sp_custom_profile_fields_do_deactivate();
}

function sp_custom_profile_fields_uninstall_option($actionlink, $plugin) {
    require_once CPFLIBDIR.'sp-custom-profile-fields-components.php';
    $actionlink = sp_custom_profile_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_custom_profile_fields_sp_uninstall() {
	require_once CPFDIR.'sp-custom-profile-fields-uninstall.php';
    sp_custom_profile_fields_do_sp_uninstall();
}


function sp_custom_profile_fields_show($out, $userid, $thisForm) {
    require_once CPFLIBDIR.'sp-custom-profile-fields-components.php';
	return sp_custom_profile_fields_output($out, $userid, $thisForm);
}

function sp_custom_profile_fields_submit($userid, $thisForm) {
    require_once CPFLIBDIR.'sp-custom-profile-fields-components.php';
	sp_custom_profile_fields_save($userid, $thisForm);
}

function sp_custom_profile_load_meta($list) {
    require_once CPFLIBDIR.'sp-custom-profile-fields-components.php';
	$list = sp_custom_profile_do_load_meta($list);
    return $list;
}

function sp_custom_profile_fields_bp_profile() {
    require_once CPFLIBDIR.'sp-custom-profile-fields-components.php';
	sp_custom_profile_fields_do_bp_profile();
}

function sp_custom_profile_fields_bp_profile_save($errors, $userid) {
    require_once CPFLIBDIR.'sp-custom-profile-fields-components.php';
	$errors = sp_custom_profile_fields_do_bp_profile_save($errors, $userid);
    return $errors;
}

# personal data export
function sp_privacy_custom_profile($exportItems, $spUserData, $groupID, $groupLabel) {
    require_once CPFLIBDIR.'sp-custom-profile-fields-components.php';
	return sp_privacy_do_custom_profile($exportItems, $spUserData, $groupID, $groupLabel);
}

# Define Template Tags globally available (dont have to be enabled on template tag panel)

function sp_CustomProfileFieldsDisplay($name, $userid=0) {
	require_once CPFLIBDIR.'sp-custom-profile-fields-components.php';
    require_once CPFTAGSDIR.'sp-custom-profile-fields-display-tag.php';
    sp_do_CustomProfileFieldsDisplay($name, $userid);
}

function sp_CustomProfileFieldsDisplayExtended($args, $name, $userid=0, $label='') {
	require_once CPFLIBDIR.'sp-custom-profile-fields-components.php';
    require_once CPFTAGSDIR.'sp-custom-profile-fields-display-tag.php';
    sp_do_CustomProfileFieldsDisplayExtended($args, $name, $userid, $label);
}

function sp_CustomProfileFieldsProfileDisplay($name, $userid=0, $label='') {
	require_once CPFLIBDIR.'sp-custom-profile-fields-components.php';
    require_once CPFTAGSDIR.'sp-custom-profile-fields-display-tag.php';
    sp_do_CustomProfileFieldsProfileDisplay($name, $userid, $label);
}

