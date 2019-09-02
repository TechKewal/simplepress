<?php
/*
Simple:Press Plugin Title: Private Message System
Version: 3.2.0
Item Id: 3906
Plugin URI: https://simple-press.com/downloads/private-messaging-plugin/
Description: Private Messaging System plugin for Simple:Press
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
A plugin for Simple:Press to add a private messaging system to your forum.
$LastChangedDate: 2018-12-11 20:31:24 -0600 (Tue, 11 Dec 2018) $
$Rev: 15843 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPDBVERSION', 18);

define('SPPMTHREADS', 		SP_PREFIX.'sfpmthreads');
define('SPPMMESSAGES', 	    SP_PREFIX.'sfpmmessages');
define('SPPMRECIPIENTS', 	SP_PREFIX.'sfpmrecipients');
define('SPPMBUDDIES',		SP_PREFIX.'sfbuddies');
define('SPPMADVERSARIES', 	SP_PREFIX.'sfadversaries');
define('SPPMATTACHMENTS', 	SP_PREFIX.'sfpmattachments');

define('PMDIR', 		SPPLUGINDIR.'private-messaging/');
define('PMADMINDIR', 	SPPLUGINDIR.'private-messaging/admin/');
define('PMLIBDIR', 		SPPLUGINDIR.'private-messaging/library/');
define('PMAJAXDIR', 	SPPLUGINDIR.'private-messaging/ajax/');
define('PMFORMSDIR', 	SPPLUGINDIR.'private-messaging/forms/');
define('PMTAGSDIR', 	SPPLUGINDIR.'private-messaging/template-tags/');
define('PMSCRIPT', 		SPPLUGINURL.'private-messaging/resources/jscript/');
define('PMIMAGES', 		SPPLUGINURL.'private-messaging/resources/images/');
define('PMIMAGESMOB',	SPPLUGINURL.'private-messaging/resources/images/mobile/');
define('PMCSS',		 	SPPLUGINURL.'private-messaging/resources/css/');
define('PMTEMPDIR', 	SPPLUGINDIR.'private-messaging/template-files/');

add_action('sph_admin_menu', 									    'sp_pm_menu');
add_action('init', 												    'sp_pm_localization');
add_action('sph_activate_private-messaging/sp-pm-plugin.php', 	    'sp_pm_install');
add_action('sph_deactivate_private-messaging/sp-pm-plugin.php',     'sp_pm_deactivate');
add_action('sph_uninstalled', 								    	'sp_pm_sp_uninstall');
add_action('sph_uninstall_private-messaging/sp-pm-plugin.php',     	'sp_pm_uninstall');
add_action('sph_print_plugin_scripts', 								'sp_pm_load_js');
add_action('sph_scripts_admin_end', 						    	'sp_pm_load_admin_js');
add_action('sph_get_query_vars', 							    	'sp_pm_get_query_vars');
add_action('sph_get_def_query_vars', 						    	'sp_pm_get_def_query_vars');
add_action('sph_print_plugin_styles', 								'sp_pm_header');
add_action('sph_member_created', 						    		'sp_pm_member_add');
add_action('sph_member_deleted', 						    		'sp_pm_member_del');
add_action('sph_UpdateProfileGlobalOptions',				    	'sp_pm_profile_save', 10, 2);
add_action('sph_UpdateProfileDisplayOptions',				    	'sp_pm_display_save', 10, 2);
add_action('sph_pm_cron', 									    	'sp_pm_remove_pms');
add_action('sph_activated', 				                        'sp_pm_sp_activate');
add_action('sph_deactivated', 				                        'sp_pm_sp_deactivate');
add_action('sph_toolbox_housekeeping_profile_tabs',                 'sp_pm_reset_profile_tabs');
add_action('admin_footer',                                          'sp_pm_upgrade_check');
add_action('sph_plugin_update_private-messaging/sp-pm-plugin.php',  'sp_pm_upgrade_check');
add_action('sph_permissions_reset',                                 'sp_pm_reset_permissions');
add_action('sp_pm_header_begin',                                    'sp_pm_start');
add_action('sph_footer_end',                                        'sp_pm_footer');
add_action('sph_stats_scheduler',                                   'sp_pm_scheduler');
add_action('sph_admin_caps_form', 					     	        'sp_pm_admin_cap_form', 10, 2);
add_action('sph_admin_caps_list', 						            'sp_pm_admin_cap_list', 10, 2);
add_action('sph_user_class_member',						            'sp_pm_add_to_user_class');
add_action('sph_user_class_member_small',				            'sp_pm_add_to_user_class');
add_action('sph_admin_menu',                                        'sp_pm_admin_menu');
add_action('sph_add_style',											'sp_pm_add_style_icon');

add_filter('sph_plugins_active_buttons', 				'sp_pm_uninstall_option', 10, 2);
add_filter('sph_admin_help-admin-plugins', 				'sp_pm_admin_help', 10, 3);
add_filter('sph_perms_tooltips', 						'sp_pm_tooltips', 10, 2);
add_filter('sph_query_vars', 							'sp_pm_query_vars');
add_filter('sph_rewrite_rules_start', 					'sp_pm_rewrite_rules', 10, 3);
add_filter('sph_pageview', 								'sp_pm_pageview');
add_filter('sph_canonical_url', 						'sp_pm_canonical_url');
add_filter('sph_page_title', 							'sp_pm_page_title', 10, 2);
add_filter('sph_editor_check', 							'sp_pm_editor');
add_filter('sph_syntax_check', 							'sp_pm_editor');
add_filter('sph_memberdata_update_query', 				'sp_pm_member_update', 10, 4);
add_filter('sph_ProfileGlobalOptionsFormBottom',		'sp_pm_profile_options', 10, 2);
add_filter('sph_ProfileDisplayOptionsFormBottom',		'sp_pm_display_options', 10, 2);
add_filter('sph_ProfileFormSave_manage-buddies',		'sp_pm_profile_save_buddies', 10, 3);
add_filter('sph_ProfileFormSave_manage-adversaries',	'sp_pm_profile_save_adversaries', 10, 3);
add_filter('sph_MemberListActions', 					'sp_pm_members_send_button');
add_filter('sph_DefaultViewTemplate',					'sp_pm_template_name', 10, 2);
add_filter('sph_AllRSSButton',							'sp_pm_remove_rss_button');
add_filter('sph_topicview_query',						'sp_pm_topic_query');
add_filter('sph_OnlineActivityPageview',				'sp_pm_online_activity', 10, 5);
add_filter('sph_BreadCrumbs', 				            'sp_pm_breadcrumb', 10, 5);
add_filter('sph_BreadCrumbsMobile', 		            'sp_pm_breadcrumbMobile', 10, 2);
add_filter('sph_plupload_check', 				        'sp_pm_uploads_check');
add_filter('sph_plupload_check_css',    		        'sp_pm_uploads_check');
add_filter('sph_admin_caps_new', 			            'sp_pm_admin_caps_new', 10, 2);
add_filter('sph_admin_caps_update', 		            'sp_pm_admin_caps_update', 10, 3);
add_filter('sph_ShowAdminLinks', 		                'sp_pm_admin_links', 10, 2);
add_filter('sph_members_list_records',	                'sp_pm_add_to_member_class', 10, 2);

# personal provacy data export
add_filter('sp_privacy_forum_section_data', 			'sp_pm_privacy_message_listing', 10, 7);
add_action('sph_options_members_privacy_export',		'sp_pm_privacy_option');
add_action('sph_option_members_save',					'sp_pm_privacy_option_save');




# Ajax Handlers
add_action('wp_ajax_pm-manage', 'sp_pm_ajax_manage');
add_action('wp_ajax_nopriv_pm-manage', 'sp_pm_ajax_manage');
add_action('wp_ajax_pm-post', 'sp_pm_ajax_post');
add_action('wp_ajax_nopriv_pm-post', 'sp_pm_ajax_post');


function sp_pm_admin_menu($parent) {
    if (!SP()->auths->current_user_can('SPF Manage PM')) return;
	add_submenu_page($parent, esc_attr(__('Private Messaging', 'sp-pm')), esc_attr(__('Private Messaging', 'sp-pm')), 'read', SP_FOLDER_NAME.'/admin/panel-plugins/spa-plugins.php&tab=plugin&admin=sp_pm_admin_options&save=sp_pm_admin_save_options&form=1&panel='.urlencode(__('Private Messaging', 'sp-pm')), 'dummy');
}

function sp_pm_menu() {
	$panels = array(
                __('Options', 'sp-pm') => array('admin' => 'sp_pm_admin_options', 'save' => 'sp_pm_admin_save_options', 'form' => 1, 'id' => 'pmopt'),
                __('Member PM Stats', 'sp-pm') => array('admin' => 'sp_pm_admin_stats', 'save' => '', 'form' => 0, 'id' => 'pmstats')
				);
    SP()->plugin->add_admin_panel(__('Private Messaging', 'sp-pm'), 'SPF Manage PM', __('Options for Private Messaging', 'sp-pm'), 'icon-PrivateMessaging', $panels, 7);
}

function sp_pm_add_style_icon() {
	echo('.spaicon-PrivateMessaging:before {content: "\e108";}');
}

function sp_pm_admin_options() {
    require_once PMADMINDIR.'sp-pm-admin-options.php';
	sp_pm_admin_options_form();
}

function sp_pm_admin_stats() {
    require_once PMADMINDIR.'sp-pm-admin-stats.php';
	sp_pm_admin_stats_form();
}

function sp_pm_admin_save_options() {
    require_once PMADMINDIR.'sp-pm-admin-options-save.php';
    return sp_pm_admin_options_save();
}

function sp_pm_uninstall_option($actionlink, $plugin) {
    require_once PMDIR.'sp-pm-uninstall.php';
    $actionlink = sp_pm_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_pm_admin_help($file, $tag, $lang) {
    if ($tag == '[private-messaging]' || $tag == '[pm-addressing]' || $tag == '[pm-access]' || $tag == '[pm-removal]' || $tag == '[pm-stats]' || $tag == '[pm-adversaries]') $file = PMADMINDIR.'sp-pm-admin-help.'.$lang;
    return $file;
}

function sp_pm_tooltips($tips, $t) {
    $tips['use_pm'] = $t.__('Can use the private messaging system', 'sp-pm');
    return $tips;
}

function sp_pm_localization() {
	sp_plugin_localisation('sp-pm');
}

function sp_pm_uninstall() {
    require_once PMDIR.'sp-pm-uninstall.php';
    sp_pm_do_uninstall();
}

function sp_pm_sp_uninstall($admins) {
    require_once PMDIR.'sp-pm-uninstall.php';
    sp_pm_do_sp_uninstall($admins);
}

function sp_pm_install() {
    require_once PMDIR.'sp-pm-install.php';
    sp_pm_do_install();
}

function sp_pm_deactivate() {
    require_once PMDIR.'sp-pm-uninstall.php';
    sp_pm_do_deactivate();
}

function sp_pm_sp_activate() {
    require_once PMDIR.'sp-pm-install.php';
    sp_pm_do_sp_activate();
}

function sp_pm_sp_deactivate() {
    require_once PMDIR.'sp-pm-uninstall.php';
    sp_pm_do_sp_deactivate();
}

function sp_pm_upgrade_check() {
    require_once PMDIR.'sp-pm-upgrade.php';
    sp_pm_do_upgrade_check();
}

function sp_pm_reset_permissions() {
    require_once PMDIR.'sp-pm-install.php';
    sp_pm_do_reset_permissions();
}

function sp_pm_ajax_manage() {
    require_once PMLIBDIR.'sp-pm-components.php';
    require_once PMLIBDIR.'sp-pm-database.php';
    require_once PMAJAXDIR.'sp-pm-ajax-manage.php';
}

function sp_pm_ajax_post() {
    require_once PMAJAXDIR.'sp-pm-ajax-post.php';
}

function sp_pm_load_js($footer) {
    require_once PMLIBDIR.'sp-pm-components.php';
	sp_pm_do_load_js($footer);
}

function sp_pm_load_admin_js($footer) {
	$script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? PMSCRIPT.'sp-pm-admin.js' : PMSCRIPT.'sp-pm-admin.min.js';
	wp_enqueue_script('sp-pm-admin', $script, false, false, $footer);
}

function sp_pm_get_query_vars() {
	SP()->rewrites->pageData['pm'] = SP()->filters->str(get_query_var('sf_pm'));
	SP()->rewrites->pageData['box'] = SP()->filters->str(get_query_var('sf_box'));
	SP()->rewrites->pageData['thread'] = SP()->filters->str(get_query_var('sf_thread'));
    SP()->rewrites->pageData['member'] = (int) get_query_var('sf_member');
	SP()->rewrites->pageData['page'] = get_query_var('sf_page');

    if (SP()->rewrites->pageData['pm'] == 'send') {
        SP()->rewrites->pageData['box'] = 'inbox';
        if (SP()->rewrites->pageData['member'] == 0) SP()->rewrites->pageData['member'] = -1;
    }

	if (empty(SP()->rewrites->pageData['pm'])) SP()->rewrites->pageData['pm'] = 0;
}

function sp_pm_query_vars($vars) {
	$vars[] = 'sf_pm';
	$vars[] = 'sf_box';
	$vars[] = 'sf_thread';
	$vars[] = 'sf_page';
    return $vars;
}

function sp_pm_get_def_query_vars($stuff) {
    require_once PMLIBDIR.'sp-pm-components.php';
	sp_pm_do_get_def_query_vars($stuff);
}

function sp_pm_breadcrumb($breadCrumbs, $args, $crumbEnd, $crumbSpace, $treeCount) {
    require_once PMLIBDIR.'sp-pm-components.php';
    $breadCrumbs = sp_pm_do_breadcrumb($breadCrumbs, $args, $crumbEnd, $crumbSpace, $treeCount);
    return $breadCrumbs;
}

function sp_pm_breadcrumbMobile($breadCrumbs, $args) {
    require_once PMLIBDIR.'sp-pm-components.php';
    $breadCrumbs = sp_pm_do_breadcrumbMobile($breadCrumbs, $args);
    return $breadCrumbs;
}

function sp_pm_header() {
    require_once PMLIBDIR.'sp-pm-components.php';
	sp_pm_do_header();
}

function sp_pm_rewrite_rules($rules, $slugmatch, $slug) {
    require_once PMLIBDIR.'sp-pm-components.php';
	$rules = sp_pm_do_rewrite_rules($rules, $slugmatch, $slug);
    return $rules;
}

function sp_pm_pageview($pageview) {
    require_once PMLIBDIR.'sp-pm-components.php';
	$pageview = sp_pm_do_pageview($pageview);
    return $pageview;
}

function sp_pm_canonical_url($url) {
    require_once PMLIBDIR.'sp-pm-components.php';
	$url = sp_pm_do_canonical_url($url);
    return $url;
}

function sp_pm_page_title($title, $sep) {
    require_once PMLIBDIR.'sp-pm-components.php';
	$title = sp_pm_do_page_title($title, $sep);
    return $title;
}

function sp_pm_editor($pageview) {
    if (sp_pm_get_auth('use_pm')) $pageview .= ' pm pmthread';
    return $pageview;
}

function sp_pm_member_update($sql, $itemname, $itemdata, $userid) {
	if ($itemname == 'buddies') $sql = 'UPDATE '.SPMEMBERS." SET buddies = ".$itemdata." WHERE user_id=$userid";
	return $sql;
}

function sp_pm_member_add($userid) {
    require_once PMLIBDIR.'sp-pm-components.php';
	sp_pm_do_member_add($userid);
}

function sp_pm_member_del($userid) {
    require_once PMLIBDIR.'sp-pm-components.php';
	sp_pm_do_member_del($userid);
}

function sp_pm_remove_pms() {
    require_once PMLIBDIR.'sp-pm-components.php';
	sp_pm_do_remove_pms();
}

function sp_pm_profile_options($content, $userid) {
    require_once PMLIBDIR.'sp-pm-components.php';
	$content = sp_pm_do_profile_options($content, $userid);
	return $content;
}

function sp_pm_display_options($content, $userid) {
    require_once PMLIBDIR.'sp-pm-components.php';
	$content = sp_pm_do_display_options($content, $userid);
	return $content;
}

function sp_pm_profile_save($message, $userid) {
	$options = SP()->memberData->get($userid, 'user_options');
    $update = apply_filters('sph_ProfileUserPMEmailUpdate', true);
    if ($update) if (isset($_POST['pmemail'])) $options['pmemail'] = true; else $options['pmemail'] = false;
    $update = apply_filters('sph_ProfileUserPMOptOutUpdate', true);
	if ($update) if (isset($_POST['pmoptout'])) $options['pmoptout'] = true; else $options['pmoptout'] = false;
	SP()->memberData->update($userid, 'user_options', $options);
    return $message;
}

function sp_pm_display_save($message, $userid) {
   	$options = SP()->memberData->get($userid, 'user_options');
    $update = apply_filters('sph_ProfileUserPMOrderUpdate', true);
	if ($update) if (isset($_POST['pmsortorder'])) $options['pmsortorder'] = true; else $options['pmsortorder'] = false;
    $update = apply_filters('sph_ProfileUserPMOpenAllUpdate', true);
	if ($update) if (isset($_POST['pmopenall'])) $options['pmopenall'] = true; else $options['pmopenall'] = false;
    SP()->memberData->update($userid, 'user_options', $options);
    return $message;
}

function sp_pm_add_to_user_class(&$user) {
   	$user->buddies = SP()->DB->select("SELECT buddy_id FROM ".SPPMBUDDIES." WHERE user_id=$user->ID", 'col');
   	$user->adversaries = SP()->DB->select("SELECT adversary_id FROM ".SPPMADVERSARIES." WHERE user_id=$user->ID", 'col');
}

function sp_pm_add_to_member_class($record, $data) {
   	$record->buddies = SP()->DB->select("SELECT buddy_id FROM ".SPPMBUDDIES." WHERE user_id=$data->user_id", 'col');
   	$record->adversaries = SP()->DB->select("SELECT adversary_id FROM ".SPPMADVERSARIES." WHERE user_id=$data->user_id", 'col');
    return $record;
}

function sp_pm_profile_save_buddies($message, $thisUser, $thisForm) {
    require_once PMLIBDIR.'sp-pm-components.php';
	$message = sp_pm_do_save_buddies($thisUser, $message);
	return $message;
}

function sp_pm_profile_save_adversaries($message, $thisUser, $thisForm) {
    require_once PMLIBDIR.'sp-pm-components.php';
	$message = sp_pm_do_save_adversaries($thisUser, $message);
	return $message;
}

function sp_pm_members_send_button($content) {
    require_once PMLIBDIR.'sp-pm-components.php';
	$content = sp_pm_do_members_send_button($content);
	return $content;
}

function sp_pm_template_name($name, $pageview) {
    require_once PMLIBDIR.'sp-pm-components.php';
    require_once PMLIBDIR.'sp-pm-template-functions.php';
    require_once PMLIBDIR.'sp-pm-threads-class.php';
    require_once PMLIBDIR.'sp-pm-messages-class.php';
    $name = spPmTemplateName($name, $pageview);
    return $name;
}

function sp_pm_remove_rss_button($output) {
    if (SP()->rewrites->pageData['pageview'] == 'pm') $output = ''; # remove all rss button on pm views
	return $output;
}

function sp_pm_topic_query($query) {
    require_once PMLIBDIR.'sp-pm-components.php';
	$query = sp_pm_do_topic_query($query);
	return $query;
}

global $pmOnline;
$pmOnline = false;
function sp_pm_online_activity($out, $user, $generalClass, $titleClass, $userClass) {
    require_once PMLIBDIR.'sp-pm-components.php';
	$out = sp_pm_do_online_activity($out, $user, $generalClass, $titleClass, $userClass);
	return $out;
}

function sp_pm_reset_profile_tabs() {
    require_once PMLIBDIR.'sp-pm-components.php';
    sp_pm_do_reset_profile_tabs();
}

function sp_pm_uploads_check($check) {
    $pm = SP()->options->get('pm');
    if ($pm['uploads']) $check.= ' pm';
    return $check;
}

function sp_pm_footer() {
    require_once PMLIBDIR.'sp-pm-components.php';
    if (SP()->isForum && (SP()->rewrites->pageData['pageview'] == 'pm' || SP()->rewrites->pageData['pageview'] == 'pmthread')) sp_pm_do_footer();
}

function sp_pm_scheduler() {
	$pmdata = SP()->options->get('pm');
    if ($pmdata['remove'] && !wp_next_scheduled('sph_pm_cron')) {
		wp_schedule_event(time(), 'daily', 'sph_pm_cron');
    }
}

function sp_pm_start() {
    require_once PMLIBDIR.'sp-pm-components.php';
    sp_pm_do_start();
}

function sp_pm_admin_cap_form($user) {
	require_once PMLIBDIR.'sp-pm-components.php';
	sp_pm_do_admin_cap_form($user);
}

function sp_pm_admin_cap_list($user) {
	require_once PMLIBDIR.'sp-pm-components.php';
	sp_pm_do_admin_cap_list($user);
}

function sp_pm_admin_caps_new($newadmin, $user) {
	require_once PMLIBDIR.'sp-pm-components.php';
	$newadmin = sp_pm_do_admin_caps_new($newadmin, $user);
	return $newadmin;
}

function sp_pm_admin_caps_update($still_admin, $remove_admin, $user) {
	require_once PMLIBDIR.'sp-pm-components.php';
	$still_admin = sp_pm_do_admin_caps_update($still_admin, $remove_admin, $user);
	return $still_admin;
}

function sp_pm_admin_links($out, $br) {
	if (SP()->auths->current_user_can('SPF Manage PM')) {
		$out.= sp_open_grid_cell();
		$out.= '<p><a href="'.admin_url('admin.php?page='.SP_FOLDER_NAME.'/admin/panel-plugins/spa-plugins.php&tab=plugin&admin=sp_pm_admin_options&save=sp_pm_admin_save_options&form=1').'">';
		$out.= SP()->theme->paint_icon('spIcon', PMIMAGES, "sp_ManagePM.png").$br;
		$out.= __('Private Messaging', 'sp-pm').'</a></p>';
		$out.= sp_close_grid_cell();
	}
    return $out;
}

# replacement auth check for post count access checking
function sp_pm_get_auth($check, $id='global', $user='') {
	# check for post count access
	$pmdata = SP()->options->get('pm');
	if ($pmdata['accessposts'] > 0) {
	    if (empty($user) || (isset(SP()->user->thisUser) && $user == SP()->user->thisUser->ID)) {
	    	$posts = SP()->user->thisUser->posts;
	    	$userid = SP()->user->thisUser->ID;
		} elseif (is_object($user)) {
			$posts = $user->posts;
			$userid = $user->ID;
		} else {
			$posts = SP()->memberData->get($user, 'posts');
			$userid = $user;
		}
		if (SP()->auths->forum_admin($userid) == false && $posts < $pmdata['accessposts']) return false;
	}
	return SP()->auths->get($check, $id, $user);
}

# privacy export of messages and options form
function sp_pm_privacy_message_listing($exportItems, $spUserData, $groupID, $groupLabel, $page, $number, $done) {
    require_once PMLIBDIR.'sp-pm-export.php';
	return sp_pm_privacy_do_message_listing($exportItems, $spUserData, $groupID, $groupLabel, $page, $number, $done);
}

function sp_pm_privacy_option() {
    require_once PMLIBDIR.'sp-pm-export.php';
	return sp_pm_privacy_do_option();
}

function sp_pm_privacy_option_save() {
    require_once PMLIBDIR.'sp-pm-export.php';
	sp_pm_privacy_do_option_save();
}


# Define Template Tags globally available (dont have to be enabled on template tag panel)

function sp_PmInboxButton($args='', $label='', $toolTip='') {
    require_once PMTAGSDIR.'sp-pm-inbox-button-tag.php';
    sp_PmInboxButtonTag($args, $label, $toolTip);
}

function sp_PostIndexSendPm($args='', $label='', $toolTip='') {
    require_once PMLIBDIR.'sp-pm-components.php';
    require_once PMTAGSDIR.'sp-pm-send-pm-button-tag.php';
    sp_PostIndexSendPmTag($args, $label, $toolTip);
}

function sp_ProfileSendPm($args='', $label='', $toolTip='') {
    require_once PMLIBDIR.'sp-pm-components.php';
    require_once PMTAGSDIR.'sp-pm-profile-send-pm-tag.php';
    sp_ProfileSendPmTag($args, $label, $toolTip);
}

function sp_pm_is_pmview() {
    require_once PMTAGSDIR.'sp-pm-pmview-tag.php';
    $value = sp_pm_do_is_pmview($args, $label, $toolTip);
    return $value;
}

function sp_pm_inbox($display=true, $usersonly=false) {
    require_once PMTAGSDIR.'sp-pm-inbox-tag.php';
    $pm = sp_pm_do_inbox($display, $usersonly);
    return $pm;
}

function sp_pm_send_pm($userid, $text='') {
    require_once PMTAGSDIR.'sp-pm-send-tag.php';
    sp_pm_do_send_pm($userid, $text);
}

function sp_pm_sidedash($show_avatar=true, $show_pm=true, $redirect=4, $show_admin_link=true, $show_login_link=true) {
    require_once PMTAGSDIR.'sp-pm-sidedash-tag.php';
    sp_pm_do_sidedash($show_avatar, $show_pm, $redirect, $show_admin_link, $show_login_link);
}
