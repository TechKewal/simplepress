<?php
/*
Simple:Press
Thanks Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_thanks_admin_options_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	# Save Thanks plugin options
	$thanksdata = SP()->options->get('thanks');

	if (isset($_POST['thank-enabled'])) { $thanksdata['thank-enabled'] = true; } else { $thanksdata['thank-enabled'] = false; }
	if (isset($_POST['show-most-thanked'])) { $thanksdata['show-most-thanked'] = true; } else { $thanksdata['show-most-thanked'] = false; }
	if (isset($_POST['points-enabled'])) { $thanksdata['points-enabled'] = true; } else { $thanksdata['points-enabled'] = false; }
	if (isset($_POST['points-in-post'])) { $thanksdata['points-in-post'] = true; } else { $thanksdata['points-in-post'] = false; }
	if (isset($_POST['points-in-member-list'])) { $thanksdata['points-in-member-list'] = true; } else { $thanksdata['points-in-member-list'] = false; }
	if (isset($_POST['points-in-profile'])) { $thanksdata['points-in-profile'] = true; } else { $thanksdata['points-in-profile'] = false; }

	$thanksdata['thank-message-before-name'] = SP()->saveFilters->title($_POST['thank-message-before-name']);
	$thanksdata['thank-message-after-name'] = SP()->saveFilters->title($_POST['thank-message-after-name']);
	$thanksdata['thank-message-save'] = SP()->saveFilters->title($_POST['thank-message-save']);
	$thanksdata['points-for-day'] = SP()->filters->integer($_POST['points-for-day']);
	$thanksdata['points-for-thank'] = SP()->filters->integer($_POST['points-for-thank']);
	$thanksdata['points-for-thanked'] = SP()->filters->integer($_POST['points-for-thanked']);
	$thanksdata['points-for-post'] = SP()->filters->integer($_POST['points-for-post']);
	$thanksdata['level-1-name'] = SP()->saveFilters->title($_POST['level-1-name']);
	$thanksdata['level-2-name'] = SP()->saveFilters->title($_POST['level-2-name']);
	$thanksdata['level-3-name'] = SP()->saveFilters->title($_POST['level-3-name']);
	$thanksdata['level-4-name'] = SP()->saveFilters->title($_POST['level-4-name']);
	$thanksdata['level-5-name'] = SP()->saveFilters->title($_POST['level-5-name']);
	$thanksdata['level-6-name'] = SP()->saveFilters->title($_POST['level-6-name']);
	$thanksdata['level-7-name'] = SP()->saveFilters->title($_POST['level-7-name']);
	$thanksdata['level-1-value'] = SP()->filters->integer($_POST['level-1-value']);
	$thanksdata['level-2-value'] = SP()->filters->integer($_POST['level-2-value']);
	$thanksdata['level-3-value'] = SP()->filters->integer($_POST['level-3-value']);
	$thanksdata['level-4-value'] = SP()->filters->integer($_POST['level-4-value']);
	$thanksdata['level-5-value'] = SP()->filters->integer($_POST['level-5-value']);
	$thanksdata['level-6-value'] = SP()->filters->integer($_POST['level-6-value']);
	$thanksdata['level-7-value'] = SP()->filters->integer($_POST['level-6-value']);

	SP()->options->update('thanks', $thanksdata);

	$out = __('Thanks options updated', 'sp-thanks');
	return $out;
}
