<?php
/*
Simple:Press
Profanity Filter plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_profanity_filter_do_install() {
	$filter = SP()->options->get('profanity-filter');
	if (empty($filter)) {
		$badwords = SP()->options->get('sfbadwords');
		if (!empty($badwords)) {
			$replacementwords = SP()->options->get('sfreplacementwords');
			$filter['badwords'] = stripslashes(SP()->editFilters->text($badwords));  # extra stripslashes due to regex slashing
			$filter['replacementwords'] = SP()->editFilters->text($replacementwords);
			$filter['noboundary'] = false;
			$filter['replaceall'] = false;
			SP()->options->delete('sfbadwords');
			SP()->options->delete('sfreplacementwords');
		} else {
			$filter['badwords'] = '';
			$filter['replacementwords'] = '';
			$filter['noboundary'] = false;
			$filter['replaceall'] = false;
		}
		SP()->options->add('profanity-filter', $filter);
    }
	# admin task glossary entries
	require_once 'sp-admin-glossary.php';
}
