<?php
/*
Simple:Press Plugin Title: Policy Documents
Version: 2.1.0
Item Id: 3971
Plugin URI: https://simple-press.com/downloads/policy-documents-plugin/
Description: A Simple:Press plugin for displaying a forum policy document in your forum
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPPDDBVERSION', 2);

define('PDDIR', 		SPPLUGINDIR.'policy-doc/');
define('PDADMINDIR', 	SPPLUGINDIR.'policy-doc/admin/');
define('PDAJAXDIR', 	SPPLUGINDIR.'policy-doc/ajax/');
define('PDLIBDIR', 		SPPLUGINDIR.'policy-doc/library/');
define('PDSCRIPT', 		SPPLUGINURL.'policy-doc/resources/jscript/');
define('PDTAGSDIR', 	SPPLUGINDIR.'policy-doc/template-tags/');
define('PDTEMPDIR', 	SPPLUGINDIR.'policy-doc/template-files/');
define('PDCSS', 		SPPLUGINURL.'policy-doc/resources/css/');
define('PDIMAGES',	 	SPPLUGINURL.'policy-doc/resources/images/');

add_action('sph_activate_policy-doc/sp-policy-doc-plugin.php', 	    'sp_policy_doc_install');
add_action('sph_deactivate_policy-doc/sp-policy-doc-plugin.php',	'sp_policy_doc_deactivate');
add_action('sph_uninstall_policy-doc/sp-policy-doc-plugin.php',     'sp_policy_doc_uninstall');
add_action('sph_admin_menu', 									    'sp_policy_doc_menu');
add_action('sph_integration_storage_panel_location', 			    'sp_policy_doc_storage_location');
add_action('sph_integration_storage_save', 						    'sp_policy_doc_storage_save');
add_action('sph_print_plugin_scripts', 							    'sp_policy_doc_load_js');
add_action('sph_print_plugin_styles', 							    'sp_policy_doc_header');
add_action('init', 												    'sp_policy_doc_localization');
add_action('sph_uninstalled', 								        'sp_policy_doc_sp_uninstall');
add_action('sph_get_query_vars', 								    'sp_policy_doc_get_query_vars');
add_action('sph_get_def_query_vars', 							    'sp_policy_doc_get_def_query_vars');
add_action('sph_plugin_update_policy-doc/sp-policy-doc-plugin.php', 'sp_policy_doc_upgrade_check');
add_action('admin_footer',                                          'sp_policy_doc_upgrade_check');

add_filter('sph_plugins_active_buttons', 		'sp_policy_doc_uninstall_option', 10, 2);
add_filter('sph_admin_help-admin-components', 	'sp_policy_doc_admin_help', 10, 3);
add_filter('sph_integration_tooltips', 			'sp_policy_doc_tooltip');
add_filter('sph_RegisterButton_args', 			'sp_policy_doc_register_link');
add_filter('sph_pageview_policy', 				'sp_policy_doc_render_page');
add_filter('sph_rewrite_rules_start', 			'sp_policy_doc_rewrite_rules', 10, 3);
add_filter('sph_query_vars', 					'sp_policy_doc_query_vars');
add_filter('sph_pageview', 						'sp_policy_doc_pageview');
add_filter('sph_canonical_url', 				'sp_policy_doc_canonical_url');
add_filter('sph_page_title', 					'sp_policy_doc_page_title', 1, 2);
add_filter('sph_BreadCrumbs', 				    'sp_policy_doc_breadcrumb', 10, 5);
add_filter('sph_BreadCrumbsMobile', 			'sp_policy_doc_breadcrumbMobile', 10, 2);
add_filter('sph_DefaultViewTemplate',			'sp_policy_doc_template_name', 10, 2);

# Ajax Handler
add_action('wp_ajax_policy-doc',		'sp_policy_doc_ajax_manage');
add_action('wp_ajax_nopriv_policy-doc',	'sp_policy_doc_ajax_manage');


function sp_policy_doc_menu() {
    $subpanels = array(
                __('Policy Documents', 'sp-policy') => array('admin' => 'sp_policy_doc_admin_options', 'save' => 'sp_policy_doc_admin_save_options', 'form' => 1, 'id' => 'policyopt')
                            );
    SP()->plugin->add_admin_subpanel('components', $subpanels);
}

function sp_policy_doc_admin_options() {
    require_once PDADMINDIR.'sp-policy-doc-admin-options.php';
	sp_policy_doc_admin_options_form();
}

function sp_policy_doc_admin_save_options() {
    require_once PDADMINDIR.'sp-policy-doc-admin-options-save.php';
    return sp_policy_doc_admin_options_save();
}

function sp_policy_doc_admin_help($file, $tag, $lang) {
    if ($tag == '[registration-policy]' || $tag == '[privacy-policy]') $file = PDADMINDIR.'sp-policy-doc-admin-help.'.$lang;
    return $file;
}

function sp_policy_doc_uninstall_option($actionlink, $plugin) {
    require_once PDLIBDIR.'sp-policy-doc-components.php';
    $actionlink = sp_policy_doc_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_policy_doc_localization() {
	sp_plugin_localisation('sp-policy');
}

function sp_policy_doc_deactivate() {
    require_once PDDIR.'sp-policy-doc-uninstall.php';
    sp_policy_doc_do_deactivate();
}

function sp_policy_doc_uninstall() {
    require_once PDDIR.'sp-policy-doc-uninstall.php';
    sp_policy_doc_do_uninstall();
}

function sp_policy_doc_sp_uninstall() {
	require_once PDDIR.'sp-policy-doc-uninstall.php';
    sp_policy_doc_do_sp_uninstall();
}

function sp_policy_doc_install() {
    require_once PDDIR.'sp-policy-doc-install.php';
    sp_policy_doc_do_install();
}

function sp_policy_doc_upgrade_check() {
    require_once PDDIR.'sp-policy-doc-upgrade.php';
    sp_policy_doc_do_upgrade_check();
}

function sp_policy_doc_rewrite_rules($rules, $slugmatch, $slug) {
	$rules[$slugmatch.'/policy/?$'] = 'index.php?pagename='.$slug.'&sf_policy=show';
    return $rules;
}

function sp_policy_doc_query_vars($vars) {
	$vars[] = 'sf_policy';
    return $vars;
}

function sp_policy_doc_get_query_vars() {
	SP()->rewrites->pageData['policy'] = SP()->filters->str(get_query_var('sf_policy'));
	if (empty(SP()->rewrites->pageData['policy'])) SP()->rewrites->pageData['policy'] = 0;
}

function sp_policy_doc_get_def_query_vars($stuff) {
	if ($stuff[1] == 'policy') SP()->rewrites->pageData['policy'] = 'show';
	if (empty(SP()->rewrites->pageData['policy'])) SP()->rewrites->pageData['policy'] = 0;
}

function sp_policy_doc_pageview($pageview) {
	if (!empty(SP()->rewrites->pageData['policy'])) $pageview = 'policy';
    return $pageview;
}

function sp_policy_doc_canonical_url($url) {
	if (SP()->rewrites->pageData['pageview'] == 'policy') $url = SP()->spPermalinks->get_url('policy');
    return $url;
}

function sp_policy_doc_page_title($title, $sep) {
	$policy = (isset(SP()->rewrites->pageData['policy'])) ? urlencode(SP()->rewrites->pageData['policy']) : '';
	if (!empty($policy) && $policy == 'show') return __('Site Policy', 'sp-policy').$sep.$title;
    return $title;
}

function sp_policy_doc_breadcrumb($breadCrumbs, $args, $crumbEnd, $crumbSpace, $treeCount) {
    if (!empty(SP()->rewrites->pageData['policy'])) {
    	extract($args, EXTR_SKIP);
		if (!empty($icon)) {
			$icon = SP()->theme->paint_icon($iconClass, SPTHEMEICONSURL, sanitize_file_name($icon));
		} else {
			if (!empty($iconText)) $icon = SP()->saveFilters->kses($iconText);
		}
		$breadCrumbs.= $crumbEnd.str_repeat($crumbSpace, $treeCount).$icon."<a class='$linkClass' href='".SP()->spPermalinks->get_url('policy')."'>".__('Policy', 'sp-policy').'</a>';
    }
    return $breadCrumbs;
}

function sp_policy_doc_breadcrumbMobile($breadCrumbs, $args) {
    if (!empty(SP()->rewrites->pageData['policy'])) {
    	extract($args, EXTR_SKIP);
		$breadCrumbs.= "<a class='$tagClass' href='".SP()->spPermalinks->get_url('policy')."'>".__('Policy', 'sp-policy').'</a>';
    }
    return $breadCrumbs;
}

function sp_policy_doc_header() {
    require_once PDLIBDIR.'sp-policy-doc-components.php';
    sp_policy_doc_do_header();
}

function sp_policy_doc_ajax_manage() {
	require_once PDLIBDIR.'sp-policy-doc-components.php';
	require_once PDAJAXDIR.'sp-policy-doc-ajax-manage.php';
}

function sp_policy_doc_storage_location() {
	$storage = SP()->options->get('sfconfig');
	$path = SP_STORE_DIR.'/'.$storage['policies'];
	spa_paint_storage_input(__('Forum policy documents folder', 'sp-policy'), 'policies', $storage['policies'], $path, false, true);
}

function sp_policy_doc_storage_save() {
	$storage = SP()->options->get('sfconfig');
	if (!empty($_POST['policies'])) $storage['policies'] = trim(SP()->saveFilters->title(trim($_POST['policies'])), '/');
	SP()->options->update('sfconfig', $storage);
}

function sp_policy_doc_tooltip($tooltips) {
    require_once PDLIBDIR.'sp-policy-doc-components.php';
    $tooltips = sp_policy_doc_do_tooltip($tooltips);
	return $tooltips;
}

function sp_policy_doc_load_js($footer) {
    $script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? PDSCRIPT.'sp-policy-doc.js' : PDSCRIPT.'sp-policy-doc.min.js';
	SP()->plugin->enqueue_script('sppolicydoc', $script, array(), false, $footer);
}

function sp_policy_doc_register_link($args) {
	$policy = SP()->options->get('policy-doc');
	if ($policy['regform']) $args['link'] = SP()->spPermalinks->get_url('policy');
	return $args;
}

function sp_policy_doc_template_name($name, $pageview) {
    require_once PDLIBDIR.'sp-policy-doc-components.php';
    $name = spPolicyDocTemplateName($name, $pageview);
    return $name;
}

# Define Template Tags globally available (dont have to be enabled on template tag panel)

# for inline registration policy on the register form
function sp_PolicyDocShow($args='', $headerLabel='', $acceptLabel='') {
	require_once PDTAGSDIR.'sp-policy-doc-show-tag.php';
	sp_PolicyDocShowTag($args, $headerLabel, $acceptLabel);
}

# for standalone registration policy page
function sp_PolicyDocRegisterShow($args='', $label='') {
	require_once PDTAGSDIR.'sp-policy-doc-show-register-tag.php';
	sp_PolicyDocShowRegisterTag($args, $label);
}

# for standalone privacy policy page
function sp_PolicyDocPrivacyShow($args='', $label='') {
	require_once PDTAGSDIR.'sp-policy-doc-show-privacy-tag.php';
	sp_PolicyDocShowPrivacyTag($args, $label);
}

function sp_PolicyDocPolicyLink($args='', $label='', $toolTip='') {
	require_once PDTAGSDIR.'sp-policy-doc-policy-link-tag.php';
	sp_PolicyDocPolicyLinkTag($args, $label, $toolTip);
}

function sp_PolicyDocPrivacyLink($args='', $label='', $toolTip='') {
	require_once PDTAGSDIR.'sp-policy-doc-privacy-link-tag.php';
	sp_PolicyDocPrivacyLinkTag($args, $label, $toolTip);
}
