<?php
/*
Simple:Press Plugin Title: Add User Identities
Version: 2.1.0
Item Id: 3952
Plugin URI: https://simple-press.com/downloads/add-user-identities-plugin/
Description: A Simple:Press plugin for adding and displaying new identities for users
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
These identities are for user accounts similar to facebook, twitter, google plus, etc.  You will need a base url and the user will have to enter an account for
the base url site.  If you just want a different profile field, then you should be using the Custom Profile Fields plugin.  This identity plugin is specifically for
online identities.  After creating identiies in the plugin, use the template functoin defined at the end of this file to display them.  The template function call
would be added where you want to display, typically in your spTopicView.php template file of you SP theme
$LastChangedDate: 2018-08-21 08:48:39 -0500 (Tue, 21 Aug 2018) $
$Rev: 15716 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPIDENTDBVERSION', 1);

define('SPIDENTDIR', 		SPPLUGINDIR.'identities/');
define('SPIDENTADMINDIR',   SPPLUGINDIR.'identities/admin/');
define('SPIDENTAJAXDIR', 	SPPLUGINDIR.'identities/ajax/');
define('SPIDENTLIBDIR', 	SPPLUGINDIR.'identities/library/');
define('SPIDENTLIBURL', 	SPPLUGINURL.'identities/library/');
define('SPIDENTCSS', 		SPPLUGINURL.'identities/resources/css/');
define('SPIDENTSCRIPT', 	SPPLUGINURL.'identities/resources/jscript/');
define('SPIDENTIMAGES', 	SPPLUGINURL.'identities/resources/images/');
define('SPIDENTTAGDIR', 	SPPLUGINDIR.'identities/template-tags/');
define('SPIDENTTEMPDIR', 	SPPLUGINDIR.'identities/template-files/');

add_action('init', 										            'sp_identities_localization');
add_action('sph_activate_identities/sp-identities-plugin.php', 	    'sp_identities_install');
add_action('sph_uninstall_identities/sp-identities-plugin.php',     'sp_identities_uninstall');
add_action('sph_deactivate_identities/sp-identities-plugin.php',    'sp_identities_deactivate');
add_action('sph_activated', 				                        'sp_identities_sp_activate');
add_action('sph_deactivated', 				                        'sp_identities_sp_deactivate');
add_action('sph_uninstalled', 								        'sp_identities_sp_uninstall');
add_action('sph_plugin_update_identities/sp-identities-plugin.php', 'sp_identities_upgrade_check');
add_action('admin_footer',                                          'sp_identities_upgrade_check');
add_action('sph_admin_menu', 										'sp_identities_menu');
add_action('sph_integration_storage_panel_location', 			    'sp_identities_storage_location');
add_action('sph_integration_storage_save', 						    'sp_identities_storage_save');
add_action('sph_UpdateProfileIdentities', 							'sp_identities_profile_save', 10, 2);

add_filter('sph_plugins_active_buttons',        'sp_identities_uninstall_option', 10, 2);
add_filter('sph_admin_help-admin-profiles',	    'sp_identities_help', 10, 3);
add_filter('sph_ProfileIdentitiesFormBottom',   'sp_identities_profile_edit', 10, 2);
add_filter('sph_user_class_meta', 			    'sp_identities_load_meta');
add_filter('sph_integration_tooltips', 		    'sp_identities_tooltip');

# Personal Data Export
add_filter('sp_privacy_profile_data', 					'sp_privacy_identities_profile', 10, 4);

# Ajax Calls
add_action('wp_ajax_identities-admin',	 							'sp_identities_ajax_admin');
add_action('wp_ajax_nopriv_identities-admin', 						'sp_identities_ajax_admin');

function sp_identities_menu() {
    $subpanels = array(
                __('User Identities', 'sp-identities') => array('admin' => 'sp_identities_admin', 'save' => 'sp_identities_update', 'form' => 0, 'id' => 'spident')
                            );
    SP()->plugin->add_admin_subpanel('profiles', $subpanels);
}

function sp_identities_admin() {
    require_once SPIDENTADMINDIR.'sp-identities-admin.php';
	sp_identities_admin_form();
}

function sp_identities_update() {
    require_once SPIDENTADMINDIR.'sp-identities-admin-save.php';
	return sp_identities_admin_save();
}

function sp_identities_help($file, $tag, $lang) {
    if ($tag == '[identities]' || $tag == '[identity-upload]') $file = SPIDENTADMINDIR.'sp-identities-admin-help.'.$lang;
    return $file;
}

function sp_identities_localization() {
	sp_plugin_localisation('sp-identities');
}

function sp_identities_install() {
    require_once SPIDENTDIR.'sp-identities-install.php';
    sp_identities_do_install();
}

function sp_identities_uninstall() {
    require_once SPIDENTDIR.'sp-identities-uninstall.php';
    sp_identities_do_uninstall();
}

function sp_identities_deactivate() {
    require_once SPIDENTDIR.'sp-identities-uninstall.php';
    sp_identities_do_deactivate();
}

function sp_identities_sp_activate() {
	require_once SPIDENTDIR.'sp-identities-install.php';
    sp_identities_do_sp_activate();
}

function sp_identities_sp_deactivate() {
	require_once SPIDENTDIR.'sp-identities-uninstall.php';
    sp_identities_do_sp_deactivate();
}

function sp_identities_sp_uninstall() {
	require_once SPIDENTDIR.'sp-identities-uninstall.php';
    sp_identities_do_sp_uninstall();
}

function sp_identities_upgrade_check() {
    require_once SPIDENTDIR.'sp-identities-upgrade.php';
    sp_identities_do_upgrade_check();
}

function sp_identities_uninstall_option($actionlink, $plugin) {
    require_once SPIDENTDIR.'sp-identities-uninstall.php';
    $actionlink = sp_identities_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_identities_storage_location() {
    require_once SPIDENTLIBDIR.'sp-identities-components.php';
    sp_identities_do_storage_location();
}

function sp_identities_storage_save() {
    require_once SPIDENTLIBDIR.'sp-identities-components.php';
    sp_identities_do_storage_save();
}

function sp_identities_ajax_admin() {
    require_once SPIDENTAJAXDIR.'sp-identities-ajax-admin.php';
}

function sp_identities_profile_edit($out, $id) {
    require_once SPIDENTLIBDIR.'sp-identities-components.php';
    $out = sp_identities_do_profile_edit($out, $id);
    return $out;
}

function sp_identities_profile_save($message, $thisUser) {
    require_once SPIDENTLIBDIR.'sp-identities-components.php';
    $message = sp_identities_do_profile_save($message, $thisUser);
    return $message;
}

function sp_identities_load_meta($list) {
    require_once SPIDENTLIBDIR.'sp-identities-components.php';
	$list = sp_identities_do_load_meta($list);
    return $list;
}

# template function for display
function sp_PostIndexIdentityDisplay($args='', $toolTip='', $identity) {
	require_once SPIDENTLIBDIR.'sp-identities-components.php';
    require_once SPIDENTTAGDIR.'sp-identities-display-tag.php';
    sp_do_PostIndexIdentityDisplay($args, $toolTip, $identity);
}

function sp_IdentitiesProfileDisplay($args, $tooltip, $identity, $label) {
    include_once(SPIDENTTAGDIR.'sp-identities-profile-display-tag.php');
    sp_do_IdentitiesProfileDisplay($args, $tooltip, $identity, $label);
}

# personal data export
function sp_privacy_identities_profile($exportItems, $spUserData, $groupID, $groupLabel) {
	require_once SPIDENTLIBDIR.'sp-identities-components.php';
	return sp_privacy_do_identities_profile($exportItems, $spUserData, $groupID, $groupLabel);
}

function sp_identities_tooltip($tooltips) {
    $tooltips['identities'] = "The Identities Icon Folder is the location for storing uploaded custom identity icons for use with the Identities Plugin for Simple Press.";
	return $tooltips;
}
