<?php
/*
Simple:Press Plugin Title: Post Thanks
Version: 3.1.0
Item Id: 3944
Plugin URI: https://simple-press.com/downloads/thank-posts-plugin/
Description: A Simple:Press plugin for allowing the thanking of posts and a point system
Author: Simple:Press
Original Author: Chris Smith (updated by Simple Press Team)
Author URL: https://simple-press.com
Original Author URI: http://www.flexsim.com
Simple:Press Versions: 6.0 and above
A plugin for a Simple:Press member to thank someone for a post.
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPTHANKSDBVERSION',		3);

define('SPACTIVITY_THANKS',		SP()->activity->get_type('give thanks'));
define('SPACTIVITY_THANKED',	SP()->activity->get_type('receive thanks'));

define('THANKSDIR',			SPPLUGINDIR.'post-thanks/');
define('THANKSADMINDIR',	SPPLUGINDIR.'post-thanks/admin/');
define('THANKSAJAXDIR',		SPPLUGINDIR.'post-thanks/ajax/');
define('THANKSLIBDIR',		SPPLUGINDIR.'post-thanks/library/');
define('THANKSTAGSDIR',		SPPLUGINDIR.'post-thanks/template-tags/');
define('THANKSIMAGES',		SPPLUGINURL.'post-thanks/resources/images/');
define('THANKSCSS',			SPPLUGINURL.'post-thanks/resources/css/');
define('THANKSSCRIPT',		SPPLUGINURL.'post-thanks/resources/jscript/');

add_action('init',											  'sp_thanks_localization');
add_action('sph_admin_menu',								  'sp_thanks_menu');
add_action('sph_activate_post-thanks/sp-thanks-plugin.php',	  'sp_thanks_install');
add_action('sph_deactivate_post-thanks/sp-thanks-plugin.php', 'sp_thanks_deactivate');
add_action('sph_uninstall_post-thanks/sp-thanks-plugin.php',  'sp_thanks_uninstall');
add_action('sph_activated',									  'sp_thanks_sp_activate');
add_action('sph_deactivated',								  'sp_thanks_sp_deactivate');
add_action('sph_uninstalled',								  'sp_thanks_sp_uninstall');
add_action('sph_plugin_update_thanks/sp-thanks-plugin.php',	  'sp_thanks_upgrade_check');
add_action('admin_footer',									  'sp_thanks_upgrade_check');
add_action('sph_print_plugin_styles',						  'sp_thanks_head');
add_action('sph_permissions_reset',							  'sp_thanks_reset_permissions');
add_action('sph_print_plugin_scripts',						  'sp_thanks_load_js');

add_filter('sph_plugins_active_buttons',		'sp_thanks_uninstall_option', 10, 2);
add_filter('sph_perms_tooltips',				'sp_thanks_tooltips', 10, 2);
add_filter('sph_admin_help-admin-components',	'sp_thanks_admin_help', 10, 3);
add_filter('sph_topicview_combined_data',		'sp_thanks_post_records', 10, 3);
add_action('sph_user_class_member',				'sp_thanks_load_to_user_class');
add_action('sph_user_class_member_small',		'sp_thanks_load_to_user_class');

# Mycred Support
add_action('mycred_pre_init',			'sp_thanks_load_mycred', 2);
add_filter('add_sp_mycred_extension',	'sp_thanks_extend_mycred');
add_action('prefs_sp_mycred_extension', 'sp_thanks_prefs_create');
add_action('sph_post_thanks_actions',	'sp_thanks_save_mycred', 1, 2);

# Ajax Handler
add_action('wp_ajax_thanks',		'sp_thanks_ajax');
add_action('wp_ajax_nopriv_thanks',	'sp_thanks_ajax');


function sp_thanks_menu() {
	$subpanels = array(__('Post Thanks', 'sp-thanks') => array('admin' => 'sp_thanks_admin_options', 'save' => 'sp_thanks_admin_save_options', 'form' => 1, 'id' => 'thanksopt'));
	SP()->plugin->add_admin_subpanel('components', $subpanels);
}

function sp_thanks_admin_options() {
	require_once THANKSADMINDIR.'sp-thanks-admin-options.php';
	sp_thanks_admin_options_form();
}

function sp_thanks_admin_save_options() {
	require_once THANKSADMINDIR.'sp-thanks-admin-options-save.php';
	return sp_thanks_admin_options_save();
}

function sp_thanks_install() {
	require_once THANKSDIR.'sp-thanks-install.php';
	sp_thanks_do_install();
}

function sp_thanks_deactivate() {
	require_once THANKSDIR.'sp-thanks-uninstall.php';
	sp_thanks_do_deactivate();
}

function sp_thanks_uninstall() {
	require_once THANKSDIR.'sp-thanks-uninstall.php';
	sp_thanks_do_uninstall();
}

function sp_thanks_sp_activate() {
	require_once THANKSDIR.'sp-thanks-install.php';
	sp_thanks_do_sp_activate();
}

function sp_thanks_sp_deactivate() {
	require_once THANKSDIR.'sp-thanks-uninstall.php';
	sp_thanks_do_sp_deactivate();
}

function sp_thanks_sp_uninstall() {
	require_once THANKSDIR.'sp-thanks-uninstall.php';
	sp_thanks_do_sp_uninstall();
}

function sp_thanks_upgrade_check() {
	require_once THANKSDIR.'sp-thanks-upgrade.php';
	sp_thanks_do_upgrade_check();
}

function sp_thanks_uninstall_option($actionlink, $plugin) {
	require_once THANKSDIR.'sp-thanks-uninstall.php';
	$actionlink = sp_thanks_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_thanks_reset_permissions() {
	require_once THANKSDIR.'sp-thanks-install.php';
	sp_thanks_do_reset_permissions();
}

function sp_thanks_localization() {
	sp_plugin_localisation('sp-thanks');
}

function sp_thanks_tooltips($tips, $t) {
	$tips['thank_posts'] = $t.__('Can give thanks for a forum post', 'sp-thanks');
	return $tips;
}

function sp_thanks_admin_help($file, $tag, $lang) {
	if ($tag == '[thanks-options]' || $tag == '[thanks-points]' || $tag == '[thanks-levels]') $file = THANKSADMINDIR.'sp-thanks-admin-help.'.$lang;
	return $file;
}

function sp_thanks_head() {
	$css = SP()->theme->find_css(THANKSCSS, 'sp-thanks.css', 'sp-thanks.spcss');
	SP()->plugin->enqueue_style('sp-thanks', $css);
}

function sp_thanks_post_records($tData, $posts, $users) {
	require_once THANKSLIBDIR.'sp-thanks-components.php';
	return sp_thanks_do_post_records($tData, $posts, $users);
}

function sp_thanks_ajax(){
	require_once THANKSAJAXDIR.'sp-thanks-ajax.php';
}

function sp_thanks_load_js($footer) {
	require_once THANKSLIBDIR.'sp-thanks-components.php';
	sp_thanks_do_load_js($footer);
}

function sp_thanks_load_to_user_class(&$user) {
    $user->thanks  = SP()->activity->count('type='.SPACTIVITY_THANKS."&uid=$user->ID");
    $user->thanked = SP()->activity->count('type='.SPACTIVITY_THANKED."&uid=$user->ID");
}

# MyCred Support
function sp_thanks_load_mycred() {
	require_once THANKSLIBDIR.'sp-thanks-mycred.php';
}

function sp_thanks_extend_mycred($defs) {
	return sp_thanks_do_extend_mycred($defs);
}

function sp_thanks_prefs_create($args) {
	sp_thanks_do_prefs_create($args);
}

function sp_thanks_save_mycred($userid, $topicid) {
	require_once THANKSLIBDIR.'sp-thanks-mycred.php';
	sp_thanks_do_save_mycred($userid, $topicid);
}

# template functions

function sp_thanks_thank_the_post($args='', $label='', $thankedLabel='', $toolTip='', $thankedToolTip='') {
	require_once THANKSTAGSDIR.'sp-thanks-thank-the-post.php';
	sp_thanks_do_thank_the_post($args, $label, $thankedLabel, $toolTip, $thankedToolTip);
}

function sp_thanks_thanks_for_post($args='') {
	require_once THANKSTAGSDIR.'sp-thanks-thanks-for-post.php';
	sp_thanks_do_thanks_for_post($args);
}

function sp_thanks_user_stats($args='', $userid='', $label='') {
	require_once THANKSTAGSDIR.'sp-thanks-user-stats.php';
	sp_thanks_do_user_stats($args, $userid, $label);
}

function sp_thanks_post_user_reputation($args='', $label='') {
	require_once THANKSTAGSDIR.'sp-thanks-post-user-reputation.php';
	sp_thanks_do_post_user_reputation($args, $label);
}

function sp_thanks_profile_reputation($args='', $label='') {
	require_once THANKSTAGSDIR.'sp-thanks-profile-reputation.php';
	sp_thanks_do_profile_reputation($args, $label);
}

function sp_thanks_members_list_reputation($args='', $label='') {
	require_once THANKSTAGSDIR.'sp-thanks-members-list-reputation.php';
	sp_thanks_do_members_list_reputation($args, $label);
}

function sp_thanks_stats_top_thanked($args='', $label='') {
	require_once THANKSTAGSDIR.'sp-thanks-stats-top-thanked.php';
	sp_thanks_do_stats_top_thanked($args, $label);
}
