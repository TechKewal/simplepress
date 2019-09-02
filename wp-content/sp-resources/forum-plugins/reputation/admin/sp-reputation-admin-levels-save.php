<?php
/*
Simple:Press
Reputation System plugin levels Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_reputation_do_admin_save_levels() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	# save reputation levels
	for ($x = 0; $x < count($_POST['levelname']); $x++) {
		if (!empty($_POST['levelname'][$x])) {
			$levels = array();
			$levels['points'] = SP()->filters->integer($_POST['levelpoints'][$x]);
			$levels['maxgive'] = SP()->filters->integer($_POST['levelmax'][$x]);
			$levels['maxday'] = SP()->filters->integer($_POST['leveldaily'][$x]);
			$levels['badge'] = SP()->saveFilters->filename($_POST['levelbadge'][$x]);
			if ($_POST['levelid'][$x] == -1) {
				SP()->meta->add('reputation level', SP()->saveFilters->title(trim($_POST['levelname'][$x])), $levels);
			} else {
				SP()->meta->update('reputation level', SP()->saveFilters->title(trim($_POST['levelname'][$x])), $levels, SP()->filters->integer($_POST['levelid'][$x]));
			}
		}
	}

	return __('Reputation levels updated!', 'sp-reputation');
}
