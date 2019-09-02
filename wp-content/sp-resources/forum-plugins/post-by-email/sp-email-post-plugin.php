<?php
/*
Simple:Press Plugin Title: Post By Email
Version: 2.1.0
Item Id: 3920
Plugin URI: https://simple-press.com/downloads/post-by-email-plugin/
Description: Optionally allow for email posting to reply to topics
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-11-08 06:38:20 -0600 (Thu, 08 Nov 2018) $
$Rev: 15812 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPPBEDBVERSION', 2);

# ======================================
# CONSTANTS USED BY THIS PLUGIN
# ======================================
define('SFMAILLOG',		SP_PREFIX.'sfmaillog');
define('SPEPDIR',		SPPLUGINDIR.'post-by-email/');
define('SPEPLIB',		SPEPDIR.'library/');
define('SPEPICON',		SPPLUGINURL.'post-by-email/resources/images/');
define('SPEPSCRIPT', 	SPPLUGINURL.'post-by-email/resources/jscript/');
define('SPEPSOURCE',	1);

# ======================================
# ACTIONS/FILTERS USED BY THUS PLUGIN
# ======================================
# Plugin install/uninstall
add_action('sph_activate_post-by-email/sp-email-post-plugin.php',	    'sp_emailpost_install');
add_action('sph_deactivate_post-by-email/sp-email-post-plugin.php',     'sp_emailpost_deactivate');
add_action('sph_uninstall_post-by-email/sp-email-post-plugin.php', 	    'sp_emailpost_uninstall');
add_action('sph_uninstalled', 								    	    'sp_emailpost_sp_uninstall');
add_filter('sph_plugins_active_buttons',						     	'sp_emailpost_uninstall_option', 10, 2);
add_action('init', 													    'sp_emailpost_localisation');
add_action('sph_plugin_update_post-by-email/sp-email-post-plugin.php',  'sp_emailpost_upgrade_check');
add_action('admin_footer',                                              'sp_emailpost_upgrade_check');
add_action('sph_permissions_reset',                                     'sp_emailpost_reset_permissions');

# forum email column
add_action('sph_forum_create_forum_options',						'sp_emailpost_add_email_field');
add_action('sph_forum_edit_forum_options',							'sp_emailpost_add_email_field');
add_action('sph_forum_forum_create',								'sp_emailpost_save_forum');
add_action('sph_forum_forum_edit',									'sp_emailpost_save_forum');
add_filter('sph_new_post_data_saved',								'sp_emailpost_newpost');

# options post by email panel
add_action('sph_options_email_left_panel',	 						'sp_emailpost_admin_email_panel');
add_action('sph_option_email_save',									'sp_emailpost_admin_email_save');
add_action('sph_scripts_admin_end', 					            'sp_emailpost_load_admin_js');

# admin help and tooltips
add_filter('sph_admin_help-admin-options',	 						'sp_emailpost_admin_help', 10, 3);
add_filter('sph_perms_tooltips', 				                    'sp_emailpost_tooltips', 10, 2);

# subscription email link
add_filter('sph_email_replyto',										'sp_emailpost_replyto', 1, 2);
add_filter('sph_subscriptions_email_replyto',						'sp_emailpost_replyto', 1, 2);
add_filter('sph_admin_email',										'sp_emailpost_add_email_link', 10, 4);
add_filter('sph_subscriptions_notification_email',					'sp_emailpost_add_email_link', 10, 4);

# add an alternate email address to profile
add_filter('sph_user_class_meta', 									'sp_emailpost_usermeta_list');

# cron processing
add_action('cron_schedules', 				                        'sp_emailpost_cron_schedule');
add_action('sph_activated', 				                        'sp_emailpost_sp_activate');
add_action('sph_deactivated', 				                        'sp_emailpost_sp_deactivate');
add_action('sph_emailpost_cron', 				                    'sp_emailpost_cron_run');
add_action('sph_stats_scheduler',                                   'sp_emailpost_scheduler');

# email processing log
add_action('sph_admin_menu',										'sp_emailpost_admin_menu');
add_filter('sph_ProfileUserNewPassword', 							'sp_emailpost_add_alt_email', 1, 3);
add_filter('sph_UpdateProfileSettings', 							'sp_emailpost_save_alt_email', 1, 2);

# Personal Data Export
add_filter('sp_privacy_profile_data', 				'sp_privacy_emailpost_profile', 20, 4);

# Ajax Handler
add_action('wp_ajax_pbetest',			'sp_emailpost_ajax');
add_action('wp_ajax_nopriv_pbetest',	'sp_emailpost_ajax');


# ----------------------------------------------
# Run Install Script on Activation action
# ----------------------------------------------
function sp_emailpost_install() {
	require_once SPEPDIR.'sp-email-post-install.php';
	sp_email_do_install();
}

# ----------------------------------------------
# Run Deactivate Script on Deactivation action
# ----------------------------------------------
function sp_emailpost_deactivate() {
	require_once SPEPDIR.'sp-email-post-uninstall.php';
	sp_emailpost_do_deactivate();
}

# ----------------------------------------------
# Run Uninstall Script on Uninstall action
# ----------------------------------------------
function sp_emailpost_uninstall() {
	require_once SPEPDIR.'sp-email-post-uninstall.php';
	sp_emailpost_do_uninstall();
}

function sp_emailpost_sp_uninstall() {
	require_once SPEPDIR.'sp-email-post-uninstall.php';
	sp_emailpost_do_sp_uninstall();
}

# ------------------------------------------------------
# Add the 'Uninstall' and 'Options' link to plugins list
# ------------------------------------------------------
function sp_emailpost_uninstall_option($actionlink, $plugin) {
	require_once SPEPDIR.'sp-email-post-install.php';
    $actionlink = sp_emailpost_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_emailpost_upgrade_check() {
    require_once SPEPDIR.'sp-email-post-upgrade.php';
    sp_emailpost_do_upgrade_check();
}

function sp_emailpost_reset_permissions() {
	require_once SPEPDIR.'sp-email-post-install.php';
	sp_emailpost_do_reset_permissions();
}

# ------------------------------------------------------
# Set up language file
# ------------------------------------------------------
function sp_emailpost_localisation() {
	sp_plugin_localisation('sp-pbe');
}

# ------------------------------------------------------
# Creates new email address field on new forum panel
# ------------------------------------------------------
function sp_emailpost_add_email_field($forum = '') {
	require_once SPEPLIB.'sp-email-post-support.php';
	if(empty($forum) ? $e = '' : $e = $forum->forum_email);
	sp_emailpost_do_add_email_field($e);
}

# ------------------------------------------------------
# Saves the new email field to the forum
# ------------------------------------------------------
function sp_emailpost_save_forum($forumid) {
	require_once SPEPLIB.'sp-email-post-support.php';
	sp_emailpost_do_save_forum($forumid);
}

# ----------------------------------------------
# Display the admin panel on the email page
# ----------------------------------------------
function sp_emailpost_admin_email_panel() {
    require_once SPEPDIR.'admin/spa-options-email-post-form.php';
	sp_emailpost_do_admin_email_panel();
}

# ----------------------------------------------
# Saves the admin panel on the email page
# ----------------------------------------------
function sp_emailpost_admin_email_save() {
    require_once SPEPDIR.'admin/spa-options-email-post-save.php';
	sp_emailpost_do_admin_email_save();
}

# ------------------------------------------------------
# Set up Ajax handler
# ------------------------------------------------------
function sp_emailpost_ajax() {
	require_once SPEPDIR.'ajax/sp-ajax-pbe-test.php';
}

# ----------------------------------------------
# Action the post by emnail admin panel popup help
# ----------------------------------------------
function sp_emailpost_admin_help($file, $tag, $lang) {
    if ($tag == '[post-by-email]') $file = SPEPDIR.'admin/spa-email-post-admin-help.'.$lang;
    return $file;
}

# ----------------------------------------------
# Add permission tooltips to permissions panels
# ----------------------------------------------
function sp_emailpost_tooltips($tips, $t) {
    $tips['post_by_email_reply'] = $t.__('Can reply to topics using email', 'sp-pbe');
    $tips['post_by_email_start'] = $t.__('Can start new topics using email', 'sp-pbe');
    return $tips;
}

# ----------------------------------------------
# Adds the forum email address to $newpost
# ----------------------------------------------
function sp_emailpost_newpost($newpost) {
	$newpost['forumemail'] = SP()->DB->table(SPFORUMS, 'forum_id='.$newpost['forumid'], 'forum_email');
	return $newpost;
}


function sp_emailpost_replyto($replyto, $newpost) {
	if(isset($newpost['forumemail'])) {
		if($newpost['forumemail'] ? $replyto = $newpost['forumemail'] : $replyto='');
	}
	return $replyto;
}

function sp_emailpost_load_admin_js() {
	wp_enqueue_script('spemailpost', SPEPSCRIPT.'sp-email-post-admin.min.js', array('jquery'), false, false);
}

# ----------------------------------------------
# Adds the remail link and message to email
# ----------------------------------------------
function sp_emailpost_add_email_link($m, $newpost, $userid, $type) {
	require_once SPEPLIB.'sp-email-post-support.php';
	return sp_emailpost_do_add_email_link($m, $newpost, $userid, $type);
}

# ----------------------------------------------
# Add alternate email address to profile
# ----------------------------------------------
function sp_emailpost_usermeta_list($list) {
	$list['alt_user_email'] = 'email';
	return $list;
}

function sp_emailpost_add_alt_email($out, $userid, $slug) {
	require_once SPEPLIB.'sp-email-post-support.php';
	return sp_emailpost_do_add_alt_email($out, $userid);
}

function sp_emailpost_save_alt_email($message, $userid) {
	require_once SPEPLIB.'sp-email-post-support.php';
	return sp_emailpost_do_save_alt_email($message, $userid);
}

# ----------------------------------------------
# Cron routines
# ----------------------------------------------
function sp_emailpost_cron_schedule($schedules) {
	$opts = SP()->options->get('spEmailPost');
	if(!isset($opts['interval']) || $opts['interval']==0) $opts['interval']=1800;
    $schedules['sp_emailpost_interval'] = array('interval' => $opts['interval'], 'display' => __('SP Post by Email Interval')); # default is 1800 - half hour
    return $schedules;
}

function sp_emailpost_sp_activate() {
	require_once SPEPDIR.'sp-email-post-install.php';
    sp_emailpost_do_sp_activate();
}

function sp_emailpost_sp_deactivate() {
	require_once SPEPDIR.'sp-email-post-uninstall.php';
    sp_emailpost_do_sp_deactivate();
}

function sp_emailpost_cron_run() {
	require_once SPEPLIB.'sp-email-post-process.php';
    sp_emailpost_process_emails();
}

# ----------------------------------------------
# Add Email Log Admin Panel to Toolbox
# ----------------------------------------------
function sp_emailpost_admin_menu() {
	$subpanels = array(
		__("Email Post Log", "sp-pbe") => array('admin' => 'spa_emailpost_log_form', 'save' => '', 'form' => 0, 'id' => 'emaillog')
	);
	SP()->plugin->add_admin_subpanel('toolbox', $subpanels);
}

# ----------------------------------------------
# Load the email log panel
# ----------------------------------------------
function spa_emailpost_log_form() {
	require_once SPEPDIR.'admin/spa-email-post-log-form.php';
	spa_emailpost_show_log();
}

function sp_emailpost_scheduler() {
    if (!wp_next_scheduled('sph_emailpost_cron')) {
	    wp_schedule_event(time(), 'sp_emailpost_interval', 'sph_emailpost_cron');
    }
}

# personal data export
function sp_privacy_emailpost_profile($exportItems, $spUserData, $groupID, $groupLabel) {
	require_once SPEPLIB.'sp-email-post-support.php';
	return sp_privacy_do_emailpost_profile($exportItems, $spUserData, $groupID, $groupLabel);
}

# ----------------------------------------------
# Define Template Tag globally available
# ----------------------------------------------
function sp_PostIndexPostByEmail($args='', $toolTip='') {
    require_once SPEPDIR.'template-tags/sp-email-post-icon.php';
    sp_PostIndexPostByEmailTag($args, $toolTip);
}
