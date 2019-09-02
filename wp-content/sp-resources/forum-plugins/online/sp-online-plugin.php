<?php
/*
Simple:Press Plugin Title: Who's Online
Version: 2.1.0
Item Id: 3959
Plugin URI: https://simple-press.com/downloads/whos-online-plugin/
Description: A Simple:Press plugin for showing who is online around your forum
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPWODBVERSION', 1);

define('SPWODIR', 			SPPLUGINDIR.'online/');
define('SPWOADMINDIR', 		SPPLUGINDIR.'online/admin/');
define('SPWOLIBDIR', 		SPPLUGINDIR.'online/library/');
define('SPWOTEMPDIR', 		SPPLUGINDIR.'online/template-files/');
define('SPWOTAGSDIR', 		SPPLUGINDIR.'online/template-tags/');
define('SPWOCSS', 		    SPPLUGINURL.'online/resources/css/');

add_action('init', 											'sp_online_localization');
add_action('sph_activate_online/sp-online-plugin.php', 		'sp_online_install');
add_action('sph_uninstall_online/sp-online-plugin.php', 	'sp_online_uninstall');
add_action('sph_deactivate_online/sp-online-plugin.php', 	'sp_online_deactivate');
add_action('sph_activated', 				                'sp_online_sp_activate');
add_action('sph_deactivated', 				                'sp_online_sp_deactivate');
add_action('sph_uninstalled', 								'sp_online_sp_uninstall');
add_action('sph_plugin_update_online/sp-online-plugin.php', 'sp_online_upgrade_check');
add_action('admin_footer',                                  'sp_online_upgrade_check');
add_action('sph_get_query_vars', 							'sp_online_get_query_vars');
add_action('sph_get_def_query_vars', 						'sp_online_get_def_query_vars');
add_action('sph_permissions_reset', 						'sp_online_reset_permissions');

add_filter('sph_plugins_active_buttons', 		'sp_online_uninstall_option', 10, 2);
add_filter('sph_rewrite_rules_start', 			'sp_online_rewrite_rules', 10, 3);
add_filter('sph_query_vars', 					'sp_online_query_vars');
add_filter('sph_pageview', 						'sp_online_pageview');
add_filter('sph_canonical_url', 				'sp_online_canonical_url');
add_filter('sph_page_title', 					'sp_online_page_title', 10, 2);
add_filter('sph_DefaultViewTemplate',			'sp_online_template_name', 10, 2);
add_action('sph_print_plugin_styles',			'sp_online_header');
add_filter('sph_perms_tooltips', 				'sp_online_tooltips', 10, 2);
add_filter('sph_BreadCrumbs', 				    'sp_online_breadcrumb', 10, 5);
add_filter('sph_BreadCrumbsMobile', 		    'sp_online_breadcrumbMobile', 10, 2);

function sp_online_localization() {
	sp_plugin_localisation('spwo');
}

function sp_online_install() {
    require_once SPWODIR.'sp-online-install.php';
    sp_online_do_install();
}

function sp_online_reset_permissions() {
    require_once SPWODIR.'sp-online-install.php';
    sp_online_do_reset_permissions();
}

function sp_online_uninstall() {
    require_once SPWODIR.'sp-online-uninstall.php';
    sp_online_do_uninstall();
}

function sp_online_deactivate() {
    SP()->auths->deactivate('view_online_activity');
}

function sp_online_sp_activate() {
	require_once SPWODIR.'sp-online-install.php';
    sp_online_do_sp_activate();
}

function sp_online_sp_deactivate() {
	require_once SPWODIR.'sp-online-uninstall.php';
    sp_online_do_sp_deactivate();
}

function sp_online_upgrade_check() {
    require_once SPWODIR.'sp-online-upgrade.php';
    sp_online_do_upgrade_check();
}

function sp_online_sp_uninstall() {
	require_once SPWODIR.'sp-online-uninstall.php';
    sp_online_do_sp_uninstall();
}

function sp_online_uninstall_option($actionlink, $plugin) {
    require_once SPWODIR.'sp-online-uninstall.php';
    $actionlink = sp_online_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_online_tooltips($tips, $t) {
    $tips['view_online_activity'] = $t.__('Can view the online activity page showing what users/guests are viewing', 'spwo');
    return $tips;
}

function sp_online_rewrite_rules($rules, $slugmatch, $slug) {
	$rules[$slugmatch.'/online/?$'] = 'index.php?pagename='.$slug.'&sf_online=view';
    return $rules;
}

function sp_online_header() {
    require_once SPWOLIBDIR.'sp-online-components.php';
    sp_online_do_header();
}

function sp_online_query_vars($vars) {
	$vars[] = 'sf_online';
    return $vars;
}

function sp_online_get_query_vars() {
	SP()->rewrites->pageData['online'] = SP()->filters->str(get_query_var('sf_online'));
	if (empty(SP()->rewrites->pageData['online'])) SP()->rewrites->pageData['online'] = 0;
}

function sp_online_get_def_query_vars($stuff) {
    if ($stuff[1] == 'online') {
        SP()->rewrites->pageData['online'] = true;
        SP()->rewrites->pageData['plugin-vars'] = true;
    }
	if (empty(SP()->rewrites->pageData['online'])) SP()->rewrites->pageData['online'] = 0;
}

function sp_online_pageview($pageview) {
    if (!empty(SP()->rewrites->pageData['online'])) $pageview = 'online';
    return $pageview;
}

function sp_online_canonical_url($url) {
    require_once SPWOLIBDIR.'sp-online-components.php';
    $url = spOnlineCanonicalUrl($url);
    return $url;
}

function sp_online_breadcrumb($breadCrumbs, $args, $crumbEnd, $crumbSpace, $treeCount) {
    if (!empty(SP()->rewrites->pageData['online'])) {
    	extract($args, EXTR_SKIP);
		if (!empty($icon)) {
			$icon = SP()->theme->paint_icon($iconClass, SPTHEMEICONSURL, sanitize_file_name($icon));
		} else {
			if (!empty($iconText)) $icon = SP()->saveFilters->kses($iconText);
		}
		$breadCrumbs.= $crumbEnd.str_repeat($crumbSpace, $treeCount).$icon."<a class='$linkClass' href='".SP()->spPermalinks->get_url('online')."'>".__('Online', 'spwo').'</a>';
    }
    return $breadCrumbs;
}

function sp_online_breadcrumbMobile($breadCrumbs, $args) {
    if (!empty(SP()->rewrites->pageData['online'])) {
    	extract($args, EXTR_SKIP);
		$breadCrumbs.= "<a class='$tagClass' href='".SP()->spPermalinks->get_url('online')."'>".__('Online', 'spwo').'</a>';
    }
    return $breadCrumbs;
}

function sp_online_page_title($title, $sep) {
    require_once SPWOLIBDIR.'sp-online-components.php';
    $title = spOnlinePageTitle($title, $sep);
    return $title;
}

function sp_online_template_name($name, $pageview) {
    require_once SPWOLIBDIR.'sp-online-components.php';
    $name = spOnlineTemplateName($name, $pageview);
    return $name;
}

# Define Template Tags globally available (dont have to be enabled on template tag panel)
function sp_OnlineCurrentlyOnline($args, $currentLabel='', $guestLabel='') {
    require_once SPWOLIBDIR.'sp-online-components.php';
    require_once SPWOTAGSDIR.'sp-online-currently-tag.php';
    sp_OnlineCurrentlyOnlineTag($args, $currentLabel, $guestLabel);
}

function sp_OnlineSiteActivity($args) {
    require_once SPWOTAGSDIR.'sp-online-activity-tag.php';
    sp_OnlineSiteActivityTag($args);
}

function sp_OnlinePageLink($args, $label='') {
    require_once SPWOTAGSDIR.'sp-online-link-tag.php';
    sp_OnlinePageLinkTag($args, $label);
}
