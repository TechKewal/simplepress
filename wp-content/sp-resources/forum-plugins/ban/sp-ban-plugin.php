<?php
/*
Simple:Press Plugin Title: Ban
Version: 2.1.0
Item Id: 3957
Plugin URI: https://simple-press.com/downloads/user-ban-plugin/
Description: A Simple:Press plugin for banning users from the forum by IP, IP range, hostname, user agent or User ID
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPBANDBVERSION', 1);

define('SPBANDIR', 		SPPLUGINDIR.'ban/');
define('SPBANADMINDIR', SPPLUGINDIR.'ban/admin/');
define('SPBANAJAXDIR', 	SPPLUGINDIR.'ban/ajax/');
define('SPBANLIBDIR', 	SPPLUGINDIR.'ban/library/');
define('SPBANCSS', 		SPPLUGINURL.'ban/resources/css/');
define('SPBANIMAGES', 	SPPLUGINURL.'ban/resources/images/');
define('SPBANSCRIPT', 	SPPLUGINURL.'ban/resources/jscript/');
define('SPBANTEMP', 	SPPLUGINDIR.'ban/template-files/');

add_action('init', 										 'sp_ban_localization');
add_action('sph_activate_ban/sp-ban-plugin.php',         'sp_ban_install');
add_action('sph_deactivate_ban/sp-ban-plugin.php',       'sp_ban_deactivate');
add_action('sph_uninstall_ban/sp-ban-plugin.php',        'sp_ban_uninstall');
add_action('sph_activated', 				             'sp_ban_sp_activate');
add_action('sph_deactivated', 				             'sp_ban_sp_deactivate');
add_action('sph_uninstalled', 							 'sp_ban_sp_uninstall');
add_action('sph_plugin_update_ban/sp-ban-plugin.php',    'sp_ban_upgrade_check');
add_action('admin_footer',                               'sp_ban_upgrade_check');
add_action('sph_permissions_reset', 					 'sp_ban_reset_permissions');
add_action('sph_admin_menu', 							 'sp_ban_menu');
add_action('template_redirect',                          'sp_ban_check_bans');
add_action('sph_get_query_vars', 						 'sp_ban_get_query_vars');
add_action('sph_get_def_query_vars', 					 'sp_ban_get_def_query_vars');
add_action('sph_head_end', 						         'sp_ban_header');
add_action('sph_scripts_admin_end', 					 'sp_ban_load_admin_js');

add_filter('sph_plugins_active_buttons',    'sp_ban_uninstall_option', 10, 2);
add_filter('sph_rewrite_rules_start', 		'sp_ban_rewrite_rules', 10, 3);
add_filter('sph_query_vars', 				'sp_ban_query_vars');
add_filter('sph_pageview', 					'sp_ban_pageview');
add_filter('sph_canonical_url', 			'sp_ban_canonical_url');
add_filter('sph_page_title', 				'sp_ban_page_title', 10, 2);
add_filter('sph_DefaultViewTemplate',		'sp_ban_template_name', 10, 2);
add_filter('sph_BreadCrumbs', 				'sp_ban_breadcrumb', 10, 5);
add_filter('sph_BreadCrumbsMobile', 		'sp_ban_breadcrumbMobile', 10, 2);
add_filter('sph_admin_help-admin-users',	'sp_ban_help', 10, 3);

# Ajax Call
add_action('wp_ajax_ban-manage', 'sp_ban_ajax_manage');
add_action('wp_ajax_nopriv_ban-manage', 'sp_ban_ajax_manage');


function sp_ban_menu() {
    $subpanels = array(
            __('Bans', 'sp-ban') => array('admin' => 'sp_ban_admin', 'save' => 'sp_ban_admin_save', 'form' => 0, 'id' => 'banpanel'),
            );
    SP()->plugin->add_admin_subpanel('users', $subpanels);
}

function sp_ban_admin() {
    require_once SPBANADMINDIR.'sp-ban-admin.php';
	sp_ban_admin_form();
}

function sp_ban_admin_save_bans() {
    require_once SPBANADMINDIR.'sp-ban-admin-save.php';
    return sp_ban_admin_do_save_bans();
}

function sp_ban_admin_save_msgs() {
    require_once SPBANADMINDIR.'sp-ban-admin-save.php';
    return sp_ban_admin_do_save_msgs();
}

function sp_ban_admin_save_user() {
    require_once SPBANADMINDIR.'sp-ban-admin-save.php';
    return sp_ban_admin_do_save_user();
}

function sp_ban_help($file, $tag, $lang) {
    if ($tag == '[ban-ip]' || $tag == '[ban-ip-range]' || $tag == '[ban-hostname]' || $tag == '[ban-user-agent]' || $tag == '[ban-general-message]' ||
        $tag == '[ban-restriction-message]' || $tag == '[ban-user-message]' || $tag == '[ban-expire-message]' || $tag == '[ban-user]' || $tag == '[current-banned]') $file = SPBANADMINDIR.'sp-ban-admin-help.'.$lang;
    return $file;
}

function sp_ban_load_admin_js() {
    wp_enqueue_script('jquery-ui-autocomplete', false, array('jquery', 'jquery-ui-core', 'jquery-ui-widget'));
}

function sp_ban_ajax_manage() {
    require_once SPBANAJAXDIR.'sp-ban-ajax-manage.php';
}

function sp_ban_localization() {
	sp_plugin_localisation('sp-ban');
}

function sp_ban_install() {
    require_once SPBANDIR.'sp-ban-install.php';
    sp_ban_do_install();
}

function sp_ban_deactivate() {
    require_once SPBANDIR.'sp-ban-uninstall.php';
    sp_ban_do_deactivate();
}

function sp_ban_uninstall() {
    require_once SPBANDIR.'sp-ban-uninstall.php';
    sp_ban_do_uninstall();
}

function sp_ban_sp_activate() {
	require_once SPBANDIR.'sp-ban-install.php';
    sp_ban_do_sp_activate();
}

function sp_ban_sp_deactivate() {
	require_once SPBANDIR.'sp-ban-uninstall.php';
    sp_ban_do_sp_deactivate();
}

function sp_ban_sp_uninstall() {
	require_once SPBANDIR.'sp-ban-uninstall.php';
    sp_ban_do_sp_uninstall();
}

function sp_ban_upgrade_check() {
    require_once SPBANDIR.'sp-ban-upgrade.php';
    sp_ban_do_upgrade_check();
}

function sp_ban_uninstall_option($actionlink, $plugin) {
    require_once SPBANDIR.'sp-ban-uninstall.php';
    $actionlink = sp_ban_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_ban_reset_permissions() {
    require_once SPBANDIR.'sp-ban-install.php';
    sp_ban_do_reset_permissions();
}

function sp_ban_check_bans() {
    require_once SPBANLIBDIR.'sp-ban-components.php';
    sp_ban_do_check_bans();
}

function sp_ban_rewrite_rules($rules, $slugmatch, $slug) {
	$rules[$slugmatch.'/banned/?$'] = 'index.php?pagename='.$slug.'&sf_banned=view';
    return $rules;
}

function sp_ban_query_vars($vars) {
	$vars[] = 'sf_banned';
    return $vars;
}

function sp_ban_get_query_vars() {
	SP()->rewrites->pageData['banned'] = SP()->filters->str(get_query_var('sf_banned'));
	if (empty(SP()->rewrites->pageData['banned'])) SP()->rewrites->pageData['banned'] = 0;
}

function sp_ban_get_def_query_vars($stuff) {
    if ($stuff[1] == 'banned') {
        SP()->rewrites->pageData['banned'] = true;
        SP()->rewrites->pageData['plugin-vars'] = true;
    }
	if (empty(SP()->rewrites->pageData['banned'])) SP()->rewrites->pageData['banned'] = 0;
}

function sp_ban_pageview($pageview) {
    if (!empty(SP()->rewrites->pageData['banned'])) $pageview = 'banned';
    return $pageview;
}

function sp_ban_canonical_url($url) {
    if (SP()->rewrites->pageData['pageview'] == 'banned') $url = SP()->spPermalinks->get_url('banned');
    return $url;
}

function sp_ban_breadcrumb($breadCrumbs, $args, $crumbEnd, $crumbSpace, $treeCount) {
    if (!empty(SP()->rewrites->pageData['banned'])) {
    	extract($args, EXTR_SKIP);

		if (!empty($icon)) {
			$icon = SP()->theme->paint_icon($iconClass, SPTHEMEICONSURL, sanitize_file_name($icon));
		} else {
			if (!empty($iconText)) $icon = SP()->saveFilters->kses($iconText);
		}

		$breadCrumbs.= $crumbEnd.str_repeat($crumbSpace, $treeCount).$icon."<a class='$linkClass' href='".SP()->spPermalinks->get_url('banned')."'>".__('Banned', 'sp-ban').'</a>';
    }
    return $breadCrumbs;
}

function sp_ban_breadcrumbMobile($breadCrumbs, $args) {
    if (!empty(SP()->rewrites->pageData['banned'])) {
    	extract($args, EXTR_SKIP);
		$breadCrumbs.= "<a class='$tagClass' href='".SP()->spPermalinks->get_url('banned')."'>".__('Banned', 'sp-ban').'</a>';
    }
    return $breadCrumbs;
}

function sp_ban_page_title($title, $sep) {
	$sfseo = SP()->options->get('sfseo');
	if ($sfseo['sfseo_page'] && SP()->rewrites->pageData['pageview'] == 'banned') $title = __('Banned', 'sp-ban').$sep.$title;
    return $title;
}

function sp_ban_template_name($name, $pageview) {
	if ($pageview == 'banned') $name = SP()->theme->find_template(SPBANTEMP,'spBannedView.php');
	return $name;
}

function sp_ban_header() {
	if (SP()->rewrites->pageData['pageview'] != 'banned') return;
	$css = SP()->theme->find_css(SPBANCSS, 'sp-ban.css');
	echo "<link rel='stylesheet' href='$css' />\n";
}

# Define Template Tags for plugin

function sp_DisplayBannedMessage() {
    require_once SPBANLIBDIR.'sp-ban-components.php';
    sp_do_DisplayBannedMessage();
}
