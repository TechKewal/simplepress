<?php
/*
Simple:Press Plugin Title: Private Posts
Version: 2.1.0
Item Id: 4182
Plugin URI: https://simple-press.com/downloads/private-posts-plugin/
Description: A Simple:Press plugin for marking posts within a topic as private
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPPRIVATEPOSTSDBVERSION', 1);

define('SPPRIVATEPOSTSDIR', 		SPPLUGINDIR.'private-posts/');
define('SPPRIVATEPOSTSADMINDIR',    SPPLUGINDIR.'private-posts/admin/');
define('SPPRIVATEPOSTSLIBDIR', 	    SPPLUGINDIR.'private-posts/library/');
define('SPPRIVATEPOSTSLIBURL', 	    SPPLUGINURL.'private-posts/library/');
define('SPPRIVATEPOSTSCSS', 		SPPLUGINURL.'private-posts/resources/css/');
define('SPPRIVATEPOSTSIMAGES', 	    SPPLUGINURL.'private-posts/resources/images/');

add_action('init', 										                    'sp_private_posts_localization');
add_action('sph_activate_private-posts/sp-private-posts-plugin.php',        'sp_private_posts_install');
add_action('sph_deactivate_private-posts/sp-private-posts-plugin.php',      'sp_private_posts_deactivate');
add_action('sph_uninstall_private-posts/sp-private-posts-plugin.php',       'sp_private_posts_uninstall');
add_action('sph_activated', 				                                'sp_private_posts_sp_activate');
add_action('sph_deactivated', 				                                'sp_private_posts_sp_deactivate');
add_action('sph_uninstalled', 								                'sp_private_posts_sp_uninstall');
add_action('sph_plugin_update_private-posts/sp-private-posts-plugin.php',   'sp_private_posts_upgrade_check');
add_action('admin_footer',                                                  'sp_private_posts_upgrade_check');
add_action('sph_permissions_reset', 						                'sp_private_posts_reset_permissions');
add_action('sph_new_post', 							                        'sp_private_posts_save_post', 99);
add_action('sph_setup_forum',                                               'sp_private_posts_process_actions');
add_action('sph_options_content_right_panel', 				                'sp_private_posts_admin_options');
add_action('sph_option_content_save', 						                'sp_private_posts_admin_save_options');
add_filter('sph_admin_help-admin-options', 					                'sp_private_posts_admin_help', 10, 3);
add_action('sph_print_plugin_styles',							            'sp_private_posts_header');

add_filter('sph_plugins_active_buttons',    'sp_private_posts_uninstall_option', 10, 2);
add_filter('sph_perms_tooltips', 			'sp_private_posts_tooltips', 10, 2);
add_filter('sph_topic_options_add', 		'sp_private_posts_form_options', 99, 2);
add_filter('sph_post_options_add', 			'sp_private_posts_form_options', 99, 2);
add_filter('sph_topicview_query', 			'sp_private_posts_topic_query');
add_filter('sph_topicview_post_records', 	'sp_private_posts_post_records', 10, 2);
add_filter('sph_add_post_tool', 	        'sp_private_posts_post_tool', 10, 10);
add_filter('sph_auth_view_post_content',    'sp_private_posts_view_content', 20, 7);
add_filter('sph_uninstall_message',         'sp_private_posts_uninstall_message', 10, 2);

function sp_private_posts_admin_options() {
	require_once SPPRIVATEPOSTSADMINDIR.'sp-private-posts-admin-options.php';
	sp_private_posts_do_admin_options();
}

function sp_private_posts_admin_save_options() {
	require_once SPPRIVATEPOSTSADMINDIR.'sp-private-posts-admin-options-save.php';
	sp_private_posts_do_admin_save_options();
}

function sp_private_posts_admin_help($file, $tag, $lang) {
	if ($tag == '[private-posts]') $file = SPPRIVATEPOSTSADMINDIR.'sp-private-posts-admin-help.'.$lang;
	return $file;
}

function sp_private_posts_localization() {
	sp_plugin_localisation('sp-private-posts');
}

function sp_private_posts_install() {
    require_once SPPRIVATEPOSTSDIR.'sp-private-posts-install.php';
    sp_private_posts_do_install();
}

function sp_private_posts_deactivate() {
    require_once SPPRIVATEPOSTSDIR.'sp-private-posts-uninstall.php';
    sp_private_posts_do_deactivate();
}

function sp_private_posts_uninstall() {
    require_once SPPRIVATEPOSTSDIR.'sp-private-posts-uninstall.php';
    sp_private_posts_do_uninstall();
}

function sp_private_posts_sp_activate() {
	require_once SPPRIVATEPOSTSDIR.'sp-private-posts-install.php';
    sp_private_posts_do_sp_activate();
}

function sp_private_posts_sp_deactivate() {
	require_once SPPRIVATEPOSTSDIR.'sp-private-posts-uninstall.php';
    sp_private_posts_do_sp_deactivate();
}

function sp_private_posts_sp_uninstall() {
	require_once SPPRIVATEPOSTSDIR.'sp-private-posts-uninstall.php';
    sp_private_posts_do_sp_uninstall();
}

function sp_private_posts_upgrade_check() {
    require_once SPPRIVATEPOSTSDIR.'sp-private-posts-upgrade.php';
    sp_private_posts_do_upgrade_check();
}

function sp_private_posts_uninstall_option($actionlink, $plugin) {
    require_once SPPRIVATEPOSTSDIR.'sp-private-posts-uninstall.php';
    $actionlink = sp_private_posts_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_private_posts_reset_permissions() {
    require_once SPPRIVATEPOSTSDIR.'sp-private-posts-install.php';
    sp_private_posts_do_reset_permissions();
}

function sp_private_posts_tooltips($tips, $t) {
    $tips['view_private_posts'] = $t.__('Can view posts marked as private', 'sp-private-posts');
    $tips['post_private'] = $t.__('Can make a private post', 'sp-private-posts');
    return $tips;
}

function sp_private_posts_form_options($content, $thisObject) {
    require_once SPPRIVATEPOSTSLIBDIR.'sp-private-posts-components.php';
	$content = sp_private_posts_do_form_options($content, $thisObject);
	return $content;
}

function sp_private_posts_save_post($newpost) {
    require_once SPPRIVATEPOSTSLIBDIR.'sp-private-posts-components.php';
	sp_private_posts_do_save_post($newpost);
}

function sp_private_posts_topic_query($query) {
	$query->fields.= ', '.SPPOSTS.'.private';
	return $query;
}

function sp_private_posts_post_records($data, $records) {
    require_once SPPRIVATEPOSTSLIBDIR.'sp-private-posts-components.php';
	$data = sp_private_posts_do_post_records($data, $records);
    return $data;
}

function sp_private_posts_post_tool($out, $post, $forum, $topic, $page, $postnum, $useremail, $guestemail, $displayname, $br) {
    require_once SPPRIVATEPOSTSLIBDIR.'sp-private-posts-components.php';
	$out = sp_private_posts_do_post_tool($out, $post, $forum, $topic, $page, $postnum, $useremail, $guestemail, $displayname, $br);
    return $out;
}

function sp_private_posts_process_actions() {
    require_once SPPRIVATEPOSTSLIBDIR.'sp-private-posts-components.php';
	sp_private_posts_do_process_actions();
}

function sp_prviate_posts_add_private_class($args) {
    require_once SPPRIVATEPOSTSLIBDIR.'sp-private-posts-components.php';
	$args = sp_prviate_posts_do_add_private_class($args);
    return $args;
}

function sp_private_posts_header() {
	$css = SP()->theme->find_css(SPPRIVATEPOSTSCSS, 'sp-private-posts.css', 'sp-private-posts.spcss');
    SP()->plugin->enqueue_style('sp-private-posts', $css);
}

function sp_private_posts_view_content($auth, $forumid, $view, $userid, $posterid, $topicid, $postid) {
    require_once SPPRIVATEPOSTSLIBDIR.'sp-private-posts-components.php';
	$auth = sp_private_posts_do_view_content($auth, $forumid, $view, $userid, $posterid, $topicid, $postid);
    return $auth;
}

function sp_private_posts_uninstall_message($msg, $plugin) {
    require_once SPPRIVATEPOSTSDIR.'sp-private-posts-uninstall.php';
    $msg = sp_private_posts_do_uninstall_message($msg, $plugin);
	return $msg;
}
