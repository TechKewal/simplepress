<?php
/*
Simple:Press
Thanks Plugin - Functions for displaying user reputation on profile
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');


function sp_thanks_do_profile_reputation($args='', $label='') {
	if (!SP()->auths->get('view_profiles')) return;

	$defs = array('tagClass'	=> 'spProfileShowDisplayName',
				  'leftClass'	=> 'spColumnSection spProfileLeftCol',
				  'middleClass'	=> 'spColumnSection spProfileSpacerCol',
				  'rightClass'	=> 'spColumnSection spProfileRightCol',
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_ProfileShowThanksReputation_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagClass		= esc_attr($tagClass);
	$leftClass		= esc_attr($leftClass);
	$middleClass	= esc_attr($middleClass);
	$rightClass		= esc_attr($rightClass);
	$label			= SP()->displayFilters->title($label);

	$thanksdata = SP()->options->get('thanks');

	# This area gets the number of days to add to the userrating
    if (empty($thanksdata['points-for-day'])) {
        $daypoints = 0;
    } else {
    	$dateregistered = explode(' ', SP()->user->profileUser->user_registered);
    	$startdate = strtotime($dateregistered[0]);
    	$currentdate = strtotime(date('Y-m-d'));
    	$days = ($currentdate-$startdate) / (60 * 60 * 24);
    	$daypoints = round($days / $thanksdata['points-for-day']);
    }

	# This area gets the number of thanks to add to the userrating
	$thankspoints = SP()->user->profileUser->thanks * $thanksdata['points-for-thank'];

	# This area gets the number of thanked to add to the userrating
	$thankedpoints = SP()->user->profileUser->thanked * $thanksdata['points-for-thanked'];

	# This area gets the number of posts to add to the userrating
	$postpoints = SP()->user->profileUser->posts * $thanksdata['points-for-post'];

	# This area calculates the total reputation points
	$reputation = $daypoints + $thankspoints + $thankedpoints + $postpoints;

	# This area creates the display
	$out = "<div class='$leftClass'><p>$label:</p></div><div class='$middleClass'></div><div class='$rightClass'><p>$reputation</p></div>";

	$out = apply_filters('sph_ProfileShowThanksReputation', $out, SP()->user->profileUser, $a);

	echo $out;
}
