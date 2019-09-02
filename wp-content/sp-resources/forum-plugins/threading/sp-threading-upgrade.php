<?php
/*
Simple:Press
Threading$LastChangedDate: 2014-06-07 22:32:19 +0100 (Sat, 07 Jun 2014) $
$Rev: 11528 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_threading_do_upgrade_check() {
	if (!SP()->plugin->is_active('threading/sp-threading-plugin.php')) return;

	$tt = SP()->options->get('threading');

	$db = $tt['dbversion'];
	if (empty($db)) $db = 0;

	# quick bail check
	if ($db == SPTHREADDBVERSION ) return;

	# apply upgrades as needed

	# db version upgrades

	# save data
	$tt['dbversion'] = SPTHREADDBVERSION;
	SP()->options->update('threading', $tt);
}
