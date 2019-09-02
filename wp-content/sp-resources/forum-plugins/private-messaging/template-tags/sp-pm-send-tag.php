<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

/* 	=====================================================================================
	sp_pm_send_pm($userid, $text)

	template tag to send a pm to a user.  Default text will be used for the link unless the
	optional $text argument is sent.  If you specify the $text argument, you need to specify
	where in the string you want the link inserted by the sequence %%.  For example:

	$text = '<a href="%%" title="Send PM">Send PM</a>';

	If the person viewing the site is not a registered member or does not have PM permissions,
	then an empty string is returned.

	parameters:

		$userid		user to send a PM to
		$text		optional parameter to specify text, img or html for the link
 	===================================================================================*/
function sp_pm_do_send_pm($userid, $text='') {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

    sp_forum_ajax_support();

    # set up user object for this user
    $user = SP()->user->get($userid);

    $userid = SP()->filters->integer($userid);
	$adversaries = SP()->DB->select('SELECT adversary_id FROM '.SPPMADVERSARIES.' WHERE user_id='.SP()->user->thisUser->ID, 'col');
	$blocked = SP()->DB->select('SELECT user_id FROM '.SPPMADVERSARIES." WHERE user_id=$userid AND adversary_id=".SP()->user->thisUser->ID, 'var');
	if (!sp_pm_get_auth('use_pm') || !sp_pm_get_auth('use_pm', '', $userid) || $blocked || (!empty($adversaries) && in_array($userid, $adversaries))) return '';
	$user_opts = SP()->memberData->get($userid, 'user_options');
	if ($user_opts['pmoptout']) return '';

    # are we limiting pms by usergroup?
    $pm = SP()->options->get('pm');
    if ($pm['limitedug']) {
        $common = sp_pm_array_intersect_assoc(SP()->user->thisUser->memberships, $user->memberships); # are they in same usergroup?
        if (!$user->admin && empty($common)) return '';
    }

	$out = '';
	if ($userid) {
	    $url = SP()->spPermalinks->get_url('private-messaging/send/'.$user->ID);
		if ($text == '') {
			$out.= '<a class="spSendPmTag" href="'.$url.'">'.SP()->theme->paint_icon('', PMIMAGES, 'sp_PmSendPmButton.png', esc_attr(__('Send private message', 'sp-pm'))).'</a>';
		} else {
			$out.= str_replace('%%', $url, $text);
		}
	}
	echo $out;
}
