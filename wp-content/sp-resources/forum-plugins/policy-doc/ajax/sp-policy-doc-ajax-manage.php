<?php
/*
Simple:Press
Policy Docs plugin ajax routine for management functions
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

if (isset($_GET['popup'])) {
	$action = SP()->filters->str($_GET['popup']);
} else {
	die();
}

if ($action == 'reg') {
	echo '<div class="spRegistrationPolicy">';
	echo sp_policy_doc_retrieve('registration');
	echo '</div>';
}

if ($action == 'priv') {
	echo '<div class="spPrivacyPolicy">';
	echo sp_policy_doc_retrieve('privacy');
	echo '</div>';
}

die();
