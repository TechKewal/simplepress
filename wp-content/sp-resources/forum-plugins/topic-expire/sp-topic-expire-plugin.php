<?php
/*
Simple:Press Plugin Title: Topic Expire
Version: 2.1.0
Item Id: 3967
Plugin URI: https://simple-press.com/downloads/topic-expire-plugin/
Description: A Simple:Press plugin that allows you to specifiy an expiration date when you create a topic.  Expired topics are either deleted or moved to another forum.
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPEXPIREDBVERSION', 0);

define('SPEXPIREDIR', 		SPPLUGINDIR.'topic-expire/');
define('SPEXPIRELIBDIR', 	SPPLUGINDIR.'topic-expire/library/');
define('SPEXPIRELIBURL', 	SPPLUGINURL.'topic-expire/library/');
define('SPEXPIREAJAXDIR', 	SPPLUGINDIR.'topic-expire/ajax/');
define('SPEXPIRECSS', 		SPPLUGINURL.'topic-expire/resources/css/');
define('SPEXPIREIMAGES', 	SPPLUGINURL.'topic-expire/resources/images/');
define('SPEXPIREIMAGESMOB', SPPLUGINURL.'topic-expire/resources/images/mobile/');
define('SPEXPIRESCRIPT', 	SPPLUGINURL.'topic-expire/resources/jscript/');

add_action('init', 										                    'sp_topic_expire_localization');
add_action('sph_activate_topic-expire/sp-topic-expire-plugin.php',          'sp_topic_expire_install');
add_action('sph_deactivate_topic-expire/sp-topic-expire-plugin.php',        'sp_topic_expire_deactivate');
add_action('sph_uninstall_topic-expire/sp-topic-expire-plugin.php',         'sp_topic_expire_uninstall');
add_action('sph_activated', 				                                'sp_topic_expire_sp_activate');
add_action('sph_deactivated', 				                                'sp_topic_expire_sp_deactivate');
add_action('sph_uninstalled', 								                'sp_topic_expire_sp_uninstall');
add_action('sph_plugin_update_topic-expire/sp-topic-expire-plugin.php',     'sp_topic_expire_upgrade_check');
add_action('admin_footer',                                                  'sp_topic_expire_upgrade_check');
add_action('sph_permissions_reset', 						                'sp_topic_expire_reset_permissions');
add_action('sph_post_create', 							                    'sp_topic_expire_save_post');
add_action('sph_print_plugin_styles',						                'sp_topic_expire_head');
add_action('sph_print_plugin_scripts', 								    	'sp_topic_expire_load_js');
add_action('sph_topic_expire_cron', 				                        'sp_topic_expire_check_expired');
add_action('sph_setup_forum', 							    	            'sp_topic_expire_process_actions');
add_action('sph_stats_scheduler',                                           'sp_topic_expire_scheduler');

add_filter('sph_plugins_active_buttons',    'sp_topic_expire_uninstall_option', 10, 2);
add_filter('sph_perms_tooltips', 			'sp_topic_expire_tooltips', 10, 2);
add_filter('sph_add_topic_tool', 		    'sp_topic_expire_forum_tool', 10, 5);
add_filter('sph_topicview_query',			'sp_topic_expire_topic_query');
add_filter('sph_topicview_topic_record',	'sp_topic_expire_topic_records', 10, 2);
add_filter('sph_forumview_query', 			'sp_topic_expire_forum_query');
add_filter('sph_forumview_forum_record',	'sp_topic_expire_forum_records', 10, 2);
add_filter('sph_forumview_topic_records', 	'sp_topic_expire_forum_records', 10, 2);

if (SP()->core->forumData['display']['editor']['toolbar']) {
	add_filter('sph_topic_editor_toolbar_buttons',	'sp_topic_expire_button', 10, 4);
	add_filter('sph_topic_editor_toolbar',			'sp_topic_expire_container', 10, 4);
} else {
	add_filter('sph_topic_editor_footer_top',		'sp_topic_expire_container', 10, 2);
}

# Ajax Handler
add_action('wp_ajax_topic-expire',			'sp_topic_expire_ajax');
add_action('wp_ajax_nopriv_topic-expire',	'sp_topic_expire_ajax');

function sp_topic_expire_localization() {
	sp_plugin_localisation('sp-topic-expire');
}

function sp_topic_expire_install() {
    require_once SPEXPIREDIR.'sp-topic-expire-install.php';
    sp_topic_expire_do_install();
}

function sp_topic_expire_deactivate() {
    require_once SPEXPIREDIR.'sp-topic-expire-uninstall.php';
    sp_topic_expire_do_deactivate();
}

function sp_topic_expire_uninstall() {
    require_once SPEXPIREDIR.'sp-topic-expire-uninstall.php';
    sp_topic_expire_do_uninstall();
}

function sp_topic_expire_sp_activate() {
	require_once SPEXPIREDIR.'sp-topic-expire-install.php';
    sp_topic_expire_do_sp_activate();
}

function sp_topic_expire_sp_deactivate() {
	require_once SPEXPIREDIR.'sp-topic-expire-uninstall.php';
    sp_topic_expire_do_sp_deactivate();
}

function sp_topic_expire_sp_uninstall() {
	require_once SPEXPIREDIR.'sp-topic-expire-uninstall.php';
    sp_topic_expire_do_sp_uninstall();
}

function sp_topic_expire_upgrade_check() {
    require_once SPEXPIREDIR.'sp-topic-expire-upgrade.php';
    sp_topic_expire_do_upgrade_check();
}

function sp_topic_expire_uninstall_option($actionlink, $plugin) {
    require_once SPEXPIREDIR.'sp-topic-expire-uninstall.php';
    $actionlink = sp_topic_expire_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_topic_expire_reset_permissions() {
    require_once SPEXPIREDIR.'sp-topic-expire-install.php';
    sp_topic_expire_do_reset_permissions();
}

function sp_topic_expire_tooltips($tips, $t) {
    $tips['set_topic_expire'] = $t.__('Can set a topic expiration when creating new topics and have it deleted or moved to another forum when it expires', 'sp-topic-expire');
    return $tips;
}

function sp_topic_expire_head() {
    $css = SP()->theme->find_css(SPEXPIRECSS, 'jquery-ui.css');
    SP()->plugin->enqueue_style('sp-topic-expire-ui', $css);
}

function sp_topic_expire_load_js($footer) {
	wp_enqueue_script('jquery-ui-datepicker', false, array('jquery', 'jquery-ui-core', 'jquery-ui-widget'), false, $footer);
}

function sp_topic_expire_button($out, $data, $a) {
	require_once SPEXPIRELIBDIR.'sp-topic-expire-components.php';
	$out = sp_topic_do_expire_button($out, $data, $a);
	return $out;
}

function sp_topic_expire_container($out, $data) {
	require_once SPEXPIRELIBDIR.'sp-topic-expire-components.php';
	$out = sp_topic_expire_do_container($out, $data);
	return $out;
}

function sp_topic_expire_save_post($newpost) {
	require_once SPEXPIRELIBDIR.'sp-topic-expire-components.php';
    sp_topic_expire_do_save_post($newpost);
}

function sp_topic_expire_check_expired() {
	require_once SPEXPIRELIBDIR.'sp-topic-expire-components.php';
    sp_topic_expire_do_check_expired();
}

function sp_topic_expire_forum_tool($out, $topic, $forum, $page, $br) {
	require_once SPEXPIRELIBDIR.'sp-topic-expire-components.php';
	$out = sp_topic_expire_do_forum_tool($out, $forum, $topic, $page, $br);
    return $out;
}

function sp_topic_expire_ajax() {
	require_once SPEXPIRELIBDIR.'sp-topic-expire-components.php';
    require_once SPEXPIREAJAXDIR.'sp-topic-expire-ajax.php';
}

function sp_topic_expire_process_actions() {
	require_once SPEXPIRELIBDIR.'sp-topic-expire-components.php';
	sp_topic_expire_do_process_actions();
}

function sp_topic_expire_scheduler() {
    if (!wp_next_scheduled('sph_topic_expire_cron')) {
        wp_schedule_event(time(), 'daily', 'sph_topic_expire_cron');
    }
}

function sp_topic_expire_topic_query($query) {
	$query->fields.= ', expire_date';
	return $query;
}

function sp_topic_expire_topic_records($data, $record) {
	$data->expire_date = $record->expire_date;
	return $data;
}

function sp_topic_expire_forum_query($query) {
	$query->fields.= ', expire_date';
	return $query;
}

function sp_topic_expire_forum_records($data, $record) {
	$data->expire_date = $record->expire_date;
	return $data;
}
