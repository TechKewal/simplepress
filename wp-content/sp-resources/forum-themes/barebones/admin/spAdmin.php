<?php
/*
Simple:Press
Barebones SP Theme Admin Custom Control
$LastChangedDate: 2014-09-12 07:30:12 +0100 (Fri, 12 Sep 2014) $
$Rev: 11958 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');


# customiser ----------------------------------------------------

add_action('sph_activate_theme',			'sp_barebones_activate');
add_action('sph_admin_menu',				'sp_barebones_custom_menu');
add_action('sph_scripts_admin_end', 		'sp_barebones_load_admin_js');
add_filter('sph_UpdateBar',					'sp_barebones_update_bar', 1, 2);
add_filter('sph_admin_help-admin-themes',	'sp_barebones_custom_help', 10, 3);

function sp_barebones_activate() {
    require_once SPBBADMIN.'sp-barebones-activate.php';
	sp_barebones_setup();
}

function sp_barebones_custom_menu() {
	$subpanels = array(
		__('Barebones Customiser', 'spBarebones') => array('admin' => 'sp_barebones_options', 'save' => 'sp_barebones_options_save', 'form' => 1, 'id' => 'barebones')
	);
	SP()->plugin->add_admin_subpanel('themes', $subpanels);
}

function sp_barebones_load_admin_js() {
	wp_enqueue_script('farbtastic');
	wp_enqueue_style('farbtastic');
}

function sp_barebones_options() {
    require_once SPBBADMIN.'sp-barebones-options-form.php';
	sp_barebones_options_form();
}

function sp_barebones_options_save() {
    require_once SPBBADMIN.'sp-barebones-options-save.php';
	sp_barebones_options_save_custom();
}

function sp_barebones_update_bar($bar, $reload) {
	if($reload != 'barebones') return $bar;
    require_once SPBBADMIN.'sp-barebones-options-form.php';
	return sp_barebones_update_bar_custom($bar);
}

function sp_barebones_custom_help($file, $tag, $lang) {
    if ($tag == '[custom-options]') $file = SPBBADMIN.'sp-barebones-options-help.'.$lang;
    return $file;
}
