<?php
/*
Simple:Press Plugin Title: Remove Spam Registrations
Version: 2.1.0
Item Id: 3974
Plugin URI: https://simple-press.com/downloads/remove-spam-registrations-plugin/
Description: A Simple:Press plugin for removing spam registrations (users who registered by never posted)
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
$LastChangedDate: 2018-08-15 07:57:46 -0500 (Wed, 15 Aug 2018) $
$Rev: 15706 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

define('SRDIR', 		SPPLUGINDIR.'spam-registrations/');
define('SRADMINDIR', 	SPPLUGINDIR.'spam-registrations/admin/');
define('SRSCRIPT', 		SPPLUGINURL.'spam-registrations/resources/jscript/');
define('SRAJAXDIR', 	SPPLUGINDIR.'spam-registrations/ajax/');

add_action('init', 		                'sp_spam_reg_localization');
add_action('sph_admin_menu', 			'sp_spam_reg_menu');
add_action('sph_scripts_admin_end', 	'sp_spam_reg_load_js');

add_filter('sph_admin_help-admin-users', 	'sp_spam_reg_admin_help', 10, 3);
add_filter('sph_load_admin_textdomain', 	'sp_spam_reg_load_admin');
add_filter('sph_plugins_active_buttons',    'sp_spam_reg_uninstall_option', 10, 2);

# Ajax handler
add_action('wp_ajax_spam-reg',				'sp_spam_reg_ajax_manage');
add_action('wp_ajax_nopriv_spam-reg',		'sp_spam_reg_ajax_manage');


function sp_spam_reg_menu() {
    $subpanels = array(
                __('Spam Registrations', 'sp-spam') => array('admin' => 'sp_spam_reg_admin_registrations', 'save' => '', 'form' => 0, 'id' => 'spamreg')
                            );
    SP()->plugin->add_admin_subpanel('users', $subpanels);
}

function sp_spam_reg_admin_registrations() {
    require_once SRADMINDIR.'sp-spam-reg-admin-registrations.php';
	sp_spam_reg_admin_registrations_form();
}

function sp_spam_reg_admin_list() {
    require_once SRADMINDIR.'sp-spam-reg-admin-list.php';
	sp_spam_reg_admin_list_form();
}

function sp_spam_reg_admin_list_save() {
    require_once SRADMINDIR.'sp-spam-reg-admin-list-save.php';
	sp_spam_reg_admin_list_do_save();
}

function sp_spam_reg_admin_help($file, $tag, $lang) {
    if ($tag == '[remove-spam-registrations]') $file = SRADMINDIR.'sp-spam-reg-admin-help.'.$lang;
    return $file;
}

function sp_spam_reg_localization() {
	sp_plugin_localisation('sp-spam');
}

function sp_spam_reg_load_js($footer) {
    wp_enqueue_script('sp-spam-reg', SRSCRIPT.'sp-spam-reg.min.js', false, false, $footer);
}

function sp_spam_reg_ajax_manage() {
	require_once SRAJAXDIR.'sp-spam-reg-ajax.php';
}

function sp_spam_reg_load_admin($special) {
    $special[] = 'action=spam-reg&';
    return $special;
}

function sp_spam_reg_uninstall_option($actionlink, $plugin) {
	if ($plugin == 'spam-registrations/sp-spam-reg-plugin.php') {
        $url = SPADMINPLUGINS.'&amp;tab=plugin&amp;admin=sp_spam_reg_admin_registrations&amp;save=&amp;form=0';
        $actionlink.= "&nbsp;&nbsp;<a href='$url' title='".__('Options', 'sp-spam')."'>".__('Options', 'sp-spam').'</a>';
    }
	return $actionlink;
}
