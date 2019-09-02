<?php
/*
Simple:Press Plugin Title: Ranks Information
Version: 2.1.0
Item Id: 3965
Plugin URI: https://simple-press.com/downloads/forum-ranks-information-plugin/
Description: A Simple:Press plugin for displaying information about the ranks on the forum
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPRANKSDBVERSION', 1);

define('SPRANKSDIR', 		SPPLUGINDIR.'rank-info/');
define('SPRANKSADMINDIR',   SPPLUGINDIR.'rank-info/admin/');
define('SPRANKSLIBDIR', 	SPPLUGINDIR.'rank-info/library/');
define('SPRANKSCSS', 		SPPLUGINURL.'rank-info/resources/css/');
define('SPRANKSIMAGES', 	SPPLUGINURL.'rank-info/resources/images/');
define('SPRANKSTAGS', 	    SPPLUGINDIR.'rank-info/template-tags/');
define('SPRANKSTEMP', 	    SPPLUGINDIR.'rank-info/template-files/');
define('SPRANKSSCRIPT',		SPPLUGINURL.'rank-info/resources/jscript/');

add_action('init', 										             'sp_rank_info_localization');
add_action('sph_activate_rank-info/sp-rank-info-plugin.php',         'sp_rank_info_install');
add_action('sph_deactivate_rank-info/sp-rank-info-plugin.php',       'sp_rank_info_deactivate');
add_action('sph_uninstall_rank-info/sp-rank-info-plugin.php',        'sp_rank_info_uninstall');
add_action('sph_activated', 				                         'sp_rank_info_sp_activate');
add_action('sph_deactivated', 				                         'sp_rank_info_sp_deactivate');
add_action('sph_uninstalled', 								         'sp_rank_info_sp_uninstall');
add_action('sph_plugin_update_rank-info/sp-rank-info-plugin.php',    'sp_rank_info_upgrade_check');
add_action('admin_footer',                                           'sp_rank_info_upgrade_check');
add_action('sph_permissions_reset', 						         'sp_rank_info_reset_permissions');
add_action('sph_components_ranks_panel', 						     'sp_rank_info_options');
add_action('sph_get_query_vars', 							         'sp_rank_info_get_query_vars');
add_action('sph_get_def_query_vars', 						         'sp_rank_info_get_def_query_vars');
add_action('sph_print_plugin_styles',				                 'sp_rank_info_load_css');
add_action('sph_print_plugin_scripts',						         'sp_rank_info_load_js');

add_filter('sph_plugins_active_buttons',        'sp_rank_info_uninstall_option', 10, 2);
add_filter('sph_rewrite_rules_start', 			'sp_rank_info_rewrite_rules', 10, 3);
add_filter('sph_query_vars', 					'sp_rank_info_query_vars');
add_filter('sph_pageview', 						'sp_rank_info_pageview');
add_filter('sph_canonical_url', 				'sp_rank_info_canonical_url');
add_filter('sph_page_title', 					'sp_rank_info_page_title', 10, 2);
add_filter('sph_BreadCrumbs', 				    'sp_rank_info_breadcrumb', 10, 5);
add_filter('sph_BreadCrumbsMobile', 		    'sp_rank_info_breadcrumbMobile', 10, 2);
add_filter('sph_DefaultViewTemplate',			'sp_rank_info_template_name', 10, 2);
add_filter('sph_admin_help-admin-components',	'sp_rank_info_help', 10, 3);

function sp_rank_info_localization() {
	sp_plugin_localisation('sp-rank-info');
}

function sp_rank_info_install() {
    require_once SPRANKSDIR.'sp-rank-info-install.php';
    sp_rank_info_do_install();
}

function sp_rank_info_deactivate() {
    require_once SPRANKSDIR.'sp-rank-info-uninstall.php';
    sp_rank_info_do_deactivate();
}

function sp_rank_info_uninstall() {
    require_once SPRANKSDIR.'sp-rank-info-uninstall.php';
    sp_rank_info_do_uninstall();
}

function sp_rank_info_sp_activate() {
	require_once SPRANKSDIR.'sp-rank-info-install.php';
    sp_rank_info_do_sp_activate();
}

function sp_rank_info_sp_deactivate() {
	require_once SPRANKSDIR.'sp-rank-info-uninstall.php';
    sp_rank_info_do_sp_deactivate();
}

function sp_rank_info_sp_uninstall() {
	require_once SPRANKSDIR.'sp-rank-info-uninstall.php';
    sp_rank_info_do_sp_uninstall();
}

function sp_rank_info_upgrade_check() {
    require_once SPRANKSDIR.'sp-rank-info-upgrade.php';
    sp_rank_info_do_upgrade_check();
}

function sp_rank_info_uninstall_option($actionlink, $plugin) {
    require_once SPRANKSDIR.'sp-rank-info-uninstall.php';
    $actionlink = sp_rank_info_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_rank_info_reset_permissions() {
    require_once SPRANKSDIR.'sp-rank-info-install.php';
    sp_rank_info_do_reset_permissions();
}

function sp_rank_info_options() {
    require_once SPRANKSADMINDIR.'sp-rank-info-options.php';
	sp_rank_info_options_form();
}

function sp_rank_info_options_save() {
    require_once SPRANKSADMINDIR.'sp-rank-info-options-save.php';
	return sp_do_rank_info_options_save();
}

function sp_rank_info_rewrite_rules($rules, $slugmatch, $slug) {
	$rules[$slugmatch.'/rankinfo/?$'] = 'index.php?pagename='.$slug.'&sf_rankinfo=view';
    return $rules;
}

function sp_rank_info_query_vars($vars) {
	$vars[] = 'sf_rankinfo';
    return $vars;
}

function sp_rank_info_get_query_vars() {
	SP()->rewrites->pageData['rankinfo'] = SP()->filters->str(get_query_var('sf_rankinfo'));
	if (empty(SP()->rewrites->pageData['rankinfo'])) SP()->rewrites->pageData['rankinfo'] = 0;
}

function sp_rank_info_get_def_query_vars($stuff) {
    if ($stuff[1] == 'rankinfo') {
        SP()->rewrites->pageData['rankinfo'] = true;
        SP()->rewrites->pageData['plugin-vars'] = true;
    }
	if (empty(SP()->rewrites->pageData['rankinfo'])) SP()->rewrites->pageData['rankinfo'] = 0;
}

function sp_rank_info_pageview($pageview) {
    if (!empty(SP()->rewrites->pageData['rankinfo'])) $pageview = 'rankinfo';
    return $pageview;
}

function sp_rank_info_canonical_url($url) {
    if (SP()->rewrites->pageData['pageview'] == 'rankinfo') $url = SP()->spPermalinks->get_url("rankinfo");
    return $url;
}

function sp_rank_info_breadcrumb($breadCrumbs, $args, $crumbEnd, $crumbSpace, $treeCount) {
    if (!empty(SP()->rewrites->pageData['rankinfo'])) {
    	extract($args, EXTR_SKIP);
		if (!empty($icon)) {
			$icon = SP()->theme->paint_icon($iconClass, SPTHEMEICONSURL, sanitize_file_name($icon));
		} else {
			if (!empty($iconText)) $icon = SP()->saveFilters->kses($iconText);
		}
		$breadCrumbs.= $crumbEnd.str_repeat($crumbSpace, $treeCount).$icon."<a class='$linkClass' href='".SP()->spPermalinks->get_url('rankinfo')."'>".__('Rank Information', 'sp-rank-info').'</a>';
    }
    return $breadCrumbs;
}

function sp_rank_info_breadcrumbMobile($breadCrumbs, $args) {
    if (!empty(SP()->rewrites->pageData['rankinfo'])) {
    	extract($args, EXTR_SKIP);
		$breadCrumbs.= "<a class='$tagClass' href='".SP()->spPermalinks->get_url('rankinfo')."'>".__('Rank Information', 'sp-rank-info').'</a>';
    }
    return $breadCrumbs;
}

function sp_rank_info_page_title($title, $sep) {
	$sfseo = SP()->options->get('sfseo');
	if ($sfseo['sfseo_page'] && SP()->rewrites->pageData['pageview'] == 'rankinfo') $title = __('Rank Information', 'sp-rank-info').$sep.$title;
    return $title;
}

function sp_rank_info_template_name($name, $pageview) {
	if ($pageview != 'rankinfo') return $name;
	$tempName = SP()->theme->find_template(SPRANKSTEMP,'spRankInfoView.php');
	return $tempName;
}

function sp_rank_info_load_js($footer) {
    $script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPRANKSSCRIPT.'sp-rank-info.js' : SPRANKSSCRIPT.'sp-rank-info.min.js';
	SP()->plugin->enqueue_script('sprankinfo', $script, array('jquery'), false, $footer);
}

function sp_rank_info_load_css() {
	$css = SP()->theme->find_css(SPRANKSCSS, 'sp-rank-info.css', 'sp-rank-info.spcss');
    SP()->plugin->enqueue_style('sp-rank-info', $css);
}

function sp_rank_info_help($file, $tag, $lang) {
    if ($tag == '[rank-info-options]') $file = SPRANKSADMINDIR.'sp-rank-info-admin-help.'.$lang;
    return $file;
}

# Define Template Tags

function sp_DisplayRankInfo() {
    require_once SPRANKSLIBDIR.'sp-rank-info-components.php';
    sp_do_DisplayRankInfo();
}

function sp_RankInfo($args='', $label='', $toolTip='') {
    require_once SPRANKSTAGS.'sp-rank-info-tag.php';
    return sp_RankInfoTag($args, $label, $toolTip);
}
