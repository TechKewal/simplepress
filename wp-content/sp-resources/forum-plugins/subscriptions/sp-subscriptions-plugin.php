<?php
/*
Simple:Press Plugin Title: Subscriptions 
Version: 2.1.0
Item Id: 3908
Plugin URI: https://simple-press.com/downloads/subscriptions-plugin/
Description: A Simple:Press plugin for allowing end users to subscribe to topics and get notifications via email
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
A Simple:Press plugin for allowing end users to subscribe to topics and get notifications via email.
$LastChangedDate: 2019-01-16 07:57:46 -0500 (Wed, 16 Jan 2019) $
$Rev: 15707 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPSUBSDBVERSION', 12);

define('SPACTIVITY_SUBSTOPIC', SP()->activity->get_type('topic subscriptions'));
define('SPACTIVITY_SUBSFORUM', SP()->activity->get_type('forum subscriptions'));

define('SPDIGEST',		SP_PREFIX.'sfdigest');

define('SDIR',			SPPLUGINDIR.'subscriptions/');
define('SADMINDIR',		SPPLUGINDIR.'subscriptions/admin/');
define('SLIBDIR',		SPPLUGINDIR.'subscriptions/library/');
define('SAJAXDIR',		SPPLUGINDIR.'subscriptions/ajax/');
define('SSCRIPT',		SPPLUGINURL.'subscriptions/resources/jscript/');
define('SCSS',			SPPLUGINURL.'subscriptions/resources/css/');
define('STAGSDIR',		SPPLUGINDIR.'subscriptions/template-tags/');
define('STEMPDIR',		SPPLUGINDIR.'subscriptions/template-files/');
define('SFORMSDIR',		SPPLUGINDIR.'subscriptions/forms/');
define('SIMAGES',		SPPLUGINURL.'subscriptions/resources/images/');
define('SIMAGESMOB',	SPPLUGINURL.'subscriptions/resources/images/mobile/');

add_action('sph_activate_subscriptions/sp-subscriptions-plugin.php',		'sp_subscriptions_install');
add_action('sph_deactivate_subscriptions/sp-subscriptions-plugin.php',		'sp_subscriptions_deactivate');
add_action('sph_uninstall_subscriptions/sp-subscriptions-plugin.php',		'sp_subscriptions_uninstall');
add_action('init',															'sp_subscriptions_localization');
add_action('sph_print_plugin_scripts',										'sp_subscriptions_load_js');
add_action('sph_member_created',											'sp_subscriptions_member_add');
add_action('sph_member_deleted',											'sp_subscriptions_member_del');
add_action('sph_user_class_member',						        			'sp_subscriptions_add_to_user_class');
add_action('sph_forum_forum_del',											'sp_subscriptions_forum_delete');
add_action('sph_topic_delete',												'sp_subscriptions_topic_delete');
add_action('sph_post_delete',												'sp_subscriptions_post_delete');
add_action('sph_scripts_admin_end',											'sp_subscriptions_load_admin_js');
add_action('sph_admin_menu',												'sp_subscriptions_menu');
add_action('sph_setup_forum',												'sp_subscriptions_process_actions');
add_action('sph_new_forum_post',											'sp_subscriptions_new_forum_post', 1);
add_action('sph_print_plugin_styles',										'sp_subscriptions_header');
add_action('sph_toolbox_housekeeping_profile_tabs',							'sp_subscriptions_reset_profile_tabs');
add_action('cron_schedules',												'sp_subscriptions_cron_schedule');
add_action('sph_activated',													'sp_subscriptions_sp_activate');
add_action('sph_deactivated',												'sp_subscriptions_sp_deactivate');
add_action('sph_uninstalled',												'sp_subscriptions_sp_uninstall');
add_action('sph_subs_digest_cron',											'sp_subscriptions_cron_digest');
add_action('sph_post_create',												'sp_subscriptions_digest_entry');
add_action('admin_footer',													'sp_subscriptions_upgrade_check');
add_action('sph_plugin_update_subscriptions/sp-subscriptions-plugin.php',	'sp_subscriptions_upgrade_check');
add_action('sph_post_approved',												'sp_subscriptions_post_approved');
add_action('sph_permissions_reset',											'sp_subscriptions_reset_permissions');
add_action('sph_get_query_vars',											'sp_subscriptions_get_query_vars');
add_action('sph_get_def_query_vars',										'sp_subscriptions_get_def_query_vars');
add_action('sph_move_topic',												'sp_subscriptions_topic_moved', 10, 3);
add_action('sph_move_post',													'sp_subscriptions_post_moved', 10, 5);
add_action('sph_stats_scheduler',											'sp_subscriptions_scheduler');

add_filter('sph_admin_help-admin-components',			'sp_subscriptions_admin_help', 10, 3);
add_filter('sph_admin_help-admin-users',				'sp_subscriptions_admin_help', 10, 3);
add_filter('sph_plugins_active_buttons',				'sp_subscriptions_uninstall_option', 10, 2);
add_filter('sph_perms_tooltips',						'sp_subscriptions_tooltips', 10, 2);
add_filter('sph_post_message',							'sp_subscriptions_post_create', 10, 2);
add_filter('sph_new_post_notifications',				'sp_subscriptions_post_notification', 10, 2);
add_filter('sph_topic_options_add',						'sp_subscriptions_topic_form_options', 10, 2);
add_filter('sph_post_options_add',						'sp_subscriptions_post_form_options', 10, 2);
add_filter('sph_forumview_combined_data',				'sp_subscriptions_records_forumview', 10, 2);
add_filter('sph_topicview_topic_record',				'sp_subscriptions_records', 10, 2);
add_filter('sph_TopicIndexStatusIconsLast',				'sp_subscriptions_forum_status');
add_filter('sph_post_editor_footer_bottom',				'sp_subscriptions_post_footer', 10, 3);
add_filter('sph_post_editor_display_options',			'sp_subscriptions_editor_options');
add_filter('sph_topic_editor_display_options',			'sp_subscriptions_editor_options');
add_filter('sph_load_admin_textdomain',					'sp_subscriptions_load_admin');
add_filter('sph_ProfileFormSave_topic-subscriptions',	'sp_subscriptions_profile_topics', 10, 3);
add_filter('sph_ProfileFormSave_forum-subscriptions',	'sp_subscriptions_profile_forums', 10, 3);
add_filter('sph_ProfileFormSave_subscription-options',	'sp_subscriptions_profile_options', 10, 3);
add_filter('sph_ForumIndexStatusIconsLast',				'sp_subscriptions_group_status', 10, 2);
add_filter('sph_SubForumIndexStatusIconsLast',			'sp_subscriptions_subforum_status');
add_filter('sph_rewrite_rules_start',					'sp_subscriptions_rewrite_rules', 10, 3);
add_filter('sph_query_vars',							'sp_subscriptions_query_vars');
add_filter('sph_pageview',								'sp_subscriptions_pageview');
add_filter('sph_canonical_url',							'sp_subscriptions_canonical_url');
add_filter('sph_page_title',							'sp_subscriptions_page_title', 10, 2);
add_filter('sph_BreadCrumbs',							'sp_subscriptions_breadcrumb', 10, 5);
add_filter('sph_BreadCrumbsMobile',						'sp_subscriptions_breadcrumbMobile', 10, 2);
add_filter('sph_DefaultViewTemplate',					'sp_subscriptions_template_name', 10, 2);

# Ajax Handlers
add_action('wp_ajax_subs-manage',			'sp_subscriptions_ajax_manage');
add_action('wp_ajax_nopriv_subs-manage',	'sp_subscriptions_ajax_manage');
add_action('wp_ajax_subs-forums',			'sp_subscriptions_ajax_forums');
add_action('wp_ajax_nopriv_subs-forums',	'sp_subscriptions_ajax_forums');
add_action('wp_ajax_subs-topics',			'sp_subscriptions_ajax_topics');
add_action('wp_ajax_nopriv_subs-topics',	'sp_subscriptions_ajax_topics');
add_action('wp_ajax_subs-users',			'sp_subscriptions_ajax_users');
add_action('wp_ajax_nopriv_subs-users',		'sp_subscriptions_ajax_users');
add_action('wp_ajax_subs-digest',			'sp_subscriptions_ajax_digest');
add_action('wp_ajax_nopriv_subs-digest',	'sp_subscriptions_ajax_digest');


function sp_subscriptions_menu() {
		$subpanels = array(
				__('Subscriptions (by Forum)', 'sp-subs') => array('admin' => 'sp_subscriptions_admin_forums', 'save' => '', 'form' => 0, 'id' => 'subsforum'),
				__('Subscriptions (by Topic)', 'sp-subs') => array('admin' => 'sp_subscriptions_admin_topics', 'save' => '', 'form' => 0, 'id' => 'substopic'),
				__('Subscriptions (by User)', 'sp-subs') => array('admin' => 'sp_subscriptions_admin_users', 'save' => '', 'form' => 0, 'id' => 'subsuser'),
				__('Subscriptions (by Digest)', 'sp-subs') => array('admin' => 'sp_subscriptions_admin_digest', 'save' => '', 'form' => 0, 'id' => 'subsdigest')
				);
		SP()->plugin->add_admin_subpanel('users', $subpanels);

		$subpanels = array(
				__('Subscriptions', 'sp-subs') => array('admin' => 'sp_subscriptions_admin_members', 'save' => 'sp_subscriptions_admin_save_members', 'form' => 1, 'id' => 'subsoptions')
				);
		SP()->plugin->add_admin_subpanel('components', $subpanels);
}

function sp_subscriptions_admin_members() {
	require_once SADMINDIR.'sp-subscriptions-admin-members.php';
	sp_subscriptions_admin_members_form();
}

function sp_subscriptions_admin_save_members() {
	require_once SADMINDIR.'sp-subscriptions-admin-members-save.php';
	return sp_subscriptions_admin_members_save();
}

function sp_subscriptions_admin_forums() {
	require_once SADMINDIR.'sp-subscriptions-admin-forums.php';
	sp_subscriptions_admin_forums_form();
}

function sp_subscriptions_admin_topics() {
	require_once SADMINDIR.'sp-subscriptions-admin-topics.php';
	sp_subscriptions_admin_topics_form();
}

function sp_subscriptions_admin_users() {
	require_once SADMINDIR.'sp-subscriptions-admin-users.php';
	sp_subscriptions_admin_users_form();
}

function sp_subscriptions_admin_digest() {
	require_once SADMINDIR.'sp-subscriptions-admin-digest.php';
	sp_subscriptions_admin_digest_form();
}

function sp_subscriptions_admin_help($file, $tag, $lang) {
	if ($tag == '[subscriptions-forums]' || $tag == '[subscriptions-topics]' || $tag == '[subscriptions-users]' || $tag == '[subscriptions-options]' || $tag == '[subscriptions-digest]') $file = SADMINDIR.'sp-subscriptions-admin-help.'.$lang;
	return $file;
}

function sp_subscriptions_header() {
	require_once SLIBDIR.'sp-subscriptions-components.php';
	sp_subscriptions_do_header();
}

function sp_subscriptions_uninstall_option($actionlink, $plugin) {
	require_once SLIBDIR.'sp-subscriptions-components.php';
	$actionlink = sp_subscriptions_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_subscriptions_localization() {
	sp_plugin_localisation('sp-subs');
}

function sp_subscriptions_tooltips($tips, $t) {
	$tips['subscribe'] = $t.__('Can subscribe to any topic in the forum', 'sp-subs');
	return $tips;
}

function sp_subscriptions_uninstall() {
	require_once SDIR.'sp-subscriptions-uninstall.php';
	sp_subscriptions_do_uninstall();
}

function sp_subscriptions_install() {
	require_once SDIR.'sp-subscriptions-install.php';
	sp_subscriptions_do_install();
}

function sp_subscriptions_deactivate() {
	require_once SDIR.'sp-subscriptions-uninstall.php';
	sp_subscriptions_do_deactivate();
}

function sp_subscriptions_reset_permissions() {
	require_once SDIR.'sp-subscriptions-install.php';
	sp_subscriptions_do_reset_permissions();
}

function sp_subscriptions_load_admin_js($footer) {
	require_once SLIBDIR.'sp-subscriptions-components.php';
	sp_subscriptions_do_load_admin_js($footer);
}

function sp_subscriptions_load_js($footer) {
	require_once SLIBDIR.'sp-subscriptions-components.php';
	sp_subscriptions_do_load_js($footer);
}

function sp_subscriptions_ajax_manage() {
	require_once SLIBDIR.'sp-subscriptions-components.php';
	require_once SAJAXDIR.'sp-subscriptions-ajax-manage.php';
}

function sp_subscriptions_ajax_forums() {
	require_once SAJAXDIR.'sp-subscriptions-ajax-forums.php';
}

function sp_subscriptions_ajax_topics() {
	require_once SAJAXDIR.'sp-subscriptions-ajax-topics.php';
}

function sp_subscriptions_ajax_users() {
	require_once SAJAXDIR.'sp-subscriptions-ajax-users.php';
}

function sp_subscriptions_ajax_digest() {
	require_once SAJAXDIR.'sp-subscriptions-ajax-digest.php';
}

function sp_subscriptions_member_add($userid) {
	require_once SLIBDIR.'sp-subscriptions-database.php';
	sp_subscriptions_do_member_add($userid);
}

function sp_subscriptions_member_del($userid) {
	require_once SLIBDIR.'sp-subscriptions-database.php';
	sp_subscriptions_do_member_del($userid);
}

function sp_subscriptions_add_to_user_class(&$user) {
    $user->subscribe = SP()->activity->get_col('col=item&type='.SPACTIVITY_SUBSTOPIC."&uid=$user->ID");
    $user->forum_subscribe = SP()->activity->get_col('col=item&type='.SPACTIVITY_SUBSFORUM."&uid=$user->ID");
}

function sp_subscriptions_forum_delete($forum) {
	require_once SLIBDIR.'sp-subscriptions-database.php';
	sp_subscriptions_do_forum_delete($forum);
}

function sp_subscriptions_topic_delete($posts) {
	require_once SLIBDIR.'sp-subscriptions-database.php';
	sp_subscriptions_do_topic_delete($posts);
}

function sp_subscriptions_topic_moved($currenttopicid, $currentforumid, $targetforumid) {
	require_once SLIBDIR.'sp-subscriptions-database.php';
	sp_subscriptions_do_topic_moved($currenttopicid, $currentforumid, $targetforumid);
}

function sp_subscriptions_post_moved($oldtopicid, $newtopicid, $newforumid, $oldforumid, $postid) {
	require_once SLIBDIR.'sp-subscriptions-database.php';
	sp_subscriptions_do_post_moved($oldtopicid, $newtopicid, $newforumid, $oldforumid, $postid);
}

function sp_subscriptions_post_delete($post) {
	require_once SLIBDIR.'sp-subscriptions-database.php';
	sp_subscriptions_do_post_delete($post);
}

function sp_subscriptions_process_actions() {
	require_once SLIBDIR.'sp-subscriptions-components.php';
	sp_subscriptions_do_process_actions();
}

function sp_subscriptions_post_create($msg, $newpost) {
	require_once SLIBDIR.'sp-subscriptions-components.php';
	$msg = sp_subscriptions_do_post_create($msg, $newpost);
	return $msg;
}

function sp_subscriptions_post_notification($retmsg, $newpost) {
	require_once SLIBDIR.'sp-subscriptions-components.php';
	$msg = sp_subscriptions_do_post_notification($retmsg, $newpost);
	return $msg;
}

function sp_subscriptions_new_forum_post($newpost) {
	if (isset($_POST['topicsub'])) $newpost['topicsub'] = SP()->filters->str($_POST['topicsub']);
	if (isset($_POST['topicsubsend'])) $newpost['topicsubend'] = SP()->filters->str($_POST['topicsubend']);
	return $newpost;
}

function sp_subscriptions_topic_form_options($content, $thisForum) {
	require_once SLIBDIR.'sp-subscriptions-components.php';
	$content = sp_subscriptions_do_topic_form_options($content, $thisForum);
	return $content;
}

function sp_subscriptions_post_form_options($content, $thisTopic) {
	require_once SLIBDIR.'sp-subscriptions-components.php';
	$content = sp_subscriptions_do_post_form_options($content, $thisTopic);
	return $content;
}

function sp_subscriptions_records_forumview($fData, $topics) {
	require_once SLIBDIR.'sp-subscriptions-components.php';
	return sp_subscriptions_do_records_forumview($fData, $topics);
}

function sp_subscriptions_records($data, $record) {
    $data->subscriptions = SP()->activity->count('type='.SPACTIVITY_SUBSTOPIC."&item=$data->topic_id");
	return $data;
}

function sp_subscriptions_forum_status($content) {
	require_once SLIBDIR.'sp-subscriptions-components.php';
	$content = sp_subscriptions_do_forum_status($content);
	return $content;
}

function sp_subscriptions_post_footer($out, $topic, $a) {
	require_once SLIBDIR.'sp-subscriptions-components.php';
	$out = sp_subscriptions_do_post_footer($out, $topic, $a);
	return $out;
}

function sp_subscriptions_editor_options($display) {
	if (SP()->rewrites->pageData['displaymode'] != 'edit' && SP()->auths->get('subscribe')) $display['options'] = true;
	return $display;
}

function sp_subscriptions_load_admin($special) {
	$special[] = 'action=subs-topics&';
	return $special;
}

function sp_subscriptions_reset_profile_tabs() {
	require_once SLIBDIR.'sp-subscriptions-components.php';
	sp_subscriptions_do_reset_profile_tabs();
}

function sp_subscriptions_upgrade_check() {
	require_once SDIR.'sp-subscriptions-upgrade.php';
	sp_subscriptions_do_upgrade_check();
}

function sp_subscriptions_profile_forums($message, $thisUser, $thisForm) {
	require_once SLIBDIR.'sp-subscriptions-database.php';
	require_once SLIBDIR.'sp-subscriptions-components.php';
	$message = sp_subscriptions_do_profile_forums($message, $thisUser, $thisForm);
	return $message;
}

function sp_subscriptions_profile_topics($message, $thisUser, $thisForm) {
	require_once SLIBDIR.'sp-subscriptions-database.php';
	require_once SLIBDIR.'sp-subscriptions-components.php';
	$message = sp_subscriptions_do_profile_topics($message, $thisUser, $thisForm);
	return $message;
}

function sp_subscriptions_profile_options($message, $thisUser, $thisForm) {
	require_once SLIBDIR.'sp-subscriptions-components.php';
	$message = sp_subscriptions_do_profile_options($message, $thisUser, $thisForm);
	return $message;
}

function sp_subscriptions_cron_schedule($schedules) {
	$subs = SP()->options->get('subscriptions');
	$interval = ($subs['digesttype'] == 1) ? (60*60*24) : (60*60*24*7);
	$schedules['sp_subs_digest_interval'] = array('interval' => $interval, 'display' => __('SP Subscription Digest Interval', 'sp-subs'));
	return $schedules;
}

function sp_subscriptions_sp_activate() {
	require_once SDIR.'sp-subscriptions-install.php';
	sp_subscriptions_do_sp_activate();
}

function sp_subscriptions_sp_deactivate() {
	require_once SDIR.'sp-subscriptions-uninstall.php';
	sp_subscriptions_do_sp_deactivate();
}

function sp_subscriptions_sp_uninstall() {
	require_once SDIR.'sp-subscriptions-uninstall.php';
	sp_subscriptions_do_sp_uninstall();
}

function sp_subscriptions_cron_digest() {
	require_once SLIBDIR.'sp-subscriptions-digest.php';
	sp_subscriptions_do_cron_digest();
}

function sp_subscriptions_digest_entry($newpost) {
	require_once SLIBDIR.'sp-subscriptions-digest.php';
	sp_subscriptions_do_digest_entry($newpost);
}

function sp_subscriptions_post_approved($posts) {
	require_once SLIBDIR.'sp-subscriptions-components.php';
	sp_subscriptions_do_post_approved($posts);
}

function sp_subscriptions_group_status($out, $a) {
	require_once SLIBDIR.'sp-subscriptions-components.php';
	$out = sp_subscriptions_do_group_status($out, $a);
	return $out;
}

function sp_subscriptions_subforum_status($out) {
	require_once SLIBDIR.'sp-subscriptions-components.php';
	$out = sp_subscriptions_do_subforum_status($out);
	return $out;
}

function sp_subscriptions_rewrite_rules($rules, $slugmatch, $slug) {
	$rules[$slugmatch.'/subscriptions/?$'] = 'index.php?pagename='.$slug.'&sf_subscriptions=view';
	return $rules;
}

function sp_subscriptions_query_vars($vars) {
	$vars[] = 'sf_subscriptions';
	return $vars;
}

function sp_subscriptions_get_query_vars() {
	SP()->rewrites->pageData['subscriptions'] = SP()->filters->str(get_query_var('sf_subscriptions'));
	if (empty(SP()->rewrites->pageData['subscriptions'])) SP()->rewrites->pageData['subscriptions'] = 0;
}

function sp_subscriptions_get_def_query_vars($stuff) {
	if ($stuff[1] == 'subscriptions') {
		SP()->rewrites->pageData['subscriptions'] = true;
		SP()->rewrites->pageData['plugin-vars'] = true;
	}
	if (empty(SP()->rewrites->pageData['subscriptions'])) SP()->rewrites->pageData['subscriptions'] = 0;
}

function sp_subscriptions_pageview($pageview) {
	if (!empty(SP()->rewrites->pageData['subscriptions'])) $pageview = 'subscriptions';
	return $pageview;
}

function sp_subscriptions_canonical_url($url) {
	if (SP()->rewrites->pageData['pageview'] == 'subscriptions') $url = SP()->spPermalinks->get_url("subscriptions");
	return $url;
}

function sp_subscriptions_breadcrumb($breadCrumbs, $args, $crumbEnd, $crumbSpace, $treeCount) {
	if (!empty(SP()->rewrites->pageData['subscriptions'])) {
		extract($args, EXTR_SKIP);
		if (!empty($icon)) {
			$icon = SP()->theme->paint_icon($iconClass, SPTHEMEICONSURL, sanitize_file_name($icon));
		} else {
			if (!empty($iconText)) $icon = SP()->saveFilters->kses($iconText);
		}
		$breadCrumbs.= $crumbEnd.str_repeat($crumbSpace, $treeCount).$icon."<a class='$linkClass' href='".SP()->spPermalinks->get_url('subscriptions')."'>".__('Subscriptions', 'sp-subs').'</a>';
	}
	return $breadCrumbs;
}

function sp_subscriptions_breadcrumbMobile($breadCrumbs, $args) {
	if (!empty(SP()->rewrites->pageData['subscriptions'])) {
		extract($args, EXTR_SKIP);
		$breadCrumbs.= "<a class='$tagClass' href='".SP()->spPermalinks->get_url('subscriptions')."'>".__('Subscriptions', 'sp-subs').'</a>';
	}
	return $breadCrumbs;
}

function sp_subscriptions_page_title($title, $sep) {
	$sfseo = SP()->options->get('sfseo');
	if ($sfseo['sfseo_page'] && SP()->rewrites->pageData['pageview'] == 'subscriptions') $title = __('Subscriptions', 'sp-subs').$sep.$title;
	return $title;
}

function sp_subscriptions_template_name($name, $pageview) {
	if ($pageview != 'subscriptions') return $name;
	$tempName = SP()->theme->find_template(STEMPDIR,'spSubscriptionsView.php');
	return $tempName;
}

function sp_subscriptions_scheduler() {
	$subs = SP()->options->get('subscriptions');
	if ($subs['digestsub'] && !wp_next_scheduled('sph_subs_digest_cron')) {
		wp_schedule_event(time(), 'sp_subs_digest_interval', 'sph_subs_digest_cron');
	}
}

# Define Template Tags globally available (dont have to be enabled on template tag panel)

function sp_SubscriptionsReviewButton($args='', $label='', $toolTip='') {
	require_once STAGSDIR.'sp-subscriptions-review-button-tag.php';
	sp_SubscriptionsReviewButtonTag($args, $label, $toolTip);
}

function sp_SubscriptionsSubscribeButton($args='', $subscribeLabel='', $unsubscribeLabel='', $subscribeToolTip='', $unsubscribeToolTip='') {
	require_once STAGSDIR.'sp-subscriptions-subscribe-button-tag.php';
	sp_SubscriptionsSubscribeButtonTag($args, $subscribeLabel, $unsubscribeLabel, $subscribeToolTip, $unsubscribeToolTip);
}

function sp_SubscriptionsUnreadTopics($display=true) {
	require_once STAGSDIR.'sp-subscriptions-topics-tags.php';
	sp_SubscriptionsUnreadTopicsTag($display=true);
}

function sp_SubscriptionsSubscribeForumButton($args='', $subscribeLabel='', $unsubscribeLabel='', $subscribeToolTip='', $unsubscribeToolTip='') {
	require_once STAGSDIR.'sp-subscriptions-subscribe-forum-button-tag.php';
	sp_SubscriptionsSubscribeForumButtonTag($args, $subscribeLabel, $unsubscribeLabel, $subscribeToolTip, $unsubscribeToolTip);
}

function sp_ForumIndexSubscriptionIcon($args='', $subToolTip='', $unSubToolTip='') {
	require_once STAGSDIR.'sp-subscriptions-forum-index-icon-tag.php';
	sp_ForumIndexSubscriptionIconTag($args, $subToolTip, $unSubToolTip);
}
