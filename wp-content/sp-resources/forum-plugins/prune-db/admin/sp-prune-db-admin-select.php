<?php
/*
Simple:Press
Prune Database Plugin Admin Pruning Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_prune_db_admin_do_prune() {
	require_once SP_PLUGIN_DIR.'/forum/database/sp-db-management.php';
	# loop through all of the filtered topics to see which ones we want to delete
	$tcount = SP()->filters->integer($_POST['tcount']);
	for ($x=0; $x<$tcount; $x++) {
		if (isset($_POST['topic'.$x])) {
			$ids = explode(':', $_POST['topic'.$x]);
			# call core function to remove topics
			sp_delete_topic(SP()->filters->integer($ids[1]), SP()->filters->integer($ids[0]), false);
		}
	}

    $mess = __('Database pruned!', 'sp-prune');
    return $mess;
}
