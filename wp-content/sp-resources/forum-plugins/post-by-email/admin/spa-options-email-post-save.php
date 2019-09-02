<?php
/*
Simple:Press
Admin Save Post by Email settings
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_emailpost_do_admin_email_save() {
	check_admin_referer('forum-adminform_email', 'forum-adminform_email');

	$opts = SP()->options->get('spEmailPost');
	$interval = $opts['interval'];

	$opts['server'] 	= SP()->filters->str($_POST['server']);
	$opts['port'] 		= SP()->filters->integer($_POST['port']);
	$opts['pass'] 		= SP()->filters->str($_POST['pass']);
	$opts['interval']	= SP()->filters->integer($_POST['interval']);

	$opts['tls']		= (isset($_POST['tls'])) ? true : false;
	$opts['ssl']		= (isset($_POST['ssl'])) ? true : false;

	if($opts['interval']==0) $opts['interval']=1800;

	SP()->options->update('spEmailPost', $opts);

	if($interval != $opts['interval']) {
	 	# restart cron
	 	wp_clear_scheduled_hook('sph_emailpost_cron');
	    wp_schedule_event(time(), 'sp_emailpost_interval', 'sph_emailpost_cron');
	}
}
