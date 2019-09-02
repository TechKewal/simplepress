<?php
/*
Simple:Press Plugin Title: Post Ratings
Version: 2.1.0
Item Id: 3916
Plugin URI: https://simple-press.com/downloads/post-ratings-plugin/
Description: A Simple:Press plugin for allowing rating of posts
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
A plugin for Simple:Press to add a post rating system to your forum.
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPRATINGDBVERSION', 4);

define('SPACTIVITY_RATING', SP()->activity->get_type('posts rated'));

define('SPRATINGS', 	SP_PREFIX.'sfpostratings');
define('PRDIR', 		SPPLUGINDIR.'post-rating/');
define('PRADMINDIR', 	SPPLUGINDIR.'post-rating/admin/');
define('PRLIBDIR', 		SPPLUGINDIR.'post-rating/library/');
define('PRAJAXDIR', 	SPPLUGINDIR.'post-rating/ajax/');
define('PRTAGSDIR', 	SPPLUGINDIR.'post-rating/template-tags/');
define('PRSCRIPT', 		SPPLUGINURL.'post-rating/resources/jscript/');
define('PRIMAGES', 		SPPLUGINURL.'post-rating/resources/images/');
define('PRCSS', 		SPPLUGINURL.'post-rating/resources/css/');

add_action('sph_activate_post-rating/sp-rating-plugin.php', 	       'sp_rating_install');
add_action('sph_deactivate_post-rating/sp-rating-plugin.php', 	       'sp_rating_deactivate');
add_action('sph_uninstalled', 									       'sp_rating_sp_uninstall');
add_action('sph_uninstall_post-rating/sp-rating-plugin.php', 	       'sp_rating_uninstall');
add_action('sph_print_plugin_scripts', 								   'sp_rating_load_js');
add_action('init', 											      	   'sp_rating_localization');
add_action('sph_forum_create_forum_options', 				       	   'sp_rating_create_forum');
add_action('sph_forum_forum_create', 							       'sp_rating_create_forum_save');
add_action('sph_forum_edit_forum_options', 						       'sp_rating_edit_forum');
add_action('sph_forum_forum_edit', 								       'sp_rating_edit_forum_save');
add_action('sph_topic_delete', 								     	   'sp_rating_topic_delete');
add_action('sph_post_delete', 								      	   'sp_rating_post_delete');
add_action('sph_options_display_right_panel', 					       'sp_rating_admin_options');
add_action('sph_option_display_save', 							       'sp_rating_admin_save_options');
add_action('sph_member_deleted', 								       'sp_rating_member_del');
add_action('sph_print_plugin_styles', 							       'sp_rating_header');
add_action('sph_plugin_update_post-rating/sp-rating-plugin.php',       'sp_rating_upgrade_check');
add_action('admin_footer',                                             'sp_rating_upgrade_check');
add_action('sph_permissions_reset',                                    'sp_rating_permissions_reset');

add_filter('sph_admin_help-admin-options', 		'sp_rating_admin_help', 10, 3);
add_filter('sph_perms_tooltips', 				'sp_rating_tooltips', 10, 2);
add_filter('sph_plugins_active_buttons', 		'sp_rating_uninstall_option', 10, 2);
add_filter('sph_forumview_query', 				'sp_rating_forum_query');
add_filter('sph_forumview_forum_record', 		'sp_rating_forum_records', 10, 2);
add_filter('sph_topicview_query', 				'sp_rating_topic_query');
add_filter('sph_topicview_topic_record', 		'sp_rating_forum_records', 10, 2);
add_filter('sph_topicview_post_records', 		'sp_rating_topic_records', 10, 2);

# Mycred Support
add_action('mycred_pre_init',				'sp_rating_load_mycred', 2);
add_filter('add_sp_mycred_extension',		'sp_rating_extend_mycred');
add_action('prefs_sp_mycred_extension', 	'sp_rating_prefs_create');
add_action('sph_post_rating_actions',		'sp_rating_save_mycred', 1, 2);

# Ajax Handler
add_action('wp_ajax_rating-manage',			'sp_rating_ajax_manage');
add_action('wp_ajax_nopriv_rating-manage',	'sp_rating_ajax_manage');


function sp_rating_admin_options() {
    require_once PRADMINDIR.'sp-rating-admin-options.php';
	sp_rating_admin_options_form();
}

function sp_rating_admin_save_options() {
    require_once PRADMINDIR.'sp-rating-admin-options-save.php';
    sp_rating_admin_options_save();
}

function sp_rating_admin_save_uninstall($action, $plugin) {
    require_once PRADMINDIR.'sp-rating-admin-options-save.php';
    sp_rating_admin_uninstall_save($action, $plugin);
}

function sp_rating_admin_help($file, $tag, $lang) {
    if ($tag == '[post-ratings]') $file = PRADMINDIR.'sp-rating-admin-help.'.$lang;
    return $file;
}

function sp_rating_uninstall_option($actionlink, $plugin) {
    require_once PRLIBDIR.'sp-rating-components.php';
    $actionlink = sp_rating_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_rating_localization() {
	sp_plugin_localisation('sp-rating');
}

function sp_rating_header() {
    require_once PRLIBDIR.'sp-rating-components.php';
    sp_rating_do_header();
}

function sp_rating_tooltips($tips, $t) {
    $tips['rate_posts'] = $t.__('Can rate posts in any topic in the forum subject to the forum use of post rating', 'sp-rating');
    return $tips;
}

function sp_rating_uninstall() {
    require_once PRDIR.'sp-rating-uninstall.php';
    sp_rating_do_uninstall();
}

function sp_rating_sp_uninstall() {
    require_once PRDIR.'sp-rating-uninstall.php';
    sp_rating_do_sp_uninstall();
}

function sp_rating_install() {
    require_once PRDIR.'sp-rating-install.php';
    sp_rating_do_install();
}

function sp_rating_deactivate() {
    require_once PRLIBDIR.'sp-rating-components.php';
	sp_rating_do_deactivate();
}

function sp_rating_upgrade_check() {
    require_once PRDIR.'sp-rating-upgrade.php';
    sp_rating_do_upgrade_check();
}

function sp_rating_permissions_reset() {
    require_once PRDIR.'sp-rating-install.php';
    sp_rating_do_permissions_reset();
}

function sp_rating_load_js($footer) {
    require_once PRLIBDIR.'sp-rating-components.php';
	sp_rating_do_load_js($footer);
}

function sp_rating_ajax_manage() {
    require_once PRAJAXDIR.'sp-rating-ajax-manage.php';
}

function sp_rating_create_forum() {
    require_once PRLIBDIR.'sp-rating-components.php';
	sp_rating_do_create_forum();
}

function sp_rating_create_forum_save($forumid) {
	if (isset($_POST['forum_ratings'])) $postrating = 1; else $postrating = 0;
	SP()->DB->execute('UPDATE '.SPFORUMS." SET post_ratings=$postrating WHERE forum_id=$forumid");
    SP()->auths->reset_cache();
}

function sp_rating_edit_forum($forum) {
    require_once PRLIBDIR.'sp-rating-components.php';
	sp_rating_do_edit_forum($forum);
}

function sp_rating_edit_forum_save($forumid) {
	if (isset($_POST['forum_ratings'])) $postrating = 1; else $postrating = 0;
	SP()->DB->execute('UPDATE '.SPFORUMS." SET post_ratings=$postrating WHERE forum_id=$forumid");
    SP()->auths->reset_cache();
}

function sp_rating_topic_delete($posts) {
    require_once PRLIBDIR.'sp-rating-components.php';
	sp_rating_do_topic_delete($posts);
}

function sp_rating_post_delete($post) {
    require_once PRLIBDIR.'sp-rating-components.php';
	sp_rating_do_post_delete($post);
}

function sp_rating_member_del($userid) {
    require_once PRLIBDIR.'sp-rating-components.php';
    require_once PRLIBDIR.'sp-rating-database.php';
	sp_rating_do_member_del($userid);
}

function sp_rating_forum_query($query) {
	$query->fields.= ', post_ratings';
	return $query;
}

function sp_rating_forum_records($data, $record) {
	$data->post_ratings = $record->post_ratings;
	return $data;
}

function sp_rating_topic_query($query) {
	$query->fields.= ', post_ratings, rating_id, vote_count, ratings_sum, ips, members';
	$query->left_join[] = SPRATINGS.' ON '.SPRATINGS.'.post_id = '.SPPOSTS.'.post_id';
	return $query;
}

function sp_rating_topic_records($data, $record) {
	$data->rating_id 	= $record->rating_id;
	$data->vote_count 	= $record->vote_count;
	$data->ratings_sum 	= $record->ratings_sum;
	$data->ips 			= $record->ips;
	$data->members 		= $record->members;
	return $data;
}

# MyCred Support
function sp_rating_load_mycred() {
    require_once PRLIBDIR.'sp-rating-mycred.php';
}

function sp_rating_extend_mycred($defs) {
    return sp_rating_do_extend_mycred($defs);
}

function sp_rating_prefs_create($args) {
	sp_rating_do_prefs_create($args);
}

function sp_rating_save_mycred($postid, $action) {
    require_once PRLIBDIR.'sp-rating-mycred.php';
	sp_rating_do_save_mycred($postid, $action);
}

# Define Template Tags globally available (dont have to be enabled on template tag panel)

function sp_TopicIndexRating($args='', $summaryLabel='') {
    require_once PRTAGSDIR.'sp-rating-topic-rating.php';
	sp_TopicIndexRatingTag($args, $summaryLabel);
}

function sp_PostIndexRatePost($args='') {
    require_once PRTAGSDIR.'sp-rating-post-rating.php';
	sp_PostIndexRatePostTag($args);
}

function sp_rating_highest_rated_posts_tag($limit=10, $forum=true, $user=true, $postdate=true, $listtags=true, $forumids=0, $rating=true, $count=true) {
    require_once PRTAGSDIR.'sp-highest-rated-tag.php';
    sp_rating_do_highest_rated_posts_tag($limit, $forum, $user, $postdate, $listtags, $forumids, $rating, $count);
}

function sp_rating_most_rated_posts_tag($limit=10, $forum=true, $user=true, $postdate=true, $listtags=true, $forumids=0, $count=true, $avg=true) {
    require_once PRTAGSDIR.'sp-most-rated-tag.php';
    sp_rating_do_most_rated_posts_tag($limit, $forum, $user, $postdate, $listtags, $forumids, $count, $avg);
}
