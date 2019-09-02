<?php
/*
Simple:Press
Report Post plugin install routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_prune_db_do_install() {
	$option = SP()->options->get('prune-db');
	if (empty($option)) {
		$option['email-list'] = get_option('admin_email');
		SP()->options->add('prune-db', $option);
    }
}
