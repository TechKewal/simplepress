<?php
/*
Simple:Press
Who's Online Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_admin_bar_admin_options_save() {
	$options = array();
    $options['dashboardposts'] = isset($_POST['dashboardposts']);
	SP()->options->update('spAdminBar', $options);

	SP()->options->update('spAkismet', SP()->filters->integer($_POST['spAkismet']));

	return __('Admin bar options updated!', 'spab');
}
