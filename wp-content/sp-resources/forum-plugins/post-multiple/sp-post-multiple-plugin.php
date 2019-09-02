<?php
/*
Simple:Press Plugin Title: Post Multiple Forums
Version: 2.1.0
Item Id: 3969
Plugin URI: https://simple-press.com/downloads/post-in-multiple-forums-plugin/
Description: A Simple:Press plugin for allowing a new topic to be posted in multiple, selectable forums
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPMULTIDBVERSION', 1);

define('SPMULTIDIR', 		SPPLUGINDIR.'post-multiple/');
define('SPMULTIADMINDIR',   SPPLUGINDIR.'post-multiple/admin/');
define('SPMULTIAJAXDIR', 	SPPLUGINDIR.'post-multiple/ajax/');
define('SPMULTILIBDIR', 	SPPLUGINDIR.'post-multiple/library/');
define('SPMULTILIBURL', 	SPPLUGINURL.'post-multiple/library/');
define('SPMULTISCRIPT', 	SPPLUGINURL.'post-multiple/resources/jscript/');
define('SPMULTIIMAGES', 	SPPLUGINURL.'post-multiple/resources/images/');
define('SPMULTIIMAGESMOB', 	SPPLUGINURL.'post-multiple/resources/images/mobile/');

add_action('init', 										                    'sp_post_multiple_localization');
add_action('sph_activate_post-multiple/sp-post-multiple-plugin.php',        'sp_post_multiple_install');
add_action('sph_deactivate_post-multiple/sp-post-multiple-plugin.php',      'sp_post_multiple_deactivate');
add_action('sph_uninstall_post-multiple/sp-post-multiple-plugin.php',       'sp_post_multiple_uninstall');
add_action('sph_activated', 				                                'sp_post_multiple_sp_activate');
add_action('sph_deactivated', 				                                'sp_post_multiple_sp_deactivate');
add_action('sph_uninstalled', 								                'sp_post_multiple_sp_uninstall');
add_action('sph_plugin_update_post-multiple/sp-post-multiple-plugin.php',   'sp_post_multiple_upgrade_check');
add_action('admin_footer',                                                  'sp_post_multiple_upgrade_check');
add_action('sph_permissions_reset', 						                'sp_post_multiple_reset_permissions');
add_action('sph_post_create', 							                    'sp_post_multiple_save_post');
add_action('sph_admin_menu', 	                                            'sp_post_multiple_menu');

add_filter('sph_plugins_active_buttons',        'sp_post_multiple_uninstall_option', 10, 2);
add_filter('sph_perms_tooltips', 				'sp_post_multiple_tooltips', 10, 2);
add_filter('sph_admin_help-admin-components',   'sp_post_multiple_admin_help', 10, 3);

if (SP()->core->forumData['display']['editor']['toolbar']) {
	add_filter('sph_topic_editor_toolbar_buttons',	'sp_post_multiple_button', 10, 4);
	add_filter('sph_topic_editor_toolbar',			'sp_post_multiple_container', 10, 4);
} else {
	add_filter('sph_topic_editor_footer_top',		'sp_post_multiple_container', 10, 2);
}

function sp_post_multiple_localization() {
	sp_plugin_localisation('sp-post-multiple');
}

function sp_post_multiple_install() {
    require_once SPMULTIDIR.'sp-post-multiple-install.php';
    sp_post_multiple_do_install();
}

function sp_post_multiple_deactivate() {
    require_once SPMULTIDIR.'sp-post-multiple-uninstall.php';
    sp_post_multiple_do_deactivate();
}

function sp_post_multiple_uninstall() {
    require_once SPMULTIDIR.'sp-post-multiple-uninstall.php';
    sp_post_multiple_do_uninstall();
}

function sp_post_multiple_sp_activate() {
	require_once SPMULTIDIR.'sp-post-multiple-install.php';
    sp_post_multiple_do_sp_activate();
}

function sp_post_multiple_sp_deactivate() {
	require_once SPMULTIDIR.'sp-post-multiple-uninstall.php';
    sp_post_multiple_do_sp_deactivate();
}

function sp_post_multiple_sp_uninstall() {
	require_once SPMULTIDIR.'sp-post-multiple-uninstall.php';
    sp_post_multiple_do_sp_uninstall();
}

function sp_post_multiple_upgrade_check() {
    require_once SPMULTIDIR.'sp-post-multiple-upgrade.php';
    sp_post_multiple_do_upgrade_check();
}

function sp_post_multiple_uninstall_option($actionlink, $plugin) {
    require_once SPMULTIDIR.'sp-post-multiple-uninstall.php';
    $actionlink = sp_post_multiple_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_post_multiple_reset_permissions() {
    require_once SPMULTIDIR.'sp-post-multiple-install.php';
    sp_post_multiple_do_reset_permissions();
}

function sp_post_multiple_tooltips($tips, $t) {
    $tips['post_multiple'] = $t.__('Can create a new topic and have it posted in multiple forums at once', 'sp-post-multiple');
    return $tips;
}

function sp_post_multiple_menu() {
    $subpanels = array(__('Post Multiple', 'sp-post-multiple') => array('admin' => 'sp_post_multiple_admin_options', 'save' => 'sp_post_multiple_admin_save_options', 'form' => 1, 'id' => 'multiopt'));
    SP()->plugin->add_admin_subpanel('components', $subpanels);
}

function sp_post_multiple_admin_options() {
    require_once SPMULTIADMINDIR.'sp-post-multiple-admin-options.php';
	sp_post_multiple_admin_options_form();
}

function sp_post_multiple_admin_save_options() {
    require_once SPMULTIADMINDIR.'sp-post-multiple-admin-options-save.php';
    return sp_post_multiple_admin_options_save();
}

function sp_post_multiple_admin_help($file, $tag, $lang) {
    if ($tag == '[post-multiple-options]') $file = SPMULTIADMINDIR.'sp-post-multiple-admin-help.'.$lang;
    return $file;
}

function sp_post_multiple_button($out, $data, $a) {
	require_once SPMULTILIBDIR.'sp-post-multiple-components.php';
	$out = sp_post_multiple_do_button($out, $data, $a);
	return $out;
}

function sp_post_multiple_container($out, $data) {
	require_once SPMULTILIBDIR.'sp-post-multiple-components.php';
	$out = sp_post_multiple_do_container($out, $data);
	return $out;
}

function sp_post_multiple_save_post($newpost) {
    require_once SPMULTILIBDIR.'sp-post-multiple-components.php';
    sp_post_multiple_do_save_post($newpost);
}
