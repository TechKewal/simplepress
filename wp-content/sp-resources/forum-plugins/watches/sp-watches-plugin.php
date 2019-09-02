<?php
/*
Simple:Press Plugin Title: Watches
Version: 2.1.0
Item Id: 3918
Plugin URI: https://simple-press.com/downloads/watches-plugin/
Description: A Simple:Press plugin for allowing watching of topics
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
A plugin for Simple:Press to allow users to watch topics.
$LastChangedDate: 2016-03-11 18:47:14 -0800 (Fri, 11 Mar 2016) $
$Rev: 14050 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPWATCHESDBVERSION', 6);

define('SPACTIVITY_WATCH', SP()->activity->get_type('watches'));

define('WDIR',		SPPLUGINDIR.'watches/');
define('WADMINDIR', SPPLUGINDIR.'watches/admin/');
define('WLIBDIR',	SPPLUGINDIR.'watches/library/');
define('WAJAXDIR',	SPPLUGINDIR.'watches/ajax/');
define('WSCRIPT',	SPPLUGINURL.'watches/resources/jscript/');
define('WCSS',		SPPLUGINURL.'watches/resources/css/');
define('WTAGSDIR',	SPPLUGINDIR.'watches/template-tags/');
define('WTEMPDIR',	SPPLUGINDIR.'watches/template-files/');
define('WFORMSDIR', SPPLUGINDIR.'watches/forms/');
define('WIMAGES',	SPPLUGINURL.'watches/resources/images/');
define('WIMAGESMOB',SPPLUGINURL.'watches/resources/images/mobile/');

add_action('sph_activate_watches/sp-watches-plugin.php',		'sp_watches_install');
add_action('sph_deactivate_watches/sp-watches-plugin.php',		'sp_watches_deactivate');
add_action('sph_uninstall_watches/sp-watches-plugin.php',		'sp_watches_uninstall');
add_action('init',												'sp_watches_localization');
add_action('sph_print_plugin_scripts',							'sp_watches_load_js');
add_action('sph_print_plugin_styles',							'sp_watches_header');
add_action('sph_scripts_admin_end',								'sp_watches_load_admin_js');
add_action('sph_admin_menu',									'sp_watches_menu');
add_action('sph_setup_forum',									'sp_watches_process_actions');
add_action('sph_topic_delete',									'sp_watches_topic_delete');
add_action('sph_member_deleted',								'sp_watches_member_del');
add_action('sph_new_forum_post',								'sp_watches_new_forum_post', 1);
add_action('sph_quickreply_form',								'sp_watches_admin_bar', 10, 3);
add_action('sph_quick_reply',									'sp_watches_quick_reply');
add_action('sph_toolbox_housekeeping_profile_tabs',				'sp_watches_reset_profile_tabs');
add_action('admin_footer',										'sp_watches_upgrade_check');
add_action('sph_plugin_update_watches/sp-watches-plugin.php',	'sp_watches_upgrade_check');
add_action('sph_permissions_reset',								'sp_watches_reset_permissions');
add_action('sph_get_query_vars',								'sp_watches_get_query_vars');
add_action('sph_get_def_query_vars',							'sp_watches_get_def_query_vars');
add_action('sph_user_class_member',								'sp_watches_add_to_user_class');

add_filter('sph_perms_tooltips',					'sp_watches_tooltips', 10, 2);
add_filter('sph_admin_help-admin-users',			'sp_watches_admin_help', 10, 3);
add_filter('sph_plugins_active_buttons',			'sp_watches_uninstall_option', 10, 2);
add_filter('sph_post_message',						'sp_watches_post_create', 10, 2);
add_filter('sph_TopicIndexStatusIconsLast',			'sp_watches_forum_status', 10, 2);
add_filter('sph_post_options_add',					'sp_watches_post_form_options', 10, 2);
add_filter('sph_topic_options_add',					'sp_watches_topic_form_options', 10, 2);
add_filter('sph_forumview_combined_data',			'sp_watches_records_forumview', 10, 2);
add_filter('sph_topicview_topic_record',			'sp_watches_records', 10, 2);
add_filter('sph_add_post_tool',						'sp_watches_post_tool', 10, 10);
add_filter('sph_add_topic_tool',					'sp_watches_topic_tool', 10, 5);
add_filter('sph_post_editor_footer_bottom',			'sp_watches_post_footer', 10, 3);
add_filter('sph_post_editor_display_options',		'sp_watches_editor_options');
add_filter('sph_topic_editor_display_options',		'sp_watches_editor_options');
add_filter('sph_load_admin_textdomain',				'sp_watches_load_admin');
add_filter('sph_ProfileFormSave_manage-watches',	'sp_watches_profile_update', 10, 3);
add_filter('sph_rewrite_rules_start',				'sp_watches_rewrite_rules', 10, 3);
add_filter('sph_query_vars',						'sp_watches_query_vars');
add_filter('sph_pageview',							'sp_watches_pageview');
add_filter('sph_canonical_url',						'sp_watches_canonical_url');
add_filter('sph_page_title',						'sp_watches_page_title', 10, 2);
add_filter('sph_BreadCrumbs',						'sp_watches_breadcrumb', 10, 5);
add_filter('sph_BreadCrumbsMobile',					'sp_watches_breadcrumbMobile', 10, 2);
add_filter('sph_DefaultViewTemplate',				'sp_watches_template_name', 10, 2);

# Ajax Calls

add_action('wp_ajax_watches-manage', 'sp_watches_ajax_manage');
add_action('wp_ajax_nopriv_watches-manage', 'sp_watches_ajax_manage');

add_action('wp_ajax_watches-topics', 'sp_watches_ajax_topics');
add_action('wp_ajax_nopriv_watches-topics', 'sp_watches_ajax_topics');

add_action('wp_ajax_watches-users', 'sp_watches_ajax_users');
add_action('wp_ajax_nopriv_watches-users', 'sp_watches_ajax_users');


function sp_watches_menu() {
		$subpanels = array(
				__('Watches (by Topic)', 'sp-watches') => array('admin' => 'sp_watches_admin_topics', 'save' => '', 'form' => 0, 'id' => 'watchtopic'),
				__('Watches (by User)', 'sp-watches') => array('admin' => 'sp_watches_admin_users', 'save' => '', 'form' => 0, 'id' => 'watchuser')
		);
		SP()->plugin->add_admin_subpanel('users', $subpanels);
}

function sp_watches_admin_topics() {
	require_once WADMINDIR.'sp-watches-admin-topics.php';
	sp_watches_admin_topics_form();
}

function sp_watches_admin_users() {
	require_once WADMINDIR.'sp-watches-admin-users.php';
	sp_watches_admin_users_form();
}

function sp_watches_admin_help($file, $tag, $lang) {
	if ($tag == '[topic-watches]' || $tag == '[watches-topics]' || $tag == '[watches-users]') $file = WADMINDIR.'sp-watches-admin-help.'.$lang;
	return $file;
}

function sp_watches_uninstall_option($actionlink, $plugin) {
	require_once WLIBDIR.'sp-watches-components.php';
	$actionlink = sp_watches_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_watches_localization() {
	sp_plugin_localisation('sp-watches');
}

function sp_watches_tooltips($tips, $t) {
	$tips['watch'] = $t.__('Can set a watch on any topic in the forum', 'sp-watches');
	return $tips;
}

function sp_watches_uninstall() {
	require_once WDIR.'sp-watches-uninstall.php';
	sp_watches_do_uninstall();
}

function sp_watches_install() {
	require_once WDIR.'sp-watches-install.php';
	sp_watches_do_install();
}

function sp_watches_deactivate() {
	require_once WLIBDIR.'sp-watches-components.php';
	sp_watches_do_deactivate();
}

function sp_watches_reset_permissions() {
	require_once WDIR.'sp-watches-install.php';
	sp_watches_do_reset_permissions();
}

function sp_watches_ajax_manage() {
	require_once WLIBDIR.'sp-watches-components.php';
	require_once WAJAXDIR.'sp-watches-ajax-manage.php';
}

function sp_watches_ajax_topics() {
	require_once WAJAXDIR.'sp-watches-ajax-topics.php';
}

function sp_watches_ajax_users() {
	require_once WAJAXDIR.'sp-watches-ajax-users.php';
}

function sp_watches_load_admin_js() {
	$script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? WSCRIPT.'sp-watches-admin.js' : WSCRIPT.'sp-watches-admin.min.js';
	wp_enqueue_script('sp-watches-admin', $script, false, false, false);
}

function sp_watches_header() {
	require_once WLIBDIR.'sp-watches-components.php';
	sp_watches_do_header();
}

function sp_watches_load_js($footer) {
	require_once WLIBDIR.'sp-watches-components.php';
	sp_watches_do_load_js($footer);
}

function sp_watches_process_actions() {
	require_once WLIBDIR.'sp-watches-components.php';
	sp_watches_do_process_actions();
}

function sp_watches_topic_delete($posts) {
	require_once WLIBDIR.'sp-watches-components.php';
	sp_watches_do_topic_delete($posts);
}

function sp_watches_member_del($userid) {
	require_once WLIBDIR.'sp-watches-database.php';
	sp_watches_do_member_del($userid);
}

function sp_watches_post_create($msg, $newpost) {
	require_once WLIBDIR.'sp-watches-components.php';
	$msg = sp_watches_do_post_create($msg, $newpost);
	return $msg;
}

function sp_watches_new_forum_post($newpost) {
	if (isset($_POST['topicwatch'])) $newpost['topicwatch'] = SP()->filters->str($_POST['topicwatch']);
	if (isset($_POST['topicwatchend'])) $newpost['topicwatchend'] = SP()->filters->str($_POST['topicwatchend']);
	return $newpost;
}

function sp_watches_forum_status($content) {
	require_once WLIBDIR.'sp-watches-components.php';
	$content = sp_watches_do_forum_status($content);
	return $content;
}

function sp_watches_topic_form_options($content, $thisForum) {
	require_once WLIBDIR.'sp-watches-components.php';
	$content = sp_watches_do_topic_form_options($content, $thisForum);
	return $content;
}

function sp_watches_post_form_options($content, $thisTopic) {
	require_once WLIBDIR.'sp-watches-components.php';
	$content = sp_watches_do_post_form_options($content, $thisTopic);
	return $content;
}

function sp_watches_records($data, $record) {
    $data->topic_watches = SP()->activity->get_col('col=uid&type='.SPACTIVITY_WATCH."&item=$data->topic_id");
	return $data;
}

function sp_watches_records_forumview($fData, $topics) {
	require_once WLIBDIR.'sp-watches-components.php';
	return sp_watches_do_records_forumview($fData, $topics);
}

function sp_watches_admin_bar($newtopic, $topic, $post) {
	require_once WLIBDIR.'sp-watches-components.php';
	sp_watches_do_admin_bar($newtopic, $topic, $post);
}

function sp_watches_quick_reply($newpost) {
	require_once WLIBDIR.'sp-watches-database.php';
	require_once WLIBDIR.'sp-watches-components.php';
	sp_watches_do_quick_reply($newpost);
}

function sp_watches_topic_tool($out, $topic, $forum, $page, $br) {
	require_once WLIBDIR.'sp-watches-components.php';
	$out = sp_watches_forum_tool($out, $forum, $topic, $br);
	return $out;
}

function sp_watches_post_tool($out, $post, $forum, $topic, $page, $postnum, $useremail, $guestemail, $displayname, $br) {
	require_once WLIBDIR.'sp-watches-components.php';
	$out = sp_watches_forum_tool($out, $forum, $topic, $br);
	return $out;
}

function sp_watches_post_footer($out, $topic, $a) {
	require_once WLIBDIR.'sp-watches-components.php';
	$out = sp_watches_do_post_footer($out, $topic, $a);
	return $out;
}

function sp_watches_editor_options($display) {
	if (SP()->rewrites->pageData['displaymode'] != 'edit' && SP()->auths->get('watch')) $display['options'] = true;
	return $display;
}

function sp_watches_load_admin($special) {
	$special[] = 'action=watches-topics&';
	return $special;
}

function sp_watches_reset_profile_tabs() {
	require_once WLIBDIR.'sp-watches-components.php';
	sp_watches_do_reset_profile_tabs();
}

function sp_watches_upgrade_check() {
	require_once WDIR.'sp-watches-upgrade.php';
	sp_watches_do_upgrade_check();
}

function sp_watches_profile_update($message, $thisUser, $thisForm) {
	require_once WLIBDIR.'sp-watches-database.php';
	require_once WLIBDIR.'sp-watches-components.php';
	$message = sp_watches_do_profile_update($message, $thisUser, $thisForm);
	return $message;
}

function sp_watches_rewrite_rules($rules, $slugmatch, $slug) {
	$rules[$slugmatch.'/watches/?$'] = 'index.php?pagename='.$slug.'&sf_watches=view';
	return $rules;
}

function sp_watches_query_vars($vars) {
	$vars[] = 'sf_watches';
	return $vars;
}

function sp_watches_get_query_vars() {
	SP()->rewrites->pageData['watches'] = SP()->filters->str(get_query_var('sf_watches'));
	if (empty(SP()->rewrites->pageData['watches'])) SP()->rewrites->pageData['watches'] = 0;
}

function sp_watches_get_def_query_vars($stuff) {
	if ($stuff[1] == 'watches') {
		SP()->rewrites->pageData['watches'] = true;
		SP()->rewrites->pageData['plugin-vars'] = true;
	}
	if (empty(SP()->rewrites->pageData['watches'])) SP()->rewrites->pageData['watches'] = 0;
}

function sp_watches_pageview($pageview) {
	if (!empty(SP()->rewrites->pageData['watches'])) $pageview = 'watches';
	return $pageview;
}

function sp_watches_canonical_url($url) {
	if (SP()->rewrites->pageData['pageview'] == 'watches') $url = SP()->spPermalinks->get_url("watches");
	return $url;
}

function sp_watches_breadcrumb($breadCrumbs, $args, $crumbEnd, $crumbSpace, $treeCount) {
	if (!empty(SP()->rewrites->pageData['watches'])) {
		extract($args, EXTR_SKIP);
		if (!empty($icon)) {
			$icon = SP()->theme->paint_icon($iconClass, SPTHEMEICONSURL, sanitize_file_name($icon));
		} else {
			if (!empty($iconText)) $icon = SP()->saveFilters->kses($iconText);
		}
		$breadCrumbs.= $crumbEnd.str_repeat($crumbSpace, $treeCount).$icon."<a class='$linkClass' href='".SP()->spPermalinks->get_url('watches')."'>".__('Watches', 'sp-watches').'</a>';
	}
	return $breadCrumbs;
}

function sp_watches_breadcrumbMobile($breadCrumbs, $args) {
	if (!empty(SP()->rewrites->pageData['watches'])) {
		extract($args, EXTR_SKIP);
		$breadCrumbs.= "<a class='$tagClass' href='".SP()->spPermalinks->get_url('watches')."'>".__('Watches', 'sp-watches').'</a>';
	}
	return $breadCrumbs;
}

function sp_watches_page_title($title, $sep) {
	$sfseo = SP()->options->get('sfseo');
	if ($sfseo['sfseo_page'] && SP()->rewrites->pageData['pageview'] == 'watches') $title = __('Watches', 'sp-watches').$sep.$title;
	return $title;
}

function sp_watches_template_name($name, $pageview) {
	if ($pageview != 'watches') return $name;
	$tempName = SP()->theme->find_template(WTEMPDIR,'spWatchesView.php');
	return $tempName;
}

function sp_watches_add_to_user_class(&$user) {
    $user->watches = SP()->activity->get_col('col=item&type='.SPACTIVITY_WATCH."&uid=$user->ID");
}

# Define Template Tags globally available (dont have to be enabled on template tag panel)

function sp_WatchesReviewButton($args='', $label='', $toolTip='') {
	require_once WTAGSDIR.'sp-watches-review-button-tag.php';
	sp_WatchesReviewButtonTag($args, $label, $toolTip);
}

function sp_WatchesWatchButton($args='', $watchLabel='', $stopWatchLabel='', $watchToolTip='', $stopWatchToolTip='') {
	require_once WTAGSDIR.'sp-watches-watch-button-tag.php';
	sp_WatchesWatchButtonTag($args, $watchLabel, $stopWatchLabel, $watchToolTip, $stopWatchToolTip);
}

function sp_WatchesUnreadTopics($display=true) {
	require_once WTAGSDIR.'sp-watches-topics-tag.php';
	sp_WatchesUnreadTopicsTag($display);
}
