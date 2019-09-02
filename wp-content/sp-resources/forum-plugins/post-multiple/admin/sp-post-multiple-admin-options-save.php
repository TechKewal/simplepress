<?php
/*
Simple:Press
Post Multiple Forums Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_post_multiple_admin_options_save() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	# Save Thanks plugin options
	$data = SP()->options->get('post-multiple');

    $exclude = array();
    if (isset($_POST['exclude'])) {
        foreach ($_POST['exclude'] as $id => $forum) {
            $exclude[] = $id;
        }
    }
	$data['exclude'] = $exclude;

	SP()->options->update('post-multiple', $data);

	$out = __('Post multiple forums options updated', 'sp-post-multiple');
	return $out;
}
