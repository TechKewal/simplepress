<?php
/*
Simple:Press Plugin Title: WooCommerce
Version: 1.0.1
Item Id: 77646
Plugin URI: https://simple-press.com
Description: A Simple:Press plugin for adding a link to the SimplePress user profile on the WooCommerce dashboard
Author: Simple:Press Team
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2017-05-20 17:46:30 -0500 (Sat, 20 May 2017) $
$Rev: 15387 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPWCDBVERSION', 1);

define('SPWCDIR', 		SPPLUGINDIR.'woocommerce/');
define('SPWCADMINDIR',	SPPLUGINDIR.'woocommerce/admin/');
define('SPWCAJAXDIR',	SPPLUGINDIR.'woocommerce/ajax/');
define('SPWCLIBDIR',	SPPLUGINDIR.'woocommerce/library/');
define('SPWCLIBURL',	SPPLUGINURL.'woocommerce/library/');
define('SPWCCSS',		SPPLUGINURL.'woocommerce/resources/css/');
define('SPWCSCRIPT',	SPPLUGINURL.'woocommerce/resources/jscript/');
define('SPWCIMAGES',	SPPLUGINURL.'woocommerce/resources/images/');
define('SPWCTAGS',		SPPLUGINDIR.'woocommerce/template-tags/');
define('SPWCTEMP', 	    SPPLUGINDIR.'woocommerce/template-files/');

add_action('init',													'sp_woocommerce_localization');
add_action('sph_activate_woocommerce/sp-woocommerce-plugin.php',	'sp_woocommerce_install');
add_action('sph_deactivate_woocommerce/sp-woocommerce-plugin.php',	'sp_woocommerce_deactivate');
add_action('sph_uninstall_woocommerce/sp-woocommerce-plugin.php',	'sp_woocommerce_uninstall');
add_action('sph_activated',											'sp_woocommerce_sp_activate');
add_action('sph_deactivated',										'sp_woocommerce_sp_deactivate');
add_action('sph_uninstalled',										'sp_woocommerce_sp_uninstall');
add_action('sph_plugin_update_woocommerce/sp-woocommerce-plugin.php', 'sp_woocommerce_upgrade_check');
add_action('admin_footer',											'sp_woocommerce_upgrade_check');
add_action('sph_permissions_reset',									'sp_woocommerce_reset_permissions');
add_action('sph_admin_menu',										'sp_woocommerce_menu');

add_filter('sph_plugins_active_buttons',        'sp_woocommerce_uninstall_option', 10, 2);
add_filter('sph_admin_help-admin-components', 	'sp_woocommerce_admin_help', 10, 3);

add_filter('init', 	'sp_woocommerce_links');  

function sp_woocommerce_menu() {
        $subpanels = array(
                __('WooCommerce', 'sp-woocommerce') => array('admin' => 'sp_woocommerce_admin_form', 'save' => 'sp_woocommerce_admin_save', 'form' => 1, 'id' => 'woocommerce')
                );
        SP()->plugin->add_admin_subpanel('components', $subpanels);
}

function sp_woocommerce_admin_form() {
    include_once(SPWCADMINDIR.'sp-woocommerce-admin-form.php');
	sp_woocommerce_do_admin_form();
}

function sp_woocommerce_admin_save() {
    include_once(SPWCADMINDIR.'sp-woocommerce-admin-save.php');
    return sp_woocommerce_do_admin_save();
}

function sp_woocommerce_localization() {
	sp_plugin_localisation('sp-woocommerce');
}

function sp_woocommerce_admin_help($file, $tag, $lang) {
    if ($tag == '[woocommerce-options]' || $tag == '[woocommerce-labels]' ) $file = SPWCADMINDIR.'sp-woocommerce-admin-help.'.$lang;
    return $file;
}

function sp_woocommerce_install() {
    include_once(SPWCDIR.'sp-woocommerce-install.php');
    sp_woocommerce_do_install();
}

function sp_woocommerce_deactivate() {
    include_once(SPWCDIR.'sp-woocommerce-uninstall.php');
    sp_woocommerce_do_deactivate();
}

function sp_woocommerce_uninstall() {
    include_once(SPWCDIR.'sp-woocommerce-uninstall.php');
    sp_woocommerce_do_uninstall();
}

function sp_woocommerce_sp_activate() {
	include_once(SPWCDIR.'sp-woocommerce-install.php');
    sp_woocommerce_do_sp_activate();
}

function sp_woocommerce_sp_deactivate() {
	include_once(SPWCDIR.'sp-woocommerce-uninstall.php');
    sp_woocommerce_do_sp_deactivate();
}

function sp_woocommerce_sp_uninstall() {
	include_once(SPWCDIR.'sp-woocommerce-uninstall.php');
    sp_woocommerce_do_sp_uninstall();
}

function sp_woocommerce_upgrade_check() {
    include_once(SPWCDIR.'sp-woocommerce-upgrade.php');
    sp_woocommerce_do_upgrade_check();
}

function sp_woocommerce_uninstall_option($actionlink, $plugin) {
    include_once(SPWCDIR.'sp-woocommerce-uninstall.php');
    $actionlink = sp_woocommerce_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_woocommerce_reset_permissions() {
    include_once(SPWCDIR.'sp-woocommerce-install.php');
    sp_woocommerce_do_reset_permissions();
}

function sp_woocommerce_links() {
    include_once(SPWCLIBDIR.'sp-woocommerce-components.php');
    //sp_woocommerce_do_links();
}

?>