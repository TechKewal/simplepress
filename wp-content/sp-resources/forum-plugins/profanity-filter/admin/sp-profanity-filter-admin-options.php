<?php
/*
Simple:Press
Profanity Filter Plugin Admin Options Form
$LastChangedDate: 2018-10-26 00:19:28 -0500 (Fri, 26 Oct 2018) $
$Rev: 15772 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_profanity_filter_admin_options_form() {
	$filter = SP()->options->get('profanity-filter');
	$badwords = stripslashes(stripslashes($filter['badwords']));  # extra stripslashes due to regex slashing
	$replacementwords = stripslashes($filter['replacementwords']);

	spa_paint_open_panel();
		spa_paint_open_fieldset(__('Profanity Filter', 'sp-profanity'), true, 'profanity-filter');
			spa_paint_checkbox(__('Replace every profanity listed word with all word(s) in replacement list', 'sp-profanity'), 'replaceall', $filter['replaceall']);
			$submessage=__('Leave this option unchecked for one to one word or phrase replacements', 'sp-profanity');
			echo "<small><strong>$submessage</strong><br /><br /></small>\n";
			$submessage=__('Enter profanity terms one word or phrase per line - there must be a corresponding entry in the replacement term list.', 'sp-profanity');
			spa_paint_thin_textarea(__('Profanity Term List - words or phrases to filter from a post', 'sp-profanity'), 'badwords', stripslashes($badwords), $submessage, 4);
			$submessage=__('Enter replacement terms one word or phrase per line - there must be a corresponding entry in the profanities term list unless replacing all with a single term.', 'sp-profanity');
			spa_paint_thin_textarea(__('Replacement Term List - words or phrases to replace in a post', 'sp-profanity'), 'replacementwords', stripslashes($replacementwords), $submessage, 4);
			spa_paint_checkbox(__('Do not use word boundary checking', 'sp-profanity'), 'noboundary', $filter['noboundary']);
			$submessage=__("Recommnended only for languages that do not separate words by blank characters", 'sp-profanity');
			echo "<small><strong>$submessage</strong></small>\n";
		spa_paint_close_fieldset();
	spa_paint_close_panel();
}
