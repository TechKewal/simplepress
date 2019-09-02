<?php
/*
Simple:Press Plugin Title: Share This
Version: 2.1.0
Item Id: 3911
Plugin URI: https://simple-press.com/downloads/share-this-plugin/
Description: A Simple:Press plugin for social sharing of forum topics and posts with Facebook, Twitter, Google Plus and more using the Share This service
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPSHAREDBVERSION', 1);

define('SPSHAREDIR', 		SPPLUGINDIR.'share-this/');
define('SPSHAREADMINDIR',   SPPLUGINDIR.'share-this/admin/');
define('SPSHARELIBDIR', 	SPPLUGINDIR.'share-this/library/');
define('SPSHARELIBURL', 	SPPLUGINURL.'share-this/library/');
define('SPSHARECSS', 		SPPLUGINURL.'share-this/resources/css/');
define('SPSHAREIMAGES', 	SPPLUGINURL.'share-this/resources/images/');
define('SPSHARETAGS', 	    SPPLUGINDIR.'share-this/template-tags/');

add_action('init', 										               'sp_share_this_localization');
add_action('sph_activate_share-this/sp-share-this-plugin.php',         'sp_share_this_install');
add_action('sph_deactivate_share-this/sp-share-this-plugin.php',       'sp_share_this_deactivate');
add_action('sph_uninstall_share-this/sp-share-this-plugin.php',        'sp_share_this_uninstall');
add_action('sph_activated', 				                           'sp_share_this_sp_activate');
add_action('sph_deactivated', 				                           'sp_share_this_sp_deactivate');
add_action('sph_uninstalled', 								           'sp_share_this_sp_uninstall');
add_action('sph_plugin_update_share-this/sp-share-this-plugin.php',    'sp_share_this_upgrade_check');
add_action('admin_footer',                                             'sp_share_this_upgrade_check');
add_action('sph_permissions_reset', 						           'sp_share_this_reset_permissions');
add_action('sph_admin_menu', 	                                       'sp_share_this_menu');
add_action('sph_print_plugin_styles',						           'sp_share_this_header');

add_filter('sph_plugins_active_buttons',        'sp_share_this_uninstall_option', 10, 2);
add_filter('sph_admin_help-admin-components',   'sp_share_this_admin_help', 10, 3);

# set up global var to limit js loading to once
global $shareLoaded;
$shareLoaded = false;

function sp_share_this_menu() {
    $subpanels = array(__('Share This', 'sp-share-this') => array('admin' => 'sp_share_this_admin_options', 'save' => 'sp_share_this_admin_save_options', 'form' => 1, 'id' => 'shareopt'));
    SP()->plugin->add_admin_subpanel('components', $subpanels);
}

function sp_share_this_admin_options() {
    require_once SPSHAREADMINDIR.'sp-share-this-admin-options.php';
	sp_share_this_admin_options_form();
}

function sp_share_this_admin_save_options() {
    require_once SPSHAREADMINDIR.'sp-share-this-admin-options-save.php';
    return sp_share_this_admin_options_save();
}

function sp_share_this_admin_help($file, $tag, $lang) {
    if ($tag == '[share-this-options]' || $tag == '[share-this-buttons]' || $tag == '[share-this-style]' || $tag == '[share-this-theme]') $file = SPSHAREADMINDIR.'sp-share-this-admin-help.'.$lang;
    return $file;
}

function sp_share_this_localization() {
	sp_plugin_localisation('sp-share-this');
}

function sp_share_this_install() {
    require_once SPSHAREDIR.'sp-share-this-install.php';
    sp_share_this_do_install();
}

function sp_share_this_deactivate() {
    require_once SPSHAREDIR.'sp-share-this-uninstall.php';
    sp_share_this_do_deactivate();
}

function sp_share_this_uninstall() {
    require_once SPSHAREDIR.'sp-share-this-uninstall.php';
    sp_share_this_do_uninstall();
}

function sp_share_this_sp_activate() {
	require_once SPSHAREDIR.'sp-share-this-install.php';
    sp_share_this_do_sp_activate();
}

function sp_share_this_sp_deactivate() {
	require_once SPSHAREDIR.'sp-share-this-uninstall.php';
    sp_share_this_do_sp_deactivate();
}

function sp_share_this_sp_uninstall() {
	require_once SPSHAREDIR.'sp-share-this-uninstall.php';
    sp_share_this_do_sp_uninstall();
}

function sp_share_this_upgrade_check() {
    require_once SPSHAREDIR.'sp-share-this-upgrade.php';
    sp_share_this_do_upgrade_check();
}

function sp_share_this_uninstall_option($actionlink, $plugin) {
    require_once SPSHAREDIR.'sp-share-this-uninstall.php';
    $actionlink = sp_share_this_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_share_this_reset_permissions() {
    require_once SPSHAREDIR.'sp-share-this-install.php';
    sp_share_this_do_reset_permissions();
}

function sp_share_this_header() {
    require_once SPSHARELIBDIR.'sp-share-this-components.php';
    sp_share_this_do_header();
}

# Define Template Tags

function sp_ShareThisForumTag($args='') {
    require_once SPSHARELIBDIR.'sp-share-this-components.php';
    require_once SPSHARETAGS.'sp-share-this-forum-tag.php';
	return sp_do_ShareThisForumTag($args);
}

function sp_ShareThisTopicTag($args='') {
    require_once SPSHARELIBDIR.'sp-share-this-components.php';
    require_once SPSHARETAGS.'sp-share-this-topic-tag.php';
	return sp_do_ShareThisTopicTag($args);
}

function sp_ShareThisTopicIndexTag($args='') {
    require_once SPSHARELIBDIR.'sp-share-this-components.php';
    require_once SPSHARETAGS.'sp-share-this-topic-index-tag.php';
	return sp_do_ShareThisTopicIndexTag($args);
}

function sp_ShareThisTag($args='') {
    require_once SPSHARELIBDIR.'sp-share-this-components.php';
    require_once SPSHARETAGS.'sp-share-this-tag.php';
	return sp_do_ShareThisTag($args);
}
