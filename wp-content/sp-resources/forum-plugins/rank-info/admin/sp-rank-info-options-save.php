<?php
/*
Simple:Press
Ranks Info Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_do_rank_info_options_save() {
    check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

    $options = SP()->options->get('rank-info');

    $options['membership'] = isset($_POST['membership']);
    $options['badge'] = isset($_POST['badge']);
    $options['users'] = isset($_POST['users']);
    $options['same_rank'] = isset($_POST['same_rank']);
    $options['special_ranks'] = isset($_POST['special_ranks']);
    $options['special_users'] = isset($_POST['special_users']);
    $options['same_special_rank'] = isset($_POST['same_special_rank']);

    SP()->options->update('rank-info', $options);

    $mess = __('Rank info options updated!', 'sp-rank-info');
	return $mess;
}
