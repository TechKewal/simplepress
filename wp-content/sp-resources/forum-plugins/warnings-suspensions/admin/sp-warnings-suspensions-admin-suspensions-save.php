<?php
/*
Simple:Press
Warnings and Suspensions Plugin Admin Suspensions Save Routine
$LastChangedDate: 2018-08-04 11:37:53 -0500 (Sat, 04 Aug 2018) $
$Rev: 15677 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_warnings_suspensions_do_suspensions_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

    # get the data
    $name = SP()->filters->str($_POST['suspenduser']);
    $userid = SP()->DB->table(SPMEMBERS, "display_name='$name'", 'user_id');
    $ugid = SP()->filters->integer($_POST['usergroup_id']);

    # validity checks
    if (empty($name) || empty($userid) || empty($_POST['suspendexpire'])) return __('No suspension added. Please complete all entries.', 'sp-warnings-suspensions');
    $expire = date("Y-m-d H:i:s", strtotime(SP()->filters->str($_POST['suspendexpire'])));

    $membership_list = array();
    $memberships = SP()->user->get_memberships($userid);
    if ($memberships) {
        foreach ($memberships as $index => $membership) {
            $membership_list[$index]['id'] = $membership['usergroup_id'];
            $membership_list[$index]['name'] = $membership['usergroup_name'];
        }
    }
    $membership_list = serialize($membership_list);

    $ug = spa_get_usergroups_row($ugid);
	$ugname = (!empty($ug)) ? $ug->usergroup_name : '';

    # create db entry
	SP()->DB->execute("INSERT INTO ".SPWARNINGS." (warn_type, user_id, display_name, expiration, usergroup, saved_memberships) VALUES (".SPWARNSUSPENSION.", $userid, '$name', '$expire', '$ugname', '$membership_list')");

    # remove current memberships
    SP()->DB->execute('DELETE FROM '.SPMEMBERSHIPS." WHERE user_id=$userid");

    # add new membership
    SP()->user->add_membership($ugid, $userid);

    sp_warnings_suspensions_notify_suspension($userid, $expire);

	return __('User Suspension Added', 'sp-warnings-suspensions');
}
