<?php
/*
Simple:Press Plugin Title: Post Anonymously
Version: 2.1.0
Item Id: 4001
Plugin URI: https://simple-press.com/post-anonymously-plugin
Description: A Simple:Press plugin for allowing users to post anonymously
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPANONYMOUSDBVERSION', 0);

define('SPACTIVITY_ANON', SP()->activity->get_type('anonymous poster'));

define('SPANONYMOUSDIR', 		SPPLUGINDIR.'anonymous/');
define('SPANONYMOUSADMINDIR',   SPPLUGINDIR.'anonymous/admin/');
define('SPANONYMOUSAJAXDIR', 	SPPLUGINDIR.'anonymous/ajax/');
define('SPANONYMOUSLIBDIR', 	SPPLUGINDIR.'anonymous/library/');
define('SPANONYMOUSLIBURL', 	SPPLUGINURL.'anonymous/library/');
define('SPANONYMOUSCSS', 		SPPLUGINURL.'anonymous/resources/css/');
define('SPANONYMOUSSCRIPT', 	SPPLUGINURL.'anonymous/resources/jscript/');
define('SPANONYMOUSIMAGES', 	SPPLUGINURL.'anonymous/resources/images/');
define('SPANONYMOUSTAGS', 	    SPPLUGINDIR.'anonymous/template-tags/');
define('SPANONYMOUSTEMP', 	    SPPLUGINDIR.'anonymous/template-files/');

add_action('init', 										             'sp_anonymous_localization');
add_action('sph_activate_anonymous/sp-anonymous-plugin.php',         'sp_anonymous_install');
add_action('sph_deactivate_anonymous/sp-anonymous-plugin.php',       'sp_anonymous_deactivate');
add_action('sph_uninstall_anonymous/sp-anonymous-plugin.php',        'sp_anonymous_uninstall');
add_action('sph_activated', 				                         'sp_anonymous_sp_activate');
add_action('sph_deactivated', 				                         'sp_anonymous_sp_deactivate');
add_action('sph_uninstalled', 								         'sp_anonymous_sp_uninstall');
add_action('sph_plugin_update_anonymous/sp-anonymous-plugin.php',    'sp_anonymous_upgrade_check');
add_action('admin_footer',                                           'sp_anonymous_upgrade_check');
add_action('sph_permissions_reset', 						         'sp_anonymous_reset_permissions');
add_action('sph_new_post_pre_save',							         'sp_anonymous_new_forum_post', 999);
add_action('sph_new_post',											 'sp_anonymous_set_useractivity');
add_action('sph_UpdateProfilePostingOptions',				    	 'sp_anonymous_profile_save', 10, 2);

add_filter('sph_plugins_active_buttons',            'sp_anonymous_uninstall_option', 10, 2);
add_filter('sph_perms_tooltips', 			        'sp_anonymous_tooltips', 10, 2);
add_filter('sph_post_editor_display_options',       'sp_anonymous_editor_options');
add_filter('sph_topic_editor_display_options',      'sp_anonymous_editor_options');
add_filter('sph_topic_options_add',	                'sp_anonymous_post_form_options', 10, 2);
add_filter('sph_post_options_add',                  'sp_anonymous_post_form_options', 10, 2);
add_filter('sph_ProfilePostingOptionsFormBottom',	'sp_anonymous_profile_posting_options', 10, 2);

function sp_anonymous_localization() {
	sp_plugin_localisation('sp-anonymous');
}

function sp_anonymous_install() {
    require_once SPANONYMOUSDIR.'sp-anonymous-install.php';
    sp_anonymous_do_install();
}

function sp_anonymous_deactivate() {
    require_once SPANONYMOUSDIR.'sp-anonymous-uninstall.php';
    sp_anonymous_do_deactivate();
}

function sp_anonymous_uninstall() {
    require_once SPANONYMOUSDIR.'sp-anonymous-uninstall.php';
    sp_anonymous_do_uninstall();
}

function sp_anonymous_sp_activate() {
	require_once SPANONYMOUSDIR.'sp-anonymous-install.php';
    sp_anonymous_do_sp_activate();
}

function sp_anonymous_sp_deactivate() {
	require_once SPANONYMOUSDIR.'sp-anonymous-uninstall.php';
    sp_anonymous_do_sp_deactivate();
}

function sp_anonymous_sp_uninstall() {
	require_once SPANONYMOUSDIR.'sp-anonymous-uninstall.php';
    sp_anonymous_do_sp_uninstall();
}

function sp_anonymous_upgrade_check() {
    require_once SPANONYMOUSDIR.'sp-anonymous-upgrade.php';
    sp_anonymous_do_upgrade_check();
}

function sp_anonymous_uninstall_option($actionlink, $plugin) {
    require_once SPANONYMOUSDIR.'sp-anonymous-uninstall.php';
    $actionlink = sp_anonymous_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_anonymous_reset_permissions() {
    require_once SPANONYMOUSDIR.'sp-anonymous-install.php';
    sp_anonymous_do_reset_permissions();
}

function sp_anonymous_tooltips($tips, $t) {
    $tips['post_anonymous'] = $t.__('Can create a new topic or post reply anonymously', 'sp-anonymous');
    return $tips;
}

function sp_anonymous_editor_options($display) {
	if (SP()->rewrites->pageData['displaymode'] != 'edit' && SP()->auths->get('post_anonymous')) $display['options'] = true;
	return $display;
}

function sp_anonymous_post_form_options($content, $object) {
	require_once SPANONYMOUSLIBDIR.'sp-anonymous-components.php';
	$content = sp_anonymous_do_post_form_options($content, $object);
	return $content;
}

function sp_anonymous_new_forum_post($post) {
	require_once SPANONYMOUSLIBDIR.'sp-anonymous-components.php';
	sp_anonymous_do_new_forum_post($post);
}

function sp_anonymous_set_useractivity($post) {
	require_once SPANONYMOUSLIBDIR.'sp-anonymous-components.php';
	sp_anonymous_do_set_useractivity($post);
}

function sp_anonymous_profile_posting_options($content, $userid) {
    require_once SPANONYMOUSLIBDIR.'sp-anonymous-components.php';
	$content = sp_anonymous_do_profile_posting_options($content, $userid);
	return $content;
}

function sp_anonymous_profile_save($message, $userid) {
   	$options = SP()->memberData->get($userid, 'user_options');
    $update = apply_filters('sph_ProfileUserAnonymousUpdate', true);
    if ($update) $options['postanonymous'] = isset($_POST['postanonymous']);
    SP()->memberData->update($userid, 'user_options', $options);
    return $message;
}
