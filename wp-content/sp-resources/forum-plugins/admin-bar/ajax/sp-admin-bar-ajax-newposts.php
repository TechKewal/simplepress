<?php
/*
Simple:Press
Admin Bar plugin ajax routine for new post list
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

if (SP()->user->thisUser->admin || SP()->user->thisUser->moderator) {
    require_once SPABLIBDIR.'sp-admin-bar-components.php';

	# must be loading up the new post list
	$newposts = sp_GetAdminsQueuedPosts();
	sp_NewPostListAdmin($newposts);
}

die();
