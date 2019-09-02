<?php
/*
Simple:Press Plugin Title: Topic Redirect
Version: 2.1.0
Itemd Id: 3968
Plugin URI: https://simple-press.com/downloads/topic-redirect-plugin/
Description: A Simple:Press plugin for creating a forum topic which is a simple redirect to a url.
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPREDIRECTDBVERSION', 0);

define('SPREDIRECTDIR',			SPPLUGINDIR.'topic-redirect/');
define('SPREDIRECTIMAGES',		SPPLUGINURL.'topic-redirect/resources/images/');
define('SPREDIRECTSCRIPTS',		SPPLUGINURL.'topic-redirect/resources/jscript/');
define('SPREDIRECTLIBDIR',		SPPLUGINDIR.'topic-redirect/library/');

add_action('init',														'sp_redirect_localization');
add_action('sph_activate_topic-redirect/sp-redirect-plugin.php',		'sp_redirect_install');
add_action('sph_deactivate_topic-redirect/sp-redirect-plugin.php',		'sp_redirect_deactivate');
add_action('sph_uninstall_topic-redirect/sp-redirect-plugin.php',		'sp_redirect_uninstall');
add_action('sph_plugin_update_topic-redirect/sp-redirect-plugin.php',	'sp_redirect_upgrade_check');
add_action('admin_footer',												'sp_redirect_upgrade_check');
add_action('sph_permissions_reset',										'sp_redirect_reset_permissions');
add_action('sph_print_plugin_scripts', 							        'sp_redirect_load_js');

add_filter('sph_plugins_active_buttons',								'sp_redirect_uninstall_option', 10, 2);

# Group View Query and Content
add_filter('sph_groupview_stats_query',									'sp_redirect_add_query');
add_filter('sph_groupview_stats_records', 								'sp_redirect_groupview_records', 10, 2);
add_filter('sph_ForumIndexLastPost', 									'sp_redirect_groupview_title');

# Forum View Query and Content
add_filter('sph_forumview_query', 										'sp_redirect_add_query');
add_filter('sph_forumview_topic_records', 								'sp_redirect_forumview_records', 10, 2);
add_filter('sph_TopicIndexName',										'sp_redirect_topicview_title');

add_filter('sph_TopicIndexPostCount',									'sp_redirect_forumview_remove', 10, 2);
add_filter('sph_TopicIndexReplyCount',									'sp_redirect_forumview_remove', 10, 2);
add_filter('sph_TopicIndexStatusIcons',									'sp_redirect_forumview_remove', 10, 2);
add_filter('sph_TopicIndexLastPost',									'sp_redirect_forumview_remove', 10, 2);
add_filter('sph_TopicIndexFirstPost',									'sp_redirect_forumview_first_post', 10, 3);

# Topic View query and content
add_filter('sph_topicview_query', 										'sp_redirect_add_query');
add_filter('sph_topicview_topic_record', 								'sp_redirect_forumview_records', 10, 2);

# List Topic Query
add_filter('sph_topic_list_query', 										'sp_redirect_add_query');
add_filter('sph_topic_list_record',										'sp_redirect_set_permalink', 10, 2);

# List Post query
add_filter('sph_post_list_query',										'sp_redirect_postlist_query');

# Add Topic Options Box
add_filter('sph_topic_options_add', 									'sp_redirect_add_topic_option', 1, 2);

# Save Post record
add_filter('sph_save_post_content_filter',								'sp_redirect_filter_url', 10, 3);
add_filter('sph_new_topic_data',										'sp_redirect_add_save_fields');
add_action('sph_post_new_completed',									'sp_redirect_redirectUrl');

# Perform Redirect
add_action('sph_BeforeDisplayEnd',										'sp_redirect_perform_redirect', 1, 2);

add_filter('sph_perms_tooltips', 						                'sp_redirect_tooltips', 10, 2);

function sp_redirect_localization() {
	sp_plugin_localisation('sp-redirect');
}

function sp_redirect_install() {
	require_once SPREDIRECTDIR.'sp-redirect-install.php';
	sp_redirect_do_install();
}

function sp_redirect_deactivate() {
	require_once SPREDIRECTDIR.'sp-redirect-uninstall.php';
	sp_redirect_do_deactivate();
}

function sp_redirect_uninstall() {
	require_once SPREDIRECTDIR.'sp-redirect-uninstall.php';
	sp_redirect_do_uninstall();
}

function sp_redirect_upgrade_check() {
	require_once SPREDIRECTDIR.'sp-redirect-upgrade.php';
	sp_redirect_do_upgrade_check();
}

function sp_redirect_uninstall_option($actionlink, $plugin) {
	require_once SPREDIRECTDIR.'sp-redirect-uninstall.php';
	$actionlink = sp_redirect_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_redirect_reset_permissions() {
	require_once SPREDIRECTDIR.'sp-redirect-install.php';
	sp_redirect_do_reset_permissions();
}

function sp_redirect_load_js($footer) {
    $script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPREDIRECTSCRIPTS.'sp-redirect.js' : SPREDIRECTSCRIPTS.'sp-redirect.min.js';
	SP()->plugin->enqueue_script('spredirect', $script, array('jquery'), false, $footer);
}

# ----------------------------------------------
# View Queries
# ----------------------------------------------
function sp_redirect_add_query($query) {
	require_once SPREDIRECTLIBDIR.'sp-redirect-components.php';
	return sp_do_redirect_add_query($query);
}

# ----------------------------------------------
# Group View Content
# ----------------------------------------------
function sp_redirect_groupview_records($data, $record) {
	require_once SPREDIRECTLIBDIR.'sp-redirect-components.php';
	$data = sp_do_redirect_groupview_records($data, $record);
	return $data;
}

function sp_redirect_groupview_title($out) {
	if (SP()->forum->view->thisForum->redirect) {
		require_once SPREDIRECTLIBDIR.'sp-redirect-components.php';
		$out = sp_do_redirect_view_title('group', $out, SP()->forum->view->thisForum->topic_name);
	}
	return $out;
}

# ----------------------------------------------
# Forum View Query and Content
# ----------------------------------------------
function sp_redirect_forumview_records($data, $record) {
	require_once SPREDIRECTLIBDIR.'sp-redirect-components.php';
	return sp_do_redirect_forumview_records($data, $record);
}

function sp_redirect_forumview_remove($out, $a) {
	if (SP()->forum->view->thisTopic->redirect) {
		require_once SPREDIRECTLIBDIR.'sp-redirect-components.php';
		$out = sp_do_redirect_forumview_remove($out, $a);
	}
	return $out;
}

function sp_redirect_forumview_first_post($out, $a, $label) {
	 if (SP()->forum->view->thisTopic->redirect) {
		require_once SPREDIRECTLIBDIR.'sp-redirect-components.php';
		$out = sp_do_redirect_forumview_first_post($out, $a, $label);
	}
	return $out;
}

# ----------------------------------------------
# List Topic View Content
# ----------------------------------------------
function sp_redirect_set_permalink($data, $record) {
	if ($record->redirect) {
		require_once SPREDIRECTLIBDIR.'sp-redirect-components.php';
		$data = sp_do_redirect_set_permalink($data, $record);
	}
	return $data;
}

function sp_redirect_topicview_title($out) {
	if (SP()->forum->view->thisTopic->redirect) {
		require_once SPREDIRECTLIBDIR.'sp-redirect-components.php';
		$out = sp_do_redirect_view_title('topic', $out, SP()->forum->view->thisTopic->topic_name);
	}
	return $out;
}

# ----------------------------------------------
# List Posts View Query
# ----------------------------------------------
function sp_redirect_postlist_query($query) {
	require_once SPREDIRECTLIBDIR.'sp-redirect-components.php';
	return sp_do_redirect_postlist_query($query);
}

# ----------------------------------------------
# Add Topic Options Box
# ----------------------------------------------
function sp_redirect_add_topic_option($optionsBox, $forum) {
	if (SP()->auths->get('create_topic_redirects', $forum->forum_id)) {
		require_once SPREDIRECTLIBDIR.'sp-redirect-components.php';
		$optionsBox = sp_do_redirect_add_topic_option($optionsBox, $forum);
	}
	return $optionsBox;
}

# ----------------------------------------------
# Add Topic Save
# ----------------------------------------------
function sp_redirect_filter_url($content, $original, $action) {
	if (isset($_POST['spRedirect'])) {
		require_once SPREDIRECTLIBDIR.'sp-redirect-components.php';
		$content = sp_do_redirect_filter_url($content, $original, $action);
	}
	return $content;
}

function sp_redirect_add_save_fields($query) {
	if (isset($_POST['spRedirect'])) {
		require_once SPREDIRECTLIBDIR.'sp-redirect-components.php';
		$query = sp_do_redirect_add_save_fields($query);
	}
	return $query;
}

function sp_redirect_redirectUrl($data) {
	if (isset($_POST['spRedirect'])) {
		require_once SPREDIRECTLIBDIR.'sp-redirect-components.php';
		sp_do_redirect_redirectUrl($data);
	}
}

# ----------------------------------------------
# Do the redirect
# ----------------------------------------------
function sp_redirect_perform_redirect($pageview, $template) {
	if ($pageview == 'topic') {
		if (!empty(SP()->forum->view->thisTopic->redirect)) {
			sp_do_redirect_perform_redirect($pageview, $template);
		}
	}
}

function sp_redirect_tooltips($tips, $t) {
    $tips['create_topic_redirects'] = $t.__('Can create redirects for new topics', 'sp-redirect');
    return $tips;
}
