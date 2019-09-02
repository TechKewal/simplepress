<?php
/*
Simple:Press
Topic Subscriptions plugin ajax routine for forums management functions
$LastChangedDate: 2018-10-17 15:17:49 -0500 (Wed, 17 Oct 2018) $
$Rev: 15756 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

spa_admin_ajax_support();

if (!sp_nonce('subs-forums')) die();

require_once SLIBDIR.'sp-subscriptions-database.php';
require_once SLIBDIR.'sp-subscriptions-components.php';
require_once SP_PLUGIN_DIR.'/admin/library/spa-tab-support.php';

# Check Whether User Can Manage Users
if (!SP()->auths->current_user_can('SPF Manage Users')) die();

$action = SP()->filters->str($_GET['targetaction']);

if ($action == 'forumlist') {
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

    sp_subscriptions_render_forum_subscriptions($curpage, $search);

    die();
}

if ($action == 'del_subs') {
	$fid = SP()->filters->integer($_GET['id']);
	sp_subscriptions_remove_forum_subscriptions($fid);
}

die();
