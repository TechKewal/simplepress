<?php
/*
Simple:Press Plugin Title: Search
Version: 2.1.0
Item Id: 3912
Plugin URI: https://simple-press.com
Description: A Simple:Press plugin for displaying search results by post instead of by topic
Author: Simple:Press
Original Authors: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2019-02-01 07:57:46 -0500 $
$Rev: 15708 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPSEARCHDBVERSION', 3);

define('SPSEARCHDIR', 		SPPLUGINDIR.'search/');
define('SPSEARCHADMINDIR',	SPPLUGINDIR.'search/admin/');
define('SPSEARCHLIBDIR', 	SPPLUGINDIR.'search/library/');
define('SPSEARCHCSS', 		SPPLUGINURL.'search/resources/css/');
define('SPSEARCHIMAGES',	SPPLUGINURL.'search/resources/images/');
define('SPSEARCHTAGS', 	    SPPLUGINDIR.'search/template-tags/');
define('SPSEARCHTEMP', 	    SPPLUGINDIR.'search/template-files/');
define('SPSEARCHCLASS',		SPPLUGINDIR.'search/classes/');

add_action('init', 										    'sp_search_localization');
add_action('sph_activate_search/sp-search-plugin.php',      'sp_search_install');
add_action('sph_deactivate_search/sp-search-plugin.php',    'sp_search_deactivate');
add_action('sph_uninstall_search/sp-search-plugin.php',     'sp_search_uninstall');
add_action('sph_activated', 				                'sp_search_sp_activate');
add_action('sph_deactivated', 				                'sp_search_sp_deactivate');
add_action('sph_uninstalled', 								'sp_search_sp_uninstall');
add_action('sph_plugin_update_search/sp-search-plugin.php', 'sp_search_upgrade_check');
add_action('admin_footer',                                  'sp_search_upgrade_check');
add_action('sph_search_results',                            'sp_search_results');
add_action('sph_print_plugin_styles', 						'sp_search_load_css');
add_action('sph_permissions_reset',							'sp_search_reset_permissions');
add_action('sph_options_display_left_panel',	 			'sp_search_admin_options_panel');
add_action('sph_option_display_save',						'sp_search_admin_options_save');
add_filter('sph_admin_help-admin-options',	 				'sp_search_admin_help', 10, 3);
add_filter('sph_perms_tooltips', 				            'sp_search_tooltips', 10, 2);
add_action('sph_BeforeSectionStart',						'sp_search_open_tabs', 1, 2);
add_action('sph_AfterSectionEnd',							'sp_search_close_tabs', 1, 2);

add_filter('sph_plugins_active_buttons',    'sp_search_uninstall_option', 10, 2);
add_filter('sph_SearchResults_args',        'sp_search_old_results');
add_filter('sph_search_query',              'sp_search_query', 10, 4);
add_filter('sph_SearchFormForumScope', 		'sp_search_search_form');
add_filter('sph_add_prepare_search',		'sp_search_prepare_url');
add_filter('sph_build_search_url',			'sp_search_add_search_param');
add_filter('sph_support_vars',				'sp_search_add_page_data');
add_filter('sph_SectionStartRowClass',		'sp_search_fix_rowclass', 1, 3);
add_filter('sph_SectionStartRowID',			'sp_search_fix_rowid', 1, 3);

# Always load our widget
require_once SPSEARCHTAGS.'sp-search-widget.php';

function sp_search_localization() {
	sp_plugin_localisation('sp-search');
}

function sp_search_install() {
    require_once SPSEARCHDIR.'sp-search-install.php';
    sp_search_do_install();
}

function sp_search_deactivate() {
    require_once SPSEARCHDIR.'sp-search-uninstall.php';
    sp_search_do_deactivate();
}

function sp_search_uninstall() {
    require_once SPSEARCHDIR.'sp-search-uninstall.php';
    sp_search_do_uninstall();
}

function sp_search_sp_activate() {
	require_once SPSEARCHDIR.'sp-search-install.php';
    sp_search_do_sp_activate();
}

function sp_search_sp_deactivate() {
	require_once SPSEARCHDIR.'sp-search-uninstall.php';
    sp_search_do_sp_deactivate();
}

function sp_search_sp_uninstall() {
	require_once SPSEARCHDIR.'sp-search-uninstall.php';
    sp_search_do_sp_uninstall();
}

function sp_search_upgrade_check() {
    require_once SPSEARCHDIR.'sp-search-upgrade.php';
    sp_search_do_upgrade_check();
}

function sp_search_uninstall_option($actionlink, $plugin) {
    require_once SPSEARCHDIR.'sp-search-uninstall.php';
    $actionlink = sp_search_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_search_reset_permissions() {
	require_once SPSEARCHDIR.'sp-search-install.php';
	sp_search_do_reset_permissions();
}

function sp_search_admin_options_panel() {
	require_once SPSEARCHADMINDIR.'sp-search-options-form.php';
	sp_search_do_admin_options_panel();
}

function sp_search_admin_options_save() {
	require_once SPSEARCHADMINDIR.'sp-search-options-save.php';
	sp_search_do_admin_options_save();
}

function sp_search_admin_help($file, $tag, $lang) {
    if ($tag == '[search]') $file = SPSEARCHADMINDIR.'sp-search-admin-help.'.$lang;
    return $file;
}

function sp_search_tooltips($tips, $t) {
    $tips['blogsearch'] = $t.__('Can search blog posts and pages within forum search', 'sp-search');
    return $tips;
}

function sp_search_old_results($args) {
    require_once SPSEARCHLIBDIR.'sp-search-components.php';
    $args = sp_search_do_old_results($args);
	return $args;
}

function sp_search_results() {
    require_once SPSEARCHLIBDIR.'sp-search-components.php';
    sp_search_do_results();
}

function sp_search_query($query, $searchTerm, $searchType, $searchInclude) {
    require_once SPSEARCHLIBDIR.'sp-search-components.php';
    $query = sp_search_do_query($query, $searchTerm, $searchType, $searchInclude);
	return $query;
}

function sp_search_load_css() {
    require_once SPSEARCHLIBDIR.'sp-search-components.php';
	sp_search_do_load_css();
}

function sp_search_open_tabs($section, $args) {
	if (!sp_search_is_valid($section)) return;
	require_once SPSEARCHLIBDIR.'sp-search-components.php';
	sp_search_do_open_tabs();
	return;
}

function sp_search_close_tabs($section, $args) {
	if (!sp_search_is_valid($section)) return;
	require_once SPSEARCHLIBDIR.'sp-search-components.php';
	sp_search_do_close_tabs();
	return;
}

function sp_search_search_form($out) {
	require_once SPSEARCHLIBDIR.'sp-search-components.php';
	return sp_search_do_search_form($out);
}

function sp_search_prepare_url($params) {
	require_once SPSEARCHLIBDIR.'sp-search-components.php';
	return sp_search_do_prepare_url($params);
}

function sp_search_add_search_param($params) {
	if (!isset($_REQUEST['blog'])) return;
	require_once SPSEARCHLIBDIR.'sp-search-components.php';
	$params = sp_search_do_add_search_param($params);
	return $params;
}

function sp_search_add_page_data($data) {
	require_once SPSEARCHLIBDIR.'sp-search-components.php';
	$data = sp_search_do_add_page_data($data);
	return $data;
}

function sp_search_fix_rowclass($rowClass, $sectionName, $a) {
	if ($sectionName == 'bloglist') {
		require_once SPSEARCHLIBDIR.'sp-search-components.php';
		$rowClass = sp_search_do_fix_rowclass($rowClass);
	}
	return $rowClass;
}

function sp_search_fix_rowid($rowId, $sectionName, $a) {
	if($sectionName == 'bloglist') {
		require_once SPSEARCHLIBDIR.'sp-search-components.php';
		$rowID = sp_search_do_fix_rowid($rowId);
	}
	return $rowId;
}

function sp_search_is_valid($section) {
	$valid = false;
	if ($section == 'body' && isset($_REQUEST['blog']) && isset($_REQUEST['value']) && $_REQUEST['type'] < 4 && $_REQUEST['include'] < 4) $valid = true;
	return $valid;
}
