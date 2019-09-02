<?php
/*
Simple:Press
Thanks Plugin - Functions for displaying user reputation in topic view
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_thanks_do_user_stats($args='', $userid, $label='') {
	$defs = array('tagClass' 	 => 'spThanksUserStats',
                  'usespan'      => 0,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_ThanksUserStats_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagClass		= esc_attr($tagClass);
	$usespan   		= (int) $usespan;

	$userid    		= (int) $userid;
	$label			= SP()->displayFilters->title($label);

    # bail if empty userid and we have a guest
    if (empty($userid) && empty(SP()->user->thisUser->ID)) return;

    # set up the user data
    if (empty($userid)) {
        $user = SP()->user->thisUser;
    } else {
		$user = SP()->user->get($userid);
    }
    if (empty($user->thanked)) $user->thanked = 0;
	if (empty($user->thanks)) $user->thanks = 0;

	$label = str_ireplace('%THANKED%', $user->thanked, $label);
	$label = str_ireplace('%THANKS%', $user->thanks, $label);
	$label = str_ireplace('%POSTS%', $user->posts, $label);


    $tag = ($usespan) ? 'span' : 'div';
    $out = "<$tag class='$tagClass'>$label</$tag>";

	$out = apply_filters('sph_ThanksUserStats', $out, $a);

	echo $out;
}
