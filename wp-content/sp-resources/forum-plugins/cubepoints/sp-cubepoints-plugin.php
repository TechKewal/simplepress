<?php
/*
Simple:Press Plugin Title: CubePoints Integration
Version: 2.1.0
Item Id: 3953
Plugin URI: https://simple-press.com/downloads/cubepoints-integration-plugin/
Description: Integration of Simple:Press with the (Required) WordPress CubePoints plugin
Author: James Holding (Cubehouse) for Simple:Press
Original Author: James Holding (Cubehouse), Andy Staines & Steve Klasen
Author URI: http://www.cubehouse.org and https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2013-02-16 16:20:30 -0700 (Sat, 16 Feb 2013) $
$Rev: 9848 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPCUBEDBVERSION', 3);

# ======================================
# CONSTANTS USED BY THIS PLUGIN
# ======================================
define('SPCUBEDIR',         SPPLUGINDIR.'cubepoints/');
define('SPCUBEURL',         SPPLUGINURL.'cubepoints/');
define('SPCUBEADMINDIR',    SPPLUGINDIR.'cubepoints/admin/');
define('SPCUBEICON',        SPPLUGINURL.'cubepoints/resources/images/');
define('SPCUBECSS',	        SPPLUGINURL.'cubepoints/resources/css/');

# ======================================
# ACTIONS/FILTERS USED BY THUS PLUGIN
# ======================================
add_action('sph_activate_cubepoints/sp-cubepoints-plugin.php',	    'sp_cubepoints_install');
add_action('sph_deactivate_cubepoints/sp-cubepoints-plugin.php',	'sp_cubepounts_deactivate');
add_action('sph_uninstalled', 									    'sp_cubepoints_sp_uninstall');
add_action('sph_uninstall_cubepoints/sp-cubepoints-plugin.php',	    'sp_cubepoints_uninstall');
add_action('init', 												    'sp_cubepoints_localisation');
add_action('sph_admin_menu', 									    'sp_cubepoints_menu');
add_filter('sph_plugins_active_buttons',						    'sp_cubepoints_uninstall_option', 10, 2);
add_filter('sph_admin_help-admin-components', 					    'sp_cubepoints_admin_help', 10, 3);
add_action('sph_admin_panel_header', 							    'sp_cubepoints_show_alert');
add_action('admin_footer',                                          'sp_cubepoints_upgrade_check');
add_action('sph_plugin_update_cubepoints/sp-cubepoints-plugin.php', 'sp_cubepoints_upgrade_check');

if (function_exists('cp_points')) {
	add_action('sph_post_create', 								'sp_cubepoints_add_post');
	add_action('sph_post_delete', 								'sp_cubepoints_delete_post');
	add_action('sph_topic_delete', 								'sp_cubepoints_delete_topic');
	add_action('sph_post_rating_add',							'sp_cubepoints_rate_post', 10, 4);
	add_action('cp_logs_description', 							'sp_cubepoints_logs_desc', 10, 4);
	add_action('sph_print_plugin_styles',						'sp_cubepoints_header');
	add_action('sph_poll_created', 								'sp_cubepoints_poll_created', 10, 2);
	add_action('sph_poll_voted', 								'sp_cubepoints_poll_voted', 10, 3);
}

function sp_cubepoints_install() {
    require_once SPCUBEDIR.'sp-cubepoints-install.php';
    sp_cubepoints_do_install();
}

function sp_cubepounts_deactivate() {
    require_once SPCUBEDIR.'sp-cubepoints-uninstall.php';
    sp_cubepoints_do_deactivate();
}

function sp_cubepoints_uninstall() {
    require_once SPCUBEDIR.'sp-cubepoints-uninstall.php';
    sp_cubepoints_do_uninstall();
}

function sp_cubepoints_sp_uninstall() {
    require_once SPCUBEDIR.'sp-cubepoints-uninstall.php';
    sp_cubepoints_do_sp_uninstall();
}

function sp_cubepoints_upgrade_check() {
    require_once SPCUBEDIR.'sp-cubepoints-upgrade.php';
    sp_cubepoints_do_upgrade_check();
}

function sp_cubepoints_localisation() {
	sp_plugin_localisation('sp-cube');
}

function sp_cubepoints_menu() {
	$subpanels = array(
		__('CubePoints', 'sp-cube') => array('admin' => 'sp_cubepoints_admin_form', 'save' => 'sp_cubepoints_admin_save', 'form' => 1, 'id' => 'cubepts')
	);
	SP()->plugin->add_admin_subpanel('components', $subpanels);
}

function sp_cubepoints_uninstall_option($actionlink, $plugin) {
	if ($plugin == 'cubepoints/sp-cubepoints-plugin.php') {
		$url = SPADMINPLUGINS.'&amp;action=uninstall&amp;plugin='.$plugin.'&amp;sfnonce='.wp_create_nonce('forum-adminform_plugins');
		$actionlink.= "&nbsp;&nbsp;<a href='".$url."' title='".__('Uninstall this plugin', 'sp-cube')."'>".__('Uninstall', 'sp-cube')."</a>";

		$url = SPADMINCOMPONENTS.'&amp;tab=plugin&amp;admin=sp_cubepoints_admin_form&amp;save=sp_cubepoints_admin_save&amp;form=1';
		$actionlink.= "&nbsp;&nbsp;<a href='".$url."' title='".__('Options', 'sp-cube')."'>".__('Options', 'sp-cube')."</a>";
	}
	return $actionlink;
}

function sp_cubepoints_admin_help($file, $tag, $lang) {
    if ($tag == '[cubepoints]') $file = SPCUBEADMINDIR.'sp-cubepoints-admin-help.'.$lang;
    return $file;
}

function sp_cubepoints_show_alert(){
	if (!function_exists('cp_points')){
		echo '<div class="sfoptionerror">'.__('The WordPress CubePoints plugin cannot be found!', 'sp-cube').' <a href="http://wordpress.org/extend/plugins/cubepoints/" target="_blank">'.__('Please install from the WordPress plugin site', 'sp-cube').'</a> '.__('to enable Simple:Press CubePoints integration.', 'sp-cube').'</div>';
	}
}

function sp_cubepoints_admin_form(){
    require_once SPCUBEDIR.'/admin/spa-components-cubepoints-form.php';
	spa_cubepoints_admin_options_form();
}

function sp_cubepoints_admin_save(){
    require_once SPCUBEDIR.'/admin/spa-components-cubepoints-form.php';
	return spa_cubepoints_admin_options_save();
}

function sp_cubepoints_add_post($newpost) {
    require_once SPCUBEDIR.'/library/sp-cubepoints-components.php';
    sp_cubepoints_do_add_post($newpost);
}

function sp_cubepoints_delete_post($oldpost) {
    require_once SPCUBEDIR.'/library/sp-cubepoints-components.php';
	sp_cubepoints_do_delete_post($oldpost);
}

function sp_cubepoints_delete_topic($posts) {
    require_once SPCUBEDIR.'/library/sp-cubepoints-components.php';
	sp_cubepoints_do_delete_topic($posts);
}

function sp_cubepoints_rate_post($postid, $count, $sum, $user_id) {
    require_once SPCUBEDIR.'/library/sp-cubepoints-components.php';
	sp_cubepoints_do_rate_post($postid, $count, $sum, $user_id);
}

function sp_cubepoints_poll_created($pollid, $userid) {
    require_once SPCUBEDIR.'/library/sp-cubepoints-components.php';
	sp_cubepoints_do_poll_created($pollid, $userid);
}

function sp_cubepoints_poll_voted($pollid, $userid, $creator) {
    require_once SPCUBEDIR.'/library/sp-cubepoints-components.php';
	sp_cubepoints_do_poll_voted($pollid, $userid, $creator);
}

function sp_cubepoints_logs_desc($type,$uid,$points,$data) {
    require_once SPCUBEDIR.'/library/sp-cubepoints-components.php';
    sp_cubepoints_do_logs_desc($type,$uid,$points,$data);
}

function sp_cubepoints_header() {
    require_once SPCUBEDIR.'/library/sp-cubepoints-components.php';
    sp_cubepoints_do_header();
}

# ----------------------------------------------
# Define Template Tag globally available
# ----------------------------------------------

# tag needs to be used in the forum view topic loop
function sp_PostIndexCubePoints($args='', $toolTip='') {
    require_once SPCUBEDIR.'template-tags/sp-cubepoints-post-index-points.php';
    sp_PostIndexCubePointsTag($args, $toolTip);
}

# generic tag can be used anywhere
# for current logged in user (or guest), leave $userid blank
# for specific user, pass the $userid
function sp_CubePoints($args='', $userid='', $toolTip='') {
    require_once SPCUBEDIR.'template-tags/sp-cubepoints-points.php';
    sp_CubePointsTag($args, $userid, $toolTip);
}
