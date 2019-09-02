<?php
/*
Simple:Press Plugin Title: Reputation System
Version: 2.1.0
Item Id: 12726
Plugin URI: https://simple-press.com/downloads/reputation/
Description: A Simple:Press plugin for adding a reputation system to a Simple:Press forum
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-11-13 20:42:11 -0600 (Tue, 13 Nov 2018) $
$Rev: 15818 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPREPDBVERSION', 1);

define('SPACTIVITY_REPUTATION', SP()->activity->get_type('reputation'));

define('SPREPDIR',          SPPLUGINDIR.'reputation/');
define('SPREPADMINDIR',     SPPLUGINDIR.'reputation/admin/');
define('SPREPAJAXDIR', 	    SPPLUGINDIR.'reputation/ajax/');
define('SPREPLIBDIR',       SPPLUGINDIR.'reputation/library/');
define('SPREPLIBURL',       SPPLUGINURL.'reputation/library/');
define('SPREPCSS', 		    SPPLUGINURL.'reputation/resources/css/');
define('SPREPSCRIPT', 	    SPPLUGINURL.'reputation/resources/jscript/');
define('SPREPIMAGES',       SPPLUGINURL.'reputation/resources/images/');
define('SPREPIMAGESDIR', 	SPPLUGINDIR.'reputation/resources/images/');
define('SPREPTAGS', 	    SPPLUGINDIR.'reputation/template-tags/');

add_action('init', 										             'sp_reputation_localization');
add_action('sph_activate_reputation/sp-reputation-plugin.php',       'sp_reputation_install');
add_action('sph_deactivate_reputation/sp-reputation-plugin.php',     'sp_reputation_deactivate');
add_action('sph_uninstall_reputation/sp-reputation-plugin.php',      'sp_reputation_uninstall');
add_action('sph_activated', 				                         'sp_reputation_sp_activate');
add_action('sph_deactivated', 				                         'sp_reputation_sp_deactivate');
add_action('sph_uninstalled', 								         'sp_reputation_sp_uninstall');
add_action('sph_plugin_update_reputation/sp-reputation-plugin.php',  'sp_reputation_upgrade_check');
add_action('admin_footer',                                           'sp_reputation_upgrade_check');
add_action('sph_permissions_reset', 						         'sp_reputation_reset_permissions');
add_action('sph_add_style',											 'sp_reputation_add_style_icon');
add_action('sph_admin_menu',                                         'sp_reputation_admin_menu');
add_action('sph_admin_menu', 									     'sp_reputation_menu');
add_action('sph_admin_caps_form', 					     	         'sp_reputation_admin_cap_form', 10, 2);
add_action('sph_admin_caps_list', 						             'sp_reputation_admin_cap_list', 10, 2);
add_action('sph_integration_storage_panel_location', 			     'sp_reputation_storage_location');
add_action('sph_integration_storage_save', 						     'sp_reputation_storage_save');
add_action('sph_scripts_admin_end', 						    	 'sp_reputation_load_admin_js');
add_action('sph_member_created', 						    	 	 'sp_reputation_member_add');
add_action('sph_member_deleted', 						    		 'sp_reputation_member_del');
add_action('wp_login', 			                                     'sp_reputation_register_check', 9999, 2);
add_action('sph_new_post',                                           'sp_reputation_post_check');
add_action('sph_print_plugin_styles',                                'sp_reputation_add_css');
add_action('sph_user_class_object',                                  'sp_reputation_add_user_class');
add_action('sph_topic_delete',                                       'sp_reputation_topic_delete');
add_action('sph_post_delete',                                        'sp_reputation_post_delete');

add_filter('sph_perms_tooltips', 			'sp_reputation_tooltips', 10, 2);
add_filter('sph_integration_tooltips', 	    'sp_reputation_storage_tooltip');
add_filter('sph_plugins_active_buttons',    'sp_reputation_uninstall_option', 10, 2);
add_filter('sph_admin_help-admin-plugins',  'sp_reputation_admin_help', 10, 3);
add_filter('sph_admin_caps_new', 			'sp_reputation_admin_caps_new', 10, 2);
add_filter('sph_admin_caps_update',         'sp_reputation_admin_caps_update', 10, 3);
add_filter('sph_ShowAdminLinks', 		    'sp_reputation_admin_links', 10, 2);
add_filter('sph_SectionStartRowClass',      'sp_reputation_add_post_class', 10, 3);
add_filter('sph_members_list_query',        'sp_reputation_members_query');
add_filter('sph_members_list_records',      'sp_reputation_members_records', 10, 2);

# Ajax Handler
add_action('wp_ajax_reputation-manage',			'sp_reputation_ajax_manage');
add_action('wp_ajax_nopriv_reputation-manage',	'sp_reputation_ajax_manage');

# Mycred Support
add_action('mycred_pre_init',			'sp_reputation_load_mycred', 2);
add_filter('add_sp_mycred_extension',	'sp_reputation_extend_mycred');
add_action('prefs_sp_mycred_extension', 'sp_reputation_prefs_create');
add_action('sph_reputation_given',	    'sp_reputation_save_mycred', 1, 4);

# MyCred Support
function sp_reputation_load_mycred() {
	include_once(SPREPLIBDIR.'sp-reputation-mycred.php');
}

function sp_reputation_extend_mycred($defs) {
	return sp_reputation_do_extend_mycred($defs);
}

function sp_reputation_prefs_create($args) {
	sp_reputation_do_prefs_create($args);
}

function sp_reputation_save_mycred($giver_id, $receiver_id, $amount, $receiver_rep) {
	include_once(SPREPLIBDIR.'sp-reputation-mycred.php');
	sp_reputation_do_save_mycred($giver_id, $receiver_id, $amount);
}

# plugin hoks
function sp_reputation_add_style_icon() {
	echo '.spaicon-Reputation:before {content: "\e115";}';
}

function sp_reputation_admin_menu($parent) {
    if (!SP()->auths->current_user_can('SPF Manage Reputation')) return;
	add_submenu_page($parent, esc_attr(__('Reputation System', 'sp-reputation')), esc_attr(__('Reputation System', 'sp-reputationl')), 'read', SP_FOLDER_NAME.'/admin/panel-plugins/spa-plugins.php&tab=plugin&admin=sp_reputation_admin_options&save=sp_reputation_admin_save_options&form=1&panel='.urlencode(__('Reputation System', 'sp-reputationl')), 'dummy');
}

function sp_reputation_menu() {
	$option = SP()->options->get('reputation');
	$panels = array(
        __('Options', 'sp-reputation') => array('admin' => 'sp_reputation_admin_options', 'save' => 'sp_reputation_admin_save_options', 'form' => 1, 'id' => 'reputation'),
        __('Levels', 'sp-reputation') => array('admin' => 'sp_reputation_admin_levels', 'save' => '', 'form' => 0, 'id' => 'reputation-levels'),
        __('Reset', 'sp-reputation') => array('admin' => 'sp_reputation_admin_reset', 'save' => '', 'form' => 0, 'id' => 'reputation-reset'),
	);

    SP()->plugin->add_admin_panel(__('Reputation System', 'sp-reputation'), 'SPF Manage Reputation', __('Manage Reputation System', 'sp-reputation'), 'icon-Reputation', $panels, 7);
}

function sp_reputation_admin_options() {
    require_once SPREPADMINDIR.'sp-reputation-admin-options.php';
	sp_reputation_do_admin_options();
}

function sp_reputation_admin_save_options() {
    require_once SPREPADMINDIR.'sp-reputation-admin-options-save.php';
    return sp_reputation_do_admin_save_options();
}

function sp_reputation_admin_levels() {
    require_once SPREPADMINDIR.'sp-reputation-admin-levels.php';
	sp_reputation_do_admin_levels();
}

function sp_reputation_admin_save_levels() {
    require_once SPREPADMINDIR.'sp-reputation-admin-levels-save.php';
    return sp_reputation_do_admin_save_levels();
}

function sp_reputation_admin_reset() {
    require_once SPREPADMINDIR.'sp-reputation-admin-reset.php';
	sp_reputation_do_admin_reset();
}

function sp_reputation_admin_save_reset() {
    require_once SPREPADMINDIR.'sp-reputation-admin-reset-save.php';
    return sp_reputation_do_admin_save_reset();
}

function sp_reputation_localization() {
	sp_plugin_localisation('sp-reputation');
}

function sp_reputation_install() {
    require_once SPREPDIR.'sp-reputation-install.php';
    sp_reputation_do_install();
}

function sp_reputation_deactivate() {
    require_once SPREPDIR.'sp-reputation-uninstall.php';
    sp_reputation_do_deactivate();
}

function sp_reputation_uninstall() {
    require_once SPREPDIR.'sp-reputation-uninstall.php';
    sp_reputation_do_uninstall();
}

function sp_reputation_sp_activate() {
	require_once SPREPDIR.'sp-reputation-install.php';
    sp_reputation_do_sp_activate();
}

function sp_reputation_sp_deactivate() {
	require_once SPREPDIR.'sp-reputation-uninstall.php';
    sp_reputation_do_sp_deactivate();
}

function sp_reputation_sp_uninstall() {
	require_once SPREPDIR.'sp-reputation-uninstall.php';
    sp_reputation_do_sp_uninstall();
}

function sp_reputation_upgrade_check() {
    require_once SPREPDIR.'sp-reputation-upgrade.php';
    sp_reputation_do_upgrade_check();
}

function sp_reputation_uninstall_option($actionlink, $plugin) {
    require_once SPREPDIR.'sp-reputation-uninstall.php';
    $actionlink = sp_reputation_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_reputation_reset_permissions() {
    require_once SPREPDIR.'sp-reputation-install.php';
    sp_reputation_do_reset_permissions();
}

function sp_reputation_tooltips($tips, $t) {
    $tips['use_reputation'] = $t.__('Can give or take reputation to other users', 'sp-reputation');
    $tips['get_reputation'] = $t.__('Can gain or lose reputation from other users', 'sp-reputation');
    return $tips;
}

function sp_reputation_admin_help($file, $tag, $lang) {
    if ($tag == '[reputation-options]' || $tag == '[highlight-options]' || $tag == '[lowlight-options]' || $tag == '[reputation-strings]' ||
        $tag == '[reputation-levels]' || $tag == '[reputation-upload]' || $tag == '[reputation-badges]' ||
        $tag == '[reputation-reset]' || $tag == '[user-reputation]' || $tag == '[options-reset]') {
        $file = SPREPADMINDIR.'sp-reputation-admin-help.'.$lang;
    }
    return $file;
}

function sp_reputation_admin_cap_form($user) {
	require_once SPREPLIBDIR.'sp-reputation-components.php';
	sp_reputation_do_admin_cap_form($user);
}

function sp_reputation_admin_cap_list($user) {
	require_once SPREPLIBDIR.'sp-reputation-components.php';
	sp_reputation_do_admin_cap_list($user);
}

function sp_reputation_admin_caps_new($newadmin, $user) {
	require_once SPREPLIBDIR.'sp-reputation-components.php';
	$newadmin = sp_reputation_do_admin_caps_new($newadmin, $user);
	return $newadmin;
}

function sp_reputation_admin_caps_update($still_admin, $remove_admin, $user) {
	require_once SPREPLIBDIR.'sp-reputation-components.php';
	$still_admin = sp_reputation_do_admin_caps_update($still_admin, $remove_admin, $user);
	return $still_admin;
}

function sp_reputation_admin_links($out, $br) {
	if (SP()->auths->current_user_can('SPF Manage Reputation')) {
		$out.= sp_open_grid_cell();
		$out.= '<p><a href="'.admin_url('admin.php?page='.SP_FOLDER_NAME.'/admin/panel-plugins/spa-plugins.php&tab=plugin&admin=sp_reputation_admin_options&save=sp_reputation_admin_save_options&form=1').'">';
		$out.= SP()->theme->paint_icon('spIcon', SPREPIMAGES, "sp_ManageReputation.png").$br;
		$out.= __('Reputation System', 'sp-reputation').'</a></p>';
		$out.= sp_close_grid_cell();
	}
    return $out;
}

function sp_reputation_storage_location() {
	$storage = SP()->options->get('sfconfig');
	$path = SP_STORE_DIR.'/'.$storage['reputation'];
	spa_paint_storage_input(__('Reputation badges folder', 'sp-reputation'), 'reputation', $storage['reputation'], $path, false, true);
}

function sp_reputaton_storage_save() {
	$storage = SP()->options->get('sfconfig');
	if (!empty($_POST['reputation'])) $storage['reputation'] = trim(SP()->saveFilters->title(trim($_POST['reputation'])), '/');
	SP()->options->update('sfconfig', $storage);
}

function sp_reputation_load_admin_js($footer) {
    wp_enqueue_script('jquery-ui-autocomplete', false, array('jquery', 'jquery-ui-core', 'jquery-ui-widget'));
}

function sp_reputation_ajax_manage() {
    require_once SPREPAJAXDIR.'sp-reputation-ajax-manage.php';
}

function sp_reputation_member_add($userid) {
    require_once SPREPLIBDIR.'sp-reputation-components.php';
	sp_reputation_do_member_add($userid);
}

function sp_reputation_member_del($userid) {
    require_once SPREPLIBDIR.'sp-reputation-components.php';
	sp_reputation_do_member_del($userid);
}

function sp_reputation_register_check($user_login, $user) {
    require_once SPREPLIBDIR.'sp-reputation-components.php';
	sp_reputation_do_register_check($user_login, $user);
}

function sp_reputation_post_check($newpost) {
    require_once SPREPLIBDIR.'sp-reputation-components.php';
	sp_reputation_do_post_check($newpost);
}

function sp_reputation_add_post_class($rowClass, $sectionName, $a) {
    require_once SPREPLIBDIR.'sp-reputation-components.php';
	$rowClass = sp_reputation_do_add_post_class($rowClass, $sectionName, $a);
    return $rowClass;
}

function sp_reputation_add_css() {
    require_once SPREPLIBDIR.'sp-reputation-components.php';
	sp_reputation_do_add_css();
}

function sp_reputation_add_user_class(&$user) {
    require_once SPREPLIBDIR.'sp-reputation-components.php';
	sp_reputation_do_add_user_class($user);
}

function sp_reputation_members_query($query){
    $query->fields.= ', reputation';
    return $query;
}

function sp_reputation_members_records($data, $record) {
	$data->reputation = $record->reputation;
	return $data;
}

function sp_reputation_topic_delete($posts) {
    require_once SPREPLIBDIR.'sp-reputation-components.php';
	sp_reputation_do_topic_deleted($posts);
}

function sp_reputation_post_delete($post) {
    require_once SPREPLIBDIR.'sp-reputation-components.php';
	sp_reputation_do_posts_deleted($post);
}

function sp_reputation_storage_tooltip($tooltips) {
	$tooltips['reputation'] = 'The reputation badges folder is the location for storing badge images for each reputation level';
	return $tooltips;
}

############################## Template Tags ################################

function sp_PostIndexUserReputationLevel($args='') {
    require_once SPREPTAGS.'sp-reputation-post-index-level.php';
    return sp_reputation_post_index_level($args);
}

function sp_UserReputationLevel($args='', $reputation_level='') {
    require_once SPREPTAGS.'sp-reputation-user-level.php';
    return sp_reputation_user_level($args, $reputation_level);
}

function sp_MembersListReputationLevel($args='', $label='') {
    require_once SPREPTAGS.'sp-reputation-members-list-level.php';
    return sp_reputation_members_list_level($args, $label);
}

function sp_PostIndexRepUser($args='', $label='', $toolTip='') {
    require_once SPREPTAGS.'sp-reputation-rep-user.php';
	sp_reputation_rep_user($args, $label, $toolTip);
}

function sp_MostReputable($args='') {
    require_once SPREPLIBDIR.'sp-reputation-components.php';
    require_once SPREPTAGS.'sp-reputation-most-reputable.php';
	return sp_do_MostReputable($args);
}
