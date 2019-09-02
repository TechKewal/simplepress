<?php
/*
Simple:Press Plugin Title: Search By User
Version: 2.1.0
Item Id: 12731
Plugin URI: https://simple-press.com/downloads/search-by-user/
Description: A Simple:Press plugin that allows you to search posts by a specific user. Requires our Simple:Press Search Plugin.
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPSEARCHUSERDBVERSION', 0);

define('SPSEARCHUSERDIR', 		SPPLUGINDIR.'search-by-user/');
define('SPSEARCHUSERAJAXDIR', 	SPPLUGINDIR.'search-by-user/ajax/');
define('SPSEARCHUSERLIBDIR', 	SPPLUGINDIR.'search-by-user/library/');
define('SPSEARCHUSERLIBURL', 	SPPLUGINURL.'search-by-user/library/');
define('SPSEARCHUSERCSS', 		SPPLUGINURL.'search-by-user/resources/css/');
define('SPSEARCHUSERSCRIPT',	SPPLUGINURL.'search-by-user/resources/jscript/');

add_action('init', 										                    'sp_search_by_user_localization');
add_action('sph_activate_search-by-user/sp-search-by-user-plugin.php',      'sp_search_by_user_install');
add_action('sph_deactivate_search-by-user/sp-search-by-user-plugin.php',    'sp_search_by_user_deactivate');
add_action('sph_uninstall_search-by-user/sp-search-by-user-plugin.php',     'sp_search_by_user_uninstall');
add_action('sph_activated', 				                                'sp_search_by_user_sp_activate');
add_action('sph_deactivated', 				                                'sp_search_by_user_sp_deactivate');
add_action('sph_uninstalled', 								                'sp_search_by_user_sp_uninstall');
add_action('sph_plugin_update_search-by-user/sp-search-by-user-plugin.php', 'sp_search_by_user_upgrade_check');
add_action('admin_footer',                                                  'sp_search_by_user_upgrade_check');

add_filter('sph_plugins_active_buttons',    'sp_search_by_user_uninstall_option', 10, 2);

if (SP()->plugin->is_active('search/sp-search-plugin.php')) {
    add_action('sph_print_plugin_scripts', 					'sp_search_by_user_load_js');
    add_action('sph_print_plugin_styles', 					'sp_search_by_user_header');
    add_action('sph_admin_panel_header', 					'sp_search_by_user_show_alert');

    add_filter('sph_SearchFormForumScope', 		'sp_search_by_user_search_form', 100);
    add_filter('sph_search_query',              'sp_search_by_user_query', 10, 4);
    add_filter('sph_blog_search_query',         'sp_blog_search_by_user_query', 10, 4);
    add_filter('sph_add_prepare_search',		'sp_search_by_user_prepare_url');
    add_filter('sph_build_search_url',			'sp_search_by_user_add_search_param');
    add_filter('sph_support_vars',				'sp_search_by_user_add_page_data');

	# Ajax Handler
	add_action('wp_ajax_search-by-user-manage',			'sp_search_by_user_ajax_manage');
	add_action('wp_ajax_nopriv_search-by-user-manage',	'sp_search_by_user_ajax_manage');
}

function sp_search_by_user_localization() {
	sp_plugin_localisation('sp-search-by-user');
}

function sp_search_by_user_install() {
    require_once SPSEARCHUSERDIR.'sp-search-by-user-install.php';
    sp_search_by_user_do_install();
}

function sp_search_by_user_deactivate() {
    require_once SPSEARCHUSERDIR.'sp-search-by-user-uninstall.php';
    sp_search_by_user_do_deactivate();
}

function sp_search_by_user_uninstall() {
    require_once SPSEARCHUSERDIR.'sp-search-by-user-uninstall.php';
    sp_search_by_user_do_uninstall();
}

function sp_search_by_user_sp_activate() {
	require_once SPSEARCHUSERDIR.'sp-search-by-user-install.php';
    sp_search_by_user_do_sp_activate();
}

function sp_search_by_user_sp_deactivate() {
	require_once SPSEARCHUSERDIR.'sp-search-by-user-uninstall.php';
    sp_search_by_user_do_sp_deactivate();
}

function sp_search_by_user_sp_uninstall() {
	require_once SPSEARCHUSERDIR.'sp-search-by-user-uninstall.php';
    sp_search_by_user_do_sp_uninstall();
}

function sp_search_by_user_upgrade_check() {
    require_once SPSEARCHUSERDIR.'sp-search-by-user-upgrade.php';
    sp_search_by_user_do_upgrade_check();
}

function sp_search_by_user_uninstall_option($actionlink, $plugin) {
    require_once SPSEARCHUSERDIR.'sp-search-by-user-uninstall.php';
    $actionlink = sp_search_by_user_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_search_by_user_search_form($out) {
	require_once SPSEARCHUSERLIBDIR.'sp-search-by-user-components.php';
	return sp_search_by_user_do_search_form($out);
}

function sp_search_by_user_query($query, $searchTerm, $searchType, $searchInclude) {
    require_once SPSEARCHUSERLIBDIR.'sp-search-by-user-components.php';
    $query = sp_search_by_user_do_query($query, $searchTerm, $searchType, $searchInclude);
	return $query;
}

function sp_blog_search_by_user_query($query, $searchTerm, $searchType, $searchInclude) {
    require_once SPSEARCHUSERLIBDIR.'sp-search-by-user-components.php';
    $query = sp_blog_search_by_user_do_query($query, $searchTerm, $searchType, $searchInclude);
	return $query;
}

function sp_search_by_user_load_js($footer) {
    require_once SPSEARCHUSERLIBDIR.'sp-search-by-user-components.php';
	sp_search_by_user_do_load_js($footer);
}

function sp_search_by_user_header() {
    require_once SPSEARCHUSERLIBDIR.'sp-search-by-user-components.php';
	sp_search_by_user_do_header();
}

function sp_search_by_user_ajax_manage() {
    require_once SPSEARCHUSERAJAXDIR.'sp-search-by-user-ajax-manage.php';
}

function sp_search_by_user_prepare_url($params) {
	require_once SPSEARCHUSERLIBDIR.'sp-search-by-user-components.php';
	return sp_search_by_user_do_prepare_url($params);
}

function sp_search_by_user_add_search_param($params) {
	if (!isset($_REQUEST['blog'])) return $params;
	require_once SPSEARCHUSERLIBDIR.'sp-search-by-user-components.php';
	$params = sp_search_do_add_search_param($params);
	return $params;
}

function sp_search_by_user_add_page_data($data) {
	require_once SPSEARCHUSERLIBDIR.'sp-search-by-user-components.php';
	$data = sp_search_by_user_do_add_page_data($data);
	return $data;
}

function sp_search_by_user_show_alert(){
	if (!SP()->plugin->is_active('search/sp-search-plugin.php')) echo '<div class="sfoptionerror">'.__('The Simple:Press Search plugin cannot be found!  Please install the Simple:Press Search Plugin to enable Search By User integration.', 'sp-search-by-user').'</div>';
}
