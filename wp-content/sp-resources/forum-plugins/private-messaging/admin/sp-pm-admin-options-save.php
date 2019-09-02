<?php
/*
Simple:Press
PM Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_pm_admin_options_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	# Save Private Message options
	$pmdata = SP()->options->get('pm');
	if (isset($_POST['email'])) { $pmdata['email'] = true; } else { $pmdata['email'] = false; }
	if (isset($_POST['cc'])) { $pmdata['cc'] = true; } else { $pmdata['cc'] = false; }
	if (isset($_POST['bcc'])) { $pmdata['bcc'] = true; } else { $pmdata['bcc'] = false; }
	if (isset($_POST['limitedsend'])) { $pmdata['limitedsend'] = true; } else { $pmdata['limitedsend'] = false; }
	if (isset($_POST['limitedug'])) { $pmdata['limitedug'] = true; } else { $pmdata['limitedug'] = false; }

	$pmdata['max'] = SP()->filters->integer($_POST['max']);
	$pmdata['threadpaging'] = SP()->filters->integer($_POST['threadpaging']);
	$pmdata['messagepaging'] = SP()->filters->integer($_POST['messagepaging']);
	$pmdata['maxrecipients'] = SP()->filters->integer($_POST['maxrecipients']);
	$pmdata['accessposts'] = SP()->filters->integer($_POST['accessposts']);

	if (isset($_POST['uploads'])) { $pmdata['uploads'] = true; } else { $pmdata['uploads'] = false; }

	# auto removal period
	if (isset($_POST['keep']) && $_POST['keep'] > 0) {
		$pmdata['keep'] = intval($_POST['keep']);
	} else {
		$pmdata['keep'] = 365; # if not filled in make it one year
	}

	# auto removal cron job
	wp_clear_scheduled_hook('sph_pm_cron');
	if (isset($_POST['remove'])) {
		$pmdata['remove'] = true;
		wp_schedule_event(time(), 'daily', 'sph_pm_cron');
	} else {
		$pmdata['remove'] = false;
	}

	SP()->options->update('pm', $pmdata);

	$mess = __('Private messaging options updated', 'sp-pm');
	return $mess;
}
