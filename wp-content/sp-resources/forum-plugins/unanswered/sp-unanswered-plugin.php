<?php
/*
Simple:Press Plugin Title: Unanswered Topics
Version: 2.1.0
Item Id: 4092
Plugin URI: https://simple-press.com/downloads/unanswered-topics-plugin/
Description: A Simple:Press plugin for showing a page of unanswered topics in your forum (requires Template Tags plugin for SP)
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPUADBVERSION', 0);

define('SPUADIR', 			SPPLUGINDIR.'unanswered/');
define('SPUATEMPDIR', 		SPPLUGINDIR.'unanswered/template-files/');
define('SPUACSS', 		    SPPLUGINURL.'unanswered/resources/css/');

add_action('init', 											          'sp_unanswered_localization');
add_action('sph_activate_unanswered/sp-unanswered-plugin.php', 	      'sp_unanswered_install');
add_action('sph_deactivate_unanswered/sp-unanswered-plugin.php',      'sp_unanswered_deactivate');
add_action('sph_uninstall_unanswered/sp-unanswered-plugin.php',       'sp_unanswered_uninstall');
add_action('sph_activated', 				                          'sp_unanswered_sp_activate');
add_action('sph_deactivated', 				                          'sp_unanswered_sp_deactivate');
add_action('sph_uninstalled', 								          'sp_unanswered_sp_uninstall');
add_action('sph_plugin_update_unanswered/sp-unanswered-plugin.php',   'sp_unanswered_upgrade_check');
add_action('sph_get_query_vars', 							          'sp_unanswered_get_query_vars');
add_action('sph_get_def_query_vars', 						          'sp_unanswered_get_def_query_vars');

add_filter('sph_plugins_active_buttons', 		'sp_unanswered_uninstall_option', 10, 2);
add_filter('sph_rewrite_rules_start', 			'sp_unanswered_rewrite_rules', 10, 3);
add_filter('sph_query_vars', 					'sp_unanswered_query_vars');
add_filter('sph_pageview', 						'sp_unanswered_pageview');
add_filter('sph_canonical_url', 				'sp_unanswered_canonical_url');
add_filter('sph_page_title', 					'sp_unanswered_page_title', 10, 2);
add_filter('sph_DefaultViewTemplate',			'sp_unanswered_template_name', 10, 2);
add_action('sph_print_plugin_styles', 			'sp_unanswered_header');
add_filter('sph_BreadCrumbs', 				    'sp_unanswered_breadcrumb', 10, 5);
add_filter('sph_BreadCrumbsMobile', 		    'sp_unanswered_breadcrumbMobile', 10, 2);

function sp_unanswered_localization() {
	sp_plugin_localisation('sp-unanswered');
}

function sp_unanswered_install() {
    require_once SPUADIR.'sp-unanswered-install.php';
    sp_unanswered_do_install();
}

function sp_unanswered_uninstall() {
    require_once SPUADIR.'sp-unanswered-uninstall.php';
    sp_unanswered_do_uninstall();
}

function sp_unanswered_deactivate() {
    SP()->auths->deactivate('view_unanswered_activity');
}

function sp_unanswered_sp_activate() {
	require_once SPUADIR.'sp-unanswered-install.php';
    sp_unanswered_do_sp_activate();
}

function sp_unanswered_sp_deactivate() {
	require_once SPUADIR.'sp-unanswered-uninstall.php';
    sp_unanswered_do_sp_deactivate();
}

function sp_unanswered_sp_uninstall() {
	require_once SPUADIR.'sp-unanswered-uninstall.php';
    sp_unanswered_do_sp_uninstall();
}

function sp_unanswered_uninstall_option($actionlink, $plugin) {
    require_once SPUADIR.'sp-unanswered-uninstall.php';
    $actionlink = sp_unanswered_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_unanswered_rewrite_rules($rules, $slugmatch, $slug) {
	$rules[$slugmatch.'/unanswered/?$'] = 'index.php?pagename='.$slug.'&sf_unanswered=view';
    return $rules;
}

function sp_unanswered_header() {
	$css = SP()->theme->find_css(SPUACSS, 'sp-unanswered.css', 'sp-unanswered.spcss');
    SP()->plugin->enqueue_style('sp-unanswered', $css);
}

function sp_unanswered_query_vars($vars) {
	$vars[] = 'sf_unanswered';
    return $vars;
}

function sp_unanswered_get_query_vars() {
	SP()->rewrites->pageData['unanswered'] = SP()->filters->str(get_query_var('sf_unanswered'));
	if (empty(SP()->rewrites->pageData['unanswered'])) SP()->rewrites->pageData['unanswered'] = 0;
}

function sp_unanswered_get_def_query_vars($stuff) {
    if ($stuff[1] == 'unanswered') {
        SP()->rewrites->pageData['unanswered'] = true;
        SP()->rewrites->pageData['plugin-vars'] = true;
    }
	if (empty(SP()->rewrites->pageData['unanswered'])) SP()->rewrites->pageData['unanswered'] = 0;
}

function sp_unanswered_pageview($pageview) {
    if (!empty(SP()->rewrites->pageData['unanswered'])) $pageview = 'unanswered';
    return $pageview;
}

function sp_unanswered_canonical_url($url) {
    if (SP()->rewrites->pageData['pageview'] == 'unanswered') $url = SP()->spPermalinks->get_url('unanswered');
    return $url;
}

function sp_unanswered_breadcrumb($breadCrumbs, $args, $crumbEnd, $crumbSpace, $treeCount) {
    if (!empty(SP()->rewrites->pageData['unanswered'])) {
    	extract($args, EXTR_SKIP);
		if (!empty($icon)) {
			$icon = SP()->theme->paint_icon($iconClass, SPTHEMEICONSURL, sanitize_file_name($icon));
		} else {
			if (!empty($iconText)) $icon = SP()->saveFilters->kses($iconText);
		}
		$breadCrumbs.= $crumbEnd.str_repeat($crumbSpace, $treeCount).$icon."<a class='$linkClass' href='".SP()->spPermalinks->get_url('unanswered')."'>".__('Unanswered', 'sp-unanswered').'</a>';
    }
    return $breadCrumbs;
}

function sp_unanswered_breadcrumbMobile($breadCrumbs, $args) {
    if (!empty(SP()->rewrites->pageData['unanswered'])) {
    	extract($args, EXTR_SKIP);
		$breadCrumbs.= "<a class='$tagClass' href='".SP()->spPermalinks->get_url('unanswered')."'>".__('Unanswered', 'sp-unanswered').'</a>';
    }
    return $breadCrumbs;
}

function sp_unanswered_page_title($title, $sep) {
	$sfseo = SP()->options->get('sfseo');
	if ($sfseo['sfseo_page'] && SP()->rewrites->pageData['pageview'] == 'unanswered') $title = __('Unanswered Posts', 'sp-unanswered').$sep.$title;
    return $title;
}

function sp_Unanswered_template_name($name, $pageview) {
	if ($pageview != 'unanswered') return $name;
	$name = SP()->theme->find_template(SPUATEMPDIR,'spUnansweredView.php');
	return $name;
}
