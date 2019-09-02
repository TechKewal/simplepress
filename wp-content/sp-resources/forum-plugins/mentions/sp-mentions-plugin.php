<?php
/*
Simple:Press Plugin Title: Mentions
Version: 3.1.0
Item Id: 3917
Plugin URI: https://simple-press.com/downloads/mentions-plugin/
Description: A Simple:Press plugin for detecting Twitter style @name mentions and send them an email or PM
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPMENTIONSDBVERSION', 2);

define('SPACTIVITY_MENTIONED', SP()->activity->get_type('mentions'));

define('SPMENTIONSDIR', 		SPPLUGINDIR.'mentions/');
define('SPMENTIONSADMINDIR',    SPPLUGINDIR.'mentions/admin/');
define('SPMENTIONSAJAXDIR', 	SPPLUGINDIR.'mentions/ajax/');
define('SPMENTIONSLIBDIR', 	    SPPLUGINDIR.'mentions/library/');
define('SPMENTIONSCSS', 		SPPLUGINURL.'mentions/resources/css/');
define('SPMENTIONSSCRIPT', 	    SPPLUGINURL.'mentions/resources/jscript/');
define('SPMENTIONSTAGS', 	    SPPLUGINDIR.'mentions/template-tags/');

add_action('init', 										            'sp_mentions_localization');
add_action('sph_activate_mentions/sp-mentions-plugin.php',          'sp_mentions_install');
add_action('sph_deactivate_mentions/sp-mentions-plugin.php',        'sp_mentions_deactivate');
add_action('sph_uninstall_mentions/sp-mentions-plugin.php',         'sp_mentions_uninstall');
add_action('sph_activated', 				                        'sp_mentions_sp_activate');
add_action('sph_deactivated', 				                        'sp_mentions_sp_deactivate');
add_action('sph_uninstalled', 								        'sp_mentions_sp_uninstall');
add_action('sph_plugin_update_mentions/sp-mentions-plugin.php',     'sp_mentions_upgrade_check');
add_action('admin_footer',                                          'sp_mentions_upgrade_check');
add_action('sph_permissions_reset', 						        'sp_mentions_reset_permissions');
add_action('sph_print_plugin_scripts', 								'sp_mentions_load_js');
add_action('sph_print_plugin_styles', 								'sp_mentions_header');
add_action('sph_new_post',                                          'sp_mentions_new_post');
add_action('sph_member_created', 						    		'sp_mentions_member_add');
add_action('sph_UpdateProfileGlobalOptions',				    	'sp_mentions_profile_save', 10, 2);

add_filter('sph_plugins_active_buttons',            'sp_mentions_uninstall_option', 10, 2);
add_filter('sph_display_post_content_filter',       'sp_mentions_content_filter');
add_action('sph_options_members_right_panel', 	    'sp_mentions_admin_options');
add_action('sph_option_members_save', 			    'sp_mentions_admin_save_options');
add_filter('sph_admin_help-admin-options', 		    'sp_mentions_admin_help', 10, 3);
add_filter('sph_add_tm_plugin',					    'sp_mentions_add_tm_plugin');
add_filter('sph_mce_external_plugins',              'sp_mentions_setup_tm_plugin');
add_filter('sph_tm_init',                           'sp_mentions_tm_init');
add_filter('sph_ProfileGlobalOptionsFormBottom',    'sp_mentions_profile_options', 10, 2);
add_filter('sph_acknowledgements',					'sp_mentions_acknowledgement');

# Ajax Handler
add_action('wp_ajax_mentions',				'sp_mentions_ajax');
add_action('wp_ajax_nopriv_mentions',		'sp_mentions_ajax');


function sp_mentions_admin_options() {
    require_once SPMENTIONSADMINDIR.'sp-mentions-admin-options.php';
	sp_mentions_do_admin_options();
}

function sp_mentions_admin_save_options() {
    require_once SPMENTIONSADMINDIR.'sp-mentions-admin-options-save.php';
    sp_mentions_do_admin_save_options();
}

function sp_mentions_admin_help($file, $tag, $lang) {
    if ($tag == '[mentions-options]') $file = SPMENTIONSADMINDIR.'sp-mentions-admin-help.'.$lang;
    return $file;
}

function sp_mentions_localization() {
	sp_plugin_localisation('sp-mentions');
}

function sp_mentions_install() {
    require_once SPMENTIONSDIR.'sp-mentions-install.php';
    sp_mentions_do_install();
}

function sp_mentions_deactivate() {
    require_once SPMENTIONSDIR.'sp-mentions-uninstall.php';
    sp_mentions_do_deactivate();
}

function sp_mentions_uninstall() {
    require_once SPMENTIONSDIR.'sp-mentions-uninstall.php';
    sp_mentions_do_uninstall();
}

function sp_mentions_sp_activate() {
	require_once SPMENTIONSDIR.'sp-mentions-install.php';
    sp_mentions_do_sp_activate();
}

function sp_mentions_sp_deactivate() {
	require_once SPMENTIONSDIR.'sp-mentions-uninstall.php';
    sp_mentions_do_sp_deactivate();
}

function sp_mentions_sp_uninstall() {
	require_once SPMENTIONSDIR.'sp-mentions-uninstall.php';
    sp_mentions_do_sp_uninstall();
}

function sp_mentions_upgrade_check() {
    require_once SPMENTIONSDIR.'sp-mentions-upgrade.php';
    sp_mentions_do_upgrade_check();
}

function sp_mentions_uninstall_option($actionlink, $plugin) {
    require_once SPMENTIONSDIR.'sp-mentions-uninstall.php';
    $actionlink = sp_mentions_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_mentions_reset_permissions() {
    require_once SPMENTIONSDIR.'sp-mentions-install.php';
    sp_mentions_do_reset_permissions();
}

function sp_mentions_content_filter($content) {
    require_once SPMENTIONSLIBDIR.'sp-mentions-components.php';
    $content = sp_mentions_do_content_filter($content);
    return $content;
}

function sp_mentions_header() {
    require_once SPMENTIONSLIBDIR.'sp-mentions-components.php';
	sp_mentions_do_header();
}

function sp_mentions_add_tm_plugin($plugins) {
	$plugins.= ',mention';
	return $plugins;
}

function sp_mentions_setup_tm_plugin($plugins){
    # https://github.com/CogniStreamer/tinyMCE-mention
    # last update April 9, 2014 version
    # note plugin requires modification - see /******** comments in unminified js
    # have to self create the min.js file after changes
    $plugins['mention'] = SPMENTIONSSCRIPT.'mention/plugin.min.js';
    return $plugins;
}

function sp_mentions_tm_init($tiny) {
	$ajaxURL = SPAJAXURL.'mentions';
    $tiny['mentions'] = "{delay: 250, items: 25, source: function (query, process, delimter) {
		jQuery.ajax({
			type: 'GET',
			url: '".$ajaxURL."',
			cache: false,
			data: 'q=' + query,
			success: function (data) {
			    data = jQuery.parseJSON(data);
                process(data);
			},
		});}}";
    return $tiny;
}

function sp_mentions_ajax(){
    require_once SPMENTIONSAJAXDIR.'sp-mentions-ajax.php';
}

function sp_mentions_new_post($newpost) {
    require_once SPMENTIONSLIBDIR.'sp-mentions-components.php';
    sp_mentions_do_new_post($newpost);
}

function sp_mentions_member_add($userid) {
    require_once SPMENTIONSLIBDIR.'sp-mentions-components.php';
	sp_mentions_do_member_add($userid);
}

function sp_mentions_profile_options($content, $userid) {
    require_once SPMENTIONSLIBDIR.'sp-mentions-components.php';
	$content = sp_mentions_do_profile_options($content, $userid);
	return $content;
}

function sp_mentions_profile_save($message, $userid) {
	$options = SP()->memberData->get($userid, 'user_options');
    $update = apply_filters('sph_ProfileUserMentionsOptOutUpdate', true);
	if ($update) if (isset($_POST['mentionsoptout'])) $options['mentionsoptout'] = true; else $options['mentionsoptout'] = false;
	SP()->memberData->update($userid, 'user_options', $options);
    return $message;
}

function sp_mentions_load_js() {
    wp_enqueue_script('jquery');
}

function sp_mentions_acknowledgement($ack) {
	$ack[] = '<a href="http://cognistreamer.com/">'.__("Uses Code from Steven Devooght's Mention tinyMCE Plugin", "sp-mentions").'</a>';
	return $ack;
}

# Define Template Tags

function sp_MentionsLatestTag($args='') {
    require_once SPMENTIONSTAGS.'sp-mentions-latest-mentions-tag.php';
	return sp_do_MentionsLatestTag($args);
}
function sp_MentionsLatestShortcode($atts) {
    require_once SPMENTIONSTAGS.'sp-mentions-latest-mentions-tag.php';
    return sp_do_MentionsLatestShortcode($atts);
}
add_shortcode('sp_mentions_latest', 'sp_MentionsLatestShortcode');

function sp_MentionsYourLatestTag($args='') {
    require_once SPMENTIONSTAGS.'sp-mentions-your-latest-mentions-tag.php';
	return sp_do_MentionsYourLatestTag($args);
}
function sp_MentionsYourLatestShortcode($atts) {
    require_once SPMENTIONSTAGS.'sp-mentions-your-latest-mentions-tag.php';
    return sp_do_MentionsYourLatestShortcode($atts);
}
add_shortcode('sp_mentions_your_latest', 'sp_MentionsYourLatestShortcode');
