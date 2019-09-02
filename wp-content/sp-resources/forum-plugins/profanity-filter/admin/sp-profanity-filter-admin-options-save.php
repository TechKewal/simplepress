<?php
/*
Simple:Press
Profanity Filter Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_profanity_filter_admin_options_save() {
	check_admin_referer('forum-adminform_content', 'forum-adminform_content');

	$filter = SP()->options->get('profanity-filter');
	$filter['badwords'] = SP()->filters->regex(SP()->saveFilters->nohtml(trim($_POST['badwords']), false));
	$filter['replacementwords'] = SP()->saveFilters->nohtml(trim($_POST['replacementwords']), false);
    $filter['noboundary'] = isset($_POST['noboundary']);
    $filter['replaceall'] = isset($_POST['replaceall']);
	SP()->options->update('profanity-filter', $filter);

	return __('Profanity filter options updated!', 'sp-profanity');
}
