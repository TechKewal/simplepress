<?php
/*
Simple:Press
Who's Online Plugin Admin Options Save Routine
$LastChangedDate: 2013-08-11 14:04:16 -0500 (Sun, 11 Aug 2013) $
$Rev: 10507 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_admin_bar_options_update($ops) {
    $ops['sfadminbar'] = isset($_POST['adminbar']);
    return $ops;
}

?>