<?php
/*
Simple:Press Plugin Title: Announce
Version: 2.1.0
Itemd Id: 3972
Plugin URI: https://simple-press.com/downloads/announcements-plugin/
Description: A Simple:Press plugin for displaying announcements or news in your forum
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPANNOUNCEDBVERSION', 3);

define('SPANNOUNCEDIR', 		SPPLUGINDIR.'announce/');
define('SPANNOUNCEADMINDIR',    SPPLUGINDIR.'announce/admin/');
define('SPANNOUNCECSS', 		SPPLUGINURL.'announce/resources/css/');
define('SPANNOUNCETAGS', 	    SPPLUGINDIR.'announce/template-tags/');

add_action('init', 										            'sp_announce_localization');
add_action('sph_activate_announce/sp-announce-plugin.php',          'sp_announce_install');
add_action('sph_deactivate_announce/sp-announce-plugin.php',        'sp_announce_deactivate');
add_action('sph_uninstall_announce/sp-announce-plugin.php',         'sp_announce_uninstall');
add_action('sph_activated', 				                        'sp_announce_sp_activate');
add_action('sph_deactivated', 				                        'sp_announce_sp_deactivate');
add_action('sph_uninstalled', 								        'sp_announce_sp_uninstall');
add_action('sph_plugin_update_announce/sp-announce-plugin.php',     'sp_announce_upgrade_check');
add_action('admin_footer',                                          'sp_announce_upgrade_check');
add_action('sph_permissions_reset', 						        'sp_announce_reset_permissions');
add_action('sph_admin_menu', 	                                    'sp_announce_menu');
add_action('sph_print_plugin_styles',							    'sp_announce_header');

add_filter('sph_plugins_active_buttons',        'sp_announce_uninstall_option', 10, 2);
add_filter('sph_admin_help-admin-components',   'sp_announce_admin_help', 10, 3);

# now lets hook into the right spot for the message output
$data = SP()->options->get('announce');
switch ($data['location']) {
    case 1: # before forum display
        add_action('sph_BeforeDisplayStart', 'sp_AnnounceMessage', 5);
        break;

    case 2: # before header
        add_action('sph_HeaderBegin', 'sp_AnnounceMessage', 5);
        break;

    case 3: # after header
        add_action('sph_HeaderEnd', 'sp_AnnounceMessage', 5);
        break;

    case 4: # before footer
        add_action('sph_FooterBegin', 'sp_AnnounceMessage', 5);
        break;

    case 5: # after footer
        add_action('sph_FooterEnd', 'sp_AnnounceMessage', 5);
        break;

    case 6: # after forum display
        add_action('sph_AfterDisplayEnd', 'sp_AnnounceMessage', 5);
        break;

    case 7: # use the display template function
    default:
        break;
}

function sp_announce_localization() {
	sp_plugin_localisation('sp-announce');
}

function sp_announce_install() {
    require_once SPANNOUNCEDIR.'sp-announce-install.php';
    sp_announce_do_install();
}

function sp_announce_deactivate() {
    require_once SPANNOUNCEDIR.'sp-announce-uninstall.php';
    sp_announce_do_deactivate();
}

function sp_announce_uninstall() {
    require_once SPANNOUNCEDIR.'sp-announce-uninstall.php';
    sp_announce_do_uninstall();
}

function sp_announce_sp_activate() {
	require_once SPANNOUNCEDIR.'sp-announce-install.php';
    sp_announce_do_sp_activate();
}

function sp_announce_sp_deactivate() {
	require_once SPANNOUNCEDIR.'sp-announce-uninstall.php';
    sp_announce_do_sp_deactivate();
}

function sp_announce_sp_uninstall() {
	require_once SPANNOUNCEDIR.'sp-announce-uninstall.php';
    sp_announce_do_sp_uninstall();
}

function sp_announce_upgrade_check() {
    require_once SPANNOUNCEDIR.'sp-announce-upgrade.php';
    sp_announce_do_upgrade_check();
}

function sp_announce_uninstall_option($actionlink, $plugin) {
    require_once SPANNOUNCEDIR.'sp-announce-uninstall.php';
    $actionlink = sp_announce_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_announce_reset_permissions() {
    require_once SPANNOUNCEDIR.'sp-announce-install.php';
    sp_announce_do_reset_permissions();
}

function sp_announce_menu() {
    $subpanels = array(__('Announce', 'sp-announce') => array('admin' => 'sp_announce_admin_options', 'save' => 'sp_announce_admin_save_options', 'form' => 1, 'id' => 'announceopt'));
    SP()->plugin->add_admin_subpanel('components', $subpanels);
}

function sp_announce_admin_options() {
    require_once SPANNOUNCEADMINDIR.'sp-announce-admin-options.php';
	sp_announce_admin_options_form();
}

function sp_announce_admin_save_options() {
    require_once SPANNOUNCEADMINDIR.'sp-announce-admin-options-save.php';
    return sp_announce_admin_options_save();
}

function sp_announce_admin_help($file, $tag, $lang) {
    if ($tag == '[announce-options]' || $tag == '[announce-message]') $file = SPANNOUNCEADMINDIR.'sp-announce-admin-help.'.$lang;
    return $file;
}

function sp_announce_header() {
	$css = SP()->theme->find_css(SPANNOUNCECSS, 'sp-announce.css', 'sp-announce.spcss');
    SP()->plugin->enqueue_style('sp-announce', $css);
}

# display template function
function sp_AnnounceMessage($args='') {
    require_once SPANNOUNCETAGS.'sp-announce-display-tag.php';
    sp_do_AnnounceMessage($args);
}
