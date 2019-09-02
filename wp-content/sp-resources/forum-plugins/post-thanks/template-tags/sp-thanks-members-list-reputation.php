<?php
/*
Simple:Press
Thanks Plugin - Functions for displaying user reputation in members list
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_thanks_do_members_list_reputation($args='', $label='') {
	if (!SP()->auths->get('view_members_list')) return;

	$defs = array('tagId'    		=> 'spMembersListThanksRank%ID%',
				  'tagClass' 		=> 'spInRowCount',
				  'labelClass'		=> 'spInRowLabel',
				  'numberClass'		=> 'spInRowNumber',
				  'stack'			=> 1,
				  );
	$a = wp_parse_args($args, $defs);
	$a = apply_filters('sph_MemberListThanksReputation_args', $a);
	extract($a, EXTR_SKIP);

	# sanitize before use
	$tagId			= esc_attr($tagId);
	$tagClass		= esc_attr($tagClass);
	$labelClass		= esc_attr($labelClass);
	$numberClass	= esc_attr($numberClass);
	$stack			= (int) $stack;
	$label			= SP()->displayFilters->title($label);

	$tagId = str_ireplace('%ID%', SP()->forum->view->thisMember->user_id, $tagId);
	$att = ($stack) ? '<br />' : ': ';

	$thanksdata = SP()->options->get('thanks');

	# This area gets the number of days to add to the userrating
    if (empty($thanksdata['points-for-day'])) {
        $daypoints = 0;
    } else {
    	$dateregistered = explode(' ',  SP()->forum->view->thisMember->user_registered);
    	$startdate = strtotime($dateregistered[0]);
    	$currentdate = strtotime(date('Y-m-d'));
    	$days = ($currentdate - $startdate) / (60 * 60 * 24);
    	$daypoints = round($days / $thanksdata['points-for-day']);
    }

	# This area gets the number of thanks to add to the userrating
	$thankspoints = SP()->forum->view->thisMember->thanks * $thanksdata['points-for-thank'];

	# This area gets the number of thanked to add to the userrating
	$thankedpoints = SP()->forum->view->thisMember->thanked * $thanksdata['points-for-thanked'];

	# This area gets the number of posts to add to the userrating
	$postpoints = SP()->forum->view->thisMember->posts * $thanksdata['points-for-post'];

	# This area calculates the total reputation points
	$reputation = $daypoints + $thankspoints + $thankedpoints + $postpoints;

	# This area creates the display
	$out = "<div id='$tagId' class='$tagClass'><span class='$labelClass'>$label$att</span><span class='$numberClass'>$reputation</span></div>";

	$out = apply_filters('sph_MemberListThanksReputation', $out, $a);

	echo $out;
}
