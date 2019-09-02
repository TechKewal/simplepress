<?php
/*
Simple:Press Plugin Title: Buddypress
Version: 2.1.0
Item Id: 3910
Plugin URI: https://simple-press.com/downloads/buddypress-integration-plugin/
Description: A Simple:Press plugin for adding support for the WordPress BuddyPress plugin
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$Rev: 15725 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPBUDDYPRESSDBVERSION', 2);

define('SPBUDDYPRESSDIR', 		SPPLUGINDIR.'buddypress/');
define('SPBUDDYPRESSADMINDIR',  SPPLUGINDIR.'buddypress/admin/');
define('SPBUDDYPRESSLIBDIR', 	SPPLUGINDIR.'buddypress/library/');
define('SPBUDDYPRESSLIBURL', 	SPPLUGINURL.'buddypress/library/');
define('SPBUDDYPRESSCSS',       SPPLUGINURL.'buddypress/resources/css/');

# general actions
add_action('init', 										             'sp_buddypress_localization');
add_action('sph_activate_buddypress/sp-buddypress-plugin.php',       'sp_buddypress_install');
add_action('sph_deactivate_buddypress/sp-buddypress-plugin.php',     'sp_buddypress_deactivate');
add_action('sph_uninstall_buddypress/sp-buddypress-plugin.php',      'sp_buddypress_uninstall');
add_action('sph_activated', 				                         'sp_buddypress_sp_activate');
add_action('sph_deactivated', 				                         'sp_buddypress_sp_deactivate');
add_action('sph_uninstalled', 								         'sp_buddypress_sp_uninstall');
add_action('sph_plugin_update_buddypress/sp-buddypress-plugin.php',  'sp_buddypress_upgrade_check');
add_action('admin_footer',                                           'sp_buddypress_upgrade_check');
add_action('sph_permissions_reset', 						         'sp_buddypress_reset_permissions');
add_action('sph_admin_menu', 	                                     'sp_buddypress_menu');
add_action('sph_admin_panel_header', 							     'sp_buddypress_show_alert');

# general filters
add_filter('sph_plugins_active_buttons',        'sp_buddypress_uninstall_option', 10, 2);
add_filter('sph_admin_help-admin-components',   'sp_buddypress_admin_help', 10, 3);

# make sure wp buddypress plugin is active
if (class_exists('BuddyPress')){
    #check if forum displayed
	include_once SPBOOT.'site/sp-site-support-functions.php';
	include_once SP_PLUGIN_DIR.'/forum/database/sp-db-newposts.php';

	$message = sp_abort_display_forum();
    if (empty($message)) {
    	$bpdata = SP()->options->get('buddypress');

        # configure the main BP componeont
        add_action('bp_setup_components',   'sp_buddypress_setup_component');
        add_action('wp_enqueue_scripts',	'sp_buddypress_head');

        # activity stream stuff
        add_action('sph_post_create',                   'sp_buddypress_new_post');
        add_action('bp_activity_filter_options',        'sp_buddypress_activity_filter');
        add_action('bp_member_activity_filter_options', 'sp_buddypress_activity_filter');
        add_filter('bp_activity_get',                   'sp_buddypress_activity_permission_check');

        # avatar stuff
        if ($bpdata['avatar'] == 2) add_filter('sph_Avatar',            'sp_buddypress_bp_avatar', 10, 2);
        if ($bpdata['avatar'] == 3) add_filter('bp_core_fetch_avatar',  'sp_buddypress_sp_avatar', 10, 2);

        # some notifications
        add_action('admin_bar_menu', 'sp_buddypress_notifications');

        # add spam form to buddypress registration if using
        add_action('bp_before_registration_submit_buttons',     'sp_buddypress_registration', 1);
        add_filter('bp_signup_validate',                        'sp_buddypress_registration_check');
        $captcha = SP()->options->get('spCaptcha');
        if (SP()->plugin->is_active('captcha/sp-captcha-plugin.php') && $captcha['registration']) {
            add_action('wp_enqueue_scripts',                        'sp_captcha_login_load_js');
            add_action('bp_before_registration_submit_buttons',     'sp_buddypress_captcha_registration');
            add_filter('bp_signup_validate',                        'sp_buddypress_registration_captcha_check', 10, 3);
        }
    }
}

function sp_buddypress_localization() {
	sp_plugin_localisation('sp-buddypress');
}

function sp_buddypress_install() {
    require_once SPBUDDYPRESSDIR.'sp-buddypress-install.php';
    sp_buddypress_do_install();
}

function sp_buddypress_deactivate() {
    require_once SPBUDDYPRESSDIR.'sp-buddypress-uninstall.php';
    sp_buddypress_do_deactivate();
}

function sp_buddypress_uninstall() {
    require_once SPBUDDYPRESSDIR.'sp-buddypress-uninstall.php';
    sp_buddypress_do_uninstall();
}

function sp_buddypress_sp_activate() {
	require_once SPBUDDYPRESSDIR.'sp-buddypress-install.php';
    sp_buddypress_do_sp_activate();
}

function sp_buddypress_sp_deactivate() {
	require_once SPBUDDYPRESSDIR.'sp-buddypress-uninstall.php';
    sp_buddypress_do_sp_deactivate();
}

function sp_buddypress_sp_uninstall() {
	require_once SPBUDDYPRESSDIR.'sp-buddypress-uninstall.php';
    sp_buddypress_do_sp_uninstall();
}

function sp_buddypress_upgrade_check() {
    require_once SPBUDDYPRESSDIR.'sp-buddypress-upgrade.php';
    sp_buddypress_do_upgrade_check();
}

function sp_buddypress_uninstall_option($actionlink, $plugin) {
    require_once SPBUDDYPRESSDIR.'sp-buddypress-uninstall.php';
    $actionlink = sp_buddypress_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_buddypress_reset_permissions() {
    require_once SPBUDDYPRESSDIR.'sp-buddypress-install.php';
    sp_buddypress_do_reset_permissions();
}

function sp_buddypress_menu() {
    $subpanels = array(__('BuddyPress', 'sp-buddypress') => array('admin' => 'sp_buddypress_admin_options', 'save' => 'sp_buddypress_admin_save_options', 'form' => 1, 'id' => 'buddypressopt'));
    SP()->plugin->add_admin_subpanel('components', $subpanels);
}

function sp_buddypress_admin_options() {
    require_once SPBUDDYPRESSADMINDIR.'sp-buddypress-admin-options.php';
	sp_buddypress_admin_options_form();
}

function sp_buddypress_admin_save_options() {
    require_once SPBUDDYPRESSADMINDIR.'sp-buddypress-admin-options-save.php';
    return sp_buddypress_admin_options_save();
}

function sp_buddypress_admin_help($file, $tag, $lang) {
    if ($tag == '[buddypress-activity]' || $tag == '[buddypress-avatars]'  || $tag == '[buddypress-links]' || $tag == '[buddypress-notifications]') $file = SPBUDDYPRESSADMINDIR.'sp-buddypress-admin-help.'.$lang;
    return $file;
}

function sp_buddypress_show_alert(){
	if (!class_exists('BuddyPress')) echo '<div class="sfoptionerror">'.__('The WordPress BuddyPress plugin cannot be found!', 'sp-buddypress').' <a href="http://wordpress.org/extend/plugins/buddypress/" target="_blank">'.__('Please install from the WordPress plugin site', 'sp-buddypress').'</a> '.__('to enable Simple:Press BuddyPress integration.', 'sp-buddypress').'</div>';
}

# main BP compononent
function sp_buddypress_setup_component() {
    require_once SPBUDDYPRESSLIBDIR.'sp-buddypress-component.php';
	sp_buddypress_do_setup_component();
}

function sp_buddypress_head() {
	$css = SP()->theme->find_css(SPBUDDYPRESSCSS, 'sp-buddypress.css', 'sp-buddypress.spcss');
    wp_enqueue_style('sp-buddypress', $css);
}

# activity stream stuff
function sp_buddypress_new_post($newpost) {
    require_once SPBUDDYPRESSLIBDIR.'sp-buddypress-activity.php';
	sp_buddypress_do_new_post($newpost);
}

function sp_buddypress_activity_filter() {
    require_once SPBUDDYPRESSLIBDIR.'sp-buddypress-activity.php';
	sp_buddypress_do_activity_filter();
}

function sp_buddypress_activity_permission_check($data) {
    require_once SPBUDDYPRESSLIBDIR.'sp-buddypress-activity.php';
    $data = sp_buddypress_do_activity_permission_check($data);
    return $data;
}

# avatar stuff
function sp_buddypress_sp_avatar($avatar, $params) {
    require_once SPBUDDYPRESSLIBDIR.'sp-buddypress-avatar.php';
	$avatar = sp_buddypress_do_sp_avatar($avatar, $params);
    return $avatar;
}

function sp_buddypress_bp_avatar($avatar, $params) {
    require_once SPBUDDYPRESSLIBDIR.'sp-buddypress-avatar.php';
	$avatar = sp_buddypress_do_bp_avatar($avatar, $params);
    return $avatar;
}

# notification stuff
function sp_buddypress_notifications() {
    if (bp_is_active('notifications')) {
        require_once SPBUDDYPRESSLIBDIR.'sp-buddypress-notifications.php';
	   sph_buddypress_do_notifications();
    }
}

# registration stuff
function sp_buddypress_registration() {
    echo '<div class="sp_math_check" style="clear:both;">';
    spa_register_math();
    do_action( 'bp_math_check_errors' );
    echo '</div>';
}

function sp_buddypress_registration_check() {
    $errors = spa_register_error(new WP_Error());
    if ($errors->get_error_code('Bad Math')) {
        global $bp;
        $bp->signup->errors['math_check'] = $errors->errors['Bad Math'][0];
    }
}

function sp_buddypress_captcha_registration() {
    echo '<div class="sp_captcha_check" style="clear:both;">';
    do_action( 'bp_captcha_check_errors' );
    require_once SPCAPLIBDIR.'sp-captcha-components.php';
    sp_captcha_do_registration_form('signup_form', '');
    echo '</div>';
}

function sp_buddypress_registration_captcha_check() {
    $errors = sp_captcha_check_registration(new WP_Error(), '', '');
    if ($errors->get_error_code('Bad Math')) {
        global $bp;
        $bp->signup->errors['captcha_check'] = $errors->errors['incorrect_captcha'][0];
    }
}
