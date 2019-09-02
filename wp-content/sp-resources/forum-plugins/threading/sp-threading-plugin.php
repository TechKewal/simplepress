<?php
/*
Simple:Press Plugin Title: Threading
Version: 2.1.0
Item Id: 60463
Plugin URI: https://simple-press.com/downloads/threading/
Description: A Simple:Press plugin allowing topics to be threaded instead of flat
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2015-03-05 11:43:07 +0000 (Thu, 05 Mar 2015) $
$Rev: 12545 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPTHREADDBVERSION', 0);

define('SPTHREADDIR',		SPPLUGINDIR.'threading/');
define('SPTHREADADMINDIR',	SPPLUGINDIR.'threading/admin/');
define('SPTHREADAJAXDIR',	SPPLUGINDIR.'threading/ajax/');
define('SPTHREADLIBDIR',	SPPLUGINDIR.'threading/library/');
define('SPTHREADJS',		SPPLUGINURL.'threading/resources/jscript/');
define('SPTHREADCSS',		SPPLUGINURL.'threading/resources/css/');
define('SPTHREADIMAGES',	SPPLUGINURL.'threading/resources/images/');
define('SPTHREADIMAGESMOB', SPPLUGINURL.'threading/resources/images/mobile/');
define('SPTHREADTAGSDIR',	SPPLUGINDIR.'threading/template-tags/');

# The admin bits -----------------------------------------------

add_action('init',												'sp_threading_localization');
add_action('sph_activate_threading/sp-threading-plugin.php',	'sp_threading_install');
add_action('sph_uninstall_threading/sp-threading-plugin.php',	'sp_threading_uninstall');
add_filter('sph_plugins_active_buttons',						'sp_threading_uninstall_option', 10, 2);
add_action('sph_deactivate_threading/sp-threading-plugin.php',	'sp_threading_deactivate');
add_action('sph_activated',										'sp_threading_sp_activate');
add_action('sph_deactivated',									'sp_threading_sp_deactivate');
add_action('sph_uninstalled',									'sp_threading_sp_uninstall');
add_action('sph_plugin_update_threading/sp-threading-plugin.php',  'sp_threading_upgrade_check');
add_action('admin_footer',										'sp_threading_upgrade_check');

add_action('sph_options_global_right_panel',					'sp_threading_admin_options');
add_action('sph_option_global_save',							'sp_threading_admin_save_options');
add_action('sph_options_display_right_panel',					'sp_threading_disable_sort');
add_filter('sph_admin_help-admin-options',						'sp_threading_admin_help', 10, 3);

# The front end bits --------------------------------------------

add_action('sph_print_plugin_scripts',		'sp_threading_load_js');
add_action('sph_print_plugin_styles',		'sp_threading_head');

add_filter('sph_ProfileUserPostDESC', 		'sp_threading_profile_hide_sort', 10, 3);
add_filter('sph_post_tool_order',			'sp_threading_profile_hide_sort', 10, 3);

add_filter('sph_new_post_data',				'sp_threading_add_save', 10, 2);
add_filter('sph_postindex_select',			'sp_threading_postindex_select', 10);
add_filter('sph_topicview_query',			'sp_threading_topic_query');
add_filter('sph_topicview_post_records',	'sp_threading_topic_records', 10, 2);
add_filter('sph_listview_newposts_query',	'sp_threading_list_newposts_query');
add_filter('sph_SectionStartRowClass',		'sp_threading_postRowClass', 10, 3);
add_filter('sph_SectionStart',				'sp_threading_postRowIndent', 10, 3);
add_filter('sph_new_post_pre_data_saved',	'sp_threading_prepare_post_data');
add_action('sph_setup_forum',				'sp_threading_process_delete');
add_filter('sph_post_tool_move', 			'sp_threading_move_post_menu', 1, 5);
add_filter('sph_post_tool_delete',			'sp_threading_delete_post_menu', 1, 5);
add_action('sph_build_post_index',			'sp_threading_rebuild_indexes');

# Ajax Handler
add_action('wp_ajax_sp-thread-tools',			'sp_threading_ajax_tools');
add_action('wp_ajax_nopriv_sp-thread-tools',	'sp_threading_ajax_tools');
add_action('wp_ajax_sp-thread-move',			'sp_threading_ajax_move');
add_action('wp_ajax_nopriv_sp-thread-move',		'sp_threading_ajax_move');


# Install, Uninstall, Setup Etc. ------------------------

function sp_threading_install() {
	require_once SPTHREADDIR.'sp-threading-install.php';
	sp_threading_do_install();
}

function sp_threading_uninstall() {
	require_once SPTHREADDIR.'sp-threading-uninstall.php';
	sp_threading_do_uninstall();
}

function sp_threading_deactivate() {
	require_once SPTHREADDIR.'sp-threading-uninstall.php';
	sp_threading_do_deactivate();
}

function sp_threading_sp_activate() {
	require_once SPTHREADDIR.'sp-threading-install.php';
	sp_threading_do_sp_activate();
}

function sp_threading_sp_deactivate() {
	require_once SPTHREADDIR.'sp-threading-uninstall.php';
	sp_threading_do_sp_deactivate();
}

function sp_threading_sp_uninstall() {
	require_once SPTHREADDIR.'sp-threading-uninstall.php';
	sp_threading_do_sp_uninstall();
}

function sp_threading_uninstall_option($actionlink, $plugin) {
	require_once SPTHREADDIR.'sp-threading-uninstall.php';
	$actionlink = sp_threading_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_threading_upgrade_check() {
	require_once SPTHREADDIR.'sp-threading-upgrade.php';
	sp_threading_do_upgrade_check();
}

# Admin Options -----------------------------------------

function sp_threading_admin_options() {
	require_once SPTHREADADMINDIR.'sp-threading-admin-options.php';
	sp_threading_admin_options_form();
}

function sp_threading_admin_save_options() {
	require_once SPTHREADADMINDIR.'sp-threading-admin-options-save.php';
	return sp_threading_admin_options_save();
}

function sp_threading_admin_help($file, $tag, $lang) {
	if ($tag == '[threading]') $file = SPTHREADADMINDIR.'sp-threading-admin-help.'.$lang;
	return $file;
}

# Admin Tools and Changes -------------------------------

function sp_threading_disable_sort() {
	require_once SPTHREADADMINDIR.'sp-threading-admin-support.php';
	sp_threading_do_disable_sort();
}

function sp_threading_profile_hide_sort($out, $x, $y) {
	return '';
}

# Loading support resources -------------------------------

function sp_threading_localization() {
	sp_plugin_localisation('sp-threading');
}

function sp_threading_load_js($footer) {
	$script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPTHREADJS.'sp-threading.js' : SPTHREADJS.'sp-threading.min.js';
	SP()->plugin->enqueue_script('spthreading', $script, array('jquery'), false, $footer);

	$strings = array(
		'replyLabel'	=> esc_js(__('Threaded Reply to Post', 'sp-threading')),
		'madebyLabel'	=> esc_js(__('made by', 'sp-threading')),
		'cancelverify'	=> esc_js(__('Are you sure you want to cancel this editor and open a new one?', 'sp-threading'))
	);
	SP()->plugin->localize_script('spthreading', 'sp_threading_vars', $strings);
}

function sp_threading_head() {
	$css = SP()->theme->find_css(SPTHREADCSS, 'sp-threading.css', 'sp-threading.spcss');
	SP()->plugin->enqueue_style('sp-threading', $css);
}

# Run-time suport functions ----------------------------------

# add field and change sort to topic view query
function sp_threading_topic_query($query) {
	$query->fields.= ', '.SPPOSTS.'.thread_index, '.SPPOSTS.'.thread_parent';
	# NOTE: This ignores sort settings - how can you sort DESC?
	$query->orderby = 'post_pinned DESC, '.SPPOSTS.".thread_index ASC";
	return $query;
}

# add new data to topic view class
function sp_threading_topic_records($data, $record) {
	$data->thread_index = $record->thread_index;
	$data->thread_parent = $record->thread_parent;
	$depth = explode('.', $record->thread_index);
	$data->thread_level = (count($depth)-1);
	return $data;
}

# change newpost post count from post_index to control_index
function sp_threading_list_newposts_query($query) {
	# I have no idea why str_replace did not work!
	$pos = strpos($query->fields, 'post_index-1');
	$query->fields = substr_replace($query->fields, 'control_index-1', $pos, 12);
	return $query;
}

# add a new class to post block for custom use of required
function sp_threading_postRowClass($rowClass, $sectionName, $a) {
	if ($sectionName == 'eachPost' || $sectionName == 'post') {
		require_once SPTHREADLIBDIR.'sp-threading-components.php';
		$rowClass.=sp_threading_do_postRowClass($rowClass, $sectionName, $a);
	}
	return $rowClass;
}

# add the indent markers at display post time
function sp_threading_postRowIndent($out, $sectionName, $a) {
	if ($sectionName == 'eachPost' || $sectionName == 'post') {
		require_once SPTHREADLIBDIR.'sp-threading-components.php';
		$out = sp_threading_do_postRowIndent() . $out;
	}
	return $out;
}

# Prepare and populate the threading data for new post
function sp_threading_prepare_post_data($postData) {
	require_once SPTHREADLIBDIR.'sp-threading-database.php';
	return sp_threading_do_prepare_post_data($postData);
}

# add the new field to the post save query statement
function sp_threading_add_save($query, $data) {
	$query->fields[] = 'thread_index';
	$query->fields[] = 'control_index';
	$query->data[] = $data['thread_index'];
	$query->data[] = $data['control_index'];
	return $query;
}

# rebuilding post indexing
function sp_threading_postindex_select($query) {
	$query->fields .= ', thread_index';
	$query->orderby = 'post_pinned DESC, control_index ASC';
	return $query;
}

# Rebuild post indexes - this to rebuild control_index
function sp_threading_rebuild_indexes($topicId) {
	require_once SPTHREADLIBDIR.'sp-threading-database.php';
	sp_threading_do_rebuild_indexes($topicId);
}

# process the delete action
function sp_threading_process_delete() {
	require_once SPTHREADLIBDIR.'sp-threading-database.php';
	sp_threading_do_process_delete();
}

# Delete post routines

function sp_threading_delete_post_menu($out, $post, $forum, $topic, $page) {
	require_once SPTHREADADMINDIR.'sp-threading-admin-support.php';
	return sp_threading_do_delete_post_menu($out, $post, $forum, $topic, $page);
}

# Move post routines

function sp_threading_move_post_menu($out, $post, $forum, $topic, $page) {
	require_once SPTHREADADMINDIR.'sp-threading-admin-support.php';
	return sp_threading_do_move_post_menu($out, $post, $forum, $topic, $page);
}

# Ajax management - Move post tool menu
function sp_threading_ajax_tools() {
	require_once SPTHREADAJAXDIR.'sp-threading-ajax-tools.php';
}

# Ajax management - move post handler
function sp_threading_ajax_move() {
	require_once SPTHREADAJAXDIR.'sp-threading-ajax-move.php';
}

# define Template Tags globally available ----------------------------

function sp_PostIndexThreadedReply($args='', $label='', $toolTip='') {
	require_once SPTHREADTAGSDIR.'sp-threading-tags.php';
	sp_PostIndexThreadedReplyTag($args, $label, $toolTip);
}

function sp_PostIndexDeleteThread($args='', $threadLabel='', $standardLabel='', $threadToolTip='', $standardToolTip='') {
	require_once SPTHREADTAGSDIR.'sp-threading-tags.php';
	sp_PostIndexDeleteThreadTag($args, $threadLabel, $standardLabel, $threadToolTip, $standardToolTip);
}
