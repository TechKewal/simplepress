<?php
/*
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_OnlineCurrentlyOnlineTag($args='', $currentLabel='', $guestLabel='') {
    #check if forum displayed
    if (sp_abort_display_forum()) return;

	if (!SP()->auths->get('view_online_activity')) return;

	$defs = array('pCurrentClass'	=> 'spOnlineCurrently',
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_CurrentlyOnline_args', $a);
	extract($a, EXTR_SKIP);

	$pCurrentClass 	= esc_attr($pCurrentClass);
	if (!empty($currentLabel)) 	$currentLabel 	= SP()->displayFilters->title($currentLabel);
	if (!empty($guestLabel)) 	$guestLabel 	= SP()->displayFilters->title($guestLabel);

	$out = "<p class='$pCurrentClass'>$currentLabel";

	# members online
	$online = SP()->DB->count(SPTRACK);
	$members = sp_get_members_online();
	if ($members) {
		$firstOnline = true;
		$spMemberOpts = SP()->options->get('sfmemberopts');
		foreach ($members as $user) {
			$userOpts = unserialize($user->user_options);
			if (SP()->user->thisUser->admin || !$spMemberOpts['sfhidestatus'] || !$userOpts['hidestatus']) {
				if (!$firstOnline) $out.= ', ';
				$out.= SP()->user->name_display($user->trackuserid, SP()->displayFilters->name($user->display_name), true);
				$firstOnline = false;
			}
		}
	}

	# guests online
	if ($online && ($online > count($members))) {
		$guests = ($online - count($members));
		if (!$firstOnline) $out.= ', ';
		$out.= $guests.' '.$guestLabel;
	}
	$out.= '</p>';

	$out = apply_filters('sph_CurrentlyOnline', $out, $a);
	echo $out;
}
