<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/* 	=====================================================================================
	sp_SubscriptionsUnreadTopicsTag($display)

	template tag to display number of unread subscribed topics.  This tag includes
	default text that is output with the unread count data.   This text can
	be suppressed by setting $display to false. If suppressed, the new unread count is returned
    to the caller. Nothing is displayed and 0 returned for guests.

	parameters:

		$display		Determines whether to display unread count plus informational text
 	===================================================================================*/
function sp_SubscriptionsUnreadTopicsTag($display=true) {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	sp_forum_ajax_support();

	$count = 0;
	if (SP()->user->thisUser->member) {
		$list = SP()->user->thisUser->subscribe;
		if (!empty($list)) {
			$newpostlist = SP()->user->thisUser->newposts;
			if (empty($newpostlist)) sp_update_users_newposts(true);
			foreach ($list as $topicid) {
				if (sp_is_in_users_newposts($topicid)) $count++;
			}
		}
	}

	if ($display) {
		$out = '';
		if (SP()->user->thisUser->member) {
			$out .= '<p class="spSubscribed">';
			$out .= __('You have', 'sp-subs').' '.$count.' '.__('unread subscribed topics', 'sp-subs').'.';
			$out .= '</p>';
		}
        echo $out;
	}
	return $count;
}
