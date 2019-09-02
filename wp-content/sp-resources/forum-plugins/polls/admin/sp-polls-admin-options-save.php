<?php
/*
Simple:Press
Polls Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_polls_admin_save_options() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$polls = SP()->options->get('polls');

	if (isset($_POST['topiccreate'])) { $polls['topiccreate'] = true; } else { $polls['topiccreate'] = false; }
	if (isset($_POST['hide-results'])) { $polls['hide-results'] = true; } else { $polls['hide-results'] = false; }

	$polls['track'] = SP()->filters->integer($_POST['track']);
	$polls['poll-expire']      = min(SP()->filters->integer($_POST['poll-expire']), (12*25));
	$polls['cookie-expire']    = SP()->filters->integer($_POST['cookie-expire']);
	$polls['bar-background']   = substr(SP()->saveFilters->title(trim($_POST['bar-background'])), 1);
	$polls['bar-border']       = substr(SP()->saveFilters->title(trim($_POST['bar-border'])), 1);
	$polls['bar-height']       = SP()->filters->integer($_POST['bar-height']);
	$polls['answercriteria']   = SP()->filters->integer($_POST['answercriteria']);
	$polls['answersort']       = SP()->filters->integer($_POST['answersort']);
	$polls['resultcriteria']   = SP()->filters->integer($_POST['resultcriteria']);
	$polls['resultsort']       = SP()->filters->integer($_POST['resultsort']);
	$polls['hide-message']     = SP()->saveFilters->title($_POST['hide-message']);

	SP()->options->update('polls', $polls);

	return __('Options updated', 'sp-polls');
}
