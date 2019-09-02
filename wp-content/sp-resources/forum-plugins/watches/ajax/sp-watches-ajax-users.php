<?php
/*
Simple:Press
Topic Watches plugin ajax routine for users management functions
$LastChangedDate: 2018-10-17 15:17:49 -0500 (Wed, 17 Oct 2018) $
$Rev: 15756 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

spa_admin_ajax_support();

if (!sp_nonce('watches-users')) die();

require_once WLIBDIR.'sp-watches-database.php';

# Check Whether User Can Manage Users
if (!SP()->auths->current_user_can('SPF Manage Users')) die();

$action = SP()->filters->str($_GET['targetaction']);

if ($action == 'del_watches') {
	$userid = SP()->filters->integer($_GET['id']);
    SP()->activity->delete('type='.SPACTIVITY_WATCH."&uid=$userid");
    die();
}

if ($action == 'topiclist') {
    if (isset($_GET['page']))  {
    	$curpage = SP()->filters->integer($_GET['page']);
   	} else {
   		$curpage = 1;
   	}
    if (isset($_GET['swsearch'])) {
    	$search = SP()->filters->str($_GET['swsearch']);
   	} else {
   		$search = '';
   	}

    sp_watches_render_user_watches($curpage, $search);

    die();
}

die();
