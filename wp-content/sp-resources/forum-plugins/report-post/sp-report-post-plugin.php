<?php
/*
Simple:Press Plugin Title: Report Post
Version: 2.1.0
Item Id: 3960
Plugin URI: https://simple-press.com/downloads/report-post-plugin/
Description: A Simple:Press plugin for reporting a post with questionable content
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPRPDBVERSION', 2);

define('RPDIR', 		SPPLUGINDIR.'report-post/');
define('RPADMINDIR', 	SPPLUGINDIR.'report-post/admin/');
define('RPLIBDIR', 		SPPLUGINDIR.'report-post/library/');
define('RPTAGSDIR', 	SPPLUGINDIR.'report-post/template-tags/');
define('RPTEMPDIR', 	SPPLUGINDIR.'report-post/template-files/');
define('RPIMAGES',	 	SPPLUGINURL.'report-post/resources/images/');
define('RPIMAGESMOB',	SPPLUGINURL.'report-post/resources/images/mobile/');
define('RPCSS', 		SPPLUGINURL.'report-post/resources/css/');
define('RPSCRIPT',	    SPPLUGINURL.'report-post/resources/jscript/');

add_action('init', 											              'sp_report_post_localization');
add_action('sph_activate_report-post/sp-report-post-plugin.php', 	      'sp_report_post_install');
add_action('sph_deactivate_report-post/sp-report-post-plugin.php',	      'sp_report_post_deactivate');
add_action('sph_uninstall_report-post/sp-report-post-plugin.php', 	      'sp_report_post_uninstall');
add_action('sph_get_query_vars', 									      'sp_report_post_get_query_vars');
add_action('sph_get_def_query_vars', 								      'sp_report_post_get_def_query_vars');
add_action('sph_options_email_right_panel', 						      'sp_report_post_admin_options');
add_action('sph_option_email_save', 								      'sp_report_post_admin_save_options');
add_action('sph_print_plugin_styles', 								      'sp_report_post_header');
add_action('sph_print_plugin_scripts',						              'sp_report_post_load_js');
add_action('sph_plugin_update_creport-post/sp-report-post-plugin.php',    'sp_report_post_upgrade_check');
add_action('admin_footer',                                                'sp_report_post_upgrade_check');
add_action('sph_setup_forum',                                             'sp_report_post_setup_forum');
add_action('sph_permissions_reset',                                       'sp_report_post_reset_permissions');

add_filter('sph_plugins_active_buttons', 	'sp_report_post_uninstall_option', 10, 2);
add_filter('sph_admin_help-admin-options', 	'sp_report_post_admin_help', 10, 3);
add_filter('sph_rewrite_rules_start', 		'sp_report_post_rewrite_rules', 10, 3);
add_filter('sph_query_vars', 				'sp_report_post_query_vars');
add_filter('sph_pageview', 					'sp_report_post_pageview');
add_filter('sph_canonical_url', 			'sp_report_post_canonical_url');
add_filter('sph_page_title', 				'sp_report_post_page_title', 10, 2);
add_filter('sph_DefaultViewTemplate',		'sp_report_post_template_name', 10, 2);

function sp_report_post_admin_options() {
    require_once RPADMINDIR.'sp-report-post-admin-options.php';
	sp_report_post_admin_options_form();
}

function sp_report_post_admin_save_options() {
    require_once RPADMINDIR.'sp-report-post-admin-options-save.php';
    return sp_report_post_admin_options_save();
}

function sp_report_post_admin_help($file, $tag, $lang) {
    if ($tag == '[report-post]') $file = RPADMINDIR.'sp-report-post-admin-help.'.$lang;
    return $file;
}

function sp_report_post_uninstall_option($actionlink, $plugin) {
    require_once RPLIBDIR.'sp-report-post-components.php';
    $actionlink = sp_report_post_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_report_post_localization() {
	sp_plugin_localisation('sp-report');
}

function sp_report_post_uninstall() {
    require_once RPDIR.'sp-report-post-uninstall.php';
    sp_report_post_do_uninstall();
}

function sp_report_post_install() {
    require_once RPDIR.'sp-report-post-install.php';
    sp_report_post_do_install();
}

function sp_report_post_deactivate() {
    # deactivation so make our auth not active
    SP()->auths->deactivate('report_posts');
	# remove glossary entries
	sp_remove_glossary_plugin('sp-reportpost');
}

function sp_report_post_upgrade_check() {
    require_once RPDIR.'sp-report-post-upgrade.php';
    sp_report_post_do_upgrade_check();
}

function sp_report_post_reset_permissions() {
    require_once RPDIR.'sp-report-post-install.php';
    sp_report_post_do_reset_permissions();
}

function sp_report_post_load_js($footer) {
    $script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? RPSCRIPT.'sp-report-post.js' : RPSCRIPT.'sp-report-post.min.js';
	SP()->plugin->enqueue_script('spreportpost', $script, array('jquery'), false, $footer);
}

function sp_report_post_header() {
    require_once RPLIBDIR.'sp-report-post-components.php';
    sp_report_post_do_header();
}

function sp_report_post_rewrite_rules($rules, $slugmatch, $slug) {
    # online rewrite rules
	$rules[$slugmatch.'/report-post/?$'] = 'index.php?pagename='.$slug.'&sf_report=view';
    return $rules;
}

function sp_report_post_query_vars($vars) {
	$vars[] = 'sf_report';
    return $vars;
}

function sp_report_post_get_query_vars() {
	SP()->rewrites->pageData['report-post'] = SP()->filters->str(get_query_var('sf_report'));
	if (empty(SP()->rewrites->pageData['report-post'])) SP()->rewrites->pageData['report-post'] = 0;
}

function sp_report_post_get_def_query_vars($stuff) {
    if ($stuff[1] == 'report-post') {
        SP()->rewrites->pageData['report-post'] = true;
        SP()->rewrites->pageData['plugin-vars'] = true;
    }
	if (empty(SP()->rewrites->pageData['report-post'])) SP()->rewrites->pageData['report-post'] = 0;
}

function sp_report_post_pageview($pageview) {
    if (!empty(SP()->rewrites->pageData['report-post'])) $pageview = 'report-post';
    return $pageview;
}

function sp_report_post_canonical_url($url) {
    require_once RPLIBDIR.'sp-report-post-components.php';
    $url = spReportPostCanonicalUrl($url);
    return $url;
}

function sp_report_post_page_title($title, $sep) {
    require_once RPLIBDIR.'sp-report-post-components.php';
    $title = spReportPostPageTitle($title, $sep);
    return $title;
}

function sp_report_post_template_name($name, $pageview) {
    require_once RPLIBDIR.'sp-report-post-components.php';
    $name = spReportPostTemplateName($name, $pageview);
    return $name;
}

function sp_ReportPostForm() {
    require_once RPLIBDIR.'sp-report-post-components.php';
    sp_ReportPostFormTag();
}

function sp_report_post_setup_forum() {
	if (isset($_POST['sendrp'])) {
	    require_once RPLIBDIR.'sp-report-post-components.php';
		spReportPostSendEmail();
	}
}

# Define Template Tags globally available
function sp_PostIndexReportPost($args='', $label='', $toolTip='') {
    require_once RPTAGSDIR.'sp-report-post-tag.php';
    sp_PostIndexReportPostTag($args, $label, $toolTip);
}
