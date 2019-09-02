<?php
/*
Simple:Press Plugin Title: Auto Linking
Version: 2.1.0
Item Id: 3951
Plugin URI: https://simple-press.com/downloads/auto-linking-plugin/
Description: A Simple:Press plugin for parsing post content and converting keywords into links
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPAUTODBVERSION', 1);

define('SPAUTODIR', 		SPPLUGINDIR.'autolink/');
define('SPAUTOADMINDIR',    SPPLUGINDIR.'autolink/admin/');
define('SPAUTOAJAXDIR', 	SPPLUGINDIR.'autolink/ajax/');
define('SPAUTOLIBDIR', 	    SPPLUGINDIR.'autolink/library/');
define('SPAUTOLIBURL', 	    SPPLUGINURL.'autolink/library/');
define('SPAUTOCSS', 		SPPLUGINURL.'autolink/resources/css/');
define('SPAUTOSCRIPT', 	    SPPLUGINURL.'autolink/resources/jscript/');
define('SPAUTOIMAGES', 	    SPPLUGINURL.'autolink/resources/images/');
define('SPAUTOTAGS', 	    SPPLUGINDIR.'autolink/template-tags/');
define('SPAUTOTEMP', 	    SPPLUGINDIR.'autolink/template-files/');

add_action('init', 										           'sp_autolink_localization');
add_action('sph_activate_autolink/sp-autolink-plugin.php',         'sp_autolink_install');
add_action('sph_deactivate_autolink/sp-autolink-plugin.php',       'sp_autolink_deactivate');
add_action('sph_uninstall_autolink/sp-autolink-plugin.php',        'sp_autolink_uninstall');
add_action('sph_activated', 				                       'sp_autolink_sp_activate');
add_action('sph_deactivated', 				                       'sp_autolink_sp_deactivate');
add_action('sph_uninstalled', 								       'sp_autolink_sp_uninstall');
add_action('sph_plugin_update_autolink/sp-autolink-plugin.php',    'sp_autolink_upgrade_check');
add_action('admin_footer',                                         'sp_autolink_upgrade_check');
add_action('sph_permissions_reset', 						       'sp_autolink_reset_permissions');
add_action('sph_admin_menu', 									   'sp_autolink_menu');

add_filter('sph_plugins_active_buttons',        'sp_autolink_uninstall_option', 10, 2);
add_filter('sph_admin_help-admin-components', 	'sp_autolink_admin_help', 10, 3);
add_filter('sph_display_post_content_filter', 	'sp_autolink_filter', 99);

function sp_autolink_menu() {
        $subpanels = array(
                __('Auto Linking', 'sp-autolink') => array('admin' => 'sp_autolink_admin_form', 'save' => 'sp_autolink_admin_save', 'form' => 1, 'id' => 'autolink')
                );
        SP()->plugin->add_admin_subpanel('components', $subpanels);
}

function sp_autolink_admin_form() {
    require_once SPAUTOADMINDIR.'sp-autolink-admin-form.php';
	sp_autolink_do_admin_form();
}

function sp_autolink_admin_save() {
    require_once SPAUTOADMINDIR.'sp-autolink-admin-save.php';
    return sp_autolink_do_admin_save();
}

function sp_autolink_localization() {
	sp_plugin_localisation('sp-autolink');
}

function sp_autolink_admin_help($file, $tag, $lang) {
    if ($tag == '[autolink-options]' || $tag == '[autolink-keywords]' || $tag == '[autolink-replacements]') $file = SPAUTOADMINDIR.'sp-autolink-admin-help.'.$lang;
    return $file;
}

function sp_autolink_install() {
    require_once SPAUTODIR.'sp-autolink-install.php';
    sp_autolink_do_install();
}

function sp_autolink_deactivate() {
    require_once SPAUTODIR.'sp-autolink-uninstall.php';
    sp_autolink_do_deactivate();
}

function sp_autolink_uninstall() {
    require_once SPAUTODIR.'sp-autolink-uninstall.php';
    sp_autolink_do_uninstall();
}

function sp_autolink_sp_activate() {
	require_once SPAUTODIR.'sp-autolink-install.php';
    sp_autolink_do_sp_activate();
}

function sp_autolink_sp_deactivate() {
	require_once SPAUTODIR.'sp-autolink-uninstall.php';
    sp_autolink_do_sp_deactivate();
}

function sp_autolink_sp_uninstall() {
	require_once SPAUTODIR.'sp-autolink-uninstall.php';
    sp_autolink_do_sp_uninstall();
}

function sp_autolink_upgrade_check() {
    require_once SPAUTODIR.'sp-autolink-upgrade.php';
    sp_autolink_do_upgrade_check();
}

function sp_autolink_uninstall_option($actionlink, $plugin) {
    require_once SPAUTODIR.'sp-autolink-uninstall.php';
    $actionlink = sp_autolink_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_autolink_reset_permissions() {
    require_once SPAUTODIR.'sp-autolink-install.php';
    sp_autolink_do_reset_permissions();
}

function sp_autolink_filter($content) {
    require_once SPAUTOLIBDIR.'sp-autolink-components.php';
    $content = sp_autolink_do_filter($content);
	return $content;
}
