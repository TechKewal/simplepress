<?php
/*
Simple:Press Plugin Title: Gravatar Cache
Version: 2.1.0
Item Id: 3958
Plugin URI: https://simple-press.com/downloads/gravatar-cache-plugin/
Description: A Simple:Press plugin for caching Gravatars within the local filesystem - Only use if you are utilising Gravatars on your forum
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPGRAVCACHE', 2);

define('SPGCDIR', 		SPPLUGINDIR.'gravatar-cache/');
define('SPGCSTOREDIR',	SP_STORE_DIR.'/'.SP()->plugin->storage['gravatar-cache']);
define('SPGCSTOREURL',	SP_STORE_URL.'/'.SP()->plugin->storage['gravatar-cache']);
define('SPGCJS',		SPPLUGINURL.'gravatar-cache/resources/jscript/');

add_action('sph_activate_gravatar-cache/sp-gravatar-cache-plugin.php', 	        'sp_gravatar_cache_install');
add_action('sph_uninstall_gravatar-cache/sp-gravatar-cache-plugin.php',         'sp_gravatar_cache_uninstall');
add_filter('sph_plugins_active_buttons', 							            'sp_gravatar_cache_uninstall_option', 10, 2);
add_action('sph_integration_storage_panel_location', 				           	'sp_gravatar_cache_storage_location');
add_action('sph_integration_storage_save', 							          	'sp_gravatar_cache_storage_save');
add_filter('sph_acknowledgements',									         	'sp_gravatar_cache_acknowledgement');
add_action('init',													         	'sp_gravatar_cache_localisation');
add_filter('sph_integration_tooltips', 		                                    'sp_gravatar_cache_tooltip');
add_filter('sph_ProfileAvatarDisplay',									        'sp_gravatar_cache_reset', 10 ,2);
add_action('sph_profile_overview_form_top',										'sp_gravatar_cache_reset_default', 10, 1);
add_action('sph_print_plugin_scripts', 										    'sp_gravatar_cache_load_js');
add_action('admin_footer',			                        			        'sp_gravatar_cache_upgrade');
add_action('sph_plugin_update_gravatar-cache/sp-gravatar-cache-plugin.php',     'sp_gravatar_cache_upgrade');
add_action('sph_uninstalled', 								                    'sp_gravatar_cache_sp_uninstall');

# Ajax Call
add_action('wp_ajax_gravcache', 'sp_gravatar_cache_ajax');
add_action('wp_ajax_nopriv_gravcache', 'sp_gravatar_cache_ajax');


function sp_gravatar_cache_install() {
    require_once SPGCDIR.'sp-gravatar-cache-install.php';
    sp_gravatar_cache_do_install();
}

function sp_gravatar_cache_uninstall() {
    require_once SPGCDIR.'sp-gravatar-cache-uninstall.php';
    sp_gravatar_cache_do_uninstall();
}

function sp_gravatar_cache_sp_uninstall() {
	require_once SPGCDIR.'sp-gravatar-cache-uninstall.php';
    sp_gravatar_cache_do_sp_uninstall();
}

function sp_gravatar_cache_upgrade() {
    require_once SPGCDIR.'sp-gravatar-cache-install.php';
    sp_gravatar_cache_do_upgrade();
}

function sp_gravatar_cache_uninstall_option($actionlink, $plugin) {
    require_once SPGCDIR.'sp-gravatar-cache-install.php';
    $actionlink = sp_gravatar_cache_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

function sp_gravatar_cache_storage_location() {
	$storage = SP()->options->get('sfconfig');
	$path = SP_STORE_DIR.'/'.$storage['gravatar-cache'];
	spa_paint_storage_input(__('Gravatar Cache Folder', 'sp-gravcache'), 'gravatar-cache', $storage['gravatar-cache'], $path, false, true);
}

function sp_gravatar_cache_storage_save() {
	$storage = SP()->options->get('sfconfig');
	if (!empty($_POST['gravatar-cache'])) $storage['gravatar-cache'] = trim(SP()->saveFilters->title(trim($_POST['gravatar-cache'])), '/');
	SP()->options->update('sfconfig', $storage);
}

function sp_gravatar_cache_acknowledgement($ack) {
	$ack[] = '<a href="http://zenpax.com/">'.__("Uses Code ideas from Kip Bond", "sp-gravcache").'</a>';
	return $ack;
}

function sp_gravatar_cache_tooltip($tooltips) {
    $tooltips['gravatar-cache'] = "The directory you wish to use for the Gravatar cache.";
	return $tooltips;
}

function sp_gravatar_cache_localisation() {
	sp_plugin_localisation('sp-gravcache');
}

function sp_gravatar_cache_ajax() {
	require_once SPGCDIR.'ajax/spa-ajax-gravatar-cache.php';
}

function sp_gravatar_cache_reset($out, $user) {
	if (file_exists(SPGCSTOREDIR.'/'.md5($user->user_email).'.jpeg')) {
		$ajaxURL = wp_nonce_url(SPAJAXURL."gravcache&amp;cache=".md5($user->user_email).".jpeg&amp;id=".$user->ID, 'gravcache');
		$out.= '<div class="spProfileFormSubmit">';
		$out.= '<input type="button" class="spSubmit spCenter" id="gravreset" name="gravreset" value="'.__('Reset Cached Gravatar', 'sp-gravcache').'" data-url="'.$ajaxURL.'" />';
		$out.= '</div>';
	}
	return $out;
}

function sp_gravatar_cache_reset_default($userid) {
	$av = SP()->memberData->get($userid, 'avatar');
	$av['default'] = 0;
	SP()->memberData->update($userid, 'avatar', $av);
}

function sp_gravatar_cache_load_js($footer) {
    $script = (defined('SP_SCRIPTS_DEBUG') && SP_SCRIPTS_DEBUG) ? SPGCJS.'sp-gravcache.js' : SPGCJS.'sp-gravcache.min.js';
	SP()->plugin->enqueue_script('spgrav', $script, array('jquery'), false, $footer);
}

# --------------------------------------------
# This is the main engine call
# --------------------------------------------
function sp_get_gravatar_cache_url($email = '', $size, $userid, $data) {
    require_once SPGCDIR.'library/sp-gravatar-cache.php';
	return sp_get_gravatar($email, $size, $userid, $data);
}
