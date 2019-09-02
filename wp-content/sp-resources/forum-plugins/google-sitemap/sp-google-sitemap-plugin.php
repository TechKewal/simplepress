<?php
/*
Simple:Press Plugin Title: Google XML Sitemap
Version: 3.1.0
Item Id: 3922
Plugin URI: https://simple-press.com/downloads/xml-sitemap-plugin/
Description: A Simple:Press plugin for generating an xml sitemap. Works in conjunction with and requires either the Arne Brachold Google XML Sitemap or the Yoast WordPress SEO plugin for WordPress
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

define('GSMDBVERSION', 5);

define('GSMDIR', 		SPPLUGINDIR.'google-sitemap/');
define('GSMADMINDIR', 	SPPLUGINDIR.'google-sitemap/admin/');
define('GSMLIBDIR', 	SPPLUGINDIR.'google-sitemap/library/');

add_action('init', 												            'sp_google_sitemap_localization');
add_action('sph_activate_google-sitemap/sp-google-sitemap-plugin.php', 	    'sp_google_sitemap_install');
add_action('sph_uninstall_google-sitemap/sp-google-sitemap-plugin.php',     'sp_google_sitemap_uninstall');
add_action('sph_forum_create_forum_options', 			                    'sp_google_sitemap_create_forum');
add_action('sph_forum_forum_create', 					                    'sp_google_sitemap_create_forum_save');
add_action('sph_forum_edit_forum_options', 				                    'sp_google_sitemap_edit_forum');
add_action('sph_forum_forum_edit', 						                    'sp_google_sitemap_edit_forum_save');
add_action('sph_activated', 				                                'sp_google_sitemap_sp_activate');
add_action('sph_deactivated', 				                                'sp_google_sitemap_sp_deactivate');
add_action('sph_plugin_update_google-sitemap/sp-google-sitemap-plugin.php', 'sp_google_sitemap_upgrade_check');
add_action('admin_footer',                                                  'sp_google_sitemap_upgrade_check');

add_filter('sph_plugins_active_buttons',    'sp_google_sitemap_uninstall_option', 10, 2);

# google xml sitemap support
add_action('sm_build_index', 'sp_google_sitemap_add_index');
function sp_google_sitemap_add_index($data) {
    require_once GSMLIBDIR.'sp-google-sitemap-components.php';
    sp_google_sitemap_do_add_index($data);
}

add_action('sm_build_content', 'sp_google_sitemap_add_sitemap', 10, 3);
function sp_google_sitemap_add_sitemap($data, $type, $params) {
    require_once GSMLIBDIR.'sp-google-sitemap-components.php';
    sp_google_sitemap_do_add_sitemap($data, $type, $params);
}

add_action('sm_buildmap', 'sp_google_sitemap_build_sitemap');
function sp_google_sitemap_build_sitemap() {
    if (class_exists('GoogleSitemapGeneratorStandardBuilder')) return; # only use in old google sitemap < 4.x
    require_once GSMLIBDIR.'sp-google-sitemap-components.php';
    sp_google_sitemap_do_build_sitemap();
}

# wp seo sitemap support
add_filter('wpseo_sitemap_index', 'sp_google_sitemap_generate_yoast_main');
function sp_google_sitemap_generate_yoast_main($map) {
    require_once GSMLIBDIR.'sp-google-sitemap-components.php';
    $map = sp_google_sitemap_do_generate_yoast_main();
    return $map;
}

add_action('wpseo_do_sitemap_forum', 'sp_google_sitemap_generate_yoast_map');
function sp_google_sitemap_generate_yoast_map() {
    require_once GSMLIBDIR.'sp-google-sitemap-components.php';
    sp_google_sitemap_do_generate_yoast_map();
}

# wp all in one seo sitemap support
add_filter('aiosp_sitemap_extra', 'sp_google_sitemap_generate_aioseo_slug');
function sp_google_sitemap_generate_aioseo_slug($extra) {
    require_once GSMLIBDIR.'sp-google-sitemap-components.php';
    $extra = sp_google_sitemap_do_generate_aioseo_slug($extra);
    return $extra;
}

add_filter('aiosp_sitemap_data', 'sp_google_sitemap_generate_aioseo_index', 10, 4);
function sp_google_sitemap_generate_aioseo_index($map, $type, $page, $options) {
    require_once GSMLIBDIR.'sp-google-sitemap-components.php';
    $map = sp_google_sitemap_do_generate_aioseo_index($map, $type, $page, $options);
    return $map;
}

add_action('aiosp_sitemap_custom_forum', 'sp_google_sitemap_generate_aioseo_map');
function sp_google_sitemap_generate_aioseo_map($map) {
    require_once GSMLIBDIR.'sp-google-sitemap-components.php';
    $map = sp_google_sitemap_do_generate_aioseo_map($map);
    return $map;
}

# plugin stuff
function sp_google_sitemap_localization() {
	sp_plugin_localisation('sp-gsm');
}

function sp_google_sitemap_uninstall() {
    require_once GSMDIR.'sp-google-sitemap-uninstall.php';
    sp_google_sitemap_do_uninstall();
}

function sp_google_sitemap_install() {
    require_once GSMDIR.'sp-google-sitemap-install.php';
    sp_google_sitemap_do_install();
}

function sp_google_sitemap_sp_activate() {
    require_once GSMDIR.'sp-google-sitemap-install.php';
    sp_google_sitemap_do_sp_activate();
}

function sp_google_sitemap_sp_deactivate() {
    require_once GSMDIR.'sp-google-sitemap-uninstall.php';
    sp_google_sitemap_do_sp_deactivate();
}

function sp_google_sitemap_upgrade_check() {
    require_once GSMDIR.'sp-google-sitemap-upgrade.php';
    sp_google_sitemap_do_upgrade_check();
}

function sp_google_sitemap_uninstall_option($actionlink, $plugin) {
    require_once GSMDIR.'sp-google-sitemap-uninstall.php';
    $actionlink = sp_google_sitemap_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_google_sitemap_create_forum() {
	spa_paint_checkbox(__('Include this forum in sitemap', 'sp-gsm'), 'forum_sitemap', 1);
}

function sp_google_sitemap_create_forum_save($forumid) {
	$sitemap = (isset($_POST['forum_sitemap'])) ? 1 : 0;
	SP()->DB->execute('UPDATE '.SPFORUMS." SET in_sitemap=$sitemap WHERE forum_id=$forumid");
}

function sp_google_sitemap_edit_forum($forum) {
	spa_paint_checkbox(__('Include this forum in sitemap', 'sp-gsm'), 'forum_sitemap', $forum->in_sitemap);
}

function sp_google_sitemap_edit_forum_save($forumid) {
	$sitemap = (isset($_POST['forum_sitemap'])) ? 1 : 0;
	SP()->DB->execute('UPDATE '.SPFORUMS." SET in_sitemap=$sitemap WHERE forum_id=$forumid");
}
