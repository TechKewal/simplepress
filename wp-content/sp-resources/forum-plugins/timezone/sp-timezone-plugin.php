<?php
/*
Simple:Press Plugin Title: Timezone On Registration
Version: 2.1.0
Item Id: 4436
Plugin URI: https://simple-press.com/downloads/timezone-on-registration-plugin/
Description: A Simple:Press plugin for having users set their timezone when registering
Author: Simple:Press
Original Author: Andy Staines & Steve Klasen
Author URI: https://simple-press.com
Simple:Press Versions: 6.0 and above
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

# IMPORTANT DB VERSION
define('SPTIMEZONEDBVERSION', 0);

define('SPTIMEZONEDIR', 		SPPLUGINDIR.'timezone/');

# set up our actions
add_action('init', 										        'sp_timezone_localization');
add_action('sph_activate_timezone/sp-timezone-plugin.php',      'sp_timezone_install');
add_action('sph_deactivate_timezone/sp-timezone-plugin.php',    'sp_timezone_deactivate');
add_action('sph_uninstall_timezone/sp-timezone-plugin.php',     'sp_timezone_uninstall');
add_action('sph_activated', 				                    'sp_timezone_sp_activate');
add_action('sph_deactivated', 				                    'sp_timezone_sp_deactivate');
add_action('sph_uninstalled', 								    'sp_timezone_sp_uninstall');
add_action('sph_plugin_update_timezone/sp-timezone-plugin.php', 'sp_timezone_upgrade_check');
add_action('admin_footer',                                      'sp_timezone_upgrade_check');
add_action('register_form',		                                'sp_timezone_registration_form');
add_action('user_register',                                     'sp_timezone_registration', 999);

add_filter('sph_plugins_active_buttons',    'sp_timezone_uninstall_option', 10, 2);

function sp_timezone_localization() {
	sp_plugin_localisation('sp-timezone');
}

function sp_timezone_install() {
    require_once SPTIMEZONEDIR.'sp-timezone-install.php';
    sp_timezone_do_install();
}

function sp_timezone_deactivate() {
    require_once SPTIMEZONEDIR.'sp-timezone-uninstall.php';
    sp_timezone_do_deactivate();
}

function sp_timezone_uninstall() {
    require_once SPTIMEZONEDIR.'sp-timezone-uninstall.php';
    sp_timezone_do_uninstall();
}

function sp_timezone_sp_activate() {
	require_once SPTIMEZONEDIR.'sp-timezone-install.php';
    sp_timezone_do_sp_activate();
}

function sp_timezone_sp_deactivate() {
	require_once SPTIMEZONEDIR.'sp-timezone-uninstall.php';
    sp_timezone_do_sp_deactivate();
}

function sp_timezone_sp_uninstall() {
	require_once SPTIMEZONEDIR.'sp-timezone-uninstall.php';
    sp_timezone_do_sp_uninstall();
}

function sp_timezone_upgrade_check() {
    require_once SPTIMEZONEDIR.'sp-timezone-upgrade.php';
    sp_timezone_do_upgrade_check();
}

function sp_timezone_uninstall_option($actionlink, $plugin) {
    require_once SPTIMEZONEDIR.'sp-timezone-uninstall.php';
    $actionlink = sp_timezone_uninstall_option_links($actionlink, $plugin);
	return $actionlink;
}

# add our stuff to the wp registration form
function sp_timezone_registration_form() {
    # grab default timezone from wp sserver timezone
    $tz = get_option('timezone_string');
    if (empty($tz) || substr($tz, 0, 3) == 'UTC') $tz = 'UTC';

    # output the timezone select to the registration form
    echo '<p><br /><label>'.__('Select Your Timezone', 'sp-timezone').'</label><br /><select id="sptimezone" name="sptimezone">';
    $wptz = explode('<optgroup label=', wp_timezone_choice($tz));
    unset($wptz[count($wptz)-1]);
    echo implode('<optgroup label=', $wptz);
    echo '</select></p><br />';
}

# when registration is saved, set up the user timezone for the forum
function sp_timezone_registration($user_id) {
    # make sure we have valid user registered
    if (empty($user_id)) return;

    # get the user forum options
	$options = SP()->memberData->get($user_id, 'user_options');

    # make sure we have valid timezone set on the form
	if (isset($_POST['sptimezone'])) {
        # even though WP allows timezone of UTC its not a valid timezone so adjust
		if (preg_match('/^UTC[+-]/', $_POST['sptimezone']) ) {
			# correct for manual UTC offets
			$userOffset = preg_replace('/UTC\+?/', '', $_POST['sptimezone']) * 3600;
		} else {
			# get timezone offset for user
			$date_time_zone_selected = new DateTimeZone(SP()->filters->str($_POST['sptimezone']));
			$userOffset = timezone_offset_get($date_time_zone_selected, date_create());
		}

		# get timezone offset for server based on wp settings
		$wptz = get_option('timezone_string');
		if (empty($wptz)) {
			$serverOffset = get_option('gmt_offset');
		} else {
			$date_time_zone_selected = new DateTimeZone($wptz);
			$serverOffset = timezone_offset_get($date_time_zone_selected, date_create());
		}

		# calculate time offset between user and server
		$options['timezone'] = (int) round(($userOffset - $serverOffset) / 3600, 2);
		$options['timezone_string'] = SP()->filters->str($_POST['sptimezone']);
	} else {
		$options['timezone'] = 0;
		$options['timezone_string'] = 'UTC';
	}

    # save the user timezone in their user options record
	SP()->memberData->update($user_id, 'user_options', $options);
}
