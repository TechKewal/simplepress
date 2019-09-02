<?php
/*
Simple:Press Plugin Title: Template Tags and Widgets
Version: 2.1.0
Item Id: 3913
Plugin URI: https://simple-press.com/downloads/template-tags-and-widgets-plugin/
Description: A Simple:Press plugin to expose available Template Tags and Widgets
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
A plugin for Simple:Press to to expose available Template Tags and Widgets
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

define('SPTTDIR', 		SPPLUGINDIR.'template-tags/');
define('SPTEMPTAGSDIR',	SPPLUGINDIR.'template-tags/library/');

add_action('init', 	'sp_ttags_localisation');

add_action('sph_admins_options_bottom_panel',   'sp_ttags_admin_options');
add_filter('sph_admin_your_options_change',     'sp_ttags_admin_options_save');
add_filter('sph_admin_help-admin-admins',       'sp_ttags_help', 10, 3);
add_action('sph_admin_new_admin',               'sp_ttags_new_admin');

# Always load our widget
require_once SPTEMPTAGSDIR.'sp-RecentPosts-widget.php';

# Define Template Tags globally available (dont have to be enabled on template tag panel)

# ------------------------------------------------------
# Set up core stuff
# ------------------------------------------------------
function sp_ttags_localisation() {
	sp_plugin_localisation('sp-ttags');
}

function sp_ttags_help($file, $tag, $lang) {
    if ($tag == '[admin-offline]') $file = SPTTDIR.'sp-template-tags-admin-help.'.$lang;
    return $file;
}

# handle some options
function sp_ttags_admin_options() {
	$options = SP()->memberData->get(SP()->user->thisUser->ID, 'admin_options');
    $offline_message = (!empty($options['offline_message'])) ? SP()->editFilters->text($options['offline_message']) : '';
	spa_paint_open_panel();
		spa_paint_open_fieldset(__('Admin offline message', 'sp-ttags'), 'true', 'admin-offline');
			$submessage = __('Text you enter here will be displayed as a custom message when you are offline if the sp_AdminModeratorOnlineTag() template tag is used', 'sp-ttags');
			spa_paint_wide_textarea(__('Custom offline status message', 'sp-ttags'), 'offline_message', $offline_message, $submessage);
		spa_paint_close_fieldset();
	spa_paint_close_panel();
}

function sp_ttags_admin_options_save($ops) {
    $ops['offline_message'] = SP()->saveFilters->text(trim($_POST['offline_message']));
    return $ops;
}

function sp_ttags_new_admin($uid) {
	$sfadminoptions = SP()->memberData->get($uid, 'admin_options');
    $sfadminoptions['offline_message'] = '';
    SP()->memberData->update($uid, 'admin_options', $sfadminoptions);
}

# ==========================================================
# Simple Links
# ==========================================================

function sp_ForumHomeLinkTag($args='') {
    require_once SPTEMPTAGSDIR.'sp-ForumHomeLink-tag.php';
	return sp_do_sp_ForumHomeLinkTag($args);
}
function sp_ForumHomeLinkShortcode($atts) {
    require_once SPTEMPTAGSDIR.'sp-ForumHomeLink-tag.php';
    return sp_do_ForumHomeLinkShortcode($atts);
}
add_shortcode('sp_forum_home_link', 'sp_ForumHomeLinkShortcode');

function sp_GroupLinkTag($args='') {
    require_once SPTEMPTAGSDIR.'sp-GroupLink-tag.php';
	return sp_do_sp_GroupLinkTag($args);
}
function sp_GroupLinkShortcode($atts) {
    require_once SPTEMPTAGSDIR.'sp-GroupLink-tag.php';
    return sp_do_GroupLinkShortcode($atts);
}
add_shortcode('sp_group_link', 'sp_GroupLinkShortcode');

function sp_ForumLinkTag($args='') {
    require_once SPTEMPTAGSDIR.'sp-ForumLink-tag.php';
	return sp_do_sp_ForumLinkTag($args);
}
function sp_ForumLinkShortcode($atts) {
    require_once SPTEMPTAGSDIR.'sp-ForumLink-tag.php';
    return sp_do_ForumLinkShortcode($atts);
}
add_shortcode('sp_forum_link', 'sp_ForumLinkShortcode');

function sp_TopicLinkTag($args='') {
    require_once SPTEMPTAGSDIR.'sp-TopicLink-tag.php';
	return sp_do_sp_TopicLinkTag($args);
}
function sp_TopicLinkShortcode($atts) {
    require_once SPTEMPTAGSDIR.'sp-TopicLink-tag.php';
    return sp_do_TopicLinkShortcode($atts);
}
add_shortcode('sp_topic_link', 'sp_TopicLinkShortcode');

# ==========================================================
# Special Links
# ==========================================================

function sp_AddNewTopicLinkTag($args='') {
    require_once SPTEMPTAGSDIR.'sp-AddNewTopicLink-tag.php';
	return sp_do_sp_AddNewTopicLinkTag($args);
}
function sp_AddNewTopicLinkShortcode($atts) {
    require_once SPTEMPTAGSDIR.'sp-AddNewTopicLink-tag.php';
    return sp_do_AddNewTopicLinkShortcode($atts);
}
add_shortcode('sp_add_topic_link', 'sp_AddNewTopicLinkShortcode');

function sp_ForumDropdownTag($args='') {
    require_once SPTEMPTAGSDIR.'sp-ForumDropdown-tag.php';
	return sp_do_sp_ForumDropdownTag($args);
}
function sp_ForumDropdownShortcode($atts) {
    require_once SPTEMPTAGSDIR.'sp-ForumDropdown-tag.php';
    return sp_do_ForumDropdownShortcode($atts);
}
add_shortcode('sp_forum_dropdown', 'sp_ForumDropdownShortcode');

# ==========================================================
# Topic/Post Lists
# ==========================================================

function sp_RecentPostsTag($args='') {
    require_once SPTEMPTAGSDIR.'sp-RecentPosts-tag.php';
	return sp_do_sp_RecentPostsTag($args);
}
function sp_RecentPostsShortcode($atts) {
    require_once SPTEMPTAGSDIR.'sp-RecentPosts-tag.php';
    return sp_do_RecentPostsShortcode($atts);
}
add_shortcode('sp_recent_posts', 'sp_RecentPostsShortcode');

function sp_LatestPostsTag($args='') {
    require_once SPTEMPTAGSDIR.'sp-LatestPosts-tag.php';
	return sp_do_sp_LatestPostsTag($args);
}
function sp_LatestPostsShortcode($atts) {
    require_once SPTEMPTAGSDIR.'sp-LatestPosts-tag.php';
    return sp_do_LatestPostsShortcode($atts);
}
add_shortcode('sp_latest_posts', 'sp_LatestPostsShortcode');

function sp_NewestTopicsTag($args='') {
    require_once SPTEMPTAGSDIR.'sp-NewestTopics-tag.php';
	return sp_do_sp_NewestTopicsTag($args);
}
function sp_NewestTopicsShortcode($atts) {
    require_once SPTEMPTAGSDIR.'sp-NewestTopics-tag.php';
    return sp_do_NewestTopicsShortcode($atts);
}
add_shortcode('sp_newest_topics', 'sp_NewestTopicsShortcode');

function sp_UnansweredPostsTag($args='') {
    require_once SPTEMPTAGSDIR.'sp-UnansweredPosts-tag.php';
	return sp_do_sp_UnansweredPostsTag($args);
}
function sp_UnansweredPostsShortcode($atts) {
    require_once SPTEMPTAGSDIR.'sp-UnansweredPosts-tag.php';
    return sp_do_UnansweredPostsShortcode($atts);
}
add_shortcode('sp_unanswered_posts', 'sp_UnansweredPostsShortcode');

function sp_TodaysPostsTag($args='') {
    require_once SPTEMPTAGSDIR.'sp-TodaysPosts-tag.php';
	return sp_do_sp_TodaysPostsTag($args);
}
function sp_TodaysPostsShortcode($atts) {
    require_once SPTEMPTAGSDIR.'sp-TodaysPosts-tag.php';
    return sp_do_TodaysPostsShortcode($atts);
}
add_shortcode('sp_todays_posts', 'sp_TodaysPostsShortcode');

function sp_AuthorPostsTag($args='') {
    require_once SPTEMPTAGSDIR.'sp-AuthorPosts-tag.php';
	return sp_do_sp_AuthorPostsTag($args);
}
function sp_AuthorPostsShortcode($atts) {
    require_once SPTEMPTAGSDIR.'sp-AuthorPosts-tag.php';
    return sp_do_AuthorPostsShortcode($atts);
}
add_shortcode('sp_author_posts', 'sp_AuthorPostsShortcode');

# ==========================================================
# Status
# ==========================================================

function sp_AdminModeratorOnlineTag($args='') {
    require_once SPTEMPTAGSDIR.'sp-AdminModeratorOnline-tag.php';
	return sp_do_sp_AdminModeratorOnlineTag($args);
}
function sp_AdminModeratorOnlineShortcode($atts) {
    require_once SPTEMPTAGSDIR.'sp-AdminModeratorOnline-tag.php';
    return sp_do_AdminModeratorOnlineShortcode($atts);
}
add_shortcode('sp_admins_online', 'sp_AdminModeratorOnlineShortcode');

# ==========================================================
# Current Profile Related
# ==========================================================

function sp_ProfileLinkTag($args='') {
    require_once SPTEMPTAGSDIR.'sp-ProfileLink-tag.php';
	return sp_do_sp_ProfileLinkTag($args);
}
function sp_ProfileLinkShortcode($atts) {
    require_once SPTEMPTAGSDIR.'sp-ProfileLink-tag.php';
    return sp_do_ProfileLinkShortcode($atts);
}
add_shortcode('sp_profile_link', 'sp_ProfileLinkShortcode');

# ==========================================================
# Ranks Related
# ==========================================================

function sp_ShowForumRankTag($userid, $args='') {
    require_once SPTEMPTAGSDIR.'sp-ShowForumRank-tag.php';
	return sp_do_sp_ShowForumRankTag($userid, $args);
}
function sp_ShowForumRankShortcode($atts) {
    require_once SPTEMPTAGSDIR.'sp-ShowForumRank-tag.php';
    return sp_do_ShowForumRankShortcode($atts);
}
add_shortcode('sp_forum_rank', 'sp_ShowForumRankShortcode');

function sp_ShowSpecialRanksTag($userid, $args='') {
    require_once SPTEMPTAGSDIR.'sp-ShowSpecialRanks-tag.php';
	return sp_do_sp_ShowSpecialRanksTag($userid, $args);
}
function sp_ShowSpecialRanksShortcode($atts) {
    require_once SPTEMPTAGSDIR.'sp-ShowSpecialRanks-tag.php';
    return sp_do_ShowSpecialRanksShortcode($atts);
}
add_shortcode('sp_special_ranks', 'sp_ShowSpecialRanksShortcode');

# ==========================================================
# User Groups
# ==========================================================

function sp_ShowUserGroupsTag($userid, $args='', $noMembershipLabel='', $adminLabel='') {
    require_once SPTEMPTAGSDIR.'sp-ShowUserGroups-tag.php';
	return sp_do_sp_UserGroupsTag($userid, $args, $noMembershipLabel, $adminLabel);
}
function sp_ShowUserGroupsShortcode($atts) {
    require_once SPTEMPTAGSDIR.'sp-ShowUserGroups-tag.php';
    return sp_do_UserGroupsShortcode($atts);
}
add_shortcode('sp_user_groups', 'sp_ShowUserGroupsShortcode');

# ==========================================================
# Sidebar Dashboard
# ==========================================================

function sp_SideDashTag($args='') {
    require_once SPTEMPTAGSDIR.'sp-SideDash-tag.php';
	return sp_do_sp_SideDashTag($args);
}
function sp_SideDashShortcode($atts) {
    require_once SPTEMPTAGSDIR.'sp-SideDash-tag.php';
    return sp_do_SideDashShortcode($atts);
}
add_shortcode('sp_sidedash', 'sp_SideDashShortcode');

# ==========================================================
# Hot Topics
# ==========================================================

function sp_HotTopicsTag($args='') {
    require_once SPTEMPTAGSDIR.'sp-HotTopics-tag.php';
	return sp_do_sp_HotTopicsTag($args);
}
function sp_HotTopicsShortcode($atts) {
    require_once SPTEMPTAGSDIR.'sp-HotTopics-tag.php';
    return sp_do_HotTopicsShortcode($atts);
}
add_shortcode('sp_hot_topics', 'sp_HotTopicsShortcode');
