<?php
/*
Simple:Press
Post Thanks plugin install routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_thanks_do_install() {
	SP()->activity->add_type('give thanks');
	SP()->activity->add_type('receive thanks');

	$options = SP()->options->get('thanks');
	if (empty($options)) {
    	$spthanks = array();

    	# The thank options
    	$spthanks['thank-message-before-name'] = __('The following users say thank you to', 'sp-thanks');
    	$spthanks['thank-message-after-name'] = __('for this useful post', 'sp-thanks');
    	$spthanks['thank-message-save'] = __('User Thanked', 'sp-thanks');

    	# The points options
    	$spthanks['points-for-day'] = 7;
    	$spthanks['points-for-thank'] = 2;
    	$spthanks['points-for-thanked'] = 10;
    	$spthanks['points-for-post'] = 2;

    	# The point level options
    	$spthanks['level-1-name'] = __('Novice', 'sp-thanks');
    	$spthanks['level-1-value'] = 400;
    	$spthanks['level-2-name'] = __('Beginner', 'sp-thanks');
    	$spthanks['level-2-value'] = 1000;
    	$spthanks['level-3-name'] = __('Intermediate', 'sp-thanks');
    	$spthanks['level-3-value'] = 2000;
    	$spthanks['level-4-name'] = __('Advanced', 'sp-thanks');
    	$spthanks['level-4-value'] = 4000;
    	$spthanks['level-5-name'] = __('Expert', 'sp-thanks');
    	$spthanks['level-5-value'] = 7000;
    	$spthanks['level-6-name'] = __('Advanced Expert', 'sp-thanks');
    	$spthanks['level-6-value'] = 10000;
    	$spthanks['level-7-name'] = __('The Best', 'sp-thanks');
    	$spthanks['level-7-value'] = 10000;

        $spthanks['dbversion'] = SPTHANKSDBVERSION;

    	SP()->options->add('thanks', $spthanks);
    }
    # add a new permission into the auths table
 	SP()->auths->add('thank_posts', __('Can thank a user for a post', 'sp-thanks'), 1, 1, 0, 0, 1);

    # activation so make our auth active
    SP()->auths->activate('thank_posts');

	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}

# sp reactivated.
function sp_thanks_do_sp_activate() {
}

function sp_thanks_do_reset_permissions() {
	SP()->auths->add('thank_posts', __('Can thank a user for a post', 'sp-thanks'), 1, 1, 0, 0, 1);
}
