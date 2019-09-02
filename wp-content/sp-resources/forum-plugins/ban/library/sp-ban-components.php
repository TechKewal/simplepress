<?php
/*
Simple:Press
Ban Plugin Components
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_ban_do_check_bans() {
    # bail if for some reason the user object is not set up
    if (empty(SP()->user->thisUser)) return;

    # Let banned users view the banned page
    if (empty(SP()->rewrites->pageData['pageview']) || SP()->rewrites->pageData['pageview'] == 'banned') return;

    sp_ban_check_banned(SP()->options->get('banned_ips'), SP()->user->thisUser->ip);

   	$ip = SP()->user->thisUser->ip;
    if ($ip !== false) {
        sp_ban_check_ip_range(SP()->options->get('banned_ip_ranges'), $ip);

        sp_ban_check_banned(SP()->options->get('banned_hostnames'), @gethostbyaddr($ip));
    }

    sp_ban_check_banned(SP()->options->get('banned_agents'), $_SERVER['HTTP_USER_AGENT']);

    sp_ban_check_users(SP()->options->get('banned_users'), SP()->user->thisUser->ID);
}

function sp_ban_check_banned($bans, $ip) {
	if (is_array($bans)) $bans = array_filter($bans);
	if (empty($bans) || empty($ip)) return;

	foreach ($bans as $ban) {
		$regexp = str_replace ('.', '\\.', $ban);
		$regexp = str_replace ('*', '.+', $regexp);
		if (preg_match("/^$regexp$/", $ip)) SP()->primitives->redirect(SP()->spPermalinks->get_url('banned'));
	}
}

function sp_ban_check_ip_range($ranges, $ip) {
	if (is_array($ranges)) $ranges = array_filter($ranges);
	if (empty($ranges)) return;

	foreach ($ranges as $range) {
		$range = explode('-', $range);
		$start = long2ip(ip2long(trim($range[0])));
		$end = long2ip(ip2long(trim($range[1])));
    	if ($ip >= $start && $ip <= $end) SP()->primitives->redirect(SP()->spPermalinks->get_url('banned'));
	}
}

function sp_ban_check_users($bans, $id) {
	if (empty($bans) || empty($id)) return;

	foreach ($bans as $index => $ban) {
        if ($ban['id'] == $id) {
            # see if ban has expired and remove if so
            if (!empty($ban['expire']) && time() >= $ban['expire']) {
                sp_ban_remove_user_ban($index, $id, true);
            } else {
                # was user moved to usergroup? if not redirect to banned page
    			if (empty($ban['usergroups'])) SP()->primitives->redirect(SP()->spPermalinks->get_url('banned'));
            }
        }
	}
}

function sp_ban_remove_user_ban($ban_index, $userid) {
    $bans = SP()->options->get('banned_users');

    # restore any usergroups
    if (!empty($bans[$ban_index]['usergroups'])) {
        SP()->DB->execute('DELETE FROM '.SPMEMBERSHIPS." WHERE user_id=$userid");
        foreach ((array) $bans[$ban_index]['usergroups'] as $usergroup) {
            SP()->user->add_membership($usergroup['usergroup_id'], $userid);
        }
        SP()->user->reset_memberships($user->user_id);
    }

    # remove the ban
    unset($bans[$ban_index]);
    SP()->options->update('banned_users', $bans);

    # generate user notice
   	$ban = SP()->options->get('ban');
	$nData = array();
	$nData['user_id']		= $userid;
	$nData['guest_email']	= '';
	$nData['post_id']		= 0;
	$nData['link']			= '';
	$nData['link_text']		= '';
	$nData['message']		= $ban['restore-message'];
	$nData['expires']		= time() + (30 * 24 * 60 * 60); # 30 days; 24 hours; 60 mins; 60secs
	SP()->notifications->add($nData);
}

function sp_do_DisplayBannedMessage() {
  	$ban = SP()->options->get('ban');
    $msg = (SP()->user->thisUser->banned) ? $ban['user-message'] : $ban['general-message'];
    echo SP()->displayFilters->text($msg);
}
