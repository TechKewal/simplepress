<?php
/*
Simple:Press Plugin Title: Achievements Integration
Version: 2.1.0
Item Id: 3954
Plugin URI: https://simple-press.com/downloads/achievements-integration-plugin/
Description: Extended Integration of Simple:Press with the (Required) WordPress Achievements plugin adding Topic Creation and Post Reply targets
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPACHDBVERSION', 1);

# ======================================
# CONSTANTS USED BY THIS PLUGIN
# ======================================
define('SPACHDIR',		SPPLUGINDIR.'achievements/');
define('SPACHURL',		SPPLUGINURL.'achievements/');
define('SPACHIMAGES',	SPPLUGINURL.'achievements/resources/images/');

# ======================================
# ACTIONS/FILTERS USED BY THUS PLUGIN
# ======================================
add_action('sph_activate_achievements/sp-achievements-plugin.php',		'sp_achievements_install');
add_action('sph_admin_panel_header',									'sp_achievements_show_alert');
add_action('init',														'sp_achievements_load_class', 1);
add_action('init',														'sp_achievements_localisation');
add_action('dpa_ready', 												'sp_setup_simplepress_extension');
add_action('sph_uninstalled',											'sp_achievements_sp_uninstall');
add_action('sph_uninstall_achievements/sp-achievements-plugin.php',		'sp_achievements_uninstall');
add_action('admin_footer',												'sp_achievements_upgrade_check');
add_action('sph_plugin_update_achievements/sp-achievements-plugin.php', 'sp_achievements_upgrade_check');
add_filter('sph_plugins_active_buttons',								'sp_achievements_uninstall_option', 10, 2);


function sp_achievements_install() {
	require_once SPACHDIR.'sp-achievements-install.php';
	sp_achievements_do_install();
}

function sp_achievements_show_alert() {
	if(!class_exists('DPA_Achievements_Loader')) {
		echo '<div class="sfoptionerror"><b>'.__('The WordPress Achievements plugin cannot be found!', 'sp-achieve').' <a href="http://wordpress.org/plugins/achievements/" target="_blank">'.__('Please install from the WordPress plugin site', 'sp-achieve').'</a> '.__('to enable Simple:Press Achievements integration.', 'sp-achieve').'</b></div>';
	}
}

function sp_achievements_load_class() {
	if(class_exists('DPA_CPT_Extension')) {
		require_once SPACHDIR.'/library/sp-achievements-component-class.php';
	}
}

function sp_setup_simplepress_extension() {
	require_once SPACHDIR.'/library/sp-achievements-component-class.php';
	dpa_init_simplepress_extension();
}

function sp_achievements_localisation() {
	sp_plugin_localisation('sp-achieve');
}

function sp_achievements_uninstall() {
	require_once SPACHDIR.'sp-achievements-uninstall.php';
	sp_achievements_do_uninstall();
}

function sp_achievements_sp_uninstall() {
	require_once SPACHDIR.'sp-achievements-uninstall.php';
	sp_achievements_do_sp_uninstall();
}

function sp_achievements_upgrade_check() {
	require_once SPACHDIR.'sp-achievements-upgrade.php';
	sp_achievements_do_upgrade_check();
}

function sp_achievements_uninstall_option($actionlink, $plugin) {
	if ($plugin == 'achievements/sp-achievements-plugin.php') {
		$url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
		$actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Uninstall this plugin', 'sp-achieve')."'>".__('Uninstall', 'sp-achieve').'</a>';
	}
	return $actionlink;
}
