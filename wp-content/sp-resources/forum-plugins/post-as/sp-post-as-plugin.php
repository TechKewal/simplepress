<?php
/*
Simple:Press Plugin Title: Post As
Version: 2.1.0
Item Id: 3949
Plugin URI: https://simple-press.com/downloads/post-as-plugin/
Description: A Simple:Press plugin for allowing users to posts as another user
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPPOSTASDBVERSION', 0);

define('SPPOSTASDIR', 		SPPLUGINDIR.'post-as/');
define('SPPOSTASAJAXDIR', 	SPPLUGINDIR.'post-as/ajax/');
define('SPPOSTASLIBDIR', 	SPPLUGINDIR.'post-as/library/');
define('SPPOSTASCSS',		SPPLUGINURL.'post-as/resources/css/');
define('SPPOSTASSCRIPT',	SPPLUGINURL.'post-as/resources/jscript/');

add_action('init', 										         'sp_post_as_localization');
add_action('sph_activate_post-as/sp-post-as-plugin.php',         'sp_post_as_install');
add_action('sph_deactivate_post-as/sp-post-as-plugin.php',       'sp_post_as_deactivate');
add_action('sph_uninstall_post-as/sp-post-as-plugin.php',        'sp_post_as_uninstall');
add_action('sph_activated', 				                     'sp_post_as_sp_activate');
add_action('sph_deactivated', 				                     'sp_post_as_sp_deactivate');
add_action('sph_uninstalled', 								     'sp_post_as_sp_uninstall');
add_action('sph_plugin_update_post-as/sp-post-as-plugin.php',    'sp_post_as_upgrade_check');
add_action('admin_footer',                                       'sp_post_as_upgrade_check');
add_action('sph_permissions_reset', 						     'sp_post_as_reset_permissions');
add_action('sph_new_forum_post', 							     'sp_post_as_save_post', 99);
add_action('sph_print_plugin_scripts', 							 'sp_post_as_load_js');
add_action('sph_print_plugin_styles', 						     'sp_post_as_header');

add_filter('sph_plugins_active_buttons',    'sp_post_as_uninstall_option', 10, 2);
add_filter('sph_topic_options_add', 		'sp_post_as_form_options', 99, 2);
add_filter('sph_post_options_add', 			'sp_post_as_form_options', 99, 2);
add_filter('sph_perms_tooltips', 			'sp_post_as_tooltips', 10, 2);

# Ajax Handler
add_action('wp_ajax_post-as-manage',		'sp_post_as_ajax_manage');
add_action('wp_ajax_nopriv_post-as-manage',	'sp_post_as_ajax_manage');

function sp_post_as_localization() {
	sp_plugin_localisation('sp-post-as');
}

function sp_post_as_install() {
    require_once SPPOSTASDIR.'sp-post-as-install.php';
    sp_post_as_do_install();
}

function sp_post_as_deactivate() {
    require_once SPPOSTASDIR.'sp-post-as-uninstall.php';
    sp_post_as_do_deactivate();
}

function sp_post_as_uninstall() {
    require_once SPPOSTASDIR.'sp-post-as-uninstall.php';
    sp_post_as_do_uninstall();
}

function sp_post_as_sp_activate() {
	require_once SPPOSTASDIR.'sp-post-as-install.php';
    sp_post_as_do_sp_activate();
}

function sp_post_as_sp_deactivate() {
	require_once SPPOSTASDIR.'sp-post-as-uninstall.php';
    sp_post_as_do_sp_deactivate();
}

function sp_post_as_sp_uninstall() {
	require_once SPPOSTASDIR.'sp-post-as-uninstall.php';
    sp_post_as_do_sp_uninstall();
}

function sp_post_as_upgrade_check() {
    require_once SPPOSTASDIR.'sp-post-as-upgrade.php';
    sp_post_as_do_upgrade_check();
}

function sp_post_as_uninstall_option($actionlink, $plugin) {
    require_once SPPOSTASDIR.'sp-post-as-uninstall.php';
    $actionlink = sp_post_as_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_post_as_reset_permissions() {
    require_once SPPOSTASDIR.'sp-post-as-install.php';
    sp_post_as_do_reset_permissions();
}

function sp_post_as_form_options($content, $thisObject) {
    require_once SPPOSTASLIBDIR.'sp-post-as-components.php';
	$content = sp_post_as_do_form_options($content, $thisObject);
	return $content;
}

function sp_post_as_save_post($newpost) {
    require_once SPPOSTASLIBDIR.'sp-post-as-components.php';
	$newpost = sp_post_as_do_save_post($newpost);
	return $newpost;
}

function sp_post_as_load_js($footer) {
    require_once SPPOSTASLIBDIR.'sp-post-as-components.php';
	sp_post_as_do_load_js($footer);
}

function sp_post_as_header() {
    require_once SPPOSTASLIBDIR.'sp-post-as-components.php';
	sp_post_as_do_header();
}

function sp_post_as_ajax_manage() {
    require_once SPPOSTASAJAXDIR.'sp-post-as-ajax-manage.php';
}

function sp_post_as_tooltips($tips, $t) {
    $tips['post_as_user'] = $t.__('Can create a forum post and have the poster be a different user', 'sp-post-as');
    return $tips;
}
