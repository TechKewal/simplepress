<?php
/*
Simple:Press
Reset Gravatar Cache routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

if(isset($_GET['cache']) && !empty($_GET['cache'])) {
	$md5 = SP()->filters->str($_GET['cache']);
	@unlink(SPGCSTOREDIR.'/'.$md5);

	$userid = SP()->filters->integer($_GET['id']);
	$av = array();
	$av['uploaded'] = '';
	$av['default'] = 0;
	SP()->memberData->update($userid, 'avatar', $av);
}

die();
