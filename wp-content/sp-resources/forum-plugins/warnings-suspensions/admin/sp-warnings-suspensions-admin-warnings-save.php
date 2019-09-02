<?php
/*
Simple:Press
Warnings and Suspensions Plugin Admin Warning Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_warnings_suspensions_do_warnings_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

    # get the data
    $name = SP()->filters->str($_POST['warnuser']);
    $userid = SP()->DB->table(SPMEMBERS, "display_name='$name'", 'user_id');

    # validity checks
    if (empty($name) || empty($userid) || empty($_POST['warnexpire'])) return __('No warning added. Please complete all entries.', 'sp-warnings-suspensions');
    $expire = date("Y-m-d H:i:s", strtotime(SP()->filters->str($_POST['warnexpire'])));

    # create db entry
	SP()->DB->execute("INSERT INTO ".SPWARNINGS." (warn_type, user_id, display_name, expiration) VALUES (".SPWARNWARNING.", $userid, '$name', '$expire')");

    sp_warnings_suspensions_notify_warning($userid, $expire);

	return __('User Warning Added', 'sp-warnings-suspensions');
}
