<?php
/*
Simple:Press Plugin Title: myCred Extended Integration
Version: 2.1.0
Item Id: 3923 
Plugin URI: https://simple-press.com/downloads/mycred-integration-plugin/
Description: Extended Integration of Simple:Press with the (Required) WordPress myCred plugin<br />MyCred support available with the <b><u>Answers Topic</u></b>, <b><u>Birthdays</u></b>, <b><u>Polls</u></b>, <b><u>Post Rating</u></b>, <b><u>Post Thanks</u></b> and <b><u>Reputation</u></b> plugins
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPCREDDBVERSION', 1);

# ======================================
# CONSTANTS USED BY THIS PLUGIN
# ======================================
define('SPCREDDIR',			SPPLUGINDIR.'mycred/');
define('SPCREDURL',			SPPLUGINURL.'mycred/');
define('SPCREDICON',		SPPLUGINURL.'mycred/resources/images/');
define('SPCREDCSS',			SPPLUGINURL.'mycred/resources/css/');

# ======================================
# ACTIONS/FILTERS USED BY THUS PLUGIN
# ======================================
add_action('sph_activate_mycred/sp-mycred-plugin.php',				'sp_mycred_install');
add_action('sph_admin_panel_header',								'sp_mycred_show_alert');
add_action('mycred_pre_init',										'sp_mycred_load_class', 1);
add_action('init',													'sp_mycred_localisation');
add_action('sph_uninstalled',										'sp_mycred_sp_uninstall');
add_action('sph_uninstall_mycred/sp-mycred-plugin.php',				'sp_mycred_uninstall');
add_action('admin_footer',											'sp_mycred_upgrade_check');
add_action('sph_plugin_update_mycred/sp-mycred-plugin.php', 		'sp_mycred_upgrade_check');
add_filter('sph_plugins_active_buttons',							'sp_mycred_uninstall_option', 10, 2);
add_action('sph_print_plugin_styles',								'sp_mycred_header');


function sp_mycred_install() {
	require_once SPCREDDIR.'sp-mycred-install.php';
	sp_mycred_do_install();
}

function sp_mycred_show_alert() {
	if (! class_exists('myCRED_Core')) {
		echo '<div class="sfoptionerror"><b>'.__('The WordPress mycred plugin cannot be found!', 'sp-mycred').' <a href="http://wordpress.org/extend/plugins/mycred/" target="_blank">'.__('Please install from the WordPress plugin site', 'sp-mycred').'</a> '.__('to enable Simple:Press mycred integration.', 'sp-mycred').'</b></div>';
	}
}

function sp_mycred_load_class() {
	if (defined('myCRED_VERSION')) {
		require_once SPCREDDIR.'/library/sp-mycred-component-class.php';
	}
}

function sp_mycred_localisation() {
	sp_plugin_localisation('sp-mycred');
}

function sp_mycred_uninstall() {
	require_once SPCREDDIR.'sp-mycred-uninstall.php';
	sp_mycred_do_uninstall();
}

function sp_mycred_sp_uninstall() {
	require_once SPCREDDIR.'sp-mycred-uninstall.php';
	sp_mycred_do_sp_uninstall();
}

function sp_mycred_upgrade_check() {
	require_once SPCREDDIR.'sp-mycred-upgrade.php';
	sp_mycred_do_upgrade_check();
}

function sp_mycred_uninstall_option($actionlink, $plugin) {
	if ($plugin == 'mycred/sp-mycred-plugin.php') {
		$url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
		$actionlink.= "&nbsp;&nbsp;<a href='".$url."' title='".__('Uninstall this plugin', 'sp-mycred')."'>".__('Uninstall', 'sp-mycred')."</a>";

		$myCredStatus = get_option('mycred_setup_completed');
		if (defined('myCRED_VERSION') && !empty($myCredStatus)) {
			$actionlink.= "&nbsp;&nbsp;<a href='".admin_url()."admin.php?page=mycred-hooks' title='".__('MyCred Hooks', 'sp-mycred')."'>".__('MyCred Hooks', 'sp-mycred')."</a>";
		}
	}
	return $actionlink;
}

function sp_mycred_header() {
	$css = SP()->theme->find_css(SPCREDCSS, 'sp-mycred.css');
    SP()->plugin->enqueue_style('sp-mycred', $css);
}

# =========================================================
# TEMPLATE TAGS REQUIRE WORKING ON IF ANY TO BE USED
# =========================================================

# ----------------------------------------------
# Define Template Tag globally available
# ----------------------------------------------

# tag needs to be used in the forum view topic loop
function sp_PostIndexMyCred($args='', $label='', $toolTip='') {
	require_once SPCREDDIR.'template-tags/sp-mycred-post-index-points.php';
	sp_PostIndexMyCredTag($args, $label, $toolTip);
}

# generic tag can be used anywhere
# for current logged in user (or guest), leave $userid blank
# for specific user, pass the $userid
function sp_MyCred($args='', $userid='', $toolTip='') {
	require_once SPCREDDIR.'template-tags/sp-mycred-points.php';
	sp_MyCredTag($args, $userid, $toolTip);
}
