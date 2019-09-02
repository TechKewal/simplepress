<?php
/*
Simple:Press
Post Rating Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_rating_admin_options_save() {
	check_admin_referer('forum-adminform_display', 'forum-adminform_display');

	$postratings = SP()->options->get('postratings');

	# before changing ratings style make sure it was confirmed
	if (isset($_POST['confirm-box-ratingsstyle'])) {
		# reset post ratings data
		SP()->DB->truncate(SPRATINGS);
		# save new ratings style
		$postratings['ratingsstyle'] = SP()->filters->integer($_POST['ratingsstyle']);

        SP()->auths->reset_cache();
	}
	SP()->options->update('postratings', $postratings);
}
