<?php
/*
Simple:Press Plugin Title: Topic Status
Version: 3.1.0
Item Id: 3947
Plugin URI: https://simple-press.com/downloads/topic-status-plugin/
Description: Assign a status to topics from a selected list
Author: Simple:Press
Original Authors: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2019-02-20 18:38:47 -0600 (Wed, 20 Feb 2019) $
$Rev: 15870 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPTSDBVERSION', 2);

# ======================================
# CONSTANTS USED BY THIS PLUGIN
# ======================================
define('SPTSDIR',		SPPLUGINDIR.'topic-status/');
define('SPTSURL',		SPPLUGINURL.'topic-status/');
define('SPTSCSS', 		SPTSURL.'resources/css/');
define('SPTSLIB',		SPTSDIR.'library/sp-topicstatus-support.php');
define('SPTSTAGS',		SPTSDIR.'template-tags/sp-topicstatus-template-tags.php');
define('SPTSAJAX',		SPTSDIR.'ajax/sp-ajax-topicstatus-change.php');
define('SPTSIMAGES',	SPTSURL.'resources/images/');
define('SPTSIMAGESMOB',	SPTSURL.'resources/images/mobile/');
define('SPTSSCRIPT', 	SPPLUGINURL.'topic-status/resources/jscript/');

# ======================================
# ACTIONS/FILTERS USED BY THUS PLUGIN
# ======================================
# Plugin and admin
add_action('sph_activate_topic-status/sp-topicstatus-plugin.php',	     'sp_topicstatus_install');
add_action('sph_deactivate_topic-status/sp-topicstatus-plugin.php',      'sp_topicstatus_deactivate');
add_action('sph_uninstall_topic-status/sp-topicstatus-plugin.php', 	     'sp_topicstatus_uninstall');
add_filter('sph_plugins_active_buttons',						  	     'sp_topicstatus_uninstall_option', 10, 2);
add_action('init', 													     'sp_topicstatus_localisation');
add_action('sph_admin_menu',										     'sp_topicstatus_admin_menu');
add_filter('sph_admin_help-admin-components', 						     'sp_topicstatus_admin_help', 10, 3);
add_action('sph_forum_create_forum_options',						     'sp_topicstatus_add_ts_field');
add_action('sph_forum_edit_forum_options',							     'sp_topicstatus_edit_ts_field');
add_action('sph_forum_forum_create',								     'sp_topicstatus_save_forum');
add_action('sph_forum_forum_edit',									     'sp_topicstatus_save_forum');
add_action('sph_plugin_update_topic-status/sp-topic-status-plugin.php',  'sp_topicstatus_upgrade_check');
add_action('admin_footer',                                               'sp_topicstatus_upgrade_check');
add_action('sph_permissions_reset',                                      'sp_topicstatus_reset_permissions');

add_action('sph_print_plugin_styles', 								'sp_topicstatus_header');
add_action('admin_enqueue_scripts',									'sp_topic_status_load_js');
add_filter('sph_load_page_data',									'sp_topicstatus_load_page_data', 1);

add_filter('sph_forumview_query', 									'sp_topicstatus_forum_query');
add_filter('sph_forumview_forum_record',							'sp_topicstatus_forum_records', 10, 2);
add_filter('sph_forumview_topic_records', 							'sp_topicstatus_forum_topic_records', 10, 2);
add_filter('sph_topicview_query',									'sp_topicstatus_topic_query');
add_filter('sph_topicview_topic_record',							'sp_topicstatus_topic_records', 10, 2);
add_filter('sph_post_options_add',									'sp_topicstatus_add_ts_post_form', 99, 2);
add_filter('sph_new_post_data_saved',								'sp_topicstatus_post_change_status');
add_filter('sph_topic_options_add',								    'sp_topicstatus_add_ts_topic_form', 99, 2);
add_filter('sph_new_topic_data',									'sp_topicstatus_add_first_status');

add_filter('sph_add_topic_tool', 									'sp_topicstatus_topic_tool', 10, 5);
add_action('sph_setup_forum',										'sp_topicstatus_change_listen');
add_filter('sph_search_query', 										'sp_topicstatus_search_join', 1, 4);
add_filter('sph_search_type_where', 								'sp_topicstatus_search_type', 1, 4);
add_filter('sph_search_label', 										'sp_topicstatus_search_label', 1, 4);
add_filter('sph_perms_tooltips', 				                    'sp_topicstatus_tooltips', 10, 2);

add_filter('sph_topic_editor_toolbar',			     'sp_topic_editor_smileys_options', 1, 4);

add_filter('sph_topic_editor_display_options',                    'sph_topic_editor_display_options_function', 2);

# Ajax Handler
add_action('wp_ajax_topicstatus',				'sp_topicstatus_change_ajax');
add_action('wp_ajax_nopriv_topicstatus',		'sp_topicstatus_change_ajax');


# ======================================
# PLUGIN AND ADMIN
# ======================================


# ----------------------------------------------
# Add filter for status options
# ----------------------------------------------
function sph_topic_editor_display_options_function($display){
	if(SP()->user->thisUser->guest){	
		$display['options'] = false;
	}else{
		$display['options'] = true;
	}
	return $display;
}

# ----------------------------------------------
# Run Install Script on Activation action
# ----------------------------------------------
function sp_topicstatus_install() {
	require_once SPTSDIR.'sp-topicstatus-install.php';
	sp_topicstatus_do_install();
}

# ----------------------------------------------
# Run Deactivate Script on Deactivation action
# ----------------------------------------------
function sp_topicstatus_deactivate() {
	require_once SPTSDIR.'sp-topicstatus-uninstall.php';
	sp_topicstatus_do_deactivate();
}

# ----------------------------------------------
# Run Uninstall Script on Uninstall action
# ----------------------------------------------
function sp_topicstatus_uninstall() {
	require_once SPTSDIR.'sp-topicstatus-uninstall.php';
	sp_topicstatus_do_uninstall();
}

function sp_topicstatus_upgrade_check() {
    require_once SPTSDIR.'sp-topicstatus-upgrade.php';
    sp_topicstatus_do_upgrade_check();
}

function sp_topicstatus_reset_permissions() {
	require_once SPTSDIR.'sp-topicstatus-install.php';
	sp_topicstatus_do_reset_permissions();
}

# ------------------------------------------------------
# Add the 'Uninstall' and 'Options' link to plugins list
# ------------------------------------------------------
function sp_topicstatus_uninstall_option($actionlink, $plugin) {
	if ($plugin == 'topic-status/sp-topicstatus-plugin.php') {
		$url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
		$actionlink.= "&nbsp;&nbsp;<a href='".$url."' title='".__("Uninstall this plugin", "sp-tstatus")."'>".__("Uninstall", "sp-tstatus")."</a>";

		$url = SPADMINCOMPONENTS.'&amp;tab=plugin&amp;admin=spa_topicstatus_admin_form&amp;save=spa_topicstatus_admin_save&amp;form=1';
		$actionlink.= "&nbsp;&nbsp;<a href='".$url."' title='".__("Options", "sp-tstatus")."'>".__("Options", "sp-tstatus")."</a>";
	}
	return $actionlink;
}

# ------------------------------------------------------
# Set up language file
# ------------------------------------------------------
function sp_topicstatus_localisation() {
	sp_plugin_localisation('sp-tstatus');
}

# ----------------------------------------------
# Add Linking Admin Panel to Components
# ----------------------------------------------
function sp_topicstatus_admin_menu() {
	$subpanels = array(
		__("Topic Status", "sp-tstatus") => array('admin' => 'spa_topicstatus_admin_form', 'save' => 'spa_topicstatus_admin_save', 'form' => 1, 'id' => 'sfreloadts')
	);
	SP()->plugin->add_admin_subpanel('components', $subpanels);
}

# ----------------------------------------------
# Action the Blog Linking admin panel popup help
# ----------------------------------------------
function sp_topicstatus_admin_help($file, $tag, $lang) {
    if ($tag == '[topic-status-sets]') $file = SPTSDIR.'admin/spa-topicstatus-admin-help.'.$lang;
    return $file;
}

# ----------------------------------------------
# Add permission tooltips to permissions panels
# ----------------------------------------------
function sp_topicstatus_tooltips($tips, $t) {
    $tips['change_topic_status'] = $t.__('Can change the status of a topic', 'sp-tstatus');
    return $tips;
}

# ----------------------------------------------
# Load the Linking Options Admin Form
# ----------------------------------------------
function spa_topicstatus_admin_form() {
	require_once SPTSDIR.'admin/spa-components-topicstatus-form.php';
	spa_components_topicstatus_options();
}

# ----------------------------------------------
# Save the Linking Options Admin Form Data
# ----------------------------------------------
function spa_topicstatus_admin_save() {
	require_once SPTSDIR.'admin/spa-components-topicstatus-save.php';
	return spa_topicstatus_options_save();
}


# ======================================
# TOPIC STATUS PROCESSING/ADMIN
# ======================================

# ----------------------------------------------
# Adds TS selection to create/edit forum
# ----------------------------------------------
function sp_topicstatus_add_ts_field() {
	require_once SPTSLIB;
	sp_do_topicstatus_add_ts_field();
}

function sp_topicstatus_edit_ts_field($forum) {
	require_once SPTSLIB;
	sp_do_topicstatus_edit_ts_field($forum);
}

# ----------------------------------------------
# Save TS selection to create/edit forum
# ----------------------------------------------
function sp_topicstatus_save_forum($forumid) {
	require_once SPTSLIB;
	sp_do_topicstatus_save_forum($forumid);
}

# ----------------------------------------------
# Create the topic status map in spGlobals
# ----------------------------------------------
function sp_topicstatus_load_page_data($data) {
	$spMeta = SP()->meta->get('topic-status-set');
	if ($spMeta) {
		foreach ($spMeta as $s) {
			$data['topic-status-map'][$s['meta_id']] = $s['meta_key'];
		}
	}
	return $data;
}

# ----------------------------------------------
# Add in TS css file
# ----------------------------------------------
function sp_topicstatus_header() {
	require_once SPTSLIB;
	sp_do_topicstatus_header();
}
# ----------------------------------------------
# Add in TS js file
# ----------------------------------------------
function sp_topic_status_load_js(){	
	$script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPTSSCRIPT.'sp-topic-status-admin.js' : SPTSSCRIPT.'sp-topic-status-admin.min.js';
	wp_enqueue_script( 'spdefaulttopicstatus',$script, $in_footer = true );
}
# ----------------------------------------------
# View Class queries
# ----------------------------------------------
function sp_topicstatus_forum_query($query) {
	$query->fields.= ', topic_status_set, topic_status_flag';
	return $query;
}

function sp_topicstatus_forum_records($data, $record) {
	global $tss;
	require_once SPTSLIB;
	$data->topic_status_set = $record->topic_status_set;
	$data->topic_status_set_name = sp_topicstatus_set_name($record->topic_status_set);
	$tss = $record->topic_status_set;
	return $data;
}

function sp_topicstatus_forum_topic_records($data, $record) {
	require_once SPTSLIB;
	global $tss;
	$data->topic_status_flag = $record->topic_status_flag;
	$data->topic_status_flag_name = sp_topicstatus_flag_name($tss, $record->topic_status_flag);
	return $data;
}

function sp_topicstatus_topic_query($query) {
	$query->fields.= ', topic_status_set, topic_status_flag';
	return $query;
}

function sp_topicstatus_topic_records($data, $record) {
	require_once SPTSLIB;
	$data->topic_status_set = $record->topic_status_set;
	$data->topic_status_set_name = sp_topicstatus_set_name($record->topic_status_set);
	$data->topic_status_flag = $record->topic_status_flag;
	$data->topic_status_flag_name = sp_topicstatus_flag_name($record->topic_status_set, $record->topic_status_flag);
	return $data;
}

# ----------------------------------------------
# Add Topic/Add Post forms
# ----------------------------------------------
function sp_topicstatus_add_first_status($query) {
	require_once SPTSLIB;
	$query->fields[] = 'topic_status_flag';
	$query->data[] = sp_topicstatus_get_key(array_search('forum_id', $query->fields), 1);
	return $query;
}

function sp_topicstatus_add_ts_post_form($out, $topic) {
	return $out.sp_do_topicstatus_add_ts_post_form($out, $topic);
}

function sp_topicstatus_add_ts_topic_form($out, $forum) {
	require_once SPTSLIB;
	return $out.sp_do_topicstatus_add_ts_topic_form($out, $forum);
}

function sp_topicstatus_post_change_status($newpost) {
	require_once SPTSLIB;
	sp_do_topicstatus_post_change_status($newpost);
	return $newpost;
}

# ----------------------------------------------
# Change topic status - topic edit tool
# ----------------------------------------------
function sp_topicstatus_topic_tool($out, $topic, $forum, $page, $br) {
	require_once SPTSLIB;
	return $out.sp_do_topicstatus_topic_tool($topic, $forum, $page, $br);
}

function sp_topicstatus_change_ajax() {
	require_once SPTSAJAX;
}

function sp_topicstatus_change_listen() {
	require_once SPTSLIB;
	if (isset($_POST['makestatuschange'])) {
        $newpost = array();
        $newpost['topicid'] = SP()->filters->integer($_POST['topicid']);
		sp_do_topicstatus_post_change_status($newpost);
		SP()->notifications->message(0, __('Topic status changed', 'sp-tstatus'));
	}
}

# ----------------------------------------------
# Search filters
# ----------------------------------------------
function sp_topicstatus_search_join($query, $term, $type, $include) {
	if ($type == 10) {
		$query->join[] = SPFORUMS.' ON '.SPTOPICS.'.forum_id = '.SPFORUMS.'.forum_id';
        $query->fields = SPTOPICS.'.topic_id';
        $query->orderby = SPTOPICS.'.topic_id';
	}
	return $query;
}

function sp_topicstatus_search_type($where, $term, $type, $include) {
	if ($type == 10) {
		$where = "topic_status_flag='$term'";
		if (isset($_GET['set'])) {
			$set = (int) $_GET['set'];
			$where.= " AND ".SPFORUMS.".topic_status_set=$set";
		}
	}
	return $where;
}

function sp_topicstatus_search_label($label, $type, $include, $term) {
	if ($type == 10) {
		if (isset($_GET['set'])) {
			$set = (int) $_GET['set'];
			$set = sp_topicstatus_flag_name($set, trim($term, "'"));
			$label = __('Search results for status', 'sp-tstatus').": '".$set."'";
		}
	}
	return $label;
}

# ======================================
# TEMPLATE TAGS
# ======================================

# ----------------------------------------------
# ForunView - In row Topic Status
# ----------------------------------------------
function sp_TopicIndexTopicStatus($args='', $toolTip='') {
	require_once SPTSLIB;
    require_once SPTSTAGS;
	sp_TopicIndexTopicStatusTag($args, $toolTip);
}

# ----------------------------------------------
# TopicView - Anywhere
# ----------------------------------------------
function sp_TopicStatus($args='', $label='', $toolTip='') {
	require_once SPTSLIB;
    require_once SPTSTAGS;
	sp_TopicStatusTag($args, $label, $toolTip);
}