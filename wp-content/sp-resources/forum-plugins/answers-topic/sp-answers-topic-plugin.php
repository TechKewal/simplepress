<?php
/*
Simple:Press Plugin Title: Answers Topic
Version: 2.1.0
Item Id: 3945 
Plugin URI: https://simple-press.com/downloads/answers-topic-plugin/
Description: A Simple:Press plugin for indicating that a post answers the original post in topic
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPANSWERSDBVERSION', 0);

define('SPANSWERSDIR', 		    SPPLUGINDIR.'answers-topic/');
define('SPANSWERSLIBDIR', 	    SPPLUGINDIR.'answers-topic/library/');
define('SPANSWERSIMAGES', 	    SPPLUGINURL.'answers-topic/resources/images/');
define('SPANSWERSIMAGESMOB', 	SPPLUGINURL.'answers-topic/resources/images/mobile/');
define('SPANSWERSTAGS', 	    SPPLUGINDIR.'answers-topic/template-tags/');

add_action('init', 										                   'sp_answers_topic_localization');
add_action('sph_activate_answers-topic/sp-answers-topic-plugin.php',       'sp_answers_topic_install');
add_action('sph_deactivate_answers-topic/sp-answers-topic-plugin.php',     'sp_answers_topic_deactivate');
add_action('sph_uninstall_answers-topic/sp-answers-topic-plugin.php',      'sp_answers_topic_uninstall');
add_action('sph_activated', 				                               'sp_answers_topic_sp_activate');
add_action('sph_deactivated', 				                               'sp_answers_topic_sp_deactivate');
add_action('sph_uninstalled', 								               'sp_answers_topic_sp_uninstall');
add_action('sph_plugin_update_answers-topic/sp-answers-topic-plugin.php',  'sp_answers_topic_upgrade_check');
add_action('admin_footer',                                                 'sp_answers_topic_upgrade_check');
add_action('sph_permissions_reset', 						               'sp_answers_topic_reset_permissions');
add_action('sph_setup_forum', 							                   'sp_answers_topic_process_actions');

add_filter('sph_plugins_active_buttons',    'sp_answers_topic_uninstall_option', 10, 2);
add_filter('sph_topicview_query', 			'sp_answers_topic_topic_query');
add_filter('sph_topicview_topic_record', 	'sp_answers_topic_topic_records', 10, 2);

# Mycred Support
add_action('mycred_pre_init',				'sp_answers_topic_load_mycred', 2);
add_filter('add_sp_mycred_extension',		'sp_answers_topic_extend_mycred');
add_action('prefs_sp_mycred_extension', 	'sp_answers_topic_prefs_create');
add_action('sph_mark_answer_actions',		'sp_answers_topic_save_mycred', 1, 3);
add_action('sph_unmark_answer_actions',		'sp_answers_topic_save_mycred', 1, 3);

function sp_answers_topic_localization() {
	sp_plugin_localisation('sp-answers-topic');
}

function sp_answers_topic_install() {
    require_once SPANSWERSDIR.'sp-answers-topic-install.php';
    sp_answers_topic_do_install();
}

function sp_answers_topic_deactivate() {
    require_once SPANSWERSDIR.'sp-answers-topic-uninstall.php';
    sp_answers_topic_do_deactivate();
}

function sp_answers_topic_uninstall() {
    require_once SPANSWERSDIR.'sp-answers-topic-uninstall.php';
    sp_answers_topic_do_uninstall();
}

function sp_answers_topic_sp_activate() {
	require_once SPANSWERSDIR.'sp-answers-topic-install.php';
    sp_answers_topic_do_sp_activate();
}

function sp_answers_topic_sp_deactivate() {
	require_once SPANSWERSDIR.'sp-answers-topic-uninstall.php';
    sp_answers_topic_do_sp_deactivate();
}

function sp_answers_topic_sp_uninstall() {
	require_once SPANSWERSDIR.'sp-answers-topic-uninstall.php';
    sp_answers_topic_do_sp_uninstall();
}

function sp_answers_topic_upgrade_check() {
    require_once SPANSWERSDIR.'sp-answers-topic-upgrade.php';
    sp_answers_topic_do_upgrade_check();
}

function sp_answers_topic_uninstall_option($actionlink, $plugin) {
    require_once SPANSWERSDIR.'sp-answers-topic-uninstall.php';
    $actionlink = sp_answers_topic_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_answers_topic_reset_permissions() {
    require_once SPANSWERSDIR.'sp-answers-topic-install.php';
    sp_answers_topic_do_reset_permissions();
}

function sp_answers_topic_topic_query($query) {
	$query->fields.= ', answered';
	return $query;
}

function sp_answers_topic_topic_records($data, $records) {
	$data->answered = $records->answered;
	return $data;
}

function sp_answers_topic_process_actions() {
    require_once SPANSWERSLIBDIR.'sp-answers-topic-components.php';
	sp_answers_topic_do_process_actions();
}

# MyCred Support
function sp_answers_topic_load_mycred() {
    require_once SPANSWERSLIBDIR.'sp-answers-topic-mycred.php';
}

function sp_answers_topic_extend_mycred($defs) {
    return sp_answers_topic_do_extend_mycred($defs);
}

function sp_answers_topic_prefs_create($args) {
	sp_answers_topic_do_prefs_create($args);
}

function sp_answers_topic_save_mycred($userid, $topicid, $action) {
    require_once SPANSWERSLIBDIR.'sp-answers-topic-mycred.php';
	sp_answers_topic_do_save_mycred($userid, $topicid, $action);
}

# Define Template Tags globally available

function sp_AnswersTopicPostIndexAnswer($args='', $markLabel='', $markToolTip='', $unmarkLabel='', $unmarkToolTip='') {
    require_once SPANSWERSTAGS.'sp-answers-topic-answers-button.php';
    sp_do_AnswersTopicPostIndexAnswer($args, $markLabel, $markToolTip, $unmarkLabel, $unmarkToolTip);
}

function sp_AnswersTopicAnswer($args='', $label='', $toolTip='') {
    require_once SPANSWERSTAGS.'sp-answers-topic-answer.php';
    sp_do_AnswersTopicAnswer($args, $label, $toolTip);
}

function sp_AnswersTopicSeeAnswer($args='', $label='', $toolTip='') {
    require_once SPANSWERSTAGS.'sp-answers-topic-see-answer.php';
    sp_do_AnswersTopicSeeAnswer($args, $label, $toolTip);
}
