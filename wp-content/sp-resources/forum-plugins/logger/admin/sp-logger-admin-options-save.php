<?php
/*
Simple:Press
Event Logger Plugin Admin Options Save Routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_logger_admin_options_save_form() {
	check_admin_referer('forum-adminform_userplugin', 'forum-adminform_userplugin');

	$logger = SP()->options->get('logger');

	$logger['logentries'] = (is_numeric($_POST['logentries'])) ? max(0, SP()->filters->integer($_POST['logentries'])) : 100;

    $logger['postedited'] = isset($_POST['postedited']);
    $logger['topicedited'] = isset($_POST['topicedited']);
    $logger['postdeleted'] = isset($_POST['postdeleted']);
    $logger['topicdeleted'] = isset($_POST['topicdeleted']);
    $logger['postmoved'] = isset($_POST['postmoved']);
    $logger['topicmoved'] = isset($_POST['topicmoved']);
    $logger['postapproved'] = isset($_POST['postapproved']);
    $logger['postunapproved'] = isset($_POST['postunapproved']);
    $logger['postreassigned'] = isset($_POST['postreassigned']);
    $logger['postcreated'] = isset($_POST['postcreated']);

    if (!empty($_POST['hookname'])) {
        $newhook = array();
        $newhook['name'] = SP()->saveFilters->title($_POST['hookname']);
        $newhook['callback'] = SP()->saveFilters->title($_POST['hookcallback']);
    	$newhook['pri'] = (is_numeric($_POST['hookpri'])) ? max(0, SP()->filters->integer($_POST['hookpri'])) : 9999;

        $logger['hooks'][] = $newhook;
    }

	SP()->options->update('logger', $logger);

	return __('Options updated', 'sp-logger');
}
