<?php
/*
Simple:Press Plugin Title: Polls
Version: 2.1.0
Item Id: 3919 
Plugin URI: https://simple-press.com/downloads/polls-plugin/
Description: A Simple:Press plugin for adding a poll to a forum post
Author; Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2016-03-11 16:47:24 -0800 (Fri, 11 Mar 2016) $
$Rev: 14048 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPPOLLSDBVERSION', 6);

define('SPPOLLS', 	     SP_PREFIX.'sfpolls');
define('SPPOLLSANSWERS', SP_PREFIX.'sfpollsanswers');
define('SPPOLLSVOTERS',  SP_PREFIX.'sfpollsvoters');

define('POLLSDIR', 		SPPLUGINDIR.'polls/');
define('POLLSADMINDIR', SPPLUGINDIR.'polls/admin/');
define('POLLSAJAXDIR', 	SPPLUGINDIR.'polls/ajax/');
define('POLLSLIBDIR', 	SPPLUGINDIR.'polls/library/');
define('POLLSSCRIPT', 	SPPLUGINURL.'polls/resources/jscript/');
define('POLLSTAGSDIR', 	SPPLUGINDIR.'polls/template-tags/');
define('POLLSCSS', 		SPPLUGINURL.'polls/resources/css/');
define('POLLSIMAGES',	SPPLUGINURL.'polls/resources/images/');
define('POLLSIMAGESMOB',SPPLUGINURL.'polls/resources/images/mobile/');

add_action('sph_activate_polls/sp-polls-plugin.php',     	'sp_polls_install');
add_action('sph_deactivate_polls/sp-polls-plugin.php',   	'sp_polls_deactivate');
add_action('sph_uninstall_polls/sp-polls-plugin.php',     	'sp_polls_uninstall');
add_action('sph_activated', 				                'sp_polls_sp_activate');
add_action('sph_deactivated', 				                'sp_polls_sp_deactivate');
add_action('sph_uninstalled', 								'sp_polls_sp_uninstall');
add_filter('sph_plugins_active_buttons', 		            'sp_polls_uninstall_option', 10, 2);
add_action('sph_plugin_update_polls/sp-polls-plugin.php',   'sp_polls_upgrade_check');
add_action('admin_footer',                                  'sp_polls_upgrade_check');
add_action('init', 											'sp_polls_localization');
add_action('sph_admin_menu', 								'sp_polls_menu');
add_action('sph_forum_create_forum_options', 				'sp_polls_create_forum');
add_action('sph_forum_forum_create', 						'sp_polls_create_forum_save');
add_action('sph_forum_edit_forum_options', 					'sp_polls_edit_forum');
add_action('sph_forum_forum_edit', 							'sp_polls_edit_forum_save');
add_action('sph_print_plugin_styles',						'sp_polls_head');
add_action('sph_print_plugin_scripts', 						'sp_polls_load_js');
add_action('sph_scripts_admin_end', 						'sp_polls_load_admin_js');
add_action('sph_permissions_reset', 						'sp_polls_reset_permissions');
add_action('sph_admin_caps_form', 					     	'sp_polls_admin_cap_form', 10, 2);
add_action('sph_admin_caps_list', 						    'sp_polls_admin_cap_list', 10, 2);
add_action('sph_admin_menu',                                'sp_polls_admin_menu');

add_filter('sph_admin_help-admin-plugins', 			        'sp_polls_admin_help', 10, 3);
add_filter('sph_forumview_query', 				            'sp_polls_forum_query');
add_filter('sph_forumview_forum_record', 		            'sp_polls_forum_records', 10, 2);
add_filter('sph_topicview_query', 				            'sp_polls_topic_query');
add_filter('sph_topicview_topic_record', 		            'sp_polls_forum_records', 10, 2);
add_filter('sph_topicview_post_records', 		            'sp_polls_post_records', 10, 2);
add_filter('sph_topic_editor_toolbar_buttons',	            'sp_polls_create_poll_form');
add_filter('sph_post_editor_toolbar_buttons',	            'sp_polls_create_poll_form');
add_filter('sph_perms_tooltips', 					        'sp_polls_tooltips', 10, 2);
add_filter('sph_admin_caps_new', 			                'sp_polls_admin_caps_new', 10, 2);
add_filter('sph_admin_caps_update', 		                'sp_polls_admin_caps_update', 10, 3);
add_filter('sph_ShowAdminLinks', 		                    'sp_polls_admin_links', 10, 2);
add_action('sph_add_style',									'sp_polls_add_style_icon');

add_filter('sph_internal_shortcodes',   'sp_polls_add_shortcode');
add_shortcode('sp_show_poll',           'sp_PollsShortcode');

# Mycred Support
add_action('mycred_pre_init',			'sp_polls_load_mycred', 2);
add_filter('add_sp_mycred_extension',	'sp_polls_extend_mycred');
add_action('prefs_sp_mycred_extension', 'sp_polls_prefs_create');
add_action('sph_poll_created', 			'sp_polls_create_save_mycred', 1, 2);
add_action('sph_poll_voted', 			'sp_polls_vote_save_mycred', 1, 3);

# Ajax Handlers
add_action('wp_ajax_polls-create',			'sp_polls_ajax_create');
add_action('wp_ajax_nopriv_polls-create',	'sp_polls_ajax_create');
add_action('wp_ajax_polls-manage',			'sp_polls_ajax_manage');
add_action('wp_ajax_nopriv_polls-manage',	'sp_polls_ajax_manage');
add_action('wp_ajax_polls-edit',			'sp_polls_ajax_edit');
add_action('wp_ajax_nopriv_polls-edit',		'sp_polls_ajax_edit');
add_action('wp_ajax_polls-log',				'sp_polls_ajax_log');
add_action('wp_ajax_nopriv_polls-log',		'sp_polls_ajax_log');


function sp_polls_admin_menu($parent) {
    if (!SP()->auths->current_user_can('SPF Manage Polls')) return;
	add_submenu_page($parent, esc_attr(__('Polls', 'sp-polls')), esc_attr(__('Polls', 'sp-polls')), 'read', SP_FOLDER_NAME.'/admin/panel-plugins/spa-plugins.php&tab=plugin&admin=sp_polls_admin_options&save=sp_polls_admin_options_save&form=1&panel='.urlencode(__('Polls', 'sp-polls')), 'dummy');
}

function sp_polls_menu() {
	$panels = array(
                __('Options', 'sp-polls') => array('admin' => 'sp_polls_admin_options', 'save' => 'sp_polls_admin_options_save', 'form' => 1, 'id' => 'pollsoptions'),
                __('Manage Polls', 'sp-polls') => array('admin' => 'sp_polls_admin_manage', 'save' => '', 'form' => 0, 'id' => 'pollmanage')
				);
    SP()->plugin->add_admin_panel(__('Polls', 'sp-polls'), 'SPF Manage Polls', __('Manage your polls', 'sp-polls'), 'icon-Polls', $panels, 7);
}

function sp_polls_admin_options() {
    require_once POLLSADMINDIR.'sp-polls-admin-options.php';
	sp_polls_admin_options_form();
}

function sp_polls_admin_options_save() {
    require_once POLLSADMINDIR.'sp-polls-admin-options-save.php';
    return sp_polls_admin_save_options();
}

function sp_polls_admin_manage() {
    require_once POLLSADMINDIR.'sp-polls-admin-manage.php';
	sp_polls_admin_manage_form();
}

function sp_polls_localization() {
	sp_plugin_localisation('sp-polls');
}

function sp_polls_tooltips($tips, $t) {
    $tips['create_poll'] = $t.__('Can create and manage a poll in a forum post', 'sp-polls');
    $tips['vote_poll'] = $t.__('Can vote in a poll in a forum post', 'sp-polls');
    return $tips;
}

function sp_polls_install() {
    require_once POLLSDIR.'sp-polls-install.php';
    sp_polls_do_install();
}

function sp_polls_uninstall($admins) {
    require_once POLLSDIR.'sp-polls-uninstall.php';
    sp_polls_do_uninstall($admins);
}

function sp_polls_uninstall_option($actionlink, $plugin) {
    require_once POLLSDIR.'sp-polls-uninstall.php';
    $actionlink = sp_polls_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_polls_deactivate() {
    require_once POLLSDIR.'sp-polls-uninstall.php';
    sp_polls_do_deactivate();
}

function sp_polls_sp_uninstall($admins) {
	require_once POLLSDIR.'sp-polls-uninstall.php';
    sp_polls_do_sp_uninstall($admins);
}

function sp_polls_sp_activate() {
	require_once POLLSDIR.'sp-polls-install.php';
    sp_polls_do_sp_activate();
}

function sp_polls_sp_deactivate() {
	require_once POLLSDIR.'sp-polls-uninstall.php';
    sp_polls_do_sp_deactivate();
}

function sp_polls_upgrade_check() {
    require_once POLLSDIR.'sp-polls-upgrade.php';
    sp_polls_do_upgrade_check();
}

function sp_polls_reset_permissions() {
    require_once POLLSDIR.'sp-polls-install.php';
    sp_polls_do_reset_permissions();
}

function sp_polls_admin_help($file, $tag, $lang) {
    if ($tag == '[polls-options]' || $tag == '[polls-tracking]' || $tag == '[polls-answers]' || $tag == '[polls-results]' || $tag == '[polls-manage]' || $tag == '[polls-display]') $file = POLLSADMINDIR.'sp-polls-admin-help.'.$lang;
    return $file;
}

function sp_polls_create_forum() {
    require_once POLLSLIBDIR.'sp-polls-components.php';
	sp_polls_do_create_forum();
}

function sp_polls_create_forum_save($forumid) {
	if (isset($_POST['forum_polls'])) $forumpoll = 1; else $forumpoll = 0;
	SP()->DB->execute('UPDATE '.SPFORUMS." SET polls=$forumpoll WHERE forum_id=$forumid");
    SP()->auths->reset_cache();
}

function sp_polls_edit_forum($forum) {
    require_once POLLSLIBDIR.'sp-polls-components.php';
	sp_polls_do_edit_forum($forum);
}

function sp_polls_edit_forum_save($forumid) {
	if (isset($_POST['forum_polls'])) $forumpoll = 1; else $forumpoll = 0;
	SP()->DB->execute('UPDATE '.SPFORUMS." SET polls=$forumpoll WHERE forum_id=$forumid");
    SP()->auths->reset_cache();
}

function sp_polls_forum_query($query) {
	$query->fields.= ', polls';
	return $query;
}

function sp_polls_forum_records($data, $record) {
	$data->polls = $record->polls;
	return $data;
}

function sp_polls_topic_query($query) {
	$query->fields.= ', polls, poll';
	return $query;
}

function sp_polls_post_records($data, $record) {
	$data->poll = $record->poll;
	return $data;
}

function sp_polls_create_poll_form($out) {
	require_once POLLSLIBDIR.'sp-polls-components.php';
	$out = sp_polls_do_create_poll_form($out);
	return $out;
}

function sp_polls_head() {
    require_once POLLSLIBDIR.'sp-polls-components.php';
    sp_polls_do_head();
}

function sp_polls_load_js($footer) {
    require_once POLLSLIBDIR.'sp-polls-components.php';
	sp_polls_do_load_js($footer);
}

function sp_polls_load_admin_js() {
    require_once POLLSLIBDIR.'sp-polls-components.php';
	sp_polls_do_load_admin_js();
}

function sp_polls_ajax_create() {
    require_once POLLSAJAXDIR.'sp-polls-ajax-create.php';
}

function sp_polls_ajax_manage() {
    require_once POLLSLIBDIR.'sp-polls-components.php';
    require_once POLLSAJAXDIR.'sp-polls-ajax-manage.php';
}

function sp_polls_ajax_edit() {
    require_once POLLSAJAXDIR.'sp-polls-ajax-edit.php';
}

function sp_polls_ajax_log() {
    require_once POLLSAJAXDIR.'sp-polls-ajax-log.php';
}

function sp_polls_admin_save_poll() {
    require_once POLLSLIBDIR.'sp-polls-components.php';
    return sp_polls_do_admin_save_poll();
}

function sp_polls_admin_cap_form($user) {
	require_once POLLSLIBDIR.'sp-polls-components.php';
	sp_poll_do_admin_cap_form($user);
}

function sp_polls_admin_cap_list($user) {
	require_once POLLSLIBDIR.'sp-polls-components.php';
	sp_poll_do_admin_cap_list($user);
}

function sp_polls_admin_caps_new($newadmin, $user) {
	require_once POLLSLIBDIR.'sp-polls-components.php';
	$newadmin = sp_poll_do_admin_caps_new($newadmin, $user);
	return $newadmin;
}

function sp_polls_admin_caps_update($still_admin, $remove_admin, $user) {
	require_once POLLSLIBDIR.'sp-polls-components.php';
	$still_admin = sp_poll_do_admin_caps_update($still_admin, $remove_admin, $user);
	return $still_admin;
}

# MyCred Support
function sp_polls_load_mycred() {
    require_once POLLSLIBDIR.'sp-polls-mycred.php';
}

function sp_polls_extend_mycred($defs) {
    return sp_polls_do_extend_mycred($defs);
}

function sp_polls_prefs_create($args) {
	sp_polls_do_prefs_create($args);
}

function sp_polls_create_save_mycred($response, $userid) {
    require_once POLLSLIBDIR.'sp-polls-mycred.php';
	sp_polls_do_create_save_mycred($response, $userid);
}

function sp_polls_vote_save_mycred($pollid, $voterid, $ownerid) {
    require_once POLLSLIBDIR.'sp-polls-mycred.php';
	sp_polls_do_vote_save_mycred($pollid, $voterid, $ownerid);
}

function sp_polls_admin_links($out, $br) {
	if (SP()->auths->current_user_can('SPF Manage Polls')) {
		$out.= sp_open_grid_cell();
		$out.= '<p><a href="'.admin_url('admin.php?page='.SP_FOLDER_NAME.'/admin/panel-plugins/spa-plugins.php&tab=plugin&admin=sp_polls_admin_options&save=sp_polls_admin_options_save&form=1').'">';
		$out.= SP()->theme->paint_icon('spIcon', POLLSIMAGES, "sp_ManagePolls.png").$br;
		$out.= __('Polls', 'sp-polls').'</a></p>';
		$out.= sp_close_grid_cell();
	}
    return $out;
}

function sp_polls_add_style_icon() {
	echo('.spaicon-Polls:before { content: "\e10a";}');
}

/* shortcode stuff */

function sp_polls_add_shortcode($codes) {
    $codes[] = 'sp_show_poll';
    return $codes;
}

function sp_PollsShortcode($atts) {
	require_once POLLSLIBDIR.'sp-polls-components.php';
	$content = sp_polls_do_PollsShortcode($atts);
	return $content;
}
