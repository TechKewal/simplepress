<?php
/*
Simple:Press Plugin Title: Blog Post Linking
Version: 2.2.0
Item Id: 3909
Plugin URI: https://simple-press.com/downloads/blog-post-linking-plugin/
Description: Links Blog Posts and Comments to Forum Topics and Posts
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-12-14 15:17:56 -0600 (Fri, 14 Dec 2018) $
$Rev: 15849 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

require_once SP_PLUGIN_DIR.'/forum/database/sp-db-management.php';

# IMPORTANT DB VERSION
define('SPLINKINGDBVERSION', 2);

# ======================================
# CONSTANTS USED BY THIS PLUGIN
# ======================================
define('SFLINKS', 		SP_PREFIX.'sflinks');
define('SPBLDIR',		SPPLUGINDIR.'blog-linking/');
define('SPBLLIB',		SPPLUGINDIR.'blog-linking/library/');
define('SPBLFORM',		SPPLUGINDIR.'blog-linking/forms/');
define('SPBLAJAX',		SPPLUGINDIR.'blog-linking/ajax/');
define('SPBLIMAGES',	SPPLUGINURL.'blog-linking/resources/images/');
define('SPBLCSS',		SPPLUGINURL.'blog-linking/resources/css/');
define('SPBLJS', 		SPPLUGINURL.'blog-linking/resources/jscript/');
define('SPBLIMAGESMOB',	SPPLUGINURL.'blog-linking/resources/images/mobile/');

# ======================================
# ACTIONS/FILTERS USED BY THUS PLUGIN
# ======================================
# Plugin and admin
add_action('sph_activate_blog-linking/sp-linking-plugin.php',	    'sp_linking_install');
add_action('sph_deactivate_blog-linking/sp-linking-plugin.php',     'sp_linking_deactivate');
add_action('sph_uninstall_blog-linking/sp-linking-plugin.php', 	    'sp_linking_uninstall');
add_filter('sph_plugins_active_buttons',						    'sp_linking_uninstall_option', 10, 2);
add_action('init', 												    'sp_linking_localisation');
add_action('sph_admin_menu',									    'sp_linking_admin_menu');
add_filter('sph_admin_help-admin-components', 				   	    'sp_linking_admin_help', 10, 3);
add_filter('sph_perms_tooltips', 								    'sp_linking_admin_tooltips', 10, 2);
add_action('sph_merge_forums',									    'sp_linking_merge_forums', 1, 2);
add_action('admin_footer',                                          'sp_linking_upgrade_check');
add_action('sph_plugin_update_blog-linking/sp-linking-plugin.php',  'sp_linking_upgrade_check');
add_action('sph_permissions_reset',			                        'sp_linking_reset_permissions');

# Blog Post Link Processing
$sfpostlinking = SP()->options->get('sfpostlinking');

add_action('sph_print_plugin_styles',		'sp_linking_header');
add_action('sph_print_plugin_scripts', 		'sp_linking_load_js');

add_action('save_post', 					'sp_linking_save_blog_link');
add_action('draft_to_publish',				'sp_linking_publish_blog_link', 10, 1);
add_action('future_to_publish',			 	'sp_linking_publish_blog_link', 10, 1);
add_action('pending_to_publish',			'sp_linking_publish_blog_link', 10, 1);
add_action('new_to_publish',		 		'sp_linking_publish_blog_link', 10, 1);
add_action('edit_post',						'sp_linking_update_blog_link');
add_action('delete_post', 					'sp_linking_delete_blog_link');
add_action('sph_setup_forum',				'sp_linking_check_delete_action');

if ($sfpostlinking['sfuseautolabel']) {
	add_filter('the_content', 				'sp_linking_show_blog_link');
	add_filter('the excerpt', 				'sp_linking_show_blog_link');
}

if ($sfpostlinking['sflinkcomments'] == 2 || $sfpostlinking['sflinkcomments'] == 3) {
	add_filter('comments_array', 			'sp_linking_topic_as_comments');
	add_filter('get_avatar_comment_types', 	'sp_linking_add_comment_type');
	add_filter('edit_comment_link', 		'sp_linking_remove_edit_comment_link', 1, 2);

	if (!is_admin()) add_filter('get_comments_number', 		'sp_comments_count_filter', 1, 2);
}

if (isset($sfpostlinking['sfpostcomment']) && $sfpostlinking['sfpostcomment'] == true) {
	add_action('wp_set_comment_status', 	'sp_linking_process_new_comment', 10, 2);
	add_action('comment_post', 				'sp_linking_process_new_comment', 10, 2);
	add_action('edit_comment', 				'sp_linking_update_comment_post');
}

add_action('admin_init', 					'sp_linking_blog_link_form');
add_filter('manage_posts_columns', 			'sp_linking_add_admin_link_column');
add_filter('manage_pages_columns', 			'sp_linking_add_admin_link_column');
add_action('manage_posts_custom_column', 	'sp_linking_show_admin_link_column', 10, 2);
add_action('manage_pages_custom_column', 	'sp_linking_show_admin_link_column', 10, 2);

# Forum wide support
add_action('sph_move_post_form',			'sp_linking_move_post_form', 1, 2);
add_action('sph_move_post', 				'sp_linking_move_post', 1, 3);
add_action('sph_move_topic',				'sp_linking_move_topic', 1, 3);
add_filter('sph_forumview_query', 			'sp_linking_forumview_query');
add_filter('sph_forumview_topic_records', 	'sp_linking_forumview_records', 10, 2);
add_filter('sph_topicview_query', 			'sp_linking_forumview_query');
add_filter('sph_topicview_topic_record', 	'sp_linking_forumview_records', 10, 2);
add_action('sph_topic_delete', 				'sp_linking_delete_topic');
add_filter('sph_topic_editor_display_options',	'sp_linking_check_topic_option', 1);
add_filter('sph_topic_options_add', 		'sp_linking_add_topic_option', 1, 2);

# Make new blog post from forum topic
add_filter('sph_new_forum_post', 			'sp_linking_collect_link_data');
add_action('sph_post_create', 				'sp_linking_create_blog_post');

# URL handling
add_action('sph_blog_support_start', 		'sp_linking_canonical_url', 1, 1);
add_filter('sph_aioseo_canonical_url',		'sp_linking_aioseo_caninical_url', 1, 2);
add_filter('sph_canonical_url',				'sp_linking_switch_canionical_url');

# Forum Display Items
add_filter('sph_TopicIndexStatusIconsLast',	'sp_linking_add_status_icon');

# Topic Tool
add_filter('sph_add_topic_tool', 			'sp_linking_topic_tool', 10, 5);
add_action('sph_setup_forum',				'sp_linking_break_listen');

# Topic Post Edit
add_filter('sph_post_edit_submit_top',		'sp_linking_post_edit_form', 1, 3);
add_filter('sph_post_editor_edit_above_toolbar',	'sp_linking_post_edit_form', 1, 3);
add_action('sph_post_edit_after_save',		'sp_linking_update_blog_post');

add_action('admin_enqueue_scripts', 		'sp_linking_admin_load_js');

# Ajax Calls
add_action('wp_ajax_linking', 				'sp_linking_break_link_ajax');
add_action('wp_ajax_nopriv_linking', 		'sp_linking_break_link_ajax');
add_action('wp_ajax_categories', 			'sp_linking_categories_ajax');
add_action('wp_ajax_nopriv_categories',		'sp_linking_categories_ajax');


# ======================================
# BLOG POST LINING PROCESSING
# ======================================

# ----------------------------------------------
# Save, publish, update and delete
# blog posts
# ----------------------------------------------
function sp_linking_save_blog_link($postid) {
	require_once SPBLLIB.'sp-linking-blog.php';
	$postid = (int) $postid;
	sp_save_blog_link($postid);
}

function sp_linking_publish_blog_link($post) {
	require_once SPBLLIB.'sp-linking-blog.php';
	sp_publish_blog_link($post);
}

function sp_linking_update_blog_link($postid) {
	require_once SPBLLIB.'sp-linking-blog.php';
	sp_update_blog_link($postid);
}

function sp_linking_delete_blog_link($postid) {
	require_once SPBLLIB.'sp-linking-blog.php';
	sp_delete_blog_link($postid);
}

function sp_linking_check_delete_action() {
	require_once SPBLLIB.'sp-linking-support.php';
	if (isset($_POST['linkbreak'])) {
		sp_break_blog_link(SP()->filters->integer($_POST['linkbreak']), SP()->filters->integer($_POST['blogpost']));
	}
}

function sp_linking_upgrade_check() {
    require_once SPBLDIR.'sp-linking-upgrade.php';
    sp_linking_do_upgrade_check();
}

# ----------------------------------------------
# Display the blog link itself in blog post
# ----------------------------------------------
function sp_linking_show_blog_link($content) {
	require_once SPBLLIB.'sp-linking-blog.php';
	return sp_show_blog_link($content);
}

# ----------------------------------------------
# Manipulation of comments
# ----------------------------------------------
function sp_linking_topic_as_comments($comments) {
	require_once SPBLLIB.'sp-linking-comments.php';
	return sp_topic_as_comments($comments);
}

function sp_linking_add_comment_type($list) {
	require_once SPBLLIB.'sp-linking-comments.php';
	return sp_add_comment_type($list);
}

function sp_linking_remove_edit_comment_link($link, $id) {
	require_once SPBLLIB.'sp-linking-comments.php';
	$link = sp_remove_edit_comment_link($link, $id);
	return $link;
}

function sp_linking_process_new_comment($cid, $commentstatus) {
	require_once SPBLLIB.'sp-linking-comments.php';
	sp_process_new_comment($cid, $commentstatus);
}

function sp_linking_update_comment_post($cid) {
	require_once SPBLLIB.'sp-linking-comments.php';
	sp_update_comment_post($cid);
}

# ----------------------------------------------
# render the blog post linking form in edit post
# ----------------------------------------------
function sp_linking_blog_link_form() {
	require_once SPBLFORM.'sp-linking-form.php';
	sp_blog_link_form();
}

# ----------------------------------------------
# Add blog linking info to post and page lists
# ----------------------------------------------
function sp_linking_add_admin_link_column($defaults) {
	require_once SPBLLIB.'sp-linking-blog.php';
	return sp_add_admin_link_column($defaults);
}

function sp_linking_show_admin_link_column($column, $postid) {
	require_once SPBLLIB.'sp-linking-blog.php';
	sp_show_admin_link_column($column, $postid);
}

# ----------------------------------------------
# Ajax call to breaklink/categories: set handler
# ----------------------------------------------
function sp_linking_break_link_ajax() {
	require_once SPBLAJAX.'sp-ajax-linking.php';
}

function sp_linking_categories_ajax() {
	require_once SPBLAJAX.'sp-ajax-categories.php';
}

# ----------------------------------------------
# Prepare data when new topic is created
# ----------------------------------------------
function sp_linking_collect_link_data($postdata) {
	require_once SPBLLIB.'sp-linking-forum.php';
	return sp_do_collect_link_data($postdata);
}

function sp_linking_create_blog_post($postdata) {
	require_once SPBLLIB.'sp-linking-forum.php';
	sp_do_create_blog_post($postdata);
}

# ======================================
# PLUGIN AND ADMIN
# ======================================

# ----------------------------------------------
# Run Install Script on Activation action
# ----------------------------------------------
function sp_linking_install() {
	require_once SPBLDIR.'sp-linking-install.php';
	sp_linking_do_install();
}

function sp_linking_reset_permissions() {
	require_once SPBLDIR.'sp-linking-install.php';
	sp_linking_do_reset_permissions();
}

# ----------------------------------------------
# Run Deactivate Script on Deactivation action
# ----------------------------------------------
function sp_linking_deactivate() {
	require_once SPBLDIR.'sp-linking-uninstall.php';
	sp_linking_do_deactivate();
}

# ----------------------------------------------
# Run Uninstall Script on Uninstall action
# ----------------------------------------------
function sp_linking_uninstall() {
	require_once SPBLDIR.'sp-linking-uninstall.php';
	sp_linking_do_uninstall();
}

# ------------------------------------------------------
# Add the 'Uninstall' and 'Options' link to plugins list
# ------------------------------------------------------
function sp_linking_uninstall_option($actionlink, $plugin) {
	if ($plugin == 'blog-linking/sp-linking-plugin.php') {
		$url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
		$actionlink.= "&nbsp;&nbsp;<a href='".$url."' title='".__("Uninstall this plugin", "sp-linking")."'>".__("Uninstall", "sp-linking")."</a>";

		$url = SPADMINCOMPONENTS.'&amp;tab=plugin&amp;admin=sp_linking_admin_form&amp;save=sp_linking_admin_save&amp;form=1';
		$actionlink.= "&nbsp;&nbsp;<a href='".$url."' title='".__("Options", "sp-linking")."'>".__("Options", "sp-linking")."</a>";
	}
	return $actionlink;
}

# ------------------------------------------------------
# Set up language file
# ------------------------------------------------------
function sp_linking_localisation() {
	sp_plugin_localisation('sp-linking');
}

# ----------------------------------------------
# Add permission tooltips to permissions panels
# ----------------------------------------------
function sp_linking_admin_tooltips($tips, $t) {
    $tips['create_linked_topics'] = $t.__('Can create linked blog post and forum topics', 'sp-linking');
    $tips['break_linked_topics'] = $t.__('Can break link between blog posts and forum topics', 'sp-linking');
    return $tips;
}

# ----------------------------------------------
# Add Linking Admin Panel to Components
# ----------------------------------------------
function sp_linking_admin_menu() {
	$subpanels = array(
		__("Blog Post Linking", "sp-linking") => array('admin' => 'sp_linking_admin_form', 'save' => 'sp_linking_admin_save', 'form' => 1, 'id' => 'bloglink')
	);
	SP()->plugin->add_admin_subpanel('components', $subpanels);
}

function sp_linking_merge_forums($source, $target) {
	require_once SPBLLIB.'sp-linking-support.php';
	sp_do_linking_merge_forums($source, $target);
}

# ----------------------------------------------
# Load the Linking Options Admin Form
# ----------------------------------------------
function sp_linking_admin_form() {
	require_once SPBLDIR.'admin/spa-components-linking-form.php';
	spa_components_linking_options();
}

# ----------------------------------------------
# Save the Linking Options Admin Form Data
# ----------------------------------------------
function sp_linking_admin_save() {
	require_once SPBLDIR.'admin/spa-components-linking-save.php';
	return spa_linking_options_save();
}

# ----------------------------------------------
# Action the Blog Linking admin panel popup help
# ----------------------------------------------
function sp_linking_admin_help($file, $tag, $lang) {
    if ($tag == '[post-linking]' ||
    	$tag == '[link-urls]' ||
    	$tag == '[link-text-display]' ||
    	$tag == '[show-as-comments]' ||
    	$tag == '[posts-from-comments]') $file = SPBLDIR.'admin/spa-linking-admin-help.'.$lang;
    return $file;
}

# ----------------------------------------------
# Move Post Management
# ----------------------------------------------
function sp_linking_move_post_form($postid, $topicid) {
	require_once SPBLLIB.'sp-linking-support.php';
	sp_do_move_post_form($postid, $topicid);
}

function sp_linking_move_post($oldtopicid, $newtopicid, $newforumid) {
	require_once SPBLLIB.'sp-linking-support.php';
	sp_do_move_post($oldtopicid, $newtopicid, $newforumid);
}

# ----------------------------------------------
# Move Topic Management
# ----------------------------------------------
function sp_linking_move_topic($topicid, $oldforumid, $newforumid) {
	require_once SPBLLIB.'sp-linking-support.php';
	sp_relink_topic($topicid, $topicid, $newforumid);
}

function sp_linking_header() {
	require_once SPBLLIB.'sp-linking-support.php';
    sp_linking_do_header();
}

function sp_linking_load_js($footer) {
    $script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPBLJS.'sp-linking.js' : SPBLJS.'sp-linking.min.js';
	SP()->plugin->enqueue_script('splinking', $script, array('jquery'), false, $footer);
}

function sp_linking_admin_load_js($hook) {
    if ($hook != 'post.php') return;

    $script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPBLJS.'sp-linking.js' : SPBLJS.'sp-linking.min.js';
	wp_enqueue_script('splinking', $script, array('jquery'), false, false);
}

# ----------------------------------------------
# Add blog_post_id to forum view query/class
# ----------------------------------------------
function sp_linking_forumview_query($query) {
	$query->fields.= ', blog_post_id';
	return $query;
}

function sp_linking_forumview_records($data, $record) {
	$data->blog_post_id = $record->blog_post_id;
	return $data;
}

# ----------------------------------------------
# Delete blog link from a deleted topic
# ----------------------------------------------
function sp_linking_delete_topic($posts) {
	if ($posts) {
    	$postid = (is_object($posts)) ? $posts->post_id : $posts[0]->post_id;
		sp_blog_links_control('delete', $postid);
	}
}

# ----------------------------------------------
# check if link option needed on add topic form
# ----------------------------------------------
function sp_linking_check_topic_option($display) {
	if (SP()->auths->get('create_linked_topics', SP()->forum->view->thisForum->forum_id) && current_user_can('publish_posts')) $display['options'] = true;
	return $display;
}

# ----------------------------------------------
# Add link option to form add topic form
# ----------------------------------------------
function sp_linking_add_topic_option($optionsBox, $forum) {
	require_once SPBLLIB.'sp-linking-support.php';
	return sp_do_add_topic_option($optionsBox, $forum);
}

# ----------------------------------------------
# Canonical URL handling
# ----------------------------------------------
function sp_linking_canonical_url($wpPost) {
	require_once SPBLLIB.'sp-linking-support.php';
	sp_do_canonical_url($wpPost);
}

function sp_linking_aioseo_caninical_url($url, $wpPost) {
	require_once SPBLLIB.'sp-linking-support.php';
	return sp_do_aioseo_canonical_url($url, $wpPost);
}

function sp_linking_switch_canionical_url($url) {
	require_once SPBLLIB.'sp-linking-support.php';
	return sp_do_switch_canonical_url($url);
}

# ----------------------------------------------
# Add blog link status icon to forum view
# ----------------------------------------------
function sp_linking_add_status_icon($content) {
	$out = '';
	if (SP()->forum->view->thisTopic->blog_post_id) {
		$p = (SP()->core->device == 'mobile' && current_theme_supports('sp-theme-responsive')) ? SPBLIMAGESMOB : SPBLIMAGES;
		$toolTip = esc_attr(__('This topic is linked to a blog post', 'sp-linking'));
		$out = SP()->theme->paint_icon('spIcon spIconNoAction', $p, "sp_BlogLink.png", $toolTip);
	}
	return $content.$out;
}

# ======================================
# TEMPLATE TAGS
# ======================================

# Template function tags
# ----------------------------------------------
# Display the blog link on the topic view
# ----------------------------------------------
function sp_TopicHeaderShowBlogLink($args='', $label='', $toolTip='') {
	require_once SPBLDIR.'template-tags/sp-linking-show-link.php';
	sp_do_TopicHeaderShowBlogLink($args, $label, $toolTip);
}

#  Blog/Site tags
function spBlogTopicLink($postid, $show_img=true) {
	require_once SPBLLIB.'sp-linking-blog.php';
	require_once SPBLDIR.'template-tags/sp-linking-blog-tags.php';
	spBlogTopicLinkTag($postid, $show_img);
}

function spLinkedTopicPostCount() {
	require_once SPBLLIB.'sp-linking-blog.php';
	require_once SPBLDIR.'template-tags/sp-linking-blog-tags.php';
	spLinkedTopicPostCountTag();
}

function spFirstTopicPostLink($blogpostid, $linktext) {
	require_once SPBLDIR.'template-tags/sp-linking-blog-tags.php';
	spFirstTopicPostLinkTag($blogpostid, $linktext);
}

function spLastTopicPostLink($blogpostid, $linktext) {
	require_once SPBLDIR.'template-tags/sp-linking-blog-tags.php';
	spLastTopicPostLinkTag($blogpostid, $linktext);
}

function spCommentsNumber($no_comment="0 Comments", $one_comment="1 Comment", $many_comment="% Comments", $blogcomments=false, $postid=0) {
	# quickly remove comment count filter for backwards comat and to avoid adding count in twice
	remove_filter('get_comments_number', 'sp_comments_count_filter', 1);
	require_once SPBLLIB.'sp-linking-blog.php';
	require_once SPBLDIR.'template-tags/sp-linking-comment-tags.php';
	spCommentsNumberTag($no_comment, $one_comment, $many_comment, $blogcomments, $postid);
}

# ----------------------------------------------
# Add topic posts to comment count - wp filter
# ----------------------------------------------
function sp_comments_count_filter($count, $postid) {
	require_once SPBLLIB.'sp-linking-blog.php';
	require_once SPBLDIR.'template-tags/sp-linking-comment-tags.php';
	return sp_do_comments_count_filter($count, $postid);
}

# ----------------------------------------------
# Break Link - topic edit tool
# ----------------------------------------------
function sp_linking_topic_tool($out, $topic, $forum, $page, $br) {
	require_once SPBLLIB.'sp-linking-support.php';
	return $out.sp_do_linking_topic_tool($topic, $forum, $page, $br);
}

function sp_linking_break_listen() {
	if (isset($_POST['linktopic'])) {
		require_once SPBLLIB.'sp-linking-support.php';
		sp_break_blog_link($_POST['linktopic'], $_POST['linkpost']);
		SP()->notifications->message(0, __('Link Broken', 'sp-linking'));
	}
}

# ----------------------------------------------
# Post topic edit
# ----------------------------------------------

function sp_linking_post_edit_form($content, $topic, $a) {
	if (!isset($topic->editpost_id)) return $out;
	$out='';
	if ($topic->blog_post_id && $topic->posts[$topic->editpost_id]->post_index == 1) {
		require_once SPBLLIB.'sp-linking-support.php';
		$out = sp_do_linking_post_edit_form($topic->blog_post_id);
	}
	return $content.$out;
}
/*
function sp_linking_post_edit_form($content, $postid, $topic) {
	$out='';
	if($topic->blog_post_id && $topic->posts[$postid]->post_index ==1) {
		require_once SPBLLIB.'sp-linking-support.php';
		$out = sp_do_linking_post_edit_form($topic->blog_post_id);
	}
	return $content.$out;
}
*/
function sp_linking_update_blog_post($newpost) {
	if (isset($_POST['blogpostid']) && isset($_POST['blogpostedit'])) {
		require_once SPBLLIB.'sp-linking-support.php';
		$newpost['blogpostid'] = SP()->filters->integer($_POST['blogpostid']);
		sp_do_linking_update_blog_post($newpost);
	}
}

# ------------------------------------------------------------------
# sp_blog_links_control()
#
# In main pliugin file because everythign uses it
#
# General postmeta handling for blog linking
#	$action		save, update, read or delete
#	$postid		WP Post id of the link
#	$forumid	ID of target forum
#	$topicid	ID of target topic
#	$syncedit	Optional - sync edit flag
# ------------------------------------------------------------------
function sp_blog_links_control($action, $postid, $forumid=0, $topicid=0, $syncedit=0) {
	if (!isset($postid)) return;

	static $bLinks = array();

	switch($action) {
		case 'save':
			# check if there already...
			$result = SP()->DB->table(SFLINKS, "post_id=$postid", 'row');
			if ($result) {
				$sql = 'UPDATE '.SFLINKS." SET forum_id=$forumid, topic_id=$topicid, syncedit=$syncedit WHERE post_id=$postid";
				SP()->DB->execute($sql);
			} else {
				$sql = 'INSERT INTO '.SFLINKS." (post_id, forum_id, topic_id, syncedit) VALUES ($postid, $forumid, $topicid, $syncedit)";
				SP()->DB->execute($sql);
			}
			unset($bLinks);
			break;

		case 'update':
			$sql = 'UPDATE '.SFLINKS." SET forum_id=$forumid, topic_id=$topicid, syncedit=$syncedit WHERE post_id=$postid";
			SP()->DB->execute($sql);
			unset($bLinks);
			break;

		case 'read':
			if (is_admin()) {
				return SP()->DB->table(SFLINKS, "post_id=$postid", 'row');
			} else {
				if (!array_key_exists($postid, $bLinks)) $bLinks[$postid] = SP()->DB->table(SFLINKS, "post_id=$postid", 'row');
				return $bLinks[$postid];
			}
			break;

		case 'delete':
			$sql = 'DELETE FROM '.SFLINKS." WHERE post_id=$postid";
			unset($bLinks);
			return (SP()->DB->select($sql, 'row'));
			break;
	}
}
