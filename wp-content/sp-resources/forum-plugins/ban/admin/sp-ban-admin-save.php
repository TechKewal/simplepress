<?php
/*
Simple:Press
Ban Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_ban_admin_do_save_bans() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$ips = explode("\n", trim(SP()->saveFilters->title($_POST['ip_addr'])));
    SP()->options->update('banned_ips', $ips);

	$ip_ranges = explode("\n", trim(SP()->saveFilters->title($_POST['ip_addr_range'])));
    SP()->options->update('banned_ip_ranges', $ip_ranges);

	$hostnames = explode("\n", trim(SP()->saveFilters->title($_POST['hostname'])));
    SP()->options->update('banned_hostnames', $hostnames);

	$user_agents = explode("\n", trim(SP()->saveFilters->title($_POST['user_agent'])));
    SP()->options->update('banned_agents', $user_agents);

	return __('Bans updated', 'sp-ban');
}

function sp_ban_admin_do_save_msgs() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

  	$ban = SP()->options->get('ban');

	$ban['general-message'] = SP()->saveFilters->text($_POST['genmsg']);
	$ban['user-message'] = SP()->saveFilters->text($_POST['usermsg']);
	$ban['ug-message'] = SP()->saveFilters->text($_POST['ugmsg']);
	$ban['restore-message'] = SP()->saveFilters->text($_POST['restoremsg']);
    SP()->options->update('ban', $ban);

	return __('Ban messages updated', 'sp-ban');
}

function sp_ban_admin_do_save_user() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

    $banned = SP()->options->get('banned_users');

	if (!empty($_POST['sp_ban_user'])) {
	    $display_name = SP()->filters->str($_POST['sp_ban_user']);
    	$user = SP()->DB->table(SPMEMBERS, "display_name='$display_name'", 'row');
        if (!empty($user)) {
            $newban = array();
            $newban['id'] = $user->user_id;
            $newban['name'] = $user->display_name;
            $newban['expire'] = (empty($_POST['sp_ban_expire'])) ? '' : time() + (SP()->filters->integer($_POST['sp_ban_expire']) * 3600);
            if ($_POST['usergroup_id'] == -1) {
                $newban['usergroups'] = '';
            } else {
                $newban['usergroups'] = SP()->DB->select('SELECT '.SPMEMBERSHIPS.'.usergroup_id FROM '.SPMEMBERSHIPS." WHERE user_id=$user->user_id", 'set', ARRAY_A);
                SP()->DB->execute('DELETE FROM '.SPMEMBERSHIPS." WHERE user_id=$user->user_id");
                SP()->user->add_membership(SP()->filters->integer($_POST['usergroup_id']), $user->user_id);
                SP()->user->reset_memberships($user->user_id);

                $ban = SP()->options->get('ban');
            	$nData = array();
            	$nData['user_id']		= $user->user_id;
            	$nData['guest_email']	= '';
            	$nData['post_id']		= 0;
            	$nData['link']			= '';
            	$nData['link_text']		= '';
            	$nData['message']		= $ban['ug-message'];
            	$nData['expires']		= time() + (30 * 24 * 60 * 60); # 30 days; 24 hours; 60 mins; 60secs
            	SP()->notifications->add($nData);
            }

            $banned[] = $newban;
            SP()->options->update('banned_users', $banned);
        } else {
        	return __('User ban failed - invalid user entered', 'sp-ban');
        }
    } else {
    	return __('User ban failed - you must enter a user', 'sp-ban');
    }

	return __('User ban added', 'sp-ban');
}
