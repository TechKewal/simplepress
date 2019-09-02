<?php
/*
Simple:Press
Event Logger plugin ajax routine for creation functions
$LastChangedDate: 2018-10-17 15:17:49 -0500 (Wed, 17 Oct 2018) $
$Rev: 15756 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

sp_forum_ajax_support();

if (!sp_nonce('logger-manage')) die();

if (!SP()->auths->current_user_can('SPF Manage Logger')) die();

if (!isset($_GET['targetaction'])) die();
$action = SP()->filters->str($_GET['targetaction']);

if ($action == 'delete-hook') {
    $name = SP()->filters->str($_GET['name']);
	$logger = SP()->options->get('logger');
    foreach ($logger['hooks'] as $index => $hook) {
        if ($hook['name'] == $name) {
            unset($logger['hooks'][$index]);
            $logger['hooks'] = array_values($logger['hooks']);
        	SP()->options->update('logger', $logger);
            die();
        }
    }

    die();
}

if ($action == 'clearlog') {
    SP()->DB->truncate(SPEVENTLOG);

    echo '<p>'.__('There no events/hooks currently logged in the database', 'sp-logger').'</p>';

    die();
}

die();
