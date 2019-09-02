<?php
/*
Simple:Press
Name plugin install/upgrade routine
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_redirect_do_upgrade_check() {
    $options = SP()->options->get('redirect');
    if (empty($options)) return;

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPREDIRECTDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades

    # save data
    $options['dbversion'] = SPREDIRECTDBVERSION;
    SP()->options->update('redirect', $options);
}
