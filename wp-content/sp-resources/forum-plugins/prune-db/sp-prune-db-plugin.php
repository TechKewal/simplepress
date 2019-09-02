<?php
/*
Simple:Press Plugin Title: Prune Database
Version: 2.2.0
Item Id: 3978
Plugin URI: https://simple-press.com/downloads/prune-database-plugin/
Description: A Simple:Press plugin for pruning database topics and inactive users
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2019-01-07 21:16:56 -0600 (Mon, 07 Jan 2019) $
$Rev: 15866 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

define('PDBDIR', 		SPPLUGINDIR.'prune-db/');
define('PDBADMINDIR', 	SPPLUGINDIR.'prune-db/admin/');
define('PDBAJAXDIR', 	SPPLUGINDIR.'prune-db/ajax/');
define('PDBLIBDIR', 	SPPLUGINDIR.'prune-db/library/');
define('PDBSCRIPT', 	SPPLUGINURL.'prune-db/resources/jscript/');
define('PDBIMAGES', 	SPPLUGINURL.'prune-db/resources/images/');

add_action('init', 						'sp_prune_db_localization');
add_action('sph_scripts_admin_end', 	'sp_prune_db_load_js');
add_action('sph_admin_menu', 			'sp_prune_db_menu');

add_filter('sph_admin_help-admin-toolbox',	'sp_prune_db_help', 10, 3);
add_filter('sph_admin_help-admin-plugins', 	'sp_prune_db_help', 10, 3);
add_filter('sph_plugins_active_buttons',    'sp_prune_db_uninstall_option', 10, 2);

function sp_prune_db_menu() {
    $subpanels = array(
                __('Prune Database', 'sp-prune') => array('admin' => 'sp_prune_db_admin_filter', 'save' => 'sp_prune_db_admin_select', 'form' => 0, 'id' => 'spprune')
                            );
    SP()->plugin->add_admin_subpanel('toolbox', $subpanels);
}

function sp_prune_db_admin_filter() {
    require_once PDBLIBDIR.'sp-prune-db-components.php';
    require_once PDBADMINDIR.'sp-prune-db-admin-filter.php';
	sp_prune_db_admin_filter_form();
}

function sp_prune_db_admin_prune() {
    require_once PDBLIBDIR.'sp-prune-db-components.php';
    require_once PDBADMINDIR.'sp-prune-db-admin-prune.php';
	sp_prune_db_admin_prune_form();
}

function sp_prune_db_admin_select() {
    require_once PDBADMINDIR.'sp-prune-db-admin-select.php';
    return sp_prune_db_admin_do_prune();
}

function sp_prune_db_help($file, $tag, $lang) {
    if ($tag == '[select-topic-filter-date]' || $tag == '[select-group-forum-to-prune]' || $tag == '[select-topics-to-prune]') $file = PDBADMINDIR.'sp-prune-db-admin-help.'.$lang;
    return $file;
}

function sp_prune_db_localization() {
	sp_plugin_localisation('sp-prune');
}

function sp_prune_db_load_js() {
    wp_enqueue_script('sp-prune-db-cal', PDBSCRIPT.'sp-prune-db-calendar.min.js', false, false, false);
}

function sp_prune_db_uninstall_option($actionlink, $plugin) {
    require_once PDBDIR.'sp-prune-db-uninstall.php';
    $actionlink = sp_prune_db_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}
