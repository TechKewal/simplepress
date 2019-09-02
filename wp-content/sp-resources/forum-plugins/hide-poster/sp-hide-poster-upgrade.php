<?php
/*
Simple:Press
Hide Posters plugin install/upgrade routine
$LastChangedDate: 2017-08-05 00:56:34 -0500 (Sat, 05 Aug 2017) $
$Rev: 15487 $
*/

if (preg_match('#'.basename(__FILE__).'#', $_SERVER['PHP_SELF'])) die('Access denied - you cannot directly call this file');

function sp_hide_poster_do_upgrade_check() {
    if (!SP()->plugin->is_active('hide-poster/sp-hide-poster-plugin.php')) return;

    $options = SP()->options->get('hide-poster');

    $db = $options['dbversion'];
    if (empty($db)) $db = 0;

    # quick bail check
    if ($db == SPHIDEDBVERSION ) return;

    # apply upgrades as needed

    # db version upgrades

    # save data
    $options['dbversion'] = SPHIDEDBVERSION;
    SP()->options->update('hide-poster', $options);
}
