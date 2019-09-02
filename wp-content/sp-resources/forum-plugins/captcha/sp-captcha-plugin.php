<?php
/*
Simple:Press Plugin Title: Captcha
Version: 2.1.0
Item Id: 3946 
Plugin URI: https://simple-press.com/downloads/captcha-plugin/
Description: A Simple:Press plugin for adding image drag and drop captcha for registrations and posting form
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPCAPTCHADBVERSION', 2);

define('SPCAPPDIR',			SPPLUGINDIR.'captcha/');
define('SPCAPADMINDIR',		SPPLUGINDIR.'captcha/admin/');
define('SPCAPLIBDIR',		SPPLUGINDIR.'captcha/library/');
define('SPCAPLIBURL',		SPPLUGINURL.'captcha/library/');
define('SPCAPCSS',			SPPLUGINURL.'captcha/resources/css/');
define('SPCAPSCRIPT',		SPPLUGINURL.'captcha/resources/jscript/');
define('SPCAPIMAGES',		SPPLUGINURL.'captcha/resources/images/');

add_action('init',														'sp_captcha_localization');
add_action('sph_activate_captcha/sp-captcha-plugin.php',				'sp_captcha_install');
add_action('sph_uninstall_captcha/sp-captcha-plugin.php',				'sp_captcha_uninstall');
add_action('sph_deactivate_captcha/sp-captcha-plugin.php',				'sp_captcha_deactivate');
add_action('sph_components_login_right_panel',							'sp_captcha_admin_options');
add_action('sph_component_login_save',									'sp_captcha_admin_save_options');
add_action('sph_print_plugin_scripts',									'sp_captcha_load_js');
add_action('sph_print_plugin_styles',									'sp_captcha_load_css');
add_action('sph_plugin_update_captcha/sp-captcha-plugin.php',			'sp_captcha_upgrade_check');
add_action('admin_footer',												'sp_captcha_upgrade_check');
add_action('sph_permissions_reset',										'sp_captcha_permissions_reset');
add_filter('sph_platform_vars',											'sp_captcha_platform_vars');

add_filter('sph_plugins_active_buttons',		'sp_captcha_uninstall_option', 10, 2);
add_filter('sph_acknowledgements',				'sp_captcha_acknowledgement');
add_filter('sph_admin_help-admin-components',	'sp_captcha_help', 10, 3);

if (isset(SP()->core->forumData['display']) && SP()->core->forumData['display']['editor']['toolbar']) {
	add_filter('sph_post_editor_above_toolbar',		'sp_captcha_post_form', 10, 3);
	add_filter('sph_topic_editor_above_toolbar',	'sp_captcha_topic_form', 10, 3);
} else {
	add_filter('sph_post_editor_submit_top',		'sp_captcha_post_form', 10, 3);
	add_filter('sph_topic_editor_submit_top',		'sp_captcha_topic_form', 10, 3);
}

add_filter('sph_validate_human',				'sp_captcha_check_captcha', 10, 2);
add_filter('sph_new_forum_post',				'sp_captcha_check_post');
add_filter('sph_perms_tooltips',				'sp_captcha_tooltips', 10, 2);
add_filter('sph_forum_vars',					'sp_captcha_forum_vars');

$captcha = SP()->options->get('spCaptcha');
if ($captcha['registration']) {
	# WP
	add_action('login_enqueue_scripts', 'sp_captcha_login_load_js');
	add_action('register_form',			'sp_captcha_registration_form');
	add_filter('registration_errors',	'sp_captcha_check_registration', 10, 3);

	# multisite stuff
	if (is_multisite()) {
		add_action('wp_enqueue_scripts',			'sp_captcha_login_load_js');
		add_action('signup_extra_fields',			'sp_captcha_signup_form');
		add_filter('wpmu_validate_user_signup',		'sp_captcha_check_signup');
	}
}

function sp_captcha_admin_options() {
	require_once SPCAPADMINDIR.'sp-captcha-admin-options.php';
	sp_captcha_admin_options_form();
}

function sp_captcha_admin_save_options() {
	require_once SPCAPADMINDIR.'sp-captcha-admin-options-save.php';
	return sp_captcha_admin_options_save();
}

function sp_captcha_localization() {
	sp_plugin_localisation('sp-cap');
}

function sp_captcha_tooltips($tips, $t) {
	$tips['bypass_captcha'] = $t.__('Can bypass the drag and drop human captcha check', 'sp-cap');
	return $tips;
}

function sp_captcha_help($file, $tag, $lang) {
	if ($tag == '[captcha]') $file = SPCAPADMINDIR.'sp-captcha-admin-help.'.$lang;
	return $file;
}

function sp_captcha_install() {
	require_once SPCAPPDIR.'sp-captcha-install.php';
	sp_captcha_do_install();
}

function sp_captcha_permissions_reset() {
	require_once SPCAPPDIR.'sp-captcha-install.php';
	sp_captcha_do_permissions_reset();
}

function sp_captcha_platform_vars($platform) {
	$target = (isset(SP()->rewrites->pageData['forumid'])) ? SP()->rewrites->pageData['forumid'] : 'global';
	if (!SP()->auths->get('bypass_captcha', $target)) $platform['postvalue'] = 'active';
    return $platform;
}

function sp_captcha_forum_vars($vars) {
	$vars['nocaptcha'] = __('Captcha not properly completed', 'sp-cap');
	return $vars;
}

function sp_captcha_uninstall() {
	require_once SPCAPPDIR.'sp-captcha-uninstall.php';
	sp_captcha_do_uninstall();
}

function sp_captcha_deactivate() {
	require_once SPCAPLIBDIR.'sp-captcha-components.php';
	sp_captcha_do_deactivate();
}

function sp_captcha_upgrade_check() {
	require_once SPCAPPDIR.'sp-captcha-upgrade.php';
	sp_captcha_do_upgrade_check();
}

function sp_captcha_uninstall_option($actionlink, $plugin) {
	require_once SPCAPLIBDIR.'sp-captcha-components.php';
	$actionlink = sp_captcha_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_captcha_login_load_js($footer) {
	require_once SPCAPLIBDIR.'sp-captcha-components.php';
	sp_captcha_do_login_load_js($footer);
}

function sp_captcha_load_js($footer) {
	require_once SPCAPLIBDIR.'sp-captcha-components.php';
	sp_captcha_do_load_js($footer);
}

function sp_captcha_load_css() {
	$css = SP()->theme->find_css(SPCAPCSS, 'sp-captcha.css', 'sp-captcha.spcss');
	SP()->plugin->enqueue_style('sp-captcha', $css);
}

function sp_captcha_registration_form($errors='') {
	require_once SPCAPLIBDIR.'sp-captcha-components.php';
	sp_captcha_do_registration_form('registerform', $errors);
}

function sp_captcha_signup_form($errors='') {
	require_once SPCAPLIBDIR.'sp-captcha-components.php';
	sp_captcha_do_registration_form('setupform', $errors);
}

function sp_captcha_check_registration($errors, $sanitized_user_login, $user_email) {
	require_once SPCAPLIBDIR.'sp-captcha-components.php';
	$errors = sp_captcha_do_check_registration($errors, $sanitized_user_login, $user_email);
	return $errors;
}

function sp_captcha_check_signup($result) {
	require_once SPCAPLIBDIR.'sp-captcha-components.php';
	$result['errors'] = sp_captcha_do_check_registration($result['errors'], '', '');
	return $result;
}

function sp_captcha_topic_form($out, $thisForum, $args) {
	require_once SPCAPLIBDIR.'sp-captcha-components.php';
	$out = sp_captcha_do_topic_form($out, $thisForum, $args);
	return $out;
}

function sp_captcha_topic_button_text($text, $args) {
	require_once SPCAPLIBDIR.'sp-captcha-components.php';
	$text = sp_captcha_do_topic_button_text($text, $args);
	return $text;
}

function sp_captcha_topic_button_enable($enable, $args) {
	require_once SPCAPLIBDIR.'sp-captcha-components.php';
	$enable = sp_captcha_do_topic_button_enable($enable, $args);
	return $enable;
}

function sp_captcha_post_form($out, $thisTopic, $args) {
	require_once SPCAPLIBDIR.'sp-captcha-components.php';
	$out = sp_captcha_do_post_form($out, $thisTopic, $args);
	return $out;
}

function sp_captcha_post_button_text($text, $args) {
	require_once SPCAPLIBDIR.'sp-captcha-components.php';
	$text = sp_captcha_do_post_button_text($text, $args);
	return $text;
}

function sp_captcha_post_button_enable($enable, $args) {
	require_once SPCAPLIBDIR.'sp-captcha-components.php';
	$enable = sp_captcha_do_post_button_enable($enable, $args);
	return $enable;
}

function sp_captcha_check_captcha($abort, $postVars) {
	require_once SPCAPLIBDIR.'sp-captcha-components.php';
	$abort = sp_captcha_do_check_captcha($abort, $postVars);
	return $abort;
}

function sp_captcha_check_post($newpost) {
	require_once SPCAPLIBDIR.'sp-captcha-components.php';
	$newpost = sp_captcha_do_check_post($newpost);
	return $newpost;
}

function sp_captcha_acknowledgement($ack) {
	$ack[] = '<a href="http://www.webdesignbeach.com/beachbar/ajax-fancy-captcha-jquery-plugin">'.__('Captcha by Web Design Beach', 'sp-cap').'</a>';
	return $ack;
}
