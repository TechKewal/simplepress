<?php
/*
Simple:Press Plugin Title: Admin Bar
Version: 2.2.0
Item Id: 3914
Plugin URI: https://simple-press.com/downloads/admin-bar-plugin/
Description: The Admin Bar helps to manage the admininstrators postbag of new forum posts and posts awaiting moderation as well as add optional Akismet support to help identify and quickly deal with spam posts
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-12-14 10:26:54 -0600 (Fri, 14 Dec 2018) $
$Rev: 15846 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPABDBVERSION', 5);

define('SPABDIR', 		SPPLUGINDIR.'admin-bar/');
define('SPABADMINDIR', 	SPPLUGINDIR.'admin-bar/admin/');
define('SPABLIBDIR', 	SPPLUGINDIR.'admin-bar/library/');
define('SPABTAGSDIR', 	SPPLUGINDIR.'admin-bar/template-tags/');
define('SPABAJAXDIR', 	SPPLUGINDIR.'admin-bar/ajax/');
define('SPABIMAGES', 	SPPLUGINURL.'admin-bar/resources/images/');
define('SPABIMAGESMOB', SPPLUGINURL.'admin-bar/resources/images/mobile/');
define('SPABCSS', 		SPPLUGINURL.'admin-bar/resources/css/');
define('SPABSCRIPT', 	SPPLUGINURL.'admin-bar/resources/jscript/');

add_action('init', 												    'sp_admin_bar_localization');
add_action('sph_activate_admin-bar/sp-admin-bar-plugin.php',	    'sp_admin_bar_install');
add_action('sph_uninstall_admin-bar/sp-admin-bar-plugin.php', 	    'sp_admin_bar_uninstall');
add_action('sph_deactivate_admin-bar/sp-admin-bar-plugin.php', 	    'sp_admin_bar_deactivate');
add_action('sph_admins_global_left_panel', 						    'sp_admin_bar_admin');
add_action('sph_admin_global_options_save',						    'sp_admin_bar_admin_save');
add_action('sph_admins_global_right_panel', 					    'sp_akismet_admin_options');
add_action('sph_admins_options_top_panel',						    'sp_admin_bar_options');
add_filter('sph_admin_your_options_change',							'sp_admin_bar_options_save');
add_filter('sph_acknowledgements', 								    'sp_akismet_acknowledgement');
add_action('sph_AfterDisplayStart',								    'sp_admin_bar_get_waiting');
add_action('sph_print_plugin_styles', 							    'sp_admin_bar_header');
add_action('sph_print_plugin_scripts', 							    'sp_admin_bar_load_js');
add_action('sph_dashboard_start',								    'sp_admin_bar_dashboard');
add_action('sph_plugin_update_admin-bar/sp-admin-bar-plugin.php',   'sp_admin_bar_upgrade_check');
add_action('admin_footer',                                          'sp_admin_bar_upgrade_check');
add_action('sph_permissions_reset',                                 'sp_admin_bar_reset_permissions');

add_filter('sph_admin_help-admin-admins',	'sp_admin_bar_help', 10, 3);
add_filter('sph_plugins_active_buttons', 	'sp_admin_bar_uninstall_option', 10, 2);
add_filter('sph_add_to_waiting',			'sp_admin_bar_create_post');
add_filter('sph_admin_email',				'sp_admin_bar_email', 10, 2);
add_filter('sph_new_forum_post', 			'sp_run_akismet');
add_filter('sph_perms_tooltips', 			'sp_admin_bar_tooltips', 10, 2);

# Ajax Handlers

add_action('wp_ajax_admin-bar-links',			'sp_admin_bar_ajax_links');
add_action('wp_ajax_nopriv_admin-bar-links',	'sp_admin_bar_ajax_links');
add_action('wp_ajax_admin-bar-update',			'sp_admin_bar_ajax_update');
add_action('wp_ajax_nopriv_admin-bar-update',	'sp_admin_bar_ajax_update');
add_action('wp_ajax_admin-bar-newposts',		'sp_admin_bar_ajax_newposts');
add_action('wp_ajax_nopriv_admin-bar-newposts',	'sp_admin_bar_ajax_newposts');
add_action('wp_ajax_quickreply',				'sp_admin_bar_ajax_quickreply');
add_action('wp_ajax_nopriv_quickreply',			'sp_admin_bar_ajax_quickreply');
add_action('wp_ajax_remove-spam',				'sp_admin_bar_ajax_delspam');
add_action('wp_ajax_nopriv_remove-spam',		'sp_admin_bar_ajax_delspam');
add_action('wp_ajax_moderation',				'sp_admin_bar_ajax_moderation');
add_action('wp_ajax_nopriv_moderation',			'sp_admin_bar_ajax_moderation');


function sp_admin_bar_admin() {
    require_once SPABADMINDIR.'sp-admin-bar-admin.php';
	sp_admin_bar_admin_form();
}

function sp_admin_bar_admin_save() {
    require_once SPABADMINDIR.'sp-admin-bar-admin-save.php';
	return sp_admin_bar_admin_options_save();
}

function sp_akismet_admin_options() {
	require_once SPABADMINDIR.'sp-admin-bar-admin.php';
	sp_akismet_admin_options_form();
}

function sp_admin_bar_options() {
    require_once SPABADMINDIR.'sp-admin-bar-options.php';
	sp_admin_bar_options_form();
}

function sp_admin_bar_options_save($ops) {
    require_once SPABADMINDIR.'sp-admin-bar-options-save.php';
	return sp_admin_bar_options_update($ops);
}

function sp_admin_bar_help($file, $tag, $lang) {
    if ($tag == '[admin-bar]' || $tag == '[akismet]' || $tag == '[admin-bar-options]') $file = SPABADMINDIR.'sp-admin-bar-admin-help.'.$lang;
    return $file;
}

function sp_admin_bar_localization() {
	sp_plugin_localisation('spab');
}

function sp_admin_bar_install() {
    require_once SPABDIR.'sp-admin-bar-install.php';
    sp_admin_bar_do_install();
}

function sp_admin_bar_uninstall() {
    require_once SPABDIR.'sp-admin-bar-uninstall.php';
    sp_admin_bar_do_uninstall();
}

function sp_admin_bar_uninstall_option($actionlink, $plugin) {
    require_once SPABDIR.'sp-admin-bar-uninstall.php';
    $actionlink = sp_admin_bar_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_admin_bar_deactivate() {
    require_once SPABDIR.'sp-admin-bar-uninstall.php';
    sp_admin_bar_do_deactivate();
}

function sp_admin_bar_upgrade_check() {
    require_once SPABDIR.'sp-admin-bar-upgrade.php';
    sp_admin_bar_do_upgrade_check();
}

function sp_admin_bar_reset_permissions() {
    require_once SPABDIR.'sp-admin-bar-install.php';
    sp_admin_bar_do_reset_permissions();
}

function sp_admin_bar_tooltips($tips, $t) {
    $tips['bypass_akismet'] = $t.__('Can bypass the akismet spam checks when making a new post', 'spab');
    return $tips;
}

function sp_akismet_acknowledgement($ack) {
	$ack[] = '<a href="http://automattic.com/wordpress-plugins/">'.__('Akismet WordPress plugin by Automattic', 'spab').'</a>';
	return $ack;
}

function sp_admin_bar_header() {
    require_once SPABLIBDIR.'sp-admin-bar-components.php';
    sp_admin_bar_do_header();
}

function sp_admin_bar_load_js($footer) {
    require_once SPABLIBDIR.'sp-admin-bar-components.php';
    sp_admin_bar_do_load_js($footer);
}

function sp_admin_bar_get_waiting() {
    require_once SPABLIBDIR.'sp-admin-bar-components.php';
    sp_AdminBarGetWaiting();
}

function sp_admin_bar_ajax_links() {
	require_once SPABAJAXDIR.'sp-admin-bar-ajax-links.php';
}

function sp_admin_bar_ajax_update() {
	require_once SPABAJAXDIR.'sp-admin-bar-ajax-update.php';
}

function sp_admin_bar_ajax_newposts() {
	require_once SPABAJAXDIR.'sp-admin-bar-ajax-newposts.php';
}

function sp_admin_bar_ajax_quickreply() {
	require_once SPABAJAXDIR.'sp-admin-bar-ajax-quickreply.php';
}

function sp_admin_bar_ajax_delspam() {
	require_once SPABAJAXDIR.'sp-admin-bar-ajax-spam.php';
}

function sp_admin_bar_ajax_moderation() {
	require_once SPABAJAXDIR.'sp-admin-bar-ajax-moderation.php';
}

function sp_admin_bar_dashboard() {
    require_once SPABLIBDIR.'sp-admin-bar-components.php';
	sp_AdminBarDashboardPosts();
 }

function sp_admin_bar_create_post($add) {
	return true;
}

function sp_admin_bar_email($msg, $newpost) {
    require_once SPABLIBDIR.'sp-admin-bar-components.php';
	$msg = sp_AdminBarEmail($msg, $newpost);
	return $msg;
}

# Define Template Tags globally available (dont have to be enabled on template tag panel)
function sp_AdminQueue($args, $viewLabel='', $unreadLabel='', $modLabel='', $spamLabel='', $toolTip='') {
    require_once SPABTAGSDIR.'sp-admin-bar-queue-tag.php';
    sp_AdminQueueTag($args, $viewLabel, $unreadLabel, $modLabel, $spamLabel, $toolTip);
}

function sp_AdminLinks($args='', $label='', $toolTip='') {
    require_once SPABTAGSDIR.'sp-admin-bar-links-tag.php';
    sp_AdminLinksTag($args, $label, $toolTip);
}

# ------------------------------------------------------
# Run Akismet against a post submission
# ------------------------------------------------------
function sp_run_akismet($newpost) {
	require_once SPABLIBDIR.'sp-akismet.php';
	return sp_akismet($newpost);
}

?>