<?php
/*
Simple:Press Plugin Title: Topic Description
Version: 2.1.0
Item Id: 3966
Plugin URI: https://simple-press.com/downloads/topic-description-plugin/
Description: A Simple:Press plugin for allowing users to add an optional topic description when creating new topics
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPTDDBVERSION', 0);

define('SPTDDIR', 		SPPLUGINDIR.'topic-description/');
define('SPTDADMINDIR', 	SPPLUGINDIR.'topic-description/admin/');
define('SPTDAJAXDIR', 	SPPLUGINDIR.'topic-description/ajax/');
define('SPTDLIBDIR', 	SPPLUGINDIR.'topic-description/library/');
define('SPTDLIBURL', 	SPPLUGINURL.'topic-description/library/');
define('SPTDCSS', 		SPPLUGINURL.'topic-description/resources/css/');
define('SPTDSCRIPT', 	SPPLUGINURL.'topic-description/resources/jscript/');
define('SPTDIMAGES', 	SPPLUGINURL.'topic-description/resources/images/');
define('SPTDTAGSDIR', 	SPPLUGINDIR.'topic-description/template-tags/');

add_action('init', 												                   'sp_topic_description_localization');
add_action('sph_activate_topic-description/sp-topic-description-plugin.php', 	   'sp_topic_description_install');
add_action('sph_uninstall_topic-description/sp-topic-description-plugin.php',      'sp_topic_description_uninstall');
add_action('sph_deactivate_topic-description/sp-topic-description-plugin.php',     'sp_topic_description_deactivate');
add_action('sph_activated', 				                                       'sp_topic_description_sp_activate');
add_action('sph_deactivated', 				                                       'sp_topic_description_sp_deactivate');
add_action('sph_uninstalled', 								                       'sp_topic_description_sp_uninstall');
add_action('sph_plugin_update_topic-description/sp-topic-description-plugin.php',  'sp_topic_description_upgrade_check');
add_action('admin_footer',                                                         'sp_topic_description_upgrade_check');
add_action('sph_print_plugin_styles',							                   'sp_topic_description_head');
add_action('sph_post_create',                                                      'sp_topic_description_create_topic');
add_action('sph_topic_title_edited',                                               'sp_topic_description_edit_topic_desc');

add_filter('sph_plugins_active_buttons',    'sp_topic_description_uninstall_option', 10, 2);
add_filter('sph_page_data_topic',			'sp_topic_description_page_data', 10, 2);
add_filter('sph_topic_editor_name',         'sp_topic_description_topic_form', 10, 2);
add_filter('sph_TopicHeaderName',           'sp_topic_description_display_desc', 10, 2);
add_filter('sph_topicview_query', 			'sp_topic_description_query');
add_filter('sph_topicview_topic_record', 	'sp_topic_description_records', 10, 2);
add_filter('sph_forumview_query', 			'sp_topic_description_query');
add_filter('sph_forumview_topic_records', 	'sp_topic_description_records', 10, 2);
add_filter('sph_topic_title_edit',          'sp_topic_description_edit', 10, 2);

function sp_topic_description_localization() {
	sp_plugin_localisation('sp-topic-description');
}

function sp_topic_description_install() {
    require_once SPTDDIR.'sp-topic-description-install.php';
    sp_topic_description_do_install();
}

function sp_topic_description_uninstall() {
    require_once SPTDDIR.'sp-topic-description-uninstall.php';
    sp_topic_description_do_uninstall();
}

function sp_topic_description_deactivate() {
    require_once SPTDDIR.'sp-topic-description-uninstall.php';
    sp_topic_description_do_deactivate();
}

function sp_topic_description_sp_activate() {
	require_once SPTDDIR.'sp-topic-description-install.php';
    sp_topic_description_do_sp_activate();
}

function sp_topic_description_sp_deactivate() {
	require_once SPTDDIR.'sp-topic-description-uninstall.php';
    sp_topic_description_do_sp_deactivate();
}

function sp_topic_description_sp_uninstall() {
	require_once SPTDDIR.'sp-topic-description-uninstall.php';
    sp_topic_description_do_sp_uninstall();
}

function sp_topic_description_uninstall_option($actionlink, $plugin) {
    require_once SPTDDIR.'sp-topic-description-uninstall.php';
    $actionlink = sp_topic_description_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_topic_description_upgrade_check() {
    require_once SPTDDIR.'sp-topic-description-upgrade.php';
    sp_topic_description_do_upgrade_check();
}

function sp_topic_description_head() {
    require_once SPTDLIBDIR.'sp-topic-description-components.php';
    sp_topic_description_do_head();
}

function sp_topic_description_page_data($vars, $topic) {
	$vars['topicdesc'] = (empty($topic->topic_desc)) ? '' : $topic->topic_desc;
	return $vars;
}

function sp_topic_description_topic_form($out, $a) {
    require_once SPTDLIBDIR.'sp-topic-description-components.php';
    $out = sp_topic_description_do_topic_form($out, $a);
	return $out;
}

function sp_topic_description_create_topic($newpost) {
    require_once SPTDLIBDIR.'sp-topic-description-components.php';
    sp_topic_description_do_create_topic($newpost);
}

function sp_topic_description_query($query) {
	$query->fields.= ', topic_desc';
	return $query;
}

function sp_topic_description_records($data, $record) {
	$data->topic_desc = SP()->displayFilters->title($record->topic_desc);
	return $data;
}

function sp_topic_description_display_desc($out, $a) {
    require_once SPTDLIBDIR.'sp-topic-description-components.php';
    $out = sp_topic_description_do_display_desc($out, $a);
	return $out;
}

function sp_topic_description_edit($out, $topic){
    require_once SPTDLIBDIR.'sp-topic-description-components.php';
    $out = sp_topic_description_do_edit($out, $topic);
	return $out;
}

function sp_topic_description_edit_topic_desc($topicid) {
    require_once SPTDLIBDIR.'sp-topic-description-components.php';
    sp_topic_description_do_edit_topic_desc($topicid);
}

# Define Template Tags globally available (dont have to be enabled on template tag panel)

function sp_TopicDescription($args='') {
    require_once SPTDTAGSDIR.'sp-topic-description-tag.php';
	sp_do_TopicDescription($args);
}
