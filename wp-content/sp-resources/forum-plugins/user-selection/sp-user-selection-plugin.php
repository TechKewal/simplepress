<?php
/*
Simple:Press Plugin Title: Language and Theme Selection
Version: 2.1.0
Item Id: 3962
Plugin URI: https://simple-press.com/downloads/language-and-theme-selection-plugin/
Description: Adds front end selection boxes that allows a user to select their own overlay/color and/or language preferences
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
NOTE: This plugin will show any languages that you have installed in the Simple Press Languages folder.
To get a site fully translated you will want to download the core, theme and plugin translations as all are used on the front end.
You can get available translations from our Simple Press translation site at http://glotpress.simple-press.com/glotpress/
More information can be found at: https://simple-press.com/documentation/installation/installation-information/localization/
Do not change the WPLANG constant for this plugin unless you want your whole site in that language.
Selecting the Site Default will give users the language set by WPLANG.
$LastChangedDate: 2018-09-21 19:05:55 -0500 (Fri, 21 Sep 2018) $
$Rev: 15737 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPUSELDBVERSION', 2);

# ======================================
# CONSTANTS USED BY THIS PLUGIN
# ======================================
define('SPUSELDIR',         SPPLUGINDIR.'user-selection/');
define('SPUSELCSS',         SPPLUGINURL.'user-selection/resources/css/');
define('SPUSELJS',	        SPPLUGINURL.'user-selection/resources/jscript/');
define('SPUSELADMINDIR',    SPPLUGINDIR.'user-selection/admin/');

# ======================================
# ACTIONS/FILTERS USED BY THUS PLUGIN
# ======================================
# Plugin and admin
add_action('init', 				                                              'sp_user_selection_localisation');
add_action('sph_print_plugin_scripts', 	                                      'sp_user_selection_load_js');
add_action('sph_activate_user-selection/sp-user-selection-plugin.php',        'sp_user_selection_install');
add_action('sph_deactivate_user-selection/sp-user-selection-plugin.php',	  'sp_user_selection_deactivate');
add_action('sph_uninstall_user-selection/sp-user-selection-plugin.php',	      'sp_user_selection_uninstall');
add_action('admin_footer',                                                    'sp_user_selection_upgrade_check');
add_action('sph_plugin_update_user-selection/sp-user-selection-plugin.php',   'sp_user_selection_upgrade_check');
add_action('sph_activate_theme',                                              'sp_user_selection_theme_activated');
add_action('sph_print_plugin_styles', 			                              'sp_user_selection_header');

add_filter('sph_theme',			                                              'sp_user_selection_set_overlay');
add_filter('sph_localization_mo', 				                              'sp_user_selection_set_lang');
add_filter('sph_localization_plugin_mo', 				                      'sp_user_selection_set_plugin_lang', 10, 2);
add_filter('sph_localization_theme_mo', 				                      'sp_user_selection_set_theme_lang', 10, 2);
add_filter('sph_plugins_active_buttons',						              'sp_user_selection_uninstall_option', 10, 2);
add_action('sph_admin_menu',										          'sp_user_selection_admin_menu');
add_filter('sph_admin_help-admin-toolbox',	                                  'sp_user_selection_admin_help', 10, 3);

# ======================================
# PLUGIN AND ADMIN
# ======================================

# ------------------------------------------------------
# Set up language file
# ------------------------------------------------------
function sp_user_selection_localisation() {
	sp_plugin_localisation('sp-usel');
}

function sp_user_selection_install() {
    require_once SPUSELDIR.'sp-user-selection-install.php';
    sp_user_selection_do_install();
}

function sp_user_selection_deactivate() {
    require_once SPUSELDIR.'sp-user-selection-uninstall.php';
	sp_user_selection_do_deactivate();
}

function sp_user_selection_uninstall() {
    require_once SPUSELDIR.'sp-user-selection-uninstall.php';
    sp_user_selection_do_uninstall();
}

function sp_user_selection_uninstall_option($actionlink, $plugin) {
    require_once SPUSELDIR.'sp-user-selection-uninstall.php';
    $actionlink = sp_user_selection_do_uninstall_option($actionlink, $plugin);
	return $actionlink;
}

function sp_user_selection_upgrade_check() {
    require_once SPUSELDIR.'sp-user-selection-upgrade.php';
    sp_user_selection_do_upgrade_check();
}

function sp_user_selection_admin_menu() {
    $subpanels = array(__('User Languages', 'sp-usel') => array('admin' => 'sp_user_selection_admin_options', 'save' => 'sp_user_selection_admin_save_options', 'form' => 1, 'id' => 'usel'));
	SP()->plugin->add_admin_subpanel('toolbox', $subpanels);
}

function sp_user_selection_admin_options() {
    require_once SPUSELADMINDIR.'sp-user-selection-admin-options.php';
	sp_user_selection_admin_options_form();
}

function sp_user_selection_admin_save_options() {
    require_once SPUSELADMINDIR.'sp-user-selection-admin-options-save.php';
    return sp_user_selection_admin_options_save();
}

function sp_user_selection_admin_help($file, $tag, $lang) {
    if ($tag == '[language-options]' || $tag == '[language-names]') $file = SPUSELADMINDIR.'sp-user-selection-admin-help.'.$lang;
    return $file;
}

function sp_user_selection_header() {
	$css = SP()->theme->find_css(SPUSELCSS, 'sp-selection.css', 'sp-selection.spcss');
    SP()->plugin->enqueue_style('sp-selection', $css);
}

# ------------------------------------------------------
# Load the preview script
# ------------------------------------------------------
function sp_user_selection_load_js($footer) {
	SP()->plugin->enqueue_script('spoverlay', SPUSELJS.'sp-user-selection.min.js', array('jquery'), false, $footer);
}

# ------------------------------------------------------
# Reset language from cookie for page load
# ------------------------------------------------------
function sp_user_selection_set_lang($mofile) {
	if (isset($_COOKIE['language']) && $_COOKIE['language'] != 'default') $mofile = SP_STORE_DIR.'/'.SP()->plugin->storage['language-sp'].'/sp-'.$_COOKIE['language'].'.mo';
    return $mofile;
}

function sp_user_selection_set_plugin_lang($mofile, $domain) {
	if (isset($_COOKIE['language']) && $_COOKIE['language'] != 'default') $mofile = SP_STORE_DIR.'/'.SP()->plugin->storage['language-sp-plugins'].'/'.$domain.'-'.$_COOKIE['language'].'.mo';
    return $mofile;
}

function sp_user_selection_set_theme_lang($mofile, $domain) {
	if (isset($_COOKIE['language']) && $_COOKIE['language'] != 'default') $mofile = SP_STORE_DIR.'/'.SP()->plugin->storage['language-sp-themes'].'/'.$domain.'-'.$_COOKIE['language'].'.mo';
    return $mofile;
}

# ------------------------------------------------------
# Reset theme color from cookie for page load
# ------------------------------------------------------
function sp_user_selection_set_overlay($theme) {
	if (isset($_COOKIE['overlay']) && isset($theme['color'])) {
		if ($_COOKIE['overlay'] != 'ovdefault') {
			$theme['color'] = $_COOKIE['overlay'];

    		$icons = '';
    		if (!empty($theme['color'])) {
                if (!empty($theme['parent']) && !file_exists(SPTHEMEBASEDIR.$theme['theme'].'/styles/overlays/'.$theme['color'].'.php')) {
                    $f = SPTHEMEBASEDIR.$theme['parent'].'/styles/overlays/'.$theme['color'].'.php';
                } else {
                    $f = SPTHEMEBASEDIR.$theme['theme'].'/styles/overlays/'.$theme['color'].'.php';
                }
    			$icons = SP()->filters->str(SP()->theme->get_overlay_icons($f));
    		}

    		$theme['icons'] = $icons;
		}
	}

	return $theme;
}

function sp_user_selection_theme_activated($theme) {
    setcookie('overlay', '', time() - 3600, '/');
}

# ------------------------------------------------------
# TEMPLATE TAG
# ------------------------------------------------------
function sp_UserSelectOptions($args='', $labelTheme='', $labelLanguage='') {
	require_once SPUSELDIR.'template-tags/sp-user-selection-tags.php';
	return sp_UserSelectOptionsTag($args, $labelTheme, $labelLanguage);
}
