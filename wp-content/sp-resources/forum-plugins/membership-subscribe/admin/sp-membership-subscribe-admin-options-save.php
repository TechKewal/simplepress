<?php
/*
Simple:Press
Membership Subscribe Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_membership_subscribe_admin_options_save($forum_id, $usergroup_id, $permission) {
	check_admin_referer('forum-adminform_permissionnew', 'forum-adminform_permissionnew');

    if (isset($_POST['membership_subscribe'])) {
        $users = SP()->DB->select('SELECT user_id
            		FROM '.SPMEMBERSHIPS.'
            		JOIN '.SPPERMISSIONS.' ON '.SPPERMISSIONS.'.usergroup_id = '.SPMEMBERSHIPS.".usergroup_id
            		WHERE forum_id=$forum_id AND ".SPMEMBERSHIPS.".usergroup_id=$usergroup_id", 'col');

        if ($users) {
            require_once SLIBDIR.'sp-subscriptions-database.php';
            foreach ($users as $user) {
                sp_subscriptions_save_forum_subscription($forum_id, $user, false);
            }
        }
    }
}
