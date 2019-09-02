<?php
/*
Simple:Press
Thanks Plugin - Functions for displaying user reputation in topic view
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_thanks_do_post_user_reputation($args='', $label='') {
    if (empty(SP()->forum->view->thisPost->user_id)) return;

	$defs = array('tagClass' 	 => 'spPostUserThanksReputation',
				  'badge'		 => 1,
				  'title'		 => 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_PostThanksReputation_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagClass		= esc_attr($tagClass);
	$badge     		= (int) $badge;
	$title     		= (int) $title;
	$label			= SP()->displayFilters->title($label);

	$thanksdata = SP()->options->get('thanks');

	# This area gets the number of days to add to the userrating
    if (empty($thanksdata['points-for-day'])) {
        $daypoints = 0;
    } else {
    	$dateregistered = explode(' ', SP()->forum->view->thisPostUser->user_registered);
    	$startdate = strtotime($dateregistered[0]);
    	$currentdate = strtotime(date('Y-m-d'));
    	$days = ($currentdate - $startdate) / (60 * 60 * 24);
    	$daypoints = round($days / $thanksdata['points-for-day']);
    }

	# This area gets the number of thanks to add to the userrating
	$thankspoints = SP()->forum->view->thisPostUser->thanks * $thanksdata['points-for-thank'];

	# This area gets the number of thanked to add to the userrating
	$thankedpoints = SP()->forum->view->thisPostUser->thanked * $thanksdata['points-for-thanked'];

	# This area gets the number of posts to add to the userrating
	$postpoints = SP()->forum->view->thisPostUser->posts * $thanksdata['points-for-post'];

	# This area calculates the total reputation points
	$reputation = $daypoints + $thankspoints + $thankedpoints + $postpoints;

	# This area creates the display
	if ($reputation < $thanksdata['level-1-value']) {
		$rank = 1;
		$status = SP()->displayFilters->title($thanksdata['level-1-name']);
	} elseif ($reputation < $thanksdata['level-2-value']) {
		$rank = 2;
		$status = SP()->displayFilters->title($thanksdata['level-2-name']);
	} elseif ($reputation < $thanksdata['level-3-value']) {
		$rank = 3;
		$status = SP()->displayFilters->title($thanksdata['level-3-name']);
	} elseif ($reputation < $thanksdata['level-4-value']) {
		$rank = 4;
		$status = SP()->displayFilters->title($thanksdata['level-4-name']);
	} elseif ($reputation < $thanksdata['level-5-value']) {
		$rank = 5;
		$status = SP()->displayFilters->title($thanksdata['level-5-name']);
	} elseif ($reputation < $thanksdata['level-6-value']) {
		$rank = 6;
		$status = SP()->displayFilters->title($thanksdata['level-6-name']);
	} elseif ($reputation >= $thanksdata['level-7-value']) {
		$rank = 7;
		$status = SP()->displayFilters->title($thanksdata['level-7-name']);
	}

    if ($badge) $out = "<div class='$tagClass $tagClass$rank' title='$status'></div>";
    if ($title) $out.= "<div class='$tagClass' title='$status'>$label $reputation</div>";

	$out = apply_filters('sph_PostThanksReputation', $out, $a);

	echo $out;
}
