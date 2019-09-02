<?php
/*
Simple:Press
Birthdays plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_birthdays_do_install() {
	$options = SP()->options->get('birthdays');
	if (empty($options)) {
        $options['days'] = 7;
        $options['dbversion'] = SPBDAYDBVERSION;
        SP()->options->update('birthdays', $options);
    }

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';

    # lets start our cron at 1 minute past midnight so it fills cache properly
    wp_schedule_event(strtotime('tomorrow + 1 minute'), 'hourly', 'sph_birthdays_cron');
    do_action('sph_birthdays_cron'); # need to fill the cache initially
}

# sp reactivated.
function sp_birthdays_do_sp_activate() {
	wp_clear_scheduled_hook('sph_birthdays_cron');
    wp_schedule_event(strtotime('tomorrow + 1 minute'), 'hourly', 'sph_birthdays_cron');
    do_action('sph_birthdays_cron'); # need to fill the cache initially
}
