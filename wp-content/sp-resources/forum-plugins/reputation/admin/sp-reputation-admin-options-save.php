<?php
/*
Simple:Press
Reputation System plugin options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_reputation_do_admin_save_options() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$option = SP()->options->get('reputation');

	$option['defrep'] = (int) $_POST['defrep'];
	$option['regrep'] = (int) $_POST['regrep'];
	$option['postrep'] = (int) $_POST['postrep'];

	if (isset($_POST['highlight'])) { $option['highlight'] = true; } else { $option['highlight'] = false; }
	$option['highlightcss'] = SP()->filters->str($_POST['highlightcss']);
	$option['highlightrep'] = SP()->filters->str($_POST['highlightrep']);

	if (isset($_POST['lowlight'])) { $option['lowlight'] = true; } else { $option['lowlight'] = false; }
	$option['lowlightcss'] = SP()->filters->str($_POST['lowlightcss']);
	$option['lowlightrep'] = SP()->filters->str($_POST['lowlightrep']);

	$option['popupheader'] = SP()->saveFilters->title($_POST['popupheader']);
	$option['popupgive'] = SP()->saveFilters->title($_POST['popupgive']);
	$option['popuptake'] = SP()->saveFilters->title($_POST['popuptake']);
	$option['popupamount'] = SP()->saveFilters->title($_POST['popupamount']);
	$option['popupsubmit'] = SP()->saveFilters->title($_POST['popupsubmit']);
	$option['popupinvalid'] = SP()->saveFilters->title($_POST['popupinvalid']);
	$option['popupzero'] = SP()->saveFilters->title($_POST['popupzero']);
	$option['popuppositive'] = SP()->saveFilters->title($_POST['popuppositive']);
	$option['popupmax'] = SP()->saveFilters->title($_POST['popupmax']);
	$option['popupupdated'] = SP()->saveFilters->title($_POST['popupupdated']);
	$option['popupwrong'] = SP()->saveFilters->title($_POST['popupwrong']);

	SP()->options->update('reputation', $option);

	return __('Reputation options updated!', 'sp-reputation');
}
