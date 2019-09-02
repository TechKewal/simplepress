<?php
/*
Simple:Press Plugin Title: Print Topic
Version: 2.1.0
Item Id: 3979
Plugin URI: https://simple-press.com
Description: A Simple:Press plugin for printing a hard copy of a complete topic
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2015-01-12 15:25:28 +0000 (Mon, 12 Jan 2015) $
$Rev: 12341 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPPTDBVERSION', 0);

define('SPPRINTDIR',	SPPLUGINDIR.'print-topic/');
define('SPPTTEMPDIR',   SPPLUGINDIR.'print-topic/template-files/');
define('SPPTSCRIPT',    SPPLUGINURL.'print-topic/resources/jscript/');

add_action('init',											'sp_print_topic_localization');
add_action('sph_activate_print-topic/sp-print-plugin.php',	'sp_print_topic_install');
add_action('sph_deactivate_print-topic/sp-print-plugin.php','sp_print_topic_deactivate');
add_action('sph_uninstall_print-topic/sp-print-plugin.php',	'sp_print_topic_uninstall');
add_action('sph_activated',									'sp_print_topic_sp_activate');
add_action('sph_deactivated',								'sp_print_topic_sp_deactivate');
add_action('sph_uninstalled',								'sp_print_topic_sp_uninstall');
add_filter('sph_plugins_active_buttons',					'sp_print_topic_uninstall_option', 10, 2);
add_action('sph_print_plugin_scripts', 						'sp_print_topic_load_js');

add_action('sph_get_query_vars',		'sp_print_topic_get_query_vars');
add_action('sph_get_def_query_vars',	'sp_print_topic_get_def_query_vars');
add_filter('sph_rewrite_rules_start',	'sp_print_topic_rewrite_rules', 10, 3);
add_filter('sph_query_vars',			'sp_print_topic_query_vars');
add_filter('sph_pageview',				'sp_print_topic_pageview');
add_filter('sph_canonical_url',			'sp_print_topic_canonical_url');
add_filter('sph_page_title',			'sp_print_topic_page_title', 10, 2);
add_filter('sph_DefaultViewTemplate',	'sp_print_topic_template_name', 10, 2);
add_filter('sph_PostIndexPrint',		'sp_print_topic_single_post_tag', 10, 2);

# Ajax Handler
add_action('wp_ajax_print',				'sp_print_topic_ajax_options');
add_action('wp_ajax_nopriv_print',		'sp_print_topic_ajax_options');


function sp_print_topic_localization() {
	sp_plugin_localisation('sp-print');
}

function sp_print_topic_install() {
	require_once SPPRINTDIR.'sp-print-install.php';
	sp_print_topic_do_install();
}

function sp_print_topic_uninstall() {
	require_once SPPRINTDIR.'sp-print-uninstall.php';
	sp_print_topic_do_uninstall();
}

function sp_print_topic_deactivate() {
	SP()->auths->deactivate('view_print_topic_activity');
}

function sp_print_topic_sp_activate() {
	require_once SPPRINTDIR.'sp-print-install.php';
	sp_print_topic_do_sp_activate();
}

function sp_print_topic_sp_deactivate() {
	require_once SPPRINTDIR.'sp-print-uninstall.php';
	sp_print_topic_do_sp_deactivate();
}

function sp_print_topic_sp_uninstall() {
	require_once SPPRINTDIR.'sp-print-uninstall.php';
	sp_print_topic_do_sp_uninstall();
}

function sp_print_topic_uninstall_option($actionlink, $plugin) {
	require_once SPPRINTDIR.'sp-print-uninstall.php';
	$actionlink = sp_print_topic_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_print_topic_rewrite_rules($rules, $slugmatch, $slug) {
	$rules[$slugmatch.'/([^/]+)/([^/]+)/topicprint/?$'] = 'index.php?pagename='.$slug.'&sf_forum=$matches[1]&sf_topic=$matches[2]&sf_print=topic'; # match topic print url
    return $rules;
}

function sp_print_topic_query_vars($vars) {
	$vars[] = 'sf_print';
    return $vars;
}

function sp_print_topic_get_query_vars() {
	SP()->rewrites->pageData['topicprint'] = SP()->filters->str(get_query_var('sf_print'));
	if (empty(SP()->rewrites->pageData['topicprint'])) SP()->rewrites->pageData['topicprint'] = 0;
}

function sp_print_topic_get_def_query_vars($stuff) {
    if (isset($stuff[3]) && $stuff[3] == 'topicprint') {
        # get forum slug
		$substuff = explode('&', $stuff[1]);
		SP()->rewrites->pageData['forumslug'] = $substuff[0];

        # get topic slug
		$substuff = explode('&', $stuff[2]);
		SP()->rewrites->pageData['topicslug'] = $substuff[0];

        SP()->rewrites->pageData['topicprint'] = 'topic';
        SP()->rewrites->pageData['plugin-vars'] = true;
    }
	if (empty(SP()->rewrites->pageData['topicprint'])) SP()->rewrites->pageData['topicprint'] = 0;
}

function sp_print_topic_pageview($pageview) {
    if (!empty(SP()->rewrites->pageData['topicprint'])) $pageview = 'topicprint';
    return $pageview;
}

function sp_print_topic_canonical_url($url) {
    if (SP()->rewrites->pageData['pageview'] == 'topicprint') $url = user_trailingslashit(SP()->spPermalinks->get_url().'/'.SP()->rewrites->pageData['forumslug'].'/'.SP()->rewrites->pageData['topicslug'].'/topicprint');
    return $url;
}

function sp_print_topic_page_title($title, $sep) {
	$sfseo = SP()->options->get('sfseo');
	if ($sfseo['sfseo_page'] && SP()->rewrites->pageData['pageview'] == 'topicprint') $title = __('Topic Print', 'sp-print').$sep.$title;
    return $title;
}

function sp_print_topic_template_name($name, $pageview) {
	if ($pageview != 'topicprint') return $name;
	$name = SP()->theme->find_template(SPPTTEMPDIR,'spTopicPrintView.php');
	return $name;
}

function sp_print_topic_ajax_options() {
	require_once SPPRINTDIR.'ajax/sp-print-ajax-options.php';
}

function sp_print_topic_load_js($footer) {
    $script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPPTSCRIPT.'sp-print-topic.js' : SPPTSCRIPT.'sp-print-topic.min.js';
	SP()->plugin->enqueue_script('spprint', $script, array('jquery'), false, $footer);
}

# Template Tags

# ----------------------------------------------
# Add topic print view button on topics
# ----------------------------------------------
function sp_PrintTopicView($args='', $label='', $toolTip='') {
	require_once SPPRINTDIR.'template-tags/sp-print-tags.php';
	return sp_do_PrintTopicView($args, $label, $toolTip);
}

# ----------------------------------------------
# Add topic print button on actual termplate
# ----------------------------------------------
function sp_PrintTopic($args='', $label='', $toolTip='') {
	require_once SPPRINTDIR.'template-tags/sp-print-tags.php';
	return sp_do_PrintTopic($args, $label, $toolTip);
}

# ----------------------------------------------
# Add go back to topic button on termplate
# ----------------------------------------------
function sp_GoBack($args='', $label='', $toolTip='') {
	require_once SPPRINTDIR.'template-tags/sp-print-tags.php';
	return sp_do_GoBack($args, $label, $toolTip);
}

function sp_print_topic_single_post_tag($out, $a) {
	require_once SPPRINTDIR.'template-tags/sp-print-tags.php';
	return sp_do_print_topic_single_post_tag($out, $a);
}
